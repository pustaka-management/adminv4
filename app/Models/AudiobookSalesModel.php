<?php

namespace App\Models;

use CodeIgniter\Model;

class AudiobookSalesModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }
    public function getAudibleSummary()
    {
        // Total books
        $books = $this->db->table('audible_books')
            ->select('COUNT(id) as total_books')
            ->get()
            ->getRowArray();

        // Total authors
        $authors = $this->db->table('audible_books')
            ->select('COUNT(DISTINCT authors) as total_authors')
            ->get()
            ->getRowArray();

        // Orders + revenue
        $orders = $this->db->table('audible_transactions')
            ->select("
                COUNT(id) as total_orders,
                SUM(total_net_sales) as total_revenue,
                SUM(CASE WHEN status = 'p' THEN 1 ELSE 0 END) as orders_paid,
                SUM(CASE WHEN status = 'o' THEN 1 ELSE 0 END) as orders_pending,
                SUM(CASE WHEN status = 'p' THEN total_net_sales ELSE 0 END) as revenue_paid,
                SUM(CASE WHEN status = 'o' THEN total_net_sales ELSE 0 END) as revenue_pending
            ")
            ->get()
            ->getRowArray();

        return [
            'total_books'     => $books['total_books'] ?? 0,
            'total_authors'   => $authors['total_authors'] ?? 0,
            'total_orders'    => $orders['total_orders'] ?? 0,
            'total_net_sales' => $orders['total_revenue'] ?? 0,

            'orders_paid'     => $orders['orders_paid'] ?? 0,
            'orders_pending'  => $orders['orders_pending'] ?? 0,

            'revenue_paid'    => $orders['revenue_paid'] ?? 0,
            'revenue_pending' => $orders['revenue_pending'] ?? 0,
        ];
    }

    /*  TOP SELLING AUDIBLE BOOKS */
    public function getTopSellingAudibleBooks($limit = 10)
    {
        return $this->db->table('audible_transactions t')
            ->select('
                t.name   AS title,
                t.author AS author,
                COUNT(t.id) AS total_units
            ')
            ->where('t.status', 'p')
            ->groupBy(['t.name', 't.author'])
            ->orderBy('total_units', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /*  LANGUAGE WISE SALES  */
    public function getAudibleLanguageWiseSales()
    {
        return $this->db->table('audible_transactions t')
            ->select('
                l.language_name,
                SUM(t.total_net_sales) AS total_sales
            ')
            ->join(
                'audible_books b',
                'b.title = t.name AND b.authors = t.author',
                'left'
            )
            ->join('language_tbl l', 'l.language_id = b.language_id', 'left')
            ->where('t.status', 'p')
            ->groupBy('b.language_id')
            ->orderBy('total_sales', 'DESC')
            ->get()
            ->getResultArray();
    }

    /* TOTAL PAID SALES */
    public function getAudiblePaidSales()
    {
        return $this->db->table('audible_transactions')
            ->select('SUM(total_net_sales) as paid_amount')
            ->where('status', 'p')
            ->get()
            ->getRowArray()['paid_amount'] ?? 0;
    }

    /*  TOTAL PENDING SALES */
    public function getAudiblePendingSales()
    {
        return $this->db->table('audible_transactions')
            ->select('SUM(total_net_sales) as pending_amount')
            ->where('status', 'o')
            ->get()
            ->getRowArray()['pending_amount'] ?? 0;
    }
    public function getOverdriveAudioSummary()
    {
        $db = \Config\Database::connect();

        //  Total Titles
        $totalTitles = $db->query("
            SELECT COUNT(DISTINCT title) AS cnt
            FROM overdrive_books
            WHERE type_of_book = 3
        ")->getRow()->cnt ?? 0;

        // Total Creators
        $totalCreators = $db->query("
            SELECT COUNT(DISTINCT creators) AS cnt
            FROM overdrive_books
            WHERE type_of_book = 3
        ")->getRow()->cnt ?? 0;

        //  Total Sales 
        $sales = $db->query("
            SELECT
                SUM(inr_value) AS total_value,
                SUM(CASE WHEN status = 'p' THEN inr_value ELSE 0 END) AS paid_value,
                SUM(CASE WHEN status = 'o' THEN inr_value ELSE 0 END) AS outstanding_value
            FROM overdrive_transactions
            WHERE type_of_book = 3
              AND status IN ('p','o')
        ")->getRowArray();

        // Total orders 
        $royalty = $db->query("
            SELECT
                COUNT(*) AS total_orders,
                COUNT(CASE WHEN status = 'p' THEN overdrive_id END) AS paid_orders,
                COUNT(CASE WHEN status = 'o' THEN overdrive_id END) AS pending_orders
            FROM overdrive_transactions
            WHERE type_of_book = 3
            AND status IN ('p','o')
        ")->getRowArray();

       return [
    'total_titles'   => (int) $totalTitles,
    'total_creators' => (int) $totalCreators,

    // Revenue keys SAME as view
    'total_value'       => (float) ($sales['total_value'] ?? 0),
    'paid_value'        => (float) ($sales['paid_value'] ?? 0),
    'outstanding_value' => (float) ($sales['outstanding_value'] ?? 0),
            'total_orders'   => (float)($royalty['total_orders'] ?? 0),
            'paid_orders'    => (float)($royalty['paid_orders'] ?? 0),
            'pending_orders' => (float)($royalty['pending_orders'] ?? 0),
        ];
    }

    public function getTopSellingOverdriveAudiobooks($limit = 10)
{
    $sql = "
        SELECT 
            title,
            book_id,
            COUNT(*) AS total_orders
        FROM overdrive_transactions
        WHERE type_of_book = 3
          AND title IS NOT NULL
          AND title != ''
        GROUP BY title
        ORDER BY total_orders DESC
        LIMIT ?
    ";

    return $this->db->query($sql, [$limit])->getResultArray();
}

public function getOverdriveAudiobookLanguageWiseSales()
{
    $sql = "
        SELECT
            l.language_name,
            SUM(t.inr_value) AS total_sales
        FROM overdrive_transactions t
        JOIN language_tbl l 
            ON l.language_id = t.language_id
        WHERE t.type_of_book = 3
          AND t.status = 'p'
        GROUP BY t.language_id, l.language_name
        ORDER BY total_sales DESC
    ";

    return $this->db->query($sql)->getResultArray();
}
/* ===================== GOOGLE AUDIOBOOK SUMMARY ===================== */
public function getGoogleAudiobookSummary()
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
        WHERE type_of_book = 3
    ";

    return $this->db->query($sql)->getRowArray();
}

/* ===================== TOP AUDIOBOOKS ===================== */
public function getTopGoogleAudiobooks($limit = 10)
{
    $sql = "
        SELECT
            book_id,
            title,
            SUM(qty) AS total_orders
        FROM google_transactions
        WHERE type_of_book = 3
          AND status = 'p'
        GROUP BY book_id, title
        ORDER BY total_orders DESC
        LIMIT ?
    ";

    return $this->db->query($sql, [$limit])->getResultArray();
}

/* ===================== LANGUAGE WISE AUDIOBOOK SALES ===================== */
public function getGoogleAudiobookLanguageSales()
{
    $sql = "
        SELECT
            l.language_name,
            SUM(g.inr_value) AS total_sales
        FROM google_transactions g
        JOIN language_tbl l ON l.language_id = g.language_id
        WHERE g.type_of_book = 3
          AND g.status = 'p'
        GROUP BY g.language_id, l.language_name
        ORDER BY total_sales DESC
    ";

    return $this->db->query($sql)->getResultArray();
}
/* ================= AUDIOBOOK SUMMARY ================= */
public function getStorytelAudioSummary()
{
    $sql = "
        SELECT
            COUNT(DISTINCT book_id) AS total_titles,
            COUNT(DISTINCT author_id) AS total_creators,

            COUNT(*) AS total_orders,
            SUM(CASE WHEN status = 'p' THEN 1 ELSE 0 END) AS orders_paid,
            SUM(CASE WHEN status = 'o' THEN 1 ELSE 0 END) AS orders_pending,

            SUM(remuneration_inr) AS total_revenue,
            SUM(CASE WHEN status = 'p' THEN remuneration_inr ELSE 0 END) AS revenue_paid,
            SUM(CASE WHEN status = 'o' THEN remuneration_inr ELSE 0 END) AS revenue_pending
        FROM storytel_transactions
        WHERE type_of_book = 3
    ";

    return $this->db->query($sql)->getRowArray();
}

/* ================= TOP AUDIOBOOKS ================= */
public function getTopStorytelAudioBooks($limit = 10)
{
    $sql = "
        SELECT
            book_id,
            title,
            SUM(no_of_units) AS total_units
        FROM storytel_transactions
        WHERE status = 'p'
          AND type_of_book = 3
        GROUP BY book_id, title
        ORDER BY total_units DESC
        LIMIT ?
    ";

    return $this->db->query($sql, [$limit])->getResultArray();
}

/* ================= LANGUAGE WISE AUDIO SALES ================= */
public function getStorytelAudioLanguageWiseSales()
{
    $sql = "
        SELECT
            l.language_name,
            SUM(t.remuneration_inr) AS total_sales
        FROM storytel_transactions t
        JOIN language_tbl l 
            ON l.language_id = t.language_id
        WHERE t.status = 'p'
          AND t.type_of_book = 3
        GROUP BY t.language_id, l.language_name
        ORDER BY total_sales DESC
    ";

    return $this->db->query($sql)->getResultArray();
}
/* ================= SUMMARY ================= */
    public function getYoutubeSummary()
    {
        $sql = "
            SELECT
                SUM(youtube_revenue) AS youtube_total,
                SUM(CASE WHEN status='p' THEN youtube_revenue ELSE 0 END) AS youtube_paid,
                SUM(CASE WHEN status='o' THEN youtube_revenue ELSE 0 END) AS youtube_pending,

                SUM(pustaka_earnings) AS pustaka_total,
                SUM(CASE WHEN status='p' THEN pustaka_earnings ELSE 0 END) AS pustaka_paid,
                SUM(CASE WHEN status='o' THEN pustaka_earnings ELSE 0 END) AS pustaka_pending,

                SUM(youtube_revenue + pustaka_earnings) AS grand_total
            FROM youtube_transaction
        ";

        return $this->db->query($sql)->getRowArray();
    }

    /* ================= TOP BOOKS ================= */
    public function getTopYoutubeBooks($limit = 10)
    {
        $sql = "
            SELECT
                book_id,
                book_title,
                author_name,
                SUM(youtube_revenue + pustaka_earnings) AS total_amount
            FROM youtube_transaction
            WHERE status = 'p'
            GROUP BY book_id, book_title, author_name
            ORDER BY total_amount DESC
            LIMIT ?
        ";

        return $this->db->query($sql, [$limit])->getResultArray();
    }

    /* ================= LANGUAGE WISE ================= */
    public function getYoutubeLanguageSales()
    {
        $sql = "
            SELECT
                l.language_name,
                SUM(y.youtube_revenue + y.pustaka_earnings) AS total_sales
            FROM youtube_transaction y
            JOIN language_tbl l ON l.language_id = y.language_id
            WHERE y.status = 'p'
            GROUP BY y.language_id, l.language_name
            ORDER BY total_sales DESC
        ";

        return $this->db->query($sql)->getResultArray();
    }
     /* ================= BOOK COUNTS ================= */
    public function getBookStats()
    {
        $db = \Config\Database::connect();

        return [
            'total_books' => $db->table('kukufm_books')
                ->select('COUNT(DISTINCT book_id) AS cnt')
                ->get()->getRow()->cnt ?? 0,

            'total_authors' => $db->table('kukufm_books')
                ->select('COUNT(DISTINCT author_id) AS cnt')
                ->get()->getRow()->cnt ?? 0,

            'languageCounts' => $db->table('kukufm_books')
                ->select('l.language_name, COUNT(DISTINCT b.book_id) AS total_books')
                ->from('kukufm_books b')
                ->join('language_tbl l','l.language_id=b.language_id')
                ->groupBy('b.language_id')
                ->orderBy('total_books','DESC')
                ->get()->getResultArray()
        ];
    }

    /* ================= TRANSACTION SUMMARY ================= */
    public function getTransactionSummary()
    {
        $sql = "
            SELECT
                SUM(content_earning_amount) AS content_total,
                SUM(CASE WHEN status='p' THEN content_earning_amount ELSE 0 END) AS content_paid,
                SUM(CASE WHEN status='o' THEN content_earning_amount ELSE 0 END) AS content_pending,

                SUM(rev_share_amount) AS rev_total,
                SUM(CASE WHEN status='p' THEN rev_share_amount ELSE 0 END) AS rev_paid,
                SUM(CASE WHEN status='o' THEN rev_share_amount ELSE 0 END) AS rev_pending
            FROM kukufm_transactions
        ";

        return $this->db->query($sql)->getRowArray();
    }

    /* ================= TOP BOOKS ================= */
    public function getTopKukuBooks($limit = 10)
    {
        $sql = "
            SELECT
                book_id,
                show_name,
                SUM(content_earning_amount) AS content_total,
                SUM(rev_share_amount) AS rev_total
            FROM kukufm_transactions
            WHERE status='p'
            GROUP BY book_id, show_name
            ORDER BY content_total DESC
            LIMIT ?
        ";

        return $this->db->query($sql, [$limit])->getResultArray();
    }

}
