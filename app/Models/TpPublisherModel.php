<?php

namespace App\Models;

use CodeIgniter\Model;

class TpPublisherModel extends Model
{
    protected $table         = 'tp_publisher_details';
    protected $primaryKey    = 'publisher_id';
    protected $allowedFields = [
        'publisher_name', 'contact_person', 'address', 'mobile', 'email_id',
        'publisher_logo', 'status', 'created_at',
        'bank_acc_no', 'bank_acc_name', 'bank_acc_ifsc', 'bank_acc_type'
    ];

    protected $useTimestamps = false;

    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        helper('date');
    }

    public function countData($publisher_id = null): array
{
    $data = [];

    // Publishers count
    $publisherQuery = $this->db->table('tp_publisher_details');
    if ($publisher_id && $publisher_id !== 'all') {
        $publisherQuery->where('publisher_id', $publisher_id); // Use publisher_id instead of id
    }
    $data['active_publisher_cnt'] = $publisherQuery->where('status', 1)->countAllResults(false);
    
    $publisherQuery = $this->db->table('tp_publisher_details');
    if ($publisher_id && $publisher_id !== 'all') {
        $publisherQuery->where('publisher_id', $publisher_id); // Use publisher_id instead of id
    }
    $data['inactive_publisher_cnt'] = $publisherQuery->where('status', 0)->countAllResults(false);

    // Authors count
    $authorTbl = $this->db->table('tp_publisher_author_details');
    if ($publisher_id && $publisher_id !== 'all') {
        $authorTbl->where('publisher_id', $publisher_id);
    }
    $data['active_author_cnt'] = $authorTbl->where('status', 1)->countAllResults(false);

    $authorTbl = $this->db->table('tp_publisher_author_details');
    if ($publisher_id && $publisher_id !== 'all') {
        $authorTbl->where('publisher_id', $publisher_id);
    }
    $data['inactive_author_cnt'] = $authorTbl->where('status', 0)->countAllResults(false);

    // Books count
    $bookTbl = $this->db->table('tp_publisher_bookdetails');
    if ($publisher_id && $publisher_id !== 'all') {
        $bookTbl->where('publisher_id', $publisher_id);
    }
    $data['active_book_cnt'] = $bookTbl->where('status', 1)->countAllResults(false);

    $bookTbl = $this->db->table('tp_publisher_bookdetails');
    if ($publisher_id && $publisher_id !== 'all') {
        $bookTbl->where('publisher_id', $publisher_id);
    }
    $data['inactive_book_cnt'] = $bookTbl->where('status', 0)->countAllResults(false);

    $bookTbl = $this->db->table('tp_publisher_bookdetails');
    if ($publisher_id && $publisher_id !== 'all') {
        $bookTbl->where('publisher_id', $publisher_id);
    }
    $data['pending_book_cnt'] = $bookTbl->where('status', 2)->countAllResults(false);

    // Stock counts - JOIN with books table for publisher filtering
    $stockTbl = $this->db->table('tp_publisher_book_stock as s')
        ->join('tp_publisher_bookdetails as b', 'b.book_id = s.book_id');
    
    if ($publisher_id && $publisher_id !== 'all') {
        $stockTbl->where('b.publisher_id', $publisher_id);
    }
    $result = $stockTbl->selectSum('s.book_quantity')->get()->getRow();
    $data['tot_stock_count'] = $result->book_quantity ?? 0;

    $stockTbl = $this->db->table('tp_publisher_book_stock as s')
        ->join('tp_publisher_bookdetails as b', 'b.book_id = s.book_id');
    
    if ($publisher_id && $publisher_id !== 'all') {
        $stockTbl->where('b.publisher_id', $publisher_id);
    }
    $result = $stockTbl->selectSum('s.stock_in_hand')->get()->getRow();
    $data['stock_in_hand'] = $result->stock_in_hand ?? 0;

    // Stock Ledger
    $ledgerTbl = $this->db->table('tp_publisher_book_stock_ledger as l')
        ->join('tp_publisher_bookdetails as b', 'b.book_id = l.book_id');
    
    if ($publisher_id && $publisher_id !== 'all') {
        $ledgerTbl->where('b.publisher_id', $publisher_id);
    }
    
    $result = $ledgerTbl->selectSum('l.stock_out')->get()->getRow();
    $data['stock_out'] = $result->stock_out ?? 0;

    // Sales - JOIN with books table for publisher filtering
    $salesTbl = $this->db->table('tp_publisher_sales as s')
        ->join('tp_publisher_bookdetails as b', 'b.book_id = s.book_id');
    
    if ($publisher_id && $publisher_id !== 'all') {
        $salesTbl->where('b.publisher_id', $publisher_id);
    }
    
    $result = $salesTbl->select('COUNT(DISTINCT s.create_date) as unique_sales')->get()->getRow();
    $data['total_sales'] = $result->unique_sales ?? 0;

    // Sales by channel
    $pustakaQuery = $this->db->table('tp_publisher_sales as s')
        ->join('tp_publisher_bookdetails as b', 'b.book_id = s.book_id')
        ->select("COUNT(DISTINCT s.create_date) as total_qty", false)
        ->where('s.channel_type', 'PUS');
    if ($publisher_id && $publisher_id !== 'all') {
        $pustakaQuery->where('b.publisher_id', $publisher_id);
    }
    $data['qty_pustaka'] = $pustakaQuery->get()->getRow()->total_qty ?? 0;

    $amazonQuery = $this->db->table('tp_publisher_sales as s')
        ->join('tp_publisher_bookdetails as b', 'b.book_id = s.book_id')
        ->select("COUNT(DISTINCT s.create_date) as total_qty", false)
        ->where('s.channel_type', 'AMZ');
    if ($publisher_id && $publisher_id !== 'all') {
        $amazonQuery->where('b.publisher_id', $publisher_id);
    }
    $data['qty_amazon'] = $amazonQuery->get()->getRow()->total_qty ?? 0;

    $bookfairQuery = $this->db->table('tp_publisher_sales as s')
        ->join('tp_publisher_bookdetails as b', 'b.book_id = s.book_id')
        ->select("COUNT(DISTINCT s.create_date) as total_qty", false)
        ->like('s.channel_type', 'BFR');
    if ($publisher_id && $publisher_id !== 'all') {
        $bookfairQuery->where('b.publisher_id', $publisher_id);
    }
    $data['qty_bookfair'] = $bookfairQuery->get()->getRow()->total_qty ?? 0;

    $otherQuery = $this->db->table('tp_publisher_sales as s')
        ->join('tp_publisher_bookdetails as b', 'b.book_id = s.book_id')
        ->select("COUNT(DISTINCT s.create_date) as total_qty", false)
        ->where('s.channel_type', 'OTH');
    if ($publisher_id && $publisher_id !== 'all') {
        $otherQuery->where('b.publisher_id', $publisher_id);
    }
    $data['qty_other'] = $otherQuery->get()->getRow()->total_qty ?? 0;

    // Orders - JOIN with orders table for publisher filtering
    $orderTbl = $this->db->table('tp_publisher_order_details as od')
        ->join('tp_publisher_order as o', 'o.order_id = od.order_id');
    
    if ($publisher_id && $publisher_id !== 'all') {
        $orderTbl->where('o.publisher_id', $publisher_id);
    }

    $data['total_orders_cnt'] = $orderTbl->select('COUNT(DISTINCT od.order_id) as total')->get()->getRow()->total ?? 0;

    // Shipped orders
    $shippedQuery = $this->db->table('tp_publisher_order_details as od')
        ->join('tp_publisher_order as o', 'o.order_id = od.order_id');
    
    if ($publisher_id && $publisher_id !== 'all') {
        $shippedQuery->where('o.publisher_id', $publisher_id);
    }
    $data['shipped_orders_cnt'] = $shippedQuery->select('COUNT(DISTINCT od.order_id) as shipped')
        ->where('od.ship_status', 1)
        ->get()
        ->getRow()
        ->shipped ?? 0;

    // Pending orders
    $pendingQuery = $this->db->table('tp_publisher_order_details as od')
        ->join('tp_publisher_order as o', 'o.order_id = od.order_id');
    
    if ($publisher_id && $publisher_id !== 'all') {
        $pendingQuery->where('o.publisher_id', $publisher_id);
    }
    $data['pending_orders_cnt'] = $pendingQuery->select('COUNT(DISTINCT od.order_id) as pending')
        ->where('od.ship_status', 0)
        ->get()
        ->getRow()
        ->pending ?? 0;

    return $data;
}
     public function tpPublisherDetails()
{
    // Get the selected publisher from session
    $selected_publisher_id = session()->get('selected_publisher_id');
    
    // If a specific publisher is selected in dashboard, show only that publisher
    if ($selected_publisher_id && $selected_publisher_id !== 'all') {
        $publishers = $this->where('publisher_id', $selected_publisher_id)->findAll();
    } else {
        // Otherwise show all publishers
        $publishers = $this->findAll();
    }

    $result = [
        'active' => [],
        'inactive' => []
    ];

    foreach ($publishers as $publisher) {
        if ($publisher['status'] == 1) {
            $result['active'][] = $publisher;
        } else {
            $result['inactive'][] = $publisher;
        }
    }

    return $result;
}
    public function inactivePublishers($publisherId)
    {
        $db = \Config\Database::connect();
        $builder = $db->query("
            UPDATE tp_publisher_details pd
            LEFT JOIN tp_publisher_author_details pad ON pd.publisher_id = pad.publisher_id
            LEFT JOIN tp_publisher_bookdetails pabd ON pad.publisher_id = pabd.publisher_id
            SET pd.status = 0, pad.status = 0, pabd.status = 0
            WHERE pd.publisher_id = ?
        ", [$publisherId]);

        return $builder ? 1 : 0;
    }

    public function activePublishers($publisherId)
    {
        $db = \Config\Database::connect();
        $builder = $db->query("
            UPDATE tp_publisher_details pd
            LEFT JOIN tp_publisher_author_details pad ON pd.publisher_id = pad.publisher_id
            LEFT JOIN tp_publisher_bookdetails pabd ON pad.publisher_id = pabd.publisher_id
            SET pd.status = 1, pad.status = 1, pabd.status = 1
            WHERE pd.publisher_id = ?
        ", [$publisherId]);

        return $builder ? 1 : 0;
    }
   public function getPublisherOrders($publisher_id = null, $shipStatus = null, $orderStatus = null)
{
    $builder = $this->db->table('tp_publisher_order');
    $builder->select("
        tp_publisher_order.*,
        tp_publisher_order_details.ship_status,
        tp_publisher_order_details.book_id,
        tp_publisher_author_details.author_name,
        COUNT(tp_publisher_order_details.book_id) AS total_books,
        SUM(tp_publisher_order_details.quantity) AS total_qty,
    ");
    $builder->join(
        'tp_publisher_order_details',
        'tp_publisher_order.order_id = tp_publisher_order_details.order_id'
    );
    $builder->join(
        'tp_publisher_author_details',
        'tp_publisher_order_details.author_id = tp_publisher_author_details.author_id'
    );

    // Add publisher filter
    if ($publisher_id && $publisher_id !== 'all') {
        $builder->where('tp_publisher_order.publisher_id', $publisher_id);
    }

    if ($shipStatus !== null) {
        $builder->where('tp_publisher_order_details.ship_status', $shipStatus);
    }
    if ($orderStatus !== null) {
        $builder->where('tp_publisher_order.status', $orderStatus);
    }

    $builder->groupBy('tp_publisher_order.order_id');
    $builder->orderBy('tp_publisher_order.ship_date', 'DESC');

    $orders = $builder->get()->getResultArray();

    foreach ($orders as &$order) {
        if (isset($order['stock_in_hand']) && $order['stock_in_hand'] > 0) {
            $order['book_status'] = 'In Stock';
        } else {
            $order['book_status'] = 'Out of Stock';
        }

        // Show print button if out of stock and printing is allowed
        $order['show_print_button'] = (
            $order['book_status'] === 'Out of Stock' && 
            isset($order['initiate_to_print']) && 
            $order['initiate_to_print'] == 1
        );
    }

    return $orders;
}
public function tpOrderFullDetails($order_id)
    {
        // Main order info
        $order = $this->db->table('tp_publisher_order_details')
            ->select('
                tp_publisher_order_details.*,
                tp_publisher_author_details.author_name,
                tp_publisher_bookdetails.book_title,
                tp_publisher_bookdetails.sku_no,
                tp_publisher_bookdetails.mrp as price,
                tp_publisher_order.*
            ')
            ->join('tp_publisher_author_details', 'tp_publisher_author_details.author_id = tp_publisher_order_details.author_id', 'left')
            ->join('tp_publisher_bookdetails', 'tp_publisher_bookdetails.book_id = tp_publisher_order_details.book_id')
            ->join('tp_publisher_order', 'tp_publisher_order.order_id = tp_publisher_order_details.order_id')
            ->where('tp_publisher_order_details.order_id', $order_id)
            ->get()
            ->getResultArray();

        // Books info
        $books = $this->db->table('tp_publisher_order_details')
            ->select('
                tp_publisher_order_details.*,
                tp_publisher_author_details.author_name,
                tp_publisher_bookdetails.book_title,
                tp_publisher_bookdetails.sku_no,
                tp_publisher_bookdetails.no_of_pages,
                tp_publisher_bookdetails.isbn,
                tp_publisher_order.*,
                tp_publisher_bookdetails.mrp
            ')
            ->join('tp_publisher_author_details', 'tp_publisher_author_details.author_id = tp_publisher_order_details.author_id', 'left')
            ->join('tp_publisher_bookdetails', 'tp_publisher_bookdetails.book_id = tp_publisher_order_details.book_id')
            ->join('tp_publisher_order', 'tp_publisher_order.order_id = tp_publisher_order_details.order_id')
            ->where('tp_publisher_order_details.order_id', $order_id)
            ->get()
            ->getResultArray();

        return [
            'order' => !empty($order) ? $order[0] : [],
            'books' => $books
        ];
    }
public function tpPublisherOrderPayment($publisher_id = null)
{
    $builder = $this->db->table('tp_publisher_order o');
    $builder->select('
        o.order_id,
        o.order_date,
        o.ship_date,
        o.status,
        o.payment_status,
        o.sub_total,
        o.courier_charges,
        o.royalty,
        o.payment_date,
        p.publisher_name,
        MAX(od.ship_status) AS ship_status
    ');
    $builder->join('tp_publisher_order_details od', 'od.order_id = o.order_id', 'left');
    $builder->join('tp_publisher_details p', 'p.publisher_id = o.publisher_id', 'left');

    // Add publisher filter
    if ($publisher_id && $publisher_id !== 'all') {
        $builder->where('o.publisher_id', $publisher_id);
    }

    $builder->groupBy('
        o.order_id,
        o.order_date,
        o.ship_date,
        o.status,
        o.payment_status,
        o.sub_total,
        o.courier_charges,
        o.royalty,
        o.payment_date,
        p.publisher_name
    ');

    $builder->orderBy('o.order_id', 'DESC');

    return $builder->get()->getResultArray();
}

public function tpPublisherAdd()
    {
        $request = \Config\Services::request();

        $data = [
            'publisher_name'   => $request->getPost('publisher_name'),
            'contact_person'   => $request->getPost('contact_person'),
            'address'          => $request->getPost('address'),
            'mobile'           => $request->getPost('mobile'),
            'email_id'         => $request->getPost('email_id'),
            'publisher_logo'   => $request->getPost('publisher_logo'),
            'status'           => 1,
            'created_at'       => date('Y-m-d H:i:s'),
        ];

        if (
            $request->getPost('bank_acc_no') &&
            $request->getPost('bank_acc_name') &&
            $request->getPost('bank_acc_ifsc') &&
            $request->getPost('bank_acc_type')
        ) {
            $data['bank_acc_no']   = $request->getPost('bank_acc_no');
            $data['bank_acc_name'] = $request->getPost('bank_acc_name');
            $data['bank_acc_ifsc'] = $request->getPost('bank_acc_ifsc');
            $data['bank_acc_type'] = $request->getPost('bank_acc_type');
        }

        $this->insert($data);
        return $this->getInsertID(); // Return inserted ID
    }
    public function tpAuthorDetails($publisher_id = null): array
{
    // Get the selected publisher from session
    $selected_publisher_id = session()->get('selected_publisher_id');
    
    $sql = "SELECT pad.*, pd.publisher_name, pd.status AS publisher_status 
            FROM tp_publisher_author_details pad
            JOIN tp_publisher_details pd ON pd.publisher_id = pad.publisher_id";
    
    // Add WHERE clause if specific publisher is selected
    if ($selected_publisher_id && $selected_publisher_id !== 'all') {
        $sql .= " WHERE pad.publisher_id = ?";
        $query = $this->db->query($sql, [$selected_publisher_id]);
    } else {
        $query = $this->db->query($sql);
    }
    
    $authors = $query->getResultArray();

    $result = [
        'active' => [],
        'inactive' => []
    ];

    foreach ($authors as $author) {
        $record = [
            'publisher_name'   => $author['publisher_name'],
            'author_id'        => $author['author_id'],
            'author_name'      => $author['author_name'],
            'mobile'           => $author['mobile'],
            'email_id'         => $author['email_id'] ?? '',
            'author_image'     => $author['author_image'] ?? '',
            'author_status'    => $author['status'],
            'publisher_status' => $author['publisher_status'],
        ];

        // Author is active only if both author and publisher are active
        if ((int)$author['status'] === 1 && (int)$author['publisher_status'] === 1) {
            $result['active'][] = $record;
        } else {
            $result['inactive'][] = $record;
        }
    }

    return $result;
}
public function tpPublisherEdit($publisher_id)
    {
        return $this->where('publisher_id', $publisher_id)->first();
    }
   public function tpPublisherEditPost($publisherId, $request)
    {
        $data = [
            'publisher_name'   => $request->getPost('publisher_name'),
            'contact_person'   => $request->getPost('contact_person'),
            'address'          => $request->getPost('address'),
            'mobile'           => $request->getPost('mobile'),
            'email_id'         => $request->getPost('email_id'),
            'bank_acc_no'      => $request->getPost('bank_acc_no'),
            'bank_acc_name'    => $request->getPost('bank_acc_name'),
            'bank_acc_ifsc'    => $request->getPost('bank_acc_ifsc'),
            'bank_acc_type'    => $request->getPost('bank_acc_type'),
            'publisher_logo'   => $request->getPost('publisher_logo'),
            'status'           => 1
        ];

        return $this->update($publisherId, $data);
    }
 public function getTpAuthor()
    {
        return $this->orderBy('publisher_id', 'ASC')->findAll();
    }
 public function tpAuthorsAdd(array $postData): bool
    {
        $authorData = [
            'publisher_id'       => $postData['publisher_id'] ?? null,
            'author_id'          => $postData['author_id'] ?? null,
            'author_name'        => trim($postData['author_name'] ?? ''),
            'mobile'             => trim($postData['mobile'] ?? ''),
            'email_id'           => trim($postData['email_id'] ?? ''),
            'author_discription' => trim($postData['author_discription'] ?? ''),
            'author_image'       => trim($postData['author_image'] ?? ''),
            'status'             => 1,
            'created_at'         => date('Y-m-d H:i:s'),
        ];

        $builder = $this->db->table('tp_publisher_author_details');
        $builder->insert($authorData);
        return $this->db->affectedRows() > 0;
    }

public function activateAuthorWithBooks($authorId)
{
    $db = \Config\Database::connect();
    $db->transStart();

    // 1. Update author status to active
    $db->table('tp_publisher_author_details')
        ->where('author_id', $authorId)
        ->update(['status' => 1]);

    // 2. Update all their books to active
    $db->table('tp_publisher_bookdetails')
        ->where('author_id', $authorId)
        ->update(['status' => 1]);

    $db->transComplete();
    return $db->transStatus();
}

public function deactivateAuthorWithBooks($authorId)
{
    $db = \Config\Database::connect();
    $db->transStart();

    // 1. Update author status to inactive
    $db->table('tp_publisher_author_details')
        ->where('author_id', $authorId)
        ->update(['status' => 0]);

    // 2. Update all their books to inactive
    $db->table('tp_publisher_bookdetails')
        ->where('author_id', $authorId)
        ->update(['status' => 0]);

    $db->transComplete();
    return $db->transStatus();
}

function tpAuthorView($author_id)
{
    // Get author details with publisher name
    $author_sql = "SELECT pad.*, pd.publisher_name 
                   FROM tp_publisher_author_details pad 
                   JOIN tp_publisher_details pd ON pad.publisher_id = pd.publisher_id 
                   WHERE pad.author_id = ?";
    $author_query = $this->db->query($author_sql, [$author_id]);
    $author_details = $author_query->getRowArray();

    // Get book count using query builder properly
    $book_count_query = $this->db
        ->table('tp_publisher_bookdetails')
        ->select('COUNT(DISTINCT book_id) as book_count')
        ->where('author_id', $author_id)
        ->get();
    $book_count = $book_count_query->getRowArray()['book_count'];

    // Get publishers
    $publisher_sql = "SELECT pd.* 
                      FROM tp_publisher_details pd 
                      JOIN tp_publisher_author_details pad ON pd.publisher_id = pad.publisher_id 
                      WHERE pad.author_id = ?";
    $publisher_query = $this->db->query($publisher_sql, [$author_id]);
    $publishers = $publisher_query->getResultArray();

    // Get books
    $book_sql = "SELECT pab.*
                 FROM tp_publisher_bookdetails pab 
                 WHERE pab.author_id = ?";
    $books_query = $this->db->query($book_sql, [$author_id]);
    $books = $books_query->getResultArray();

    // Combine all data
    return [
        'author_details' => $author_details,
        'book_count' => $book_count,
        'publishers' => $publishers,
        'books' => $books
    ];
}
public function getAuthorList(): array
{
    // Get the selected publisher from session
    $selected_publisher_id = session()->get('selected_publisher_id');
    
    $builder = $this->db->table('tp_publisher_author_details');
    
    // Add publisher filter
    if ($selected_publisher_id && $selected_publisher_id !== 'all') {
        $builder->where('publisher_id', $selected_publisher_id);
    }
    
    return $builder->orderBy('author_name', 'ASC')
        ->get()
        ->getResultArray();
}
 public function get_common_data($type)
    {
        switch ($type) {
            case 'types':
                $table = 'book_types';
                $order_by = 'type_name';
                break;
            case 'languages':
                $table = 'language_tbl';
                $order_by = 'language_name';
                break;
            case 'genres':
                $table = 'genre_details_tbl';
                $order_by = 'genre_name';
                break;
            case 'authors':
                $table = 'tp_publisher_author_details';
                $order_by = 'author_name';
                break;
            case 'publishers':
                $table = 'tp_publisher_details';
                $order_by = 'publisher_name';
                break;
            default:
                return [];
        }

       return $this->db->table($table)
                ->orderBy($order_by)
                ->get()
                ->getResult();
    }
    public function getAuthorAndPublishers(int $author_id): array
{
    $author = $this->db->table('tp_publisher_author_details')
                       ->where('author_id', $author_id)
                       ->get()
                       ->getRowArray();

    $publishers = $this->db->table('tp_publisher_details')
                           ->orderBy('publisher_id')
                           ->get()
                           ->getResultArray();

    return [
        'author'     => $author,
        'publishers' => $publishers,
    ];
}

public function updateAuthor($author_id, $data)
{
    $this->db->table('tp_publisher_author_details')
             ->where('author_id', $author_id)
             ->update($data);

    return $this->db->affectedRows() > 0;
}
    public function tpBookAdd(array $postData): bool
    {
        $bookData = [
            'publisher_id'         => $postData['publisher_id'] ?? null,
            'author_id'            => $postData['author_id'] ?? null,
            'book_id'              => $postData['book_id'] ?? null,
            'sku_no'               => trim($postData['sku_no'] ?? ''),
            'book_title'           => trim($postData['book_title'] ?? ''),
            'book_regional_title'  => trim($postData['book_regional_title'] ?? ''),
            'book_url'             => trim($postData['book_url'] ?? ''),
            'initiate_to_print'    => !empty($postData['initiate_to_print']) ? 1 : 0,
            'book_genre'           => trim($postData['book_genre'] ?? ''),
            'type_name'            => trim($postData['type_name'] ?? ''),
            'language'             => trim($postData['language'] ?? ''),
            'book_description'     => trim($postData['book_description'] ?? ''),
            'no_of_pages'          => $postData['no_of_pages'] ?? null,
            'mrp'                  => $postData['mrp'] ?? null,
            'pustaka_price'        => $postData['pustaka_price'] ?? null,
            'isbn'                 => trim($postData['isbn'] ?? ''),
            'discount'             => $postData['discount'] ?? null,
            'status'               => 1,
            'created_at'           => date('Y-m-d H:i:s'),
        ];

        $builder = $this->db->table('tp_publisher_bookdetails');
        $builder->insert($bookData);

        return $this->db->affectedRows() > 0;
    }
    public function updateBookStatus(int $book_id, int $status): bool
{
    return $this->db->table('tp_publisher_bookdetails')
        ->where('book_id', $book_id)
        ->update(['status' => $status]);
}
   public function getStockDetails()
{
    $db = \Config\Database::connect();

    $builder = $db->table('tp_publisher_book_stock s');
        $builder->select('
            s.stock_in_hand,
            s.book_id, 
            pd.publisher_name,
            bd.sku_no,
            ad.author_name,
            bd.book_title
        ');
        $builder->join('tp_publisher_book_stock_ledger l', 'l.book_id = s.book_id AND l.author_id = s.author_id', 'left');
        $builder->join('tp_publisher_details pd', 'pd.publisher_id = l.publisher_id', 'left');
        $builder->join('tp_publisher_author_details ad', 'ad.author_id = s.author_id', 'left');
        $builder->join('tp_publisher_bookdetails bd', 'bd.book_id = s.book_id', 'left');
        $builder->groupBy('s.book_id');


    $query = $builder->get();
    return $query->getResult();

    }
public function TpbookAddStock($data)
{
    $db = \Config\Database::connect();

    $bookId       = $data['book_id'];
    $authorId     = $data['author_id'];
    $bookQuantity = (int) $data['book_quantity'];
    $userDesc     = trim($data['description']); // user-entered description

    // Check existing stock
    $stockData = $db->table('tp_publisher_book_stock')
                    ->where('book_id', $bookId)
                    ->get()
                    ->getRowArray();

    if ($stockData) {

        // Default description for update
        $description = $userDesc !== "" ? $userDesc : "Stock added to Inventory";
        $channelType = "STK";

        // Update stock
        $db->table('tp_publisher_book_stock')
            ->where('book_id', $bookId)
            ->set('book_quantity', "book_quantity + {$bookQuantity}", false)
            ->set('stock_in_hand', "stock_in_hand + {$bookQuantity}", false)
            ->update();

    } else {

        // Default description for new stock
        $description = $userDesc !== "" ? $userDesc : "Opening Stock";
        $channelType = "OST";

        // Insert stock
        $bookData = [
            'author_id'        => $authorId,
            'book_id'          => $bookId,
            'book_quantity'    => $bookQuantity,
            'stock_in_hand'    => $bookQuantity,
            'last_update_date' => date("Y-m-d H:i:s")
        ];
        $db->table('tp_publisher_book_stock')->insert($bookData);
    }

    // Fetch publisher_id for ledger
    $query = $db->query("
        SELECT publisher_id, author_id, book_id
        FROM tp_publisher_bookdetails
        WHERE book_id = ?", [$bookId]);

    $stock = $query->getRowArray();

    if (!empty($stock)) {

        // Insert ledger entry
        $ledgerData = [
            'publisher_id'     => $stock['publisher_id'],
            'author_id'        => $stock['author_id'],
            'book_id'          => $stock['book_id'],
            'description'      => $description,
            'channel_type'     => $channelType,
            'stock_in'         => $bookQuantity,
            'transaction_date' => date('Y-m-d H:i:s'),
        ];

        $db->table('tp_publisher_book_stock_ledger')->insert($ledgerData);
    }

    return $db->affectedRows() > 0
        ? ['status' => 1]
        : ['status' => 0];
}

public function getBooksByAuthor($author_id)
{
    return $this->db->table('tp_publisher_bookdetails')
        ->select('book_id, book_title')
        ->where('author_id', $author_id)
        ->orderBy('book_title', 'ASC')
        ->get()
        ->getResult();
}


    public function getBooksAndAuthors()
    {
        // Books
        $bookBuilder = $this->db->table('tp_publisher_bookdetails');
        $bookBuilder->orderBy('book_title', 'ASC');
        $books = $bookBuilder->get()->getResult();

        // Authors with publishers
        $authorBuilder = $this->db->table('tp_publisher_author_details');
        $authorBuilder->select('tp_publisher_author_details.*, tp_publisher_details.publisher_name');
        $authorBuilder->join('tp_publisher_details', 'tp_publisher_author_details.publisher_id = tp_publisher_details.publisher_id');
        $authorBuilder->orderBy('tp_publisher_author_details.author_name', 'ASC');
        $authorBuilder->orderBy('tp_publisher_details.publisher_name', 'ASC');
        $authors = $authorBuilder->get()->getResult();

        return [
            'books' => $books,
            'authors' => $authors
        ];
    }
    public function markShipped(array $data)
{
    $order_id = $data['order_id'] ?? '';
    $courier_charges = floatval($data['courier_charges'] ?? 0);

    if (!$order_id) {
        log_message('error', 'Missing order_id.');
        return false;
    }

    // Get all books & quantities for this order
    $books = $this->db->table('tp_publisher_order_details')
        ->select('book_id, quantity')
        ->where('order_id', $order_id)
        ->get()
        ->getResultArray();

    if (empty($books)) return false;

    $this->db->transStart();

    // 1. Decrease stock for each book & add ledger entries
    foreach ($books as $row) {
        $book_id = (int)$row['book_id'];
        $qty     = (int)$row['quantity'];

        // Decrease stock
        $this->db->table('tp_publisher_book_stock')
            ->where('book_id', $book_id)
            ->set('stock_in_hand', "stock_in_hand - {$qty}", false)
            ->set('book_quantity', "book_quantity - {$qty}", false)
            ->update();

        // Get author & publisher for ledger
        $stock = $this->db->table('tp_publisher_bookdetails')
            ->select('book_id, author_id, publisher_id')
            ->where('book_id', $book_id)
            ->get()
            ->getRowArray();

        if ($stock) {
            $ledgerData = [
                'book_id'          => $stock['book_id'],
                'order_id'         => $order_id,
                'author_id'        => $stock['author_id'],
                'publisher_id'     => $stock['publisher_id'],
                'description'      => 'Publisher Orders',
                'channel_type'     => 'PUB',
                'stock_out'        => $qty,
                'transaction_date' => date('Y-m-d H:i:s'),
            ];

            $this->db->table('tp_publisher_book_stock_ledger')->insert($ledgerData);
        }
    }

    // 2. Update order details - mark shipped
    $this->db->table('tp_publisher_order_details')
        ->where('order_id', $order_id)
        ->update([
            'ship_date'   => date('Y-m-d H:i:s'),
            'ship_status' => 1
        ]);

    // 3. Update main order totals & courier charges
    $order = $this->db->table('tp_publisher_order')
        ->select('sub_total, royalty')
        ->where('order_id', $order_id)
        ->get()
        ->getRowArray();

    $sub_total = floatval($order['sub_total'] ?? 0);
    $royalty   = floatval($order['royalty'] ?? 0);
    $net_total = $sub_total + $royalty + $courier_charges;

    $this->db->table('tp_publisher_order')
        ->where('order_id', $order_id)
        ->update([
            'status'          => 1,
            'courier_charges' => $courier_charges,
            'net_total'       => $net_total,
        ]);

    $this->db->transComplete();

    return $this->db->transStatus();
}

public function markCancel(array $data)
{
    $order_id = $data['order_id'] ?? '';
    $book_id = (int)($data['book_id'] ?? 0);

    if (!$order_id || !$book_id) {
        log_message('error', 'Missing order_id or book_id for cancellation.');
        return false;
    }
    $this->db->transStart();

    // Mark the overall order as cancelled (status = 2)
    $this->db->table('tp_publisher_order')
        ->where(['order_id' => $order_id])
        ->update(['status' => 2]);

    // Update specific book's ship_status to 2 (Cancelled), with date
    $this->db->table('tp_publisher_order_details')
        ->where(['order_id' => $order_id, 'book_id' => $book_id])
        ->update([
            'ship_date'   => date('Y-m-d H:i:s'),
            'ship_status' => 2
        ]);

    $this->db->transComplete();

    if ($this->db->transStatus() === false) {
        log_message('error', "Transaction failed during cancellation of order_id=$order_id, book_id=$book_id");
        return false;
    }

    log_message('debug', "Order $order_id with book_id=$book_id marked as cancelled successfully.");
    return true;
}
    public function markReturn(array $data)
    {
        $order_id = $data['order_id'] ?? '';
        $book_id = (int)($data['book_id'] ?? 0);

        if (!$order_id || !$book_id) return false;

        $qtyRow = $this->db->table('tp_publisher_order_details')
            ->select('quantity')
            ->where(['order_id' => $order_id, 'book_id' => $book_id])
            ->get()
            ->getRowArray();

        if (!$qtyRow) return false;

        $qty = (int)$qtyRow['quantity'];

        $this->db->transStart();

        $this->db->table('tp_publisher_book_stock')
            ->where('book_id', $book_id)
            // ->set('book_quantity', "book_quantity + $qty", false)
            ->set('stock_in_hand', "stock_in_hand + $qty", false)
            ->update();

        $this->db->table('tp_publisher_order')
            ->where(['order_id' => $order_id])
            ->update(['status' => 3]);

        $this->db->table('tp_publisher_order_details')
            ->where(['order_id' => $order_id, 'book_id' => $book_id])
            ->update([
                'ship_date' => date('Y-m-d H:i:s'),
                'ship_status' => 3
            ]);

        $this->db->transComplete();

        return $this->db->transStatus();
    }
    public function getPublisherDetails($publisher_id)
    {
        $result = [];

        $publisher_sql = "SELECT * FROM tp_publisher_details WHERE publisher_id = ?";
        $publisher_query = $this->db->query($publisher_sql, [$publisher_id]);
        $publisher_data = $publisher_query->getRowArray();
        $result['publishers_data'][] = $publisher_data;

        $author_sql = "SELECT pad.*, pd.publisher_id 
            FROM tp_publisher_author_details pad 
            JOIN tp_publisher_details pd ON pd.publisher_id = pad.publisher_id 
            WHERE pad.publisher_id = ?";
        $author_query = $this->db->query($author_sql, [$publisher_id]);
        $result['authors_data'] = $author_query->getResultArray();

        $result['author_count'] = $this->db
            ->table('tp_publisher_author_details')
            ->selectCount('author_id', 'author_count')
            ->where('publisher_id', $publisher_id)
            ->get()->getRow('author_count');

        $result['book_count'] = $this->db
            ->table('tp_publisher_bookdetails')
            ->selectCount('book_id', 'book_count')
            ->where('publisher_id', $publisher_id)
            ->get()->getRow('book_count');

        $book_sql = "SELECT pab.*, pad.author_name
            FROM tp_publisher_bookdetails pab 
            JOIN tp_publisher_author_details pad ON pab.author_id = pad.author_id 
            JOIN tp_publisher_details pd ON pab.publisher_id = pd.publisher_id
            WHERE pab.publisher_id = ?";
        $books_query = $this->db->query($book_sql, [$publisher_id]);
        $result['publishers_books_data'] = $books_query->getResultArray();

        $statuses = [
            'active_count_data'    => 1,
            'inactive_count_data'  => 0,
            'pending_count_data'   => 2,
            'active_book_data'     => 1,
            'inactive_book_data'   => 0,
            'pending_book_data'    => 2,
        ];

        foreach ($statuses as $key => $status) {
        $table = str_contains($key, 'book') ? 'tp_publisher_bookdetails' : 'tp_publisher_author_details';
        $query = $this->db->table($table)
            ->selectCount('*', 'cnt')
            ->where('publisher_id', $publisher_id)
            ->where('status', $status)
            ->get();
    
    $row = $query->getRowArray();
    $result[$key] = [$key => $row['cnt'] ?? 0];
        }

        return $result;
    }
    public function getBooks($status = null)
{
    // Get the selected publisher from session
    $selected_publisher_id = session()->get('selected_publisher_id');
    
    $builder = $this->db->table('tp_publisher_bookdetails pab');
    $builder->select('pab.*, pad.author_name, pad.status as author_status, pd.publisher_name, COALESCE(ps.stock_in_hand, 0) AS stock_in_hand');
    $builder->join('tp_publisher_author_details pad', 'pab.author_id = pad.author_id');
    $builder->join('tp_publisher_details pd', 'pab.publisher_id = pd.publisher_id');
    $builder->join('tp_publisher_book_stock ps', 'ps.book_id = pab.book_id', 'left'); // join stock table

    // Add publisher filter
    if ($selected_publisher_id && $selected_publisher_id !== 'all') {
        $builder->where('pab.publisher_id', $selected_publisher_id);
    }

    if (!is_null($status)) {
        $builder->where('pab.status', $status);
    }

    return $builder->get()->getResultArray();
}
    public function getFullBookData($book_id)
{
    $builder = $this->db->table('tp_publisher_bookdetails pab');
    $builder->select('
        pab.*, 
        pad.author_name, pad.author_id, pad.mobile AS author_mobile, pad.email_id AS author_email, 
        pad.author_discription, pad.author_image, pad.created_at AS author_created_at, pad.status AS author_status,
        pd.publisher_id, pd.publisher_name, pd.contact_person, pd.address, pd.mobile, pd.email_id, pd.status AS publisher_status
    ');
    $builder->join('tp_publisher_author_details pad', 'pab.author_id = pad.author_id');
    $builder->join('tp_publisher_details pd', 'pab.publisher_id = pd.publisher_id');
    $builder->where('pab.book_id', $book_id);
    $query = $builder->get();

    $result = [];

    if ($query->getNumRows() > 0) {
        $row = $query->getRowArray();

        // --- Stock info ---
        $stock = $this->db->table('tp_publisher_book_stock')
                          ->select('stock_in_hand')
                          ->where('book_id', $book_id)
                          ->get()
                          ->getRow();
        $stock_in_hand = $stock->stock_in_hand ?? 0;

        $stock_out = $this->db->table('tp_publisher_book_stock_ledger')
                              ->selectSum('stock_out', 'total_stock_out')
                              ->where('book_id', $book_id)
                              ->get()
                              ->getRow();
        $stock_out_sum = $stock_out->total_stock_out ?? 0;

        $result['books_data'][] = [
            'book_id'             => $row['book_id'],
            'sku_no'              => $row['sku_no'],
            'book_title'          => $row['book_title'],
            'book_regional_title' => $row['book_regional_title'],
            'language'            => $row['language'],
            'no_of_pages'         => $row['no_of_pages'],
            'mrp'                 => $row['mrp'],
            'pustaka_price'       => $row['pustaka_price'],
            'isbn'                => $row['isbn'],
            'status'              => $row['status'],
            'publisher_name'      => $row['publisher_name'],
            'author_name'         => $row['author_name'],
            'stock_in_hand'       => $stock_in_hand,
            'stock_out'           => $stock_out_sum
        ];

        $result['authors_data'][] = [
            'author_id'          => $row['author_id'],
            'author_name'        => $row['author_name'],
            'mobile'             => $row['author_mobile'],
            'email_id'           => $row['author_email'],
            'author_discription' => $row['author_discription'],
            'author_image'       => $row['author_image'],
            'created_at'         => $row['author_created_at'],
            'status'             => $row['author_status']
        ];

        $result['publishers_data'][] = [
            'publisher_id'   => $row['publisher_id'],
            'publisher_name' => $row['publisher_name'],
            'contact_person' => $row['contact_person'],
            'address'        => $row['address'],
            'mobile'         => $row['mobile'],
            'email_id'       => $row['email_id'],
            'status'         => $row['publisher_status']
        ];
    } else {
        $result['books_data'] = [];
        $result['authors_data'] = [];
        $result['publishers_data'] = [];
    }

    $langQuery = $this->db->table('language_tbl')->orderBy('language_name')->get();
    $result['languages'] = $langQuery->getResultArray();

    return $result;
}

        public function getEditBooks($book_id = null)
    {
        $builder = $this->db->table('tp_publisher_bookdetails');
        $builder->orderBy('book_title');

        if ($book_id !== null) {
            $builder->where('book_id', $book_id);
            $query = $builder->get(1); 
            return $query->getRowArray(); 
        }

        $query = $builder->get();
        return $query->getResultArray(); 
    }
    public function editBookPost($book_id)
{
    $request = service('request');

    $update_data = [
        'publisher_id'        => $request->getPost('publisher_id'),
        'author_id'           => $request->getPost('author_id'),
        'book_title'          => $request->getPost('book_title'),
        'book_regional_title' => $request->getPost('book_regional_title'),
        'book_url'            => $request->getPost('book_url'),
        'initiate_to_print'   => $request->getPost('initiate_to_print') ? 1 : 0,
        'book_genre'          => $request->getPost('book_genre'),
        'type_name'           => $request->getPost('type_name'),
        'language'            => $request->getPost('language'),
        'book_description'    => $request->getPost('book_description'),
        'no_of_pages'         => $request->getPost('no_of_pages'),
        'mrp'                 => $request->getPost('mrp'),
        'pustaka_price'       => $request->getPost('pustaka_price'),
        'isbn'                => $request->getPost('isbn'),
        'discount'            => $request->getPost('discount'),
        'amazon_link'         => $request->getPost('amazon_link'),
        'amazon_asin'         => $request->getPost('amazon_asin'),
    ];

    $builder = $this->db->table('tp_publisher_bookdetails');
    $builder->where('book_id', $book_id);
    $builder->update($update_data);

    if ($this->db->affectedRows() > 0) {
        return 1;
    } else {
        return 0;
    }
}
public function tpBookSalesData()
{
    // Get the selected publisher from session
    $selected_publisher_id = session()->get('selected_publisher_id');
    
    $builder = $this->db->table('tp_publisher_sales s')
        ->select('s.sales_channel, s.create_date, SUM(s.qty) as total_qty, SUM(s.total_amount) as total_amount, SUM(s.discount) as discount, SUM(s.author_amount) as author_amount')
        ->join('tp_publisher_bookdetails b', 'b.book_id = s.book_id');
    
    // Add publisher filter
    if ($selected_publisher_id && $selected_publisher_id !== 'all') {
        $builder->where('b.publisher_id', $selected_publisher_id);
    }
    
    return $builder->groupBy(['s.sales_channel', 's.create_date'])
        ->orderBy('s.sales_channel', 'ASC')
        ->orderBy('s.create_date', 'DESC')
        ->get()
        ->getResultArray();
}

public function getAlltpBookDetails()
{
    $builder = $this->db->table('tp_publisher_bookdetails b');

    $builder->select("
        b.book_id,
        b.publisher_id,
        pd.publisher_name,
        b.sku_no,
        b.book_title,
        b.author_id,
        b.mrp,
        s.book_quantity,
        -- calculate available stock
        (COALESCE(s.stock_in_hand, 0) 
         - COALESCE((
             SELECT SUM(od.quantity)
             FROM tp_publisher_order_details od
             WHERE od.book_id = b.book_id
               AND od.ship_status = 0
         ), 0)
        ) AS stock_in_hand,
        COALESCE(SUM(l.stock_out), 0) AS stock_out
    ");

    $builder->join('tp_publisher_book_stock s', 'b.book_id = s.book_id', 'left');
    $builder->join('tp_publisher_book_stock_ledger l', 'b.book_id = l.book_id', 'left');
    $builder->join('tp_publisher_details pd', 'b.publisher_id = pd.publisher_id', 'left');

    $builder->groupBy('b.book_id, s.stock_in_hand, s.book_quantity, pd.publisher_name');

    $query = $builder->get();

    return $query->getNumRows() > 0 ? $query->getResultArray() : [];
}


public function tppublisherSelectedBooks($selected_book_list)
{
    $db = \Config\Database::connect();

    if (!is_array($selected_book_list)) {
        $selected_book_list = explode(',', $selected_book_list);
    }

    $builder = $db->table('tp_publisher_bookdetails');
    $builder->select('
        tp_publisher_bookdetails.book_id,
        tp_publisher_bookdetails.sku_no,
        tp_publisher_bookdetails.publisher_id,
        tp_publisher_bookdetails.author_id,
        tp_publisher_bookdetails.book_title,
        tp_publisher_bookdetails.book_regional_title as regional_book_title,
        tp_publisher_bookdetails.no_of_pages AS number_of_page,
        tp_publisher_bookdetails.mrp AS price,
        tp_publisher_bookdetails.pustaka_price AS paper_back_inr,
        tp_publisher_author_details.author_name,
        (tp_publisher_book_stock.stock_in_hand - IFNULL(shipped.total_qty, 0)) AS stock_in_hand
    ');
    $builder->join('tp_publisher_author_details', 'tp_publisher_author_details.author_id = tp_publisher_bookdetails.author_id');
    $builder->join('tp_publisher_book_stock', 'tp_publisher_book_stock.book_id = tp_publisher_bookdetails.book_id', 'left');

    // join to calculate shipped qty with ship_status = 'O'
    $builder->join(
        '(SELECT book_id, SUM(quantity) AS total_qty
          FROM tp_publisher_order_details
          WHERE ship_status = "O"
          GROUP BY book_id) AS shipped',
        'shipped.book_id = tp_publisher_bookdetails.book_id',
        'left'
    );

    $builder->whereIn('tp_publisher_bookdetails.sku_no', $selected_book_list);

    // only show books with stock > 0
    $builder->having('stock_in_hand >', 0);

    // order by sku_no
    $builder->orderBy('tp_publisher_bookdetails.sku_no', 'ASC');

    $query = $builder->get();
    return $query->getResultArray();
}

    public function tppublisherOrderPost($selected_book_list)
{
    $db = \Config\Database::connect();

    if (!is_array($selected_book_list)) {
        $selected_book_list = explode(',', $selected_book_list);
    }

    $builder = $db->table('tp_publisher_bookdetails');

    $builder->select([
        'tp_publisher_bookdetails.book_id',
        'tp_publisher_bookdetails.sku_no',
        'tp_publisher_bookdetails.book_title',
        'tp_publisher_bookdetails.no_of_pages AS number_of_page',
        'tp_publisher_bookdetails.mrp AS price',
        'tp_publisher_bookdetails.author_id',
        'tp_publisher_bookdetails.publisher_id',
        'tp_publisher_author_details.author_name',
        'tp_publisher_book_stock.stock_in_hand',
    ]);

    $builder->join('tp_publisher_author_details', 'tp_publisher_author_details.author_id = tp_publisher_bookdetails.author_id');
    $builder->join('tp_publisher_book_stock', 'tp_publisher_book_stock.book_id = tp_publisher_bookdetails.book_id', 'left');
    $builder->whereIn('tp_publisher_bookdetails.sku_no', $selected_book_list);

    return $builder->get()->getResultArray();
}
// In App\Models\TpPublisherModel.php
public function getPublisherAndAuthorByBookId($book_id)
{
    return $this->db->table('tp_publisher_bookdetails')
                    ->select('publisher_id, author_id')
                    ->where('book_id', $book_id)
                    ->get()
                    ->getRowArray();
}
 public function getHandlingCharges($publisher_id)
    {
        return $this->db->table('tp_publisher_sales s')
            ->select("
                s.sales_channel,
                COUNT(s.book_id) AS total_books,
                SUM(s.qty) AS total_qty,
                SUM(s.qty * s.mrp) AS total_amount,
                SUM(s.handling_charge) AS total_handling_charge
            ")
            ->where('s.publisher_id', $publisher_id)
            ->groupBy('s.sales_channel')
            ->orderBy('s.sales_channel', 'ASC')
            ->get()
            ->getResultArray();
    }



 public function getGroupedSales($publisher_id = null)
{
    $builder = $this->db->table('tp_publisher_sales s')
        ->select("
            s.sales_channel,
            s.create_date,
            s.paid_status,
            s.payment_date,
            ROUND(SUM(s.qty) / 2, 0) AS total_qty,
            ROUND(SUM(s.total_amount) / 2, 2) AS total_amount,
            ROUND(SUM(s.discount) / 2, 2) AS total_discount,
            ROUND(SUM(s.author_amount) / 2, 2) AS total_author_amount
        ");

    if ($publisher_id && $publisher_id !== 'all') {
        $builder->join('tp_publisher_bookdetails b', 'b.book_id = s.book_id')
                ->where('b.publisher_id', $publisher_id);
    }

    return $builder->groupBy(['s.create_date', 's.sales_channel', 's.paid_status'])
                   ->orderBy('s.create_date', 'ASC')
                   ->get()
                   ->getResultArray();
}
   public function getSalesSummary()
{
    // Get the selected publisher from session
    $selected_publisher_id = session()->get('selected_publisher_id');
    
    $db = db_connect();
    $builder = $db->table('tp_publisher_sales s');

    $builder->select("
        COUNT(*) AS total_sales,   
        SUM(s.qty) AS total_qty,
        SUM(s.total_amount) AS total_amount,
        SUM(s.discount) AS total_discount,
        SUM(s.author_amount) AS total_author_amount,
        SUM(CASE WHEN s.paid_status = 'paid' THEN s.author_amount ELSE 0 END) AS paid_author_amount,
        SUM(CASE WHEN s.paid_status = 'pending' THEN s.author_amount ELSE 0 END) AS pending_author_amount
    ");

    // Add publisher filter - join with books table
    if ($selected_publisher_id && $selected_publisher_id !== 'all') {
        $builder->join('tp_publisher_bookdetails b', 'b.book_id = s.book_id')
                ->where('b.publisher_id', $selected_publisher_id);
    }

    return $builder->get()->getRowArray();
}
    // Full details for a given date+time
    public function getFullDetails($publisherId, $createDate, $salesChannel)
{
    $createDate   = trim($createDate);
    $salesChannel = trim($salesChannel);

    return $this->db->table('tp_publisher_sales s')
        ->select('s.*, b.book_title, b.sku_no, b.mrp as price, a.author_name, p.publisher_name')
        ->join('tp_publisher_bookdetails b', 'b.book_id = s.book_id', 'left')
        ->join('tp_publisher_author_details a', 'a.author_id = s.author_id', 'left')
        ->join('tp_publisher_details p', 'p.publisher_id = s.publisher_id', 'left') // join publisher details
        ->where('s.publisher_id', $publisherId) 
        ->where('s.sales_channel', $salesChannel)
        ->where('s.create_date >=', $createDate . ' 00:00:00')
        ->where('s.create_date <=', $createDate . ' 23:59:59')
        ->orderBy('s.create_date', 'ASC')
        ->get()
        ->getResultArray();
}

public function getFullDetailsAllPublishers($createDate, $salesChannel)
{
    return $this->db->table('tp_publisher_sales s')
        ->select('s.*, b.book_title, b.sku_no, b.mrp as price, a.author_name, p.publisher_name, s.publisher_id')
        ->join('tp_publisher_bookdetails b', 'b.book_id = s.book_id', 'left')
        ->join('tp_publisher_author_details a', 'a.author_id = s.author_id', 'left')
        ->join('tp_publisher_details p', 'p.publisher_id = s.publisher_id', 'left') // join publisher details
        ->where('s.sales_channel', $salesChannel)
        ->where('s.create_date >=', $createDate . ' 00:00:00')
        ->where('s.create_date <=', $createDate . ' 23:59:59')
        ->orderBy('s.create_date', 'ASC')
        ->get()
        ->getResultArray();
}




public function getBookDetailsById($bookId)
{
    return $this->db->table('tp_publisher_bookdetails')
        ->select('book_id, book_title, sku_no, publisher_id')
        ->where('book_id', $bookId)
        ->get()
        ->getRowArray();
}

public function getBookLedgerByIdAndType($bookId, $description)
{
    return $this->db->table('tp_publisher_book_stock_ledger l')
        ->select('l.*, b.book_title, b.sku_no, p.publisher_name')
        ->join('tp_publisher_bookdetails b', 'b.book_id = l.book_id', 'left')
        ->join('tp_publisher_details p', 'p.publisher_id = b.publisher_id', 'left')
        ->where('l.book_id', $bookId)
        ->like('l.description', $description)   
        ->orderBy('l.transaction_date', 'DESC')
        ->get()
        ->getResultArray();
}
public function getledgerBooks()
    {
        return $this->db->table('tp_publisher_book_stock_ledger l')
            ->select('b.book_id, b.sku_no, b.book_title, a.publisher_name')
            ->join('tp_publisher_bookdetails b', 'b.book_id = l.book_id', 'left')
            ->join('tp_publisher_author_details a', 'a.publisher_id = b.publisher_id', 'left')
            ->groupBy('b.book_id')
            ->get()
            ->getResultArray();
    }

    // First card - Book details
   public function getBookDetails($bookId)
{
    return $this->db->table('tp_publisher_bookdetails b')
        ->select('b.book_id, b.book_title, b.book_regional_title, b.sku_no, b.mrp, b.no_of_pages, b.isbn,
                  p.publisher_name')
        ->join('tp_publisher_details p', 'p.publisher_id = b.publisher_id', 'left')
        ->where('b.book_id', $bookId)
        ->get()
        ->getRowArray();
}

    // Second card - Stock details
    public function getBookStock($bookId)
    {
        return $this->db->table('tp_publisher_book_stock')
            ->select('book_id, book_quantity, stock_in_hand, bookfair')
            ->where('book_id', $bookId)
            ->get()
            ->getRowArray();
    }

    // Third card - Ledger available stock
   public function getLedgerStock($bookId)
{
    $sql = "
        SELECT 
            COALESCE(s.stock_in_hand, 0) AS stock_in_hand,
            (
                SELECT COALESCE(SUM(l.stock_in), 0)
                FROM tp_publisher_book_stock_ledger l
                WHERE l.book_id = s.book_id
            ) AS total_stock_in,
            (
                SELECT COALESCE(SUM(l.stock_out), 0)
                FROM tp_publisher_book_stock_ledger l
                WHERE l.book_id = s.book_id
            ) AS stock_out,
            (
                SELECT COALESCE(SUM(od.quantity), 0)
                FROM tp_publisher_order_details od
                WHERE od.book_id = s.book_id
                  AND od.ship_status = 0
            ) AS pending_qty,
            -- available stock = stock_in_hand - pending_qty
            COALESCE(s.stock_in_hand, 0) - 
            (
                SELECT COALESCE(SUM(od.quantity), 0)
                FROM tp_publisher_order_details od
                WHERE od.book_id = s.book_id
                  AND od.ship_status = 0
            ) AS available
        FROM tp_publisher_book_stock s
        WHERE s.book_id = ?
        LIMIT 1
    ";

    $row = $this->db->query($sql, [$bookId])->getRowArray();

    return [
        'stock_in'     => $row['total_stock_in'] ?? 0,
        'stock_out'    => $row['stock_out'] ?? 0,
        'pending_qty'  => $row['pending_qty'] ?? 0,
        'available'    => $row['available'] ?? 0,
    ];
}



    // First table - Order details
   public function getOrderDetails($bookId)
{
    return $this->db->table('tp_publisher_book_stock_ledger l')
        ->select('
            l.order_id,
            l.channel_type,
            l.description,
            l.stock_in,
            l.stock_out,
            l.transaction_date
        ')
        ->where('l.book_id', $bookId)
        ->orderBy('l.transaction_date', 'ASC')
        ->get()
        ->getResultArray();
}

    // Second table - Royalty / Sales details
     public function getOrderRoyaltyDetails($bookId)
{
    return $this->db->table('tp_publisher_order_details od')
        ->select('
            od.order_id,
            od.book_id,
            od.quantity,
            od.price as order_price,
            o.contact_person,
            o.city,
            o.order_date,
            o.royalty,
            bsl.channel_type
        ')
        ->join('tp_publisher_order o', 'o.order_id = od.order_id', 'left')
        ->join('tp_publisher_book_stock_ledger bsl', 'bsl.book_id = od.book_id AND bsl.order_id = od.order_id', 'left')
        ->where('od.book_id', $bookId)
        ->get()
        ->getResultArray();
}


    // Table 2: Sales Details
    public function getSalesDetails($bookId)
    {
        return $this->db->table('tp_publisher_sales ps')
            ->select('
                ps.order_id,
                ps.book_id,
                ps.qty as sales_qty,
                ps.mrp,
                ps.sales_channel,
                ps.author_amount,
                ps.create_date
            ')
            ->where('ps.book_id', $bookId)
            ->get()
            ->getResultArray();
    }
    public function getSalesSummaryDetailed($publisherId)
{
    return $this->db->table('tp_publisher_sales s')
        ->select("
            s.publisher_id,
            DATE(s.create_date) AS create_date,
            s.sales_channel,
            SUM(s.qty) AS total_qty,
            SUM(s.total_amount) AS total_amount,
            SUM(s.discount) AS total_discount,
            SUM(s.author_amount) AS total_author_amount
        ")
        ->where('s.publisher_id', $publisherId)
        ->groupBy('s.publisher_id')
        ->groupBy('DATE(s.create_date)')
        ->groupBy('s.sales_channel')
        ->orderBy('DATE(s.create_date)', 'DESC')
        ->get()
        ->getResultArray();
}

   public function getOrderPaymentStats()
{
    // Get the selected publisher from session
    $selected_publisher_id = session()->get('selected_publisher_id');
    
    $builder = $this->db->table('tp_publisher_order'); 

    $builder->select("
        COUNT(order_id) as total_orders,
        SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as shipped_orders,
        SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as pending_orders,
        SUM(CASE WHEN LOWER(payment_status) = 'paid' THEN 1 ELSE 0 END) as paid_orders,
        SUM(CASE WHEN LOWER(payment_status) = 'pending' THEN 1 ELSE 0 END) as pending_payments,
        SUM(COALESCE(net_total,0)) as total_net,
        SUM(COALESCE(royalty,0)) as total_royalty,
        SUM(COALESCE(courier_charges,0)) as total_courier,
        SUM(COALESCE(royalty,0) + COALESCE(courier_charges,0)) as total_royalty_courier,
        SUM(CASE WHEN LOWER(payment_status) = 'paid' THEN (COALESCE(royalty,0) + COALESCE(courier_charges,0)) ELSE 0 END) as total_order_value
    ");

    // Add publisher filter
    if ($selected_publisher_id && $selected_publisher_id !== 'all') {
        $builder->where('publisher_id', $selected_publisher_id);
    }

    return $builder->get()->getRowArray();
}
public function getAllPublishers()
{
    return $this->db->table('tp_publisher_details')
        ->select('publisher_id, publisher_name, email_id, mobile, status')
        ->get()
        ->getResultArray();
}

public function getPublisherById($publisher_id)
{
    return $this->db->table('tp_publisher_details')
        ->where('publisher_id', $publisher_id)
        ->get()
        ->getRowArray();
}
public function getAuthorsByPublisher($publisher_id)
{
    return $this->db->table('tp_publisher_author_details')
        ->select('author_id, author_name, email_id, mobile, status')
        ->where('publisher_id', $publisher_id)
        ->get()
        ->getResultArray();
}
public function getPublisherBooks($publisher_id)
{
    return $this->db->table('tp_publisher_bookdetails')
        ->where('publisher_id', $publisher_id)
        ->select('book_id, book_title, isbn, mrp, status')
        ->get()
        ->getResultArray();
}
public function getBooksByPublisher($publisher_id, $status = null)
{
    $builder = $this->db->table('tp_publisher_bookdetails pab');
    $builder->select('pab.*, pad.author_name, pad.status as author_status, pd.publisher_name, COALESCE(ps.stock_in_hand, 0) AS stock_in_hand');
    $builder->join('tp_publisher_author_details pad', 'pab.author_id = pad.author_id');
    $builder->join('tp_publisher_details pd', 'pab.publisher_id = pd.publisher_id');
    $builder->join('tp_publisher_book_stock ps', 'ps.book_id = pab.book_id', 'left'); // join stock table

    $builder->where('pab.publisher_id', $publisher_id); // filter by publisher

    if (!is_null($status)) {
        $builder->where('pab.status', $status);
    }

    return $builder->get()->getResultArray();
}
public function getPublisherOrder($publisher_id, $shipStatus = null, $orderStatus = null)
{
    $builder = $this->db->table('tp_publisher_order o');
    $builder->select("
        o.*,
        od.ship_status,
        od.book_id,
        pad.author_name,
        COUNT(od.book_id) AS total_books,
        SUM(od.quantity) AS total_qty
    ");
    $builder->join('tp_publisher_order_details od', 'o.order_id = od.order_id');
    $builder->join('tp_publisher_author_details pad', 'od.author_id = pad.author_id');

    if ($publisher_id !== null) {
        $builder->where('o.publisher_id', $publisher_id);
    }
    if ($shipStatus !== null) {
        $builder->where('od.ship_status', $shipStatus);
    }
    if ($orderStatus !== null) {
        $builder->where('o.status', $orderStatus);
    }

    $builder->groupBy('o.order_id');
    $builder->orderBy('o.ship_date', 'DESC');

    $orders = $builder->get()->getResultArray();

    foreach ($orders as &$order) {
        $order['book_status'] = (isset($order['stock_in_hand']) && $order['stock_in_hand'] > 0) ? 'In Stock' : 'Out of Stock';
        $order['show_print_button'] = (
            $order['book_status'] === 'Out of Stock' &&
            isset($order['initiate_to_print']) &&
            $order['initiate_to_print'] == 1
        );
    }

    return $orders;
}
public function tpPublisherOrderPayments($publisher_id = null)
{
    $builder = $this->db->table('tp_publisher_order o');
    $builder->select('
        o.order_id,
        o.order_date,
        o.ship_date,
        o.status,
        o.payment_status,
        o.sub_total,
        o.courier_charges,
        o.royalty,
        o.payment_date,
        p.publisher_name,
        MAX(od.ship_status) AS ship_status
    ');
    $builder->join('tp_publisher_order_details od', 'od.order_id = o.order_id', 'left');
    $builder->join('tp_publisher_details p', 'p.publisher_id = o.publisher_id', 'left');

    if ($publisher_id !== null) {
        $builder->where('o.publisher_id', $publisher_id);
    }

    $builder->groupBy('
        o.order_id,
        o.order_date,
        o.ship_date,
        o.status,
        o.payment_status,
        o.sub_total,
        o.courier_charges,
        o.royalty,
        o.payment_date,
        p.publisher_name
    ');

    $builder->orderBy('o.order_id', 'DESC');

    return $builder->get()->getResultArray();
}
public function getOrdersPaymentStats($publisher_id = null)
{
    $builder = $this->db->table('tp_publisher_order');

    if ($publisher_id !== null) {
        $builder->where('publisher_id', $publisher_id);
    }

    return $builder->select("
        COUNT(order_id) as total_orders,
        SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as shipped_orders,
        SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as pending_orders,
        SUM(CASE WHEN LOWER(payment_status) = 'paid' THEN 1 ELSE 0 END) as paid_orders,
        SUM(CASE WHEN LOWER(payment_status) = 'pending' THEN 1 ELSE 0 END) as pending_payments,
        SUM(COALESCE(net_total,0)) as total_net,
        SUM(COALESCE(royalty,0)) as total_royalty,
        SUM(COALESCE(courier_charges,0)) as total_courier,
        SUM(COALESCE(royalty,0) + COALESCE(courier_charges,0)) as total_royalty_courier,
        SUM(CASE WHEN LOWER(payment_status) = 'paid' THEN (COALESCE(royalty,0) + COALESCE(courier_charges,0)) ELSE 0 END) as total_order_value
    ")->get()->getRowArray();
}
// Sales
    public function tpBookSaleData($publisher_id = null) {
        // Get the selected publisher from session
    $selected_publisher_id = session()->get('selected_publisher_id');
    
    $builder = $this->db->table('tp_publisher_sales s')
        ->select('s.sales_channel, s.create_date, SUM(s.qty) as total_qty, SUM(s.total_amount) as total_amount, SUM(s.discount) as discount, SUM(s.author_amount) as author_amount')
        ->join('tp_publisher_bookdetails b', 'b.book_id = s.book_id');
    
    // Add publisher filter
    if ($selected_publisher_id && $selected_publisher_id !== 'all') {
        $builder->where('b.publisher_id', $selected_publisher_id);
    }
    
    return $builder->groupBy(['s.sales_channel', 's.create_date'])
        ->orderBy('s.sales_channel', 'ASC')
        ->orderBy('s.create_date', 'DESC')
        ->get()
        ->getResultArray();
    }

    public function getGroupedSale($publisher_id = null) 
{
    // Use session if publisher_id not provided
    if (!$publisher_id) {
        $publisher_id = session()->get('selected_publisher_id');
    }
    
    $builder = $this->db->table('tp_publisher_sales s')
        ->select("
            s.sales_channel,
            s.create_date,
            SUM(s.qty) AS total_qty,
            SUM(s.total_amount) AS total_amount,
            SUM(s.discount) AS total_discount,
            SUM(s.author_amount) AS total_author_amount
        ");
    
    // Add publisher filter - join with books table since sales table may not have publisher_id directly
    if ($publisher_id && $publisher_id !== 'all') {
        $builder->join('tp_publisher_bookdetails b', 'b.book_id = s.book_id')
                ->where('b.publisher_id', $publisher_id);
    }
    
    return $builder->groupBy(['s.create_date', 's.sales_channel'])
                   ->orderBy('s.create_date', 'ASC')
                   ->get()->getResultArray();
}

    public function getPaymentSale($publisher_id = null) 
{
    // Use session if publisher_id not provided
    if (!$publisher_id) {
        $publisher_id = session()->get('selected_publisher_id');
    }
    
    $builder = $this->db->table('tp_publisher_sales s')
        ->select("
            s.create_date, 
            s.sales_channel, 
            SUM(s.qty) as total_qty, 
            SUM(s.total_amount) as total_amount, 
            SUM(s.discount) as total_discount, 
            SUM(s.author_amount) as total_author_amount,
            s.paid_status, 
            s.payment_date
        ");
    
    // Add publisher filter - join with books table since sales table may not have publisher_id directly
    if ($publisher_id && $publisher_id !== 'all') {
        $builder->join('tp_publisher_bookdetails b', 'b.book_id = s.book_id')
                ->where('b.publisher_id', $publisher_id);
    }
    
    return $builder->groupBy("s.create_date, s.sales_channel, s.paid_status")
                   ->orderBy("s.create_date", "DESC")
                   ->get()->getResultArray();
}

    public function getSaleSummary($publisher_id = null) {
        $builder = $this->db->table('tp_publisher_sales')
            ->select("
                COUNT(*) AS total_sales,   
                SUM(qty) AS total_qty,
                SUM(total_amount) AS total_amount,
                SUM(discount) AS total_discount,
                SUM(author_amount) AS total_author_amount,
                SUM(CASE WHEN paid_status = 'paid' THEN author_amount ELSE 0 END) AS paid_author_amount,
                SUM(CASE WHEN paid_status = 'pending' THEN author_amount ELSE 0 END) AS pending_author_amount
            ");
        if ($publisher_id) $builder->where('publisher_id', $publisher_id);
        return $builder->get()->getRowArray();
    }

    // Placeholder
    public function countsData($publisher_id = null) {
        return [
            'authors' => count($this->getAuthorsByPublisher($publisher_id)),
            'books' => count($this->getPublisherBooks($publisher_id)),
            'orders' => count($this->getPublisherOrder($publisher_id)),
            'sales' => count($this->tpBookSaleData($publisher_id)),
        ];
    }
    public function getPublisherSalesStats($publisher_id)
{
    $db = \Config\Database::connect();

    // 🟢 Base query for this publisher
    $builder = $db->table('tp_publisher_sales')
        ->select("
            COUNT(DISTINCT create_date) AS total_sales,
            COUNT(DISTINCT CASE WHEN channel_type = 'PUS' THEN create_date END) AS qty_pustaka,
            COUNT(DISTINCT CASE WHEN channel_type = 'AMZ' THEN create_date END) AS qty_amazon,
            COUNT(DISTINCT CASE WHEN channel_type LIKE 'BFR' THEN create_date END) AS qty_bookfair,
            COUNT(DISTINCT CASE WHEN channel_type = 'OTH' THEN create_date END) AS qty_other
        ", false)
        ->where('publisher_id', $publisher_id);

    $result = $builder->get()->getRow();

    // 🟣 Prepare data for view
    return [
        'total_sales' => $result->total_sales ?? 0,
        'qty_pustaka' => $result->qty_pustaka ?? 0,
        'qty_amazon'  => $result->qty_amazon ?? 0,
        'qty_bookfair'=> $result->qty_bookfair ?? 0,
        'qty_other'   => $result->qty_other ?? 0,
    ];
}
// ✅ Last 30 days shipped orders (paid or pending)
public function getRecentShippedOrders()
{
    // Get the selected publisher from session
    $selected_publisher_id = session()->get('selected_publisher_id');
    
    $dateLimit = date('Y-m-d', strtotime('-30 days'));

    $sql = "
        SELECT 
            o.order_id,
            o.order_date,
            GROUP_CONCAT(DISTINCT b.sku_no SEPARATOR ', ') AS sku_nos,
            GROUP_CONCAT(DISTINCT b.book_title SEPARATOR ', ') AS book_titles,
            p.publisher_name,
            MAX(od.ship_date) AS ship_date,
            o.payment_status
        FROM tp_publisher_order o
        JOIN tp_publisher_order_details od ON o.order_id = od.order_id
        JOIN tp_publisher_bookdetails b ON b.book_id = od.book_id
        JOIN tp_publisher_details p ON p.publisher_id = b.publisher_id
        WHERE od.ship_status = 1
          AND od.ship_date >= ?
          AND o.payment_status IN ('pending', 'paid')
    ";

    // Add publisher filter
    if ($selected_publisher_id && $selected_publisher_id !== 'all') {
        $sql .= " AND b.publisher_id = ?";
        $params = [$dateLimit, $selected_publisher_id];
    } else {
        $params = [$dateLimit];
    }

    $sql .= " GROUP BY o.order_id, o.order_date, p.publisher_name, o.payment_status
              ORDER BY MAX(od.ship_date) DESC";

    return $this->db->query($sql, $params)->getResultArray();
}

// ✅ Orders shipped more than 30 days ago & still pending
public function getOldPendingOrders()
{
    // Get the selected publisher from session
    $selected_publisher_id = session()->get('selected_publisher_id');
    
    $dateLimit = date('Y-m-d', strtotime('-30 days'));

    $sql = "
        SELECT 
            o.order_id,
            o.order_date,
            GROUP_CONCAT(DISTINCT b.sku_no SEPARATOR ', ') AS sku_nos,
            GROUP_CONCAT(DISTINCT b.book_title SEPARATOR ', ') AS book_titles,
            p.publisher_name,
            MAX(od.ship_date) AS ship_date,
            o.payment_status
        FROM tp_publisher_order o
        JOIN tp_publisher_order_details od ON o.order_id = od.order_id
        JOIN tp_publisher_bookdetails b ON b.book_id = od.book_id
        JOIN tp_publisher_details p ON p.publisher_id = b.publisher_id
        WHERE od.ship_status = 1
          AND od.ship_date < ?
          AND o.payment_status = 'pending'
    ";

    // Add publisher filter
    if ($selected_publisher_id && $selected_publisher_id !== 'all') {
        $sql .= " AND b.publisher_id = ?";
        $params = [$dateLimit, $selected_publisher_id];
    } else {
        $params = [$dateLimit];
    }

    $sql .= " GROUP BY o.order_id, o.order_date, p.publisher_name, o.payment_status
              ORDER BY MAX(od.ship_date) DESC";

    return $this->db->query($sql, $params)->getResultArray();
}

// ✅ All shipped orders (paid or pending)
public function getAllShippedOrders()
{
    // Get the selected publisher from session
    $selected_publisher_id = session()->get('selected_publisher_id');
    
    $sql = "
        SELECT 
            o.order_id,
            o.order_date,
            GROUP_CONCAT(DISTINCT b.sku_no SEPARATOR ', ') AS sku_nos,
            GROUP_CONCAT(DISTINCT b.book_title SEPARATOR ', ') AS book_titles,
            p.publisher_name,
            MAX(od.ship_date) AS ship_date,
            o.payment_status
        FROM tp_publisher_order o
        JOIN tp_publisher_order_details od ON o.order_id = od.order_id
        JOIN tp_publisher_bookdetails b ON b.book_id = od.book_id
        JOIN tp_publisher_details p ON p.publisher_id = b.publisher_id
        WHERE od.ship_status = 1
          AND o.payment_status IN ('pending', 'paid')
    ";

    // Add publisher filter
    if ($selected_publisher_id && $selected_publisher_id !== 'all') {
        $sql .= " AND b.publisher_id = ?";
        $params = [$selected_publisher_id];
    } else {
        $params = [];
    }

    $sql .= " GROUP BY o.order_id, o.order_date, p.publisher_name, o.payment_status
              ORDER BY MAX(od.ship_date) DESC";

    return $this->db->query($sql, $params)->getResultArray();
}
public function getFirstPublisherId()
{
    return $this->db->table('tp_publisher_details')
        ->select('publisher_id')
        ->orderBy('publisher_id', 'ASC')
        ->get()
        ->getRow('publisher_id');
}

public function gettpPublishersDetails($publisher_id = null)
{
    $builder = $this->db->table('tp_publisher_bookdetails b');

    $builder->select("
        b.book_id,
        b.sku_no,
        b.publisher_id,
        b.book_title,
        b.author_id,
        b.mrp,
        COALESCE(s.stock_in_hand, 0) AS stock_in_hand,
        (
            SELECT COALESCE(SUM(od.quantity), 0)
            FROM tp_publisher_order_details od
            WHERE od.book_id = b.book_id AND od.ship_status = 0
        ) AS pending_qty,
        (
            COALESCE(s.stock_in_hand, 0) - COALESCE((
                SELECT SUM(od.quantity)
                FROM tp_publisher_order_details od
                WHERE od.book_id = b.book_id AND od.ship_status = 0
            ), 0)
        ) AS available_stock
    ");

    // LEFT JOIN to include books even if stock entry does not exist
    $builder->join('tp_publisher_book_stock s', 's.book_id = b.book_id', 'left');

    // Filter by publisher_id unless 'all' or null
    if ($publisher_id !== null && $publisher_id !== 'all') {
        $builder->where('b.publisher_id', $publisher_id);
    }

    $builder->groupBy('b.book_id, b.sku_no, b.publisher_id, b.book_title, b.author_id, b.mrp, s.stock_in_hand');

    $query = $builder->get();

    // Debug: log generated SQL
    log_message('debug', 'gettpPublishersDetails SQL: ' . $builder->getCompiledSelect());

    return $query->getNumRows() > 0 ? $query->getResultArray() : [];
}


// Fetch selected books for preview
public function tppublishersSelectedBooks($selected_book_list)
{
    $db = \Config\Database::connect();

    if (!is_array($selected_book_list)) {
        $selected_book_list = explode(',', $selected_book_list);
    }

    $builder = $db->table('tp_publisher_bookdetails');
    $builder->select('
        tp_publisher_bookdetails.book_id,
        tp_publisher_bookdetails.sku_no,
        tp_publisher_bookdetails.publisher_id,
        tp_publisher_bookdetails.author_id,
        tp_publisher_bookdetails.book_title,
        tp_publisher_bookdetails.book_regional_title as regional_book_title,
        tp_publisher_bookdetails.no_of_pages AS number_of_page,
        tp_publisher_bookdetails.mrp AS price,
        tp_publisher_bookdetails.isbn,
        tp_publisher_bookdetails.pustaka_price AS paper_back_inr,
        tp_publisher_author_details.author_name,
        (tp_publisher_book_stock.stock_in_hand - IFNULL(shipped.total_qty, 0)) AS stock_in_hand
    ');
    $builder->join('tp_publisher_author_details', 'tp_publisher_author_details.author_id = tp_publisher_bookdetails.author_id');
    $builder->join('tp_publisher_book_stock', 'tp_publisher_book_stock.book_id = tp_publisher_bookdetails.book_id', 'left');

    // join to calculate shipped qty with ship_status = 'O'
    $builder->join(
        '(SELECT book_id, SUM(quantity) AS total_qty
          FROM tp_publisher_order_details
          WHERE ship_status = "O"
          GROUP BY book_id) AS shipped',
        'shipped.book_id = tp_publisher_bookdetails.book_id',
        'left'
    );

    $builder->whereIn('tp_publisher_bookdetails.sku_no', $selected_book_list);

    $query = $builder->get();
    return $query->getResultArray();
}

public function tppublisherOrderStock($selected_book_list)
{
    $db = \Config\Database::connect();

    if (!is_array($selected_book_list)) {
        $selected_book_list = explode(',', $selected_book_list);
    }

    $builder = $db->table('tp_publisher_bookdetails');

    $builder->select([
        'tp_publisher_bookdetails.book_id',
        'tp_publisher_bookdetails.sku_no',
        'tp_publisher_bookdetails.book_title',
        'tp_publisher_bookdetails.no_of_pages AS number_of_page',
        'tp_publisher_bookdetails.isbn',
        'tp_publisher_bookdetails.mrp AS price',
        'tp_publisher_author_details.author_name',
        'tp_publisher_book_stock.stock_in_hand',
    ]);

    $builder->join('tp_publisher_author_details', 'tp_publisher_author_details.author_id = tp_publisher_bookdetails.author_id');
    $builder->join('tp_publisher_book_stock', 'tp_publisher_book_stock.book_id = tp_publisher_bookdetails.book_id', 'left');
    $builder->whereIn('tp_publisher_bookdetails.sku_no', $selected_book_list);

    $query = $builder->get();
    return $query->getResultArray();
}
   public function tppublisherOrderSubmit(
    $user_id, $author_id, $publisher_id, $book_ids, $quantities,
    $address, $mobile, $ship_date, $transport, $comments, $contact_person, $city )
{
    $order_id   = time();
    $order_date = date('Y-m-d H:i:s');
    $grand_total = 0;

    // Insert order without totals
    $this->db->table('tp_publisher_order')->insert([
        'order_id'      => $order_id,
        'author_id'     => $author_id,
        'publisher_id'  => $publisher_id,
        'ship_date'     => $ship_date,
        'order_date'    => $order_date,
        'status'        => 0,
        'address'       => trim($address),
        'mobile'        => trim($mobile),
        'sub_total'     => 0,
        'royalty'       => 0,
        'payment_status'=> 'pending',
        'transport'     => trim($transport),
        'comments'      => trim($comments),
        'contact_person'=> $contact_person,
        'city'          => $city,
    ]);

    $items = []; // collect all items for debug return

    foreach ($book_ids as $i => $book_id) {
        $qty = (int)($quantities[$i] ?? 0);
        if ($qty <= 0) continue;

        $bookData = $this->db->table('tp_publisher_bookdetails')
            ->select('mrp, book_title')
            ->where('book_id', $book_id)
            ->get()
            ->getRow();

        $mrp   = $bookData ? $bookData->mrp : 0;
        $title = $bookData ? $bookData->book_title : 'Unknown';
        $price = $qty * $mrp;

        $grand_total += $price;

        // Insert details row
        $this->db->table('tp_publisher_order_details')->insert([
            'order_id'     => $order_id,
            'user_id'      => $user_id,
            'publisher_id' => $publisher_id,
            'author_id'    => $author_id,
            'book_id'      => $book_id,
            'quantity'     => $qty,
            'price'        => $price,
            'ship_date'    => $ship_date,
            'order_date'   => $order_date,
            'ship_status'  => 0,
        ]);

        // Store for debug
        $items[] = [
            'book_id'    => $book_id,
            'title'      => $title,
            'mrp'        => $mrp,
            'quantity'   => $qty,
            'price'      => $price
        ];
    }

    // royalty calculation
    if ($grand_total <= 500) {
        $royalty = 25;
    } elseif ($grand_total <= 2000) {
        $royalty = ceil(($grand_total * 0.10) / 10) * 10;
    } elseif ($grand_total <= 4000) {
        $royalty = ceil(($grand_total * 0.08) / 10) * 10;
    } else {
        $royalty = ceil(($grand_total * 0.05) / 10) * 10;
    }

    // update totals
    $this->db->table('tp_publisher_order')
        ->where('order_id', $order_id)
        ->update([
            'sub_total' => $grand_total,
            'royalty'   => $royalty
        ]);

    // return full debug data
    return [
        'order_id'     => $order_id,
        'publisher_id' => $publisher_id,
        'author_id'    => $author_id,
        'sub_total'    => $grand_total,
        'royalty'      => $royalty,
        'grand_total'  => $grand_total + $royalty,
        'items'        => $items
    ];
}
}