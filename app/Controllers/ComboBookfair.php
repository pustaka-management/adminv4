<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ComboBookfairModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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
        $data['title']  = 'Bookfair Bookshop Sale or Return Orders';
        $data['orders'] = $this->combobookfairmodel->getBookfairOrders(0);

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
        $data['title']  = '';
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

        /* =======================
        * 1. READ INPUT DATA
        * ======================= */
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

            if (empty($book_id) || $quantity <= 0) continue;

            $dbBook = $this->db->table('book_tbl')
                ->where('book_id', $book_id)
                ->get()
                ->getRowArray();

            if ($dbBook) {
                $db_title = trim($dbBook['book_title']);

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
        $discounts = $this->request->getPost('discount');

        // Get currently stored data from session
        $matched = session()->get('matched_books') ?? [];
        $mismatched = session()->get('mismatched_books') ?? [];

        if (!empty($selected)) {
            foreach ($selected as $bookId) {
                // Find that mismatched book
                foreach ($mismatched as $key => $book) {
                    if ($book['book_id'] == $bookId) {
                        // Move this to matched
                        $matched[] = [
                            'book_id'  => $book['book_id'],
                            'title'    => $titles[$bookId] ?? $book['db_title'],
                            'quantity' => $quantities[$bookId] ?? $book['quantity'],
                        ];

                        // Remove from mismatched
                        unset($mismatched[$key]);
                        break;
                    }
                }
            }
        }

        // Save updated data in session

        session()->set('mismatched_books', $mismatched);

        $totalTitles = count($matched);
     
        // Reload the same view
       
        return view('printorders/bookfair/ComboSummaryView', [
            'matched' => $matched,
            'mismatched' => $mismatched,
            'totalTitles'=> $totalTitles,
            'title' => '',
            'subTitle' => '',
        ]);
    }

    public function combopackupload()
    {
       
        $acceptBooks = session()->get('accept_books');
        $comboName = $this->request->getPost('combo_pack_name');

        
        // echo "<pre>";
        //         print_r(session()->get());
        // print_r($acceptBooks);
        //       echo '<pre>';

        $result = $this->ComboBookfairModel->combopackupload($acceptBooks, $comboName);

        // // Set success flash message
        session()->setFlashdata('success', 
            ' successfully!! '
        );

        // CLEAR SESSION AFTER FINAL SAVE
        session()->remove([
            'matched_books',
            'mismatched_books',
            'upload_type',
            'accept_books'
        ]);


        // Redirect back to upload form
        return redirect()->to(base_url('combobookfair/createcombo'));

    }
}

