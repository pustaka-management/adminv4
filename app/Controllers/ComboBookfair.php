<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ComboBookfairModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;

class ComboBookfair extends BaseController
{
    protected $combobookfairmodel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->combobookfairmodel = new ComboBookfairModel();
        helper('url');
        $this->session = session();
    }

    // ================= ADD ORDER FORM =================
    public function addSaleOrReturnOrder()
    {
        $data['title']     = '';
        $data['bookshops'] = $this->combobookfairmodel->getBookshops();
        $data['combos']    = $this->combobookfairmodel->getCombos();

        return view('printorders/bookfair/saveSaleOrReturnOrder', $data);
    }

    // ================= TRANSPORT AJAX =================
    public function getBookshopTransport()
    {
        $id = $this->request->getPost('bookshop_id');

        return $this->response->setJSON(
            $this->combobookfairmodel->getBookshopTransport($id)
        );
    }

    // ================= SAVE ORDER =================
    public function saveSaleOrReturnOrder()
    {
        $comboId = $this->request->getPost('combo_id');

        if (!$comboId) {
            return redirect()->back()->with('error', 'Please select combo.');
        }

        $orderId    = time();
        $createDate = date('Y-m-d H:i:s');

        $totalQty = $this->combobookfairmodel->getComboTotalQty($comboId);

        $orderData = [
            'order_id'                  => $orderId,
            'bookshop_id'               => $this->request->getPost('bookshop_id'),
            'combo_id'                  => $comboId,
            'book_fair_name'            => $this->request->getPost('book_fair_name'),
            'create_date'               => $createDate,
            'preferred_transport'       => $this->request->getPost('preferred_transport'),
            'preferred_transport_name'  => $this->request->getPost('preferred_transport_name'),
            'remark'                    => $this->request->getPost('remark'),
            'total_qty'                 => $totalQty,
            'status'                    => 0
        ];

        try {
            $this->combobookfairmodel->createOrder($orderData, $comboId);
            return redirect()->back()->with('success', 'Order Created Successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ================= RETURN VIEW =================
   public function bookfairBookshopreturnView($orderId)
    {
        $result = $this->combobookfairmodel->getReturnOrder($orderId);

        $data['title'] = 'Return Bookfair Order';
        $data['order'] = $result['order'];  
        $data['books'] = $result['books'];  

        return view('printorders/bookfair/bookfairBookShopReturn', $data);
    }

    // ================= SAVE RETURN =================
    public function bookfairBookshopsaveReturn()
    {
        $orderId   = $this->request->getPost('order_id');
        $returnQty = $this->request->getPost('return_qty');
        $discount  = (float) ($this->request->getPost('discount') ?? 0);

        $this->combobookfairmodel->processReturn($orderId, $returnQty, $discount);

        return redirect()->to('combobookfair/bookfairbookshoppendingorders')
            ->with('success', 'Return Completed');
    }

    // ================= PENDING =================
    public function bookfairBookshopPendingOrders()
    {
        $data['title']  = '';
        $data['orders'] = $this->combobookfairmodel->getBookfairOrders(0);
        $data['bookfair_sales'] = $this->combobookfairmodel->getBookfairSalesDetails();

        // echo "<pre>";
        // print_r($data['bookfair_sales']);

        return view('printorders/bookfair/bookFairDashboard', $data);
    }

    // ================= SHIPPED =================
    public function bookfairBookshopShippedOrders()
    {
        $data['title']  = 'Bookfair – Shipped Orders';
        $data['orders'] = $this->combobookfairmodel->getBookfairOrders(1);

        return view('printorders/bookfair/bookfairBookshopShippedOrders', $data);
    }

    // ================= SOLD =================
    public function bookfairBookshopSoldOrders()
    {
        $data['title']  = 'Bookfair – Sold Orders';
        $data['orders'] = $this->combobookfairmodel->getBookfairOrders(2);

        return view('printorders/bookfair/bookfairBookshopSoldOrders', $data);
    }

    // ================= ORDER DETAILS =================
    public function bookfairBookshopOrderDetails($orderId)
    {
        $data['title'] = 'Bookfair BookShop Sold Order Details';
        $data['order'] = $this->combobookfairmodel->getBookfairOrderDetails($orderId);

        if (empty($data['order'])) {
            return redirect()->back()->with('error', 'Order not found');
        }

        return view('printorders/bookfair/bookfairBookshopOrderDetails', $data);
    }

    public function bookfairShippedOrderDetails($orderId)
    {
        $data['title'] = 'Bookfair BookShop Shipped Order Details';
        $data['order'] = $this->combobookfairmodel->getBookfairOrderDetails($orderId);

        if (empty($data['order'])) {
            return redirect()->back()->with('error', 'Order not found');
        }

        return view('printorders/bookfair/bookfairShippedOrderDetails', $data);
    }
        public function exportSingleShippedOrder($orderId)
    {
        return $this->combobookfairmodel->exportSingleShippedOrderExcel($orderId);
    }
        public function exportBookshopOrderExcel($orderId)
    {
        $model = new \App\Models\Combobookfairmodel();

        return $model->exportSoldOrderExcel($orderId);
    }

    // ================= COMBO =================
    public function bookfairComboDetails()
    {
        $data['title']  = '';
        $data['combos'] = $this->combobookfairmodel->getBookfairCombos();

        return view('printorders/bookfair/comboDashboard', $data);
    }

    public function bookfairComboBooks($comboId)
    {
        $data['title'] = '';
        $data['books'] = $this->combobookfairmodel->getBookfairComboBooks($comboId);

        return view('printorders/bookfair/comboBooks', $data);
    }

    public function comboOrderDetails($comboId)
    {
        $data['title']  = '';
        $data['orders'] = $this->combobookfairmodel->getComboOrders($comboId);

        return view('printorders/bookfair/comboOrderDetails', $data);
    }

    public function comboBookOrders($bookId)
    {
        $data['title']  = '';
        $data['orders'] = $this->combobookfairmodel->getComboBookOrders($bookId);

        return view('printorders/bookfair/comboBookOrders', $data);
    }

    public function uploadView()
    {
        $data['title'] = '';
            $data['subTitle'] = '';
            return view('printorders/bookfair/createComboView',$data);

    }
    public function uploadProcess()
    {


    // echo "<pre>";
    // print_r($_POST);
        helper(['form', 'url']);

        $uploadType = $this->request->getPost('upload_type');
        $combo_pack_name =  $this->request->getPost('combo_pack_name');
        $rows = [];

        /*  READ INPUT DATA */
        if ($uploadType === 'excel') {

            $file = $this->request->getFile('excel_file');
            if (!$file || !$file->isValid()) {
                return redirect()->back()->with('error', 'Please upload a valid Excel file.');
            }

            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads', $newName);
            $filePath = WRITEPATH . 'uploads/' . $newName;

            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray(null, true, true, true);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error reading Excel: ' . $e->getMessage());
            }

        } elseif ($uploadType === 'manual') {

            $manualStock = trim($this->request->getPost('manual_stock'));
            if (empty($manualStock)) {
                return redirect()->back()->with('error', 'Manual stock cannot be empty.');
            }

            /*
            manual_stock example:
            2-2,7839-1
            */

            $pairs = explode(',', $manualStock);

            foreach ($pairs as $pair) {
                if (!str_contains($pair, '-')) continue;

                [$bookId, $qty] = array_map('trim', explode('-', $pair));

                if (!is_numeric($bookId) || !is_numeric($qty)) continue;

                $rows[] = [
                    'A' => $bookId,
                    'B' => '',           // title ignored
                    'C' => (int) $qty,
                    'D' => 0             // default discount
                ];
            }
        } else {
            return redirect()->back()->with('error', 'Invalid upload type.');
        }

        /* =======================
        * 2. COMMON PROCESSING
        * ======================= */
       $matched = [];
$mismatched = [];

foreach ($rows as $i => $row) {

    // Skip header ONLY for Excel
    if ($uploadType === 'excel' && $i == 1) {
        continue;
    }

    $book_id     = trim($row['A'] ?? '');
    $excel_title = trim($row['B'] ?? '');
    $quantity    = (int) ($row['C'] ?? 0);
    $discount    = (float) ($row['D'] ?? 0);

    if (empty($book_id) || $quantity <= 0) {
        continue;
    }

    // 🔍 Check if book_id starts with a number
    $startsWithNumber = ctype_digit(substr($book_id, 0, 1));

    // 🔄 Decide table based on book_id format
    if ($startsWithNumber) {
        $dbBook = $this->db->table('book_tbl')
            ->where('book_id', $book_id)
            ->get()
            ->getRowArray();

        $db_title_key = 'book_title';
    } else {
        $dbBook = $this->db->table('tp_publisher_bookdetails')
            ->where('sku_no', $book_id)
            ->get()
            ->getRowArray();

        $db_title_key = 'book_title'; // change if column name differs
    }

    if ($dbBook) {
        $db_title = trim($dbBook[$db_title_key]);

        // Manual → skip title check
        if ($uploadType === 'manual' || strcasecmp($excel_title, $db_title) === 0) {

            $matched[] = [
                'book_id'  => $book_id,
                'title'    => $db_title,
                'quantity' => $quantity,
            ];

        } else {
            $mismatched[] = [
                'book_id'     => $book_id,
                'excel_title' => $excel_title,
                'db_title'    => $db_title,
                'quantity'    => $quantity,
            ];
        }

    } else {
        $mismatched[] = [
            'book_id'     => $book_id,
            'excel_title' => $excel_title,
            'db_title'    => 'Not Found in DB',
            'quantity'    => $quantity,
        ];
    }
}

        /* =======================
        * 3. STORE & RETURN VIEW
        * ======================= */

        session()->set('matched_books', $matched);
        session()->set('mismatched_books', $mismatched);

        return view('printorders/bookfair/ComboSummaryView', [
            'matched'     => $matched,
            'mismatched'  => $mismatched,
            'totalTitles' => count($matched),
             'combo_pack_name'=> $combo_pack_name,
            'title'       => '',
            'subTitle'    => '',
        ]);
    }

   public function updateAcceptBooks()
{
    $selected = $this->request->getPost('selected');
    $titles = $this->request->getPost('book_title');
    $quantities = $this->request->getPost('quantity');
    $combo_pack_name = $this->request->getPost('combo_pack_name');

    // Get session data
    $matched = session()->get('matched_books') ?? [];
    $mismatched = session()->get('mismatched_books') ?? [];

    if (!empty($selected)) {
        foreach ($selected as $bookId) {
            foreach ($mismatched as $key => $book) {
                if ($book['book_id'] == $bookId) {

                    $matched[] = [
                        'book_id'  => $book['book_id'],
                        'title'    => $titles[$bookId] ?? $book['db_title'],
                        'quantity' => $quantities[$bookId] ?? $book['quantity'],
                    ];

                    unset($mismatched[$key]);
                    break;
                }
            }
        }
    }

    // 🔥 IMPORTANT: save BOTH to session
    session()->set('matched_books', array_values($matched));
    session()->set('mismatched_books', array_values($mismatched));

    $totalTitles = count($matched);

    return view('printorders/bookfair/ComboSummaryView', [
        'matched' => $matched,
        'mismatched' => $mismatched,
        'totalTitles'=> $totalTitles,
        'combo_pack_name'=>$combo_pack_name,
        'title' => '',
        'subTitle' => '',
    ]);
}


    public function combopackupload()
    {

    // echo "<pre>";
    // print_r($_POST);
        $acceptBooks = session()->get('matched_books'); 
        $comboName   = $this->request->getPost('combo_pack_name');

        $result = $this->combobookfairmodel->combopackupload($acceptBooks, $comboName);

        session()->setFlashdata('success', 'Combo created successfully!');

        session()->remove([
            'matched_books',
            'mismatched_books',
            'upload_type',
            'accept_books'
        ]);

        return redirect()->to(base_url('combobookfair/createcombo'));
    }

    public function bookfairdetailsview($order_id)
    {
        $data['order_id'] = $order_id;
        $data['bookfair_details'] = $this->combobookfairmodel->getBookFairdetails($order_id);
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/bookfair/bookfairDetailsView', $data);

    }
    public function ship($order_id)
    {
        $model = new ComboBookfairModel();

        $result = $model->shipBookfairOrder($order_id);

        if ($result) {
            return redirect()
                ->to(base_url('combobookfair/bookfairbookshoppendingorders'))
                ->with('success', 'Order shipped successfully');
        } else {
            return redirect()
                ->back()
                ->with('error', 'Order shipping failed');
        }
    }
    
    public function downloadbookfairexcel($order_id)
    {
        $result = $this->combobookfairmodel->getBookFairdetails($order_id);

        if (empty($result['bookfair_details'])) {
            return redirect()->back()->with('error', 'No book data found');
        }

        $books = $result['bookfair_details'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = [
            'S.No',
            'Book ID',
            'Title',
            'Author',
            'Language',
            'Send Qty',
            'Book Price',
            'Total Amount'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col.'1', $header);
            $col++;
        }

        // Data
        $rowNum = 2;
        $i = 1;
        $grandTotal = 0;

        foreach ($books as $row) {
            $total = $row['send_qty'] * $row['book_price'];
            $grandTotal += $total;

            $sheet->setCellValue('A'.$rowNum, $i++);
            $sheet->setCellValue('B'.$rowNum, $row['book_id']);
            $sheet->setCellValue('C'.$rowNum, $row['book_title']);
            $sheet->setCellValue('D'.$rowNum, $row['author_name']);
            $sheet->setCellValue('E'.$rowNum, $row['language_name']);
            $sheet->setCellValue('F'.$rowNum, $row['send_qty']);
            $sheet->setCellValue('G'.$rowNum, $row['book_price']);
            // $sheet->setCellValue(
            //     'H'.$rowNum,
            //     date('d-m-Y', strtotime($row['sending_date']))
            // );
            $sheet->setCellValue('H'.$rowNum, $total);

            $rowNum++;
        }

        // Grand total row
        $sheet->setCellValue('G'.$rowNum, 'Grand Total');
        $sheet->setCellValue('H'.$rowNum, $grandTotal);

        $fileName = 'Bookfair_Books_'.$order_id.'.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
    
}

