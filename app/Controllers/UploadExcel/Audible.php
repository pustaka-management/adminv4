<?php

namespace App\Controllers\UploadExcel;

use App\Controllers\BaseController;
use Config\Database;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Audible extends BaseController
{
    public function Uploadbooks()
    {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '1024M');

        $file_name = "Title_List_Pustaka Digital.xlsx";
        $inputFileName = WRITEPATH . 'uploads/ExcelUpload/audible/' . $file_name;

        if (!file_exists($inputFileName)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'File not found'
            ]);
        }

        $db = Database::connect();

        try {
            $spreadsheet = IOFactory::load($inputFileName);
            $sheet = $spreadsheet->getActiveSheet();

            $highestRow = $sheet->getHighestRow();
            $highestCol = $sheet->getHighestColumn();

            $arr_data = [];

            // Read Excel data
            for ($row = 1; $row <= $highestRow; $row++) {
                for ($col = 'A'; $col <= $highestCol; $col++) {
                    $arr_data[$row][$col] = (string) $sheet->getCell($col . $row)->getValue();
                }
            }

            foreach ($arr_data as $rowIndex => $row) {

                // Skip header rows
                if ($rowIndex < 3) {
                    continue;
                }

                $product_id   = trim($row['A'] ?? '');
                $title        = trim($row['B'] ?? '');
                $authors      = trim($row['C'] ?? '');
                $narrators    = trim($row['D'] ?? '');
                $first_online = trim($row['J'] ?? '');

                if ($product_id === '' || $title === '') {
                    continue;
                }

                // Excel Date → Y-m-d
                $first_online_date = null;
                if (is_numeric($first_online)) {
                    $unix_date = ((int)$first_online - 25569) * 86400;
                    $first_online_date = gmdate('Y-m-d', $unix_date);
                }

                // Check duplicate product
                $exists = $db->table('audible_books')
                    ->where('product_id', $product_id)
                    ->countAllResults();

                if ($exists > 0) {
                    echo "Book Available: {$product_id}<br>--------------------------------<br>";
                    continue;
                }

                // Modify title if bracket exists
                if (strpos($title, '[') !== false) {
                    $title = trim(substr($title, 0, strpos($title, '['))) . ' - Audio Book';
                }

                // Fetch matching book
                $book_details = $db->table('book_tbl')
                    ->where('book_title', $title)
                    ->get()
                    ->getRowArray();

                if (!$book_details) {
                    echo "Book Not Found: {$title}<br>--------------------------------<br>";
                    continue;
                }

                // Safe defaults
                $audible_asin = null;
                $amazon_asin  = null;

                $insert_data = [
                    'product_id'        => $product_id,
                    'audible_asin'      => $audible_asin,
                    'amazon_asin'       => $amazon_asin,
                    'title'             => $title,
                    'authors'           => $authors,
                    'narrators'         => $narrators,
                    'first_online_date' => $first_online_date,
                    'book_id'           => $book_details['book_id'] ?? null,
                    'author_id'         => $book_details['author_name'] ?? null,
                    'copyright_owner'   => $book_details['copyright_owner'] ?? null,
                    'language_id'       => $book_details['language'] ?? null,
                ];

                echo "<pre>";
                print_r($insert_data);
                echo "</pre><hr>";

                // Uncomment after verification
                $db->table('audible_books')->insert($insert_data);
            }

            echo "Total Rows Processed: " . count($arr_data);

        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
