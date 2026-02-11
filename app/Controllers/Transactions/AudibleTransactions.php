<?php
namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AudibleTransactions extends BaseController
{
    public function uploadTransactions()
    {
        $file_name = "Q3_2025.xlsx";
        $inputFileName = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'transactions' . DIRECTORY_SEPARATOR . 'audible_reports' . DIRECTORY_SEPARATOR . $file_name;
        $transaction_date     = '2025-09-30';

        if (!file_exists($inputFileName)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => "File not found: $inputFileName"
            ]);
        }

        try {
            $spreadsheet = IOFactory::load($inputFileName);
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray(null, true, true, true);

            $db = \Config\Database::connect();
            $builder = $db->table('audible_transactions');

            $sum = 0;
            $all_data = [];
            $skipped_rows = [];

            for ($j = 2; $j <= count($data); $j++) {
                $row = $data[$j];

                $royalty_earner       = $row['A'] ?? '';
                $parent_product_id    = $row['B'] ?? '';
                $author               = $row['C'] ?? '';
                $name                 = $row['D'] ?? '';
                $isbn                 = $row['E'] ?? '';
                $provider_product_id  = $row['F'] ?? '';
                $transaction_type     = $row['G'] ?? '';
                $marketplace          = $row['H'] ?? '';
                $purchase_type        = $row['I'] ?? '';
                $offer                = $row['J'] ?? '';
                $royalty_rate         = $row['N'] ?? '';

                $total_qty            = (int) $this->parseAmount($row['P'] ?? 0);
                $total_net_sales      = $this->parseAmount($row['Q'] ?? 0);
                $total_royalty        = $this->parseAmount($row['R'] ?? 0);
             

                $audible_book = $db->table('audible_books')
                                   ->where('product_id', $parent_product_id)
                                   ->get()
                                   ->getRowArray();

                if (!$audible_book) {
                    $skipped_rows[] = [
                        'row_number' => $j,
                        'reason' => 'Parent Product ID not found in audible_books',
                        'parent_product_id' => $parent_product_id,
                        'name' => $name,
                        'author' => $author,
                        'isbn' => $isbn
                    ];
                    continue;
                }

                $language_id = $audible_book['language_id'];
                $author_id = $audible_book['author_id'];
                $book_id = $audible_book['book_id'];
                $copyright_owner = $audible_book['copyright_owner'];

                $book_details = $db->table('book_tbl')
                                   ->where('book_id', $book_id)
                                   ->get()
                                   ->getRowArray();
                $royalty_percentage = $book_details['royalty'] ?? 0;

                $author_details = $db->table('author_tbl')
                                     ->where('author_id', $author_id)
                                     ->get()
                                     ->getRowArray();
                $author_type = $author_details['author_type'] ?? '';

                $final_royalty_value = $total_royalty * ($royalty_percentage / 100);

                $insert_data = [
                    'royalty_earner'      => $royalty_earner,
                    'parent_product_id'   => $parent_product_id,
                    'name'                => $name,
                    'author'              => $author,
                    'isbn'                => $isbn,
                    'provider_product_id' => $provider_product_id,
                    'market_place'        => $marketplace,
                    'offer'               => $offer,
                    'royalty_rate'        => $royalty_rate,
                    'total_qty'           => $total_qty,
                    'total_net_sales'     => $total_net_sales,
                    'total_royalty'       => $total_royalty,
                    'book_id'             => $book_id,
                    'author_id'           => $author_id,
                    'user_id'             => $author_details['user_id'] ?? null,
                    'copyright_owner'     => $copyright_owner,
                    'language_id'         => $language_id,
                    'final_royalty_value' => $final_royalty_value,
                    'transaction_date'    => $transaction_date,
                    'status'              => 'O'
                ];

                 // Uncomment below line to insert into DB
                $builder->insert($insert_data);

                $sum += $total_net_sales;
                $all_data[] = $insert_data;
            }

            echo "<h3>Total Net Sales: $sum</h3>";
            echo "<h4>Total Processed Rows: " . count($all_data) . "</h4>";
            echo "<h4>Total Skipped Rows: " . count($skipped_rows) . "</h4>";

            echo "<h4>Skipped Rows Details:</h4><pre>";
            print_r($skipped_rows);
            print_r($all_data);
            echo "</pre>";

        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return $this->response->setStatusCode(500)->setBody($e->getMessage());
        }
    }

    //  Move parseAmount here
    private function parseAmount($value)
    {
        $value = trim($value);

        if (preg_match('/^\((.*)\)$/', $value, $matches)) {
            return -1 * floatval(str_replace(',', '', $matches[1]));
        }

        $clean = str_replace([',', '₹', '$', '€'], '', $value);
        return floatval($clean);
    }
}
