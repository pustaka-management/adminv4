<?php

namespace App\Controllers\DownloadExcel;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

use App\Models\RoyaltyModel;

class RoyaltyExcel extends BaseController
{

    protected $royaltyModel;
    protected $helpers = ['form', 'url', 'file', 'email', 'html', 'cookie', 'string'];

    public function __construct()
    {
        helper($this->helpers);

        $this->royaltyModel = new RoyaltyModel();
        session();
    }

   public function downloadBankExcel()
    {
        $royaltyModel = new RoyaltyModel();


        $royaltyData = $this->royaltyModel->getRoyaltyConsolidatedData();
       

		// Separate total bonus and royalty data
		// $total_bonus_sum = $royaltyResult['total_bonus_sum'] ?? 0;
		// unset($royaltyResult['total_bonus_sum']);
		// $royaltyData= $royaltyResult; 

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $currentDate = date('M Y');
        $i = 1;

        
        foreach ($royaltyData as $record) {
           // Calculate adjusted total_after_tds considering excess/advance
            $adjusted_after_tds = $record['total_after_tds'];

            if (!empty($record['excess_payment']) && $record['excess_payment'] > 0) {
                $adjusted_after_tds -= $record['excess_payment'];
            }

            if (!empty($record['advance_payment']) && $record['advance_payment'] > 0) {
                $adjusted_after_tds -= $record['advance_payment'];
            }

            if ($adjusted_after_tds > 500 && $record['bank_status'] === "Yes") {
                $publisher_royalty = $record['publisher_name'] . " Author Royalty " . $currentDate;
                $pub_royalty = preg_replace('/[^A-Za-z0-9]/', '', $record['publisher_name']) . "AuthorRoyalty" . $currentDate;
                $pub_name_year = substr(preg_replace('/[^A-Za-z0-9]/', '', $record['publisher_name']), 0, 6) . date('MY');
                $pustaka_acc_no = "918020059111502";

                $sheet->setCellValueExplicit('A' . $i, $pustaka_acc_no, DataType::TYPE_STRING);
                $sheet->setCellValue('B' . $i, number_format($adjusted_after_tds, 2, '.', ''));
                $sheet->setCellValue('C' . $i, $record['bank_acc_name']);
                $sheet->setCellValueExplicit('D' . $i, $record['bank_acc_no'], DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('E' . $i, $record['ifsc_code'], DataType::TYPE_STRING);
                $sheet->setCellValue('F' . $i, date('d-m-Y'));
                $sheet->setCellValue('G' . $i, "N");
                $sheet->setCellValueExplicit('H' . $i, $record['mobile'], DataType::TYPE_STRING);
                $sheet->setCellValue('I' . $i, $pub_name_year);
                
               
                // $sheet->setCellValue('F' . $i, $record['email_id']);
                // $sheet->setCellValue('G' . $i, $publisher_royalty);
                // $sheet->setCellValue('K' . $i, "10");
                // $sheet->setCellValue('L' . $i, $pub_royalty);
               
                // $sheet->setCellValue('N' . $i, number_format($record['ebooks_outstanding'], 2, '.', ''));
                // $sheet->setCellValue('O' . $i, number_format($record['audiobooks_outstanding'], 2, '.', ''));
                // $sheet->setCellValue('P' . $i, number_format($record['paperbacks_outstanding'], 2, '.', ''));
                // $sheet->setCellValue('Q' . $i, number_format($record['bonus_value'], 2, '.', ''));
                // $sheet->setCellValue('R' . $i, number_format($record['tds_value'], 2, '.', ''));
                // $sheet->setCellValue('S' . $i, number_format($record['excess_payment'], 2, '.', ''));
                // $sheet->setCellValue('T' . $i, number_format($record['advance_payment'], 2, '.', ''));
                $i++;
            }
        }

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'bank-excel-' . $currentDate . '.xls';

        // Clear any previous output
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Set HTTP headers for file download
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        // Write Excel to output
        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
