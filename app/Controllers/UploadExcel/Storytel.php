<?php

namespace App\Controllers\UploadExcel;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Exception;
use DateTime;

class Storytel extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function StorytelUpload()
    {
        ini_set('max_execution_time', 600); // 10 minutes
        ini_set('memory_limit', '1024M');
        $db = db_connect();
        $file_name = "storytel-jan23.xlsx";
        $inputFileName = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'ExcelUpload' . DIRECTORY_SEPARATOR. 'storytelReport' . DIRECTORY_SEPARATOR . $file_name;

        try {
            // Load Excel file
            $spreadsheet = IOFactory::load($inputFileName);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestDataRow();

            $insertedCount = 0;
            $skippedData = [];

            // Loop rows (skip header row)
            for ($row = 2; $row <= $highestRow; $row++) {
                $isbn = trim((string)$sheet->getCell('A' . $row)->getValue());
                $title = trim((string)$sheet->getCell('B' . $row)->getValue());
                $author_name = trim((string)$sheet->getCell('C' . $row)->getValue());
                $pub_date = trim((string)$sheet->getCell('D' . $row)->getValue());
                $type_of_book = trim((string)$sheet->getCell('H' . $row)->getValue());
                $category = trim((string)$sheet->getCell('I' . $row)->getValue());

                // Validate required fields
                if (empty($isbn) || empty($title)) {
                    $skippedData[] = [
                        'row' => $row,
                        'title' => $title,
                        'reason' => 'Missing ISBN or Title'
                    ];
                    continue;
                }

                // Convert date format if valid
                $publication_date = null;
                $pub_date_obj = DateTime::createFromFormat('Y-m-d', $pub_date);
                if ($pub_date_obj !== false) {
                    $publication_date = $pub_date_obj->format('Y-m-d');
                }

                // Check if ISBN already exists
                $existing = $db->table('storytel_books')
                    ->where('isbn', $isbn)
                    ->get()
                    ->getRowArray();

                if ($existing) {
                    $skippedData[] = [
                        'row' => $row,
                        'title' => $title,
                        'isbn' => $isbn,
                        'reason' => 'Already Exists in storytel_books'
                    ];
                    continue;
                }

                // Book type mapping
                $type = null;
                if ($type_of_book === "Ebook") {
                    $type = 1;
                } elseif ($type_of_book === "Audiobook") {
                    $type = 3;
                }

                // Find matching book details
                $title_like = $db->escapeLikeString($title);
                $sql = "
                    SELECT 
                        book_tbl.book_id,
                        book_tbl.book_title,
                        author_tbl.author_name,
                        book_tbl.author_name AS author_id,
                        book_tbl.language,
                        book_tbl.genre_id,
                        book_tbl.type_of_book,
                        book_tbl.copyright_owner
                    FROM book_tbl
                    JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id
                    WHERE book_tbl.book_title LIKE '%{$title_like}%' ESCAPE '!'
                ";

                $book = $db->query($sql)->getRowArray();

                if (!$book) {
                    $skippedData[] = [
                        'row' => $row,
                        'title' => $title,
                        'reason' => 'Book not found in book_tbl'
                    ];
                    continue;
                }

                // Prepare insert data
                $insert_data = [
                    'isbn' => $isbn,
                    'title' => $title,
                    'author_name' => $book['author_name'],
                    'category' => $category,
                    'publication_date' => $publication_date,
                    'book_id' => $book['book_id'],
                    'author_id' => $book['author_id'],
                    'copyright_owner' => $book['copyright_owner'],
                    'language_id' => $book['language'],
                    'genre_id' => $book['genre_id'],
                    'type_of_book' => $type
                ];

                // Insert into DB
                $db->table('storytel_books')->insert($insert_data);
                $insertedCount++;
            }

            // Output results
            echo "<h2>📘 Storytel Upload Report</h2>";
            echo "<p><strong>Inserted Records:</strong> {$insertedCount}</p>";
            echo "<p><strong>Skipped Records:</strong> " . count($skippedData) . "</p>";

            // Display skipped records
            if (!empty($skippedData)) {
                echo "<h3>⛔ Skipped Records</h3>";
                echo "<table border='1' cellspacing='0' cellpadding='5'>
                        <thead>
                            <tr>
                                <th>Row</th>
                                <th>Title</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>";
                foreach ($skippedData as $skip) {
                    echo "<tr>
                            <td>{$skip['row']}</td>
                            <td>" . htmlspecialchars($skip['title']) . "</td>
                            <td>{$skip['reason']}</td>
                          </tr>";
                }
                echo "</tbody></table>";
            }

        } catch (\Exception $e) {
            log_message('error', 'Storytel Upload Error: ' . $e->getMessage());
            echo "<h3 style='color:red;'>Error: {$e->getMessage()}</h3>";
        }
    }

}
