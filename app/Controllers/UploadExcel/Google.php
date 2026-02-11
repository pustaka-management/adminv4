<?php

namespace App\Controllers\UploadExcel;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Google extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function Uploadbooks()
    {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '1024M');
        $file_name = 'google-books-jan23.xlsx';
        $inputFileName = WRITEPATH . 'uploads/ExcelUpload/google/' . $file_name;

        if (!file_exists($inputFileName)) {
            return 'File not found: ' . $inputFileName;
        }

        $inserted = 0;
        $ignored  = 0;
        $ignoredBookMissing = 0;
        $invalidIsbns = [];

        try {

            $spreadsheet = IOFactory::load($inputFileName);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            // Remove header row
            unset($rows[1]);

            foreach ($rows as $row) {

                $identifier = $row['A'] ?? null;
                $status     = $row['B'] ?? null;

                if (!$identifier || stripos($status, 'Needs action') !== false) {
                    $ignored++;
                    continue;
                }

                $reference_id = $row['I'] ?? $identifier;

                /* =====================================
                   ISBN / REFERENCE ID LOGIC
                ===================================== */

                $isbn_lang_id = null;
                $isbn_author_id = null;
                $isbn_book_id = null;
                $copyright_owner = null;

                $isbn_id = substr($reference_id, 0, 3);

                if ($isbn_id === '658') {

                    $isbn_lang_id   = ltrim(substr($reference_id, 3, 2), '0');
                    $isbn_author_id = ltrim(substr($reference_id, 5, 3), '0');
                    $isbn_book_id   = ltrim(substr($reference_id, 8, 5), '0');

                    $book_result = $this->db->table('book_tbl')
                        ->where('book_id', $isbn_book_id)
                        ->where('author_name', $isbn_author_id)
                        ->get()
                        ->getRowArray();

                    $copyright_owner = $book_result['copyright_owner'] ?? null;

                } else {

                    $isbn_id2 = substr($reference_id, 0, 2);

                    if ($isbn_id2 === '35') {

                        $isbn_lang_id   = substr($reference_id, 2, 2);
                        $isbn_author_id = ltrim(substr($reference_id, 4, 4), '0');
                        $isbn_book_id   = ltrim(substr($reference_id, 8), '0');

                        $book_result = $this->db->table('book_tbl')
                            ->where('book_id', $isbn_book_id)
                            ->where('author_name', $isbn_author_id)
                            ->get()
                            ->getRowArray();

                        $copyright_owner = $book_result['copyright_owner'] ?? null;

                    } else {

                        // ---------- FALLBACK ISBN ----------
                        $book_details_result = $this->db->query(
                            "SELECT * FROM book_tbl WHERE REPLACE(isbn_number,'-','') = ?",
                            [$reference_id]
                        )->getRowArray();

                        $isbn_lang_id    = $book_details_result['language'] ?? null;
                        $isbn_author_id  = $book_details_result['author_name'] ?? null;
                        $isbn_book_id    = $book_details_result['book_id'] ?? null;
                        $copyright_owner = $book_details_result['copyright_owner'] ?? null;
                    }
                }

                /* ========= INVALID ISBN ========= */
                if (!$isbn_book_id) {
                    $ignored++;
                    $ignoredBookMissing++;

                    if (count($invalidIsbns) < 10) {
                        $invalidIsbns[] = $reference_id;
                    }
                    continue;
                }

                /* ========= DUPLICATE CHECK ========= */
                $exists = $this->db->table('google_books')
                    ->where('identifier', $identifier)
                    ->countAllResults();

                if ($exists > 0) {
                    $ignored++;
                    continue;
                }

                /* ========= INSERT ========= */
                $insert_data = [
                    'identifier'          => $identifier,
                    'status'              => $status,
                    'label'               => $row['C'] ?? null,
                    'play_store_link'     => $row['D'] ?? null,
                    'enable_for_sale'     => (($row['E'] ?? '') === 'Yes') ? 1 : 0,
                    'title'               => $row['F'] ?? null,
                    'subtitle'            => $row['G'] ?? null,
                    'book_format'         => $row['H'] ?? null,
                    'related_identifier'  => $row['I'] ?? null,
                    'contributor'         => $row['J'] ?? null,
                    'language'            => $row['L'] ?? null,
                    'description'         => $row['O'] ?? null,
                    'page_count'          => $row['Q'] ?? 0,
                    'book_id'             => $isbn_book_id,
                    'author_id'           => $isbn_author_id,
                    'language_id'         => $isbn_lang_id,
                    'copyright_owner'     => $copyright_owner,
                ];

                // Uncomment when ready to insert
                // $this->db->table('google_books')->insert($insert_data);

                $inserted++;
            }

            /* ========= FINAL OUTPUT ========= */
            return "
                Upload Completed ✅<br>
                Inserted Records : <b>{$inserted}</b><br>
                Skipped Records  : <b>{$ignored}</b><br>
                Missing Book ID : <b>{$ignoredBookMissing}</b><br>
                Invalid ISBN Samples :
                <pre>" . print_r($invalidIsbns, true) . "</pre>
            ";

        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return $e->getMessage();
        }
    }
}
