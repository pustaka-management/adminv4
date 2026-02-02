<?php

namespace App\Models;

use CodeIgniter\Model;

class PustakapaperbackModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // ONLINE ORDERS 
    public function onlineProgressBooks()
    {
        $sql = "SELECT 
                    author_tbl.author_name as author_name,
                    pod_order.order_id as online_order_id,
                    book_tbl.book_title,
                    pod_order.user_id,
                    pod_order_details.book_id,
                    pod_order_details.quantity,
                    pod_order_details.order_date,
                    paperback_stock.quantity as qty,
                    paperback_stock.stock_in_hand,
                    paperback_stock.bookfair,
                    paperback_stock.bookfair2,
                    paperback_stock.bookfair3,
                    paperback_stock.bookfair4,
                    paperback_stock.bookfair5,
                    paperback_stock.lost_qty,
                    users_tbl.username,
                    users_tbl.city
                FROM 
                    pod_order
                JOIN users_tbl ON pod_order.user_id = users_tbl.user_id
                JOIN pod_order_details ON pod_order.user_id = pod_order_details.user_id
                    AND pod_order.order_id = pod_order_details.order_id
                JOIN book_tbl ON pod_order_details.book_id = book_tbl.book_id 
                JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id 
                LEFT JOIN paperback_stock ON paperback_stock.book_id = pod_order_details.book_id 
                WHERE pod_order.user_id != 0 
                    AND pod_order_details.status = 0
                ORDER BY pod_order_details.order_date ASC";

        $query = $this->db->query($sql);
        $data['in_progress'] = $query->getResultArray();

        $sql = "SELECT 
                    author_tbl.author_name as author_name,
                    pod_order.order_id as online_order_id,
                    book_tbl.book_title,
                    pod_order.user_id,
                    pod_order_details.book_id,
                    pod_order_details.quantity,
                    pod_order_details.order_date,
                    pod_order_details.ship_date,
                    pod_order_details.tracking_url,
                    paperback_stock.stock_in_hand as total_quantity,
                    paperback_stock.bookfair,
                    users_tbl.username,
                    users_tbl.city
                FROM pod_order
                JOIN users_tbl ON pod_order.user_id = users_tbl.user_id
                JOIN pod_order_details ON pod_order.user_id = pod_order_details.user_id
                    AND pod_order.order_id = pod_order_details.order_id
                JOIN book_tbl ON pod_order_details.book_id = book_tbl.book_id 
                JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id 
                LEFT JOIN paperback_stock ON paperback_stock.book_id = pod_order_details.book_id 
                WHERE pod_order.user_id != 0 
                    AND pod_order_details.status = 1
                    AND pod_order_details.ship_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ORDER BY pod_order_details.order_date DESC";
        $query = $this->db->query($sql);
        $data['completed'] = $query->getResultArray();

        $sql = "SELECT 
                    author_tbl.author_name as author_name,
                    pod_order.order_id as online_order_id,
                    book_tbl.book_title,
                    pod_order.user_id,
                    pod_order_details.book_id,
                    pod_order_details.quantity,
                    pod_order_details.order_date,
                    pod_order_details.date,
                    paperback_stock.stock_in_hand as total_quantity,
                    paperback_stock.bookfair,
                    users_tbl.username,
                    users_tbl.city
                FROM pod_order
                JOIN users_tbl ON pod_order.user_id = users_tbl.user_id
                JOIN pod_order_details ON pod_order.user_id = pod_order_details.user_id
                    AND pod_order.order_id = pod_order_details.order_id
                JOIN book_tbl ON pod_order_details.book_id = book_tbl.book_id 
                JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id 
                LEFT JOIN paperback_stock ON paperback_stock.book_id = pod_order_details.book_id 
                WHERE pod_order.user_id != 0 
                    AND pod_order_details.status = 2
                ORDER BY pod_order_details.date DESC";
        $query = $this->db->query($sql);
        $data['cancel'] = $query->getResultArray();

        $sql = "SELECT 
                    author_tbl.author_name as author_name,
                    pod_order.order_id as online_order_id,
                    book_tbl.book_title,
                    pod_order.user_id,
                    pod_order_details.book_id,
                    pod_order_details.quantity,
                    pod_order_details.order_date,
                    pod_order_details.ship_date,
                    pod_order_details.tracking_url,
                    paperback_stock.stock_in_hand as total_quantity,
                    paperback_stock.bookfair,
                    users_tbl.username,
                    users_tbl.city
                FROM pod_order
                JOIN users_tbl ON pod_order.user_id = users_tbl.user_id
                JOIN pod_order_details ON pod_order.user_id = pod_order_details.user_id
                    AND pod_order.order_id = pod_order_details.order_id
                JOIN book_tbl ON pod_order_details.book_id = book_tbl.book_id 
                JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id 
                LEFT JOIN paperback_stock ON paperback_stock.book_id = pod_order_details.book_id 
                WHERE pod_order.user_id != 0 
                    AND pod_order_details.status = 1
                ORDER BY pod_order_details.order_date DESC";
        $query = $this->db->query($sql);
        $data['completed_all'] = $query->getResultArray();

        return $data;
    }

    // MARK SHIPPED 
    public function onlineMarkShipped($book_id,$online_order_id,$tracking_id,$tracking_url)
    {
        $select_online_order_id = "SELECT quantity FROM pod_order_details WHERE book_id = ? AND order_id = ?";
        $tmp = $this->db->query($select_online_order_id, [$book_id, $online_order_id]);
        $record = $tmp->getRowArray();

        $qty = $record['quantity'];

        $update_sql = "UPDATE paperback_stock SET quantity = quantity - ?, stock_in_hand = stock_in_hand - ? WHERE book_id = ?";
        $this->db->query($update_sql, [$qty, $qty, $book_id]);

        $update_data = [
            "status" => 1,
            "ship_date" => date('Y-m-d H:i:s'),
            "tracking_id" => $tracking_id,
            "tracking_url" => $tracking_url,
        ];
        $builder = $this->db->table('pod_order_details');
        $builder->where(['order_id' => $online_order_id, 'book_id' => $book_id]);
        $builder->update($update_data);

        // insert into ledger
        $stock_sql = "SELECT pod_order_details.*, book_tbl.*, pod_order_details.quantity as quantity,
                      paperback_stock.quantity as current_stock
                      FROM pod_order_details, book_tbl, paperback_stock
                      WHERE pod_order_details.book_id = book_tbl.book_id
                      AND paperback_stock.book_id = pod_order_details.book_id
                      AND book_tbl.book_id = ? AND pod_order_details.order_id = ?";
        $temp = $this->db->query($stock_sql, [$book_id, $online_order_id]);
        $stock = $temp->getRowArray();

        $stock_data = [
            'book_id' => $book_id,
            'order_id' => $stock['order_id'],
            'author_id' => $stock['author_name'],
            'copyright_owner' => $stock['paper_back_copyright_owner'],
            'description' => "Online Sales",
            'channel_type' => "ONL",
            'stock_out' => $stock['quantity'],
            'transaction_date' => date('Y-m-d H:i:s'),
        ];
        $this->db->table('pustaka_paperback_stock_ledger')->insert($stock_data);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    // MARK CANCEL 
    public function onlineMarkCancel($online_order_id,$book_id)
    {
        
        $update_data = [
            "status" => 2,
            "date" => date('Y-m-d'),
        ];
        $builder = $this->db->table('pod_order_details');
        $builder->where(['order_id' => $online_order_id, 'book_id' => $book_id]);
        $builder->update($update_data);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    // ORDER SHIP
    public function onlineOrdership($online_order_id, $book_id)
    {
        $sql = "SELECT * 
                FROM pod_order_details 
                WHERE order_id = ? AND book_id = ?";
        $query = $this->db->query($sql, [$online_order_id, $book_id]);
        return $query->getRowArray();
    }

    // Online BULK ORDERS 
    public function getOnlinebulkOrdersdetails($bulk_order_id)
    {
        $sql = "SELECT 
                    author_tbl.author_name as author_name,
                    pod_order.order_id as online_order_id,
                    book_tbl.book_title,
                    pod_order.user_id,
                    pod_order_details.book_id,
                    pod_order_details.quantity,
                    pod_order_details.order_date,
                    paperback_stock.quantity as qty,
                    paperback_stock.stock_in_hand,
                    paperback_stock.bookfair,
                    paperback_stock.bookfair2,
                    paperback_stock.bookfair3,
                    paperback_stock.bookfair4,
                    paperback_stock.bookfair5,
                    paperback_stock.lost_qty,
                    users_tbl.username,
                    users_tbl.city,
                    pod_order_details.price
                FROM pod_order
                JOIN users_tbl ON pod_order.user_id = users_tbl.user_id
                JOIN pod_order_details ON pod_order.user_id = pod_order_details.user_id
                    AND pod_order.order_id = pod_order_details.order_id
                JOIN book_tbl ON pod_order_details.book_id = book_tbl.book_id 
                JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id 
                LEFT JOIN paperback_stock ON paperback_stock.book_id = pod_order_details.book_id 
                WHERE pod_order.user_id != 0 
                    AND pod_order_details.status = 0
                    AND pod_order.order_id = ?";
        $query = $this->db->query($sql, [$bulk_order_id]);
        return $query->getResultArray();
    }

    public function onlineBulkOrdershipment($online_order_id, $book_ids, $tracking_id, $tracking_url)
    {
        foreach ($book_ids as $book_id) {
            $select_offline_order_id = "SELECT quantity FROM pod_order_details WHERE book_id = ? AND order_id = ?";
            $tmp = $this->db->query($select_offline_order_id, [$book_id, $online_order_id]);

            if ($tmp->getNumRows() == 0) {
                continue;
            }

            $record = $tmp->getRowArray();
            $qty = $record['quantity'];

            $update_sql = "UPDATE paperback_stock SET quantity = quantity - ?, stock_in_hand = stock_in_hand - ? WHERE book_id = ?";
            $this->db->query($update_sql, [$qty, $qty, $book_id]);

            $update_data = [
                "status" => 1,
                "tracking_id" => $tracking_id,
                "tracking_url" => $tracking_url,
                "ship_date" => date('Y-m-d'),
            ];
            $this->db->table('pod_order_details')->where(['order_id' => $online_order_id, 'book_id' => $book_id])->update($update_data);

            $stock_sql = "SELECT pod_order_details.*, book_tbl.*, pod_order_details.quantity as quantity,
                          paperback_stock.quantity as current_stock
                          FROM pod_order_details, book_tbl, paperback_stock
                          WHERE pod_order_details.book_id = book_tbl.book_id
                          AND paperback_stock.book_id = pod_order_details.book_id
                          AND book_tbl.book_id = ? AND pod_order_details.order_id = ?";
            $temp = $this->db->query($stock_sql, [$book_id, $online_order_id]);

            if ($temp->getNumRows() == 0) {
                continue;
            }

            $stock = $temp->getRowArray();

            $stock_data = [
                'book_id' => $stock['book_id'],
                'order_id' => $stock['order_id'],
                'author_id' => $stock['author_name'],
                'copyright_owner' => $stock['paper_back_copyright_owner'],
                'description' => "Online Sales",
                'channel_type' => "ONL",
                'stock_out' => $stock['quantity'],
                'transaction_date' => date('Y-m-d H:i:s'),
            ];
            $this->db->table('pustaka_paperback_stock_ledger')->insert($stock_data);
        }

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }
    public function onlineOrderDetails($order_id)
    {
        $db = \Config\Database::connect();

        $sql = "SELECT users_tbl.username, user_address.*, pod_order.order_id, pod_order.shipping_charges,
                    pod_order_details.order_date, pod_order_details.tracking_url, 
                    pod_order_details.tracking_id, pod_order_details.ship_date
                FROM users_tbl, user_address, pod_order, pod_order_details
                WHERE user_address.user_id = users_tbl.user_id 
                AND pod_order_details.order_id = pod_order.order_id 
                AND pod_order.user_id = users_tbl.user_id 
                AND pod_order.order_id = ?";

        $query = $db->query($sql, [$order_id]);
        $data['details'] = $query->getResultArray()[0] ?? [];

        $sql = "SELECT 
                    pod_order_details.*, 
                    book_tbl.book_title, 
                    author_tbl.author_name, 
                    book_tbl.paper_back_inr,
                    paperback_stock.stock_in_hand AS total_quantity,
                    paperback_stock.bookfair
                FROM 
                    pod_order
                JOIN 
                    pod_order_details ON pod_order_details.order_id = pod_order.order_id
                JOIN 
                    book_tbl ON book_tbl.book_id = pod_order_details.book_id
                JOIN 
                    author_tbl ON author_tbl.author_id = book_tbl.author_name
                LEFT JOIN 
                    paperback_stock ON paperback_stock.book_id = book_tbl.book_id
                WHERE 
                    pod_order.order_id = ?";

        $query = $db->query($sql, [$order_id]);
        $data['list'] = $query->getResultArray();

        return $data;
    }
    // Offline orders
    public function offlinePaperbackBooks()
    {
        $sql = "SELECT book_tbl.book_id, book_tbl.book_title, book_tbl.regional_book_title,
                    book_tbl.paper_back_pages as number_of_page, book_tbl.paper_back_inr, 
                    author_tbl.author_name
                FROM book_tbl, author_tbl 
                WHERE author_tbl.author_id = book_tbl.author_name
                AND book_tbl.paper_back_flag = 1";

        $query = $this->db->query($sql);

        return $query->getResultArray();
    }
    public function offlineSelectedBooksList($selected_book_list)
    {
        // Convert string into array
        $bookIDs = array_filter(array_map('trim', explode(',', $selected_book_list)));

        if (empty($bookIDs)) {
            return [];
        }

        // Prepare placeholders for binding
        $placeholders = implode(',', array_fill(0, count($bookIDs), '?'));

        $sql = "SELECT 
                    book_tbl.book_id AS bookID, 
                    book_tbl.book_title, 
                    book_tbl.regional_book_title,
                    book_tbl.paper_back_readiness_flag,
                    book_tbl.paper_back_pages as number_of_page, 
                    book_tbl.paper_back_inr, 
                    author_tbl.author_name, 
                    author_tbl.author_id,
                    paperback_stock.*,
                    (SELECT SUM(quantity) FROM pustaka_paperback_books WHERE book_id = book_tbl.book_id AND completed_flag = 0) AS Qty,
                    CASE 
                        WHEN indesign_processing.book_id = book_tbl.book_id AND indesign_processing.completed_flag = 0 THEN 'In Processing'
                        ELSE 'Not Processing'
                    END AS indesign_status
                FROM 
                    book_tbl
                JOIN 
                    author_tbl ON author_tbl.author_id = book_tbl.author_name
                LEFT JOIN 
                    paperback_stock ON paperback_stock.book_id = book_tbl.book_id
                LEFT JOIN 
                    indesign_processing ON indesign_processing.book_id = book_tbl.book_id
                WHERE 
                    book_tbl.paper_back_flag = 1
                    AND book_tbl.book_id IN ($placeholders)";

        $query = $this->db->query($sql, $bookIDs);
                
        return $query->getResultArray();   
    }

    
    public function offlineOrderbooksDetailsSubmit()
    {
        $request = \Config\Services::request(); 
        $num_of_books = $request->getPost('num_of_books');
        
        
        $order_id = time();
        $data = [
            'order_id'       => $order_id,
            'customer_name'  => trim($request->getPost('customer_name')),
            'email'          => trim($request->getPost('email')), 
            'payment_type'   => trim($request->getPost('payment_type')),
            'payment_status' => trim($request->getPost('payment_status')),
            'courier_charges'=> trim($request->getPost('courier_charges')),
            'address'        => trim($request->getPost('address')),
            'remarks'        => trim($request->getPost('remarks')),
            'mobile_no'      => trim($request->getPost('mobile_no')),
            'ship_date'      => $request->getPost('ship_date'),
            'order_date'     => date('Y-m-d H:i:s'),
            'city'           => trim($request->getPost('city')),
        ];
        $this->db->table('pustaka_offline_orders')->insert($data);

        for ($i = 0; $i < $num_of_books; $i++) {
            $tmp  = 'book_id' . $i;
            $tmp1 = 'bk_qty' . $i;
            $tmp2 = 'book_dis' . $i;
            $tmp3 = 'tot_amt' . $i;

            $book_id  = $request->getPost($tmp);
            $book_qty = $request->getPost($tmp1);
            $book_dis = $request->getPost($tmp2);
            $tot_amt  = $request->getPost($tmp3);
             
            $data = [
                'offline_order_id' => $order_id,
                'book_id'          => $book_id,
                'quantity'         => $book_qty,
                'discount'         => $book_dis,
                'total_amount'     => $tot_amt,
                'ship_date'        => $request->getPost('ship_date'),
            ];

            $this->db->table('pustaka_offline_orders_details')->insert($data);
        }
    }

    public function offlineProgressBooks()
    {
        $data = [];

        // ---------------- IN PROGRESS ----------------
        $sql = "SELECT
                    author_tbl.author_name as author_name,
                    pustaka_offline_orders_details.quantity,
                    pustaka_offline_orders_details.ship_date,
                    pustaka_offline_orders_details.offline_order_id,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.quantity as qty,
                    paperback_stock.stock_in_hand,
                    paperback_stock.bookfair,
                    paperback_stock.bookfair2,
                    paperback_stock.bookfair3,
                    paperback_stock.bookfair4,
                    paperback_stock.bookfair5,
                    paperback_stock.lost_qty,
                    pustaka_offline_orders.*
                FROM
                    pustaka_offline_orders_details
                JOIN pustaka_offline_orders 
                    ON pustaka_offline_orders_details.offline_order_id=pustaka_offline_orders.order_id
                JOIN book_tbl 
                    ON pustaka_offline_orders_details.book_id = book_tbl.book_id
                JOIN author_tbl 
                    ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN paperback_stock 
                    ON paperback_stock.book_id = book_tbl.book_id
                WHERE pustaka_offline_orders_details.ship_status = 0
                ORDER BY pustaka_offline_orders_details.ship_date ASC";

        $query = $this->db->query($sql);
        $data['in_progress'] = $query->getResultArray();


        // ---------------- COMPLETED ----------------
        // $sql = "SELECT
        //             author_tbl.author_name AS author_name,
        //             pustaka_offline_orders_details.quantity,
        //             pustaka_offline_orders_details.ship_date AS shipped_date,
        //             pustaka_offline_orders_details.offline_order_id,
        //             pustaka_offline_orders_details.tracking_url AS url,
        //             book_tbl.book_id,
        //             book_tbl.book_title,
        //             paperback_stock.stock_in_hand AS total_quantity,
        //             paperback_stock.bookfair,
        //             pustaka_offline_orders.*,
        //             pustaka_offline_orders_details.ship_status
        //         FROM
        //             pustaka_offline_orders_details
        //         JOIN pustaka_offline_orders 
        //             ON pustaka_offline_orders_details.offline_order_id = pustaka_offline_orders.order_id
        //         JOIN book_tbl 
        //             ON pustaka_offline_orders_details.book_id = book_tbl.book_id
        //         JOIN author_tbl 
        //             ON book_tbl.author_name = author_tbl.author_id 
        //         LEFT JOIN paperback_stock 
        //             ON paperback_stock.book_id = book_tbl.book_id
        //         WHERE
        //             (pustaka_offline_orders_details.ship_status = 1 
        //              AND pustaka_offline_orders_details.ship_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY))
        //             OR (pustaka_offline_orders_details.ship_status = 1 
        //                 AND pustaka_offline_orders.payment_status = 'Pending')
        //         ORDER BY pustaka_offline_orders_details.ship_date DESC";

        // $query = $this->db->query($sql);
        // $data['completed'] = $query->getResultArray();

        $sql ="SELECT
                    author_tbl.author_name AS author_name,
                    pustaka_offline_orders_details.quantity,
                    pustaka_offline_orders_details.ship_date AS shipped_date,
                    pustaka_offline_orders_details.offline_order_id,
                    pustaka_offline_orders_details.tracking_url AS url,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.stock_in_hand AS total_quantity,
                    paperback_stock.bookfair,
                    pustaka_offline_orders.*,
                    pustaka_offline_orders_details.ship_status
                FROM
                    pustaka_offline_orders_details
                JOIN pustaka_offline_orders 
                    ON pustaka_offline_orders_details.offline_order_id = pustaka_offline_orders.order_id
                JOIN book_tbl 
                    ON pustaka_offline_orders_details.book_id = book_tbl.book_id
                JOIN author_tbl 
                    ON book_tbl.author_name = author_tbl.author_id 
                LEFT JOIN paperback_stock 
                    ON paperback_stock.book_id = book_tbl.book_id
                WHERE
                    pustaka_offline_orders_details.ship_status = 1
                    AND pustaka_offline_orders_details.ship_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ORDER BY pustaka_offline_orders_details.ship_date DESC"; 
        $query = $this->db->query($sql);
        $data['completed_30days'] = $query->getResultArray();
        
        $sql ="SELECT
                    author_tbl.author_name AS author_name,
                    pustaka_offline_orders_details.quantity,
                    pustaka_offline_orders_details.ship_date AS shipped_date,
                    pustaka_offline_orders_details.offline_order_id,
                    pustaka_offline_orders_details.tracking_url AS url,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.stock_in_hand AS total_quantity,
                    paperback_stock.bookfair,
                    pustaka_offline_orders.*,
                    pustaka_offline_orders_details.ship_status
                FROM
                    pustaka_offline_orders_details
                JOIN pustaka_offline_orders 
                    ON pustaka_offline_orders_details.offline_order_id = pustaka_offline_orders.order_id
                JOIN book_tbl 
                    ON pustaka_offline_orders_details.book_id = book_tbl.book_id
                JOIN author_tbl 
                    ON book_tbl.author_name = author_tbl.author_id 
                LEFT JOIN paperback_stock 
                    ON paperback_stock.book_id = book_tbl.book_id
                WHERE
                    pustaka_offline_orders_details.ship_status = 1
                    AND pustaka_offline_orders.payment_status = 'Pending'
                ORDER BY pustaka_offline_orders_details.ship_date DESC";

        $query = $this->db->query($sql);
        $data['pending_payments'] = $query->getResultArray();


        // ---------------- CANCEL ----------------
        $sql = "SELECT
                    author_tbl.author_name as author_name,
                    pustaka_offline_orders_details.quantity,
                    pustaka_offline_orders_details.date,
                    pustaka_offline_orders_details.offline_order_id,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.stock_in_hand as total_quantity,
                    paperback_stock.bookfair,
                    pustaka_offline_orders.*
                FROM
                    pustaka_offline_orders_details
                JOIN pustaka_offline_orders 
                    ON pustaka_offline_orders_details.offline_order_id=pustaka_offline_orders.order_id
                JOIN book_tbl 
                    ON pustaka_offline_orders_details.book_id = book_tbl.book_id
                JOIN author_tbl 
                    ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN paperback_stock 
                    ON paperback_stock.book_id = book_tbl.book_id
                WHERE pustaka_offline_orders_details.ship_status = 2
                ORDER BY pustaka_offline_orders_details.date DESC";

        $query = $this->db->query($sql);
        $data['cancel'] = $query->getResultArray();


        // ---------------- RETURN ----------------
        $sql = "SELECT
                    author_tbl.author_name as author_name,
                    pustaka_offline_orders_details.quantity,
                    pustaka_offline_orders_details.date,
                    pustaka_offline_orders_details.offline_order_id,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.stock_in_hand as total_quantity,
                    paperback_stock.bookfair,
                    pustaka_offline_orders.*
                FROM
                    pustaka_offline_orders_details
                JOIN pustaka_offline_orders 
                    ON pustaka_offline_orders_details.offline_order_id=pustaka_offline_orders.order_id
                JOIN book_tbl 
                    ON pustaka_offline_orders_details.book_id = book_tbl.book_id
                JOIN author_tbl 
                    ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN paperback_stock 
                    ON paperback_stock.book_id = book_tbl.book_id
                WHERE pustaka_offline_orders_details.ship_status = 3
                ORDER BY pustaka_offline_orders_details.date DESC";

        $query = $this->db->query($sql);
        $data['return'] = $query->getResultArray();


        // ---------------- COMPLETED ALL ----------------
        $sql = "SELECT
                    author_tbl.author_name as author_name,
                    pustaka_offline_orders_details.quantity,
                    pustaka_offline_orders_details.ship_date as shipped_date,
                    pustaka_offline_orders_details.offline_order_id,
                    pustaka_offline_orders_details.tracking_url as url,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.stock_in_hand as total_quantity,
                    paperback_stock.bookfair,
                    pustaka_offline_orders.*
                FROM
                    pustaka_offline_orders_details
                JOIN pustaka_offline_orders 
                    ON pustaka_offline_orders_details.offline_order_id=pustaka_offline_orders.order_id
                JOIN book_tbl 
                    ON pustaka_offline_orders_details.book_id = book_tbl.book_id
                JOIN author_tbl 
                    ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN paperback_stock 
                    ON paperback_stock.book_id = book_tbl.book_id
                WHERE pustaka_offline_orders_details.ship_status = 1
                ORDER BY pustaka_offline_orders_details.ship_date DESC";

        $query = $this->db->query($sql);
        $data['completed_all'] = $query->getResultArray();

        return $data;
    }
    public function offlineOrderShip($offline_order_id, $book_id)
    {
        $sql = "SELECT * 
                FROM pustaka_offline_orders_details 
                WHERE offline_order_id = ? AND book_id = ?";
        $query = $this->db->query($sql, [$offline_order_id, $book_id]);
        return $query->getRowArray();
    }

    // function offlineMarkShipped()
    // {
    //     $request = \Config\Services::request();
    //     $offline_order_id = $request->getPost('offline_order_id');
    //     $book_id = $request->getPost('book_id');
    //     $tracking_id = $request->getPost('tracking_id');
    //     $tracking_url = $request->getPost('tracking_url');
    //     $ship_type    = $request->getPost('ship_type');

    //     $select_offline_order_id = "SELECT quantity from pustaka_offline_orders_details WHERE book_id = $book_id AND offline_order_id = $offline_order_id";
    //     $tmp = $this->db->query($select_offline_order_id);
    //     $record = $tmp->getResultArray()[0];

    //     $qty = $record['quantity'];

    //     $update_sql = "UPDATE paperback_stock set quantity = quantity - " . $qty . ",stock_in_hand = stock_in_hand - " . $qty . " where book_id = " . $book_id;
    //     $tmp = $this->db->query($update_sql);

    //     $update_data = array(
    //         "ship_status" => 1,
    //         "tracking_id" => $tracking_id,
    //         "tracking_url" => $tracking_url,
    //         "ship_date" => date('Y-m-d'), //shipped date 
    //     );
    //     $this->db->table('pustaka_offline_orders_details')
    //         ->where(array('offline_order_id' => $offline_order_id, 'book_id' => $book_id))
    //         ->update($update_data);

    //     // inserting the record into pustaka_paperback_stock_ledger table 
    //     $stock_sql = "SELECT pustaka_offline_orders_details.*,book_tbl.* ,pustaka_offline_orders_details.quantity as quantity,
    //                 paperback_stock.quantity as current_stock
    //                 from pustaka_offline_orders_details,book_tbl,paperback_stock
    //                 where pustaka_offline_orders_details.book_id=book_tbl.book_id
    //                 and paperback_stock.book_id=pustaka_offline_orders_details.book_id
    //                 and book_tbl.book_id = " . $book_id . " AND pustaka_offline_orders_details.offline_order_id = '" . $offline_order_id . "'";
    //     $temp = $this->db->query($stock_sql);
    //     $stock = $temp->getResultArray()[0];

    //     $book_id = $stock['book_id'];
    //     $offline_order_id = $stock['offline_order_id'];
    //     $author_id = $stock['author_name'];
    //     $copyright_owner = $stock['paper_back_copyright_owner'];
    //     $description = "Offline Sales";
    //     $channel_type = "OFF";
    //     $stock_out = $stock['quantity'];

    //     $stock_data = array(
    //         'book_id' => $book_id,
    //         "order_id" => $offline_order_id,
    //         "author_id" => $author_id,
    //         "copyright_owner" => $copyright_owner,
    //         "description" => $description,
    //         "channel_type" => $channel_type,
    //         "stock_out" => $stock_out,
    //         'transaction_date' => date('Y-m-d H:i:s'),
    //     );
    //     $this->db->table('pustaka_paperback_stock_ledger')->insert($stock_data);

    //     if ($this->db->affectedRows() > 0)
    //         return 1;
    //     else
    //         return 0;
    // }

public function offlineMarkShipped()
{
    $request = \Config\Services::request();

    $offline_order_id = $request->getPost('offline_order_id');
    $book_id          = $request->getPost('book_id');
    $tracking_id      = $request->getPost('tracking_id');
    $tracking_url     = $request->getPost('tracking_url');
    $ship_type        = $request->getPost('ship_type'); // mail / nomail

    // ---- Quantity ----
    $qty = $this->db->query(
        "SELECT quantity FROM pustaka_offline_orders_details 
         WHERE offline_order_id=? AND book_id=?",
        [$offline_order_id, $book_id]
    )->getRowArray()['quantity'];

    // ---- Stock update ----
    $this->db->query(
        "UPDATE paperback_stock 
         SET quantity = quantity - ?, stock_in_hand = stock_in_hand - ?
         WHERE book_id = ?",
        [$qty, $qty, $book_id]
    );

    // ---- Order update ----
    $this->db->table('pustaka_offline_orders_details')
        ->where([
            'offline_order_id' => $offline_order_id,
            'book_id' => $book_id
        ])
        ->update([
            'ship_status' => 1,
            'tracking_id' => $tracking_id,
            'tracking_url' => $tracking_url,
            'ship_date' => date('Y-m-d')
        ]);

    // ---- Ledger ----
    $stock = $this->db->query(
        "SELECT d.*, b.*
         FROM pustaka_offline_orders_details d
         JOIN book_tbl b ON b.book_id = d.book_id
         WHERE d.offline_order_id=? AND d.book_id=?",
        [$offline_order_id, $book_id]
    )->getRowArray();

    $this->db->table('pustaka_paperback_stock_ledger')->insert([
        'book_id' => $book_id,
        'order_id' => $offline_order_id,
        'author_id' => $stock['author_name'],
        'copyright_owner' => $stock['paper_back_copyright_owner'],
        'description' => 'Offline Sales',
        'channel_type' => 'OFF',
        'stock_out' => $qty,
        'transaction_date' => date('Y-m-d H:i:s')
    ]);

    // ---- SHIP WITH MAIL ----
    if ($ship_type === 'mail') {

        $emailData = $this->db->query(
            "SELECT 
                    pustaka_offline_orders.customer_name, 
                    pustaka_offline_orders.email, 
                    book_tbl.book_title
                FROM pustaka_offline_orders_details 
                JOIN book_tbl ON book_tbl.book_id = pustaka_offline_orders_details.book_id
                JOIN pustaka_offline_orders ON pustaka_offline_orders.order_id = pustaka_offline_orders_details.offline_order_id
                WHERE pustaka_offline_orders_details.offline_order_id=? AND pustaka_offline_orders_details.book_id=?",
            [$offline_order_id, $book_id]
        )->getRowArray();

        $this->sendOfflineOrderShippedMail(
            $emailData['email'],
            $emailData['customer_name'],
            $emailData['book_title'],
            $tracking_id,
            $tracking_url
        );
    }

    return ($this->db->affectedRows() > 0) ? 1 : 0;
}

/* ===========================
   SHIPPING MAIL (STYLE SAME)
   =========================== */
private function sendOfflineOrderShippedMail(
    $customerEmail,
    $customerName,
    $bookTitle,
    $trackingId,
    $trackingUrl
) {
    $email = \Config\Services::email();

    $message = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'/>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'/>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
    <meta name='x-apple-disable-message-reformatting'/>

    <link rel='preconnect' href='https://fonts.googleapis.com'>
    <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
    <link href='https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
</head>

<body style='margin:0;padding:0;background:#ffffff;font-family:Quicksand,sans-serif;'>

<table width='100%' cellpadding='0' cellspacing='0' style='padding:20px 0;'>
<tr>
<td align='center'>

<table cellpadding='0' cellspacing='0' style='max-width:850px;min-width:350px;width:100%;background:#ffffff;'>

    <!-- Header -->
    <tr>
        <td style='padding:30px;text-align:center;
            background:linear-gradient(135deg,#4685ec 0%,#00296b 100%);'>
            <img src='https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/pustaka-logo-white-3x.png'
                 style='max-width:170px;width:33%;'>
        </td>
        <tr>
            <td style='padding:20px'>
                <h2 style='text-align:center'>Your Order Has Been Shipped!</h2>
                <p>Dear Sir/Madam,</p>
                <p>Your order has been <b>successfully shipped</b>.</p>
                <p><b>Book:</b> Book Name</p>
                <p>
                    <b>Tracking ID:</b> XXXXX<br>
                    <b>Tracking URL:</b> <a href='#'>Track Order</a>
                </p>
                <p>Regards,<br>Pustaka Team</p>
            </td>
        </tr>
            <td style='padding:25px;font-size:16px;color:#000000;'>
            {{CONTENT}}
        </td>
    </tr>

    <!-- Footer -->
    <tr>
        <td style='padding:20px;text-align:center;background:#f5f7fa;'>

            <!-- Social Icons -->
            <a href='https://www.facebook.com/yourpage' target='_blank' style='margin:0 8px;'>
                <img src='https://cdn-icons-png.flaticon.com/512/733/733547.png'
                     width='24' alt='Facebook'>
            </a>

            <a href='https://www.instagram.com/yourpage' target='_blank' style='margin:0 8px;'>
                <img src='https://cdn-icons-png.flaticon.com/512/733/733558.png'
                     width='24' alt='Instagram'>
            </a>

            <p style='margin-top:12px;font-size:13px;color:#666;'>
                © " . date('Y') . " Pustaka. All rights reserved.
            </p>

        </td>
    </tr>

</table>

</td>
</tr>
</table>

</body>
</html>";


    $email->setFrom('admin@pustaka.co.in', 'Pustaka');
    $email->setTo($customerEmail);
    $email->setSubject('Your Order Has Been Shipped');
    $email->setMessage($message);
    $email->setMailType('html');

    if (!$email->send()) {
        log_message('error', $email->printDebugger());
    } else {
        log_message('info', 'Offline ship mail sent');
    }
}


    function offlineMarkCancel()
    {
        $request = \Config\Services::request();
        $update_data = array(
            "ship_status" => 2,
            "date" => date('Y-m-d'),
        );
        $offline_order_id = $request->getPost('offline_order_id');
        $book_id = $request->getPost('book_id');

        $this->db->table('pustaka_offline_orders_details')
            ->where(array('offline_order_id' => $offline_order_id, 'book_id' => $book_id))
            ->update($update_data);

        if ($this->db->affectedRows() > 0)
            return 1;
        else
            return 0;
    }

    function offlineMarkPay()
    {
        $request = \Config\Services::request();
        $offline_order_id = $request->getPost('offline_order_id');

        $update_data = array(
            "payment_status" => trim('Paid'),
        );

        $this->db->table('pustaka_offline_orders')
            ->where(array('order_id' => $offline_order_id))
            ->update($update_data);

        if ($this->db->affectedRows() > 0)
            return 1;
        else
            return 0;
    }

    function offlineMarkReturn()
    {
        $request = \Config\Services::request();
        $offline_order_id = $request->getPost('offline_order_id');
        $book_id = $request->getPost('book_id');

        $select_offline_order_id = "SELECT quantity from pustaka_offline_orders_details WHERE book_id = $book_id AND offline_order_id = $offline_order_id";
        $tmp = $this->db->query($select_offline_order_id);
        $record = $tmp->getResultArray()[0];

        $qty = $record['quantity'];

        $update_sql = "UPDATE paperback_stock set quantity = quantity + " . $qty . ",stock_in_hand = stock_in_hand + " . $qty . " where book_id = " . $book_id;
        $tmp = $this->db->query($update_sql);

        $update_data = array(
            "ship_status" => 3,
            "date" => date('Y-m-d'),
        );
        $this->db->table('pustaka_offline_orders_details')
            ->where(array('offline_order_id' => $offline_order_id, 'book_id' => $book_id))
            ->update($update_data);

        // inserting the record into pustaka_paperback_stock_ledger table 
        $stock_sql = "SELECT pustaka_offline_orders_details.*,book_tbl.* ,pustaka_offline_orders_details.quantity as quantity,
                    paperback_stock.quantity as current_stock
                    from pustaka_offline_orders_details,book_tbl,paperback_stock
                    where pustaka_offline_orders_details.book_id=book_tbl.book_id
                    and paperback_stock.book_id=pustaka_offline_orders_details.book_id
                    and book_tbl.book_id = " . $book_id . " AND pustaka_offline_orders_details.offline_order_id = '" . $offline_order_id . "'";
        $temp = $this->db->query($stock_sql);
        $stock = $temp->getResultArray()[0];

        $book_id = $stock['book_id'];
        $offline_order_id = $stock['offline_order_id'];
        $author_id = $stock['author_name'];
        $copyright_owner = $stock['paper_back_copyright_owner'];
        $description = "Offline Return";
        $channel_type = "OFF";
        $stock_in = $stock['quantity'];
        $current_stock = $stock['current_stock'];

        $stock_data = array(
            'book_id' => $book_id,
            "order_id" => $offline_order_id,
            "author_id" => $author_id,
            "copyright_owner" => $copyright_owner,
            "description" => $description,
            "stock_in" => $stock_in,
            "channel_type" => $channel_type,
            'transaction_date' => date('Y-m-d H:i:s'),
        );
        $this->db->table('pustaka_paperback_stock_ledger')->insert($stock_data);

        if ($this->db->affectedRows() > 0)
            return 1;
        else
            return 0;
    }

    public function offlineOrderDetails($order_id)
    {
        // First query
        $sql = "SELECT pustaka_offline_orders.*,
                    pustaka_offline_orders_details.tracking_id,
                    pustaka_offline_orders_details.tracking_url
                FROM pustaka_offline_orders
                JOIN pustaka_offline_orders_details 
                    ON pustaka_offline_orders_details.offline_order_id = pustaka_offline_orders.order_id
                WHERE order_id = ?";
        $query = $this->db->query($sql, [$order_id]);
        $data['details'] = $query->getResultArray()[0] ?? [];

        // Second query
        $sql = "SELECT pustaka_offline_orders_details.*,
                    book_tbl.book_title,
                    author_tbl.author_name,
                    book_tbl.paper_back_inr,
                    book_tbl.paper_back_isbn,
                    pustaka_offline_orders.courier_charges
                FROM pustaka_offline_orders
                JOIN pustaka_offline_orders_details 
                    ON pustaka_offline_orders.order_id = pustaka_offline_orders_details.offline_order_id
                JOIN book_tbl 
                    ON book_tbl.book_id = pustaka_offline_orders_details.book_id
                JOIN author_tbl 
                    ON author_tbl.author_id = book_tbl.author_name
                WHERE pustaka_offline_orders.order_id = ?";
        $query = $this->db->query($sql, [$order_id]);
        $data['list'] = $query->getResultArray();

        return $data;
    }

    public function getBulkOrdersDetails($bulk_order_id)
    {
        $sql = "SELECT
                    author_tbl.author_name as author_name,
                    pustaka_offline_orders_details.*,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.quantity as qty,
                    paperback_stock.stock_in_hand,
                    paperback_stock.bookfair,
                    paperback_stock.bookfair2,
                    paperback_stock.bookfair3,
                    paperback_stock.bookfair4,
                    paperback_stock.bookfair5,
                    paperback_stock.lost_qty,
                    pustaka_offline_orders.*
                FROM
                    pustaka_offline_orders_details
                JOIN
                    pustaka_offline_orders ON pustaka_offline_orders_details.offline_order_id = pustaka_offline_orders.order_id
                JOIN
                    book_tbl ON pustaka_offline_orders_details.book_id = book_tbl.book_id
                JOIN
                    author_tbl ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN
                    paperback_stock ON paperback_stock.book_id = book_tbl.book_id
                WHERE
                    pustaka_offline_orders_details.ship_status = 0 
                    AND pustaka_offline_orders_details.offline_order_id = ?
                ORDER BY
                    pustaka_offline_orders_details.ship_date ASC";

        $query = $this->db->query($sql, [$bulk_order_id]);
        return $query->getResultArray();
    }
    public function bulkOrderShipment($offline_order_id, $book_ids, $tracking_id, $tracking_url)
    {
        foreach ($book_ids as $book_id) {
            // Fetch the quantity for the given book_id and offline_order_id
            $select_sql = "SELECT quantity FROM pustaka_offline_orders_details 
                           WHERE book_id = ? AND offline_order_id = ?";
            $tmp = $this->db->query($select_sql, [$book_id, $offline_order_id]);

            if ($tmp->getNumRows() == 0) {
                continue; // Skip if no record found
            }

            $record = $tmp->getRowArray();
            $qty = $record['quantity'];

            // Update the paperback_stock table
            $update_sql = "UPDATE paperback_stock 
                           SET quantity = quantity - ?, stock_in_hand = stock_in_hand - ? 
                           WHERE book_id = ?";
            $this->db->query($update_sql, [$qty, $qty, $book_id]);

            // Update the shipment details in pustaka_offline_orders_details table
            $update_data = [
                "ship_status"   => 1,
                "tracking_id"   => $tracking_id,
                "tracking_url"  => $tracking_url,
                "ship_date"     => date('Y-m-d'),
            ];

            $this->db->table('pustaka_offline_orders_details')
                     ->where(['offline_order_id' => $offline_order_id, 'book_id' => $book_id])
                     ->update($update_data);

            // Fetching data to insert into pustaka_paperback_stock_ledger table
            $stock_sql = "SELECT pod.*, book_tbl.*, 
                                 pod.quantity as quantity,
                                 paperback_stock.quantity as current_stock
                          FROM pustaka_offline_orders_details pod
                          JOIN book_tbl ON pod.book_id = book_tbl.book_id
                          JOIN paperback_stock ON paperback_stock.book_id = pod.book_id
                          WHERE book_tbl.book_id = ? AND pod.offline_order_id = ?";
            $temp = $this->db->query($stock_sql, [$book_id, $offline_order_id]);

            if ($temp->getNumRows() == 0) {
                continue; // Skip if no record found
            }

            $stock = $temp->getRowArray();

            // Prepare data for the ledger
            $stock_data = [
                'book_id'        => $stock['book_id'],
                'order_id'       => $stock['offline_order_id'],
                'author_id'      => $stock['author_name'],
                'copyright_owner'=> $stock['paper_back_copyright_owner'],
                'description'    => "Offline Sales",
                'channel_type'   => "OFF",
                'stock_out'      => $stock['quantity'],
                'transaction_date' => date('Y-m-d H:i:s'),
            ];

            // Insert data into the ledger
            $this->db->table('pustaka_paperback_stock_ledger')->insert($stock_data);
        }

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }
    public function paperbackLedgerDetails()
    {
        $uri = service('uri');
        $db = \Config\Database::connect();

        $book_id = $uri->getSegment(3);

        $sql = "SELECT 
                    book_tbl.book_id,
                    book_tbl.book_title,
                    book_tbl.regional_book_title,
                    book_tbl.paper_back_inr,
                    book_tbl.paper_back_copyright_owner,
                    author_tbl.author_name,
                    author_tbl.author_id,
                    paperback_stock.*
                FROM 
                    book_tbl
                JOIN 
                    author_tbl ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN 
                    paperback_stock ON book_tbl.book_id = paperback_stock.book_id
                WHERE 
                    book_tbl.book_id = $book_id";
        $query = $db->query($sql);
        $data['books'] = $query->getResultArray()[0] ?? [];

        $sql = "SELECT 
                    pustaka_paperback_stock_ledger.transaction_date,
                    pustaka_paperback_stock_ledger.*
                FROM 
                    pustaka_paperback_stock_ledger
                WHERE 
                    pustaka_paperback_stock_ledger.book_id = $book_id
                ORDER BY 
                    pustaka_paperback_stock_ledger.transaction_date ASC";
        $query = $db->query($sql);
        $data['list'] = $query->getResultArray();

        $sql = "SELECT 
                    author_transaction.order_date,
                    author_transaction.book_final_royalty_value_inr,
                    author_transaction.comments
                FROM 
                    author_transaction
                WHERE 
                    author_transaction.book_id = $book_id 
                    AND author_transaction.order_type = 15
                ORDER BY 
                    author_transaction.order_date ASC";
        $query = $db->query($sql);
        $data['author'] = $query->getResultArray();

        $sql = "SELECT 
                    author_transaction.order_date,
                    author_transaction.order_id,
                    author_transaction.book_final_royalty_value_inr,
                    author_transaction.comments,
                    author_transaction.order_type,
                    CASE 
                        WHEN author_transaction.order_type = 7 THEN 'Online'
                        WHEN author_transaction.order_type = 9 THEN 'Book Fair'
                        WHEN author_transaction.order_type = 10 THEN 'Offline'
                        WHEN author_transaction.order_type = 11 THEN 'Amazon'
                        WHEN author_transaction.order_type = 12 THEN 'Flipkart'
                        WHEN author_transaction.order_type = 14 THEN 'Book Seller'
                    END AS channel
                FROM 
                    author_transaction
                WHERE 
                    author_transaction.book_id = $book_id
                    AND author_transaction.order_type IN (7, 9, 10, 11, 12, 14) 
                ORDER BY 
                    author_transaction.order_date ASC";
        $query = $db->query($sql);
        $data['old_details'] = $query->getResultArray();

        $sql = "SELECT 
                    author_transaction.order_date,
                    author_transaction.order_id,
                    author_transaction.comments,
                    author_transaction.order_type,
                    pod_bookfair.*
                FROM 
                    author_transaction
                JOIN 
                    pod_bookfair ON pod_bookfair.order_id = author_transaction.order_id
                WHERE 
                    pod_bookfair.book_id = $book_id";
        $query = $db->query($sql);
        $data['book_fair'] = $query->getResultArray();

        return $data;
    }
    public function getBooksStock($book_id)
    {
        $db = db_connect(); 
        $sql = "SELECT book_tbl.*, author_tbl.author_name, author_tbl.author_id
                FROM book_tbl, author_tbl
                WHERE book_tbl.author_name = author_tbl.author_id
                AND book_tbl.book_id = ?";

        $query = $db->query($sql, [$book_id]);
        return $query->getRowArray(); 
    }
    public function updateQuantity($book_id, $quantity)
    {
        $updateData = [
            'book_id'     => $book_id,
            'quantity'    => $quantity,
            'order_id'    => time(),
            'order_date'  => date('Y-m-d H:i:s'),
        ];

        $builder = $this->db->table('pustaka_paperback_books');
        $builder->insert($updateData);

        return $this->db->affectedRows() > 0 ? 1 : 0;
    }

    public function getInitiatePrintStatus()
    {
        // Not Started
        $sql = "SELECT pustaka_paperback_books.*, book_tbl.book_title, author_tbl.author_name
                FROM pustaka_paperback_books, book_tbl, author_tbl
                WHERE author_tbl.author_id = book_tbl.author_name
                AND pustaka_paperback_books.book_id = book_tbl.book_id
                AND pustaka_paperback_books.start_flag = 0
                ORDER BY pustaka_paperback_books.order_date ASC";
        $query = $this->db->query($sql);
        $data['book_not_start'] = $query->getResultArray();

        // In Progress
        $sql = "SELECT 
                    pustaka_paperback_books.*, 
                    pustaka_paperback_books.book_id AS book_ID,
                    book_tbl.book_title, 
                    book_tbl.url_name, 
                    author_tbl.author_name,
                    indesign_processing.re_completed_flag,
                    indesign_processing.rework_flag
                FROM 
                    pustaka_paperback_books
                JOIN 
                    book_tbl ON pustaka_paperback_books.book_id = book_tbl.book_id
                JOIN 
                    author_tbl ON author_tbl.author_id = book_tbl.author_name
                LEFT JOIN 
                    indesign_processing ON pustaka_paperback_books.book_id = indesign_processing.book_id
                WHERE 
                    pustaka_paperback_books.start_flag = 1 
                    AND pustaka_paperback_books.completed_flag = 0
                ORDER BY 
                    pustaka_paperback_books.order_date ASC";
        $query = $this->db->query($sql);
        $data['in_progress'] = $query->getResultArray();

        // Completed (last 30 days)
        $sql = "SELECT
                    pustaka_paperback_books.*,
                    book_tbl.book_title,
                    author_tbl.author_name
                FROM
                    pustaka_paperback_books,
                    book_tbl,
                    author_tbl
                WHERE
                    author_tbl.author_id = book_tbl.author_name
                    AND pustaka_paperback_books.book_id = book_tbl.book_id
                    AND pustaka_paperback_books.start_flag = 1
                    AND pustaka_paperback_books.completed_flag = 1
                    AND pustaka_paperback_books.completed_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ORDER BY
                    pustaka_paperback_books.completed_date DESC";
        $query = $this->db->query($sql);
        $data['completed'] = $query->getResultArray();

        // All Completed
        $sql = "SELECT
                    pustaka_paperback_books.*,
                    book_tbl.book_title,
                    author_tbl.author_name
                FROM
                    pustaka_paperback_books,
                    book_tbl,
                    author_tbl
                WHERE
                    author_tbl.author_id = book_tbl.author_name
                    AND pustaka_paperback_books.book_id = book_tbl.book_id
                    AND pustaka_paperback_books.start_flag = 1
                    AND pustaka_paperback_books.completed_flag = 1
                ORDER BY
                    pustaka_paperback_books.completed_date DESC";
        $query = $this->db->query($sql);
        $data['completed_all'] = $query->getResultArray();

        return $data;
    }
    public function getPaperbackBooks()
    {
        $db = db_connect();
        $sql = "SELECT book_tbl.book_id, book_tbl.book_title, book_tbl.regional_book_title,
                    book_tbl.paper_back_pages AS number_of_page, book_tbl.paper_back_inr, author_tbl.author_name
                FROM book_tbl, author_tbl 
                WHERE author_tbl.author_id = book_tbl.author_name
                AND book_tbl.paper_back_flag = 1";
        
        $query = $db->query($sql);
        $data['paperback_book'] = $query->getResultArray();
        return $data;
    }
    public function getPaperbackSelectedBooksList($selected_book_list)
    {
        $db = db_connect();
        $sql = "SELECT 
                    book_tbl.book_id,
                    book_tbl.book_title,
                    book_tbl.regional_book_title,
                    book_tbl.paper_back_pages AS number_of_page,
                    book_tbl.paper_back_inr,
                    author_tbl.author_name,
                    (SELECT SUM(quantity) 
                    FROM pustaka_paperback_books 
                    WHERE book_id = book_tbl.book_id 
                    AND completed_flag = 0) AS Qty
                FROM 
                    book_tbl
                JOIN 
                    author_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE 
                    book_tbl.paper_back_flag = 1
                    AND book_tbl.book_id IN ($selected_book_list)";

        $query = $db->query($sql);

        return $query->getResultArray();
    }
    public function uploadQuantityList()
    {    
        $db = db_connect();
        $type = $_POST['type'];
        $num_of_books = $_POST['num_of_books'];

        $book_ids = array();
        $book_qtys = array();
        $bk_purposes = array();

        for ($i = 1; $i <= $num_of_books; $i++) {
            $tmp = 'book_id' . $i;
            $tmp1 = 'bk_qty' . $i;
            $book_ids[$i] = $_POST[$tmp];
            $book_qtys[$i] = $_POST[$tmp1];
        }

        if ($type == 'Initiate_print') {
            for ($i = 1; $i <= $num_of_books; $i++) {
                if ($book_qtys[$i] > 0) {
                    $data = array(
                        'book_id' => $book_ids[$i],
                        'quantity' => $book_qtys[$i],
                        'order_id' => time(),
                        'order_date' => date('Y-m-d H:i:s'),
                    );
                    $db->table('pustaka_paperback_books')->insert($data);
                }
            }
        } elseif ($type == 'Free_books') {
            for ($i = 1; $i <= $num_of_books; $i++) {
                $tmp2 = 'bk_purpose' . $i;
                $bk_purposes[$i] = $_POST[$tmp2];
            }
            for ($i = 1; $i <= $num_of_books; $i++) {
                if ($book_qtys[$i] > 0) {
                    $data = array(
                        'book_id' => $book_ids[$i],
                        'quantity' => $book_qtys[$i],
                        'purpose' => $bk_purposes[$i],
                        'order_id' => time(),
                        'order_date' => date('Y-m-d H:i:s'),
                        'type' => $_POST['print_type'],
                    );
                    $db->table('free_books_paperback')->insert($data);
                }
            }
        }

        if ($db->affectedRows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    public function editInitiatePrint()
    {
        $id = service('uri')->getSegment(3);

        $sql = "SELECT pustaka_paperback_books.*, book_tbl.book_title 
                FROM book_tbl, pustaka_paperback_books 
                WHERE book_tbl.book_id = pustaka_paperback_books.book_id 
                AND pustaka_paperback_books.id = $id";

        $query = $this->db->query($sql);

        return $query->getResultArray()[0];
    }

    public function editQuantity()
    {
        $db = \Config\Database::connect();

        $id = $_POST['id'];
        $update_data = array(
            'id' => $_POST['id'],
            'quantity' => $_POST['quantity'],
        );

        $db->table('pustaka_paperback_books')
        ->where('id', $id)
        ->update($update_data);

        if ($db->affectedRows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    public function deleteInitiatePrint($id)
    {
        $db = \Config\Database::connect();

        $sql = "DELETE FROM pustaka_paperback_books WHERE id = ?";
        $db->query($sql, [$id]);

        if ($db->affectedRows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    public function markStart($id,$type)
    {
        if ($type == 'Initiate_print') {
            $update_data = ["start_flag" => 1];
            $this->db->table('pustaka_paperback_books')->where('id', $id)->update($update_data);
        } else if ($type == 'Free_books') {
            $update_data = ["start_flag" => 1];
            $this->db->table('free_books_paperback')->where('id', $id)->update($update_data);
        }

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }
    public function markCoverComplete($id, $type)
    {
        if ($type == 'Initiate_print') {
            $update_data = ["cover_flag" => 1];
            $this->db->table('pustaka_paperback_books')->where('id', $id)->update($update_data);
        } else if ($type == 'Free_books') {
            $update_data = ["cover_flag" => 1];
            $this->db->table('free_books_paperback')->where('id', $id)->update($update_data);
        }

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }


    public function markContentComplete($id,$type)
    {
        
        if ($type == 'Initiate_print') {
            $update_data = ["content_flag" => 1];
            $this->db->table('pustaka_paperback_books')->where('id', $id)->update($update_data);
        } else if ($type == 'Free_books') {
            $update_data = ["content_flag" => 1];
            $this->db->table('free_books_paperback')->where('id', $id)->update($update_data);
        }

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markLaminationComplete($id,$type)
    {

        if ($type == 'Initiate_print') {
            $update_data = ["lamination_flag" => 1];
            $this->db->table('pustaka_paperback_books')->where('id', $id)->update($update_data);
        } else if ($type == 'Free_books') {
            $update_data = ["lamination_flag" => 1];
            $this->db->table('free_books_paperback')->where('id', $id)->update($update_data);
        }

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markBindingComplete($id, $type)
    {
        

        if ($type == 'Initiate_print') {
            $update_data = ["binding_flag" => 1];
            $this->db->table('pustaka_paperback_books')->where('id', $id)->update($update_data);
        } else if ($type == 'Free_books') {
            $update_data = ["binding_flag" => 1];
            $this->db->table('free_books_paperback')->where('id', $id)->update($update_data);
        }

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markFinalcutComplete($id, $type)
    {

        if ($type == 'Initiate_print') {
            $update_data = ["finalcut_flag" => 1];
            $this->db->table('pustaka_paperback_books')->where('id', $id)->update($update_data);
        } else if ($type == 'Free_books') {
            $update_data = ["finalcut_flag" => 1];
            $this->db->table('free_books_paperback')->where('id', $id)->update($update_data);
        }

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markQcComplete($id, $type)
    {

        if ($type == 'Initiate_print') {
            $update_data = ["qc_flag" => 1];
            $this->db->table('pustaka_paperback_books')->where('id', $id)->update($update_data);
        } else if ($type == 'Free_books') {
            $update_data = ["qc_flag" => 1];
            $this->db->table('free_books_paperback')->where('id', $id)->update($update_data);
        }

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markCompleted($id, $type)
    {

        $select_quantity = "SELECT quantity, order_id, book_id FROM pustaka_paperback_books WHERE id=".$id;
        $tmp    = $this->db->query($select_quantity);
        $record = $tmp->getResultArray()[0];

        $qty     = $record['quantity'];
        $book_id = $record['book_id'];

        $update_data = [
            "cover_flag"      => 1,
            "content_flag"    => 1,
            "lamination_flag" => 1,
            "binding_flag"    => 1,
            "finalcut_flag"   => 1,
            "qc_flag"         => 1,
            "completed_flag"  => 1,
            "completed_date"  => date('Y-m-d H:i:s')
        ];

        $this->db->table('pustaka_paperback_books')->where('id', $id)->update($update_data);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }
    // amazon
    public function getAmazonPaperbackOrder()
    {
        $amazon_paperback_sql = "SELECT book_tbl.url_name as url, amazon_paperback_books.*, book_tbl.*, author_tbl.* 
                                FROM amazon_paperback_books, book_tbl, author_tbl
                                WHERE amazon_paperback_books.book_id = book_tbl.book_id 
                                AND amazon_paperback_books.author_id = author_tbl.author_id";
        $amazon_paperback_query = $this->db->query($amazon_paperback_sql);
        return $amazon_paperback_query->getResultArray();
    }

    public function getAmazonSelectedBooksList($selected_book_list)
    {
        $sql = "SELECT book_tbl.url_name as url, amazon_paperback_books.*, book_tbl.*, author_tbl.*
                FROM amazon_paperback_books, book_tbl, author_tbl 
                WHERE amazon_paperback_books.book_id = book_tbl.book_id
                AND amazon_paperback_books.author_id = author_tbl.author_id 
                AND amazon_paperback_books.book_id IN ($selected_book_list)";
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }

    public function getAmazonStockDetails($selected_book_list)
    {
        $sql = "SELECT
                    book_tbl.book_id AS bookID,
                    book_tbl.url_name AS url,
                    amazon_paperback_books.*,
                    book_tbl.*,
                    author_tbl.*,
                    paperback_stock.*,
                    (SELECT SUM(quantity) FROM pustaka_paperback_books 
                        WHERE book_id = book_tbl.book_id AND completed_flag = 0) AS Qty
                FROM amazon_paperback_books
                LEFT JOIN book_tbl ON amazon_paperback_books.book_id = book_tbl.book_id
                LEFT JOIN author_tbl ON amazon_paperback_books.author_id = author_tbl.author_id
                LEFT JOIN paperback_stock ON amazon_paperback_books.book_id = paperback_stock.book_id
                WHERE amazon_paperback_books.book_id IN ($selected_book_list)";
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }

    public function amazonOrderbooksDetailsSubmit($num_of_books)
    {
        $j = 0;
        $book_ids = [];
        $book_qtys = [];

        for ($i = 1; $i <= $num_of_books; $i++) {
            $tmp = 'book_id' . $j;
            $tmp1 = 'quantity_details' . $j++;
            $book_ids[$i] = $_POST[$tmp];
            $book_qtys[$i] = $_POST[$tmp1];
        }

        for ($i = 1; $i <= $num_of_books; $i++) {
            $data = [
                'book_id' => $book_ids[$i],
                'quantity' => $book_qtys[$i],
                'amazon_order_id' => trim($_POST['order_id']),
                'shipping_type' => trim($_POST['ship_type']),
                'ship_date' => $_POST['ship_date'],
                'order_date' => date('Y-m-d H:i:s'),
            ];

            $builder = $this->db->table('amazon_paperback_orders');
            $builder->insert($data);
        }
    }

    public function amazonInProgressBooks()
    {
        $data = [];

        $sql = "SELECT 
                    author_tbl.author_name AS author_name,
                    amazon_paperback_orders.amazon_order_id,
                    amazon_paperback_orders.quantity,
                    amazon_paperback_orders.ship_date,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.quantity AS qty,
                    paperback_stock.stock_in_hand,
                    paperback_stock.bookfair,
                    paperback_stock.bookfair2,
                    paperback_stock.bookfair3,
                    paperback_stock.bookfair4,
                    paperback_stock.bookfair5,
                    paperback_stock.lost_qty,
                    paperback_stock.excess_qty
                FROM amazon_paperback_orders
                JOIN book_tbl ON amazon_paperback_orders.book_id = book_tbl.book_id
                JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN paperback_stock ON paperback_stock.book_id = book_tbl.book_id
                WHERE amazon_paperback_orders.ship_status = 0
                ORDER BY amazon_paperback_orders.ship_date ASC";
        $query = $this->db->query($sql);
        $data['in_progress'] = $query->getResultArray();

        $sql = "SELECT 
                    author_tbl.author_name AS author_name,
                    amazon_paperback_orders.amazon_order_id,
                    amazon_paperback_orders.quantity,
                    amazon_paperback_orders.ship_date,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.stock_in_hand AS total_quantity,
                    paperback_stock.bookfair
                FROM amazon_paperback_orders
                JOIN book_tbl ON amazon_paperback_orders.book_id = book_tbl.book_id
                JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN paperback_stock ON paperback_stock.book_id = book_tbl.book_id
                WHERE amazon_paperback_orders.ship_status = 1
                  AND amazon_paperback_orders.ship_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ORDER BY amazon_paperback_orders.ship_date DESC";
        $query = $this->db->query($sql);
        $data['completed'] = $query->getResultArray();

        $sql = "SELECT 
                    author_tbl.author_name AS author_name,
                    amazon_paperback_orders.amazon_order_id,
                    amazon_paperback_orders.quantity,
                    amazon_paperback_orders.date,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.stock_in_hand AS total_quantity,
                    paperback_stock.bookfair
                FROM amazon_paperback_orders
                JOIN book_tbl ON amazon_paperback_orders.book_id = book_tbl.book_id
                JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN paperback_stock ON paperback_stock.book_id = book_tbl.book_id
                WHERE amazon_paperback_orders.ship_status = 2
                ORDER BY amazon_paperback_orders.date DESC";
        $query = $this->db->query($sql);
        $data['cancel'] = $query->getResultArray();

        $sql = "SELECT 
                    author_tbl.author_name AS author_name,
                    amazon_paperback_orders.amazon_order_id,
                    amazon_paperback_orders.quantity,
                    amazon_paperback_orders.date,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.stock_in_hand AS total_quantity,
                    paperback_stock.bookfair
                FROM amazon_paperback_orders
                JOIN book_tbl ON amazon_paperback_orders.book_id = book_tbl.book_id
                JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN paperback_stock ON paperback_stock.book_id = book_tbl.book_id
                WHERE amazon_paperback_orders.ship_status = 3
                ORDER BY amazon_paperback_orders.date DESC";
        $query = $this->db->query($sql);
        $data['return'] = $query->getResultArray();

        $sql = "SELECT 
                    author_tbl.author_name AS author_name,
                    amazon_paperback_orders.amazon_order_id,
                    amazon_paperback_orders.quantity,
                    amazon_paperback_orders.ship_date,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.stock_in_hand AS total_quantity,
                    paperback_stock.bookfair
                FROM amazon_paperback_orders
                JOIN book_tbl ON amazon_paperback_orders.book_id = book_tbl.book_id
                JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN paperback_stock ON paperback_stock.book_id = book_tbl.book_id
                WHERE amazon_paperback_orders.ship_status = 1
                ORDER BY amazon_paperback_orders.ship_date DESC";
        $query = $this->db->query($sql);
        $data['completed_all'] = $query->getResultArray();

        return $data;
    }

    public function markShipped()
    {
        $amazon_order_id = $_POST['amazon_order_id'];
        $book_id = $_POST['book_id'];

        $select_amazon_order_id = "SELECT quantity FROM amazon_paperback_orders  
                                   WHERE book_id = $book_id 
                                   AND amazon_order_id = '$amazon_order_id'";
        $tmp = $this->db->query($select_amazon_order_id);
        $record = $tmp->getResultArray()[0];
        $qty = $record['quantity'];

        $update_sql = "UPDATE paperback_stock 
                       SET quantity = quantity - $qty,
                           stock_in_hand = stock_in_hand - $qty 
                       WHERE book_id = $book_id";
        $this->db->query($update_sql);

        $builder = $this->db->table('amazon_paperback_orders');
        $builder->where(['amazon_order_id' => $amazon_order_id, 'book_id' => $book_id])
                ->update(['ship_status' => 1]);

        $stock_sql = "SELECT amazon_paperback_orders.*, book_tbl.*, amazon_paperback_orders.quantity AS quantity,
                             paperback_stock.quantity AS current_stock
                      FROM amazon_paperback_orders, book_tbl, paperback_stock
                      WHERE amazon_paperback_orders.book_id = book_tbl.book_id
                      AND paperback_stock.book_id = amazon_paperback_orders.book_id
                      AND book_tbl.book_id = $book_id 
                      AND amazon_paperback_orders.amazon_order_id = '$amazon_order_id'";
        $temp = $this->db->query($stock_sql);
        $stock = $temp->getResultArray()[0];

        $stock_data = [
            'book_id' => $stock['book_id'],
            'order_id' => $stock['amazon_order_id'],
            'author_id' => $stock['author_name'],
            'copyright_owner' => $stock['paper_back_copyright_owner'],
            'description' => "Amazon Sales",
            'channel_type' => "AMZ",
            'stock_out' => $stock['quantity'],
            'transaction_date' => date('Y-m-d H:i:s'),
        ];
        $this->db->table('pustaka_paperback_stock_ledger')->insert($stock_data);

        return $this->db->affectedRows() > 0 ? 1 : 0;
    }

    public function markCancel()
    {
        $update_data = [
            "ship_status" => 2,
            "date" => date('Y-m-d'),
        ];
        $amazon_order_id = $_POST['amazon_order_id'];
        $book_id = $_POST['book_id'];

        $this->db->table('amazon_paperback_orders')
                 ->where(['amazon_order_id' => $amazon_order_id, 'book_id' => $book_id])
                 ->update($update_data);

        return $this->db->affectedRows() > 0 ? 1 : 0;
    }

    public function markReturn()
    {
        $amazon_order_id = $_POST['amazon_order_id'];
        $book_id = $_POST['book_id'];

        $select_amazon_order_id = "SELECT quantity FROM amazon_paperback_orders  
                                   WHERE book_id = $book_id 
                                   AND amazon_order_id = '$amazon_order_id'";
        $tmp = $this->db->query($select_amazon_order_id);
        $record = $tmp->getResultArray()[0];
        $qty = $record['quantity'];

        $update_sql = "UPDATE paperback_stock 
                       SET quantity = quantity + $qty,
                           stock_in_hand = stock_in_hand + $qty 
                       WHERE book_id = $book_id";
        $this->db->query($update_sql);

        $update_data = [
            "ship_status" => 3,
            "date" => date('Y-m-d'),
        ];
        $this->db->table('amazon_paperback_orders')
                 ->where(['amazon_order_id' => $amazon_order_id, 'book_id' => $book_id])
                 ->update($update_data);

        $stock_sql = "SELECT amazon_paperback_orders.*, book_tbl.*, amazon_paperback_orders.quantity AS quantity,
                             paperback_stock.quantity AS current_stock
                      FROM amazon_paperback_orders, book_tbl, paperback_stock
                      WHERE amazon_paperback_orders.book_id = book_tbl.book_id
                      AND paperback_stock.book_id = amazon_paperback_orders.book_id
                      AND book_tbl.book_id = $book_id 
                      AND amazon_paperback_orders.amazon_order_id = '$amazon_order_id'";
        $temp = $this->db->query($stock_sql);
        $stock = $temp->getResultArray()[0];

        $stock_data = [
            'book_id' => $stock['book_id'],
            'order_id' => $stock['amazon_order_id'],
            'author_id' => $stock['author_name'],
            'copyright_owner' => $stock['paper_back_copyright_owner'],
            'description' => "Amazon Return",
            'stock_in' => $stock['quantity'],
            'channel_type' => "AMZ",
            'transaction_date' => date('Y-m-d H:i:s'),
        ];
        $this->db->table('pustaka_paperback_stock_ledger')->insert($stock_data);

        return $this->db->affectedRows() > 0 ? 1 : 0;
    }
    public function amazonOrderDetails($order_id)
    {
        $db = \Config\Database::connect();

        // First query: Single order
        $sql1 = "SELECT * FROM amazon_paperback_orders WHERE amazon_order_id = ?";
        $query1 = $db->query($sql1, [$order_id]);
        $order = $query1->getResultArray()[0] ?? [];

        // Second query: Order with joins
        $sql2 = "SELECT amazon_paperback_orders.*, book_tbl.book_title,
                        author_tbl.author_name, book_tbl.paper_back_inr, book_tbl.regional_book_title
                 FROM amazon_paperback_orders, book_tbl, author_tbl
                 WHERE book_tbl.book_id = amazon_paperback_orders.book_id
                 AND author_tbl.author_id = book_tbl.author_name
                 AND amazon_paperback_orders.amazon_order_id = ?";
        $query2 = $db->query($sql2, [$order_id]);
        $details = $query2->getResultArray();

        return [
            'order'   => $order,
            'details' => $details
        ];
    }
    //authors order//
    public function getAuthorList()
    {
        $sql = "SELECT DISTINCT(author_tbl.author_name), author_tbl.author_id
                FROM book_tbl, author_tbl 
                WHERE book_tbl.status=1 
                AND book_tbl.paper_back_flag=1 
                AND book_tbl.author_name=author_tbl.author_id 
                AND book_tbl.type_of_book=1"; 
        $query = $this->db->query($sql);

        return $query->getResultArray();
    }
    public function getAuthorBooksList($author_id)
    {
        $sql = "SELECT book_tbl.book_id, book_tbl.book_title, book_tbl.regional_book_title,
                    author_tbl.author_name, book_tbl.language as language_name, book_tbl.url_name, 
                    CASE 
                        WHEN book_tbl.status = 0 THEN 'Inactive'
                        WHEN book_tbl.status = 1 THEN 'Active'
                        WHEN book_tbl.status = 2 THEN 'CANCELLED'
                        ELSE 'Invalid'
                    END AS bk_status, 
                    book_tbl.paper_back_inr, book_tbl.paper_back_pages, book_tbl.paper_back_weight 
                FROM book_tbl, author_tbl 
                WHERE 
                    book_tbl.author_name = :author_id: AND 
                    book_tbl.paper_back_flag = 1 AND
                    book_tbl.paper_back_inr > 0 AND
                    book_tbl.author_name = author_tbl.author_id"; 

        $query = $this->db->query($sql, ['author_id' => $author_id]);

        return $query->getResultArray();
    }
    public function getSelectedBooksList($selectedBookList)
    {
        $sql = "SELECT book_tbl.book_id, book_tbl.book_title, book_tbl.regional_book_title,
                    author_tbl.author_name, book_tbl.language AS language_name, book_tbl.url_name, 
                    CASE 
                        WHEN book_tbl.status = 0 THEN 'Inactive'
                        WHEN book_tbl.status = 1 THEN 'Active'
                        WHEN book_tbl.status = 2 THEN 'CANCELLED'
                        ELSE 'Invalid'
                    END AS bk_status, 
                    book_tbl.paper_back_inr, book_tbl.paper_back_pages, book_tbl.paper_back_weight, 
                    book_tbl.paper_back_copyright_owner   
                FROM book_tbl, author_tbl 
                WHERE book_tbl.paper_back_flag=1 
                AND book_tbl.author_name=author_tbl.author_id 
                AND book_tbl.paper_back_inr>0 
                AND book_tbl.book_id IN ($selectedBookList)"; 
        $query = $this->db->query($sql);

        return $query->getResultArray();
    }

    public function getAuthorAddress($author_id)
    {
        $sql = "SELECT * FROM author_tbl WHERE author_tbl.author_id=$author_id LIMIT 1"; 
        $query = $this->db->query($sql);

        return $query->getResultArray();
    }
    public function authorOrderBooksDetailsSubmit()
    {
        $request = \Config\Services::request(); 
        $numOfBooks = count($_POST) > 0 ? (int) count(array_filter($_POST, fn($k) => str_starts_with($k, 'bk_id'), ARRAY_FILTER_USE_KEY)) : 0;
        $orderId = time();
        $orderData = [
            'order_id'        => $orderId,
            'author_id'       => $request->getPost('author_id'),
            'user_id'         => $request->getPost('user_id'),
            'ship_date'       => $request->getPost('ship_date'),
            'order_date'      => date('Y-m-d H:i:s'),
            'order_status'    => 0,
            'payment_status'  => trim($request->getPost('payment_status')),
            'shipping_charges'=> trim($request ->getPost('shipping_charges')),
            'billing_name'    => trim($request->getPost('bill_name')),
            'billing_address' => trim($request->getPost('bill_addr')),
            'bill_mobile'     => trim($request->getPost('bill_mobile')),
            'bill_email'      => trim($request->getPost('bill_email')),
            'ship_name'       => trim($request->getPost('ship_name')),
            'ship_address'    => trim($request->getPost('ship_addr')),
            'ship_mobile'     => trim($request->getPost('ship_mobile')),
            'ship_email'      => trim($request->getPost('ship_email')),
            'sub_total'       => $request->getPost('sub_total'),
            'remarks'         => trim($request->getPost('remarks')),
        ];

        $this->db->table('pod_author_order')->insert($orderData);
        $i = 0;
        while ($request->getPost('bk_id' . $i) !== null) {
            $bookId  = $request->getPost('bk_id' . $i);
            $bookQty = $request->getPost('bk_qty' . $i);
            $bookDis = $request->getPost('bk_discount' . $i);
            $totAmt  = $request->getPost('tot_amt' . $i);

            if (!empty($bookId) && is_numeric($bookQty)) {
                $detailData = [
                    'order_id'   => $orderId,
                    'user_id'    => $request->getPost('user_id'),
                    'author_id'  => $request->getPost('author_id'),
                    'book_id'    => $bookId,
                    'quantity'   => $bookQty,
                    'discount'   => $bookDis,
                    'price'      => $totAmt,
                    'ship_date'  => $request->getPost('ship_date'),
                    'order_date' => date('Y-m-d H:i:s'),
                ];

                $this->db->table('pod_author_order_details')->insert($detailData);
            }

            $i++;
        }

        return [
            'order_id' => $orderId,
            'status'   => 'success',
        ];
    }


    public function getAuthorOrderDetails()
    {
        $db = \Config\Database::connect();
        $sql = "SELECT 
                    pod_author_order_details.*,
                    author_tbl.author_name,
                    book_tbl.book_title
                FROM 
                    pod_author_order_details
                LEFT JOIN 
                    author_tbl ON author_tbl.author_id = pod_author_order_details.author_id
                JOIN 
                    book_tbl ON pod_author_order_details.book_id = book_tbl.book_id
                WHERE 
                    pod_author_order_details.status = 0 
                    AND pod_author_order_details.start_flag = 0 
                ORDER BY  
                    pod_author_order_details.ship_date DESC";

        $query = $db->query($sql);
        $data['books'] = $query->getResultArray();

        $sql = "SELECT 
                    pod_author_order_details.*,
                    author_tbl.author_name,
                    book_tbl.book_title,
                    book_tbl.url_name, 
                    indesign_processing.rework_flag
                FROM 
                    pod_author_order_details
                LEFT JOIN 
                    author_tbl ON author_tbl.author_id = pod_author_order_details.author_id
                JOIN 
                    book_tbl ON pod_author_order_details.book_id = book_tbl.book_id
                LEFT JOIN 
                    indesign_processing ON pod_author_order_details.book_id = indesign_processing.book_id
                WHERE 
                    pod_author_order_details.start_flag = 1 
                    AND pod_author_order_details.completed_flag = 0 
                ORDER BY  
                    pod_author_order_details.ship_date DESC";

        $query = $db->query($sql);
        $data['start_books'] = $query->getResultArray();

        return $data;
    }
    public function authorOrderMarkStart($orderId ,$bookId)
    {

        $updateData = ["start_flag" => 1];
        $this->db->table('pod_author_order_details')
                 ->where(['order_id' => $orderId, 'book_id' => $bookId])
                 ->update($updateData);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markFilesReadyCompleted()
    {
        $orderId = $_POST['order_id'];
        $bookId = $_POST['book_id'];

        $updateData = ["files_ready_flag" => 1];
        $this->db->table('pod_author_order_details')
                 ->where(['order_id' => $orderId, 'book_id' => $bookId])
                 ->update($updateData);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markCoverCompleted($orderId, $bookId)
    {
        
        $updateData = ["cover_flag" => 1];

        $this->db->table('pod_author_order_details')
                ->where(['order_id' => $orderId, 'book_id' => $bookId])
                ->update($updateData);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }


    public function markContentCompleted($orderId, $bookId)
    {
        $updateData = ["content_flag" => 1];
        $this->db->table('pod_author_order_details')
                 ->where(['order_id' => $orderId, 'book_id' => $bookId])
                 ->update($updateData);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markLaminationCompleted($orderId, $bookId)
    {

        $updateData = ["lamination_flag" => 1];
        $this->db->table('pod_author_order_details')
                 ->where(['order_id' => $orderId, 'book_id' => $bookId])
                 ->update($updateData);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markBindingCompleted($orderId, $bookId)
    {

        $updateData = ["binding_flag" => 1];
        $this->db->table('pod_author_order_details')
                 ->where(['order_id' => $orderId, 'book_id' => $bookId])
                 ->update($updateData);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markFinalcutCompleted($orderId, $bookId)
    {
       
        $updateData = ["finalcut_flag" => 1];
        $this->db->table('pod_author_order_details')
                 ->where(['order_id' => $orderId, 'book_id' => $bookId])
                 ->update($updateData);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markQcCompleted($orderId, $bookId)
    {

        $updateData = ["qc_flag" => 1];
        $this->db->table('pod_author_order_details')
                 ->where(['order_id' => $orderId, 'book_id' => $bookId])
                 ->update($updateData);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function authorOrderMarkCompleted($orderId, $bookId)
    {
        $updateData = [
            "files_ready_flag" => 1,
            "cover_flag" => 1,
            "content_flag" => 1,
            "lamination_flag" => 1,
            "binding_flag" => 1,
            "finalcut_flag" => 1,
            "qc_flag" => 1,
            "completed_flag" => 1,
            "status" => 1
        ];
        $this->db->table('pod_author_order_details')
                 ->where(['order_id' => $orderId, 'book_id' => $bookId])
                 ->update($updateData);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function authorInProgressOrder()
    {
        $sql="SELECT pod_author_order.*, author_tbl.author_name,
                     COUNT(pod_author_order_details.book_id) AS comp_cnt,
                     (SELECT COUNT(pod_author_order_details.book_id) 
                      FROM pod_author_order_details 
                      WHERE pod_author_order.order_id=pod_author_order_details.order_id) AS tot_book
              FROM pod_author_order
              JOIN pod_author_order_details ON pod_author_order.order_id=pod_author_order_details.order_id
              JOIN author_tbl ON pod_author_order_details.author_id=author_tbl.author_id
              WHERE pod_author_order_details.completed_flag=1 AND pod_author_order.order_status=0
              GROUP BY pod_author_order.order_id 
              ORDER BY pod_author_order.ship_date DESC";
        $query = $this->db->query($sql);
        $data['in_progress'] = $query->getResultArray();


        $sql ="SELECT pod_author_order.*, 
                    author_tbl.author_name,
                    COUNT(pod_author_order_details.book_id) AS comp_cnt,
                    (SELECT COUNT(pod_author_order_details.book_id) 
                        FROM pod_author_order_details 
                        WHERE pod_author_order.order_id = pod_author_order_details.order_id) AS tot_book
                FROM pod_author_order
                JOIN pod_author_order_details 
                    ON pod_author_order.order_id = pod_author_order_details.order_id
                JOIN author_tbl 
                    ON pod_author_order_details.author_id = author_tbl.author_id
                WHERE pod_author_order_details.completed_flag = 1
                AND pod_author_order.order_status = 1
                AND pod_author_order.ship_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY pod_author_order.order_id
                ORDER BY pod_author_order.ship_date DESC";
        $query = $this->db->query($sql);
        $data['completed_30days'] = $query->getResultArray();

        $sql ="SELECT pod_author_order.*, 
                    author_tbl.author_name,
                    COUNT(pod_author_order_details.book_id) AS comp_cnt,
                    (SELECT COUNT(pod_author_order_details.book_id) 
                        FROM pod_author_order_details 
                        WHERE pod_author_order.order_id = pod_author_order_details.order_id) AS tot_book
                FROM pod_author_order
                JOIN pod_author_order_details 
                    ON pod_author_order.order_id = pod_author_order_details.order_id
                JOIN author_tbl 
                    ON pod_author_order_details.author_id = author_tbl.author_id
                WHERE pod_author_order_details.completed_flag = 1
                AND pod_author_order.order_status = 1
                AND pod_author_order.payment_status = 'Pending'
                GROUP BY pod_author_order.order_id
                ORDER BY pod_author_order.ship_date DESC";
        $query = $this->db->query($sql);
        $data['pending_payments'] = $query->getResultArray();

        $sql="SELECT pod_author_order.*, author_tbl.author_name,
                     COUNT(pod_author_order_details.book_id) AS comp_cnt,
                     (SELECT COUNT(pod_author_order_details.book_id) 
                      FROM pod_author_order_details 
                      WHERE pod_author_order.order_id=pod_author_order_details.order_id) AS tot_book
              FROM pod_author_order
              JOIN pod_author_order_details ON pod_author_order.order_id=pod_author_order_details.order_id
              JOIN author_tbl ON pod_author_order_details.author_id=author_tbl.author_id
              WHERE pod_author_order_details.completed_flag=1 AND pod_author_order.order_status=1
              GROUP BY pod_author_order.order_id 
              ORDER BY pod_author_order.ship_date DESC";
        $query = $this->db->query($sql);
        $data['completed_all'] = $query->getResultArray();

        $sqlDetail="SELECT pod_author_order.order_id, pod_author_order.user_id, pod_author_order.author_id, 
                           pod_author_order_details.book_id, pod_author_order_details.quantity, pod_author_order.tracking_id,
                           pod_author_order.tracking_url, pod_author_order_details.ship_date, 
                           author_tbl.author_name, book_tbl.book_title, pod_author_order_details.price, pod_author_order_details.discount
                    FROM pod_author_order, pod_author_order_details, book_tbl, author_tbl
                    WHERE pod_author_order_details.completed_flag=1 
                      AND pod_author_order.order_status=1 
                      AND pod_author_order.order_id=pod_author_order_details.order_id 
                      AND pod_author_order_details.author_id=author_tbl.author_id 
                      AND pod_author_order_details.book_id=book_tbl.book_id
                    ORDER BY pod_author_order.order_id DESC";
        $queryDetail = $this->db->query($sqlDetail);
        $data['completed_all_detail'] = $queryDetail->getResultArray();

        return $data;
    }

    public function authorInvoiceDetails($orderId)
    {
        $sql="SELECT * FROM pod_author_order WHERE order_id=$orderId";
        $query = $this->db->query($sql);
        $data['invoice'] = $query->getResultArray()[0];

        return $data;
    }

    public function createInvoice( $orderId)
    {
        $updateData = [
            "invoice_flag" => 1,
            "invoice_number" => $_POST['invoice_number'],
            "sub_total" => $_POST['sub_total'],
            "shipping_charges" => $_POST['shipping_charges'],
            "net_total" => $_POST['net_total'],
        ];

        $this->db->table('pod_author_order')
                 ->where('order_id', $orderId)
                 ->update($updateData);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function authorMarkCancel($orderId)
    {
        $updateData = ["order_status" => 2];

        $this->db->table('pod_author_order')
                 ->where('order_id', $orderId)
                 ->update($updateData);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function authorOrderShip()
    {
        $orderId = service('uri')->getSegment(3);
        $sql = "SELECT * FROM pod_author_order WHERE order_id = ?";
        $query = $this->db->query($sql, [$orderId]);

        return $query->getResultArray()[0];
    }
     public function authorOrderDetails($order_id)
    {
        if (empty($order_id)) {
            return ['order' => [], 'books' => []];
        }

        $db = \Config\Database::connect();

        $sql1 = "SELECT 
                    pod_author_order_details.*,
                    author_tbl.author_name,
                    book_tbl.book_title,
                    pod_author_order.*
                FROM 
                    pod_author_order_details
                LEFT JOIN 
                    author_tbl ON author_tbl.author_id = pod_author_order_details.author_id
                JOIN 
                    book_tbl ON pod_author_order_details.book_id = book_tbl.book_id
                JOIN 
                    pod_author_order ON pod_author_order.order_id = pod_author_order_details.order_id
                WHERE 
                    pod_author_order_details.order_id = ?";

        $query = $db->query($sql1, [$order_id]);
        $orderResult = $query->getResultArray();
        $data['order'] = !empty($orderResult) ? $orderResult[0] : [];


        $sql2 = "SELECT 
                    pod_author_order_details.*,
                    pod_author_order_details.discount as dis,
                    author_tbl.author_name,
                    book_tbl.book_title,
                    pod_author_order.*,
                    book_tbl.paper_back_inr
                FROM 
                    pod_author_order_details
                LEFT JOIN 
                    author_tbl ON author_tbl.author_id = pod_author_order_details.author_id
                JOIN 
                    book_tbl ON pod_author_order_details.book_id = book_tbl.book_id
                JOIN 
                    pod_author_order ON pod_author_order.order_id = pod_author_order_details.order_id
                WHERE 
                    pod_author_order_details.order_id = ?";

        $query = $db->query($sql2, [$order_id]);
        $data['books'] = $query->getResultArray();

        return $data;
    }


    public function authorMarkShipped($orderId,$trackingId,$trackingUrl)
    {
        $updateData = [
            "order_status" => 1,
            "tracking_id" => $trackingId,
            "tracking_url" => $trackingUrl,
        ];

        $this->db->table('pod_author_order')
                 ->where('order_id', $orderId)
                 ->update($updateData);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function authorMarkPay($orderId)
    {
        $updateData = ["payment_status" => 'Paid'];

        $this->db->table('pod_author_order')
                 ->where('order_id', $orderId)
                 ->update($updateData);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }
    public function amazonSummary($filter = 'all')
    {
        $currentMonth = date("m");

        if ($currentMonth >= 4) {
            // Current FY: Apr (this year) → Mar (next year)
            $fyStart = date("Y") . "-04-01";
            $fyEnd   = (date("Y") + 1) . "-03-31";

            // Previous FY
            $prevFyStart = (date("Y") - 1) . "-04-01";
            $prevFyEnd   = date("Y") . "-03-31";
        } else {
            // Current FY: Apr (last year) → Mar (this year)
            $fyStart = (date("Y") - 1) . "-04-01";
            $fyEnd   = date("Y") . "-03-31";

            // Previous FY
            $prevFyStart = (date("Y") - 2) . "-04-01";
            $prevFyEnd   = (date("Y") - 1) . "-03-31";
        }

        // ----------------- WHERE FILTER FOR CHART ONLY -----------------
        $where = "";
        if ($filter == "current_fy") {
            $where = "WHERE apo.ship_date BETWEEN '$fyStart' AND '$fyEnd'";
        } elseif ($filter == "previous_fy") {
            $where = "WHERE apo.ship_date BETWEEN '$prevFyStart' AND '$prevFyEnd'";
        }

        // ----------------- MONTHWISE CHART QUERY -----------------
        $sql = "SELECT 
                    DATE_FORMAT(apo.ship_date, '%Y-%m') AS order_month,
                    COUNT(DISTINCT b.book_id) AS total_titles,
                    SUM(apo.quantity * b.cost) AS total_mrp
                FROM amazon_paperback_orders apo
                JOIN book_tbl b ON apo.book_id = b.book_id
                $where
                GROUP BY DATE_FORMAT(apo.ship_date, '%Y-%m')
                ORDER BY order_month ASC";

        $query = $this->db->query($sql);
        $data['chart'] = $query->getResultArray();

        // individual summaries
        $sql0="SELECT COUNT(*) AS completed_orders, COUNT(DISTINCT b.book_id) AS total_titles 
            FROM amazon_paperback_orders apo
            JOIN book_tbl b ON apo.book_id = b.book_id
            WHERE apo.ship_status = 0";
        $query0 = $this->db->query($sql0);
        $data['in_progress'] = $query0->getResultArray();

        $sql1="SELECT COUNT(*) AS completed_orders, COUNT(DISTINCT b.book_id) AS total_titles 
            FROM amazon_paperback_orders apo
            JOIN book_tbl b ON apo.book_id = b.book_id
            WHERE apo.ship_status = 1";
        $query1 = $this->db->query($sql1);
        $data['completed'] = $query1->getResultArray();

        $sql2="SELECT COUNT(*) AS cancel_orders, COUNT(DISTINCT b.book_id) AS total_titles 
            FROM amazon_paperback_orders apo
            JOIN book_tbl b ON apo.book_id = b.book_id
            WHERE apo.ship_status = 2";
        $query2 = $this->db->query($sql2);
        $data['cancelled'] = $query2->getResultArray();

        $sql3="SELECT COUNT(*) AS return_orders, COUNT(DISTINCT b.book_id) AS total_titles 
            FROM amazon_paperback_orders apo
            JOIN book_tbl b ON apo.book_id = b.book_id
            WHERE apo.ship_status = 3";
        $query3 = $this->db->query($sql3);
        $data['return'] = $query3->getResultArray();

        return $data;
    }
    public function onlineSummary($filter = 'all')
    {
        // Financial Year
        $year = date("Y");

        if (date("m") >= 4) {
            $current_fy_start = $year . "-04-01";
            $current_fy_end   = ($year + 1) . "-03-31";
        } else {
            $current_fy_start = ($year - 1) . "-04-01";
            $current_fy_end   = $year . "-03-31";
        }

        $prev_fy_start = date("Y-m-d", strtotime("-1 year", strtotime($current_fy_start)));
        $prev_fy_end   = date("Y-m-d", strtotime("-1 year", strtotime($current_fy_end)));
        $where_chart = "";

        switch ($filter) {
            case "month":
                $where_chart = "
                    AND MONTH(pod_order_details.order_date) = MONTH(CURDATE()) 
                    AND YEAR(pod_order_details.order_date) = YEAR(CURDATE()) 
                ";
                break;

            case "this_year":
                $where_chart = "
                    AND pod_order_details.order_date BETWEEN '$current_fy_start' AND '$current_fy_end'
                ";
                break;

            case "prev_year":
                $where_chart = "
                    AND pod_order_details.order_date BETWEEN '$prev_fy_start' AND '$prev_fy_end'
                ";
                break;

            default:
                $where_chart = "";
        }
        $sql = "SELECT 
                    DATE_FORMAT(pod_order_details.order_date, '%Y-%m') AS order_month,
                    COUNT(DISTINCT pod_order.order_id) AS total_orders,
                    COUNT(DISTINCT pod_order_details.book_id) AS total_titles,
                    SUM(pod_order_details.quantity * pod_order_details.price) - pod_order.discount AS total_mrp
                FROM pod_order
                JOIN pod_order_details ON pod_order.order_id = pod_order_details.order_id
                WHERE pod_order.user_id != 0
                $where_chart
                GROUP BY DATE_FORMAT(pod_order_details.order_date, '%Y-%m')
                ORDER BY order_month ASC";

        $data['chart'] = $this->db->query($sql)->getResultArray();

        $sql0 = "SELECT 
                    COUNT(DISTINCT pod_order.order_id) AS total_orders, 
                    COUNT(DISTINCT pod_order_details.book_id) AS total_titles,
                    SUM(pod_order_details.quantity * pod_order_details.price) - pod_order.discount AS total_mrp
                FROM pod_order
                JOIN pod_order_details ON pod_order.order_id = pod_order_details.order_id
                WHERE pod_order_details.status = 0";

        $data['in_progress'] = $this->db->query($sql0)->getResultArray();

        $sql1 = "SELECT 
                    COUNT(pod_order.order_id) AS total_orders,
                    COUNT(DISTINCT pod_order_details.book_id) AS total_titles,
                    SUM(pod_order_details.quantity * pod_order_details.price) - pod_order.discount AS total_mrp
                FROM pod_order
                JOIN pod_order_details ON pod_order.order_id = pod_order_details.order_id
                WHERE pod_order_details.status = 1";

        $data['completed'] = $this->db->query($sql1)->getResultArray();

        $sql2 = "SELECT 
                    COUNT(pod_order.order_id) AS total_orders,
                    COUNT(DISTINCT pod_order_details.book_id) AS total_titles,
                    SUM(pod_order_details.quantity * pod_order_details.price) - pod_order.discount AS total_mrp
                FROM pod_order
                JOIN pod_order_details ON pod_order.order_id = pod_order_details.order_id
                WHERE pod_order_details.status = 1
                AND pod_order_details.ship_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";

        $data['completed_30days'] = $this->db->query($sql2)->getResultArray();

        $sql3 = "SELECT 
                    COUNT(DISTINCT pod_order.order_id) AS total_orders, 
                    COUNT(DISTINCT pod_order_details.book_id) AS total_titles,
                    SUM(pod_order_details.quantity * pod_order_details.price) - pod_order.discount AS total_mrp
                FROM pod_order
                JOIN pod_order_details ON pod_order.order_id = pod_order_details.order_id
                WHERE pod_order_details.status = 2";

        $data['cancelled'] = $this->db->query($sql3)->getResultArray();

        $data['filter'] = $filter;
        return $data;
    }
    public function offlineSummary($filter = 'all')
    {
         // Financial Year
        $year = date("Y");

        if (date("m") >= 4) {
            $current_fy_start = $year . "-04-01";
            $current_fy_end   = ($year + 1) . "-03-31";
        } else {
            $current_fy_start = ($year - 1) . "-04-01";
            $current_fy_end   = $year . "-03-31";
        }

        $prev_fy_start = date("Y-m-d", strtotime("-1 year", strtotime($current_fy_start)));
        $prev_fy_end   = date("Y-m-d", strtotime("-1 year", strtotime($current_fy_end)));
        $where_chart = "";

        switch ($filter) {
            case "month":
                $where_chart = "
                    AND MONTH(pustaka_offline_orders.order_date) = MONTH(CURDATE()) 
                    AND YEAR(pustaka_offline_orders.order_date) = YEAR(CURDATE()) 
                ";
                break;

            case "this_year":
                $where_chart = "
                    AND pustaka_offline_orders.order_date BETWEEN '$current_fy_start' AND '$current_fy_end'
                ";
                break;

            case "prev_year":
                $where_chart = "
                    AND pustaka_offline_orders.order_date BETWEEN '$prev_fy_start' AND '$prev_fy_end'
                ";
                break;

            default:
                $where_chart = "";
        }
        $sql = "SELECT 
                    DATE_FORMAT(pustaka_offline_orders.order_date, '%Y-%m') AS order_month,
                    COUNT(DISTINCT pustaka_offline_orders.order_id) AS total_orders,
                    COUNT(DISTINCT pustaka_offline_orders_details.book_id) AS total_titles,
                    SUM(pustaka_offline_orders_details.total_amount) AS total_mrp
                FROM pustaka_offline_orders
                JOIN pustaka_offline_orders_details 
                    ON pustaka_offline_orders.order_id = pustaka_offline_orders_details.offline_order_id
                JOIN book_tbl 
                    ON pustaka_offline_orders_details.book_id = book_tbl.book_id
                JOIN author_tbl 
                    ON book_tbl.author_name = author_tbl.author_id
                WHERE 1=1
                $where_chart
                GROUP BY DATE_FORMAT(pustaka_offline_orders.order_date, '%Y-%m')
                ORDER BY order_month ASC";
        $query = $this->db->query($sql);
        $data['chart'] = $query->getResultArray();


        // ---------------- IN PROGRESS ----------------
        $sql0 = "SELECT 
                    COUNT(DISTINCT pustaka_offline_orders.order_id) AS total_orders,
                    COUNT(DISTINCT pustaka_offline_orders_details.book_id) AS total_titles,
                    SUM(pustaka_offline_orders_details.total_amount) AS total_mrp
                FROM pustaka_offline_orders
                JOIN 
                    pustaka_offline_orders_details ON pustaka_offline_orders.order_id = pustaka_offline_orders_details.offline_order_id
                JOIN 
					book_tbl ON book_tbl.book_id = pustaka_offline_orders_details.book_id
                WHERE 
                    pustaka_offline_orders_details.ship_status = 0";

        $query0 = $this->db->query($sql0);
        $data['in_progress'] = $query0->getResultArray();


        // ---------------- COMPLETED (last 30 days OR pending payment) ----------------
        $sql1 = "SELECT
                    COUNT(DISTINCT o.order_id) AS total_orders,
                    COUNT(DISTINCT book_tbl.book_id) AS total_titles,
                    SUM(od.total_amount) AS total_sales
                FROM pustaka_offline_orders_details od
                JOIN pustaka_offline_orders o 
                    ON od.offline_order_id = o.order_id
                JOIN book_tbl 
                    ON od.book_id = book_tbl.book_id
                JOIN author_tbl 
                    ON book_tbl.author_name = author_tbl.author_id 
                LEFT JOIN paperback_stock stock 
                    ON stock.book_id = book_tbl.book_id
                WHERE od.ship_status = 1
                AND od.ship_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $query1 = $this->db->query($sql1);
        $data['30days_shipment'] = $query1->getResultArray();

        $sql1a = "SELECT 
                        COUNT(DISTINCT o.order_id) AS total_orders,
                        COUNT(DISTINCT d.book_id) AS total_titles,
                        SUM(d.total_amount) AS total_mrp
                    FROM pustaka_offline_orders o
                    JOIN pustaka_offline_orders_details d 
                        ON o.order_id = d.offline_order_id
                    JOIN book_tbl b 
                        ON b.book_id = d.book_id
                    WHERE d.ship_status = 1
                    AND o.payment_status = 'Pending'";
        $query1a = $this->db->query($sql1a);
        $data['pending_payment'] = $query1a->getResultArray();

        // ---------------- CANCELLED ----------------
        $sql2 = "SELECT 
                    COUNT(DISTINCT pustaka_offline_orders.order_id) AS total_orders,
                    COUNT(DISTINCT pustaka_offline_orders_details.book_id) AS total_titles,
                    SUM(pustaka_offline_orders_details.total_amount) AS total_mrp
                FROM pustaka_offline_orders
                JOIN pustaka_offline_orders_details 
                    ON pustaka_offline_orders.order_id = pustaka_offline_orders_details.offline_order_id
                JOIN 
					book_tbl ON book_tbl.book_id = pustaka_offline_orders_details.book_id

                WHERE pustaka_offline_orders_details.ship_status = 2";
        $query2 = $this->db->query($sql2);
        $data['cancel'] = $query2->getResultArray();


        // ---------------- RETURN ----------------
        $sql3 = "SELECT 
                    COUNT(DISTINCT pustaka_offline_orders.order_id) AS total_orders,
                    COUNT(DISTINCT pustaka_offline_orders_details.book_id) AS total_titles,
                    SUM(pustaka_offline_orders_details.total_amount) AS total_mrp
                FROM pustaka_offline_orders
                JOIN pustaka_offline_orders_details 
                    ON pustaka_offline_orders.order_id = pustaka_offline_orders_details.offline_order_id
                JOIN 
					book_tbl ON book_tbl.book_id = pustaka_offline_orders_details.book_id

                WHERE pustaka_offline_orders_details.ship_status = 3";
        $query3 = $this->db->query($sql3);
        $data['return'] = $query3->getResultArray();


        // ---------------- COMPLETED ALL ----------------
        $sql4 = "SELECT 
                    COUNT(DISTINCT pustaka_offline_orders.order_id) AS total_orders,
                    COUNT(DISTINCT pustaka_offline_orders_details.book_id) AS total_titles,
                    SUM(pustaka_offline_orders_details.total_amount) AS total_mrp
                FROM pustaka_offline_orders
                JOIN pustaka_offline_orders_details 
                    ON pustaka_offline_orders.order_id = pustaka_offline_orders_details.offline_order_id
                JOIN
					book_tbl ON book_tbl.book_id = pustaka_offline_orders_details.book_id
                WHERE pustaka_offline_orders_details.ship_status = 1";
        $query4 = $this->db->query($sql4);
        $data['completed_all'] = $query4->getResultArray();


        return $data;
    }
    public function authorSummary($chartFilter = 'all')
    {
        $data = [];

        $where = "pod.completed_flag = 1";

        // Apply Filter
        if ($chartFilter === 'current_fy') {
            $where .= " AND pao.order_date >= DATE_FORMAT(CURDATE(), '%Y-04-01')
                        AND pao.order_date <= DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 YEAR), '%Y-03-31')";
        } elseif ($chartFilter === 'previous_fy') {
            $where .= " AND pao.order_date >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 YEAR), '%Y-04-01')
                        AND pao.order_date <= DATE_FORMAT(CURDATE(), '%Y-03-31')";
        }

        // Query
        $sql_chart = "
            SELECT 
                DATE_FORMAT(pao.order_date, '%Y-%m') AS order_month,
                COUNT(DISTINCT pod.book_id) AS total_titles,
                SUM(pao.net_total) AS total_mrp
            FROM pod_author_order pao
            JOIN pod_author_order_details pod ON pao.order_id = pod.order_id
            JOIN book_tbl b ON pod.book_id = b.book_id
            WHERE $where
            GROUP BY DATE_FORMAT(pao.order_date, '%Y-%m')
            ORDER BY order_month ASC
        ";

        $query = $this->db->query($sql_chart);
        $data['chart'] = $query->getResultArray();

        // In-progress (order_status = 0)
        $sql_inprogress = "SELECT 
                                COUNT(DISTINCT pao.order_id) AS total_orders, 
                                COUNT(DISTINCT pod.book_id) AS total_titles,
                                SUM(pao.net_total) AS total_mrp
                        FROM pod_author_order pao
                        JOIN pod_author_order_details pod ON pao.order_id = pod.order_id
                        WHERE pod.completed_flag = 1 AND pao.order_status = 0";
        $query = $this->db->query($sql_inprogress);
        $data['in_progress'] = $query->getRowArray();

        //completed last 30 days
        $sql_completed_30days = "SELECT 
                                    COUNT(DISTINCT pao.order_id) AS total_orders,
                                    COUNT(DISTINCT pod.book_id) AS total_titles,
                                    SUM(pao.net_total) AS total_mrp
                                FROM pod_author_order pao
                                JOIN pod_author_order_details pod 
                                    ON pao.order_id = pod.order_id
                                WHERE pod.completed_flag = 1
                                AND pao.order_status = 1
                                AND pao.ship_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $query = $this->db->query($sql_completed_30days);
        $data['completed_30days'] = $query->getRowArray();

        //pending payments
        $sql_pending = "SELECT  
                            COUNT(DISTINCT pao.order_id) AS total_orders,
                            COUNT(DISTINCT pod.book_id) AS total_titles,
                            SUM(DISTINCT pao.net_total) AS total_mrp
                        FROM pod_author_order pao
                        JOIN pod_author_order_details pod 
                            ON pao.order_id = pod.order_id
                        WHERE pod.completed_flag = 1
                        AND pao.order_status = 1
                        AND pao.payment_status = 'Pending'";
        $query = $this->db->query($sql_pending);
        $data['pending_payments'] = $query->getRowArray();

        //completed all
        $sql_completed_all = "SELECT 
                                    COUNT(DISTINCT pao.order_id) AS total_orders,
                                    COUNT(DISTINCT pod.book_id) AS total_titles,
                                    SUM(pao.net_total) AS total_mrp
                                FROM pod_author_order pao
                                JOIN pod_author_order_details pod 
                                    ON pao.order_id = pod.order_id
                                WHERE pod.completed_flag = 1
                                AND pao.order_status = 1";
        $query = $this->db->query($sql_completed_all);
        $data['completed_all'] = $query->getRowArray();

        return $data;
    }
    //Bookshop Orders
    public function getBookshopOrdersDetails()
    {
        $db = \Config\Database::connect();
        $sql = "SELECT * FROM pod_bookshop WHERE status=1";
        $query = $db->query($sql);

        $data['bookshoper'] = $query->getResultArray();

        return $data;
    }

    public function submitBookshopOrders(array $post)
{
    $db = \Config\Database::connect();
    $num_of_books = isset($post['num_of_books']) ? (int)$post['num_of_books'] : 0;
    $order_id = time();

    $insert_data = [
        'bookshop_id' => $post['bookshop_id'] ?? null,
        'order_id' => $order_id,
        'order_date' => date('Y-m-d H:i:s'),
        'preferred_transport' => $post['preferred_transport'] ?? null,
        'preferred_transport_name' => $post['preferred_transport_name'] ?? null,
        'transport_payment' => $post['transport_payment'] ?? null,
        'ship_date' => date('Y-m-d H:i:s'),
        'ship_address' => $post['ship_address'] ?? null,
        'payment_type' => $post['payment_type'] ?? null,
        'payment_status' => $post['payment_status'] ?? null,
        'vendor_po_order_number' => $post['vendor_po_order_number'] ?? null
    ];

    try {

        $db->transStart(); // 

        
        $db->table('pod_bookshop_order')->insert($insert_data);

        
        for ($i = 1; $i <= $num_of_books; $i++) {

            if (!isset($post['book_id' . $i])) continue;

            $price    = (float) $post['bk_inr' . $i];
            $qty      = (int) $post['bk_qty' . $i];
            $discount = (float) ($post['bk_dis' . $i] ?? 0); // %

           
            $gross_amount     = $price * $qty;
            $discount_amount  = ($gross_amount * $discount) / 100;
            $final_amount     = $gross_amount - $discount_amount;

            $data = [
                'bookshop_id'   => $post['bookshop_id'],
                'order_id'      => $order_id,
                'order_date'    => date('Y-m-d H:i:s'),
                'book_id'       => $post['book_id' . $i],
                'book_price'    => $price,
                'discount'      => $discount,
                'quantity'      => $qty,
                'total_amount'  => $final_amount,
                'ship_status'   => 0
            ];

            $db->table('pod_bookshop_order_details')->insert($data);
        }

        $db->transComplete(); 

        if ($db->transStatus() === false) {
            throw new \Exception('Transaction failed');
        }

        return ['status' => 1, 'message' => 'Order added successfully!'];

    } catch (\Exception $e) {
        return ['status' => 0, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

    
    public function bookshopProgressBooks()
    {
        $data = [];

        // In-progress orders
        $sql = "SELECT
                    pod_bookshop_order.*,
                    pod_bookshop.*,
                    (SELECT COUNT(order_id) FROM pod_bookshop_order_details 
                     WHERE pod_bookshop_order_details.order_id = pod_bookshop_order.order_id) as tot_book
                FROM pod_bookshop_order
                JOIN pod_bookshop ON pod_bookshop_order.bookshop_id=pod_bookshop.bookshop_id
                WHERE pod_bookshop_order.status = 0
                ORDER BY pod_bookshop_order.ship_date ASC";
        $data['in_progress'] = $this->db->query($sql)->getResultArray();

        // Completed (last 30 days shipment)
        $sql = "SELECT
                    pod_bookshop_order.*,
                    pod_bookshop.*,
                    (SELECT COUNT(order_id) FROM pod_bookshop_order_details 
                    WHERE pod_bookshop_order_details.order_id = pod_bookshop_order.order_id) as tot_book
                FROM pod_bookshop_order
                JOIN pod_bookshop ON pod_bookshop_order.bookshop_id=pod_bookshop.bookshop_id
                WHERE (pod_bookshop_order.status = 1 AND pod_bookshop_order.ship_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY))";

        $data['completed_30days'] = $this->db->query($sql)->getResultArray();

        // Pending Payment

        $sql = "SELECT
                    pod_bookshop_order.*,
                    pod_bookshop.*,
                    (SELECT COUNT(order_id) FROM pod_bookshop_order_details 
                    WHERE pod_bookshop_order_details.order_id = pod_bookshop_order.order_id) as tot_book
                FROM pod_bookshop_order
                JOIN pod_bookshop ON pod_bookshop_order.bookshop_id=pod_bookshop.bookshop_id
                WHERE (pod_bookshop_order.status = 1 AND pod_bookshop_order.payment_status = 'Pending')
                ORDER BY pod_bookshop_order.ship_date ASC";

        $data['pending_payment'] = $this->db->query($sql)->getResultArray();

        // Cancelled
        $sql = "SELECT
                    pod_bookshop_order.*,
                    pod_bookshop.*,
                    (SELECT COUNT(order_id) FROM pod_bookshop_order_details 
                     WHERE pod_bookshop_order_details.order_id = pod_bookshop_order.order_id) as tot_book
                FROM pod_bookshop_order
                JOIN pod_bookshop ON pod_bookshop_order.bookshop_id=pod_bookshop.bookshop_id
                WHERE pod_bookshop_order.status = 2
                ORDER BY pod_bookshop_order.ship_date ASC";
        $data['cancel'] = $this->db->query($sql)->getResultArray();

        // All completed
        $sql = "SELECT
                    pod_bookshop_order.*,
                    pod_bookshop.*,
                    (SELECT COUNT(order_id) FROM pod_bookshop_order_details 
                     WHERE pod_bookshop_order_details.order_id = pod_bookshop_order.order_id) as tot_book
                FROM pod_bookshop_order
                JOIN pod_bookshop ON pod_bookshop_order.bookshop_id=pod_bookshop.bookshop_id
                WHERE pod_bookshop_order.status = 1
                ORDER BY pod_bookshop_order.ship_date ASC";
        $data['completed_all'] = $this->db->query($sql)->getResultArray();

        return $data;
    }

    public function bookshopOrderShip($order_id)
    {
        // Fetch order details
        $sql = "SELECT pod_bookshop_order_details.*, pod_bookshop_order.*,
                    (SELECT COUNT(pod_bookshop_order_details.order_id) 
                        FROM pod_bookshop_order_details 
                        WHERE pod_bookshop_order_details.order_id = pod_bookshop_order.order_id) as tot_book
                FROM pod_bookshop_order_details
                JOIN pod_bookshop_order ON pod_bookshop_order.order_id = pod_bookshop_order_details.order_id
                WHERE pod_bookshop_order_details.order_id = ?";
        $query = $this->db->query($sql, [$order_id]);
        $data['details'] = $query->getFirstRow('array');

        // Fetch book list
        $sql = "SELECT pod_bookshop_order_details.book_id,
                       pod_bookshop_order_details.quantity,
                       book_tbl.book_title,
                       paperback_stock.quantity as qty,
                       paperback_stock.stock_in_hand,
                       paperback_stock.bookfair,
                       paperback_stock.bookfair2,
                       paperback_stock.bookfair3,
                       paperback_stock.bookfair4,
                       paperback_stock.bookfair5,
                       paperback_stock.lost_qty
                FROM pod_bookshop_order_details
                JOIN book_tbl ON pod_bookshop_order_details.book_id = book_tbl.book_id
                JOIN paperback_stock ON pod_bookshop_order_details.book_id = paperback_stock.book_id
                WHERE pod_bookshop_order_details.order_id = ?";
        $query = $this->db->query($sql, [$order_id]);
        $data['list'] = $query->getResultArray();

        return $data;
    }
    public function bookshopMarkShipped($order_id, $tracking_id, $tracking_url)
    {
        $db = \Config\Database::connect();
        $success = false; 

        $sql = "SELECT book_id FROM pod_bookshop_order_details WHERE order_id = $order_id";
        $query = $db->query($sql);
        $books_details = $query->getResultArray();

        foreach ($books_details as $books) {
            $bookID = $books['book_id'];

            $select_bookshop_order_id = "SELECT quantity FROM pod_bookshop_order_details WHERE book_id = $bookID AND order_id = $order_id";
            $tmp = $db->query($select_bookshop_order_id);
            $record = $tmp->getResultArray()[0];
            $qty = $record['quantity'];

            $update_sql = "UPDATE paperback_stock SET quantity = quantity - $qty, stock_in_hand = stock_in_hand - $qty WHERE book_id = $bookID";
            $db->query($update_sql);

            $update_sql2 = "UPDATE pod_bookshop_order_details 
                            SET ship_status = 1, shipped_date = '" . date('Y-m-d') . "'
                            WHERE order_id = $order_id AND book_id = $bookID";
            $db->query($update_sql2);

            $update_sql3 = "UPDATE pod_bookshop_order 
                            SET tracking_id = '$tracking_id', 
                                tracking_url = '$tracking_url', 
                                actual_ship_date = '" . date('Y-m-d') . "', 
                                status = 1
                            WHERE order_id = $order_id";
            $db->query($update_sql3);

            $stock_sql = "SELECT pod_bookshop_order_details.*, book_tbl.*, pod_bookshop.bookshop_name,
                                pod_bookshop_order_details.quantity as quantity, paperback_stock.quantity as current_stock
                        FROM pod_bookshop_order_details
                        JOIN book_tbl ON pod_bookshop_order_details.book_id = book_tbl.book_id
                        JOIN paperback_stock ON paperback_stock.book_id = book_tbl.book_id
                        JOIN pod_bookshop ON pod_bookshop.bookshop_id = pod_bookshop_order_details.bookshop_id
                        WHERE book_tbl.book_id = $bookID AND pod_bookshop_order_details.order_id = $order_id";
            $temp = $db->query($stock_sql);
            $stock = $temp->getResultArray()[0];

            $book_id = $stock['book_id'];
            $author_id = $stock['author_name'];
            $copyright_owner = $stock['paper_back_copyright_owner'];
            $description = "Bookshop Sales - " . $stock['bookshop_name'];
            $channel_type = "BKS";
            $stock_out = $stock['quantity'];

            $insert_sql = "INSERT INTO pustaka_paperback_stock_ledger 
                            (book_id, order_id, author_id, copyright_owner, description, channel_type, stock_out, transaction_date)
                            VALUES ($book_id, '$order_id', '$author_id', '$copyright_owner', '$description', '$channel_type', $stock_out, '" . date('Y-m-d H:i:s') . "')";
            $db->query($insert_sql);

            if ($db->affectedRows() >= 0) {
                $success = true;
            }
        }

        return $success ? 1 : 0;
    }
    public function bookshopMarkCancel($order_id)
    {
        $db = \Config\Database::connect();

        // Cancel all book items under this order
        $update_data = [
            'ship_status' => 2,
            'date' => date('Y-m-d'),
        ];

        $db->table('pod_bookshop_order_details')
            ->where('order_id', $order_id)
            ->update($update_data);

        // Mark main order as cancelled
        $update_data1 = [
            'status' => 2,
        ];

        $db->table('pod_bookshop_order')
            ->where('order_id', $order_id)
            ->update($update_data1);

        return ($db->affectedRows() > 0) ? 1 : 0;
    }


    public function bookshopMarkPay($order_id)
    {
        $this->db->table('pod_bookshop_order')
            ->where('order_id', $order_id)
            ->update(["payment_status" => 'Paid']);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function bookshopOrderDetails($order_id)
    {
        $sql = "SELECT pod_bookshop_order.*, pod_bookshop.bookshop_name, pod_bookshop.contact_person_name, pod_bookshop.mobile
                FROM pod_bookshop_order 
                JOIN pod_bookshop ON pod_bookshop_order.bookshop_id = pod_bookshop.bookshop_id
                WHERE pod_bookshop_order.order_id = ?";
        $data['details'] = $this->db->query($sql, [$order_id])->getRowArray();

        $sql = "SELECT pod_bookshop_order_details.*, book_tbl.book_title, author_tbl.author_name,
                       book_tbl.paper_back_isbn, pod_bookshop_order.transport_payment,
                       paperback_stock.quantity as qty, paperback_stock.stock_in_hand,
                       paperback_stock.bookfair, paperback_stock.bookfair2, paperback_stock.bookfair3,
                       paperback_stock.bookfair4, paperback_stock.bookfair5, paperback_stock.lost_qty
                FROM pod_bookshop_order
                JOIN pod_bookshop_order_details ON pod_bookshop_order.order_id = pod_bookshop_order_details.order_id
                JOIN book_tbl ON book_tbl.book_id = pod_bookshop_order_details.book_id
                JOIN author_tbl ON author_tbl.author_id = book_tbl.author_name
                LEFT JOIN paperback_stock ON paperback_stock.book_id = book_tbl.book_id
                WHERE pod_bookshop_order.order_id = ?";
        $data['list'] = $this->db->query($sql, [$order_id])->getResultArray();

        return $data;
    }

    public function bookshopInvoiceDetails($order_id)
    {
        $sql = "SELECT pod_bookshop_order.*, pod_order_details.tot_book_count, pod_order_details.net_total
                FROM pod_bookshop_order
                JOIN (
                    SELECT order_id, COUNT(order_id) as tot_book_count, SUM(total_amount) as net_total
                    FROM pod_bookshop_order_details
                    GROUP BY order_id
                ) as pod_order_details
                ON pod_bookshop_order.order_id = pod_order_details.order_id
                WHERE pod_bookshop_order.order_id = ?";
        return ['invoice' => $this->db->query($sql, [$order_id])->getRowArray()];
    }

    public function createBookshopInvoice(array $post)
    {
        $order_id = $post['order_id'];

        $this->db->table('pod_bookshop_order')
            ->where('order_id', $order_id)
            ->update(['invoice_no' => $post['invoice_number']]);

        if ($this->db->affectedRows() > 0) {
            return ['status' => true, 'message' => 'Invoice created successfully'];
        } else {
            return ['status' => false, 'message' => 'Invoice not created (maybe already exists?)'];
        }
    }
    
    public function addBookshop(array $post)
    {
        $update_data = [
            'bookshop_name' => $post['bookshopName'],
            'contact_person_name' => $post['contactPerson'],
            'email_id' => $post['email'],
            'mobile' => $post['mobile'],
            'address' => $post['address'],
            'city' => $post['city'],
            'pincode' => $post['pincode'],
            'preferred_transport' => $post['preferredTransport'],
            'preferred_transport_name' => $post['preferredTransportName'],
            'status' => 1,
        ];

        $this->db->table('pod_bookshop')->insert($update_data);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }
    //flipkart orders
    function getFlipkartPaperbackOrder()
    {
        $flipkart_paperback_sql = "SELECT  book_tbl.url_name as url,flipkart_paperback_books.*,book_tbl.*,author_tbl.* 
                                    FROM flipkart_paperback_books,book_tbl,author_tbl
                                    where flipkart_paperback_books.seller_sku_id=book_tbl.book_id and
                                    flipkart_paperback_books.author_id=author_tbl.author_id ";
        $flipkart_paperback_query = $this->db->query($flipkart_paperback_sql);
        return $flipkart_paperback_query->getResultArray();
    }
    function getFlipkartSelectedBooksList($selected_book_list)
    {
        $sql = "SELECT book_tbl.url_name as url,flipkart_paperback_books.*, book_tbl.*,author_tbl.*
            FROM flipkart_paperback_books, book_tbl,author_tbl where flipkart_paperback_books.seller_sku_id = book_tbl.book_id
            and flipkart_paperback_books.author_id = author_tbl.author_id and 
            flipkart_paperback_books.seller_sku_id in ($selected_book_list)";

        $query = $this->db->query($sql);

        return $query->getResultArray();
    }

    function getFlipkartStockDetails($selected_book_list)
    {
        $sql = "SELECT
                    book_tbl.book_id as bookID,
                    book_tbl.url_name AS url,
                    flipkart_paperback_books.*,
                    book_tbl.*,
                    author_tbl.*,
                    paperback_stock.*,
                    (SELECT sum(quantity) FROM pustaka_paperback_books WHERE book_id = book_tbl.book_id and completed_flag=0) as Qty
                FROM
                    flipkart_paperback_books
                LEFT JOIN
                    book_tbl ON flipkart_paperback_books.seller_sku_id = book_tbl.book_id
                LEFT JOIN
                    author_tbl ON flipkart_paperback_books.author_id = author_tbl.author_id
                LEFT JOIN
                    paperback_stock ON flipkart_paperback_books.seller_sku_id = paperback_stock.book_id
                WHERE
                    flipkart_paperback_books.seller_sku_id IN ($selected_book_list)";

        $query = $this->db->query($sql);

        return $query->getResultArray();
    }

    public function flipkartOrderbooksDetailsSubmit($num_of_books)
    {
        $j = 0;
        $book_ids = array();
        $book_qtys = array();

        for ($i = 0; $i < $num_of_books; $i++) {
            $tmp = 'book_id' . $j;
            $tmp1 = 'quantity_details' . $j++;
            $book_ids[$i] = $_POST[$tmp];
            $book_qtys[$i] = $_POST[$tmp1];
        }

        for ($i = 0; $i < $num_of_books; $i++) {
            $data = array(
                'book_id' => $book_ids[$i],
                'quantity' => $book_qtys[$i],
                'flipkart_order_id' => trim($_POST['order_id']),
                'ship_date' => $_POST['ship_date'],
                'order_date' => date('Y-m-d H:i:s'),
            );

            $this->db->table('flipkart_paperback_orders')->insert($data);
        }
    }

    function flipkartInProgressBooks()
    {
        $sql = "SELECT
                    author_tbl.author_name AS author_name,
                    flipkart_paperback_orders.flipkart_order_id,
                    flipkart_paperback_orders.quantity,
                    flipkart_paperback_orders.ship_date,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.stock_in_hand AS total_quantity,
                    paperback_stock.bookfair
                FROM
                    flipkart_paperback_orders
                JOIN
                    book_tbl ON flipkart_paperback_orders.book_id = book_tbl.book_id
                JOIN
                    author_tbl ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN
                    paperback_stock ON paperback_stock.book_id = book_tbl.book_id
                WHERE
                    flipkart_paperback_orders.ship_status = 0
                ORDER BY
                    flipkart_paperback_orders.ship_date ASC";
        $query = $this->db->query($sql);
        $data['in_progress'] = $query->getResultArray();

        $sql = "SELECT
                    author_tbl.author_name AS author_name,
                    flipkart_paperback_orders.flipkart_order_id,
                    flipkart_paperback_orders.quantity,
                    flipkart_paperback_orders.ship_date,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.stock_in_hand AS total_quantity,
                    paperback_stock.bookfair
                FROM
                    flipkart_paperback_orders
                JOIN
                    book_tbl ON flipkart_paperback_orders.book_id = book_tbl.book_id
                JOIN
                    author_tbl ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN
                    paperback_stock ON paperback_stock.book_id = book_tbl.book_id
                WHERE
                    flipkart_paperback_orders.ship_status = 1
                    AND flipkart_paperback_orders.ship_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ORDER BY
                    flipkart_paperback_orders.ship_date DESC";
        $query = $this->db->query($sql);
        $data['completed'] = $query->getResultArray();

        $sql = "SELECT
                    author_tbl.author_name AS author_name,
                    flipkart_paperback_orders.flipkart_order_id,
                    flipkart_paperback_orders.quantity,
                    flipkart_paperback_orders.date,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.stock_in_hand AS total_quantity,
                    paperback_stock.bookfair
                FROM
                    flipkart_paperback_orders
                JOIN
                    book_tbl ON flipkart_paperback_orders.book_id = book_tbl.book_id
                JOIN
                    author_tbl ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN
                    paperback_stock ON paperback_stock.book_id = book_tbl.book_id
                WHERE
                    flipkart_paperback_orders.ship_status = 2
                ORDER BY
                    flipkart_paperback_orders.date DESC";
        $query = $this->db->query($sql);
        $data['cancel'] = $query->getResultArray();

        $sql = "SELECT
                    author_tbl.author_name AS author_name,
                    flipkart_paperback_orders.flipkart_order_id,
                    flipkart_paperback_orders.quantity,
                    flipkart_paperback_orders.date,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.stock_in_hand AS total_quantity,
                    paperback_stock.bookfair
                FROM
                    flipkart_paperback_orders
                JOIN
                    book_tbl ON flipkart_paperback_orders.book_id = book_tbl.book_id
                JOIN
                    author_tbl ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN
                    paperback_stock ON paperback_stock.book_id = book_tbl.book_id
                WHERE
                    flipkart_paperback_orders.ship_status = 3
                ORDER BY
                    flipkart_paperback_orders.date DESC";
        $query = $this->db->query($sql);
        $data['return'] = $query->getResultArray();

        $sql = "SELECT
                    author_tbl.author_name AS author_name,
                    flipkart_paperback_orders.flipkart_order_id,
                    flipkart_paperback_orders.quantity,
                    flipkart_paperback_orders.date,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.stock_in_hand AS total_quantity,
                    paperback_stock.bookfair
                FROM
                    flipkart_paperback_orders
                JOIN
                    book_tbl ON flipkart_paperback_orders.book_id = book_tbl.book_id
                JOIN
                    author_tbl ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN
                    paperback_stock ON paperback_stock.book_id = book_tbl.book_id
                WHERE
                    flipkart_paperback_orders.ship_status = 4
                ORDER BY
                    flipkart_paperback_orders.date DESC";
        $query = $this->db->query($sql);
        $data['return_pending'] = $query->getResultArray();

        $sql = "SELECT
                    author_tbl.author_name AS author_name,
                    flipkart_paperback_orders.flipkart_order_id,
                    flipkart_paperback_orders.quantity,
                    flipkart_paperback_orders.ship_date,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.stock_in_hand AS total_quantity,
                    paperback_stock.bookfair
                FROM
                    flipkart_paperback_orders
                JOIN
                    book_tbl ON flipkart_paperback_orders.book_id = book_tbl.book_id
                JOIN
                    author_tbl ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN
                    paperback_stock ON paperback_stock.book_id = book_tbl.book_id
                WHERE
                    flipkart_paperback_orders.ship_status = 1
                ORDER BY
                    flipkart_paperback_orders.ship_date DESC";
        $query = $this->db->query($sql);
        $data['completed_all'] = $query->getResultArray();

        return $data;
    }

    // flipkart_paperback_orders table ship_status details below 
    // 1 shipped
    // 2 cancel
    // 3 return
    // 4 Pending for return confirm 

    function flipkartMarkShipped($flipkart_order_id, $book_id)
    {
        $select_flipkart_order_id = "SELECT quantity from flipkart_paperback_orders WHERE book_id = " . $book_id . " AND flipkart_order_id = '" . $flipkart_order_id . "'";
        $tmp = $this->db->query($select_flipkart_order_id);
        $record = $tmp->getResultArray()[0];

        $qty = $record['quantity'];

        $update_sql = "UPDATE paperback_stock set quantity = quantity - " . $qty . ",stock_in_hand = stock_in_hand - " . $qty . " where book_id = " . $book_id;
        $this->db->query($update_sql);

        $update_data = array(
            "ship_status" => 1,
        );
        $this->db->table('flipkart_paperback_orders')
            ->where(['flipkart_order_id' => $flipkart_order_id, 'book_id' => $book_id])
            ->update($update_data);

        // inserting the record into pustaka_paperback_stock_ledger table 
        $stock_sql = "SELECT flipkart_paperback_orders.*,book_tbl.* ,flipkart_paperback_orders.quantity as quantity,
                    paperback_stock.quantity as current_stock
                    from flipkart_paperback_orders,book_tbl,paperback_stock
                    where flipkart_paperback_orders.book_id=book_tbl.book_id
                    and paperback_stock.book_id=flipkart_paperback_orders.book_id
                    and book_tbl.book_id = " . $book_id . " AND flipkart_paperback_orders.flipkart_order_id = '" . $flipkart_order_id . "'";
        $temp = $this->db->query($stock_sql);
        $stock = $temp->getResultArray()[0];

        $book_id = $stock['book_id'];
        $flipkart_order_id = $stock['flipkart_order_id'];
        $author_id = $stock['author_name'];
        $copyright_owner = $stock['paper_back_copyright_owner'];
        $description = "Flipkart Sales";
        $channel_type = "FLP";
        $stock_out = $stock['quantity'];

        $stock_data = array(
            'book_id' => $book_id,
            "order_id" => $flipkart_order_id,
            "author_id" => $author_id,
            "copyright_owner" => $copyright_owner,
            "description" => $description,
            "channel_type" => $channel_type,
            "stock_out" => $stock_out,
            'transaction_date' => date('Y-m-d H:i:s'),
        );
        $this->db->table('pustaka_paperback_stock_ledger')->insert($stock_data);

        if ($this->db->affectedRows() > 0)
            return 1;
        else
            return 0;
    }

    function flipkartMarkCancel($flipkart_order_id, $book_id)
    {
        $update_data = array(
            "ship_status" => 2,
            "date" => date('Y-m-d'),
        );
        $flipkart_order_id = $_POST['flipkart_order_id'];
        $book_id = $_POST['book_id'];

        $this->db->table('flipkart_paperback_orders')
            ->where(['flipkart_order_id' => $flipkart_order_id, 'book_id' => $book_id])
            ->update($update_data);

        if ($this->db->affectedRows() > 0)
            return 1;
        else
            return 0;
    }

    function flipkartMarkReturnPending($flipkart_order_id, $book_id)
    {
        $update_data = array(
            "ship_status" => 4,
            "date" => date('Y-m-d'),
        );
        $flipkart_order_id = $_POST['flipkart_order_id'];
        $book_id = $_POST['book_id'];

        $this->db->table('flipkart_paperback_orders')
            ->where(['flipkart_order_id' => $flipkart_order_id, 'book_id' => $book_id])
            ->update($update_data);

        if ($this->db->affectedRows() > 0)
            return 1;
        else
            return 0;
    }

    function flipkartMarkReturn($flipkart_order_id, $book_id)
    {
        $select_flipkart_order_id = "SELECT quantity from flipkart_paperback_orders WHERE book_id = " . $book_id . " AND flipkart_order_id = '" . $flipkart_order_id . "'";
        $tmp = $this->db->query($select_flipkart_order_id);
        $record = $tmp->getResultArray()[0];

        $qty = $record['quantity'];

        $update_sql = "UPDATE paperback_stock set quantity = quantity + " . $qty . ",stock_in_hand = stock_in_hand + " . $qty . " where book_id = " . $book_id;
        $this->db->query($update_sql);

        $update_data = array(
            "ship_status" => 3,
            "date" => date('Y-m-d'),
        );
        $this->db->table('flipkart_paperback_orders')
            ->where(['flipkart_order_id' => $flipkart_order_id, 'book_id' => $book_id])
            ->update($update_data);

        // inserting the record into pustaka_paperback_stock_ledger table 
        $stock_sql = "SELECT flipkart_paperback_orders.*,book_tbl.* ,flipkart_paperback_orders.quantity as quantity,
                    paperback_stock.quantity as current_stock
                    from flipkart_paperback_orders,book_tbl,paperback_stock
                    where flipkart_paperback_orders.book_id=book_tbl.book_id
                    and paperback_stock.book_id=flipkart_paperback_orders.book_id
                    and book_tbl.book_id = " . $book_id . " AND flipkart_paperback_orders.flipkart_order_id = '" . $flipkart_order_id . "'";
        $temp = $this->db->query($stock_sql);
        $stock = $temp->getResultArray()[0];

        $book_id = $stock['book_id'];
        $flipkart_order_id = $stock['flipkart_order_id'];
        $author_id = $stock['author_name'];
        $copyright_owner = $stock['paper_back_copyright_owner'];
        $description = "Flipkart Return";
        $channel_type = "FLP";
        $stock_in = $stock['quantity'];

        $stock_data = array(
            'book_id' => $book_id,
            "order_id" => $flipkart_order_id,
            "author_id" => $author_id,
            "copyright_owner" => $copyright_owner,
            "description" => $description,
            "stock_in" => $stock_in,
            "channel_type" => $channel_type,
            'transaction_date' => date('Y-m-d H:i:s'),
        );
        $this->db->table('pustaka_paperback_stock_ledger')->insert($stock_data);

        if ($this->db->affectedRows() > 0)
            return 1;
        else
            return 0;
    }
    public function flipkartOrderDetails($order_id)
    {
        $db = \Config\Database::connect();

        // First query
        $sql = "SELECT * FROM flipkart_paperback_orders WHERE flipkart_order_id = ?";
        $query = $db->query($sql, [$order_id]);
        $data['order'] = $query->getRowArray(); // same as result_array()[0] in CI3

        // Second query
        $sql = "SELECT flipkart_paperback_orders.*, 
                    book_tbl.book_title,
                    author_tbl.author_name,
                    book_tbl.paper_back_inr,
                    book_tbl.regional_book_title
                FROM flipkart_paperback_orders
                JOIN book_tbl ON book_tbl.book_id = flipkart_paperback_orders.book_id
                JOIN author_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE flipkart_paperback_orders.flipkart_order_id = ?";
        $query = $db->query($sql, [$order_id]);
        $data['details'] = $query->getResultArray();

        return $data;
    }
    public function bookshopSummary($chartFilter)
    {
        $data = [];
        $where = "pod_bookshop_order.status = 1";

        // Apply Filter
        if ($chartFilter === 'current_fy') {
            $where .= " AND pod_bookshop_order.order_date >= DATE_FORMAT(CURDATE(), '%Y-04-01')
                        AND pod_bookshop_order.order_date <= DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 YEAR), '%Y-03-31')";
        } elseif ($chartFilter === 'previous_fy') {
            $where .= " AND pod_bookshop_order.order_date >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 YEAR), '%Y-04-01')
                        AND pod_bookshop_order.order_date <= DATE_FORMAT(CURDATE(), '%Y-03-31')";
        }
        // ---------------- CHART ----------------
        $sql = "SELECT 
                DATE_FORMAT(pod_bookshop_order.order_date, '%Y-%m') AS order_month,
                COUNT(DISTINCT pod_bookshop_order.order_id) AS total_orders,
                COUNT(DISTINCT pod_bookshop_order_details.book_id) AS total_titles,
                ROUND(SUM(pod_bookshop_order_details.total_amount)) AS total_mrp
            FROM pod_bookshop_order
            JOIN pod_bookshop_order_details 
                ON pod_bookshop_order.order_id = pod_bookshop_order_details.order_id
            JOIN pod_bookshop 
                ON pod_bookshop_order.bookshop_id = pod_bookshop.bookshop_id
            WHERE $where    
            GROUP BY DATE_FORMAT(pod_bookshop_order.order_date, '%Y-%m')
            ORDER BY order_month ASC";

        $query = $this->db->query($sql);
        $data['chart'] = $query->getResultArray();

        $sql0 = "SELECT 
                    COUNT(DISTINCT pod_bookshop_order.order_id) AS total_orders,
                    COUNT(DISTINCT pod_bookshop_order_details.book_id) AS total_titles,
                    ROUND(SUM(pod_bookshop_order_details.total_amount)) AS total_mrp
                FROM pod_bookshop_order
                JOIN pod_bookshop 
                    ON pod_bookshop_order.bookshop_id = pod_bookshop.bookshop_id
                JOIN pod_bookshop_order_details 
                    ON pod_bookshop_order.order_id = pod_bookshop_order_details.order_id
                WHERE pod_bookshop_order.status = 0";
        $query0 = $this->db->query($sql0);
        $data['in_progress'] = $query0->getResultArray();

        $sql1 = "SELECT 
                    COUNT(DISTINCT pod_bookshop_order.order_id) AS total_orders,
                    COUNT(DISTINCT pod_bookshop_order_details.book_id) AS total_titles,
                    ROUND(SUM(pod_bookshop_order_details.total_amount)) AS total_mrp
                FROM pod_bookshop_order
                JOIN pod_bookshop 
                    ON pod_bookshop_order.bookshop_id = pod_bookshop.bookshop_id
                JOIN pod_bookshop_order_details 
                    ON pod_bookshop_order.order_id = pod_bookshop_order_details.order_id
                WHERE (pod_bookshop_order.status = 1 
                    AND pod_bookshop_order.ship_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY))";

        $query1 = $this->db->query($sql1);
        $data['completed_30days'] = $query1->getResultArray();

        $sql1a = "SELECT 
                    COUNT(DISTINCT pod_bookshop_order.order_id) AS total_orders,
                    COUNT(DISTINCT pod_bookshop_order_details.book_id) AS total_titles,
                    ROUND(SUM(pod_bookshop_order_details.total_amount)) AS total_mrp
                FROM pod_bookshop_order
                JOIN pod_bookshop 
                    ON pod_bookshop_order.bookshop_id = pod_bookshop.bookshop_id
                JOIN pod_bookshop_order_details 
                    ON pod_bookshop_order.order_id = pod_bookshop_order_details.order_id
                WHERE (pod_bookshop_order.status = 1 
                    AND pod_bookshop_order.payment_status = 'Pending')";
        $query1a = $this->db->query($sql1a);
        $data['pending_payment'] = $query1a->getResultArray();


        // ---------------- CANCELLED ----------------
        $sql2 = "SELECT 
                    COUNT(DISTINCT pod_bookshop_order.order_id) AS total_orders,
                    COUNT(DISTINCT pod_bookshop_order_details.book_id) AS total_titles,
                    ROUND(SUM(pod_bookshop_order_details.total_amount)) AS total_mrp
                FROM pod_bookshop_order
                JOIN pod_bookshop 
                    ON pod_bookshop_order.bookshop_id = pod_bookshop.bookshop_id
                JOIN pod_bookshop_order_details 
                    ON pod_bookshop_order.order_id = pod_bookshop_order_details.order_id
                WHERE pod_bookshop_order.status = 2";
        $query2 = $this->db->query($sql2);
        $data['cancel'] = $query2->getResultArray();


        // ---------------- COMPLETED ALL ----------------
        $sql3 = "SELECT 
                    COUNT(DISTINCT pod_bookshop_order.order_id) AS total_orders,
                    COUNT(DISTINCT pod_bookshop_order_details.book_id) AS total_titles,
                    ROUND(SUM(pod_bookshop_order_details.total_amount)) AS total_mrp
                FROM pod_bookshop_order
                JOIN pod_bookshop 
                    ON pod_bookshop_order.bookshop_id = pod_bookshop.bookshop_id
                JOIN pod_bookshop_order_details 
                    ON pod_bookshop_order.order_id = pod_bookshop_order_details.order_id
                WHERE pod_bookshop_order.status = 1";
        $query3 = $this->db->query($sql3);
        $data['completed_all'] = $query3->getResultArray();

        return $data;
    }
    public function flipkartSummary()
    {
        $data = [];

        // ---------------- CHART ----------------
        $sqlChart = "SELECT 
                        DATE_FORMAT(flipkart_paperback_orders.ship_date, '%Y-%m') AS order_month,
                        COUNT(DISTINCT flipkart_paperback_orders.flipkart_order_id) AS total_orders,
                        COUNT(DISTINCT book_tbl.book_id) AS total_titles,
                        SUM(flipkart_paperback_orders.quantity * book_tbl.cost) AS total_mrp
                    FROM flipkart_paperback_orders
                    JOIN book_tbl 
                        ON flipkart_paperback_orders.book_id = book_tbl.book_id
                    JOIN author_tbl 
                        ON book_tbl.author_name = author_tbl.author_id
                    LEFT JOIN paperback_stock 
                        ON paperback_stock.book_id = book_tbl.book_id
                    GROUP BY DATE_FORMAT(flipkart_paperback_orders.ship_date, '%Y-%m')
                    ORDER BY order_month ASC";
        $queryChart = $this->db->query($sqlChart);
        $data['chart'] = $queryChart->getResultArray();


        // ---------------- IN PROGRESS ----------------
        $sql0 = "SELECT 
                    COUNT(DISTINCT flipkart_paperback_orders.flipkart_order_id) AS total_orders,
                    COUNT(DISTINCT book_tbl.book_id) AS total_titles,
                    SUM(flipkart_paperback_orders.quantity * book_tbl.cost) AS total_mrp
                FROM flipkart_paperback_orders
                JOIN book_tbl 
                    ON flipkart_paperback_orders.book_id = book_tbl.book_id
                JOIN author_tbl 
                    ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN paperback_stock 
                    ON paperback_stock.book_id = book_tbl.book_id
                WHERE flipkart_paperback_orders.ship_status = 0";
        $query0 = $this->db->query($sql0);
        $data['in_progress'] = $query0->getResultArray();


        // ---------------- COMPLETED (last 30 days) ----------------
        $sql1 = "SELECT 
                    COUNT(DISTINCT flipkart_paperback_orders.flipkart_order_id) AS total_orders,
                    COUNT(DISTINCT book_tbl.book_id) AS total_titles,
                    SUM(flipkart_paperback_orders.quantity * book_tbl.cost) AS total_mrp
                FROM flipkart_paperback_orders
                JOIN book_tbl 
                    ON flipkart_paperback_orders.book_id = book_tbl.book_id
                JOIN author_tbl 
                    ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN paperback_stock 
                    ON paperback_stock.book_id = book_tbl.book_id
                WHERE flipkart_paperback_orders.ship_status = 1
                AND flipkart_paperback_orders.ship_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $query1 = $this->db->query($sql1);
        $data['completed'] = $query1->getResultArray();


        // ---------------- CANCELLED ----------------
        $sql2 = "SELECT 
                    COUNT(DISTINCT flipkart_paperback_orders.flipkart_order_id) AS total_orders,
                    COUNT(DISTINCT book_tbl.book_id) AS total_titles,
                    SUM(flipkart_paperback_orders.quantity * book_tbl.cost) AS total_mrp
                FROM flipkart_paperback_orders
                JOIN book_tbl 
                    ON flipkart_paperback_orders.book_id = book_tbl.book_id
                JOIN author_tbl 
                    ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN paperback_stock 
                    ON paperback_stock.book_id = book_tbl.book_id
                WHERE flipkart_paperback_orders.ship_status = 2";
        $query2 = $this->db->query($sql2);
        $data['cancel'] = $query2->getResultArray();


        // ---------------- RETURN ----------------
        $sql3 = "SELECT 
                    COUNT(DISTINCT flipkart_paperback_orders.flipkart_order_id) AS total_orders,
                    COUNT(DISTINCT book_tbl.book_id) AS total_titles,
                    SUM(flipkart_paperback_orders.quantity * book_tbl.cost) AS total_mrp
                FROM flipkart_paperback_orders
                JOIN book_tbl 
                    ON flipkart_paperback_orders.book_id = book_tbl.book_id
                JOIN author_tbl 
                    ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN paperback_stock 
                    ON paperback_stock.book_id = book_tbl.book_id
                WHERE flipkart_paperback_orders.ship_status = 3";
        $query3 = $this->db->query($sql3);
        $data['return'] = $query3->getResultArray();


        // ---------------- RETURN PENDING ----------------
        $sql4 = "SELECT 
                    COUNT(DISTINCT flipkart_paperback_orders.flipkart_order_id) AS total_orders,
                    COUNT(DISTINCT book_tbl.book_id) AS total_titles,
                    SUM(flipkart_paperback_orders.quantity * book_tbl.cost) AS total_mrp
                FROM flipkart_paperback_orders
                JOIN book_tbl 
                    ON flipkart_paperback_orders.book_id = book_tbl.book_id
                JOIN author_tbl 
                    ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN paperback_stock 
                    ON paperback_stock.book_id = book_tbl.book_id
                WHERE flipkart_paperback_orders.ship_status = 4";
        $query4 = $this->db->query($sql4);
        $data['return_pending'] = $query4->getResultArray();


        // ---------------- COMPLETED ALL ----------------
        $sql5 = "SELECT 
                    COUNT(DISTINCT flipkart_paperback_orders.flipkart_order_id) AS total_orders,
                    COUNT(DISTINCT book_tbl.book_id) AS total_titles,
                    SUM(flipkart_paperback_orders.quantity * book_tbl.cost) AS total_mrp
                FROM flipkart_paperback_orders
                JOIN book_tbl 
                    ON flipkart_paperback_orders.book_id = book_tbl.book_id
                JOIN author_tbl 
                    ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN paperback_stock 
                    ON paperback_stock.book_id = book_tbl.book_id
                WHERE flipkart_paperback_orders.ship_status = 1";
        $query5 = $this->db->query($sql5);
        $data['completed_all'] = $query5->getResultArray();

        return $data;
    }
    public function getPaperbackStockDetails()
    {
        $db = \Config\Database::connect();

        $sql = "SELECT count(*) as books_cnt FROM paperback_stock";
        $query = $db->query($sql);
        $data['books_cnt'] = $query->getResultArray()[0];

        $sql = "SELECT sum(quantity) as quantity_cnt FROM paperback_stock";
        $query = $db->query($sql);
        $data['quantity_cnt'] = $query->getResultArray()[0];

        $sql = "SELECT paperback_stock.book_id as id, book_title as title, quantity as qty, 
                    paperback_stock.bookfair, paperback_stock.bookfair2, paperback_stock.bookfair3, paperback_stock.bookfair4, paperback_stock.bookfair5,
                    paperback_stock.lost_qty, paperback_stock.stock_in_hand, author_tbl.author_name
                FROM paperback_stock, book_tbl, author_tbl
                WHERE author_tbl.author_id = book_tbl.author_name 
                AND paperback_stock.book_id = book_tbl.book_id";
        $query = $db->query($sql);
        $data['stock_data'] = $query->getResultArray();

        return $data;
    }
    public function totalPendingBooks()
    {
        $db = \Config\Database::connect();

        $sql = "SELECT 
            author_name,
            order_id,
            channel,
            customer_name,
            quantity,
            ship_date,
            order_date,
            book_id,
            book_title,
            qty, 
            stock_in_hand,
            bookfair,
            lost_qty
            FROM (
                SELECT 
                    author_tbl.author_name,
                    amazon_paperback_orders.amazon_order_id as order_id,
                    'Amazon' as channel,
                    '' as customer_name,
                    amazon_paperback_orders.quantity,
                    amazon_paperback_orders.ship_date,
                    amazon_paperback_orders.order_date,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.quantity as qty,
                    paperback_stock.stock_in_hand,
                    (paperback_stock.bookfair+paperback_stock.bookfair2+paperback_stock.bookfair3+paperback_stock.bookfair4+paperback_stock.bookfair5) as bookfair,
                    paperback_stock.lost_qty
                FROM 
                    amazon_paperback_orders
                    JOIN book_tbl ON amazon_paperback_orders.book_id = book_tbl.book_id
                    JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id
                    LEFT JOIN paperback_stock ON paperback_stock.book_id = book_tbl.book_id 
                WHERE 
                    amazon_paperback_orders.ship_status = 0

                UNION ALL

                SELECT 
                    author_tbl.author_name,
                    flipkart_paperback_orders.flipkart_order_id as order_id,
                    'Flipkart' as channel,
                    '' as customer_name,
                    flipkart_paperback_orders.quantity,
                    flipkart_paperback_orders.ship_date,
                    flipkart_paperback_orders.order_date,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.quantity as qty,
                    paperback_stock.stock_in_hand,
                    (paperback_stock.bookfair+paperback_stock.bookfair2+paperback_stock.bookfair3+paperback_stock.bookfair4+paperback_stock.bookfair5) as bookfair,
                    paperback_stock.lost_qty
                FROM 
                    flipkart_paperback_orders
                    JOIN book_tbl ON flipkart_paperback_orders.book_id = book_tbl.book_id
                    JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id
                    LEFT JOIN paperback_stock ON paperback_stock.book_id = book_tbl.book_id 
                WHERE 
                    flipkart_paperback_orders.ship_status = 0

                UNION ALL

                SELECT 
                    author_tbl.author_name,
                    pustaka_offline_orders_details.offline_order_id as order_id,
                    'Offline' as channel,
                    pustaka_offline_orders.customer_name as customer_name,
                    pustaka_offline_orders_details.quantity,
                    pustaka_offline_orders.ship_date,
                    pustaka_offline_orders.order_date,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.quantity as qty,
                    paperback_stock.stock_in_hand,
                    (paperback_stock.bookfair+paperback_stock.bookfair2+paperback_stock.bookfair3+paperback_stock.bookfair4+paperback_stock.bookfair5) as bookfair,
                    paperback_stock.lost_qty
                FROM 
                    pustaka_offline_orders_details
                    JOIN pustaka_offline_orders ON pustaka_offline_orders_details.offline_order_id=pustaka_offline_orders.order_id
                    JOIN book_tbl ON pustaka_offline_orders_details.book_id = book_tbl.book_id
                    JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id
                    LEFT JOIN paperback_stock ON paperback_stock.book_id = book_tbl.book_id 
                WHERE 
                    pustaka_offline_orders_details.ship_status = 0

                UNION ALL

                SELECT 
                    author_tbl.author_name,
                    pod_order.order_id as order_id,
                    'Online' as channel,
                    users_tbl.username as customer_name,
                    pod_order_details.quantity,
                    Null as ship_date,
                    pod_order_details.order_date,
                    pod_order_details.book_id,
                    book_tbl.book_title,
                    paperback_stock.quantity as qty,
                    paperback_stock.stock_in_hand,
                    (paperback_stock.bookfair+paperback_stock.bookfair2+paperback_stock.bookfair3+paperback_stock.bookfair4+paperback_stock.bookfair5) as bookfair,
                    paperback_stock.lost_qty
                FROM 
                    pod_order
                    JOIN pod_order_details ON pod_order.user_id = pod_order_details.user_id
                        AND pod_order.order_id = pod_order_details.order_id
                    JOIN book_tbl ON pod_order_details.book_id = book_tbl.book_id 
                    JOIN users_tbl ON  pod_order.user_id=users_tbl.user_id
                    JOIN author_tbl ON book_tbl.author_name = author_tbl.author_id 
                    LEFT JOIN paperback_stock ON paperback_stock.book_id = pod_order_details.book_id 
                WHERE 
                    pod_order.user_id != 0 
                    AND pod_order_details.status=0
            ) AS combined_orders ORDER BY ship_date DESC";

        $query = $db->query($sql);

        return $query->getResultArray();
    }
    public function totalPendingOrders()
    {
        $db = \Config\Database::connect();

        $sql ="(SELECT 
                    pod_author_order.order_id AS order_id,
                    author_tbl.author_name COLLATE utf8_general_ci AS customer_name,
                    pod_author_order.order_date AS order_date,
                    'Author Order' AS channel,
                    (SELECT COUNT(book_id) 
                        FROM pod_author_order_details 
                        WHERE pod_author_order.order_id = pod_author_order_details.order_id) AS no_of_title,
                    pod_author_order.invoice_number COLLATE utf8_general_ci AS invoice_number,
                    pod_author_order.ship_date AS ship_date
                FROM 
                    pod_author_order
                JOIN 
                    pod_author_order_details ON pod_author_order.order_id = pod_author_order_details.order_id
                JOIN 
                    author_tbl ON pod_author_order_details.author_id = author_tbl.author_id
                WHERE 
                    pod_author_order_details.completed_flag = 1 
                    AND pod_author_order.order_status = 0
                GROUP BY 
                    pod_author_order.order_id )
                UNION ALL 
                (SELECT
                    pod_bookshop_order.order_id AS order_id,
                    pod_bookshop.bookshop_name COLLATE utf8_general_ci AS customer_name,
                    pod_bookshop_order.order_date AS order_date,
                    'Bookshop Order' AS channel,
                    (SELECT COUNT(book_id) 
                        FROM pod_bookshop_order_details 
                        WHERE pod_bookshop_order_details.order_id = pod_bookshop_order.order_id) AS no_of_title,
                    pod_bookshop_order.invoice_no COLLATE utf8_general_ci AS invoice_number,
                    pod_bookshop_order.ship_date AS ship_date
                FROM
                    pod_bookshop_order
                JOIN
                    pod_bookshop ON pod_bookshop_order.bookshop_id = pod_bookshop.bookshop_id
                WHERE
                    pod_bookshop_order.status = 0
                )
                ORDER BY 
                    ship_date ASC";

        $query = $db->query($sql);

        return $query->getResultArray();
    }
    private function getFYDates($fy)
    {
        $currentYear = date('Y');
        $currentMonth = date('m');

        // FY starts on April 1
        if ($currentMonth < 4) {
            $startCurrentFY = ($currentYear - 1)."-04-01";
            $endCurrentFY   = $currentYear."-03-31";
        } else {
            $startCurrentFY = $currentYear."-04-01";
            $endCurrentFY   = ($currentYear + 1)."-03-31";
        }

        // Previous FY
        $startPrevFY = date("Y-m-d", strtotime("-1 year", strtotime($startCurrentFY)));
        $endPrevFY   = date("Y-m-d", strtotime("-1 year", strtotime($endCurrentFY)));

        return [
            'current'  => ["start" => $startCurrentFY, "end" => $endCurrentFY],
            'previous' => ["start" => $startPrevFY,   "end" => $endPrevFY],
        ];
    }
    public function ordersDashboardData($fy)
    {
        $fyDates = $this->getFYDates($fy);

        $dateCondition = "";
        if ($fy === "current") {
            $dateCondition = " AND order_date BETWEEN '{$fyDates['current']['start']}' AND '{$fyDates['current']['end']}' ";
        }
        if ($fy === "previous") {
            $dateCondition = " AND order_date BETWEEN '{$fyDates['previous']['start']}' AND '{$fyDates['previous']['end']}' ";
        }

        //ONLINE
        $online_sql = "
            SELECT
                (SELECT SUM(quantity) FROM pod_order_details WHERE status = 1 $dateCondition) AS units,
                (SELECT COUNT(DISTINCT book_id) FROM pod_order_details WHERE status = 1 $dateCondition) AS titles,
                (SELECT SUM(price) FROM pod_order_details WHERE status = 1 $dateCondition) AS sales,
                (SELECT COUNT(DISTINCT order_id) FROM pod_order WHERE 1=1 $dateCondition) AS total_orders
        ";
        $data['online'] = $this->db->query($online_sql)->getResultArray()[0];

        //OFFLINE
        $offline_sql = "
            SELECT 
                (SELECT COUNT(order_id) FROM pustaka_offline_orders WHERE 1=1 $dateCondition) AS total_orders,
                (SELECT SUM(quantity) FROM pustaka_offline_orders_details WHERE ship_status = 1) AS units,
                (SELECT COUNT(DISTINCT book_id) FROM pustaka_offline_orders_details WHERE ship_status = 1) AS titles,
                (SELECT SUM(total_amount) FROM pustaka_offline_orders_details WHERE ship_status = 1) AS sales
        ";
        $data['offline'] = $this->db->query($offline_sql)->getResultArray()[0];

        //AMAZON
        $amazon_sql = "
            SELECT 
                SUM(quantity) AS units,
                COUNT(DISTINCT book_id) AS titles,
                COUNT(DISTINCT amazon_order_id) AS total_orders
            FROM amazon_paperback_orders 
            WHERE ship_status=1 $dateCondition
        ";
        $data['amazon'] = $this->db->query($amazon_sql)->getResultArray()[0];

        //FLIPKART
        $flipkart_sql = "
            SELECT 
                SUM(quantity) AS units,
                COUNT(DISTINCT book_id) AS titles,
                COUNT(DISTINCT flipkart_order_id) AS total_orders
            FROM flipkart_paperback_orders 
            WHERE ship_status=1 $dateCondition
        ";
        $data['flipkart'] = $this->db->query($flipkart_sql)->getResultArray()[0];

        $author_sql = "SELECT sum(pod_author_order_details.quantity) as units,COUNT(DISTINCT(pod_author_order_details.book_id)) as titles,
                        sum(pod_author_order.net_total) as sales,COUNT(DISTINCT(pod_author_order.order_id)) as total_orders
                        FROM pod_author_order,pod_author_order_details
                        where pod_author_order.order_id =pod_author_order_details.order_id
                        and pod_author_order_details.status =1";
		$author_query = $this->db->query($author_sql);
		$data['author'] = $author_query->getResultArray()[0];

        $bookshop_sql = "SELECT sum(quantity) as units,COUNT(DISTINCT(book_id)) as titles,sum(total_amount) as sales
                        FROM pod_bookshop_order_details
                        where ship_status=1";
		$bookshop_query = $this->db->query($bookshop_sql);
		$data['bookshop'] = $bookshop_query->getResultArray()[0];
        //BOOKSHOP
        $bookshop_sql = "
            SELECT 
                (SELECT COUNT(DISTINCT order_id) FROM pod_bookshop_order WHERE status = 1 $dateCondition) AS total_orders,
                (SELECT SUM(quantity) FROM pod_bookshop_order_details WHERE ship_status = 1) AS units,
                (SELECT COUNT(DISTINCT book_id) FROM pod_bookshop_order_details WHERE ship_status = 1) AS titles,
                (SELECT SUM(total_amount) FROM pod_bookshop_order_details WHERE ship_status = 1) AS sales
        ";
        $data['bookshop'] = $this->db->query($bookshop_sql)->getResultArray()[0];

        //BOOK FAIR
        $bookfair_sql = "
            SELECT 
                COUNT(DISTINCT bfis.item) AS titles,
                SUM(bfis.quantity) AS units
            FROM book_fair_item_wise_sale bfis
            WHERE 1=1
        ";
        if ($fy === "current") {
            $bookfair_sql .= " AND bfis.book_fair_start_date BETWEEN '{$fyDates['current']['start']}' AND '{$fyDates['current']['end']}' ";
        }
        if ($fy === "previous") {
            $bookfair_sql .= " AND bfis.book_fair_start_date BETWEEN '{$fyDates['previous']['start']}' AND '{$fyDates['previous']['end']}' ";
        }

        $data['bookfair'] = $this->db->query($bookfair_sql)->getResultArray()[0];

        //LIBRARY
        $library_sql = "
            SELECT 
                SUM(copies) AS units, 
                COUNT(DISTINCT book_id) AS titles
            FROM library_orders
            WHERE 1=1 $dateCondition
        ";
        $data['library'] = $this->db->query($library_sql)->getResultArray()[0];

        return $data;
    }
    public function saveOfflineBulkOrder($postData, $books)
    {  
        // echo"<pre>";
        // print_r($postData);
        // print_r($books);

        $order_id = time(); // same as your old system

        // Insert main order
        $orderData = [
            'order_id'        => $order_id,
            'customer_name'   => trim($postData['customer_name']),
            'payment_type'    => trim($postData['payment_type']),
            'payment_status'  => trim($postData['payment_status']),
            'courier_charges' => trim($postData['courier_charge']),
            'address'         => trim($postData['shipping_address']),
            'mobile_no'       => trim($postData['mobile']),
            'ship_date'       => $postData['shipping_date'],
            'order_date'      => date('Y-m-d H:i:s'),
            'city'            => trim($postData['city']),
        ];

        $this->db->table('pustaka_offline_orders')->insert($orderData);

        // Insert each book into details table
        foreach ($books as $book) 
        {
            $bookData = [
                'offline_order_id' => $order_id,
                'book_id'          => $book['book_id'],
                'quantity'         => $book['quantity'],
                'discount'         => $book['discount'],
                'total_amount'     =>($book['price'] - ($book['price'] * $book['discount'] / 100)) * $book['quantity'], // or qty * price
                'ship_date'        => $postData['shipping_date'],
            ];

            $this->db->table('pustaka_offline_orders_details')->insert($bookData);
        }

        // Return a clean response
        return [
            'order_id'     => $order_id,
            'total_books'  => count($books),
            'message'      => 'Offline bulk order saved successfully!'
        ]; 
    }

    public function saveBookshopBulkOrder($postData, $books)
    {
        // echo "<pre>";
        // print_r($postData);
        // print_r($books);
        $db = \Config\Database::connect();
        $order_id = time(); // Unique order ID

        $insert_order = [
            'order_id'                        => $order_id,
            'bookshop_id'                     => $postData['bookshop_id'] ?? null,
            'order_date'                      => date('Y-m-d H:i:s'),
            'ship_date'                       => $postData['shipping_date'] ?? null,
            'ship_address'                    => $postData['shipping_address'] ?? null,
            'transport_payment'               => $postData['transport_payment'] ?? null,
            'preferred_transport'             => $postData['preferred_transport'] ?? null,
            'preferred_transport_name'        => $postData['preferred_transport_name'] ?? null,
            'payment_type'                    => $postData['payment_type'] ?? null,
            'payment_status'                  => $postData['payment_status'] ?? null,
            'vendor_po_order_number'          => $postData['buyer_number'] ?? null
        ];

         $db->table('pod_bookshop_order')->insert($insert_order);

        // Insert books into order details table
           foreach ($books as $b) {

            $detail = [
                'order_id'      => $order_id,
                'bookshop_id'   => $postData['bookshop_id'],
                'order_date'    => date('Y-m-d H:i:s'),
                'book_id'       => $b['book_id'],
                'book_price'    => $b['price'],
                'discount'      => $b['discount'],
                'quantity'      => $b['quantity'],
                'total_amount'  => ($b['price'] - ($b['price'] * $b['discount'] / 100)) * $b['quantity'],
                'ship_status'   => 0
            ];

            $db->table('pod_bookshop_order_details')->insert($detail);
           }

            return [
                'status' => 1,
                'order_id' => $order_id,
                'message' => 'BookShop Order created successfully'
            ];
    }

}

