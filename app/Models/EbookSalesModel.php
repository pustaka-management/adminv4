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
            SELECT COUNT(DISTINCT title) AS cnt
            FROM overdrive_books
            WHERE type_of_book = 1
        ")->getRow()->cnt ?? 0;

        // Total Creators
        $totalCreators = $db->query("
            SELECT COUNT(DISTINCT creators) AS cnt
            FROM overdrive_books
            WHERE type_of_book = 1
        ")->getRow()->cnt ?? 0;

        //  Total Retailers 
        $totalRetailers = $db->query("
            SELECT COUNT(DISTINCT 
                SUBSTRING_INDEX(
                    SUBSTRING_INDEX(retailer, '(', -1),
                ')', 1)
            ) AS cnt
            FROM overdrive_transactions
            WHERE type_of_book = 1
              AND retailer IS NOT NULL
              AND retailer != ''
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

        // Total Royalty 
        $royalty = $db->query("
            SELECT
                SUM(final_royalty_value) AS total_royalty,
                SUM(CASE WHEN status = 'p' THEN final_royalty_value ELSE 0 END) AS paid_royalty,
                SUM(CASE WHEN status = 'o' THEN final_royalty_value ELSE 0 END) AS pending_royalty
            FROM overdrive_transactions
            WHERE type_of_book = 1
              AND status IN ('p','o')
        ")->getRowArray();

        return [
            'total_titles'    => (int)$totalTitles,
            'total_creators'  => (int)$totalCreators,
            'total_retailers' => (int)$totalRetailers,
            'sales_total'       => (float)($sales['total_value'] ?? 0),
            'sales_paid'        => (float)($sales['paid_value'] ?? 0),
            'sales_outstanding' => (float)($sales['outstanding_value'] ?? 0),
            'royalty_total'   => (float)($royalty['total_royalty'] ?? 0),
            'royalty_paid'    => (float)($royalty['paid_royalty'] ?? 0),
            'royalty_pending' => (float)($royalty['pending_royalty'] ?? 0),
        ];
    }

    public function getTopSellingOverdriveBooks($limit = 10)
{
    $sql = "
        SELECT 
            title,
            book_id,
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
public function getTopOverdriveRetailers($limit = 10)
{
    $sql = "
        SELECT 
            TRIM(retailer) AS retailer,
            COUNT(*) AS total_orders
        FROM overdrive_transactions
        WHERE type_of_book = 1
          AND retailer IS NOT NULL
          AND retailer != ''
        GROUP BY retailer
        ORDER BY total_orders DESC
        LIMIT ?
    ";

    return $this->db->query($sql, [$limit])->getResultArray();
}


}
