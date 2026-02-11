<?php
namespace App\Controllers\Transactions;

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Controllers\BaseController;

class KukufmTransactions extends BaseController
{
    public function uploadTransactions()
    {
        $file_name = "kukufm_Q4_2025.xlsx";
        $transaction_date="2025-12-31";
        $inputFileName = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'transactions' . DIRECTORY_SEPARATOR . 'kukufm_reports' . DIRECTORY_SEPARATOR . $file_name;

        if (!file_exists($inputFileName)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => "File not found: $inputFileName"
            ]);
        }

        try {
            $spreadsheet = IOFactory::load($inputFileName);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $header = [];
            $arr_data = [];

            for ($row = 1; $row <= $highestRow; $row++) {
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $cellValue = $worksheet->getCell($col . $row)->getValue();

                    if ($row == 1) {
                        $header[$col] = $cellValue;
                    } else {
                        $arr_data[$row][$col] = $cellValue;
                    }
                }
            }

            $db = \Config\Database::connect();
            $builder = $db->table('kukufm_transactions');

            $validRowCount = 0;

            for ($j = 2; $j <= $highestRow; $j++) {
                $show_id = trim($arr_data[$j]['A'] ?? '');

                //Skip row if show_id is empty
                if ($show_id === '') {
                    continue;
                }

                $validRowCount++;

                $show_name = $arr_data[$j]['B'] ?? '';
                $language = $arr_data[$j]['C'] ?? '';
                $show_revenue = $arr_data[$j]['G'] ?? 0;
                $rev_share_amount = $arr_data[$j]['H'] ?? 0;

                // Fetch book details
                $kukufm_book = $db->table('kukufm_books')->where('show_id', $show_id)->get()->getRowArray();

                if (!$kukufm_book) {
                    echo "No show id: " . $show_id . "<br/>";
                    continue;
                }

                $book_id = $kukufm_book['book_id'];
                $author_id = $kukufm_book['author_id'];
                $copyright_owner = $kukufm_book['copyright_owner'];
                $language = $kukufm_book['language'];

                // Get royalty and language_id
                $book_info = $db->table('book_tbl')->where('book_id', $book_id)->get()->getRowArray();
                $royalty = $book_info['royalty'] ?? 0;
                $language_id = $book_info['language'] ?? null;

                $final_royalty_value = $rev_share_amount * ($royalty / 100);

                $insert_data = [
                    'show_id' => $show_id,
                    'show_name' => $show_name,
                    'published_date' => null,
                    'language' => $language,
                    'book_id' => $book_id,
                    'author_id' => $author_id,
                    'copyright_owner' => $copyright_owner,
                    'language_id' => $language_id,
                    'content_earning_amount' => $show_revenue,
                    'rev_share_percentage' => 40,
                    'rev_share_amount' => $rev_share_amount,
                    'final_royalty_value' => $final_royalty_value,
                    'transaction_date' => $transaction_date,
                    'status' => 'O'
                ];

                echo "<pre>";
                print_r($insert_data);
                echo "--------------------------------</pre>";

                $builder->insert($insert_data);
            }

            echo "<br/>Valid Excel Rows Processed: $validRowCount";

        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return $this->response->setStatusCode(500)->setBody("Error: " . $e->getMessage());
        }
    }
}
