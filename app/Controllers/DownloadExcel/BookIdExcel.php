<?php

namespace App\Controllers\DownloadExcel;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BookIdExcel extends BaseController
{
    public function processBookExcel()
    {
        $fileName = "PUSHTHAGA RETURN BOOKS.xlsx";  // your uploaded file
        $filePath = WRITEPATH . "uploads/" . $fileName;

        try {
            // ------------------------------------------------
            // READ EXCEL
            // ------------------------------------------------
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // ------------------------------------------------
            // LOAD ALL BOOKS ONCE (FASTER)
            // ------------------------------------------------
            $allBooks = $this->db->query("SELECT book_id, book_title FROM book_tbl")->getResultArray();

            $finalList = [];

            // Skip header row
            for ($i = 1; $i < count($rows); $i++) {

                $excelTitle = trim($rows[$i][0]);  // A column
                $qty        = (int)$rows[$i][1];   // B column
                $discount   = (float)$rows[$i][2]; // C column

                // Remove " by author" part if present
                if (strpos($excelTitle, ' by ') !== false) {
                    $parts = explode(' by ', $excelTitle);
                    $title = trim($parts[0]);
                } else {
                    $title = $excelTitle;
                }

                // ------------------------------------------------
                // SMART MATCHING LOGIC (Exact → Clean Exact → Partial)
                // ------------------------------------------------

                $book_id = null;
                $exactMatches   = [];
                $cleanMatches   = [];
                $partialMatches = [];

                // Clean title: remove special characters
                $cleanTitle = strtolower(trim(preg_replace('/[^a-zA-Z0-9 ]/', ' ', $title)));

                foreach ($allBooks as $b) {

                    $dbTitle  = $b['book_title'];
                    $dbLower  = strtolower($dbTitle);

                    // Clean DB title
                    $dbClean = strtolower(trim(preg_replace('/[^a-zA-Z0-9 ]/', ' ', $dbTitle)));

                    // 1️⃣ EXACT MATCH
                    if (strcasecmp($dbTitle, $title) === 0) {
                        $exactMatches[] = $b;
                        continue;
                    }

                    // 2️⃣ CLEAN EXACT MATCH
                    if ($dbClean === $cleanTitle) {
                        $cleanMatches[] = $b;
                        continue;
                    }

                    // 3️⃣ PARTIAL FALLBACK
                    if (stripos($dbLower, strtolower($title)) !== false) {
                        $partialMatches[] = $b;
                    }
                }

                // PRIORITY CHOICE
                if (!empty($exactMatches)) {
                    $book_id = $exactMatches[0]['book_id'];
                } elseif (!empty($cleanMatches)) {
                    $book_id = $cleanMatches[0]['book_id'];
                } elseif (!empty($partialMatches)) {
                    $book_id = $partialMatches[0]['book_id'];
                } else {
                    $book_id = null;
                }

                // ------------------------------------------------
                // ADD TO FINAL LIST
                // ------------------------------------------------
                $finalList[] = [
                    "book_id"  => $book_id,
                    "title"    => $excelTitle,
                    "qty"      => $qty,
                    "discount" => $discount
                ];
            }

            return $this->downloadBookResult($finalList);

        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

   
public function processTamilBookExcel()
{
    $fileName = "library-list.xlsx";
    $filePath = WRITEPATH . "uploads/" . $fileName;

    try {
        // ------------------------------------------------
        // READ EXCEL
        // ------------------------------------------------
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        // ------------------------------------------------
        // LOAD BOOK + AUTHOR DATA ONCE
        // ------------------------------------------------
        $allBooks = $this->db->query("
            SELECT 
                b.book_id,
                b.regional_book_title,
                a.author_name
            FROM book_tbl b
            JOIN author_tbl a ON b.author_name = a.author_id
        ")->getResultArray();

        $finalList = [];

        // Skip header
        for ($i = 2; $i <= count($rows); $i++) {

            $excelTitle  = trim($rows[$i]['A']); // Tamil title
            $qty         = (int)$rows[$i]['B'];
            $discount    = (float)$rows[$i]['C'];
            $excelAuthor = trim($rows[$i]['D']); // Author name

            $matchedBooks = [];

            // ------------------------------------------------
            // MATCH BY TAMIL TITLE (LIKE)
            // ------------------------------------------------
            foreach ($allBooks as $b) {
                if (
                    !empty($b['regional_book_title']) &&
                    mb_strpos($b['regional_book_title'], $excelTitle) !== false
                ) {
                    $matchedBooks[] = $b;
                }
            }

            $book_id = null;

            // ------------------------------------------------
            // IF MULTIPLE → CHECK AUTHOR
            // ------------------------------------------------
            if (count($matchedBooks) > 1) {
                foreach ($matchedBooks as $b) {
                    if (
                        !empty($b['author_name']) &&
                        trim($b['author_name']) === $excelAuthor
                    ) {
                        $book_id = $b['book_id'];
                        break;
                    }
                }
            }
            // ------------------------------------------------
            // IF SINGLE MATCH
            // ------------------------------------------------
            elseif (count($matchedBooks) === 1) {
                $book_id = $matchedBooks[0]['book_id'];
            }

            // ------------------------------------------------
            // ADD RESULT
            // ------------------------------------------------
            $finalList[] = [
                'book_id'  => $book_id,
                'title'    => $excelTitle,
                'author'   => $excelAuthor,
                'qty'      => $qty,
                'discount' => $discount
            ];
        }

        return $this->downloadBookResult($finalList);

    } catch (\Exception $e) {
        return $this->response->setBody('Error: ' . $e->getMessage());
    }
}


    // --------------------------------------------------------
    // DOWNLOAD OUTPUT EXCEL
    // --------------------------------------------------------
    public function downloadBookResult($rows)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'book_id');
        $sheet->setCellValue('B1', 'title');
        $sheet->setCellValue('C1', 'qty');
        $sheet->setCellValue('D1', 'discount');

        $r = 2;

        foreach ($rows as $item) {
            $sheet->setCellValue('A' . $r, $item['book_id']);
            $sheet->setCellValue('B' . $r, $item['title']);
            $sheet->setCellValue('C' . $r, $item['qty']);
            $sheet->setCellValue('D' . $r, $item['discount']);
            $r++;
        }

        // Download
        $fileName = "bookIdExcel.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}
