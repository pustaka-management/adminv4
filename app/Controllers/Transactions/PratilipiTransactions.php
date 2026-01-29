<?php
namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use DateTime;

use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\HTTP\ResponseInterface;

class PratilipiTransactions extends BaseController
{
    public function UploadTransactions()
    {
        $file_name = "Q4_2025.xlsx";
        $inputFileName = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'transactions' . DIRECTORY_SEPARATOR . 'pratilipi_reports' . DIRECTORY_SEPARATOR . $file_name;

        $transaction_date = "2025-12-31";

        if (!file_exists($inputFileName)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => "File not found: $inputFileName"
            ]);
        }

     
        try {
            $spreadsheet = IOFactory::load($inputFileName);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestDataRow();

            $db = \Config\Database::connect();
            $all_data = [];
            $skipped_rows = [];

            for ($row = 2; $row <= $highestRow; $row++) {
                $user_id = trim((string) $worksheet->getCell("A$row")->getValue());
                $author_name = trim((string) $worksheet->getCell("B$row")->getValue());
                $currency = trim((string) $worksheet->getCell("C$row")->getValue());
                $earning_raw = $worksheet->getCell("H$row")->getValue();

                // Skip if required fields are missing (earning can be 0)
                if ($user_id === '' || $author_name === '' || $earning_raw === null) {
                    $skipped_rows[] = [
                        'row' => $row,
                        'reason' => "Missing required fields",
                        'user_id' => $user_id,
                        'author_name' => $author_name,
                        'earning' => $earning_raw
                    ];
                    continue;
                }

                // Clean earning value
                $earning = (float) preg_replace("/[^0-9.]/", "", $earning_raw);

                // Author lookup
                $author = $db->table('author_tbl')
                    ->where('author_name', $author_name)
                    ->get()
                    ->getRowArray();

                if (!$author) {
                    $skipped_rows[] = [
                        'row' => $row,
                        'reason' => "Author not found",
                        'user_id' => $user_id,
                        'author_name' => $author_name,
                        'earning' => $earning
                    ];
                    continue;
                }

                $final_royalty_value = $earning * 0.5;

                $insert_data = [
                    'user_id' => $user_id,
                    'earning' => $earning,
                    'currency' => $currency,
                    'author_name' => $author_name,
                    'author_id' => $author['author_id'],
                    'copyright_owner' => $author['copyright_owner'],
                    'final_royalty_value' => $final_royalty_value,
                    'type_of_book' => 1,
                    'transaction_date' => $transaction_date,
                    'status' => 'O',
                ];

                $all_data[] = $insert_data;

                // Uncomment if you want to insert
                // $db->table('pratilipi_transactions')->insert($insert_data);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Transactions uploaded successfully',
                'skipped' => count($skipped_rows),
                'skipped_details' => $skipped_rows,
                'count' => count($all_data),
                'data' => $all_data
            ]);
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
