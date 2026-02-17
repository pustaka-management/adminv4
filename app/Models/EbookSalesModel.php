<?php

namespace App\Models;

use CodeIgniter\Model;

class EbookSalesModel extends Model
{
    protected $DBGroup = 'default';

   public function getAmazonEbookSummary(): array
{
    $db = \Config\Database::connect();

    $sql = "
        SELECT
            /* amazon_books */
            (SELECT COUNT(*) FROM amazon_books) AS total_titles,
            (SELECT COUNT(DISTINCT author_id) FROM amazon_books) AS total_authors,

            /* units */
            (SELECT IFNULL(SUM(net_units), 0) FROM amazon_transactions) AS total_units_sold,
            (SELECT IFNULL(SUM(units_refunded), 0) FROM amazon_transactions) AS total_units_refunded,

            /* status = p */
            (SELECT IFNULL(SUM(inr_value), 0)
             FROM amazon_transactions
             WHERE status = 'p'
            ) AS p_inr_total,

            (SELECT IFNULL(SUM(final_royalty_value), 0)
             FROM amazon_transactions
             WHERE status = 'p'
            ) AS p_royalty_total,

            /* status = o */
            (SELECT IFNULL(SUM(inr_value), 0)
             FROM amazon_transactions
             WHERE status = 'o'
            ) AS o_inr_total,

            (SELECT IFNULL(SUM(final_royalty_value), 0)
             FROM amazon_transactions
             WHERE status = 'o'
            ) AS o_royalty_total
    ";

    return $db->query($sql)->getRowArray();
}
public function getTopSellingAndReturnedBooks($limit = 10)
{
    $rows = $this->db->table('amazon_transactions')
        ->select([
            'book_id',
            'title',
            'author',
            'SUM(net_units) AS total_units_sold',
            'SUM(units_refunded) AS total_units_refunded'
        ])
        ->groupBy('book_id, title, author')
        ->get()
        ->getResultArray();

    $topSelling  = [];
    $topReturned = [];

    foreach ($rows as $row) {

        $row['total_units_sold']     = (int) $row['total_units_sold'];
        $row['total_units_refunded'] = (int) $row['total_units_refunded'];

        if ($row['total_units_sold'] > 0) {
            $topSelling[] = $row;
        }

        if ($row['total_units_refunded'] > 0) {
            $topReturned[] = $row;
        }
    }

    usort($topSelling, function ($a, $b) {
        return $b['total_units_sold'] <=> $a['total_units_sold'];
    });

    usort($topReturned, function ($a, $b) {
        return $b['total_units_refunded'] <=> $a['total_units_refunded'];
    });

    return [
        'top_selling_books'  => array_slice($topSelling, 0, $limit),
        'top_returned_books' => array_slice($topReturned, 0, $limit),
    ];
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

    /* ===================== SUMMARY ===================== */
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


    /* ===================== LANGUAGE WISE ===================== */
    $data['language_sales'] = $this->db->query("
        SELECT l.language_name,
               SUM(t.converted_inr_full) AS total_sales
        FROM scribd_transaction t
        JOIN language_tbl l ON l.language_id = t.language_id
        WHERE t.status = 'p'
        GROUP BY t.language_id, l.language_name
        ORDER BY total_sales DESC
    ")->getResultArray(); 


    /* ===================== YEAR WISE SALES ===================== */
    $data['year_sales'] = $this->db->query("
        SELECT YEAR(Payout_month) AS year,
               SUM(converted_inr_full) AS total_sales
        FROM scribd_transaction
        WHERE status = 'p'
        GROUP BY YEAR(Payout_month)
        ORDER BY YEAR(Payout_month)
    ")->getResultArray();


    /* ===================== GENRE WISE SALES ===================== */
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
    /** GET SINGLE BOOK INFO */
    public function getBookById($bookId)
    {
        return $this->db->query("
            SELECT 
                sb.book_id,
                sb.book_title,
                sb.regional_book_title,
                sb.isbn AS isbn_number,
                sb.cost,
                sb.number_of_page,
                a.author_name
            FROM scribd_books sb
            LEFT JOIN author_tbl a 
                ON a.author_id = sb.author_id
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
                sb.book_title,
                sb.regional_book_title,
                sb.isbn AS isbn_number,
                sb.cost,
                sb.number_of_page,
                a.author_name,
                t.payout_month AS payment_month,
                t.country_of_reader,
                t.converted_inr,
                t.converted_inr_full,
                t.status
            FROM scribd_transaction t
            JOIN scribd_books sb 
                ON sb.book_id = t.book_id
            LEFT JOIN author_tbl a 
                ON a.author_id = sb.author_id
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
    /* ================= SUMMARY ================= */
    public function getStorytelSummary()
    {
        $sql = "
            SELECT
                /* Titles & Authors */
                COUNT(DISTINCT book_id) AS total_titles,
                COUNT(DISTINCT author_id) AS total_creators,

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

    /* ================= TOP BOOKS ================= */
    public function getTopStorytelBooks($limit = 10)
    {
        $sql = "
            SELECT
                t.book_id,
                t.title,
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

    /* ================= LANGUAGE WISE SALES ================= */
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
    public function getStorytelMonthlyRoyalty()
{
    $sql = "
        SELECT
            DATE_FORMAT(transaction_date, '%b %Y') AS month,
            SUM(final_royalty_value) AS total_royalty
        FROM storytel_transactions
        WHERE status = 'p'
          AND type_of_book = 1
        GROUP BY DATE_FORMAT(transaction_date, '%Y-%m')
        ORDER BY transaction_date
    ";

    return $this->db->query($sql)->getResultArray();
}
    /* ===================== SUMMARY ===================== */
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

    /* ===================== TOP BOOKS ===================== */
    public function getTopGoogleBooks($limit = 10)
    {
        $sql = "
            SELECT
                book_id,
                title,
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

    /* ===================== TOP RETAILERS ===================== */
    public function getTopGoogleRetailers($limit = 10)
    {
        $sql = "
            SELECT
                country_of_sale AS retailer,
                SUM(qty) AS total_orders
            FROM google_transactions
            WHERE type_of_book = 1
              AND status = 'p'
            GROUP BY country_of_sale
            ORDER BY total_orders DESC
            LIMIT ?
        ";

        return $this->db->query($sql, [$limit])->getResultArray();
    }

    /* ===================== LANGUAGE WISE SALES ===================== */
    public function getLanguageWiseSales()
    {
        $sql = "
            SELECT
                l.language_name,
                SUM(g.inr_value) AS total_sales
            FROM google_transactions g
            JOIN language_tbl l ON l.language_id = g.language_id
            WHERE g.type_of_book = 1
              AND g.status = 'p'
            GROUP BY g.language_id, l.language_name
            ORDER BY total_sales DESC
        ";

        return $this->db->query($sql)->getResultArray();
    }
}
