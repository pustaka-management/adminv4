<?php

namespace App\Controllers\UploadExcel;

use App\Controllers\BaseController;
use Config\Database;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Amazon extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
        helper(['form', 'url', 'file']);
    }

public function uploadEBooks()
    {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '1024M');

        $file_name = "23-jan-26.xlsx";
        $inputFileName = WRITEPATH . 'uploads/ExcelUpload/amazon/' . $file_name;

        /* ========= LOAD EXCEL ========= */
        $spreadsheet = IOFactory::load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        // Remove header
        array_shift($rows);

        /* ========= COUNTERS ========= */
        $totalRows = count($rows);
        $inserted = 0;
        $ignored = 0;

        $ignoredDuplicate = 0;
        $ignoredBookMissing = 0;
        $invalidIsbns = [];

        echo "Excel Rows: {$totalRows}<br/><br/>";

        foreach ($rows as $row) {

            /* ========= COLUMN MAP ========= */
            $reference_id = trim($row['A'] ?? '');
            if ($reference_id === '') {
                $ignored++;
                continue;
            }

            $title      = $row['B'] ?? '';
            $authors    = $row['C'] ?? '';
            $language   = $row['D'] ?? '';
            $rel_raw    = $row['E'] ?? '';
            $pub_raw    = $row['F'] ?? '';
            $asin       = $row['G'] ?? '';

            /* ========= DATE HANDLING ========= */
            $release_date = $rel_raw ? date('Y-m-d', strtotime($rel_raw)) : null;
            $publishing_date = $pub_raw ? date('Y-m-d', strtotime($pub_raw)) : null;

            if (!$release_date && $publishing_date) {
                $release_date = $publishing_date;
            }
            if (!$release_date) {
                $release_date = '1970-01-01';
            }

            /* ========= DUPLICATE CHECK ========= */
            if ($this->db->table('amazon_books')
                ->where('reference_id', $reference_id)
                ->countAllResults() > 0) {

                $ignored++;
                $ignoredDuplicate++;
                continue;
            }

            /* ========= BOOK LOOKUP (YOUR LOGIC) ========= */
            $isbn_id = substr($reference_id, 0, 3);

            $isbn_lang_id = null;
            $isbn_author_id = null;
            $isbn_book_id = null;
            $copyright_owner = null;

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

                $isbn_id = substr($reference_id, 0, 2);

                if ($isbn_id === '35') {

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

                    // Fallback → ISBN number
                    $book_details_result = $this->db->query(
                        "SELECT * FROM book_tbl WHERE REPLACE(isbn_number,'-','') = ?",
                        [$reference_id]
                    )->getRowArray();

                    $isbn_lang_id     = $book_details_result['language'] ?? null;
                    $isbn_author_id   = $book_details_result['author_name'] ?? null;
                    $isbn_book_id     = $book_details_result['book_id'] ?? null;
                    $copyright_owner  = $book_details_result['copyright_owner'] ?? null;
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

            /* ========= INSERT ========= */
            $this->db->table('amazon_books')->insert([
                'reference_id'    => $reference_id,
                'title'           => $title,
                'author'          => $authors,
                'language'        => $language,
                'release_date'    => $release_date,
                'publishing_date' => $publishing_date,
                'asin'            => $asin,
                'book_id'         => $isbn_book_id,
                'author_id'       => $isbn_author_id,
                'language_id'     => $isbn_lang_id,
                'copyright_owner' => $copyright_owner,
                'status'          => 1
            ]);

            $inserted++;
        }

        /* ========= OUTPUT ========= */
        echo "Inserted: {$inserted}<br/>";
        echo "Ignored: {$ignored}<br/><br/>";

        echo "Ignored summary:<br/>";
        echo "- Duplicate reference_id: {$ignoredDuplicate}<br/>";
        echo "- Book not found / invalid ISBN: {$ignoredBookMissing}<br/><br/>";

        if (!empty($invalidIsbns)) {
            echo "Invalid / Book not found ISBNs (sample):<br/>";
            echo "<pre>";
            print_r($invalidIsbns);
            echo "</pre>";
        }

        echo "Upload completed successfully";
    }



    public function amazon_excel()
    {
        $bookModel = new BookModel();
        $authorModel = new AuthorModel();
        $genreModel = new GenreModel();

        $langcode = [
            1 => "TAM",
            4 => "MAL",
            5 => "ENG"
        ];

        $bookIds = explode(',', $this->request->getPost('book_ids'));
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $i = 1;

        foreach ($bookIds as $bk) {
            $bk_result = $bookModel->where('book_id', $bk)->first();
            if (!$bk_result) continue;

            // Generate ISBN
            if (!empty($bk_result['isbn_number'])) {
                $ebook_isbn = str_replace('-', '', $bk_result['isbn_number']);
            } else {
                $lang_num = str_pad($bk_result['language'], 2, '0', STR_PAD_LEFT);
                $auth_num = str_pad($bk_result['author_name'], 3, '0', STR_PAD_LEFT);
                $bk_num = str_pad($bk_result['book_id'], 5, '0', STR_PAD_LEFT);
                $ebook_isbn = 658 . $lang_num . $auth_num . $bk_num;
            }

            // Author and Genre details
            $auth_result = $authorModel->where('author_id', $bk_result['author_name'])->first();
            $gen_result = $genreModel->where('genre_id', $bk_result['genre_id'])->first();

            $short_description = !empty($bk_result['description'])
                ? $bk_result['description']
                : ($auth_result['description'] ?? '');

            $sheet->setCellValue('A' . $i, '');
            $sheet->setCellValueExplicit('B' . $i, $ebook_isbn, DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $i, '');
            $sheet->setCellValue('D' . $i, '');
            $sheet->setCellValue('E' . $i, '');
            $sheet->setCellValue('F' . $i, 'Pustaka Digital Media');
            $sheet->setCellValue('G' . $i, $bk_result['book_title']);
            $sheet->setCellValue('H' . $i, $auth_result['author_name'] ?? '');
            $sheet->setCellValue('I' . $i, '');
            $sheet->setCellValue('J' . $i, '');
            $sheet->setCellValue('K' . $i, '');
            $sheet->setCellValue('N' . $i, $langcode[$bk_result['language']] ?? '');
            $sheet->setCellValue('O' . $i, '');
            $sheet->setCellValue('P' . $i, date('Ymd'));
            $sheet->setCellValue('Q' . $i, $short_description);
            $sheet->setCellValue('R' . $i, $gen_result['bisac_code'] ?? '');

            $i++;
        }

        foreach (range('A', 'R') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'amazon-' . date('Ymd') . '.xls';

        if (ob_get_length()) ob_end_clean();

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function amazon_price_excel()
    {
        $bookModel = new BookModel();

        $bookIds = explode(',', $this->request->getPost('book_ids'));
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $i = 1;

        foreach ($bookIds as $bk) {
            $bk_result = $bookModel->where('book_id', $bk)->first();
            if (!$bk_result) continue;

            // Generate ISBN
            if (!empty($bk_result['isbn_number'])) {
                $ebook_isbn = str_replace('-', '', $bk_result['isbn_number']);
            } else {
                $lang_num = str_pad($bk_result['language'], 2, '0', STR_PAD_LEFT);
                $auth_num = str_pad($bk_result['author_name'], 3, '0', STR_PAD_LEFT);
                $bk_num = str_pad($bk_result['book_id'], 5, '0', STR_PAD_LEFT);
                $ebook_isbn = 658 . $lang_num . $auth_num . $bk_num;
            }

            // Row 1 - USD
            $sheet->setCellValueExplicit('A' . $i, $ebook_isbn, DataType::TYPE_STRING);
            $sheet->setCellValue('B' . $i, $bk_result['book_cost_international']);
            $sheet->setCellValue('C' . $i, 'USD');

            // Row 2 - INR
            $i2 = $i + 1;
            $sheet->setCellValueExplicit('A' . $i2, $ebook_isbn, DataType::TYPE_STRING);
            $priceINR = ($bk_result['cost'] >= 49) ? $bk_result['cost'] : 49;
            $sheet->setCellValue('B' . $i2, $priceINR);
            $sheet->setCellValue('C' . $i2, 'INR');

            $i += 2;
        }

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'amazon-pricing-' . date('Ymd') . '.xls';

        if (ob_get_length()) ob_end_clean();

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
   public function uploadPaperbacks()
{
    ini_set('max_execution_time', 600);
    ini_set('memory_limit', '1024M');

    $file_name = "Amazon_Paperback_25_Oct.xlsx";
    $inputFile = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'ExcelUpload' . DIRECTORY_SEPARATOR . 'amazon' . DIRECTORY_SEPARATOR . $file_name;

    try {
        // Load spreadsheet
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFile);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $db = \Config\Database::connect();
        $builder = $db->table('amazon_paperback_books');

        $totalInserted = 0;
        $totalSkipped = 0;

        // 🔹 Fetch all existing book_ids once (to avoid checking DB for each record)
        $existingBooks = $builder->select('book_id')->get()->getResultArray();
        $existingBookIds = array_column($existingBooks, 'book_id');

        // Loop through rows (skip header)
        for ($r = 2; $r <= count($rows); $r++) {
            $row = $rows[$r];

            $skuString = trim($row['A'] ?? '');
            $asin = trim($row['B'] ?? '');
            $price = trim($row['C'] ?? '');

            // Skip invalid or unwanted SKU
            if ($skuString === '' || substr($skuString, 0, 2) === 'OP') {
                continue;
            }

            // Handle comma-separated SKUs
            $skuArray = strpos($skuString, ',') !== false ? explode(',', $skuString) : [$skuString];

            foreach ($skuArray as $sku) {
                $sku = trim($sku);
                if ($sku === '') continue;

                // 🔍 Skip if already exists
                if (in_array($sku, $existingBookIds)) {
                    $totalSkipped++;
                    continue;
                }

                // Fetch book details from book_tbl
                $book = $db->table('book_tbl')->where('book_id', $sku)->get()->getRowArray();
                if (!$book) {
                    $totalSkipped++;
                    continue;
                }

                $insertData = [
                    'book_id'         => $sku,
                    'asin'            => $asin,
                    'price'           => $price,
                    'copyright_owner' => $book['copyright_owner'] ?? '',
                    'author_id'       => $book['author_name'] ?? '',
                    'language'        => $book['language'] ?? ''
                ];

                $builder->insert($insertData);
                $existingBookIds[] = $sku; // Add new one to memory list
                $totalInserted++;
            }
        }

        echo "✅ Upload complete.<br/>";
        echo "📘 Total new records inserted: {$totalInserted}<br/>";
        echo "⚠️  Total skipped (existing or invalid): {$totalSkipped}<br/>";
    } catch (\Exception $e) {
        log_message('error', 'Upload Error: ' . $e->getMessage());
        return $this->response->setJSON([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}


}
