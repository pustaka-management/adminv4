<?php

namespace App\Controllers\UploadExcel;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Exception;

class Scribd extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function ScribdUpload()
    {
        ini_set('max_execution_time', 600); // 10 minutes
        ini_set('memory_limit', '1024M');
        $file_name = "scribd-book-jan23.xlsx";

        $inputFileName = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'ExcelUpload' . DIRECTORY_SEPARATOR . $file_name;

        if (!file_exists($inputFileName)) {
            return $this->response->setJSON(['error' => "File not found: $inputFileName"]);
        }

        try {
            // Load Excel
            $spreadsheet = IOFactory::load($inputFileName);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            $insertedData = [];
            $duplicateCount = 0;
            $skippedData = [];

            // Skip header (row 1)
            foreach ($rows as $i => $row) {
                if ($i == 1) continue;

                $identifier = trim($row['D']);
                if (empty($identifier)) {
                    $skippedData[] = [
                        'row' => $i,
                        'identifier' => null,
                        'reason' => 'Missing identifier'
                    ];
                    continue;
                }

                // === Derive values from identifier ===
                $language_id = null;
                $author_id   = null;
                $book_id     = null;
                $copyright_owner = null;

                if (substr($identifier, 0, 3) === "658" && strlen($identifier) === 14) {
                    // Format: 658 001 010 00047 (3-3-3-5)
                    $language_id = (int) substr($identifier, 3, 3);
                    $author_id   = (int) substr($identifier, 6, 3);
                    $book_id     = (int) substr($identifier, 9, 5);

                } elseif (substr($identifier, 0, 3) === "658" && strlen($identifier) === 13) {
                    // Format: 658 01 009 00006 (3-2-3-5)
                    $language_id = (int) substr($identifier, 3, 2);
                    $author_id   = (int) substr($identifier, 5, 3);
                    $book_id     = (int) substr($identifier, 8, 5);

                } elseif (substr($identifier, 0, 2) === "35" && strlen($identifier) >= 13) {
                    // Format: 35 01 1000 12714 (2-2-4-5)
                    $language_id = (int) substr($identifier, 2, 2);
                    $author_id   = (int) substr($identifier, 4, 4);
                    $book_id     = (int) substr($identifier, 8, 5);

                } else {
                    // Try to resolve by matching ISBN in book_tbl
                    $bookRow = $this->db->table('book_tbl')
                        ->select('book_id, language, author_name, copyright_owner')
                        ->where("REPLACE(isbn_number,'-','')", $identifier)
                        ->get()
                        ->getRowArray();

                    if ($bookRow) {
                        $book_id        = $bookRow['book_id'];
                        $language_id    = $bookRow['language'];
                        $author_id      = $bookRow['author_name'];
                        $copyright_owner = $bookRow['copyright_owner'];
                    }
                }

                if ($book_id === null) {
                    $skippedData[] = [
                        'row' => $i,
                        'identifier' => $identifier,
                        'reason' => 'Invalid identifier format / Not found'
                    ];
                    continue;
                }

                $doc_id = isset($row['C']) ? trim($row['C']) : null;
                if (empty($doc_id)) {
                    $skippedData[] = [
                        'row' => $i,
                        'identifier' => $identifier,
                        'reason' => 'Missing doc_id'
                    ];
                    continue;
                }

                // === Fetch copyright_owner from book_tbl if not already set ===
                if (empty($copyright_owner) && $book_id) {
                    $bookRow = $this->db->table('book_tbl')
                        ->select('copyright_owner')
                        ->where('book_id', $book_id)
                        ->get()
                        ->getRow();
                    if ($bookRow) {
                        $copyright_owner = $bookRow->copyright_owner;
                    }
                }

                // === Skip duplicates by identifier or book_id ===
                $exists = $this->db->table('scribd_books')
                    ->groupStart()
                    ->where('identifier', $identifier)
                    ->orWhere('book_id', $book_id)
                    ->groupEnd()
                    ->get()
                    ->getRow();

                if ($exists) {
                    $duplicateCount++;
                    $skippedData[] = [
                        'row' => $i,
                        'identifier' => $identifier,
                        'reason' => 'Duplicate identifier or book_id'
                    ];
                    continue;
                }

                // === Convert updated_at ===
                $updatedAtRaw = $row['A'];
                $updated_at = null;

                if (!empty($updatedAtRaw)) {
                    if (is_numeric($updatedAtRaw)) {
                        try {
                            $updated_at = ExcelDate::excelToDateTimeObject($updatedAtRaw)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $updated_at = null;
                        }
                    } else {
                        $ts = strtotime($updatedAtRaw);
                        if ($ts) {
                            $updated_at = date('Y-m-d', $ts);
                        }
                    }
                }

                // === Insert data ===
                $insertData = [
                    'updated_at'                => $updated_at,
                    'import_id'                 => $row['B'],
                    'doc_id'                    => $doc_id,
                    'identifier'                => $identifier,
                    'title'                     => $row['E'],
                    'published'                 => $row['F'],
                    'in_subscription'           => $row['G'],
                    'product_page_url'          => $row['H'],
                    'publisher_tools_config_id' => $row['I'],
                    'imprints'                  => $row['J'],
                    'status'                    => $row['K'],
                    'metadata_status'           => $row['L'],
                    'conversion_status'         => $row['M'],
                    'product_page_pending'      => $row['N'],
                    'subscription_pending'      => $row['O'],
                    'book_id'                   => $book_id,
                    'author_id'                 => $author_id,
                    'copyright_owner'           => $copyright_owner,
                    'language_id'               => $language_id,
                    'duplicate_flag'            => 0,
                ];

                $this->db->table('scribd_books')->insert($insertData);
                $insertedData[] = $insertData;
            }

            // === Print results on screen ===
            echo "<h3>Scribd Upload Completed</h3>";
            echo "<p><strong>Total Excel Rows:</strong> " . (count($rows) - 1) . "</p>";
            echo "<p><strong>Total Inserted:</strong> " . count($insertedData) . "</p>";
            echo "<p><strong>Total Duplicates:</strong> " . $duplicateCount . "</p>";
            echo "<p><strong>Total Skipped:</strong> " . count($skippedData) . "</p>";
            echo "<hr>";

            echo "<h4>Sample Inserted Records (first 20):</h4>";
            echo "<pre>";
            print_r(array_slice($insertedData, 0, 20));
            echo "</pre>";

            echo "<h4>Skipped/Invalid Records (first 50):</h4>";
            echo "<pre>";
            print_r(array_slice($skippedData, 0, 50));
            echo "</pre>";

        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return "Error: " . $e->getMessage();
        }
    }
}
