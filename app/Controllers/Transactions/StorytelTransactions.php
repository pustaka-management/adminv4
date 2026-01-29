<?php

namespace App\Controllers\Transactions;


use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use CodeIgniter\HTTP\ResponseInterface;

class StorytelTransactions extends BaseController
{
    public function EbookTransactions()
    {
        $file_name = "Q4_2025_royalty_report.xlsx";
        $transaction_date = '2025-12-31';
        $exchange_rate = 7.5;
        $inputFileName = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR .'transactions' . DIRECTORY_SEPARATOR .  'storytel_reports' . DIRECTORY_SEPARATOR . $file_name;

        if (!file_exists($inputFileName)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => "File not found: $inputFileName"
            ]);
        }

        try {
            $spreadsheet = IOFactory::load($inputFileName);
            $sheet = $spreadsheet->getSheet(2); // Use Sheet 3 (0-based index)
            $rows = $sheet->toArray(null, true, true, true);
            $format = 'm/d/Y';

            $arr_data = [];
            $header = [];

            foreach ($rows as $row => $columns) {
                foreach ($columns as $column => $value) {
                    $cell = $sheet->getCell($column . $row);
                    if (Date::isDateTime($cell)) {
                        $value = date($format, Date::excelToTimestamp($value));
                    } else {
                        $value = (string) $value;
                    }

                    if ($row == 1) {
                        $header[$row][$column] = $value;
                    } else {
                        $arr_data[$row][$column] = $value;
                    }
                }
            }

            echo count($arr_data);

            $db = \Config\Database::connect();

            for ($j = 2; $j < count($arr_data) + 2; $j++) {
                $author = $arr_data[$j]['A'] ?? '';
                $ebook_title = $arr_data[$j]['B'] ?? '';
                $isbn = $arr_data[$j]['C'] ?? '';
                $country = $arr_data[$j]['E'] ?? '';
                $price_model = $arr_data[$j]['F'] ?? '';
                $no_of_units = $arr_data[$j]['G'] ?? '';
                $net_receipts_per_hour_local = $arr_data[$j]['H'] ?? '';
                $ecb_exchange_rate = $arr_data[$j]['I'] ?? '';
                $net_receipts_per_hour_sek = $arr_data[$j]['J'] ?? '';
                $book_length_in_hours = $arr_data[$j]['K'] ?? '';
                $price_per_unit = $arr_data[$j]['L'] ?? '';
                $remuneration_sek = $arr_data[$j]['M'] ?? '';
                $remuneration_inr = $remuneration_sek * $exchange_rate;
                $publisher = $arr_data[$j]['O'] ?? '';
                $imprint = $arr_data[$j]['P'] ?? '';
                $consumption_dates = $arr_data[$j]['Q'] ?? '';
              

                $book = $db->table('storytel_books')->where('isbn', $isbn)->get()->getRowArray();
                if (!$book) {
                    echo "Book Not Found: " . $ebook_title . "<br/>";
                    continue;
                }

                $author_id = $book['author_id'];
                $book_id = $book['book_id'];

                $book_details = $db->table('book_tbl')->where('book_id', $book_id)->get()->getRowArray();
                $royalty_percentage = $book_details['royalty'] ?? 0;
                $copyright_owner = $book_details['copyright_owner'] ?? '';
                $type_of_book = $book_details['type_of_book'] ?? '';
                $language_id = $book_details['language'] ?? '';

                $author_details = $db->table('author_tbl')->where('author_id', $author_id)->get()->getRowArray();
                $author_type = $author_details['author_type'] ?? 0;
                $user_id = $author_details['user_id'] ?? '';

                $final_royalty_value = $remuneration_inr * ($royalty_percentage / 100);


                $insert_data = [
                    'author' => $author,
                    'title' => $ebook_title,
                    'isbn' => $isbn,
                    'country' => $country,
                    'price_model' => $price_model,
                    'no_of_units' => $no_of_units,
                    'net_receipts_per_hour_local' => $net_receipts_per_hour_local,
                    'ecb_exchange_rate' => $ecb_exchange_rate,
                    'net_receipts_per_hour_inr' => $net_receipts_per_hour_sek,
                    'book_length_in_hours' => $book_length_in_hours,
                    'price_per_unit' => $price_per_unit,
                    'remuneration_eur' => $remuneration_sek,
                    'remuneration_inr' => $remuneration_inr,
                    'publisher' => $publisher,
                    'imprint' => $imprint,
                    'consumption_dates' => $consumption_dates,
                    'book_type' => 'ebook',
                    'book_id' => $book_id,
                    'author_id' => $author_id,
                    'language_id' => $language_id,
                    'final_royalty_value' => $final_royalty_value,
                    'transaction_date' => $transaction_date,
                    'copyright_owner' => $copyright_owner,
                    'user_id' => $user_id,
                    'type_of_book' => $type_of_book,
                    'status' => 'O',
                ];

                echo "<pre>"; print_r($insert_data); echo "</pre><br/>";
                // $db->table('storytel_transactions')->insert($insert_data);
            }
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

       public function AudiobookTransactions()
    {
        $file_name = "Q4_2025_royalty_report.xlsx";
        $transaction_date = '2025-12-31';
        $exchange_rate = 7.1;
        $inputFileName = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'transactions' . DIRECTORY_SEPARATOR . 'storytel_reports' . DIRECTORY_SEPARATOR . $file_name;

        if (!file_exists($inputFileName)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => "File not found: $inputFileName"
            ]);
        }

        try {
            $spreadsheet = IOFactory::load($inputFileName);
            $sheet = $spreadsheet->getSheet(1); // Sheet 2 (0-based index)
            $rows = $sheet->toArray(null, true, true, true); // [nullValue, calculateFormulas, formatData, returnCellRef]

            $header = [];
            $arr_data = [];

            foreach ($rows as $index => $row) {
                if ($index == 1) {
                    $header = $row;
                } else {
                    if (array_filter($row)) { // Skip empty rows
                        $arr_data[$index] = $row;
                    }
                }
            }

            echo "Row count: " . count($arr_data) . "<br/>";

            $db = \Config\Database::connect();

            foreach ($arr_data as $j => $dataRow) {
                $author = $dataRow['A'] ?? '';
                $ebook_title = $dataRow['B'] ?? '';
                $isbn = $dataRow['C'] ?? '';
                $country = $dataRow['E'] ?? '';
                $price_model = $dataRow['F'] ?? '';
                $no_of_units = $dataRow['G'] ?? 0;
                $net_receipts_per_hour_local = $dataRow['H'] ?? 0;
                $ecb_exchange_rate = $dataRow['I'] ?? 0;
                $net_receipts_per_hour_sek = $dataRow['J'] ?? 0;
                $book_length_in_hours = $dataRow['K'] ?? 0;
                $price_per_unit = $dataRow['L'] ?? 0;
                $remuneration_sek = $dataRow['M'] ?? 0;
                $remuneration_inr = $remuneration_sek * $exchange_rate;
                $publisher = $dataRow['O'] ?? '';
                $imprint = $dataRow['P'] ?? '';
                $consumption_dates = $dataRow['Q'] ?? '';

                $book = $db->table('storytel_books')->where('isbn', $isbn)->get()->getRowArray();
                if (!$book) {
                    echo "Book Not Found: " . $ebook_title . "<br/>";
                    continue;
                }

                $author_id = $book['author_id'];
                $book_id = $book['book_id'];

                $book_details = $db->table('book_tbl')->where('book_id', $book_id)->get()->getRowArray();
                $royalty_percentage = $book_details['royalty'] ?? 0;
                $copyright_owner = $book_details['copyright_owner'] ?? '';
                $type_of_book = $book_details['type_of_book'] ?? '';
                $language_id = $book_details['language'] ?? '';

                $author_details = $db->table('author_tbl')->where('author_id', $author_id)->get()->getRowArray();
                $author_type = $author_details['author_type'] ?? 0;
                $user_id = $author_details['user_id'] ?? 0;

                $final_royalty_value = $remuneration_inr * ($royalty_percentage / 100);


                $insert_data = [
                    'author' => $author,
                    'title' => $ebook_title,
                    'isbn' => $isbn,
                    'country' => $country,
                    'price_model' => $price_model,
                    'no_of_units' => $no_of_units,
                    'net_receipts_per_hour_local' => $net_receipts_per_hour_local,
                    'ecb_exchange_rate' => $ecb_exchange_rate,
                    'net_receipts_per_hour_inr' => $net_receipts_per_hour_sek,
                    'book_length_in_hours' => $book_length_in_hours,
                    'price_per_unit' => $price_per_unit,
                    'remuneration_eur' => $remuneration_sek,
                    'remuneration_inr' => $remuneration_inr,
                    'publisher' => $publisher,
                    'imprint' => $imprint,
                    'consumption_dates' => $consumption_dates,
                    'book_type' => 'audiobook',
                    'book_id' => $book_id,
                    'author_id' => $author_id,
                    'language_id' => $language_id,
                    'final_royalty_value' => $final_royalty_value,
                    'transaction_date' => $transaction_date,
                    'copyright_owner' => $copyright_owner,
                    'user_id' => $user_id,
                    'type_of_book' => $type_of_book,
                    'status' => 'O',
                ];

                echo "<pre>"; print_r($insert_data); echo "<br/>";
                // Uncomment below to insert into DB:
                // $db->table('storytel_transactions')->insert($insert_data);
            }
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return show_error($e->getMessage());
        }
    }

}
