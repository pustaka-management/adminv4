<?php

namespace App\Controllers\UploadExcel;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use DateTime;
use Exception;


class Overdrive extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
     

    public function OverdriveUpload()
{
    ini_set('max_execution_time', 600);
    ini_set('memory_limit', '1024M');

    $file_name = "overdrive-ebooks.xlsx";
    $inputFileName = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'ExcelUpload' . DIRECTORY_SEPARATOR . 'OverdriveReport' . DIRECTORY_SEPARATOR . $file_name;

    try {
        $spreadsheet = IOFactory::load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestDataRow();

        $insertedCount = 0;
        $skippedData = [];

        // Reason counters
        $missingFieldCount = 0;
        $alreadyExistsCount = 0;
        $bookNotFoundCount = 0;

        for ($row = 2; $row <= $highestRow; $row++) {
            $overdrive_id = trim((string)$sheet->getCell('A' . $row)->getValue());
            $catalogue_id = trim((string)$sheet->getCell('B' . $row)->getValue());
            $isbn = trim((string)$sheet->getCell('C' . $row)->getValue());
            $title = trim((string)$sheet->getCell('E' . $row)->getValue());
            $creators = trim((string)$sheet->getCell('K' . $row)->getValue());
            $whs_usd = (float)$sheet->getCell('O' . $row)->getValue();
            $lib_usd = (float)$sheet->getCell('Q' . $row)->getValue();
            $onsale_date1 = trim((string)$sheet->getCell('S' . $row)->getValue());
            $status = trim((string)$sheet->getCell('U' . $row)->getValue());

            // 1️⃣ Skip missing fields
            if (empty($overdrive_id) || empty($title)) {
                $skippedData[] = [
                    'row' => $row,
                    'overdrive_id' => $overdrive_id,
                    'catalogue_id' => $catalogue_id,
                    'isbn' => $isbn,
                    'title' => $title ?: '(No Title)',
                    'creators' => $creators,
                    'reason' => 'Missing Overdrive ID or Title'
                ];
                $missingFieldCount++;
                continue;
            }

            // 2️⃣ Check if already exists in `overdrive_books`
            $existing = $this->db->table('overdrive_books')
                ->where('overdrive_id', $overdrive_id)
                ->get()
                ->getRowArray();

            if ($existing) {
                $skippedData[] = [
                    'row' => $row,
                    'overdrive_id' => $overdrive_id,
                    'catalogue_id' => $catalogue_id,
                    'isbn' => $isbn,
                    'title' => $title,
                    'creators' => $creators,
                    'reason' => 'Already Exists in overdrive_books'
                ];
                $alreadyExistsCount++;
                continue;
            }

            // 3️⃣ Convert date safely
            $onsale_date = null;
            if (!empty($onsale_date1)) {
                $dateObj = DateTime::createFromFormat('m/d/Y', $onsale_date1);
                if ($dateObj) {
                    $onsale_date = $dateObj->format('Y-m-d');
                }
            }

            // 4️⃣ Find matching book in `book_tbl`
            $book_details_result = null;

            if (empty($isbn)) {
                if (empty($catalogue_id)) {
                    // Search by title
                    $book_details_result = $this->db->table('book_tbl')
                        ->where('book_title', $title)
                        ->get()
                        ->getRowArray();
                } else {
                    if (substr($catalogue_id, 0, 3) === '978') {
                        // ISBN pattern
                        $book_details_result = $this->db->query(
                            "SELECT * FROM book_tbl WHERE REPLACE(isbn_number, '-', '') = ?",
                            [str_replace('-', '', $catalogue_id)]
                        )->getRowArray();
                    } elseif (substr($catalogue_id, 0, 2) === '35') {
                        // Internal 35xxxxx pattern
                        $isbn_lang_id   = ltrim(substr($catalogue_id, 2, 2), '0');
                        $isbn_author_id = ltrim(substr($catalogue_id, 4, 4), '0');
                        $isbn_book_id   = ltrim(substr($catalogue_id, 8, 5), '0');

                        $book_details_result = $this->db->table('book_tbl')
                            ->where('book_id', $isbn_book_id)
                            ->where('author_name', $isbn_author_id)
                            ->get()
                            ->getRowArray();
                    } else {
                        // Other pattern
                        if (strlen($catalogue_id) >= 14) {
                            $isbn_lang_id   = ltrim(substr($catalogue_id, 3, 2), '0');
                            $isbn_author_id = ltrim(substr($catalogue_id, 5, 4), '0');
                            $isbn_book_id   = ltrim(substr($catalogue_id, 9, 5), '0');
                        } else {
                            $isbn_lang_id   = ltrim(substr($catalogue_id, 3, 2), '0');
                            $isbn_author_id = ltrim(substr($catalogue_id, 5, 3), '0');
                            $isbn_book_id   = ltrim(substr($catalogue_id, 8, 5), '0');
                        }

                        $book_details_result = $this->db->table('book_tbl')
                            ->where('book_id', $isbn_book_id)
                            ->where('author_name', $isbn_author_id)
                            ->get()
                            ->getRowArray();
                    }
                }
            } else {
                // Direct ISBN match
                $book_details_result = $this->db->query(
                    "SELECT * FROM book_tbl WHERE REPLACE(isbn_number, '-', '') = ?",
                    [str_replace('-', '', $isbn)]
                )->getRowArray();
            }

            // 5️⃣ Book not found
            if (!$book_details_result) {
                $skippedData[] = [
                    'row' => $row,
                    'overdrive_id' => $overdrive_id,
                    'catalogue_id' => $catalogue_id,
                    'isbn' => $isbn,
                    'title' => $title,
                    'creators' => $creators,
                    'reason' => 'Book not found in book_tbl'
                ];
                $bookNotFoundCount++;
                continue;
            }

            // 6️⃣ Prepare insert data
            $insert_data = [
                'overdrive_id'      => $overdrive_id,
                'catalogue_id'      => $catalogue_id ?: 0,
                'isbn'              => $isbn ?: 0,
                'title'             => $title,
                'creators'          => $creators,
                'whs_usd'           => $whs_usd ?: 0,
                'lib_usd'           => $lib_usd ?: 0,
                'onsale_date'       => $onsale_date,
                'status'            => $status,
                'book_id'           => $book_details_result['book_id'] ?? null,
                'author_id'         => $book_details_result['author_name'] ?? null,
                'copyright_owner'   => $book_details_result['copyright_owner'] ?? null,
                'language_id'       => $book_details_result['language'] ?? null,
                'type_of_book'      => $book_details_result['type_of_book'] ?? null
            ];

            // 7️⃣ Insert record
            $this->db->table('overdrive_books')->insert($insert_data);
            $insertedCount++;
        }

        // ✅ Summary
        echo "<h2>Overdrive Upload Report</h2>";
        echo "<p><strong>Total Excel Rows:</strong> " . ($highestRow - 1) . "</p>";
        echo "<p><strong>Inserted Records:</strong> {$insertedCount}</p>";
        echo "<p><strong>Total Skipped:</strong> " . count($skippedData) . "</p>";
        echo "<ul>
                <li> Missing Overdrive ID/Title: {$missingFieldCount}</li>
                <li> Already Exists: {$alreadyExistsCount}</li>
                <li> Book Not Found: {$bookNotFoundCount}</li>
              </ul>";

        if (!empty($skippedData)) {
            echo "<h3>Skipped Records</h3>
                  <table border='1' cellspacing='0' cellpadding='5'>
                    <thead>
                      <tr>
                        <th>Row</th><th>Overdrive ID</th><th>Catalogue ID</th><th>ISBN</th>
                        <th>Title</th><th>Creators</th><th>Reason</th>
                      </tr>
                    </thead><tbody>";
            foreach ($skippedData as $skip) {
                echo "<tr>
                        <td>{$skip['row']}</td>
                        <td>{$skip['overdrive_id']}</td>
                        <td>{$skip['catalogue_id']}</td>
                        <td>{$skip['isbn']}</td>
                        <td>" . htmlspecialchars($skip['title']) . "</td>
                        <td>" . htmlspecialchars($skip['creators']) . "</td>
                        <td><strong style='color:red;'>{$skip['reason']}</strong></td>
                      </tr>";
            }
            echo "</tbody></table>";
        }

    } catch (Exception $e) {
        log_message('error', 'Overdrive Upload Error: ' . $e->getMessage());
        echo "<h3 style='color:red;'>Error: {$e->getMessage()}</h3>";
    }
}


  public function uploadAudiobooks()
{
    ini_set('max_execution_time', 600);
    ini_set('memory_limit', '1024M');

    $fileName = "overdrive-audiobooks.xlsx";
    $inputFileName = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'ExcelUpload' . DIRECTORY_SEPARATOR . 'OverdriveReport' . DIRECTORY_SEPARATOR . $fileName;

    try {
        $spreadsheet = IOFactory::load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestDataRow();

        $insertedCount = 0;
        $skippedData = [];

        $columnNames = range('A', 'Z');
        $columnNames = array_merge($columnNames, ['AA', 'AB', 'AC', 'AD']);

        for ($row = 2; $row <= $highestRow; $row++) {
            $overdriveId = trim((string)$sheet->getCell($columnNames[0] . $row)->getValue());
            $catalogueId = trim((string)$sheet->getCell($columnNames[1] . $row)->getValue());
            $isbn = trim((string)$sheet->getCell($columnNames[2] . $row)->getValue());
            $title = trim((string)$sheet->getCell($columnNames[4] . $row)->getValue());
            $creators = trim((string)$sheet->getCell($columnNames[10] . $row)->getValue());
            $subject = trim((string)$sheet->getCell($columnNames[11] . $row)->getValue());
            $format = trim((string)$sheet->getCell($columnNames[12] . $row)->getValue());
            $filesize = trim((string)$sheet->getCell($columnNames[13] . $row)->getValue());
            $whsUsd = $sheet->getCell($columnNames[14] . $row)->getValue() ?: 0;
            $whsUsdDiscount = $sheet->getCell($columnNames[15] . $row)->getValue() ?: 0;
            $libUsd = trim((string)$sheet->getCell($columnNames[16] . $row)->getValue());
            $libUsdDiscount = trim((string)$sheet->getCell($columnNames[17] . $row)->getValue());
            $onsaleDate = DateTime::createFromFormat('m/d/Y', $sheet->getCell($columnNames[18] . $row)->getValue())->format('Y-m-d');
            $pubDate = !empty($sheet->getCell($columnNames[19] . $row)->getValue()) ?
                DateTime::createFromFormat('m/d/Y', $sheet->getCell($columnNames[19] . $row)->getValue())->format('Y-m-d') : null;
            $status = trim((string)$sheet->getCell($columnNames[20] . $row)->getValue());
            $sampleLink = trim((string)$sheet->getCell($columnNames[21] . $row)->getValue());
            $readboxEnabled = trim((string)$sheet->getCell($columnNames[22] . $row)->getValue());

            // Skip if essential data is missing
            if (empty($title) || empty($overdriveId)) {
                $skippedData[] = [
                    'row' => $row,
                    'overdrive_id' => $overdriveId,
                    'catalogue_id' => $catalogueId,
                    'isbn' => $isbn,
                    'title' => $title ?: '(No Title)',
                    'creators' => $creators,
                    'reason' => 'Missing Overdrive ID or Title'
                ];
                continue;
            }

            // ✅ STEP 1: Check if already exists in overdrive_books
            $existingRecord = $this->db->table('overdrive_books')
                ->where('overdrive_id', $overdriveId)
                ->get()
                ->getRowArray();

            if ($existingRecord) {
                $skippedData[] = [
                    'row' => $row,
                    'overdrive_id' => $overdriveId,
                    'catalogue_id' => $catalogueId,
                    'isbn' => $isbn,
                    'title' => $title,
                    'creators' => $creators,
                    'reason' => 'Already Exists in overdrive_books'
                ];
                continue; // skip further processing
            }

            // ✅ STEP 2: Find book details
            $book_details_result = null;

            if (empty($catalogueId)) {
                // No catalogue_id, search by title
                $book_details_result = $this->db->table('book_tbl')
                    ->where('book_title', $title)
                    ->get()
                    ->getRowArray();

                // Try " - Audio Book" variation if not found
                if (!$book_details_result) {
                    $book_details_result = $this->db->table('book_tbl')
                        ->where('book_title', $title . ' - Audio Book')
                        ->get()
                        ->getRowArray();
                }

            } else {
                if (substr($catalogueId, 0, 3) === '978') {
                    $book_details_result = $this->db->query(
                        "SELECT * FROM book_tbl WHERE REPLACE(isbn_number, '-', '') = ?",
                        [str_replace('-', '', $catalogueId)]
                    )->getRowArray();

                } elseif (substr($catalogueId, 0, 2) === '35') {
                    $isbn_lang_id = ltrim(substr($catalogueId, 2, 2), '0');
                    $isbn_author_id = ltrim(substr($catalogueId, 4, 4), '0');
                    $isbn_book_id = ltrim(substr($catalogueId, 8, 5), '0');

                    $book_details_result = $this->db->table('book_tbl')
                        ->where('book_id', $isbn_book_id)
                        ->where('author_name', $isbn_author_id)
                        ->get()
                        ->getRowArray();

                } else {
                    $isbn_lang_id = ltrim(substr($catalogueId, 3, 2), '0');
                    $isbn_author_id = ltrim(substr($catalogueId, 5, 3), '0');
                    $isbn_book_id = ltrim(substr($catalogueId, 8, 5), '0');

                    $book_details_result = $this->db->table('book_tbl')
                        ->where('book_id', $isbn_book_id)
                        ->where('author_name', $isbn_author_id)
                        ->get()
                        ->getRowArray();
                }
            }

            // ✅ STEP 3: If not found → skip
            if (!$book_details_result) {
                $skippedData[] = [
                    'row' => $row,
                    'overdrive_id' => $overdriveId,
                    'catalogue_id' => $catalogueId,
                    'isbn' => $isbn,
                    'title' => $title,
                    'creators' => $creators,
                    'reason' => 'Book not found in book_tbl'
                ];
                continue;
            }

            // ✅ STEP 4: Insert new record
            $insertData = [
                'overdrive_id'      => $overdriveId,
                'catalogue_id'      => $catalogueId,
                'isbn'              => $isbn,
                'title'             => $title,
                'creators'          => $creators,
                'subject'           => $subject,
                'format'            => $format,
                'filesize'          => $filesize,
                'whs_usd'           => $whsUsd,
                'whs_usddiscount'   => $whsUsdDiscount,
                'lib_usd'           => $libUsd,
                'lib_usddiscount'   => $libUsdDiscount,
                'onsale_date'       => $onsaleDate,
                'pub_date'          => $pubDate,
                'sample_link'       => $sampleLink,
                'status'            => $status,
                'readbox_enabled'   => $readboxEnabled,
                'book_id'           => $book_details_result['book_id'],
                'author_id'         => $book_details_result['author_name'],
                'copyright_owner'   => $book_details_result['copyright_owner'],
                'language_id'       => $book_details_result['language'],
                'type_of_book'      => $book_details_result['type_of_book']
            ];

            $this->db->table('overdrive_books')->insert($insertData);
            $insertedCount++;
        }

        // ✅ Summary
        echo "<h2>Audiobooks Upload Report</h2>";
        echo "<p><strong>Total Excel Rows:</strong> " . ($highestRow - 1) . "</p>";
        echo "<p><strong>Inserted Records:</strong> {$insertedCount}</p>";
        echo "<p><strong>Skipped Records:</strong> " . count($skippedData) . "</p>";

        if (!empty($skippedData)) {
            echo "<h3>Skipped Audiobooks</h3>";
            echo "<table border='1' cellspacing='0' cellpadding='5'>
                    <thead>
                        <tr>
                            <th>Row</th>
                            <th>Overdrive ID</th>
                            <th>Catalogue ID</th>
                            <th>ISBN</th>
                            <th>Title</th>
                            <th>Creators</th>
                            <th>Reason</th>
                        </tr>
                    </thead><tbody>";
            foreach ($skippedData as $skip) {
                echo "<tr>
                        <td>{$skip['row']}</td>
                        <td>{$skip['overdrive_id']}</td>
                        <td>{$skip['catalogue_id']}</td>
                        <td>{$skip['isbn']}</td>
                        <td>" . htmlspecialchars($skip['title']) . "</td>
                        <td>" . htmlspecialchars($skip['creators']) . "</td>
                        <td><strong style='color:red;'>{$skip['reason']}</strong></td>
                      </tr>";
            }
            echo "</tbody></table>";
        }

    } catch (Exception $e) {
        log_message('error', 'Audiobooks Upload Error: ' . $e->getMessage());
        echo "<h3 style='color:red;'>Error: {$e->getMessage()}</h3>";
    }
}




}
