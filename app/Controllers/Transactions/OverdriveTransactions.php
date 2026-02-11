<?php

namespace App\Controllers\Transactions;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use DateTime;

class OverdriveTransactions extends BaseController
{
    public function uploadTransactions()
    {
        $file_name = "Overdrive_SalesDetail_Dec2025.xlsx";
        $exchange_rate = 70;
        $inputFileName = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'transactions' . DIRECTORY_SEPARATOR .'overdrive_reports' . DIRECTORY_SEPARATOR . $file_name;

        if (!file_exists($inputFileName)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => "File not found: $inputFileName"
            ]);
        }

        try {
            $spreadsheet = IOFactory::load($inputFileName);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $header = $rows[0];
            $arr_data = array_slice($rows, 1); // Skip header row

            $processed_data = [];
            $skipped_data = [];

            $db = \Config\Database::connect();

            foreach ($arr_data as $index => $row) {
                $row_number = $index + 2; // Excel row (since header is row 1)

                // Skip empty rows
                if (!isset($row[0]) || trim($row[0]) === '') {
                    $skipped_data[] = [
                        'row' => $row_number,
                        'data' => $row,
                        'reason' => 'Empty date cell'
                    ];
                    continue;
                }

                // Handle date conversion
                $raw_date = $row[0];
                $transaction_date = null;

                if (is_numeric($raw_date)) {
                    $transaction_date = Date::excelToDateTimeObject($raw_date)->format('Y-m-d');
                } else {
                    $formats = ['d-m-Y', 'd/m/Y', 'm/d/Y', 'Y-m-d'];
                    foreach ($formats as $format) {
                        $dt = DateTime::createFromFormat($format, trim($raw_date));
                        if ($dt !== false) {
                            $transaction_date = $dt->format('Y-m-d');
                            break;
                        }
                    }

                    if (!$transaction_date) {
                        $skipped_data[] = [
                            'row' => $row_number,
                            'data' => $row,
                            'reason' => "Invalid date format: '{$raw_date}'"
                        ];
                        continue;
                    }
                }

                $overdrive_id     = (string) $row[1];
                $isbn             = (string) $row[2];
                $title            = (string) $row[3];
                $subtitle         = (string) $row[4];
                $author           = (string) $row[5];
                $retailer         = (string) $row[6];
                $country_of_sale  = (string) $row[7];
                $format           = (string) $row[8];
                $srp_usd          = (float) $row[9];
                $discount         = (float) $row[10];
                $amt_owed_usd     = (float) $row[11];

                // --- Fetch Overdrive Book Info ---
                $od_book = $db->table('overdrive_books')
                    ->where('overdrive_id', $overdrive_id)
                    ->get()
                    ->getRowArray();

                if (!$od_book) {
                    $skipped_data[] = [
                        'row' => $row_number,
                        'data' => $row,
                        'reason' => "Overdrive book not found for ID: {$overdrive_id}"
                    ];
                    continue;
                }

                $author_id       = $od_book['author_id'];
                $book_id         = $od_book['book_id'];
                $copyright_owner = $od_book['copyright_owner'];
                $isbn            = $od_book['isbn'];

                // --- Fetch Book Info ---
                $book = $db->table('book_tbl')
                    ->where('book_id', $book_id)
                    ->get()
                    ->getRowArray();

                if (!$book) {
                    $skipped_data[] = [
                        'row' => $row_number,
                        'data' => $row,
                        'reason' => "Book not found for book_id: {$book_id}"
                    ];
                    continue;
                }

                $royalty_percentage = $book['royalty'];
                $language_id        = $book['language'];
                $type_of_book       = $book['type_of_book'];

                // --- Fetch Author Info ---
                $author_info = $db->table('author_tbl')
                    ->where('author_id', $author_id)
                    ->get()
                    ->getRowArray();

                if (!$author_info) {
                    $skipped_data[] = [
                        'row' => $row_number,
                        'data' => $row,
                        'reason' => "Author not found for author_id: {$author_id}"
                    ];
                    continue;
                }

                $author_type = $author_info['author_type'];
                $user_id     = $author_info['user_id'];

                // --- Calculations ---
                $inr_value = $amt_owed_usd * $exchange_rate;
                $final_royalty_value = ($author_type == 1)
                    ? $inr_value * ($royalty_percentage / 100)
                    : $inr_value;

                $insert_data = [
                    'transaction_date'     => $transaction_date,
                    'overdrive_id'         => $overdrive_id,
                    'isbn'                 => $isbn,
                    'title'                => $title,
                    'subtitle'             => $subtitle,
                    'author'               => $author,
                    'retailer'             => $retailer,
                    'country_of_sale'      => $country_of_sale,
                    'format'               => $format,
                    'srp_usd'              => $srp_usd,
                    'discount'             => $discount,
                    'amt_owed_usd'         => $amt_owed_usd,
                    'book_id'              => $book_id,
                    'author_id'            => $author_id,
                    'language_id'          => $language_id,
                    'exchange_rate'        => $exchange_rate,
                    'inr_value'            => $inr_value,
                    'final_royalty_value'  => $final_royalty_value,
                    'user_id'              => $user_id,
                    'copyright_owner'      => $copyright_owner,
                    'type_of_book'         => $type_of_book,
                    'status'               => 'O'
                ];

                $processed_data[] = $insert_data;

                // Uncomment below to actually insert into DB
                $db->table('overdrive_transactions')->insert($insert_data);
            }

            return $this->response->setJSON([
                'status'        => 'success',
                'message'       => 'Transactions processed',
                'total_rows'    => count($arr_data),
                'processed'     => count($processed_data),
                'skipped'       => count($skipped_data),
                'processed_data'=> $processed_data,
                'skipped_data'  => $skipped_data
            ]);

        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
