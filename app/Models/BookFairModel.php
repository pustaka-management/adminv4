<?php

namespace App\Models;

use CodeIgniter\Model;

class BookFairModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'book_tbl';
    protected $primaryKey = 'book_id';
    protected $allowedFields = ['book_title', 'author_id', 'paper_back_isbn'];

    // 1. Base books with authors + priority
    public function getBaseBooks()
    {
        return $this->db->table('book_fair_authors_list bf')
            ->select('a.author_name, a.author_id, b.book_id, b.book_title, bf.priority_code')
            ->join('author_tbl a', 'a.author_id = bf.author_id')
            ->join('book_tbl b', 'b.author_name = a.author_id')
            ->orderBy('bf.priority_code', 'ASC')
            ->orderBy('a.author_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    // 2. Allocated books
    public function getAllocatedBooks()
    {
        return $this->db->table('bookfair_allocated_books')
            ->select('book_id, COUNT(DISTINCT bookfair_name) AS no_of_bookfairs, SUM(quantity) AS allocated_qty')
            ->groupBy('book_id')
            ->get()
            ->getResultArray();
    }

    // 3. Sales qty
    public function getSalesQty()
    {
        return $this->db->table('book_fair_item_wise_sale s')
            ->select('bt.book_id, SUM(s.quantity) AS sales_qty')
            ->join('book_tbl bt', "REPLACE(bt.paper_back_isbn, '-', '') = s.isbn")
            ->groupBy('bt.book_id')
            ->get()
            ->getResultArray();
    }

    // 4. Stock in hand
    public function getStockQty()
    {
        return $this->db->table('paperback_stock')
            ->select('book_id, stock_in_hand')
            ->get()
            ->getResultArray();
    }

    // 5. Merge all data in PHP
    public function getFinalBookFairData()
    {
        $base  = $this->getBaseBooks();
        $alloc = array_column($this->getAllocatedBooks(), null, 'book_id');
        $sales = array_column($this->getSalesQty(), null, 'book_id');
        $stock = array_column($this->getStockQty(), null, 'book_id');

        foreach ($base as &$b) {
            $id = $b['book_id'];

            $b['no_of_bookfairs'] = $alloc[$id]['no_of_bookfairs'] ?? 0;
            $b['allocated_qty']   = $alloc[$id]['allocated_qty'] ?? 0;
            $b['sales_qty']       = $sales[$id]['sales_qty'] ?? 0;
            $b['stock_in_hand']   = $stock[$id]['stock_in_hand'] ?? 0;
        }

        return $base;
    }

    // 6. Filter by priority
    public function getPriorityBooks($priority)
    {
        $all = $this->getFinalBookFairData();

        if ($priority === 'high')
            return array_filter($all, fn($x) => $x['priority_code'] == 1);

        if ($priority === 'highmedium')
            return array_filter($all, fn($x) => in_array($x['priority_code'], [1,2]));

        return $all;
    }
    
 public function getBooksWithStats($priority = 'all')
{
    $priorityWhere = "";

    if ($priority === 'high') {
        $priorityWhere = " AND bfal.priority_code = 1";
    } 
    elseif ($priority === 'highmedium') {
        $priorityWhere = " AND bfal.priority_code IN (1,2)";
    }

    $sql = "
        SELECT 
            a.author_name,
            a.author_id,
            b.book_id,
            b.book_title,
            bfal.priority_code,

            IFNULL(bfb.no_of_bookfairs, 0) AS no_of_bookfairs,
            IFNULL(bfb.allocated_qty, 0) AS allocated_qty,
            IFNULL(bfs.sales_qty, 0) AS sales_qty,
            IFNULL(ps.stock_in_hand, 0) AS stock_in_hand

        FROM book_fair_authors_list bfal
        JOIN author_tbl a 
            ON a.author_id = bfal.author_id

        JOIN book_tbl b 
            ON (b.author_name = a.author_id OR b.author_name = a.author_id)

        LEFT JOIN (
            SELECT 
                book_id, 
                COUNT(DISTINCT bookfair_name) AS no_of_bookfairs,
                SUM(quantity) AS allocated_qty
            FROM bookfair_allocated_books
            GROUP BY book_id
        ) bfb ON bfb.book_id = b.book_id

        LEFT JOIN (
            SELECT 
                bt.book_id,
                SUM(s.quantity) AS sales_qty
            FROM book_fair_item_wise_sale s
            JOIN book_tbl bt 
                ON REPLACE(bt.paper_back_isbn, '-', '') = s.isbn
            GROUP BY bt.book_id
        ) bfs ON bfs.book_id = b.book_id

        LEFT JOIN (
            SELECT 
                book_id,
                stock_in_hand
            FROM paperback_stock
        ) ps ON ps.book_id = b.book_id

        WHERE 1=1
        $priorityWhere

        ORDER BY bfal.priority_code ASC, a.author_name ASC
    ";

    return $this->db->query($sql)->getResultArray();
}
 // BOOK DETAILS + AUTHOR NAME
    public function getBookDetails($book_id)
    {
        return $this->db->table('book_tbl')
            ->select('book_tbl.*, author_tbl.author_name')
            ->join('author_tbl', 'author_tbl.author_id = book_tbl.author_name', 'left')
            ->where('book_tbl.book_id', $book_id)
            ->get()
            ->getRowArray();
    }

    // ALLOCATION DETAILS
    public function getAllocationDetails($book_id)
    {
        return $this->db->table('bookfair_allocated_books')
            ->select('bookfair_name, quantity, created_date')
            ->where('book_id', $book_id)
            ->orderBy('created_date', 'DESC')
            ->get()
            ->getResultArray();
    }

    // SALES DETAILS
   public function getSalesDetails($book_id)
{
    // 1. Get ISBN from book_tbl
    $isbn = $this->db->table('book_tbl')
        ->select('paper_back_isbn')
        ->where('book_id', $book_id)
        ->get()
        ->getRow('paper_back_isbn');

    if (!$isbn) {
        return [];
    }

    // Remove hyphens from ISBN to match book_fair_item_wise_sale
    $cleanIsbn = str_replace('-', '', $isbn);

    // 2. Get sales details from book_fair_item_wise_sale
    return $this->db->table('book_fair_item_wise_sale')
        ->select('book_fair_name, book_fair_start_date, quantity')
        ->where('REPLACE(isbn, "-", "")', $cleanIsbn)   // match clean format
        ->orderBy('book_fair_start_date', 'DESC')
        ->get()
        ->getResultArray();
}


    }




