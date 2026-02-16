<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
    return $this->db->query("SELECT * FROM pod_bookshop")
        ->getResultArray();
}

    public function getBookshopTransport($bookshopId)
{
    return $this->db->query(
        "SELECT * FROM pod_bookshop WHERE bookshop_id = ?",
        [$bookshopId]
    )->getRowArray();
}

    // ================= COMBO DROPDOWN =================
    public function getCombos()
{
    return $this->db->query(
        "SELECT combo_id, pack_name
         FROM bookfair_combo_pack
         ORDER BY created_date DESC"
    )->getResultArray();
}


    // ================= TOTAL QTY =================
   public function getComboTotalQty($comboId)
{
    $row = $this->db->query(
        "SELECT SUM(default_value) AS total_qty
         FROM bookfair_combo_pack_details
         WHERE combo_id = ?",
        [$comboId]
    )->getRowArray();

    return $row['total_qty'] ?? 0;
}

    // ================= CREATE ORDER =================
    public function createOrder($orderData, $comboId)
{
    $db = \Config\Database::connect();
    $db->transBegin();

    // MAIN ORDER INSERT
    $db->query(
        "INSERT INTO bookfair_sale_or_return_orders
        (order_id, bookshop_id, create_date, combo_id, total_qty, status)
        VALUES (?, ?, ?, ?, ?, ?)",
        [
            $orderData['order_id'],
            $orderData['bookshop_id'],
            $orderData['create_date'],
            $orderData['combo_id'],
            $orderData['total_qty'],
            $orderData['status']
        ]
    );

    // FETCH COMBO BOOKS
    $comboBooks = $db->query(
        "SELECT * FROM bookfair_combo_pack_details WHERE combo_id = ?",
        [$comboId]
    )->getResultArray();

    if (!$comboBooks) {
        throw new \Exception('Combo books not found');
    }

    foreach ($comboBooks as $row) {

        $sku = $row['book_id'];
        $qty = $row['default_value'];

        // GET PRICE FROM PUBLISHER TABLE USING SKU
        $pubBook = $db->query(
            "SELECT mrp FROM tp_publisher_bookdetails WHERE sku_no = ?",
            [$sku]
        )->getRowArray();

        if (!$pubBook) {
            continue;
        }

        $price = $pubBook['mrp'] ?? 0;

        // INSERT ORDER DETAILS
        $db->query(
            "INSERT INTO bookfair_sale_or_return_order_details
            (order_id, bookshop_id, create_date, book_id, send_qty, book_price, discount, status)
            VALUES (?, ?, ?, ?, ?, ?, 0, 0)",
            [
                $orderData['order_id'],
                $orderData['bookshop_id'],
                $orderData['create_date'],
                $sku,
                $qty,
                $price
            ]
        );
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
    $db = \Config\Database::connect();

    // ORDER DETAILS (unchanged)
    $sql1 = "
        SELECT o.*, bs.bookshop_name, cp.pack_name
        FROM bookfair_sale_or_return_orders o
        LEFT JOIN pod_bookshop bs ON bs.bookshop_id = o.bookshop_id
        LEFT JOIN bookfair_combo_pack cp ON cp.combo_id = o.combo_id
        WHERE o.order_id = ?
    ";

    $data['order'] = $db->query($sql1, [$orderId])->getRowArray();


    // BOOK LIST (TITLE + AUTHOR ONLY, RK SUPPORT)
    $sql2 = "
    SELECT 
        d.id,
        d.order_id,
        d.book_id,
        d.send_qty,  

        CASE 
            WHEN d.book_id LIKE 'RK%' THEN pb.book_title
            ELSE b.book_title
        END AS book_title,

        CASE 
            WHEN d.book_id LIKE 'RK%' THEN pa.author_name
            ELSE a.author_name
        END AS author_name

    FROM bookfair_sale_or_return_order_details d

    LEFT JOIN book_tbl b ON b.book_id = d.book_id
    LEFT JOIN author_tbl a ON a.author_id = b.author_name

    LEFT JOIN tp_publisher_bookdetails pb ON pb.sku_no = d.book_id
    LEFT JOIN tp_publisher_author_details pa ON pa.publisher_id = pb.publisher_id

    WHERE d.order_id = ?
";


    $data['books'] = $db->query($sql2, [$orderId])->getResultArray();

    return $data;
}

    public function processReturn($orderId, $returnQtyArr, $discountPercent)
{
    $db = \Config\Database::connect();
    $db->transBegin();

    // Get order details
    $details = $db->query(
        "SELECT * FROM bookfair_sale_or_return_order_details WHERE order_id = ?",
        [$orderId]
    )->getResultArray();

    if (!$details) {
        throw new \Exception('No order details found');
    }

    foreach ($details as $row) {

        $bookId = $row['book_id'];
        $price  = $row['book_price'];

        $saleQty   = $row['sale_qty'] ?? $row['send_qty'];
        $returnQty = $returnQtyArr[$bookId] ?? 0;

        if ($returnQty > $saleQty) {
            $returnQty = $saleQty;
        }

        $finalSaleQty = $saleQty - $returnQty;

        // Sales amount
        $salesAmount = $finalSaleQty * $price;

        // Discount calculation
        $discountAmount = ($discountPercent > 0)
            ? round(($salesAmount * $discountPercent) / 100, 2)
            : 0;

        $finalAmount = max(0, $salesAmount - $discountAmount);

        // Update order details row
        $db->query(
            "UPDATE bookfair_sale_or_return_order_details
             SET sale_qty = ?,
                 return_qty = ?,
                 discount = ?,
                 total_amount = ?,
                 status = 2
             WHERE order_id = ? AND book_id = ?",
            [
                $finalSaleQty,
                ($row['return_qty'] ?? 0) + $returnQty,
                $discountPercent,
                $finalAmount,
                $orderId,
                $bookId
            ]
        );
    }

    // Update main order status
    $db->query(
        "UPDATE bookfair_sale_or_return_orders
         SET status = 2
         WHERE order_id = ?",
        [$orderId]
    );

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
        $sql = "
            SELECT 
                o.order_id,
                o.sending_date,
                s.bookshop_name,
                c.pack_name,
                c.no_of_title,
                c.total_quantity,
                MAX(d.discount) AS discount
            FROM bookfair_sale_or_return_orders o
            LEFT JOIN pod_bookshop s 
                ON s.bookshop_id = o.bookshop_id
            LEFT JOIN bookfair_combo_pack c 
                ON c.combo_id = o.combo_id
            LEFT JOIN bookfair_sale_or_return_order_details d 
                ON d.order_id = o.order_id
            WHERE o.status = ?
            GROUP BY o.order_id
            ORDER BY o.order_id DESC
        ";

        return $this->db->query($sql, [$status])->getResultArray();
    }


    public function getBookfairOrderDetails($orderId)
{
    $db = \Config\Database::connect();

    // ORDER DETAILS
    $sql1 = "
        SELECT o.*, s.*, c.*
        FROM bookfair_sale_or_return_orders o
        LEFT JOIN pod_bookshop s ON s.bookshop_id = o.bookshop_id
        LEFT JOIN bookfair_combo_pack c ON c.combo_id = o.combo_id
        WHERE o.order_id = ?
    ";

    $details = $db->query($sql1, [$orderId])->getRowArray();


    // BOOK LIST (RK + NORMAL SUPPORT)
    $sql2 = "
        SELECT 
            d.*,

            /* BOOK TITLE */
            CASE 
                WHEN d.book_id LIKE 'RK%' THEN pb.book_title
                ELSE b.book_title
            END AS book_title,

            /* AUTHOR */
            CASE 
                WHEN d.book_id LIKE 'RK%' THEN pa.author_name
                ELSE a.author_name
            END AS author_name,

            /* LANGUAGE */
            CASE 
                WHEN d.book_id LIKE 'RK%' THEN pl.language_name
                ELSE l.language_name
            END AS language_name

        FROM bookfair_sale_or_return_order_details d

        /* NORMAL BOOK TABLES */
        LEFT JOIN book_tbl b 
            ON b.book_id = d.book_id

        LEFT JOIN author_tbl a 
            ON a.author_id = b.author_name

        LEFT JOIN language_tbl l 
            ON l.language_id = b.language


        /* RK BOOK TABLES */
        LEFT JOIN tp_publisher_bookdetails pb 
            ON pb.sku_no = d.book_id

        LEFT JOIN tp_publisher_author_details pa 
            ON pa.publisher_id = pb.publisher_id

        LEFT JOIN language_tbl pl 
            ON pl.language_id = pb.language

        WHERE d.order_id = ?
    ";

    $list = $db->query($sql2, [$orderId])->getResultArray();

    return [
        'details' => $details,
        'list'    => $list
    ];
}


    // ================= COMBO =================
    public function getBookfairCombos()
    {
        $sql = "
            SELECT 
                c.combo_id,
                c.pack_name,
                c.total_quantity,
                COUNT(d.book_id) AS book_count
            FROM bookfair_combo_pack c
            LEFT JOIN bookfair_combo_pack_details d 
                ON d.combo_id = c.combo_id
            GROUP BY c.combo_id
            ORDER BY c.combo_id DESC
        ";

        return $this->db->query($sql)->getResultArray();
    }

   public function getBookfairComboBooks($comboId)
{
    $db = \Config\Database::connect();

    $sql = "
        SELECT 
            c.pack_name,
            d.book_id,

            CASE 
                WHEN d.book_id LIKE 'RK%' THEN pb.book_title
                ELSE b.book_title
            END AS book_title,

            CASE 
                WHEN d.book_id LIKE 'RK%' THEN pb.mrp
                ELSE b.paper_back_inr
            END AS paper_back_inr,

            CASE 
                WHEN d.book_id LIKE 'RK%' THEN pa.author_name
                ELSE a.author_name
            END AS author_name,

            CASE 
                WHEN d.book_id LIKE 'RK%' THEN pb.language
                ELSE l.language_name
            END AS language_name,

            /*  STOCK FROM CORRECT TABLE */
            CASE 
                WHEN d.book_id LIKE 'RK%' THEN IFNULL(ps.stock_in_hand,0)
                ELSE IFNULL(p.stock_in_hand,0)
            END AS stock,

            d.default_value

        FROM bookfair_combo_pack_details d

        LEFT JOIN bookfair_combo_pack c 
            ON c.combo_id = d.combo_id

        LEFT JOIN book_tbl b ON b.book_id = d.book_id
        LEFT JOIN author_tbl a ON a.author_id = b.author_name
        LEFT JOIN language_tbl l ON l.language_id = b.language
        LEFT JOIN paperback_stock p ON p.book_id = b.book_id

        /* RK BOOK TABLE */
        LEFT JOIN tp_publisher_bookdetails pb 
            ON pb.sku_no = d.book_id

        LEFT JOIN tp_publisher_author_details pa 
            ON pa.publisher_id = pb.publisher_id

        /*  IMPORTANT JOIN */
       LEFT JOIN tp_publisher_book_stock ps 
    ON ps.book_id = pb.book_id

        WHERE d.combo_id = ?
    ";

    return $db->query($sql, [$comboId])->getResultArray();
}



    public function getComboOrders($comboId)
{
    $sql = "
        SELECT 
            o.order_id,
            o.combo_id,
            o.book_fair_name,
            o.sending_date,
            o.total_qty,
            o.status,
            bs.bookshop_name,
            c.pack_name AS combo_name
        FROM bookfair_sale_or_return_orders AS o
        LEFT JOIN pod_bookshop AS bs 
            ON bs.bookshop_id = o.bookshop_id
        LEFT JOIN bookfair_combo_pack AS c 
            ON c.combo_id = o.combo_id
        WHERE o.combo_id = ?
        ORDER BY o.order_id DESC
    ";

    return $this->db->query($sql, [$comboId])->getResultArray();
}



   public function getComboBookOrders($bookId)
{
    $db = \Config\Database::connect();

    $sql = "
        SELECT 
            d.*,

            /* TITLE */
            CASE 
                WHEN pb.book_id IS NOT NULL THEN pb.book_title
                ELSE b.book_title
            END AS book_title,

            /* AUTHOR */
            CASE 
                WHEN pb.book_id IS NOT NULL THEN pa.author_name
                ELSE a.author_name
            END AS author_name,

            bs.bookshop_name,
            o.book_fair_name

        FROM bookfair_sale_or_return_order_details d

        LEFT JOIN book_tbl b 
            ON b.book_id = d.book_id

        LEFT JOIN author_tbl a 
            ON a.author_id = b.author_name

        /* PUBLISHER BOOK TABLE */
        LEFT JOIN tp_publisher_bookdetails pb 
            ON pb.book_id = d.book_id

        LEFT JOIN tp_publisher_author_details pa 
            ON pa.publisher_id = pb.publisher_id

        LEFT JOIN pod_bookshop bs 
            ON bs.bookshop_id = d.bookshop_id

        LEFT JOIN bookfair_sale_or_return_orders o 
            ON o.order_id = d.order_id

        WHERE d.book_id = ?
    ";

    return $db->query($sql, [$bookId])->getResultArray();
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

        // Get last inserted combo ID
        $combo_id = $this->db->insertID();

        // Insert combo pack details
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
    public function getBookfairSalesDetails()
    {
        $db = \Config\Database::connect(); 
        
        $sql ="SELECT 
                    bookfair_sale_or_return_orders.order_id,
                    bookfair_sale_or_return_orders.create_date,
                    pod_bookshop.bookshop_name,
                    bookfair_combo_pack.pack_name,
                    COUNT(bookfair_sale_or_return_order_details.book_id) AS no_of_titles,
                    SUM(bookfair_sale_or_return_order_details.send_qty) AS send_qty,
                    bookfair_sale_or_return_order_details.discount
                FROM
                    bookfair_sale_or_return_orders
                JOIN
                    pod_bookshop 
                    ON pod_bookshop.bookshop_id = bookfair_sale_or_return_orders.bookshop_id
                JOIN
                    bookfair_sale_or_return_order_details 
                    ON bookfair_sale_or_return_orders.order_id = bookfair_sale_or_return_order_details.order_id
                JOIN
                    bookfair_combo_pack 
                    ON bookfair_combo_pack.combo_id = bookfair_sale_or_return_orders.combo_id
                WHERE 
                    bookfair_sale_or_return_orders.status = 0
                GROUP BY 
                    bookfair_sale_or_return_orders.order_id,
                    bookfair_sale_or_return_orders.sending_date,
                    pod_bookshop.bookshop_name,
                    bookfair_combo_pack.pack_name,
                    bookfair_sale_or_return_order_details.discount
                ORDER BY 
                    bookfair_sale_or_return_orders.order_id DESC";

        $query = $db->query($sql);
        $data['bookfair_list'] = $query->getResultArray();
        return $data;
    }
    public function getBookFairdetails($order_id)
{
    $db = \Config\Database::connect(); 

    /* ---------------- BOOKFAIR BASIC DETAILS ---------------- */

    $sql = "SELECT 
                o.book_fair_name,
                o.preferred_transport_name,
                s.contact_person_name,
                s.mobile,
                s.address
            FROM bookfair_sale_or_return_orders o
            JOIN pod_bookshop s 
                ON s.bookshop_id = o.bookshop_id
            WHERE o.order_id = ?";

    $data['bookfair'] = $db->query($sql, [$order_id])->getResultArray();


    /* ---------------- COMBO DETAILS ---------------- */

    $sql1 = "SELECT 
                c.pack_name,
                o.create_date,
                o.preferred_transport_name,
                o.remark
            FROM bookfair_sale_or_return_orders o
            JOIN bookfair_combo_pack c 
                ON c.combo_id = o.combo_id
            WHERE o.order_id = ?";

    $data['bookfair_combo'] = $db->query($sql1, [$order_id])->getResultArray();


    /* ---------------- BOOK DETAILS (SKU + NORMAL BOTH) ---------------- */

    $sql2 = "SELECT  
                d.id,
                o.order_id,
                d.book_id,

                -- TITLE (publisher first, else normal)
                COALESCE(pb.book_title, b.book_title) AS book_title,

                -- AUTHOR
                COALESCE(pa.author_name, a.author_name) AS author_name,

                -- LANGUAGE
                COALESCE(l2.language_name, l1.language_name) AS language_name,

                -- PRICE
                COALESCE(pb.mrp, d.book_price) AS book_price,

                d.send_qty,
                d.create_date

            FROM bookfair_sale_or_return_order_details d

            JOIN bookfair_sale_or_return_orders o
                ON o.order_id = d.order_id

            -- NORMAL BOOK TABLE
            LEFT JOIN book_tbl b
                ON b.book_id = d.book_id

            LEFT JOIN author_tbl a
                ON a.author_id = b.author_name

            LEFT JOIN language_tbl l1
                ON l1.language_id = b.language

            -- PUBLISHER BOOK TABLE (SKU)
            LEFT JOIN tp_publisher_bookdetails pb
                ON pb.sku_no = d.book_id

            LEFT JOIN language_tbl l2
                ON l2.language_id = pb.language

            LEFT JOIN tp_publisher_author_details pa
                ON pa.publisher_id = pb.publisher_id

            WHERE o.order_id = ?";

    $data['bookfair_details'] = $db->query($sql2, [$order_id])->getResultArray();

    return $data;
}

    public function shipBookfairOrder($order_id)
{
    $db = \Config\Database::connect();
    $db->transBegin();

    /* GET ORDER CREATE DATE */
    $sql1 = "SELECT create_date 
             FROM bookfair_sale_or_return_orders 
             WHERE order_id = ?";

    $order = $db->query($sql1, [$order_id])->getRowArray();

    if (!$order) {
        return false;
    }

    $createDate = $order['create_date'];

    /* UPDATE DETAILS TABLE */
    $sql2 = "UPDATE bookfair_sale_or_return_order_details
             SET status = 1,
                 sending_date = ?
             WHERE order_id = ?";

    $db->query($sql2, [$createDate, $order_id]);

    /* UPDATE MAIN ORDER TABLE */
    $sql3 = "UPDATE bookfair_sale_or_return_orders
             SET status = 1,
                 sending_date = ?
             WHERE order_id = ?";

    $db->query($sql3, [$createDate, $order_id]);

    /* TRANSACTION CHECK */
    if ($db->transStatus() === false) {
        $db->transRollback();
        return false;
    }

    $db->transCommit();
    return true;
}

public function exportSingleShippedOrderExcel($orderId)
{
    $order = $this->getBookfairOrderDetails($orderId);

    if (empty($order['details'])) {
        return false;
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // ===== Order Info =====
    $sheet->setCellValue('A1', 'Order ID:');
    $sheet->setCellValue('B1', $order['details']['order_id']);

    $sheet->setCellValue('A2', 'Bookshop:');
    $sheet->setCellValue('B2', $order['details']['bookshop_name']);

    $sheet->setCellValue('A3', 'Sending Date:');
    $sheet->setCellValue('B3', !empty($order['details']['sending_date']) 
        ? date('d-m-Y', strtotime($order['details']['sending_date'])) 
        : '');

    // ===== Table Header =====
    $sheet->setCellValue('A5', '#');
    $sheet->setCellValue('B5', 'Book ID');
    $sheet->setCellValue('C5', 'Title');
    $sheet->setCellValue('D5', 'Author');
    $sheet->setCellValue('E5', 'Language');
    $sheet->setCellValue('F5', 'Qty');
    $sheet->setCellValue('G5', 'Price');
    $sheet->setCellValue('H5', 'Total');

    $sheet->getStyle('A5:H5')->getFont()->setBold(true);

    $row = 6;
    $i = 1;
    $grandTotal = 0;

    foreach ($order['list'] as $book) {

        $rowTotal = $book['total_amount'] ?? ($book['send_qty'] * $book['book_price']);
        $grandTotal += $rowTotal;

        $sheet->setCellValue('A' . $row, $i++);
        $sheet->setCellValue('B' . $row, $book['book_id']);
        $sheet->setCellValue('C' . $row, $book['book_title']);
        $sheet->setCellValue('D' . $row, $book['author_name']);
        $sheet->setCellValue('E' . $row, $book['language_name']);
        $sheet->setCellValue('F' . $row, $book['send_qty']);
        $sheet->setCellValue('G' . $row, $book['book_price']);
        $sheet->setCellValue('H' . $row, $rowTotal);

        $row++;
    }

    // ===== Grand Total =====
    $sheet->setCellValue('G' . $row, 'Grand Total');
    $sheet->setCellValue('H' . $row, $grandTotal);
    $sheet->getStyle('G' . $row . ':H' . $row)->getFont()->setBold(true);

    // Auto column width
    foreach (range('A', 'H') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $writer = new Xlsx($spreadsheet);
    $filename = 'Order_' . $orderId . '_' . date('Ymd_His') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
}

public function exportSoldOrderExcel($orderId)
{
    $order = $this->getBookfairOrderDetails($orderId);

    if(empty($order['details'])){
        return false;
    }

    $d     = $order['details'];
    $books = $order['list'];

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // ===== TITLE =====
    $sheet->setCellValue('A1', 'Bookshop Sold Order Details');
    $sheet->mergeCells('A1:K1');

    // ===== ORDER INFO =====
    $sheet->setCellValue('A3', 'Order ID');
    $sheet->setCellValue('B3', $d['order_id']);

    $sheet->setCellValue('A4', 'Bookshop');
    $sheet->setCellValue('B4', $d['bookshop_name']);

    $sheet->setCellValue('A5', 'Contact');
    $sheet->setCellValue('B5', $d['contact_person_name']);

    $sheet->setCellValue('A6', 'Mobile');
    $sheet->setCellValue('B6', $d['mobile']);

    $sheet->setCellValue('A7', 'Combo Pack');
    $sheet->setCellValue('B7', $d['pack_name']);

    // ===== TABLE HEADER =====
    $row = 9;

    $sheet->setCellValue('A'.$row,'#');
    $sheet->setCellValue('B'.$row,'Book ID');
    $sheet->setCellValue('C'.$row,'Title');
    $sheet->setCellValue('D'.$row,'Author');
    $sheet->setCellValue('E'.$row,'Language');
    $sheet->setCellValue('F'.$row,'Qty');
    $sheet->setCellValue('G'.$row,'Price');
    $sheet->setCellValue('H'.$row,'Sale Qty');
    $sheet->setCellValue('I'.$row,'Discount');
    $sheet->setCellValue('J'.$row,'Total');
    $sheet->setCellValue('K'.$row,'Sending Date');

    $sheet->getStyle('A'.$row.':K'.$row)->getFont()->setBold(true);

    $row++;

    // ===== DATA =====
    $i = 1;
    $grand = 0;

    foreach($books as $b){

        $total = $b['total_amount'];
        $grand += $total;

        $sheet->setCellValue('A'.$row,$i++);
        $sheet->setCellValue('B'.$row,$b['book_id']);
        $sheet->setCellValue('C'.$row,$b['book_title']);
        $sheet->setCellValue('D'.$row,$b['author_name']);
        $sheet->setCellValue('E'.$row,$b['language_name']);
        $sheet->setCellValue('F'.$row,$b['send_qty']);
        $sheet->setCellValue('G'.$row,$b['book_price']);
        $sheet->setCellValue('H'.$row,$b['sale_qty']);
        $sheet->setCellValue('I'.$row,$b['discount']);
        $sheet->setCellValue('J'.$row,$total);
        $sheet->setCellValue(
            'K'.$row,
            !empty($b['sending_date']) ? date('d-m-Y',strtotime($b['sending_date'])) : ''
        );

        $row++;
    }

    // ===== GRAND TOTAL =====
    $sheet->setCellValue('I'.$row,'Grand Total');
    $sheet->setCellValue('J'.$row,$grand);
    $sheet->getStyle('I'.$row.':J'.$row)->getFont()->setBold(true);

    // ===== AUTO SIZE =====
    foreach(range('A','K') as $col){
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // ===== DOWNLOAD =====
    $filename = "Sold_Order_".$orderId.".xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
        
}

