<?php

namespace App\Controllers\DownloadExcel;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Config\Database;


class ChannelExcel extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
        helper(['form', 'url', 'file', 'html', 'cookie', 'string']);
        session();
    }

    public function amazon_excel()
    {
        $langcode = [
            1 => "TAM",
            4 => "MAL",
            5 => "ENG"
        ];

        $book_ids = $this->request->getPost('book_ids');
        $book_id = explode(",", $book_ids);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $i = 1;

        foreach ($book_id as $bk) {
            $bk_result = $this->db->table('book_tbl')->where('book_id', $bk)->get()->getRowArray();
            if (!$bk_result) continue;

            if (!empty($bk_result['isbn_number'])) {
                $ebook_isbn = str_replace('-', '', $bk_result['isbn_number']);
            } else {
                $lang_num = str_pad($bk_result['language'], 2, '0', STR_PAD_LEFT);
                $auth_num = str_pad($bk_result['author_name'], 3, '0', STR_PAD_LEFT);
                $bk_num   = str_pad($bk_result['book_id'], 5, '0', STR_PAD_LEFT);

                if (strlen((string)$auth_num) < 4) {
                    $ebook_isbn = '658' . str_pad($lang_num, 2, '0', STR_PAD_LEFT) . str_pad($auth_num, 3, '0', STR_PAD_LEFT) . $bk_num;
                } else {
                    $ebook_isbn = '35' . $lang_num . $auth_num . $bk_num;
                }
            }

            $auth_result = $this->db->table('author_tbl')->where('author_id', $bk_result['author_name'])->get()->getRowArray();
            $gen_result  = $this->db->table('genre_details_tbl')->where('genre_id', $bk_result['genre_id'])->get()->getRowArray();

            $short_description = !empty($bk_result['description']) ? $bk_result['description'] : ($auth_result['description'] ?? '');

            // Fill Excel
            $sheet->setCellValue('A'.$i, '');
            $sheet->setCellValueExplicit('B'.$i, $ebook_isbn, DataType::TYPE_STRING);
            $sheet->setCellValue('C'.$i, '');
            $sheet->setCellValue('D'.$i, '');
            $sheet->setCellValue('E'.$i, '');
            $sheet->setCellValue('F'.$i, "Pustaka Digital Media");
            $sheet->setCellValue('G'.$i, $bk_result['book_title']);
            $sheet->setCellValue('H'.$i, $auth_result['author_name'] ?? '');
            $sheet->setCellValue('I'.$i, '');
            $sheet->setCellValue('J'.$i, '');
            $sheet->setCellValue('K'.$i, '');
            $sheet->setCellValue('N'.$i, $langcode[$bk_result['language']] ?? '');
            $sheet->setCellValue('O'.$i, "");
            $sheet->setCellValue('P'.$i, date('Ymd'));
            $sheet->setCellValue('Q'.$i, $short_description);
            $sheet->setCellValue('R'.$i, $gen_result['bisac_code'] ?? '');
            $i++;
        }

        foreach (range('A', 'R') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'amazon.xls';
        if (ob_get_length()) ob_end_clean();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function amazon_price_excel()
    {
        $book_ids = $this->request->getPost('book_ids');
        $book_id = explode(",", $book_ids);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $i = 1;

        foreach ($book_id as $bk) {
            $bk_result = $this->db->table('book_tbl')->where('book_id', $bk)->get()->getRowArray();
            if (!$bk_result) continue;

            if (!empty($bk_result['isbn_number'])) {
                $ebook_isbn = str_replace('-', '', $bk_result['isbn_number']);
            } else {
                $lang_num = str_pad($bk_result['language'], 2, '0', STR_PAD_LEFT);
                $auth_num = str_pad($bk_result['author_name'], 3, '0', STR_PAD_LEFT);
                $bk_num   = str_pad($bk_result['book_id'], 5, '0', STR_PAD_LEFT);

                if (strlen((string)$auth_num) < 4) {
                    $ebook_isbn = '658' . $lang_num . str_pad($auth_num, 3, '0', STR_PAD_LEFT) . $bk_num;
                } else {
                    $ebook_isbn = '35' . $lang_num . $auth_num . $bk_num;
                }
            }

            $sheet->setCellValueExplicit('A'.$i, $ebook_isbn, DataType::TYPE_STRING);
            $sheet->setCellValue('B'.$i, $bk_result['book_cost_international']);
            $sheet->setCellValue('C'.$i, 'USD');

            $i2 = $i + 1;
            $sheet->setCellValueExplicit('A'.$i2, $ebook_isbn, DataType::TYPE_STRING);
            $sheet->setCellValue('B'.$i2, ($bk_result['cost'] >= 49) ? $bk_result['cost'] : 49);
            $sheet->setCellValue('C'.$i2, 'INR');

            $i += 2;
        }

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'amazon-pricing.xls';
        if (ob_get_length()) ob_end_clean();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function amazonPaperback_excel_download()
    {
        $db = \Config\Database::connect();

        // Get posted book IDs
        $book_ids = trim($this->request->getPost('book_ids'));
        if (empty($book_ids)) {
            return redirect()->back()->with('error', 'Please enter Book IDs');
        }

        // Clean and explode into array
        $book_id_arr = array_filter(array_map('trim', explode(',', $book_ids)));

        if (empty($book_id_arr)) {
            return redirect()->back()->with('error', 'Invalid Book IDs');
        }

        // Prepare SQL with IN condition
        $builder = $db->table('book_tbl');
        $builder->select("
            book_tbl.book_id,
            author_tbl.author_name,
            CONCAT(book_tbl.book_title, ' | ', author_tbl.author_name, ' | ', genre_details_tbl.genre_name, ' | ', language_tbl.language_name, ' | ', 'Pustaka') AS title,
            CONCAT(book_tbl.book_title, ', ', author_tbl.author_name, ', ', genre_details_tbl.genre_name, ', ', language_tbl.language_name, ', ', 'Pustaka') AS search_terms,
            language_tbl.language_name,
            genre_details_tbl.genre_name,
            book_tbl.regional_book_title,
            genre_details_tbl.amazon_rbn,
            genre_details_tbl.bisac_sub_code,
            genre_details_tbl.bisac_sub_heading_code,
            genre_details_tbl.bisac_code,
            book_tbl.paper_back_pages,
            book_tbl.paper_back_isbn,
            book_tbl.paper_back_inr
        ");
        $builder->join('author_tbl', 'book_tbl.author_name = author_tbl.author_id');
        $builder->join('genre_details_tbl', 'book_tbl.genre_id = genre_details_tbl.genre_id');
        $builder->join('language_tbl', 'language_tbl.language_id = book_tbl.language');
        $builder->whereIn('book_tbl.book_id', $book_id_arr);
        $builder->where('book_tbl.paper_back_readiness_flag', 1);

        $records = $builder->get()->getResultArray();

        if (empty($records)) {
            return redirect()->back()->with('error', 'No records found for given Book IDs');
        }

        // Get height-weight mapping
        $weight_records = $db->query("SELECT * FROM height_weight ORDER BY paperback_page")->getResultArray();

        // Create Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = [
            'A1' => 'Book ID',
            'B1' => 'Title',
            'C1' => 'Search Terms',
            'D1' => 'Language',
            'E1' => 'Genre',
            'F1' => 'Regional Title',
            'G1' => 'Amazon RBN',
            'H1' => 'BISAC Sub Code',
            'I1' => 'BISAC Sub Heading',
            'J1' => 'BISAC Code',
            'K1' => 'Pages',
            'L1' => 'Height',
            'M1' => 'Weight',
            'N1' => 'ISBN',
            'O1' => 'Author',
            'P1' => 'INR'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $i = 2; // Start from row 2 (after header)
        foreach ($records as $record) {
            $pages = (int)$record['paper_back_pages'];
            $h = '';
            $w = '';

            foreach ($weight_records as $weight_record) {
                if ($pages <= $weight_record['paperback_page']) {
                    $h = $weight_record['height'];
                    $w = $weight_record['weight'] + 0.6;
                    break;
                }
            }

            $sheet->setCellValue('A' . $i, $record['book_id']);
            $sheet->setCellValue('B' . $i, $record['title']);
            $sheet->setCellValue('C' . $i, $record['search_terms']);
            $sheet->setCellValue('D' . $i, $record['language_name']);
            $sheet->setCellValue('E' . $i, $record['genre_name']);
            $sheet->setCellValue('F' . $i, $record['regional_book_title']);
            $sheet->setCellValue('G' . $i, $record['amazon_rbn']);
            $sheet->setCellValue('H' . $i, $record['bisac_sub_code']);
            $sheet->setCellValue('I' . $i, $record['bisac_sub_heading_code']);
            $sheet->setCellValue('J' . $i, $record['bisac_code']);
            $sheet->setCellValue('K' . $i, $record['paper_back_pages']);
            $sheet->setCellValue('L' . $i, $h);
            $sheet->setCellValue('M' . $i, $w);
            $sheet->setCellValue('N' . $i, $record['paper_back_isbn']);
            $sheet->setCellValue('O' . $i, $record['author_name']);
            $sheet->setCellValue('P' . $i, $record['paper_back_inr']);

            $i++;
        }

        // Auto-size columns
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output Excel
        $filename = 'amazon_paperback_books_' . date('Ymd_His') . '.xls';
        if (ob_get_length()) ob_end_clean();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function scribd_excel()
    {
        $langcode = [
            1 => "tam",
            2 => "kan",
            3 => "tel",
            4 => "mal",
            5 => "eng"
        ];

        $book_ids = $this->request->getPost('book_ids');
        $book_id = explode(",", $book_ids);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $i = 1;

        foreach ($book_id as $bk) {
            $bk_result = $this->db->table('book_tbl')->where('book_id', $bk)->get()->getRowArray();
            if (!$bk_result) continue;

            // Generate ISBN
            if (!empty($bk_result['isbn_number'])) {
                $ebook_isbn = str_replace('-', '', $bk_result['isbn_number']);
            } else {
                $lang_num = str_pad($bk_result['language'], 2, '0', STR_PAD_LEFT);
                $auth_num = str_pad($bk_result['author_name'], 3, '0', STR_PAD_LEFT);
                $bk_num   = str_pad($bk_result['book_id'], 5, '0', STR_PAD_LEFT);
                if (strlen((string)$auth_num) < 4) {
                    $ebook_isbn = '658' . $lang_num . str_pad($auth_num, 3, '0', STR_PAD_LEFT) . $bk_num;
                } else {
                    $ebook_isbn = '35' . $lang_num . $auth_num . $bk_num;
                }
            }

            $auth_result = $this->db->table('author_tbl')->where('author_id', $bk_result['author_name'])->get()->getRowArray();
            $gen_result  = $this->db->table('genre_details_tbl')->where('genre_id', $bk_result['genre_id'])->get()->getRowArray();

            $short_description = !empty($bk_result['description']) ? $bk_result['description'] : ($auth_result['description'] ?? '');

            $bk_format = explode('.', $bk_result['epub_url']);

            if ($bk_result['type_of_book'] == 1) {
                $sheet->setCellValue('A'.$i, "Pustaka Digital Media");
                $sheet->setCellValue('B'.$i, '');
                $sheet->setCellValueExplicit('C'.$i, $ebook_isbn, DataType::TYPE_STRING);
                $sheet->setCellValue('D'.$i, $bk_format[1] ?? '');
                $sheet->setCellValue('E'.$i, $ebook_isbn.'.'.($bk_format[1] ?? ''));
                $sheet->setCellValue('F'.$i, $bk_result['book_title']);
                $sheet->setCellValue('G'.$i, '');
                $sheet->setCellValue('H'.$i, $auth_result['author_name'] ?? '');
                $sheet->setCellValue('I'.$i, date('d/m/y'));
                $sheet->setCellValue('J'.$i, date('d/m/y'));
                $sheet->setCellValue('K'.$i, $bk_result['book_cost_international']);
                $sheet->setCellValue('L'.$i, "USD");
                $sheet->setCellValue('M'.$i, "WORLD");
                $sheet->setCellValue('N'.$i, "");
                $sheet->setCellValue('O'.$i, $short_description);
                $sheet->setCellValue('P'.$i, $gen_result['bisac_code'] ?? '');
                $sheet->setCellValue('Q'.$i, $bk_result['number_of_page']);
                $sheet->setCellValue('R'.$i, '');
                $sheet->setCellValue('S'.$i, '');
                $sheet->setCellValue('T'.$i, '');
                $sheet->setCellValue('U'.$i, '');
                $sheet->setCellValue('V'.$i, '');
                $sheet->setCellValue('W'.$i, $langcode[$bk_result['language']] ?? '');
                $i++;
            }
        }

        foreach (range('A', 'W') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename='scribd.xls';

        if (ob_get_length()) ob_end_clean();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function storytel_excel()
{
    $db = db_connect();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    /* ===================== LANGUAGE CODES ===================== */
    $langcode = [
        1 => "ta",
        2 => "kn",
        3 => "te",
        4 => "ml",
        5 => "en"
    ];

    /* ===================== GENRE MAP ===================== */
    $genre_id = [
        1=>"Fiction",2=>"Thrillers",3=>"Crime",4=>"History",5=>"Fiction",
        6=>"Thrillers",7=>"Thrillers",8=>"Personal Development",
        9=>"Non-Fiction",10=>"Personal Development",11=>"Fiction",
        12=>"Religion & Spirituality",13=>"Classics",14=>"Biographies",
        15=>"Children",16=>"Language",17=>"Personal Development",
        18=>"Non-Fiction",19=>"Fantasy & Scifi",20=>"Personal Development",
        21=>"Non-Fiction",22=>"Non-Fiction",23=>"Romance",24=>"Romance",
        25=>"Non-Fiction",26=>"Fiction",27=>"Economy & Business",
        28=>"Non-Fiction",29=>"Romance",30=>"Fiction",31=>"Non-Fiction",
        32=>"Non-Fiction",33=>"Non-Fiction",34=>"Non-Fiction",
        35=>"Non-Fiction",36=>"Romance"
    ];

    /* ===================== BOOK IDS ===================== */
    $book_ids = $this->request->getPost('book_ids');
    if (!$book_ids) {
        return redirect()->back()->with('error', 'No Book IDs provided');
    }

    $book_ids = explode(',', $book_ids);

    $row = 1;

    foreach ($book_ids as $bk) {

        $bk_result = $db->table('book_tbl')
                        ->where('book_id', trim($bk))
                        ->get()
                        ->getRowArray();

        if (!$bk_result) continue;

        if (in_array($bk_result['author_name'], [80,76,82,81])) continue;

        /* ===================== ISBN ===================== */
        if (!empty($bk_result['isbn_number'])) {
            $ebook_isbn = str_replace('-', '', $bk_result['isbn_number']);
        } else {
            $lang_num = str_pad($bk_result['language'], 2, '0', STR_PAD_LEFT);
            $auth_num = str_pad($bk_result['author_name'], 3, '0', STR_PAD_LEFT);
            $bk_num   = str_pad($bk_result['book_id'], 5, '0', STR_PAD_LEFT);
            $ebook_isbn = '658' . $lang_num . $auth_num . $bk_num;
        }

        /* ===================== AUTHOR ===================== */
        $author = $db->table('author_tbl')
                     ->where('author_id', $bk_result['author_name'])
                     ->get()
                     ->getRowArray();

        if (!$author) continue;

        $short_description = $bk_result['description'] ?: $author['description'];

        /* ===================== GENRE ===================== */
        $genre_data = $db->table('genre_details_tbl')
                         ->where('genre_id', $bk_result['genre_id'])
                         ->get()
                         ->getRowArray();

        $genre_code = $genre_data['storytel_genre_code'] ?? '';

        /* ===================== NARRATOR ===================== */
        $narrator_name = '';
        if ($bk_result['narrator_id']) {
            $narrator = $db->table('narrator_tbl')
                           ->where('narrator_id', $bk_result['narrator_id'])
                           ->get()
                           ->getRowArray();
            $narrator_name = $narrator['narrator_name'] ?? '';
        }

        
        $sheet->setCellValue('A'.$row, $bk_result['book_id']);
        $sheet->setCellValue('B'.$row, $bk_result['book_title']);
        $sheet->setCellValue('C'.$row, $langcode[$bk_result['language']] ?? '');
        $sheet->setCellValue('D'.$row, $author['author_name']);
        $sheet->setCellValue('E'.$row, $bk_result['type_of_book'] == 1 ? '' : $narrator_name);
        $sheet->setCellValue('G'.$row, $bk_result['regional_book_title']);
        $sheet->setCellValue('H'.$row, $genre_code);
        $sheet->setCellValue('L'.$row, $ebook_isbn);
        $sheet->setCellValue('N'.$row, date('Ymd'));
        $sheet->setCellValue('O'.$row, 'Pustaka Digital Media');
        $sheet->setCellValue('P'.$row, $short_description);
        $sheet->setCellValue('T'.$row, 'World');

        $row++;
    }

    $filename = 'storytel.xls';

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header('Cache-Control: max-age=0');

    $writer = new Xls($spreadsheet);
    $writer->save('php://output');
    exit;
}
    public function storytel_audio_excel()
{
    // Use PhpSpreadsheet instead of PHPExcel
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Language codes
    $langcode = [
        1 => "ta",
        2 => "kn",
        3 => "te",
        4 => "ml",
        5 => "en"
    ];

    // Genre mapping
    $genre_id = [
        1 => "Fiction",
        2 => "Thrillers",
        3 => "Crime",
        4 => "History",
        5 => "Fiction",
        6 => "Thrillers",
        7 => "Thrillers",
        8 => "Personal Development",
        9 => "Non-Fiction",
        10 => "Personal Development",
        11 => "Fiction",
        12 => "Religion & Spirituality",
        13 => "Classics",
        14 => "Biographies",
        15 => "Children",
        16 => "Language",
        17 => "Personal Development",
        18 => "Non-Fiction",
        19 => "Fantasy & Scifi",
        20 => "Personal Development",
        21 => "Non-Fiction",
        22 => "Non-Fiction",
        23 => "Romance",
        24 => "Romance",
        25 => "Non-Fiction",
        26 => "Fiction",
        27 => "Economy & Business",
        28 => "Non-Fiction",
        29 => "Romance",
        30 => "Fiction",
        31 => "Non-Fiction",
        32 => "Non-Fiction",
        33 => "Non-Fiction",
        34 => "Non-Fiction",
        35 => "Non-Fiction",
        36 => "Romance"
    ];

    $book_ids = $this->request->getPost('book_ids');
    $book_id_arr = explode(",", $book_ids);
    $db = \Config\Database::connect();

    $i = 1;
    foreach ($book_id_arr as $bk) {
        $bk_result = $db->table('book_tbl')->where('book_id', $bk)->get()->getRowArray();
        if (!$bk_result) continue;

        // Skip unwanted narrators
        if (in_array($bk_result['narrator_id'], [80, 76, 82, 81])) continue;

        // Fetch author and narrator details
        $auth_result = $db->table('author_tbl')->where('author_id', $bk_result['author_name'])->get()->getRowArray();
        $narrator_result = $db->table('narrator_tbl')->where('narrator_id', $bk_result['narrator_id'])->get()->getRowArray();

        $narrator_name = $narrator_result['narrator_name'] ?? '';
        $author_name = $auth_result['author_name'] ?? '';

        $short_description = !empty($bk_result['description'])
            ? $bk_result['description']
            : ($auth_result['description'] ?? '');

        // Genre
        if ($bk_result['book_category'] == "Short Stories")
            $genre = "Short Stories";
        elseif ($bk_result['book_category'] == "Poetry")
            $genre = "Lyric Poetry";
        else
            $genre = $genre_id[$bk_result['genre_id']] ?? '';

        // Fill Excel
        $sheet->setCellValue('A'.$i, $bk_result['book_id']);
        $sheet->setCellValue('B'.$i, $bk_result['book_title']);
        $sheet->setCellValue('C'.$i, $langcode[$bk_result['language']] ?? '');
        $sheet->setCellValue('D'.$i, $author_name);
        $sheet->setCellValue('E'.$i, $narrator_name);
        $sheet->setCellValue('F'.$i, $bk_result['regional_book_title']);
        $sheet->setCellValue('G'.$i, $genre);
        $sheet->setCellValue('H'.$i, "Pustaka Digital Media");
        $sheet->setCellValue('I'.$i, date('Ymd'));
        $sheet->setCellValue('J'.$i, $short_description);
        $sheet->setCellValue('K'.$i, "World");

        $i++;
    }

    // Auto-size columns
    foreach (range('A', 'K') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Output Excel
    if (ob_get_length()) ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="storytel_audio_books.xls"');
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
    $writer->save('php://output');
    exit;
}


    
    public function google_excel()
    {
        $langcode = [
            1 => "TAM",
            2 => "KAN",
            3 => "TEL",
            4 => "MAL",
            5 => "ENG"
        ];

        $book_ids = $this->request->getPost('book_ids');
        $book_id = explode(",", $book_ids);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $i = 1;

        foreach ($book_id as $bk) {
            $bk_result = $this->db->table('book_tbl')->where('book_id', $bk)->get()->getRowArray();
            if (!$bk_result) continue;

            if ($bk_result['isbn_number'] != '') {
                $ebook_isbn = str_replace('-', '', $bk_result['isbn_number']);
            } else {
                $lang_num = str_pad($bk_result['language'], 2, '0', STR_PAD_LEFT);
                $auth_num = str_pad($bk_result['author_name'], 3, '0', STR_PAD_LEFT);
                $bk_num = str_pad($bk_result['book_id'], 5, '0', STR_PAD_LEFT);
                // $ebook_isbn = 658 . $lang_num . $auth_num . $bk_num;
                if (strlen((string)$auth_num) < 4) {
                    $ebook_isbn = '658' . $lang_num . str_pad($auth_num, 3, '0', STR_PAD_LEFT) . $bk_num;
                } else {
                    $ebook_isbn = '35' . $lang_num . $auth_num . $bk_num;
                }
            }

            $auth_result = $this->db->table('author_tbl')->where('author_id', $bk_result['author_name'])->get()->getRowArray();
            $gen_result = $this->db->table('genre_details_tbl')->where('genre_id', $bk_result['genre_id'])->get()->getRowArray();

            $short_description = !empty($bk_result['description']) ? $bk_result['description'] : ($auth_result['description'] ?? '');

            $bk_format = explode('.', $bk_result['epub_url']);
            $related_identifier = 'PKEY:' . $ebook_isbn . ' [Digital, Is part of];';
            $ebook_isbn_full = 'PKEY:' . $ebook_isbn;

            $sheet->setCellValue('A'.$i, $ebook_isbn_full);
            $sheet->setCellValue('B'.$i, "Yes");
            $sheet->setCellValue('C'.$i, $bk_result['book_title']);
            $sheet->setCellValue('D'.$i, '');
            $sheet->setCellValue('E'.$i, "Digital");
            $sheet->setCellValue('F'.$i, $related_identifier);
            $sheet->setCellValue('G'.$i, ($auth_result['author_name'] ?? '') . ' [Author]');
            $sheet->setCellValue('H'.$i, $auth_result['description'] ?? '');
            $sheet->setCellValue('I'.$i, $langcode[$bk_result['language']] ?? '');
            $sheet->setCellValue('J'.$i, ($gen_result['bisac_code'] ?? '') . ' [BISAC]');
            $sheet->setCellValue('K'.$i, '');
            $sheet->setCellValue('L'.$i, $short_description);
            $sheet->setCellValue('M'.$i, '');
            $sheet->setCellValue('N'.$i, $bk_result['number_of_page']);
            $sheet->setCellValue('O'.$i, '');
            $sheet->setCellValue('P'.$i, '');
            $sheet->setCellValue('Q'.$i, '10%');
            $sheet->setCellValue('R'.$i, 'WORLD');
            $sheet->setCellValue('S'.$i, '');
            $sheet->setCellValue('T'.$i, '');
            $sheet->setCellValue('U'.$i, 'Pustaka Digital Media');
            $sheet->setCellValue('V'.$i, 'http://www.pustaka.co.in');
            $sheet->setCellValue('W'.$i, 'Yes');
            $sheet->setCellValue('X'.$i, 'No');
            $sheet->setCellValue('Y'.$i, '');
            $sheet->setCellValue('Z'.$i, 'Yes');
            $sheet->setCellValue('AA'.$i, 'Yes');
            $sheet->setCellValue('AB'.$i, 'Yes');
            $sheet->setCellValue('AC'.$i, 'No');
            $sheet->setCellValue('AD'.$i, '0%');
            $sheet->setCellValue('AE'.$i, $bk_result['cost']);
            $sheet->setCellValue('AF'.$i, 'IN');
            $sheet->setCellValue('AG'.$i, $bk_result['book_cost_international']);
            $sheet->setCellValue('AH'.$i, 'WORLD');

            $i++;
        }

        $highestColumn = 'AH';
        $col = 'A';
        while ($col !== false) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
            if ($col === $highestColumn) {
                break;
            }
            $col++;
        }

        $filename = 'googlebooks.xls';

        if (ob_get_length()) ob_end_clean();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function google_audio_excel()
{
    $db = \Config\Database::connect();

    $langcode = [
        1 => "TAM",
        2 => "KAN",
        3 => "TEL",
        4 => "MAL",
        5 => "ENG"
    ];

    $book_ids = $this->request->getPost('book_ids');

    if (!$book_ids) {
        return redirect()->back();
    }

    $book_id = explode(",", $book_ids);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $i = 1;

    foreach ($book_id as $bk) {

        $bk_result = $db->table('book_tbl')->where('book_id', $bk)->get()->getRowArray();

        if (!$bk_result) continue;

        // ISBN generate
        if (!empty($bk_result['isbn_number'])) {

            $ebook_isbn = str_replace('-', '', $bk_result['isbn_number']);

        } else {

            $lang_num = str_pad($bk_result['language'], 2, '0', STR_PAD_LEFT);
            $auth_num = str_pad($bk_result['author_name'], 3, '0', STR_PAD_LEFT);
            $bk_num   = str_pad($bk_result['book_id'], 5, '0', STR_PAD_LEFT);

            if (strlen((string)$auth_num) < 4)
                $ebook_isbn = '658' . $lang_num . $auth_num . $bk_num;
            else
                $ebook_isbn = '35' . $lang_num . $auth_num . $bk_num;
        }

        $auth_result = $db->table('author_tbl')->where('author_id', $bk_result['author_name'])->get()->getRowArray();
        $gen_result  = $db->table('genre_details_tbl')->where('genre_id', $bk_result['genre_id'])->get()->getRowArray();

        $short_description = !empty($bk_result['description']) ? $bk_result['description'] : ($auth_result['description'] ?? '');

        $related_identifier = 'PKEY:' . $ebook_isbn . ' [Digital, Is part of];';
        $ebook_isbn = 'PKEY:' . $ebook_isbn;

        $sheet->setCellValue('A'.$i, $ebook_isbn);
        $sheet->setCellValue('B'.$i, "Yes");
        $sheet->setCellValue('C'.$i, $bk_result['book_title']);
        $sheet->setCellValue('E'.$i, "Audiobook");
        $sheet->setCellValue('F'.$i, $related_identifier);

        $sheet->setCellValue('G'.$i, ($auth_result['author_name'] ?? '') . ' [Author]');
        $sheet->setCellValue('H'.$i, $auth_result['description'] ?? '');
        $sheet->setCellValue('I'.$i, $langcode[$bk_result['language']] ?? '');
        $sheet->setCellValue('J'.$i, ($gen_result['bisac_code'] ?? '').' [BISAC]');
        $sheet->setCellValue('L'.$i, $short_description);

        $sheet->setCellValue('Q'.$i, '10%');
        $sheet->setCellValue('R'.$i, 'WORLD');
        $sheet->setCellValue('U'.$i, 'Pustaka Digital Media');
        $sheet->setCellValue('V'.$i, 'http://www.pustaka.co.in');

        $sheet->setCellValue('Z'.$i, 'Yes');
        $sheet->setCellValue('AA'.$i, 'Yes');
        $sheet->setCellValue('AB'.$i, 'Yes');

        $sheet->setCellValue('AE'.$i, $bk_result['number_of_page']);
        $sheet->setCellValue('AI'.$i, $bk_result['cost']);
        $sheet->setCellValue('AK'.$i, $bk_result['book_cost_international']);
        $sheet->setCellValue('AM'.$i, $bk_result['book_cost_international']);

        $i++;
    }

    $writer = new Xls($spreadsheet);

    $filename = "googleaudiobooks.xls";

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header('Cache-Control: max-age=0');

    $writer->save("php://output");
    exit;
}
    public function overdrive_excel()
{
    helper('filesystem');

    // Load composer autoload
    require_once ROOTPATH . 'vendor/autoload.php';

    $db = \Config\Database::connect();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->setActiveSheetIndex(0);

    // Get book IDs from POST
    $book_ids = $this->request->getPost('book_ids');
    $book_id_arr = explode(",", $book_ids);

    $i = 1;

    foreach ($book_id_arr as $bk) {

        $bk = trim($bk);
        if ($bk == "") continue;

        // Book details
        $bk_result = $db->table('book_tbl')->where('book_id', $bk)->get()->getRowArray();
        if (!$bk_result) continue;

        // --- COST MAPPING FIXED ---
        $price = (float)$bk_result['book_cost_international'];

        $price_map = [
            0.09 => 2,
            1.99 => 12,
            3.49 => 18,
            4.99 => 26,
            6.24 => 34,
            7.49 => 40,
            8.74 => 46,
            9.99 => 52,
            11.24 => 58,
            12.49 => 64,
            13.74 => 70,
            14.99 => 76,
        ];

        $cost = $price_map[$price] ?? 0;   // Always set

        // Author details
        $auth = $db->table('author_tbl')
                   ->where('author_id', $bk_result['author_name'])
                   ->get()
                   ->getRowArray();

        // Genre
        $gen = $db->table('genre_details_tbl')
                  ->where('genre_id', $bk_result['genre_id'])
                  ->get()
                  ->getRowArray();

            if ($bk_result['isbn_number'] != '') {
                $ebook_isbn = str_replace('-', '', $bk_result['isbn_number']);
            } else {
                $lang_num = str_pad($bk_result['language'], 2, '0', STR_PAD_LEFT);
                $auth_num = str_pad($bk_result['author_name'], 3, '0', STR_PAD_LEFT);
                $bk_num = str_pad($bk_result['book_id'], 5, '0', STR_PAD_LEFT);
                 if (strlen((string)$auth_num) < 4) {
                    $ebook_isbn = '658' . $lang_num . str_pad($auth_num, 3, '0', STR_PAD_LEFT) . $bk_num;
                } else {
                    $ebook_isbn = '35' . $lang_num . $auth_num . $bk_num;
                }
            }
        // Language
        $lang = $db->table('language_tbl')
                   ->where('language_id', $bk_result['language'])
                   ->get()
                   ->getRowArray();

        // ISBN Generate
        if (!empty($bk_result['isbn_number'])) {
            $ebook_isbn = str_replace('-', '', $bk_result['isbn_number']);
        } else {
            $lang_num = str_pad($bk_result['language'], 2, '0', STR_PAD_LEFT);
            $auth_num = str_pad($bk_result['author_name'], 3, '0', STR_PAD_LEFT);
            $bk_num   = str_pad($bk_result['book_id'], 5, '0', STR_PAD_LEFT);

            if (strlen($auth_num) < 4)
                $ebook_isbn = '658' . $lang_num . $auth_num . $bk_num;
            else
                $ebook_isbn = '35' . $lang_num . $auth_num . $bk_num;
        }

        // File names
        $ext = (pathinfo($bk_result['epub_url'], PATHINFO_EXTENSION) == "epub") ? "epub" : "pdf";
        $epubfilename  = $ebook_isbn . "." . $ext;
        $coverfilename = $ebook_isbn . ".jpg";

        $tag = $lang['language_name'] . ', ' . $gen['genre_name'];

        $description = !empty($bk_result['description'])
            ? $bk_result['description']
            : ($auth['description'] ?? "");

        // --- WRITE EXCEL COLUMNS ---
        $sheet->setCellValue('A' . $i, $bk_result['book_id']);
        $sheet->setCellValue('B' . $i, $bk_result['book_title']);
        $sheet->setCellValue('C' . $i, $epubfilename);
        $sheet->setCellValue('H' . $i, $auth['author_name']);
        $sheet->setCellValue('T' . $i, "Pustaka Digital Media");
        $sheet->setCellValue('U' . $i, "Pustaka Digital Media");

        // COST FIXED HERE
        $sheet->setCellValue('AB' . $i, $cost);
        $sheet->setCellValue('AC' . $i, $cost);

        $sheet->setCellValue('AD' . $i, 'USD');
        $sheet->setCellValue('AF' . $i, date('m/d/Y'));
        $sheet->setCellValue('AO' . $i, $tag);
        $sheet->setCellValue('AP' . $i, $description);
        $sheet->setCellValue('AS' . $i, $coverfilename);
        $sheet->setCellValue('BB' . $i, 'N');
        $sheet->setCellValue('BC' . $i, 'N');
        $sheet->setCellValue('BD' . $i, 'N');

        $i++;
    }

    // Output file
    $filename = 'overdrive.xls';
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new Xls($spreadsheet);
    $writer->save('php://output');
}
     public function pratilipi_excel()
    {
        $langcode = [
            1 => "Tamil",
            2 => "Kannada",
            3 => "Telugu",
            4 => "Malayalam",
            5 => "English"
        ];

        // Genre mappings
        $genre_mapping = [
            1=>"family",2=>"supernatural-thriller",3=>"detective",4=>"historical",5=>"social",
            6=>"suspense",7=>"suspense",8=>"selfhelp",9=>"travel",10=>"motivational",
            11=>"social",12=>"relegion-and-spiritual",13=>"literature",14=>"biography",
            15=>"children",16=>"literature",17=>"health",18=>"sciencefiction",19=>"books-and-movies",
            20=>"health",21=>"politics",22=>"education",23=>"romance",24=>"romance",
            25=>"research",26=>"social",27=>"business",28=>"information",29=>"romance",
            30=>"comedy",31=>"astrology",32=>"philosophy",33=>"women",34=>"cooking",
            35=>"agriculture",36=>"erotica",37=>"relegion-and-spiritual",38=>"sciencefiction"
        ];

        $tamil_genre_mapping = [1=>"குடும்பம்",2=>"அமானுஷ்யம்",3=>"துப்பறிவு",4=>"வரலாறு",5=>"சமூகம்", /* ... rest same as CI3 ... */];

        $kannada_genre_mapping = [1=>"ಕೌಟುಂಬಿಕ",2=>"ಅಲೌಕಿಕ",3=>"ಪತ್ತೇದಾರಿ", /* ... */];
        $telugu_genre_mapping = [1=>"కుటుంబం",2=>"",3=>"డిటెక్టివ్", /* ... */];
        $malayalam_genre_mapping = [1=>"ബന്ധങ്ങള്‍",2=>"അമാനുഷിക",3=>"ഡിറ്റക്ടീവ്", /* ... */];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $i = 1;
        $count = 0;
        $bk_id = 9957;

        while ($count < 450) {
            $bk_result = $this->db->table('book_tbl')->where('book_id', $bk_id)->get()->getRowArray();

            if (!$bk_result || $bk_result['status'] != 1 || $bk_result['genre_id'] == 15 || $bk_result['type_of_book'] != 1 || $bk_result['author_name'] == 4) {
                $bk_id++;
                continue;
            }

            $auth_result = $this->db->table('author_tbl')->where('author_id', $bk_result['author_name'])->get()->getRowArray();
            $auth_lang_result = $this->db->table('author_language')->where(['author_id'=>$bk_result['author_name'], 'language_id'=>$bk_result['language']])->get()->getRowArray();
            $lang_result = $this->db->table('language_tbl')->where('language_id', $bk_result['language'])->get()->getRowArray();

            $blurb = !empty($bk_result['description']) ? $bk_result['description'] : ($auth_result['description'] ?? '');

            $lang_num = str_pad($bk_result['language'],2,'0', STR_PAD_LEFT);
            $auth_num = str_pad($bk_result['author_name'],3,'0',STR_PAD_LEFT);
            $bk_num = str_pad($bk_result['book_id'],5,'0',STR_PAD_LEFT);
            if (strlen((string)$auth_num) < 4) {
                $ebook_isbn = '658' . $lang_num . str_pad($auth_num, 3, '0', STR_PAD_LEFT) . $bk_num;
            } else {
                $ebook_isbn = '35' . $lang_num . $auth_num . $bk_num;
            }
            $book_category = strtolower($bk_result['book_category'] ?? '');
            if (in_array($book_category, ['novel','drama','epic','short stories','puranam'])) $content_type = "STORY";
            elseif (in_array($book_category, ['poetry','sthothram'])) $content_type = "POEM";
            elseif (in_array($book_category, ['articles','essay','magazine'])) $content_type = "ARTICLE";
            else $content_type = "";

            $genre_id = $bk_result['genre_id'];
            $pratilipi_genre_name = $genre_mapping[$genre_id] ?? '';
            $pratilipi_regional_genre_name = match($bk_result['language']) {
                1 => $tamil_genre_mapping[$genre_id] ?? $pratilipi_genre_name,
                2 => $kannada_genre_mapping[$genre_id] ?? $pratilipi_genre_name,
                3 => $telugu_genre_mapping[$genre_id] ?? $pratilipi_genre_name,
                4 => $malayalam_genre_mapping[$genre_id] ?? $pratilipi_genre_name,
                default => $pratilipi_genre_name
            };

            $keywords = implode(',', [
                $bk_result['book_title'] ?? '',
                $bk_result['regional_book_title'] ?? '',
                $lang_result['language_name'] ?? '',
                $pratilipi_genre_name
            ]);

            $sheet->setCellValue('A'.$i, $ebook_isbn);
            $sheet->setCellValue('B'.$i, $bk_result['book_title']);
            $sheet->setCellValue('C'.$i, $bk_result['regional_book_title']);
            $sheet->setCellValue('D'.$i, $auth_result['author_name'] ?? '');
            $sheet->setCellValue('E'.$i, $auth_lang_result['regional_author_name'] ?? '');
            $sheet->setCellValue('F'.$i, $blurb);
            $sheet->setCellValue('G'.$i, $lang_result['language_name'] ?? '');
            $sheet->setCellValue('H'.$i, $content_type);
            $sheet->setCellValue('I'.$i, $pratilipi_genre_name);
            $sheet->setCellValue('J'.$i, $pratilipi_regional_genre_name);
            $sheet->setCellValue('K'.$i, $keywords);
            $sheet->setCellValue('L'.$i, $bk_id);

            $i++;
            $count++;
            $bk_id++;
        }

        $filename='pratilipi.xlsx';
        if (ob_get_length()) ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function overdrive_audio_excel()
{
    helper('filesystem');

    // Correct autoload path for CI4 + Composer
    require_once ROOTPATH . 'vendor/autoload.php';

    $db = \Config\Database::connect();
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->setActiveSheetIndex(0);

    $book_ids = $this->request->getPost('book_ids');
    $book_id_arr = explode(",", $book_ids);

    $i = 1;

    foreach ($book_id_arr as $bk)
    {
        $bk_details = $db->table('book_tbl')->where('book_id', $bk)->get()->getRowArray();
        if (!$bk_details) continue;

        // ISBN logic …
        if (!empty($bk_details['isbn_number'])) {
            $ebook_isbn = str_replace('-', '', $bk_details['isbn_number']);
        } else {
            $lang_num = str_pad($bk_details['language'], 2, '0', STR_PAD_LEFT);
            $auth_num = str_pad($bk_details['author_name'], 3, '0', STR_PAD_LEFT);
            $bk_num = str_pad($bk_details['book_id'], 5, '0', STR_PAD_LEFT);

            if (strlen($auth_num) < 4) {
                $ebook_isbn = '658' . $lang_num . $auth_num . $bk_num;
            } else {
                $ebook_isbn = '35' . $lang_num . $auth_num . $bk_num;
            }
        }

        $auth_result = $db->table('author_tbl')->where('author_id', $bk_details['author_name'])->get()->getRowArray();
        $gen_result  = $db->table('genre_details_tbl')->where('genre_id', $bk_details['genre_id'])->get()->getRowArray();
        $lang_result = $db->table('language_tbl')->where('language_id', $bk_details['language'])->get()->getRowArray();

        $cost = $bk_details['book_cost_international'] * 10;

        $tag = $lang_result['language_name'] . ', ' . $gen_result['genre_name'];
        $short_description = !empty($bk_details['description']) ? $bk_details['description'] : $auth_result['description'];

        $file_ext = pathinfo($bk_details['epub_url'], PATHINFO_EXTENSION);
        $epubfilename = $ebook_isbn . "." . ($file_ext == 'epub' ? 'epub' : 'pdf');
        $coverfilename = $ebook_isbn . ".jpg";

        // Fill Excel
        $sheet->setCellValue('A'.$i, $bk_details['book_title']);
        $sheet->setCellValue('C'.$i, 'Abridged');
        $sheet->setCellValue('D'.$i, 'First');
        $sheet->setCellValue('F'.$i, $auth_result['author_name']);
        $sheet->setCellValue('H'.$i, 'Author');
        $sheet->setCellValue('K'.$i, 'Narrator');
        $sheet->setCellValue('R'.$i, 'Pustaka Digital Media');
        $sheet->setCellValue('S'.$i, 'Pustaka Digital Media');
        $sheet->setCellValue('W'.$i, date('m/d/Y'));
        $sheet->setCellValue('X'.$i, $cost);
        $sheet->setCellValue('Y'.$i, $cost);
        $sheet->setCellValue('Z'.$i, 'USD');
        $sheet->setCellValue('AA'.$i, date('m/d/Y'));
        $sheet->setCellValue('AB'.$i, 'ta - Tamil');
        $sheet->setCellValue('AC'.$i, 'World');
        $sheet->setCellValue('AD'.$i, 'Fiction');
        $sheet->setCellValue('AH'.$i, $gen_result['bisac_code']);
        $sheet->setCellValue('AJ'.$i, $tag);
        $sheet->setCellValue('AK'.$i, $short_description);
        $sheet->setCellValue('AN'.$i, $coverfilename);
        $sheet->setCellValue('AX'.$i, 'N');
        $sheet->setCellValue('AY'.$i, 'N');

        $i++;
    }

    // Output file
    $filename = "overdrive-audio.xls";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Cache-Control: max-age=0");

    $writer = new Xls($spreadsheet);
    $writer->save('php://output');
    exit;
}

    
}
