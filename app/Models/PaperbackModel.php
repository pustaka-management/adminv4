<?php

namespace App\Models;

use CodeIgniter\Model;

class PaperbackModel extends Model
{
    public function getDashboardData()
    {
        // Step 1: Get paperback book status counts
        $builder = $this->db->table('book_tbl');
        $builder->select('paper_back_readiness_flag, paper_back_flag, COUNT(*) AS cnt');
        $builder->where('paper_back_flag', 1);
        $builder->groupBy('paper_back_readiness_flag');

        $result1 = $builder->get()->getResultArray();

        // Initialize paperback data
        $paperback = [
            'channel_name'   => 'paperback',
            'books_count'    => 0,
            'pending_books'  => 0,
            'active_books'   => 0,
            'rework_books'   => 0,
        ];
        $pustaka = array('books_count'=>0);
        foreach ($result1 as $row) {
    $paperback['books_count'] += $row['cnt'];

    if ($row['paper_back_readiness_flag'] == 0) {
        $paperback['pending_books'] += $row['cnt'];
    } elseif ($row['paper_back_readiness_flag'] == 1) {
        $paperback['active_books'] += $row['cnt'];
    } elseif ($row['paper_back_readiness_flag'] == 3) {
        $paperback['rework_books'] += $row['cnt'];
    }
}

        $result = [
            'paperback' => $paperback
        ];

        $paperback['channel_name'] = "Paperback";
        $result['paperback'] = $paperback;
        $sql1 = "
            SELECT 'Pustaka' AS channel_name, COUNT(DISTINCT book_id) AS books_count
            FROM book_tbl
            WHERE paper_back_readiness_flag = 1 and paper_back_flag=1
            UNION ALL
            SELECT 'Amazon' AS channel_name, COUNT(DISTINCT book_id) AS books_count
            FROM amazon_paperback_books
            UNION ALL
            SELECT 'Flipkart' AS channel_name, COUNT(DISTINCT seller_sku_id) AS books_count
            FROM flipkart_paperback_books
        ";

        $query1 = $this->db->query($sql1);
        foreach ($query1->getResultArray() as $row) {
            $res = [
                'channel_name' => $row['channel_name'],
                'books_count'  => $row['books_count']
            ];
            $result[$row['channel_name']] = $res;
        }

        return $result;
    }
    public function getPaperbackCurrMonthData(): array
{
    $firstDate = date('Y-m-01');
    $lastDate  = date('Y-m-t');

    // Author counts
    $authors = $this->db->query("
        SELECT 
            author_tbl.author_id,
            author_tbl.author_name,
            COUNT(*) AS auth_book_cnt
        FROM book_tbl
        JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id
        WHERE book_tbl.paper_back_readiness_flag=1
          AND book_tbl.paperback_activate_at BETWEEN '$firstDate' AND '$lastDate'
        GROUP BY book_tbl.author_name
        ORDER BY auth_book_cnt DESC
    ")->getResultArray();

    // Books details
    $books = $this->db->query("
        SELECT 
            book_tbl.book_id,
            book_tbl.book_title,
            book_tbl.language,
            book_tbl.paper_back_inr,
            book_tbl.paperback_activate_at,
            book_tbl.url_name,
            author_tbl.author_id,
            author_tbl.author_name
        FROM book_tbl
        JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id
        WHERE book_tbl.paper_back_readiness_flag=1
          AND book_tbl.paperback_activate_at BETWEEN '$firstDate' AND '$lastDate'
        ORDER BY book_tbl.paperback_activate_at DESC
    ")->getResultArray();

    // Summary (total books + total pages)
    $summary = $this->db->query("
        SELECT 
            COUNT(*) AS total_books,
            COALESCE(SUM(book_tbl.paper_back_inr),0) AS total_pages
        FROM book_tbl
        WHERE paper_back_readiness_flag=1
          AND paperback_activate_at BETWEEN '$firstDate' AND '$lastDate'
    ")->getRowArray();

    return ['authors' => $authors, 'books' => $books, 'summary' => $summary];
}

// Previous month
public function getPaperbackPrevMonthData(): array
{
    $firstDate = date('Y-m-01', strtotime('-1 month'));
    $lastDate  = date('Y-m-t', strtotime('-1 month'));

    // Author counts
    $authors = $this->db->query("
        SELECT 
            author_tbl.author_id,
            author_tbl.author_name,
            COUNT(*) AS auth_book_cnt
        FROM book_tbl
        JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id
        WHERE book_tbl.paper_back_readiness_flag=1
          AND book_tbl.paperback_activate_at BETWEEN '$firstDate' AND '$lastDate'
        GROUP BY book_tbl.author_name
        ORDER BY auth_book_cnt DESC
    ")->getResultArray();

    // Books details
    $books = $this->db->query("
        SELECT 
            book_tbl.book_id,
            book_tbl.book_title,
            book_tbl.language,
            book_tbl.paper_back_inr,
            book_tbl.paperback_activate_at,
            book_tbl.url_name,
            author_tbl.author_id,
            author_tbl.author_name
        FROM book_tbl
        JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id
        WHERE book_tbl.paper_back_readiness_flag=1
          AND book_tbl.paperback_activate_at BETWEEN '$firstDate' AND '$lastDate'
        ORDER BY book_tbl.paperback_activate_at DESC
    ")->getResultArray();

    // Summary (total books + total pages)
    $summary = $this->db->query("
        SELECT 
            COUNT(*) AS total_books,
            COALESCE(SUM(book_tbl.paper_back_inr),0) AS total_pages
        FROM book_tbl
        WHERE paper_back_readiness_flag=1
          AND paperback_activate_at BETWEEN '$firstDate' AND '$lastDate'
    ")->getRowArray();

    return ['authors' => $authors, 'books' => $books, 'summary' => $summary];
}
    public function getPaperbackDetails($status = null)
{
    $sql = "SELECT 
                book_tbl.book_id, 
                book_tbl.book_title, 
                author_tbl.author_name, 
                book_tbl.status, 
                book_tbl.paper_back_readiness_flag,
                book_tbl.created_at
            FROM book_tbl
            JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id
            WHERE book_tbl.paper_back_flag = 1";

    if ((int)$status === 0) {
        $sql .= " AND (book_tbl.paper_back_readiness_flag = 0 OR book_tbl.paper_back_readiness_flag IS NULL)";
        $query = $this->db->query($sql);
    } elseif ($status !== null) {
        $sql .= " AND book_tbl.paper_back_readiness_flag = ?";
        $query = $this->db->query($sql, [$status]);
    } else {
        $query = $this->db->query($sql);
    }

    return $query->getResultArray();
}
public function paperbackIndesignProcessing()
    {
        $builder = $this->db->table('indesign_processing');
        $builder->select('indesign_processing.*, book_tbl.book_title, author_tbl.author_name');
        $builder->join('book_tbl', 'indesign_processing.book_id = book_tbl.book_id');
        $builder->join('author_tbl', 'author_tbl.author_id = book_tbl.author_name');
        $builder->where('indesign_processing.start_flag', 0);
        $builder->orderBy('indesign_processing.created_date', 'DESC');
        $data['not_started'] = $builder->get()->getResultArray();

        $builder = $this->db->table('indesign_processing'); // Reinitialize builder
        $builder->select('indesign_processing.*, book_tbl.book_title, author_tbl.author_name');
        $builder->join('book_tbl', 'indesign_processing.book_id = book_tbl.book_id');
        $builder->join('author_tbl', 'author_tbl.author_id = book_tbl.author_name');
        $builder->where('indesign_processing.start_flag', 1);
        $builder->where('indesign_processing.completed_flag', 0);
        $builder->orderBy('indesign_processing.created_date', 'DESC');
        $data['in_progress'] = $builder->get()->getResultArray();

        return $data;
    }
public function indesignProcessingCount()
{
    $db = \Config\Database::connect();

    $data = [];

    // Not started
    $query = $db->query("SELECT COUNT(*) AS cnt FROM indesign_processing WHERE start_flag = 0");
    $data['not_start_cnt'] = $query->getRow()->cnt;

    // In progress
    $query = $db->query("SELECT COUNT(*) AS cnt FROM indesign_processing WHERE start_flag = 1 AND completed_flag = 0");
    $data['Processing'] = $query->getRow()->cnt;

    // Level 3 pending
    $query = $db->query("SELECT COUNT(*) AS cnt FROM indesign_processing WHERE start_flag = 1 AND level3_flag = 0");
    $data['level3_cnt'] = $query->getRow()->cnt;

    // InDesign pending
    $query = $db->query("SELECT COUNT(*) AS cnt FROM indesign_processing WHERE start_flag = 1 AND level3_flag = 1 AND indesign_flag = 0");
    $data['indesign_flag_cnt'] = $query->getRow()->cnt;

    // InDesign QC pending
    $query = $db->query("SELECT COUNT(*) AS cnt FROM indesign_processing WHERE start_flag = 1 AND indesign_flag = 1 AND indesign_qc_flag = 0");
    $data['indesign_qc_flag_cnt'] = $query->getRow()->cnt;

    // Re-QC pending
    $query = $db->query("SELECT COUNT(*) AS cnt FROM indesign_processing WHERE start_flag = 1 AND indesign_qc_flag = 1 AND re_qc_flag = 0");
    $data['re_qc_flag_cnt'] = $query->getRow()->cnt;

    // Cover pending
    $query = $db->query("SELECT COUNT(*) AS cnt FROM indesign_processing WHERE start_flag = 1 AND completed_flag = 0 AND indesign_cover_flag = 0");
    $data['indesign_cover_flagcnt'] = $query->getRow()->cnt;

    // ISBN ready pending
    $query = $db->query("SELECT COUNT(*) AS cnt FROM indesign_processing WHERE start_flag = 1 AND isbn_ready_flag = 0");
    $data['isbn_ready_cnt'] = $query->getRow()->cnt;

    // Final QC pending
    $query = $db->query("SELECT COUNT(*) AS cnt FROM indesign_processing WHERE start_flag = 1 AND indesign_cover_flag = 1 AND re_qc_flag = 1 AND isbn_ready_flag = 1  AND final_qc_flag = 0");
    $data['final_qc_flagcnt'] = $query->getRow()->cnt;

    // File upload pending
    $query = $db->query("SELECT COUNT(*) AS cnt FROM indesign_processing WHERE start_flag = 1 AND final_qc_flag = 1 AND file_upload_flag = 0");
    $data['file_upload_flagcnt'] = $query->getRow()->cnt;

    return $data;
}

public function getLanguageWiseBookCount()
{
    return $this->db->table('book_tbl b')
        ->select('l.language_name, COUNT(b.book_id) as total_books', false)
        ->join('language_tbl l', 'l.language_id = b.language')
        ->where(['b.paper_back_readiness_flag' => 1])
        ->groupBy('l.language_name')
        ->get()
        ->getResultArray();
}
    public function getGenreWiseBookCount()
    {
        return $this->db->table('book_tbl b')
        ->select('g.genre_name, COUNT(b.book_id) as total_books')
        ->join('genre_details_tbl g', 'g.genre_id = b.genre_id')
        ->where(['b.paper_back_readiness_flag' => 1])  // type_of_book = 1 filter added
        ->groupBy('g.genre_name')
        ->get()
        ->getResultArray();
    }

    public function getBookCategoryCount()
    {
        return $this->db->table('book_tbl b')
        ->select('b.book_category, COUNT(b.book_id) as total_books')
        ->where('b.status', 1)
        ->where('b.paper_back_readiness_flag', 1)   // type_of_book filter
        ->groupBy('b.book_category')
        ->get()
        ->getResultArray();
    }

   public function getAuthorWiseBookCount()
{
    return $this->db->table('book_tbl b')
        ->select('a.author_name, COUNT(b.book_id) as total')
        ->join('author_tbl a', 'a.author_id = b.author_name', 'left')
        ->where(['b.paper_back_readiness_flag' => 1])
        ->groupBy('a.author_id')
        ->orderBy('total', 'DESC') // Use the alias instead of COUNT()
        ->get()
        ->getResultArray();
}
    
  public function getPaperbackBooksData()
{
    $result = [];

    // Main paperback books data
    $query = $this->db->query("
        SELECT l.language_name, COUNT(b.book_id) as cnt 
        FROM book_tbl b
        JOIN language_tbl l ON b.language = l.language_id
        WHERE b.paper_back_readiness_flag = 1 
          AND b.status = 1 
        GROUP BY b.language, l.language_name
    ")->getResult();

    // Map results by language_name
    foreach ($query as $row) {
        $result["pus_{$row->language_name}_cnt"] = $row->cnt;
    }

    // --- AMAZON PAPERBACK ---
    $amazonQuery = $this->db->query("
        SELECT l.language_id, l.language_name,
               COUNT(ab.book_id) AS published,
               COUNT(b.book_id) - COUNT(ab.book_id) AS unpublished
        FROM language_tbl l
        LEFT JOIN book_tbl b 
            ON b.language = l.language_id 
           AND b.status = 1 
           AND b.paper_back_readiness_flag = 1
           AND b.paper_back_inr >= 150
        LEFT JOIN amazon_paperback_books ab 
            ON ab.book_id = b.book_id
        GROUP BY l.language_id, l.language_name
        ORDER BY l.language_id
    ")->getResult();

    foreach ($amazonQuery as $row) {
        $result[$row->language_name]['amazon']['published']   = (int)$row->published;
        $result[$row->language_name]['amazon']['unpublished'] = (int)$row->unpublished;
    }

    // --- FLIPKART PAPERBACK ---
    $flipkartQuery = $this->db->query("
        SELECT l.language_name,
               COUNT(fb.seller_sku_id) AS published,
               COUNT(b.book_id) - COUNT(fb.seller_sku_id) AS unpublished
        FROM language_tbl l
        LEFT JOIN book_tbl b 
            ON b.language = l.language_id 
           AND b.status = 1 
           AND b.paper_back_readiness_flag = 1
        LEFT JOIN flipkart_paperback_books fb 
            ON fb.seller_sku_id = b.book_id
        GROUP BY l.language_id, l.language_name
    ")->getResult();

    foreach ($flipkartQuery as $row) {
        $result[$row->language_name]['flipkart']['published']   = (int)$row->published;
        $result[$row->language_name]['flipkart']['unpublished'] = (int)$row->unpublished;
    }

    return $result;
}


        public function getPaperbackBooksList()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('book_tbl');
        $builder->select([
            'book_tbl.book_id',
            'book_tbl.book_title',
            'book_tbl.regional_book_title',
            'book_tbl.paper_back_pages as number_of_page',
            'author_tbl.author_name',
            'author_tbl.author_id'
        ]);
        $builder->join('author_tbl', 'author_tbl.author_id = book_tbl.author_name', 'inner');
        $builder->where('book_tbl.paper_back_flag', 1);
        $builder->groupStart()
                ->where('book_tbl.paper_back_readiness_flag', 0)
                ->orWhere('book_tbl.paper_back_readiness_flag IS NULL', null, false)
                ->groupEnd();
        $builder->whereNotIn('book_tbl.book_id', function ($subQuery) {
            return $subQuery->select('book_id')->from('indesign_processing');
        });

        $query = $builder->get();
        return $query->getResultArray();
    }

    public function selectedBookList($selected_book_list)
    {
        // Ensure the list is always an array
        if (!is_array($selected_book_list)) {
            $selected_book_list = explode(',', $selected_book_list);
        }

        $builder = $this->db->table('book_tbl');
        $builder->select('
            book_tbl.book_id,
            book_tbl.book_title,
            book_tbl.regional_book_title,
            book_tbl.paper_back_pages as number_of_page,
            author_tbl.author_name,
            author_tbl.author_id
        ');
        $builder->join('author_tbl', 'author_tbl.author_id = book_tbl.author_name');
        $builder->where('book_tbl.paper_back_flag', 1);
        $builder->whereIn('book_tbl.book_id', $selected_book_list);

        $query = $builder->get();
        return $query->getResultArray();
    }
    public function bookListSubmit($num_of_books, $selected_book_list, $postData)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('indesign_processing');

        $book_ids   = [];
        $author_ids = [];

        for ($i = 1; $i <= $num_of_books; $i++) {
            $tmpBook   = 'book_id' . $i;
            $tmpAuthor = 'author_id' . $i;

            $book_ids[$i]   = $postData[$tmpBook] ?? null;
            $author_ids[$i] = $postData[$tmpAuthor] ?? null;
        }

        for ($i = 1; $i <= $num_of_books; $i++) {
            if (empty($book_ids[$i]) || empty($author_ids[$i])) {
                continue;
            }

            $existing = $builder->where('book_id', $book_ids[$i])->get()->getRowArray();

            if (!$existing) {
                $data = [
                    'book_id'      => $book_ids[$i],
                    'author_id'    => $author_ids[$i],
                    'created_date' => date('Y-m-d H:i:s'),
                ];
                $builder->insert($data);
            } else {
                return "Error: Book ID " . $book_ids[$i] . " has already been submitted.";
            }
        }

        return ($db->affectedRows() > 0) ? 1 : 0;
    }
    public function indesignMarkStart($book_id)
    {
        $db = \Config\Database::connect();
        // Use table name directly here
        $builder = $db->table('indesign_processing');

        $builder->set('start_flag', 1)
                ->where('book_id', $book_id)
                ->update();

        return ($db->affectedRows() > 0) ? 1 : 0;
    }
    public function markLevel3Completed($book_id)
    {
    $db = \Config\Database::connect();
    $builder = $db->table('indesign_processing');

    $builder->set('level3_flag', 1)
            ->where('book_id', $book_id)
            ->update();

    return ($db->affectedRows() > 0) ? 1 : 0;
    }
    function markIndesignCompleted($book_id){

        $db = \Config\Database::connect();
        $builder = $db->table('indesign_processing');

        $builder->set('indesign_flag', 1)
                ->where('book_id', $book_id)
                ->update();

        return ($db->affectedRows() > 0) ? 1 : 0;
    }
    public function markIndesignQcCompleted($book_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('indesign_processing');

        $builder->set('indesign_qc_flag', 1)
                ->where('book_id', $book_id)
                ->update();

        return ($db->affectedRows() > 0) ? 1 : 0;
    }
    public function markreqccompleted($book_id){

        $db = \Config\Database::connect();
        $builder = $db->table('indesign_processing');

        $builder->set('re_qc_flag', 1)
                ->where('book_id', $book_id)
                ->update();

        return ($db->affectedRows() > 0) ? 1 : 0;
    }
    function markindesigncovercompleted($book_id){

        $db = \Config\Database::connect();
        $builder = $db->table('indesign_processing');

        $builder->set('indesign_cover_flag', 1)
                ->where('book_id', $book_id)
                ->update();

        return ($db->affectedRows() > 0) ? 1 : 0;
    }
    function markisbnreadycompleted($book_id){

        $db = \Config\Database::connect();
        $builder = $db->table('indesign_processing');

        $builder->set('isbn_ready_flag', 1)
                ->where('book_id', $book_id)
                ->update();

        return ($db->affectedRows() > 0) ? 1 : 0;
    }

    function markfinalqccompleted($book_id){

        $db = \Config\Database::connect();
        $builder = $db->table('indesign_processing');

        $builder->set('final_qc_flag', 1)
                ->where('book_id', $book_id)
                ->update();

        return ($db->affectedRows() > 0) ? 1 : 0;
    }

    function markfileuploadcompleted($book_id){

         $db = \Config\Database::connect();
        $builder = $db->table('indesign_processing');

        $builder->set('file_upload_flag', 1)
                ->where('book_id', $book_id)
                ->update();

        return ($db->affectedRows() > 0) ? 1 : 0;
    }
    public function completedBooksSubmit($book_id)
{
    $db = \Config\Database::connect();

    return $db->table('book_tbl')
        ->where('book_id',$book_id)
        ->get()
        ->getRowArray();
}

public function indesignMarkCompleted(
        $book_id,$pages,$price,$royalty,$copyright_owner,$isbn,
        $paper_back_desc,$paper_back_author_desc,
        $rate_per_page,$rate_remarks)
{
    $db = \Config\Database::connect();

    // Update indesign_processing
    $db->table('indesign_processing')
        ->where('book_id',$book_id)
        ->update([
            'completed_flag'=>1,
            'completed_date'=>date('Y-m-d H:i:s')
        ]);

    // Get old remarks
    $old = $db->table('book_tbl')
              ->select('rate_remarks')
              ->where('book_id',$book_id)
              ->get()
              ->getRowArray();

    $oldRemarks = $old['rate_remarks'] ?? '';

    // New remark with date
    $newRemark = date('d-m-Y').' : '.$rate_remarks;

    // Append old + new
    $finalRemarks = $oldRemarks
        ? $oldRemarks."\n".$newRemark
        : $newRemark;

    // Update book_tbl
    $db->table('book_tbl')
        ->where('book_id',$book_id)
        ->update([
            'paper_back_readiness_flag'=>1,
            'paper_back_pages'=>$pages,
            'paper_back_inr'=>$price,
            'paper_back_royalty'=>$royalty,
            'paper_back_copyright_owner'=>$copyright_owner,
            'paper_back_isbn'=>$isbn,
            'paper_back_desc'=>$paper_back_desc,
            'paper_back_author_desc'=>$paper_back_author_desc,
            'rate_per_page'=>$rate_per_page,
            'rate_remarks'=>$finalRemarks,
            'paperback_activate_at'=>date('Y-m-d H:i:s')
        ]);

    return ($db->affectedRows()>0)?1:0;
}
     public function paperbackReworkBook()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('book_tbl');

        $builder->select('
                book_tbl.book_id,
                book_tbl.book_title,
                book_tbl.regional_book_title,
                book_tbl.paper_back_pages as number_of_page,
                author_tbl.author_name,
                author_tbl.author_id
            ')
            ->join('author_tbl', 'author_tbl.author_id = book_tbl.author_name')
            ->where('book_tbl.paper_back_flag', 1)
            ->where('book_tbl.paper_back_readiness_flag', 1);

        $query = $builder->get();
        return $query->getResultArray();
    }
     public function paperbackReworkProcessing()
    {
        // 🔹 Not Started Books
        $sql1 = "SELECT 
                    indesign_processing.*,
                    book_tbl.book_title,
                    author_tbl.author_name
                 FROM 
                    indesign_processing
                 JOIN 
                    book_tbl ON indesign_processing.book_id = book_tbl.book_id
                 JOIN 
                    author_tbl ON author_tbl.author_id = book_tbl.author_name
                 WHERE 
                    indesign_processing.rework_start_flag = 0 
                    AND indesign_processing.rework_flag = 1";

        $query1 = $this->db->query($sql1);
        $data['not_started'] = $query1->getResultArray();

        // 🔹 In Progress Books
        $sql2 = "SELECT 
                    indesign_processing.*,
                    book_tbl.book_title,
                    author_tbl.author_name
                 FROM 
                    indesign_processing
                 JOIN 
                    book_tbl ON indesign_processing.book_id = book_tbl.book_id
                 JOIN 
                    author_tbl ON author_tbl.author_id = book_tbl.author_name
                 WHERE 
                    indesign_processing.rework_start_flag = 1 
                    AND indesign_processing.rework_flag = 1
                    AND indesign_processing.re_completed_flag = 0";

        $query2 = $this->db->query($sql2);
        $data['in_progress'] = $query2->getResultArray();

        return $data;
    }

    public function reworkProcessingCount()
    {
        $data = [];

        // 🔹 Not Started
        $query = $this->db->query(
            "SELECT COUNT(*) as cnt 
             FROM indesign_processing 
             WHERE rework_start_flag = 0 
             AND rework_flag = 1"
        );
        $data['not_start_cnt'] = $query->getRowArray();

        // 🔹 In Processing
        $query = $this->db->query(
            "SELECT COUNT(*) as cnt 
             FROM indesign_processing 
             WHERE rework_start_flag = 1 
             AND rework_flag = 1 
             AND re_completed_flag = 0"
        );
        $data['Processing'] = $query->getRowArray();

        // 🔹 Re-Proofing
        $query = $this->db->query(
            "SELECT COUNT(*) as cnt 
             FROM indesign_processing 
             WHERE rework_flag = 1 
             AND rework_start_flag = 1 
             AND re_proofing_flag = 0"
        );
        $data['re_proofing_cnt'] = $query->getRowArray();

        // 🔹 Re-Indesign
        $query = $this->db->query(
            "SELECT COUNT(*) as cnt 
             FROM indesign_processing 
             WHERE rework_flag = 1 
             AND re_proofing_flag = 1 
             AND re_indesign_flag = 0"
        );
        $data['re_indesign_cnt'] = $query->getRowArray();

        // 🔹 Re-File Upload
        $query = $this->db->query(
            "SELECT COUNT(*) as cnt 
             FROM indesign_processing 
             WHERE rework_flag = 1 
             AND re_indesign_flag = 1 
             AND re_fileupload_flag = 0"
        );
        $data['re_fileupload_cnt'] = $query->getRowArray();

        return $data;
    }
    public function reworkMarkStart()
{
    $book_id = $this->request->getPost('book_id');

    // Direct DB access using table name
    $db = \Config\Database::connect();
    $builder = $db->table('indesign_processing');

    $builder->where('book_id', $book_id);
    $builder->update(['rework_start_flag' => 1]);

    // Return 1 if affected, else 0
    return $this->response->setJSON($db->affectedRows() > 0 ? 1 : 0);
}

    public function markReProofingCompleted()
    {
        $book_id = $this->request->getPost('book_id');

        $result = $this->db->table('indesign_processing')
            ->where('book_id', $book_id)
            ->update(['re_proofing_flag' => 1]);

        return $this->db->affectedRows() > 0 ? 1 : 0;
    }

    public function markReIndesignCompleted()
    {
        $book_id = $this->request->getPost('book_id');

        $result = $this->db->table('indesign_processing')
            ->where('book_id', $book_id)
            ->update(['re_indesign_flag' => 1]);

        return $this->db->affectedRows() > 0 ? 1 : 0;
    }

    public function markReFileuploadCompleted()
    {
        $book_id = $this->request->getPost('book_id');

        $result = $this->db->table('indesign_processing')
            ->where('book_id', $book_id)
            ->update(['re_fileupload_flag' => 1]);

        return $this->db->affectedRows() > 0 ? 1 : 0;
    }
    public function getPaperbackSummary()
{
    $result = [];

    // Fetch all languages
    $languages = $this->db->table('language_tbl')->get()->getResult();

    // Initialize the result array
    foreach ($languages as $lang) {
        $result[$lang->language_name] = [
            'language_id'  => $lang->language_id,
            'published'    => 0,
            'unpublished'  => 0
        ];
    }

    // Combined published/unpublished counts using LEFT JOIN with price filter
    $query = $this->db->query("
        SELECT l.language_id, l.language_name,
               COUNT(ab.book_id) AS published,
               COUNT(b.book_id) - COUNT(ab.book_id) AS unpublished
        FROM language_tbl l
        LEFT JOIN book_tbl b 
            ON b.language = l.language_id 
           AND b.status = 1 
           AND b.paper_back_readiness_flag = 1
           AND b.paper_back_inr >= 150
        LEFT JOIN amazon_paperback_books ab 
            ON ab.book_id = b.book_id
        GROUP BY l.language_id, l.language_name
        ORDER BY l.language_id
    ")->getResult();

    // Map results into the initialized array
    foreach ($query as $row) {
        $result[$row->language_name]['published']   = (int)$row->published;
        $result[$row->language_name]['unpublished'] = (int)$row->unpublished;
    }

    return $result;
}


// Get unpublished books by language
public function getUnpublishedBooksByLanguage($langId)
{
    return $this->db->query("
        SELECT b.book_id, b.book_title, b.paperback_activate_at, b.paper_back_inr, a.author_name, b.epub_url, l.language_name
        FROM book_tbl b
        JOIN author_tbl a ON a.author_id = b.author_name
        JOIN language_tbl l ON b.language = l.language_id
        WHERE b.status = 1
          AND b.paper_back_readiness_flag = 1
          AND b.paper_back_inr >= 150
          AND b.language = ?
          AND b.book_id NOT IN (SELECT book_id FROM amazon_paperback_books)
        ORDER BY b.book_id ASC
    ", [$langId])->getResultArray();
}

// Get Flipkart paperback summary
public function getFlipkartPaperbackSummary()
{
    $result = [];

    // Fetch all languages
    $languages = $this->db->table('language_tbl')->get()->getResult();

    foreach ($languages as $lang) {
        $result[$lang->language_name] = [
            'language_id'  => $lang->language_id,
            'published'    => 0,
            'unpublished'  => 0
        ];
    }

    $query = $this->db->query("
        SELECT l.language_id, l.language_name,
               COUNT(fb.seller_sku_id) AS published,
               COUNT(b.book_id) - COUNT(fb.seller_sku_id) AS unpublished
        FROM language_tbl l
        LEFT JOIN book_tbl b 
            ON b.language = l.language_id 
           AND b.status = 1 
           AND b.paper_back_readiness_flag = 1
        LEFT JOIN flipkart_paperback_books fb 
            ON fb.seller_sku_id = b.book_id
        GROUP BY l.language_id, l.language_name
        ORDER BY l.language_id
    ")->getResult();

    foreach ($query as $row) {
        $result[$row->language_name]['published']   = (int)$row->published;
        $result[$row->language_name]['unpublished'] = (int)$row->unpublished;
    }

    return $result;
}


// Get unpublished Flipkart books by language
public function getFlipkartUnpublishedBooksByLanguage($langId)
{
    return $this->db->query("
        SELECT b.book_id, b.book_title, b.author_name, b.epub_url, l.language_name
        FROM book_tbl b
        JOIN language_tbl l ON b.language = l.language_id
        WHERE b.status = 1
          AND b.paper_back_readiness_flag = 1
          AND b.language = ?
          AND b.book_id NOT IN (SELECT seller_sku_id FROM flipkart_paperback_books)
        ORDER BY b.book_id ASC
    ", [$langId])->getResultArray();
}
    public function paperbackDetails()
    {
        $db = \Config\Database::connect();
        $result = [];


        /*----------------------------------------------------
         | 1. Monthly Paperback Pages Count
        ----------------------------------------------------*/
        $sql1 = "
            SELECT 
                DATE_FORMAT(activated_at, '%m-%y') AS monthly_number,
                SUM(paper_back_pages) AS cnt
            FROM book_tbl
            WHERE paper_back_flag = 1
              AND paper_back_readiness_flag = 1
            GROUP BY monthly_number
            ORDER BY activated_at ASC
        ";

        $query = $db->query($sql1);
        $result['pb_month'] = [];
        $result['pb_monthly_pages'] = [];

        foreach ($query->getResultArray() as $row) {
            $result['pb_month'][] = $row['monthly_number'];
            $result['pb_monthly_pages'][] = (int)$row['cnt'];
        }


        /*----------------------------------------------------
         | 2. Genre Wise Paperback Count
        ----------------------------------------------------*/
        $sql2 = "
            SELECT 
                g.genre_id,
                g.genre_name,
                COUNT(*) AS cnt,
                SUM(b.paper_back_pages) AS page_cnt
            FROM book_tbl b
            JOIN genre_details_tbl g ON g.genre_id = b.genre_id
            WHERE b.paper_back_flag = 1
              AND b.paper_back_readiness_flag = 1
            GROUP BY g.genre_id, g.genre_name
            ORDER BY cnt DESC
        ";

        $query = $db->query($sql2);
        $result['pb_genre_id'] = [];
        $result['pb_genre_name'] = [];
        $result['pb_genre_cnt'] = [];
        $result['pb_genre_page_cnt'] = [];

        foreach ($query->getResultArray() as $row) {
            $result['pb_genre_id'][] = $row['genre_id'];
            $result['pb_genre_name'][] = $row['genre_name'];
            $result['pb_genre_cnt'][] = (int)$row['cnt'];
            $result['pb_genre_page_cnt'][] = (int)$row['page_cnt'];
        }


        /*----------------------------------------------------
         | 3. Language Wise Paperback Count
        ----------------------------------------------------*/
        $sql3 = "
                    SELECT 
                        l.language_name,
                        COUNT(*) AS cnt
                    FROM book_tbl b
                    JOIN language_tbl l ON l.language_id = b.language
                    WHERE  b.paper_back_readiness_flag = 1
                    AND b.status = 1
                    GROUP BY l.language_id, l.language_name
                ";

                $query = $db->query($sql3);

                $result['pb_lang_name'] = [];
                $result['pb_lang_book_cnt'] = [];

                foreach ($query->getResultArray() as $row) {

                    // 1. Normal list arrays (for chart)
                    $result['pb_lang_name'][] = $row['language_name'];
                    $result['pb_lang_book_cnt'][] = (int)$row['cnt'];

                    // 2. Dynamic key format
                    $langKey = str_replace(' ', '_', $row['language_name']);
                    $result["pb_{$langKey}_cnt"] = (int)$row['cnt'];
                }



        /*----------------------------------------------------
         | 4. Top Authors (Paperback Books)
        ----------------------------------------------------*/
        $sql4 = "
            SELECT 
                a.author_name,
                COUNT(*) AS cnt
            FROM author_tbl a
            JOIN book_tbl b ON a.author_id = b.author_name
            WHERE b.paper_back_flag = 1
              AND b.paper_back_readiness_flag = 1
            GROUP BY a.author_id, a.author_name
            ORDER BY cnt DESC
            LIMIT 10
        ";

        $query = $db->query($sql4);
        $result['pb_author_name'] = [];
        $result['pb_author_cnt'] = [];

        foreach ($query->getResultArray() as $row) {
            $result['pb_author_name'][] = $row['author_name'];
            $result['pb_author_cnt'][] = (int)$row['cnt'];
        }

        return $result;
    }
    public function getBookfairSalesDetails()
    {
        $db = \Config\Database::connect(); 
        
        $sql ="SELECT 
                    pod_bookshop.bookshop_name,
                    bookfair_combo_pack.pack_name,
                    bookfair_sale_or_return_orders.order_id,
                    (LENGTH(bookfair_combo_pack.book_ids) 
                    - LENGTH(REPLACE(bookfair_combo_pack.book_ids, ',', '')) + 1) 
                        AS no_of_titles,
                    bookfair_combo_pack.default_value AS quantity_per_title,
                    (LENGTH(bookfair_combo_pack.book_ids) 
                    - LENGTH(REPLACE(bookfair_combo_pack.book_ids, ',', '')) + 1)
                        * bookfair_combo_pack.default_value AS total_quantity,
                    DATE(bookfair_sale_or_return_orders.sending_date) AS sending_date
                FROM 
                    pustaka.bookfair_sale_or_return_orders
                JOIN
                    bookfair_combo_pack ON bookfair_combo_pack.combo_id = bookfair_sale_or_return_orders.combo_id
                JOIN 
                    pod_bookshop ON pod_bookshop.bookshop_id = bookfair_sale_or_return_orders.bookshop_id";

        $query = $db->query($sql);
        $data['bookfair_list'] = $query->getResultArray();
        return $data;
    }
    public function getBookFairdetails($order_id)
    {
        $db = \Config\Database::connect(); 

        // Bookfair basic details
        $sql = "SELECT 
                    bookfair_sale_or_return_orders.book_fair_name,
                    bookfair_sale_or_return_orders.preferred_transport_name,
                    pod_bookshop.contact_person_name,
                    pod_bookshop.mobile,
                    pod_bookshop.address
                FROM 
                    bookfair_sale_or_return_orders
                JOIN 
                    pod_bookshop 
                    ON pod_bookshop.bookshop_id = bookfair_sale_or_return_orders.bookshop_id
                WHERE
                     bookfair_sale_or_return_orders.order_id = ?";

        $query = $db->query($sql, [$order_id]);
        $data['bookfair'] = $query->getResultArray();

        // Combo details
        $sql1 = "SELECT 
                    bookfair_combo_pack.pack_name,
                    bookfair_sale_or_return_orders.sending_date,
                    bookfair_sale_or_return_orders.preferred_transport_name,
                    bookfair_sale_or_return_orders.remark
                FROM 
                    bookfair_sale_or_return_orders
                JOIN
                    bookfair_combo_pack 
                    ON bookfair_combo_pack.combo_id = bookfair_sale_or_return_orders.combo_id
                WHERE 
                     bookfair_sale_or_return_orders.order_id = ?";

        $query1 = $db->query($sql1, [$order_id]);
        $data['bookfair_combo'] = $query1->getResultArray();

        $sql2="SELECT  
                    bookfair_sale_or_return_order_details.id,
                    bookfair_sale_or_return_orders.order_id,
                    bookfair_sale_or_return_order_details.book_id,
                    book_tbl.book_title,
                    author_tbl.author_name,
                    language_tbl.language_name,
                    bookfair_sale_or_return_order_details.send_qty,
                    bookfair_sale_or_return_order_details.book_price,
                    bookfair_sale_or_return_order_details.sending_date
                FROM
                    bookfair_sale_or_return_order_details
                JOIN
                    bookfair_sale_or_return_orders
                    ON bookfair_sale_or_return_orders.order_id = bookfair_sale_or_return_order_details.order_id
                JOIN
                    book_tbl
                    ON book_tbl.book_id = bookfair_sale_or_return_order_details.book_id
                JOIN
                    author_tbl
                    ON author_tbl.author_id = book_tbl.author_name
                JOIN
                    language_tbl
                    ON language_tbl.language_id = book_tbl.language
                WHERE
                    bookfair_sale_or_return_orders.order_id = ?";

        $query2 = $db->query($sql2, [$order_id]);
        $data['bookfair_details'] = $query2->getResultArray();

        return $data;
    }
    public function shipBookfairOrder($order_id)
    {
        $db = \Config\Database::connect();
        $db->transStart(); // 🔐 Transaction start

        $today = date('Y-m-d');

        // 1️⃣ Update order details table (ALL BOOKS)
        $db->table('bookfair_sale_or_return_order_details')
            ->where('order_id', $order_id)
            ->update([
                'status'       => 1,
                'sending_date' => $today
            ]);

        // 2️⃣ Update main order table
        $db->table('bookfair_sale_or_return_orders')
            ->where('order_id', $order_id)
            ->update([
                'status'       => 1,
                'sending_date' => $today
            ]);

        $db->transComplete(); // 🔐 Transaction end

        if ($db->transStatus() === false) {
            return false;
        }

        return true;
    }


}