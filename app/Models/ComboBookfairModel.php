<?php

namespace App\Models;

use CodeIgniter\Model;

class ComboBookfairModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // ================= BOOKSHOPS =================
    public function getBookshops()
    {
        return $this->db->table('pod_bookshop')->get()->getResultArray();
    }

    public function getBookshopTransport($bookshopId)
    {
        return $this->db->table('pod_bookshop')
            ->where('bookshop_id', $bookshopId)
            ->get()
            ->getRowArray();
    }

    // ================= COMBO DROPDOWN =================
    public function getCombos()
    {
        return $this->db->table('bookfair_combo_pack')
            ->select('combo_id, pack_name')
            ->orderBy('created_date','DESC')
            ->get()
            ->getResultArray();
    }


    // ================= TOTAL QTY =================
    public function getComboTotalQty($comboId)
    {
        $row = $this->db->table('bookfair_combo_pack_details')
            ->selectSum('default_value')
            ->where('combo_id', $comboId)
            ->get()
            ->getRowArray();

        return $row['default_value'] ?? 0;
    }

    // ================= CREATE ORDER =================
    public function createOrder($orderData, $comboId)
    {
        $db = $this->db;
        $db->transBegin();

        // MAIN ORDER
        $db->table('bookfair_sale_or_return_orders')->insert($orderData);

        // FETCH COMBO BOOKS (DETAILS TABLE)
        $comboBooks = $db->table('bookfair_combo_pack_details')
            ->where('combo_id', $comboId)
            ->get()
            ->getResultArray();

        if (!$comboBooks) {
            throw new \Exception('Combo books not found');
        }

        foreach ($comboBooks as $row) {

            $bookId = $row['book_id'];
            $qty    = $row['default_value'];

            $book = $db->table('book_tbl')
                ->where('book_id', $bookId)
                ->get()
                ->getRowArray();

            if (!$book) continue;

            $db->table('bookfair_sale_or_return_order_details')->insert([
                'order_id'    => $orderData['order_id'],
                'bookshop_id' => $orderData['bookshop_id'],
                'create_date' => $orderData['create_date'],
                'book_id'     => $bookId,
                'send_qty'    => $qty,
                'book_price'  => $book['paper_back_inr'],
                'discount'    => 0,
                'status'      => 0
            ]);
        }

        if ($db->transStatus() === false) {
            $db->transRollback();
            throw new \Exception('Transaction failed');
        }

        $db->transCommit();
        return true;
    }

    // ================= RETURN =================
    public function getReturnOrder($orderId)
    {
        $data['order'] = $this->db->table('bookfair_sale_or_return_orders o')
            ->select('o.*,bs.bookshop_name,cp.pack_name')
            ->join('pod_bookshop bs','bs.bookshop_id=o.bookshop_id','left')
            ->join('bookfair_combo_pack cp','cp.combo_id=o.combo_id','left')
            ->where('o.order_id',$orderId)
            ->get()
            ->getRowArray();

        $data['books'] = $this->db->table('bookfair_sale_or_return_order_details d')
            ->select('d.*,b.book_title,a.author_name')
            ->join('book_tbl b','b.book_id=d.book_id','left')
            ->join('author_tbl a','a.author_id=b.author_name','left')
            ->where('d.order_id',$orderId)
            ->get()
            ->getResultArray();

        return $data;
    }

    public function processReturn($orderId, $returnQtyArr, $discountPercent)
    {
        $db = $this->db;
        $db->transBegin();

        $details = $db->table('bookfair_sale_or_return_order_details')
            ->where('order_id', $orderId)
            ->get()
            ->getResultArray();

        if (!$details) {
            throw new \Exception('No order details found');
        }

        foreach ($details as $row) {

            $bookId  = $row['book_id'];
            $price  = $row['book_price'];

            $saleQty   = $row['sale_qty'] ?? $row['send_qty'];
            $returnQty = $returnQtyArr[$bookId] ?? 0;

            if ($returnQty > $saleQty) {
                $returnQty = $saleQty;
            }

            $finalSaleQty = $saleQty - $returnQty;

        
            $salesAmount = $finalSaleQty * $price;

        
            $discountAmount = ($discountPercent > 0)
                ? round(($salesAmount * $discountPercent) / 100, 2)
                : 0;

            $finalAmount = max(0, $salesAmount - $discountAmount);

            $db->table('bookfair_sale_or_return_order_details')
                ->where('order_id', $orderId)
                ->where('book_id', $bookId)
                ->update([
                    'sale_qty'     => $finalSaleQty,
                    'return_qty'   => ($row['return_qty'] ?? 0) + $returnQty,

                
                    'discount'     => $discountPercent, 
                    'total_amount' => $finalAmount,

                    'status'       => 2
                ]);
        }

        $db->table('bookfair_sale_or_return_orders')
            ->where('order_id', $orderId)
            ->update(['status' => 2]);

        if ($db->transStatus() === false) {
            $db->transRollback();
            throw new \Exception('Return failed');
        }

        $db->transCommit();
        return true;
    }


    // ================= LISTS =================
    
    public function getBookfairOrders($status)
    {
        $sql = "SELECT 
                    bookfair_sale_or_return_orders.order_id,
                    bookfair_sale_or_return_orders.sending_date,
                    pod_bookshop.bookshop_name,
                    bookfair_combo_pack.pack_name,
                    COUNT(bookfair_combo_pack_details.book_id) AS no_of_titles,
                    bookfair_combo_pack.combo_id,
                    bookfair_combo_pack.total_quantity AS total_qty,
                    bookfair_sale_or_return_order_details.discount
                FROM 
                    bookfair_sale_or_return_orders
                JOIN
                    pod_bookshop 
                    ON pod_bookshop.bookshop_id = bookfair_sale_or_return_orders.bookshop_id
                JOIN
                    bookfair_combo_pack 
                    ON bookfair_combo_pack.combo_id = bookfair_sale_or_return_orders.combo_id
                JOIN 
                    bookfair_combo_pack_details 
                    ON bookfair_combo_pack_details.combo_id = bookfair_sale_or_return_orders.combo_id
                JOIN
                    bookfair_sale_or_return_order_details 
                    ON bookfair_sale_or_return_order_details.order_id = bookfair_sale_or_return_orders.order_id
                WHERE 
                    bookfair_sale_or_return_orders.status = $status
                GROUP BY 
                    bookfair_sale_or_return_orders.order_id
                ORDER BY 
                    bookfair_sale_or_return_orders.order_id DESC";

        $query = $this->db->query($sql);

        return $query->getResultArray();
    }

    public function getBookfairOrderDetails($orderId)
    {
        $details = $this->db->table('bookfair_sale_or_return_orders o')
            ->join('pod_bookshop s','s.bookshop_id=o.bookshop_id','left')
            ->join('bookfair_combo_pack c','c.combo_id=o.combo_id','left')
            ->where('o.order_id',$orderId)
            ->get()
            ->getRowArray();

       $list = $this->db->table('bookfair_sale_or_return_order_details d')
        ->select('d.*, b.book_title, a.author_name, l.language_name')
        ->join('book_tbl b','b.book_id=d.book_id','left')
        ->join('author_tbl a','a.author_id=b.author_name','left')
        ->join('language_tbl l','l.language_id=b.language','left')
        ->where('d.order_id',$orderId)
        ->get()
        ->getResultArray();


        return ['details'=>$details,'list'=>$list];
    }

    // ================= COMBO =================
    public function getBookfairCombos()
    {
        return $this->db->table('bookfair_combo_pack c')
            ->select('
                c.combo_id,
                c.pack_name,
                COUNT(d.book_id) as book_count,
                SUM(d.default_value) as total_quantity
            ')
            ->join('bookfair_combo_pack_details d','d.combo_id=c.combo_id','left')
            ->groupBy('c.combo_id')
            ->get()
            ->getResultArray();
    }

    public function getBookfairComboBooks($comboId)
    {
        return $this->db->table('bookfair_combo_pack_details d')
            ->select('
                c.pack_name,
                b.book_id,
                b.book_title,
                b.paper_back_inr,
                a.author_name,
                l.language_name,
                IFNULL(p.stock_in_hand,0) as stock,
                d.default_value
            ')
            ->join('bookfair_combo_pack c','c.combo_id=d.combo_id','left')
            ->join('book_tbl b','b.book_id=d.book_id','left')
            ->join('author_tbl a','a.author_id=b.author_name','left')
            ->join('language_tbl l','l.language_id=b.language','left')
            ->join('paperback_stock p','p.book_id=b.book_id','left')
            ->where('d.combo_id', $comboId)
            ->get()
            ->getResultArray();
    }


    public function getComboOrders($comboId)
    {
        return $this->db->table('bookfair_sale_or_return_orders o')
            ->select('
                o.order_id,
                o.combo_id,
                o.book_fair_name,
                o.sending_date,
                o.total_qty,
                o.status,

                bs.bookshop_name,
                c.pack_name AS combo_name
            ')
            ->join('pod_bookshop bs', 'bs.bookshop_id = o.bookshop_id', 'left')
            ->join('bookfair_combo_pack c', 'c.combo_id = o.combo_id', 'left')
            ->where('o.combo_id', $comboId)
            ->orderBy('o.order_id', 'DESC')
            ->get()
            ->getResultArray();
    }


   public function getComboBookOrders($bookId)
    {
        return $this->db->table('bookfair_sale_or_return_order_details d')
            ->select('
                d.*,
                b.book_title,
                a.author_name,
                bs.bookshop_name,
                o.book_fair_name
            ')
            ->join('book_tbl b', 'b.book_id = d.book_id', 'left')
            ->join('author_tbl a', 'a.author_id = b.author_name', 'left')
            ->join('pod_bookshop bs', 'bs.bookshop_id = d.bookshop_id', 'left')
            ->join('bookfair_sale_or_return_orders o', 'o.order_id = d.order_id', 'left')
            ->where('d.book_id', $bookId)
            ->get()
            ->getResultArray();
    }
    function combopackupload($books, $comboName)
    {
        if (empty($books)) {
            return [
                'status' => 0,
                'message' => 'No books provided'
            ];
        }

        $total_quantity = 0;
        $no_of_title    = count($books);

        foreach ($books as $b) {
            if (empty($b['book_id']) || empty($b['quantity'])) {
                log_message('error', 'Missing data in combopackupload');
                continue;
            }

            $total_quantity += (int) $b['quantity'];
        }

        // 🔹 Insert into bookfair_combo_pack (ONLY ONCE)
        $comboPackData = [
            'pack_name'       =>  $comboName, // change if dynamic
            'no_of_title'     => $no_of_title,
            'total_quantity'  => $total_quantity,
            'created_date'    => date('Y-m-d H:i:s')
        ];

        $this->db->table('bookfair_combo_pack')->insert($comboPackData);

        // ✅ Get last inserted combo ID
        $combo_id = $this->db->insertID();

        // 🔹 Insert combo pack details
        foreach ($books as $b) {

            if (empty($b['book_id']) || empty($b['quantity'])) {
                continue;
            }

            $detailData = [
                'combo_id'      => $combo_id,
                'book_id'       => $b['book_id'],
                'default_value' => $b['quantity'],
                'created_date'  => date('Y-m-d H:i:s')
            ];

            $this->db->table('bookfair_combo_pack_details')->insert($detailData);
        }

        return [
            'status'   => 1,
            'combo_id' => $combo_id,
            'message'  => 'Combo Pack created successfully'
        ];
    }
}

