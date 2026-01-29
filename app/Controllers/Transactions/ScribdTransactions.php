<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Exception;

class ScribdTransactions extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function uploadTransactions()
    {
        $bk_converted_inr = 0;
        $converted_inr = 0;
        $file_name = "Pustaka_scribd_Dec 2025.xlsx";
        $exchange_rate_value = 70;
        $skipped_rows = [];

        // D:\adminv4\writable\uploads\transactions\scribd_reports
        $inputFileName = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR .'transactions' . DIRECTORY_SEPARATOR . 'scribd_reports' . DIRECTORY_SEPARATOR . $file_name;

        if (!file_exists($inputFileName)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => "File not found: $inputFileName"
            ]);
        }

        try {
            $spreadsheet = IOFactory::load($inputFileName);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, true, true);

            $processed_data = [];

            foreach ($data as $index => $row) {
                if ($index == 1 || empty($row['I'])) {
                    // $skipped_rows[] = ['reason' => 'Header or empty ISBN', 'row' => $row];
                    continue;
                }

                $isbn = str_replace('-', '', trim($row['I']));
                $amount_owed = floatval($row['C'] ?? 0);
                $isbn_id = substr($isbn, 0, 3);

                $isbn_id = substr($isbn, 0, 3); // Extract first 3 digits to identify prefix

                if ($isbn_id === '658') {
                    if (strlen($isbn) == 13) {
                        $isbn_lang_id = ltrim(substr($isbn, 3, 2), '0');
                        $isbn_author_id = ltrim(substr($isbn, 5, 3), '0');
                        $isbn_book_id = ltrim(substr($isbn, 8, 5), '0');
                    } else {
                        $isbn_lang_id = ltrim(substr($isbn, 3, 3), '0');
                        $isbn_author_id = ltrim(substr($isbn, 6, 3), '0');
                        $isbn_book_id = ltrim(substr($isbn, 9, 5), '0');
                    }
                } elseif (substr($isbn, 0, 2) === '35') {
                    // New logic for ISBNs starting with "35"
                    if (strlen($isbn) == 13) {
                        $isbn_lang_id = ltrim(substr($isbn, 2, 2), '0');
                        $isbn_author_id = ltrim(substr($isbn, 4, 4), '0');
                        $isbn_book_id = ltrim(substr($isbn, 8, 5), '0');
                    } else {
                        $isbn_lang_id = ltrim(substr($isbn, 2, 3), '0');
                        $isbn_author_id = ltrim(substr($isbn, 5, 4), '0');
                        $isbn_book_id = ltrim(substr($isbn, 9, 5), '0');
                    }
                } else {
                    // Fallback: fetch from database if ISBN doesn’t start with 658 or 35
                    $bookModel = $this->db->table('book_tbl')
                        ->where("REPLACE(isbn_number, '-', '') =", $isbn)
                        ->get()
                        ->getRowArray();

                    if (!$bookModel) {
                        $skipped_rows[] = [
                            'reason' => 'Book ISBN not matched',
                            'isbn' => $isbn,
                            'row' => $row
                        ];
                        continue;
                    }

                    $isbn_lang_id = $bookModel['language'];
                    $isbn_author_id = $bookModel['author_name'];
                    $isbn_book_id = $bookModel['book_id'];
                }


                $book = $this->db->table('book_tbl')
                    ->where('book_id', $isbn_book_id)
                    ->get()
                    ->getRowArray();

                if (!$book) {
                    $skipped_rows[] = ['reason' => 'Book not found', 'isbn' => $isbn, 'row' => $row];
                    continue;
                }

                $author = $this->db->table('author_tbl')
                    ->where('author_id', $book['author_name'])
                    ->get()
                    ->getRowArray();

                if (!$author) {
                    $skipped_rows[] = ['reason' => 'Author not found', 'isbn' => $isbn, 'row' => $row];
                    continue;
                }

                if (!isset($book['royalty']) || !is_numeric($book['royalty'])) {
                    $skipped_rows[] = ['reason' => 'Invalid royalty', 'book_id' => $isbn_book_id, 'royalty' => $book['royalty']];
                    continue;
                }

                $royalty_percent = floatval($book['royalty']);
                $bk_converted_inr = $amount_owed * ($royalty_percent / 100);
                $converted_inr = $bk_converted_inr * $exchange_rate_value;
                $converted_inr_full = $amount_owed * $exchange_rate_value;

                $insert_data = [
                    'Payout_month' => $this->convertExcelDate($row['A']),
                    'Publisher' => $row['B'] ?? '',
                    'Amount_owed_for_this_interaction' => $amount_owed,
                    'Amount_owed_currency' => $row['D'] ?? '',
                    'Price_in_original_currency' => $row['E'] ?? '',
                    'Digital_list_price' => $row['F'] ?? '',
                    'Original_currency' => $row['G'] ?? '',
                    'Price_type' => $row['H'] ?? '',
                    'ISBN' => $isbn,
                    'Title' => $row['J'] ?? '',
                    'Authors' => $row['K'] ?? '',
                    'Imprints' => $row['L'] ?? '',
                    '%_Viewed' => $row['M'] ?? '',
                    'Payout_type' => $row['N'] ?? '',
                    'Start_date_of_interaction' => $this->convertExcelDate($row['O']),
                    'Last_date_of_interaction' => $this->convertExcelDate($row['P']),
                    'Country_of_reader' => $row['Q'] ?? '',
                    'Unique_interaction_ID' => $row['R'] ?? '',
                    'ISO_Country_Code' => $row['S'] ?? '',
                    'Threshold_Date' => $this->convertExcelDate($row['T']),
                    'book_id' => $isbn_book_id,
                    'author_id' => $book['author_name'],
                    'copyright_owner' => $book['copyright_owner'],
                    'converted_inr' => $converted_inr,
                    'status' => 'O',
                    'converted_inr_full'=>$converted_inr_full,
                    'exchange_rate' => $exchange_rate_value,
                    'user_id' => $author['user_id']
                ];

                // Insert to DB
                // $this->db->table('scribd_transaction')->insert($insert_data);
                $processed_data[] = $insert_data;
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Transactions uploaded successfully',
                'count' => count($processed_data),
                'skipped_count' => count($skipped_rows),
                'skipped_rows' => $skipped_rows,
                'data' => $processed_data,
            ]);
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    private function convertExcelDate($value)
    {
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        if (is_string($value)) {
            $formats = ['d-m-Y', 'Y-m-d', 'Y-m', 'm/d/Y', 'd/m/Y'];
            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, trim($value));
                if ($date && $date->format($format) === trim($value)) {
                    return $date->format('Y-m-d');
                }
            }
        }

        return null;
    }
}
