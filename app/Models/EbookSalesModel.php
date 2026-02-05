<?php

namespace App\Models;

use CodeIgniter\Model;

class EbookSalesModel extends Model
{
    protected $DBGroup = 'default';

     // 1. Book + Author Counts
    public function getAmazonBookAndAuthorCounts()
    {
        return $this->db->table('amazon_books')
            ->select('COUNT(DISTINCT book_id) AS total_books, COUNT(DISTINCT author_id) AS total_authors')
            ->get()->getRowArray();
    }

    // 2. Orders Count & Status
    public function getAmazonOrderCounts()
    {
        return $this->db->table('amazon_transactions')
            ->select("
                COUNT(*) AS total_orders,
                SUM(CASE WHEN LOWER(status)='p' THEN 1 ELSE 0 END) AS paid_orders,
                SUM(CASE WHEN LOWER(status)='o' THEN 1 ELSE 0 END) AS pending_orders
            ")
            ->get()->getRowArray();
    }

    // 3. Amounts Paid/Pending
    public function getAmazonAmountSummary()
    {
        return $this->db->table('amazon_transactions')
            ->select("
                SUM(inr_value) AS total_amount,
                SUM(CASE WHEN LOWER(status)='p' THEN inr_value ELSE 0 END) AS paid_amount,
                SUM(CASE WHEN LOWER(status)='o' THEN inr_value ELSE 0 END) AS pending_amount
            ")
            ->get()->getRowArray();
    }

   public function getAmazonTopBooks()
{
    return $this->db->table('amazon_transactions')
        ->select('
            book_id,
            MAX(title) AS title,
            MAX(author) AS author,
            COUNT(*) AS total_units
        ')
        ->where('book_id >', 0)
        ->groupBy('book_id')
        ->orderBy('total_units', 'DESC')
        ->limit(10)
        ->get()
        ->getResultArray();
}

    // 5. Year Wise Sales
    public function getAmazonSalesByYear()
    {
        return $this->db->table('amazon_transactions')
            ->select("YEAR(original_invoice_date) AS year, SUM(inr_value) AS total")
            ->where('original_invoice_date IS NOT NULL')
            ->groupBy('YEAR(original_invoice_date)')
            ->orderBy('year','ASC')
            ->get()->getResultArray();
    }

   public function getAmazonSalesByLanguage()
{
    return $this->db->table('amazon_transactions t')
        ->select('l.language_name, SUM(t.inr_value) AS total')
        ->join('language_tbl l','l.language_id = t.language_id','left')
        ->groupBy('l.language_name')
        ->orderBy('total','DESC')
        ->get()->getResultArray();
}

   public function getAmazonSalesByGenre()
    {
        // pre-aggregate transactions per book to reduce join load
        $subQuery = $this->db->table('amazon_transactions')
            ->select('book_id, SUM(inr_value) AS total_value')
            ->groupBy('book_id')
            ->getCompiledSelect();

        return $this->db->table("($subQuery) t")
            ->select('g.genre_name, SUM(t.total_value) AS total')
            ->join('book_tbl b','b.book_id = t.book_id','inner')
            ->join('genre_details_tbl g','g.genre_id = b.genre_id','left')
            ->groupBy('g.genre_name')
            ->orderBy('total','DESC')
            ->get()
            ->getResultArray();
    }
    // 1️⃣ Amazon books list
    public function getAmazonBooks()
    {
        return $this->db->table('amazon_books ab')
            ->select('ab.book_id, ab.title, ab.author')
            ->orderBy('ab.title')
            ->get()
            ->getResultArray();
    }

    // 2️⃣ Book master details
   public function getAmazonBookDetails($book_id)
{
    return $this->db->table('book_tbl b')
        ->select('
            b.book_id,
            at.title,
            at.author,
            at.status,
            b.regional_book_title AS regional_title,
            b.isbn_number AS isbn,
            b.number_of_page AS pages,
            b.cost
        ')
        ->join('amazon_transactions at', 'at.book_id = b.book_id', 'left')
        ->where('b.book_id', $book_id)
        ->get()
        ->getRowArray();
}
    // 3️⃣ Amazon transactions
    public function getAmazonTransactions($book_id)
    {
        return $this->db->table('amazon_transactions')
            ->select('
                original_invoice_date,
                asin,
                inr_value,
                final_royalty_value,
                status
            ')
            ->where('book_id', $book_id)
            ->orderBy('original_invoice_date', 'DESC')
            ->get()
            ->getResultArray();
    }
    public function getAmazonAuthors()
    {
        return $this->db->table('amazon_books')
            ->select('author, COUNT(DISTINCT book_id) AS title_count')
            ->groupBy('author')
            ->orderBy('author', 'ASC')
            ->get()
            ->getResultArray();
    }
    public function getAmazonBooksByAuthor($author)
    {
        return $this->db->table('amazon_books')
            ->select('book_id, title')
            ->where('author', $author)
            ->orderBy('title', 'ASC')
            ->get()
            ->getResultArray();
    }
    public function getOverdriveEbookSummary()
    {
        $db = \Config\Database::connect();

        //  Total Titles
        $totalTitles = $db->query("
            SELECT COUNT(DISTINCT b.book_id) AS cnt
            FROM overdrive_books ob
            JOIN book_tbl b ON b.book_id = ob.book_id
            WHERE ob.type_of_book = 1
        ")->getRow()->cnt ?? 0;


       $totalCreators = $db->query("
            SELECT COUNT(DISTINCT ob.author_id) AS cnt
            FROM overdrive_books ob
            JOIN author_tbl a ON a.author_id = ob.author_id
            WHERE ob.type_of_book = 1
        ")->getRow()->cnt ?? 0;

        //  Total Sales 
        $sales = $db->query("
            SELECT
                SUM(inr_value) AS total_value,
                SUM(CASE WHEN status = 'p' THEN inr_value ELSE 0 END) AS paid_value,
                SUM(CASE WHEN status = 'o' THEN inr_value ELSE 0 END) AS outstanding_value
            FROM overdrive_transactions
            WHERE type_of_book = 1
              AND status IN ('p','o')
        ")->getRowArray();

        // Total orders 
        $royalty = $db->query("
            SELECT
                COUNT(*) AS total_orders,
                COUNT(CASE WHEN status = 'p' THEN overdrive_id END) AS paid_orders,
                COUNT(CASE WHEN status = 'o' THEN overdrive_id END) AS pending_orders
            FROM overdrive_transactions
            WHERE type_of_book = 1
            AND status IN ('p','o')
        ")->getRowArray();

        return [
            'total_titles'    => (int)$totalTitles,
            'total_creators'  => (int)$totalCreators,
            'sales_total'       => (float)($sales['total_value'] ?? 0),
            'sales_paid'        => (float)($sales['paid_value'] ?? 0),
            'sales_outstanding' => (float)($sales['outstanding_value'] ?? 0),
            'total_orders'   => (float)($royalty['total_orders'] ?? 0),
            'paid_orders'    => (float)($royalty['paid_orders'] ?? 0),
            'pending_orders' => (float)($royalty['pending_orders'] ?? 0),
        ];
    }

    public function getTopSellingOverdriveBooks($limit = 10)
    {
        $sql = "
            SELECT 
                title,
                book_id,
                author,
                COUNT(*) AS total_orders
            FROM overdrive_transactions
            WHERE type_of_book = 1
            AND title IS NOT NULL
            AND title != ''
            GROUP BY title
            ORDER BY total_orders DESC
            LIMIT ?
        ";

        return $this->db->query($sql, [$limit])->getResultArray();
    }

    public function getOverdriveLanguageWiseSales()
    {
        $sql = "
            SELECT
                l.language_name,
                SUM(t.inr_value) AS total_sales
            FROM overdrive_transactions t
            JOIN language_tbl l 
                ON l.language_id = t.language_id
            WHERE t.type_of_book = 1
            AND t.status = 'p'
            GROUP BY t.language_id, l.language_name
            ORDER BY total_sales DESC
        ";

        return $this->db->query($sql)->getResultArray();
    }
    public function getOverdriveYearWiseSales()
    {
        $sql = "
            SELECT 
                YEAR(transaction_date) AS sales_year,
                SUM(inr_value) AS total_sales
            FROM overdrive_transactions
            WHERE type_of_book = 1
            AND status IN ('p','o')
            GROUP BY YEAR(transaction_date)
            ORDER BY sales_year ASC
        ";

        return $this->db->query($sql)->getResultArray();
    }
    public function getOverdriveGenreWiseSales()
    {
        $sql = "
            SELECT
                g.genre_name,
                SUM(t.inr_value) AS total_sales
            FROM overdrive_transactions t
            JOIN book_tbl b ON b.book_id = t.book_id
            JOIN genre_details_tbl g ON g.genre_id = b.genre_id
            WHERE t.type_of_book = 1
            AND t.status = 'p'
            GROUP BY g.genre_name
            ORDER BY total_sales DESC
        ";

        return $this->db->query($sql)->getResultArray();
    }
    public function getOverdriveUploadedBooks()
    {
        return $this->db->query("
            SELECT 
                b.book_id,
                b.book_title,
                a.author_name
            FROM overdrive_books ob
            JOIN book_tbl b ON b.book_id = ob.book_id
            LEFT JOIN author_tbl a ON a.author_id = b.author_name
            WHERE ob.type_of_book = 1
            GROUP BY b.book_id, b.book_title, a.author_name
        ")->getResultArray();
    }

        // Total Authors → Author wise books count
    public function getOverdriveAuthorWiseBooks()
    {
        $sql = "
            SELECT 
                a.author_id,          
                a.author_name,
                COUNT(DISTINCT ob.book_id) AS total_books
            FROM overdrive_books ob
            JOIN author_tbl a 
                ON a.author_id = ob.author_id
            WHERE ob.type_of_book = 1
            GROUP BY a.author_id, a.author_name
            ORDER BY total_books DESC
        ";

        return $this->db->query($sql)->getResultArray();
    }

    public function getOverdriveBooksByAuthor($authorId)
    {
        $sql = "
            SELECT 
                b.book_id,
                b.book_title
            FROM overdrive_books ob
            JOIN book_tbl b 
                ON b.book_id = ob.book_id
            WHERE ob.type_of_book = 1
            AND ob.author_id = ?
            GROUP BY b.book_id, b.book_title
            ORDER BY b.book_title ASC
        ";

        return $this->db->query($sql, [$authorId])->getResultArray();
    }
    public function getOverdriveOrders()
    {
        $sql = "
            SELECT
                t.transaction_date,
                t.book_id,
                b.book_title,
                a.author_name,
                t.retailer,
                t.status
            FROM overdrive_transactions t
            JOIN book_tbl b 
                ON b.book_id = t.book_id
            LEFT JOIN author_tbl a 
                ON a.author_id = b.author_name
            WHERE t.type_of_book = 1
            ORDER BY t.transaction_date DESC
        ";

        return $this->db->query($sql)->getResultArray();
    }

    public function getOverdriveBookTransactions($bookId)
    {
        return $this->db->query("
            SELECT 
                t.book_id,
                b.book_title,
                b.regional_book_title,
                b.isbn_number,
                b.cost,
                b.number_of_page,
                a.author_name,
                t.transaction_date,
                t.retailer,
                t.inr_value,
                t.final_royalty_value,
                t.status
            FROM overdrive_transactions t
            JOIN book_tbl b 
                ON b.book_id = t.book_id
            LEFT JOIN author_tbl a 
                ON a.author_id = b.author_name
            WHERE t.book_id = ?
            ORDER BY t.transaction_date DESC
        ", [$bookId])->getResultArray();
    }
    public function getScribdEbookSummary()
    {
        return $this->db->table('scribd_books')
            ->select('
                COUNT(DISTINCT title) AS title_count,
                COUNT(DISTINCT author_id) AS author_count
            ')
            ->where('published', 1)
            ->where('in_subscription', 1)
            ->get()
            ->getRowArray();
    }
    public function getScribdDashboardData()
    {
        $data = [];

        /* SUMMARY */
        $summarySql = "
            SELECT
                (SELECT COUNT(DISTINCT title)
                FROM scribd_books
                WHERE published = 1
                AND in_subscription = 1) AS total_titles,

                (SELECT COUNT(DISTINCT author_id)
                FROM scribd_books
                WHERE published = 1
                AND in_subscription = 1) AS total_creators,

                (SELECT COUNT(DISTINCT Country_of_reader)
                FROM scribd_transaction
                WHERE status IN ('p','o')
                AND Country_of_reader IS NOT NULL
                AND Country_of_reader != '') AS total_countries,

                (SELECT COUNT(*) FROM scribd_transaction WHERE status IN ('p','o')) AS total_orders,
                (SELECT COUNT(*) FROM scribd_transaction WHERE status = 'p') AS orders_paid,
                (SELECT COUNT(*) FROM scribd_transaction WHERE status = 'o') AS orders_pending,

                (SELECT SUM(converted_inr_full) FROM scribd_transaction WHERE status IN ('p','o')) AS total_revenue,
                (SELECT SUM(converted_inr_full) FROM scribd_transaction WHERE status = 'p') AS revenue_paid,
                (SELECT SUM(converted_inr_full) FROM scribd_transaction WHERE status = 'o') AS revenue_pending
        ";
        $data['summary'] = $this->db->query($summarySql)->getRowArray();


        /* ===================== TOP 10 BOOKS ===================== */
        $data['top_books'] = $this->db->query("
            SELECT book_id, title, COUNT(*) AS total_reads, authors
            FROM scribd_transaction
            WHERE status = 'p'
            AND title IS NOT NULL AND title != ''
            GROUP BY book_id, title
            ORDER BY total_reads DESC
            LIMIT 10
        ")->getResultArray();


        /* LANGUAGE WISE */
        $data['language_sales'] = $this->db->query("
            SELECT l.language_name,
                SUM(t.converted_inr_full) AS total_sales
            FROM scribd_transaction t
            JOIN language_tbl l ON l.language_id = t.language_id
            WHERE t.status = 'p'
            GROUP BY t.language_id, l.language_name
            ORDER BY total_sales DESC
        ")->getResultArray(); 


        /* YEAR WISE SALES */
        $data['year_sales'] = $this->db->query("
            SELECT YEAR(Payout_month) AS year,
                SUM(converted_inr_full) AS total_sales
            FROM scribd_transaction
            WHERE status = 'p'
            GROUP BY YEAR(Payout_month)
            ORDER BY YEAR(Payout_month)
        ")->getResultArray();


        /* GENRE WISE SALES */
        $data['genre_sales'] = $this->db->table('scribd_transaction t')
        ->select('g.genre_name AS genre, SUM(t.converted_inr_full) AS total_sales')
        ->join('book_tbl b', 'b.book_id = t.book_id')
        ->join('genre_details_tbl g', 'g.genre_id = b.genre_id')
        ->where('t.status', 'p')
        ->groupBy('g.genre_name')
        ->having('g.genre_name IS NOT NULL')
        ->orderBy('total_sales', 'DESC')
        ->get()
        ->getResultArray();

        return $data;
    }
    //** GET SINGLE BOOK */
  public function getBookById($bookId)
{
    return $this->db->query("
        SELECT 
            sb.book_id,
            b.book_title,
            b.regional_book_title,
            b.isbn_number,
            b.cost,
            b.number_of_page,
            a.author_name
        FROM scribd_books sb
        LEFT JOIN book_tbl b 
            ON b.book_id = sb.book_id
        LEFT JOIN author_tbl a
            ON a.author_id = b.author_name
        WHERE sb.book_id = ?
        LIMIT 1
    ", [$bookId])->getRowArray();
}

    /** GET SCRIBD TRANSACTIONS */
    public function getTransactionsByBook($bookId)
    {
        return $this->db->query("
            SELECT
                t.book_id,
                b.book_title,
                b.regional_book_title,
                b.isbn_number,
                b.cost,
                b.number_of_page,
                a.author_name,
                t.payout_month AS payment_month,
                t.country_of_reader,
                t.converted_inr,
                t.converted_inr_full,
                t.status
            FROM scribd_transaction t
            JOIN book_tbl b
                ON b.book_id = t.book_id                      
            LEFT JOIN author_tbl a
                ON a.author_id = b.author_name             
            LEFT JOIN scribd_books sb
                ON sb.book_id = t.book_id                    
            WHERE t.book_id = ?
            AND t.status = 'p'
            ORDER BY t.payout_month DESC
        ", [$bookId])->getResultArray();
    }

    public function getScribdBooks()
        {
            return $this->db->table('scribd_books b')   // <-- make sure this table exists
                            ->select('b.*, a.author_name')
                            ->join('author_tbl a', 'a.author_id = b.author_id', 'left')
                            ->orderBy('b.book_id', 'DESC')
                            ->get()
                            ->getResultArray();
        }

    public function getAuthorsWithScribdBooks()
    {
        return $this->db->query("
            SELECT DISTINCT
                a.author_id,
                a.author_name
            FROM scribd_books sb
            JOIN author_tbl a
                ON a.author_id = sb.author_id
            ORDER BY a.author_name ASC
        ")->getResultArray();
    }

    public function getBooksScribdByAuthor($authorId)
    {
        return $this->db->query("
            SELECT
                sb.book_id,
                b.book_title AS title,
                b.isbn_number,
                b.cost
            FROM scribd_books sb
            JOIN book_tbl b ON b.book_id = sb.book_id
            WHERE sb.author_id = ?
            ORDER BY b.book_title ASC
        ", [$authorId])->getResultArray();
    }
    // public function getScribdOrders()
    // {
    //     return $this->db->query("
    //         SELECT 
    //             book_id,
    //             title,
    //             authors,
    //             isbn,
    //             converted_inr,
    //             converted_inr_full
    //         FROM scribd_transaction
    //         ORDER BY payout_month DESC
    //     ")->getResultArray();
    // }
    /*  SUMMARY */
    public function getStorytelSummary()
    {
        $sql = "
                SELECT
        /* Titles & Authors from storytel_books */
        (SELECT COUNT(DISTINCT book_id)
         FROM storytel_books
         WHERE type_of_book = 1
        ) AS total_titles,

        (SELECT COUNT(DISTINCT author_name)
         FROM storytel_books
         WHERE type_of_book = 1
        ) AS total_creators,

    /* Months */
    COUNT(DISTINCT DATE_FORMAT(transaction_date, '%Y-%m')) AS total_months,

    /* Orders */
    COUNT(*) AS total_orders,
    SUM(CASE WHEN status = 'p' THEN 1 ELSE 0 END) AS orders_paid,
    SUM(CASE WHEN status = 'o' THEN 1 ELSE 0 END) AS orders_pending,

    /* Revenue */
    SUM(remuneration_inr) AS total_revenue,
    SUM(CASE WHEN status = 'p' THEN remuneration_inr ELSE 0 END) AS revenue_paid,
    SUM(CASE WHEN status = 'o' THEN remuneration_inr ELSE 0 END) AS revenue_pending

    FROM storytel_transactions
    WHERE type_of_book = 1
            ";

        return $this->db->query($sql)->getRowArray();
    }

    /* TOP BOOKS */
    public function getTopStorytelBooks($limit = 10)
    {
        $sql = "
            SELECT
                t.book_id,
                t.title,
                t.author,
                SUM(t.no_of_units) AS total_units
            FROM storytel_transactions t
            WHERE t.status = 'p'
              AND t.type_of_book = 1
            GROUP BY t.book_id, t.title
            ORDER BY total_units DESC
            LIMIT ?
        ";

        return $this->db->query($sql, [$limit])->getResultArray();
    }

    /* LANGUAGE WISE SALES */
    public function getStorytelLanguageWiseSales()
    {
        $sql = "
            SELECT
                l.language_name,
                SUM(t.remuneration_inr) AS total_sales
            FROM storytel_transactions t
            JOIN language_tbl l 
                ON l.language_id = t.language_id
            WHERE t.status = 'p'
              AND t.type_of_book = 1
            GROUP BY t.language_id, l.language_name
            ORDER BY total_sales DESC
        ";

        return $this->db->query($sql)->getResultArray();
    }
        public function getStorytelGenreWiseSales()
    {
        $sql = "
            SELECT 
                g.genre_name,
                SUM(t.remuneration_inr) AS total_sales
            FROM storytel_transactions t
            JOIN book_tbl b ON b.book_id = t.book_id
            JOIN genre_details_tbl g ON g.genre_id = b.genre_id
            WHERE t.status = 'p'
            AND t.type_of_book = 1
            GROUP BY g.genre_id, g.genre_name
            ORDER BY total_sales DESC
        ";

        return $this->db->query($sql)->getResultArray();
    }
    public function getStorytelYearWiseSales()
    {
        $sql = "
            SELECT
                YEAR(transaction_date) AS year,
                SUM(remuneration_inr) AS total_sales
            FROM storytel_transactions
            WHERE status = 'p'
            AND type_of_book = 1
            GROUP BY YEAR(transaction_date)
            ORDER BY YEAR(transaction_date)
        ";

        return $this->db->query($sql)->getResultArray();
    }

    /* GET ALL UPLOADED STORYTEL BOOKS */
    public function getstorytelBooks()
    {
        return $this->db->query("
            SELECT sb.book_id,
                MIN(sb.title)  AS title,
                GROUP_CONCAT(DISTINCT sb.author_name) AS author_name
            FROM storytel_books sb
            WHERE sb.type_of_book = 1
            GROUP BY sb.book_id
            ORDER BY title ASC;
        ")->getResultArray();
    }
    public function getStorytelAuthorName($authorId)
    {
        return $this->db->table('storytel_books')
            ->select('author_name')
            ->where('author_id', $authorId)
            ->where('type_of_book', 1)
            ->distinct()
            ->get()
            ->getRow('author_name'); 
    }
    public function getstorytelAuthors()
    {
        return $this->db->table('storytel_books')
            ->select('author_id, author_name, COUNT(DISTINCT book_id) AS total_books')
            ->where('type_of_book', 1)
            ->groupBy('author_id, author_name')
            ->orderBy('author_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    // 2. Get books for an author
    public function getstorytelBooksByAuthor($authorId)
    {
        return $this->db->table('storytel_books sb')
            ->select('sb.book_id, sb.title, sb.author_name')
            ->join('author_tbl a', 'a.author_name = sb.author_name', 'inner')
            ->where('a.author_id', $authorId)
            ->where('sb.type_of_book', 1)
            ->groupBy('sb.book_id')
            ->orderBy('sb.title', 'ASC')
            ->get()
            ->getResultArray();
    }
        /** Get full book details with book_tbl + author */
        public function getBookDetails($bookId)
        {
            return $this->db->table('storytel_books sb')
                ->distinct()
                ->select('sb.book_id,
                        bt.book_title,
                        bt.regional_book_title,
                        bt.isbn_number,
                        bt.cost,
                        a.author_name')
                ->join('book_tbl bt', 'bt.book_id = sb.book_id', 'left')
                ->join('author_tbl a', 'a.author_id = sb.author_id', 'left') // correct join
                ->where('sb.book_id', $bookId)
                ->where('sb.type_of_book', 1)
                ->get()
                ->getRowArray();
        }


    /** GET TRANSACTIONS FOR A BOOK */
    public function getTransactions($bookId)
    {
        return $this->db->table('storytel_transactions st')
            ->select('st.transaction_date, st.remuneration_inr, st.final_royalty_value, st.status')
            ->where('st.book_id', $bookId)
            ->where('st.type_of_book', 1)
            ->orderBy('st.transaction_date', 'DESC')
            ->get()
            ->getResultArray();
    }
    /* SUMMARY */
    public function getGoogleSummary()
    {
        $sql = "
            SELECT
                COUNT(DISTINCT book_id) AS total_titles,
                COUNT(DISTINCT author_id) AS total_creators,
                COUNT(DISTINCT country_of_sale) AS total_retailers,

                COUNT(*) AS total_orders,
                SUM(CASE WHEN status = 'p' THEN 1 ELSE 0 END) AS orders_paid,
                SUM(CASE WHEN status = 'o' THEN 1 ELSE 0 END) AS orders_pending,

                SUM(inr_value) AS total_revenue,
                SUM(CASE WHEN status = 'p' THEN inr_value ELSE 0 END) AS revenue_paid,
                SUM(CASE WHEN status = 'o' THEN inr_value ELSE 0 END) AS revenue_pending,

                SUM(final_royalty_value) AS royalty_paid,
                SUM(publisher_revenue) AS publisher_revenue

            FROM google_transactions
            WHERE type_of_book = 1
        ";

        return $this->db->query($sql)->getRowArray();
    }

    /* TOP BOOKS */
    public function getTopGoogleBooks($limit = 10)
    {
        $sql = "
            SELECT
                book_id,
                title,
                author,
                SUM(qty) AS total_orders
            FROM google_transactions
            WHERE type_of_book = 1
              AND status = 'p'
            GROUP BY book_id, title
            ORDER BY total_orders DESC
            LIMIT ?
        ";

        return $this->db->query($sql, [$limit])->getResultArray();
    }

    // YEAR WISE SALES
    public function getGoogleYearWise()
    {
        return $this->db->query("
            SELECT 
                YEAR(transaction_date) AS year,
                SUM(inr_value) AS total
            FROM google_transactions
            GROUP BY YEAR(transaction_date)
        ")->getResultArray();
    }

    // LANGUAGE WISE SALES
    public function getGoogleLanguageWise()
    {
        return $this->db->query("
            SELECT 
                l.language_name,
                SUM(t.inr_value) AS total
            FROM google_transactions t
            JOIN google_books gb ON gb.book_id=t.book_id
            JOIN language_tbl l ON l.language_id=gb.language_id
            WHERE gb.type_of_book=1
            GROUP BY l.language_id
        ")->getResultArray();
    }

    // GENRE WISE SALES
    public function getGoogleGenreWise()
    {
        return $this->db->query("
            SELECT 
                g.genre_name,
                SUM(t.inr_value) AS total
            FROM google_transactions t
            JOIN book_tbl b ON b.book_id=t.book_id
            JOIN genre_details_tbl g ON g.genre_id=b.genre_id
            GROUP BY g.genre_id
        ")->getResultArray();
    }
     // ============== LISTS + DRILLS ==============
    public function getGoogleBooksList()
    {
        return $this->db->query("
            SELECT 
                gb.book_id, 
                gb.title, 
                a.author_name
            FROM google_books gb
            LEFT JOIN author_tbl a ON a.author_id = gb.author_id
            WHERE gb.type_of_book = 1
        ")->getResultArray();
    }

    public function getGoogleBookDetails($bookId)
    {
        return $this->db->query("
            SELECT 
                b.book_id, 
                b.book_title, 
                b.regional_book_title,
                a.author_name,
                b.isbn_number,
                b.cost
            FROM book_tbl b
            LEFT JOIN author_tbl a ON a.author_id = b.author_name
            WHERE b.book_id = $bookId
        ")->getRowArray();
    }

    public function getGoogleBookTransactions($bookId)
    {
        return $this->db->query("
            SELECT 
                transaction_date AS earnings_date,
                inr_value,
                final_royalty_value,
                status
            FROM google_transactions
            WHERE book_id = $bookId
            ORDER BY transaction_date DESC
        ")->getResultArray();
    }

    public function getGoogleAuthors()
    {
        return $this->db->query("
            SELECT 
                a.author_id,
                a.author_name,
                COUNT(gb.book_id) AS total_titles
            FROM google_books gb
            JOIN author_tbl a ON a.author_id = gb.author_id
            WHERE gb.type_of_book = 1
            GROUP BY gb.author_id
        ")->getResultArray();
    }

    public function getGoogleAuthorBooks($authorId)
    {
        return $this->db->query("
            SELECT 
                gb.book_id,
                gb.title
            FROM google_books gb
            WHERE gb.author_id = $authorId 
              AND gb.type_of_book = 1
        ")->getResultArray();
    }
  public function getpratilipiBooksAuthors()
{
    return $this->db->table('pratilipi_books')
        ->select('
            COUNT(DISTINCT book_id) AS total_titles,
            COUNT(DISTINCT author_id) AS total_authors
        ')
        ->where('book_id !=', 0)
        ->where('book_id IS NOT NULL')
        ->get()
        ->getRowArray();
}


// Orders Summary //
public function getpratilipiOrdersSummary()
{
    return $this->db->table('pratilipi_transactions')
        ->select("
            COUNT(*) AS total_orders,
            SUM(CASE WHEN status='p' THEN 1 ELSE 0 END) AS paid_orders,
            SUM(CASE WHEN status='o' THEN 1 ELSE 0 END) AS pending_orders
        ")
        ->where('type_of_book', 1)
        ->get()
        ->getRowArray();
}

// Earnings Summary //
public function getpratilipiEarningsSummary()
{
    return $this->db->table('pratilipi_transactions')
        ->select("
            SUM(earning) AS total_earning,
            SUM(CASE WHEN status='p' THEN earning ELSE 0 END) AS paid_earning,
            SUM(CASE WHEN status='o' THEN earning ELSE 0 END) AS pending_earning
        ")
        ->where('type_of_book', 1)
        ->get()
        ->getRowArray();
}

public function getPratilipiTopAuthors($limit = 10)
{
    return $this->db->table('pratilipi_transactions t')
        ->select("t.author_id, t.author_name, COUNT(*) AS sales_count")
        ->whereIn('t.status', ['p', 'o'])
        ->where('t.type_of_book', 1)
        ->groupBy('t.author_id, t.author_name')
        ->orderBy('sales_count', 'DESC')
        ->limit($limit)
        ->get()
        ->getResultArray();
}

// Year-wise
public function getpratilipiYearTransactions()
{
    return $this->db->table('pratilipi_transactions')
        ->select('YEAR(transaction_date) AS year, COUNT(*) AS total_orders, SUM(earning) AS total_earning')
        ->where('type_of_book', 1)
        ->groupBy('YEAR(transaction_date)')
        ->orderBy('year', 'ASC')
        ->get()
        ->getResultArray();
}

public function getPratilipiBooks()
{
    return $this->db->table('pratilipi_books')
        ->select('book_id, content_titles, author_id, author_name')
        ->where('book_id !=', 0)
        ->where('book_id IS NOT NULL')
        ->groupBy('book_id, content_titles, author_id, author_name')
        ->orderBy('book_id', 'ASC')
        ->get()
        ->getResultArray();
}
public function getPratilipiAuthorInfo($authorId)
{
    return $this->db->table('pratilipi_books')
        ->select('author_id, author_name')
        ->where('author_id', $authorId)
        ->get()
        ->getRowArray();
}

public function getPratilipiAuthorTransactions($authorId)
{
    return $this->db->table('pratilipi_transactions')
        ->select('transaction_date, earning, final_royalty_value, status')
        ->where('author_id', $authorId)
        ->where('type_of_book', 1)
        ->orderBy('transaction_date', 'DESC')
        ->get()
        ->getResultArray();
}
public function getPratilipiAuthors()
{
    return $this->db->table('pratilipi_books')
        ->select("
            author_id,
            MAX(author_name) AS author_name,
            COUNT(DISTINCT book_id) AS book_count
        ")
        ->where('book_id !=', 0)
        ->where('book_id IS NOT NULL')
        ->groupBy('author_id')
        ->orderBy('book_count', 'DESC')
        ->get()
        ->getResultArray();
}


public function getBooksByAuthor($authorId)
{
    return $this->db->table('pratilipi_books')
        ->select('book_id, MAX(content_titles) AS title, MAX(author_name) AS author_name')
        ->where('author_id', $authorId)
        ->where('book_id !=', 0)
        ->where('book_id IS NOT NULL')
        ->groupBy('book_id')
        ->orderBy('title', 'ASC')
        ->get()
        ->getResultArray();
}



public function getAuthorName($authorId)
{
    return $this->db->table('pratilipi_books')
        ->select('author_name')
        ->where('author_id', $authorId)
        ->get()
        ->getRow('author_name');
}


}
