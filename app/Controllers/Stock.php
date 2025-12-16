<?php

namespace App\Controllers;
use App\Models\StockModel; 
use App\Models\BookFairModel;
use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;  


class Stock extends BaseController
{
    protected $StockModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->StockModel = new StockModel();
        helper('url');
        $this->session = session();
    }

    public function index()
    {
        $data = [
            'title' => 'Dashboard',
            'subTitle' => 'Investment',
        ];
        return view('stock/stockDashboard', $data);
    }
    public function stockdashboard()
    {
        $data = [
            'details' => $this->StockModel->getDashboardDetails(),
            'title'     => 'Stock Dashboard',
            'subTitle'  => 'Overview',
        ];

        return view('stock/stockDashboard', $data); 
    }
    public function getstockdetails()
    {
        $mismatch_count = $this->StockModel->mismatchstockcount();
        $this->session->set('mismatch_count', $mismatch_count);
        
       $data= [
        'stock_details' => $this->StockModel->getStockDetails(),
        'stock_data' => $this->StockModel->getBookFairDetails(),
        'mismatch_stock' => $this->StockModel->getMismatchStockDetails(),
        'title'     => 'Stock Details',
        'subTitle'  => 'Overview',
       ];
        //    echo"<pre>";
        //   print_r($data);
       return view('stock/stockDetails', $data);
        
    }
    public function pendingstock()
    {
        $data['stock_details'] = $this->StockModel->getStockDetails()['stock'];
        $data['title'] = "";

        $data['filtered'] = array_filter($data['stock_details'], function($r){
            return $r['validated_flag'] == 0 && $r['paper_back_readiness_flag'] == 1;
        });
        

        return view('stock/stockFilterview', $data);
    }

    public function validatedstock()
    {
        $data['stock_details'] = $this->StockModel->getStockDetails()['stock'];
        $data['title'] = "Validated Stock";

        $data['filtered'] = array_filter($data['stock_details'], function($r){
            return $r['validated_flag'] == 1 && $r['paper_back_readiness_flag'] == 1;
        });

        return view('stock/stockFilterview', $data);
    }


    public function totalstock()
    {
        $data['stock_details'] = $this->StockModel->getStockDetails()['stock'];
        $data['title'] = "Total Books";
        $filter = array_filter($data['stock_details'], fn($r) => $r['paper_back_readiness_flag'] == 1);
        $data['filtered'] = $filter;

        return view('stock/stockFilterview', $data);
    }

    public function disabledstock()
    {
        $data['stock_details'] = $this->StockModel->getStockDetails()['stock'];
        $data['title'] = "Disabled Books";
        $filter = array_filter($data['stock_details'], fn($r) => $r['paper_back_readiness_flag'] == 0);
        $data['filtered'] = $filter;

        return view('stock/stockFilterview', $data);
    }

    public function outofstockdetails()
    {
        $data= [
            'stockout_details' => $this->StockModel->getOutofstockdetails(),
            'title'     => '',
            'subTitle'  => 'Overview',
        ];

        return view('stock/outOfstockDetails', $data);

    }
    public function loststockdetails()
    {
          

        $data= [
            'loststock_details' => $this->StockModel->getLostStockDetails(),
            'title'     => 'Lost/Excess Stock Details',
            'subTitle'  => 'Overview',
        ];

        return view('stock/lostStockDetails', $data);
    }
    public function outsidestockdetails()
    {
          

        $data= [

            'outsidestock_details' => $this->StockModel->getOutsideStockDetails(),
            'stock_data' => $this->StockModel->getBookFairDetails(),
            'title'     => 'Outside Stock Details',
            'subTitle'  => 'Overview',
        ];
        //   echo"<pre>";

        // print_r($data);
    
        return view('stock/outSidestockDetails', $data);
    }
    public function addstock()
    {
       $data = [    
           'paperback_books' => $this->StockModel->getPaperbackBooks(),
           'title'     => 'Add Stock',
           'subTitle'  => 'Overview',
       ];

        // ob_start();
        // echo "<pre>";
        // print_r($data['paperback_books']);
        // echo "</pre>";
        // return ob_get_clean();

        return view('stock/addStock', $data);
    }
    public function bookslist()
    {
        $selectedBookList = $this->request->getPost('selected_book_list') ?: $this->request->getGet('selected_book_list');

        log_message('debug', 'Selected Books list: ' . $selectedBookList);

          

        $data = [
            'selected_book_id' => $selectedBookList,
            'selected_books_data' => $this->StockModel->getPaperbackSelectedBooksList($selectedBookList),
            'title' => 'Selected Books',
            'subTitle' => 'Overview',
        ];

        return view('stock/stockbooksList', $data);
    }
   public function submitdetails()
    {
        if (!session()->get('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }
          
 
        $book_id = $this->request->getPost('book_id');
        $qty = $this->request->getPost('quantity');
        $updated_user_id = session()->get('user_id');
        $last_update_date = date('Y-m-d H:i:s');
        // $validate_user_id = session()->get('user_id');
        // $last_validated_date = date('Y-m-d H:i:s');

        $result = $this->StockModel->submitDetails($book_id, $qty, $updated_user_id, $last_update_date);

        echo $result;
    }
    
    public function stockentrydetails()
    {
        $book_id = $this->request->getGet('book_id'); 

        if (empty($book_id)) {
            return redirect()->to('stock/stockdashboard')->with('error', 'Invalid request!');
        }

          

        $data = [
            'book_id' => $book_id,
            'book_details' => $this->StockModel->getBookDetails($book_id),
            'author_transaction' => $this->StockModel->getAuthorTransaction($book_id),
            'stock_ledger' => $this->StockModel->getStockLedger($book_id),
            'stock_user_details' => $this->StockModel->getStockUserDetails($book_id), 
            'title' => 'Stock Entry Details',
            'subTitle' => 'Overview',
        ];

        return view('stock/stockEntryDetailsView', $data);
    }
    public function validateStock()
    {
        if (!session()->get('user_id')) {
            return redirect()->to(base_url('adminv4'))->with('error', 'Please login first.');
        }

        $book_id = $this->request->getPost('book_id');
        $user_id = session()->get('user_id');
        $validate_date = date('Y-m-d H:i:s');

        if (empty($book_id)) {
            return redirect()->back()->with('error', 'Book ID is missing.');
        }

          
        $updated = $this->StockModel->updateValidationInfo($book_id, $user_id, $validate_date);

        if ($updated) {
                $data = ['title' => 'Stock validated successfully'];
                return view('partials/closeWindow', $data);

        } else {
            return redirect()->back()->with('error', 'Validation failed.');
        }

        if (empty($book_id)) {
            return redirect()->to('stock/stockdashboard')->with('error', 'Invalid request!');
        }

          

        $data = [
            'book_id' => $book_id,
            'book_details' => $this->StockModel->getBookDetails($book_id),
            'author_transaction' => $this->StockModel->getAuthorTransaction($book_id),
            'stock_ledger' => $this->StockModel->getStockLedger($book_id),
            'title' => 'Stock Entry Details',
            'subTitle' => 'Overview',
        ];

        return view('stock/stockEntryDetailsView', $data);
    }
    function otherdistribution(){

		$data =[
            'title' => 'Other Distribution',
            'subTitle' => 'Overview',
            'other_distribution' => $this->StockModel->getOtherDistributionDetails(),
        ];

        return view('stock/otherDistribution', $data);
    }


    public function saveotherdistribution()
    {
        $orderData = [
            'order_id'    => time(),
            'order_date'  => date('Y-m-d H:i:s'),
            'book_id'     => $this->request->getPost('book_id'),
            'type'        => $this->request->getPost('type'),
            'purpose'     => $this->request->getPost('purpose'),
            'quantity'    => $this->request->getPost('quantity')
        ];

        $this->StockModel->insertOtherDistribution($orderData);

        return redirect()->back()->with('success', 'Data saved successfully.');
    }


    public function UpdatevalidateStock($bookId)
    {
       
        // Check login
        if (!session()->get('user_id')) {
            return redirect()
                ->to(base_url('adminv4'))
                ->with('error', 'Please login first.');
        }

        $book_id = $bookId;
        $user_id = session()->get('user_id');
        $validate_date = date('Y-m-d H:i:s');

        $updated = $this->StockModel->updateValidationInfo($book_id, $user_id, $validate_date);

        if ($updated) {
            session()->setFlashdata('message', 'Validated successfully!');
        } else {
            session()->setFlashdata('error', 'Validation failed! Please try again.');
        }

        return redirect()->to(base_url('stock/getstockdetails'));

    }

    public function getmismatchstock(){

        $mismatch_count = $this->stockModel->mismatchstockcount();
        $this->session->set('mismatch_count', $mismatch_count);

          
        $data = [
            'mismatch_details' => $this->StockModel->getMismatchStockDetails(),
            'title' => '',
            'subTitle' => 'Overview',
        ];

        return view('stock/mismatchStockViewDetails', $data);
    }
        
    public function mismatchupdate()
    {
        // Get book_id from POST
        $bookId = $this->request->getPost('book_id');
        $actionType = $this->request->getPost('action_type') ?: 'add'; // default to 'add' if not provided

        $stocks      = $this->StockModel->getBookfairNames($bookId);
        $mismatchLog = $this->StockModel->getMismatchLog($bookId);

         $data = [
            'stocks'      => $stocks,
            'mismatchLog' => $mismatchLog,
            'actionType'  => $actionType,
            'title' => '',
            'subTitle' => '',
        ];
        // echo "<pre>";
        // print_r( $data);


        return view('stock/mismatchAdd', $data);
    }

    public function mismatchSubmit()
    {
        $post = $this->request->getPost();
        $comments = $this->request->getPost('comments');

        // book_id is array → get first element
        $book_id = is_array($post['book_id']) ? $post['book_id'][0] : $post['book_id'];

        // Only mismatch fields send to model
        $updates = $post['mismatch'];

        $this->StockModel->mismatchSubmit($book_id, $updates,$comments);

        $mismatch_count = $this->stockModel->mismatchstockcount();
        $this->session->set('mismatch_count', $mismatch_count);



        return redirect()->to('stock/getstockdetails');
    }

    public function mismatchvalidate()
    {
        $post = $this->request->getPost();
        $comments = $this->request->getPost('comment');

    
        // book_id is array → get first element
        $book_id = is_array($post['book_id']) ? $post['book_id'][0] : $post['book_id'];

        // Only mismatch fields send to model
        $updates = $post['mismatch'];

        $this->StockModel->mismatchvalidate($book_id, $updates, $comments);
        
        $mismatch_count = $this->stockModel->mismatchstockcount();
        $this->session->set('mismatch_count', $mismatch_count);

        return redirect()->to('stock/getmismatchstock');
    }
    public function paperbackledgerbooks()
    {
        $data = [
            'title' => '',
            'subTitle' => '',
            'paperback_books' => $this->StockModel->paperbackLedgerBooks(),
        ];

        return view('stock/paperbackBooksList', $data);
    }
    function paperbackledgerbooksdetails(){

        $book_id = $this->request->getUri()->getSegment(3);
        $data = [
            'title' => '',
            'subTitle' => '',
            'details' => $this->StockModel->paperbackLedgerDetails($book_id),
            'book_id'  => $book_id
        ];
        
        return view('stock/paperbackBooksDetails', $data);
	}
    public function paperbackledgerstockdetails()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->setActiveSheetIndex(0);

        $db = \Config\Database::connect();

        $sql = "SELECT book_tbl.book_id, book_tbl.book_title, author_tbl.author_name, book_tbl.paper_back_isbn, 
                        book_tbl.paper_back_inr, language_tbl.language_name, genre_details_tbl.genre_name, 
                        SUM(pustaka_paperback_stock_ledger.stock_in) as print_quantity, 
                        SUM(pustaka_paperback_stock_ledger.stock_out) as stock_out, 
                        SUM(pustaka_paperback_stock_ledger.stock_in) - SUM(pustaka_paperback_stock_ledger.stock_out) as current_stock,
                        paperback_stock.stock_in_hand as stock_in_hand
                FROM book_tbl 
                JOIN author_tbl ON author_tbl.author_id = book_tbl.author_name AND book_tbl.paper_back_readiness_flag = 1
                JOIN language_tbl ON language_tbl.language_id = book_tbl.language
                JOIN genre_details_tbl on genre_details_tbl.genre_id = book_tbl.genre_id 
                JOIN pustaka_paperback_stock_ledger on pustaka_paperback_stock_ledger.book_id = book_tbl.book_id
                JOIN paperback_stock on paperback_stock.book_id = book_tbl.book_id
                GROUP BY book_tbl.book_id";

        $query = $db->query($sql);
        $records = $query->getResultArray();
        $i = 1;

        if (!empty($records)) {
            $headers = [
                'A' => 'Book ID',
                'B' => 'Book Title',
                'C' => 'Author Name',
                'D' => 'ISBN',
                'E' => 'Language',
                'F' => 'Genre',
                'G' => 'MRP',
                'H' => 'Print Quantity',
                'I' => 'Stock Out',
                'J' => 'Current Stock',
                'K' => 'Stock In Hand'
            ];

            foreach ($headers as $column => $header) {
                $sheet->setCellValue($column . '1', $header);
            }

            $i++;
        }

        foreach ($records as $record) {
            $sheet->setCellValue('A' . $i, $record['book_id']);
            $sheet->setCellValue('B' . $i, $record['book_title']);
            $sheet->setCellValue('C' . $i, $record['author_name']);
            $sheet->setCellValue('D' . $i, $record['paper_back_isbn']);
            $sheet->setCellValue('E' . $i, $record['language_name']);
            $sheet->setCellValue('F' . $i, $record['genre_name']);
            $sheet->setCellValue('G' . $i, $record['paper_back_inr']);
            $sheet->setCellValue('H' . $i, $record['print_quantity']);
            $sheet->setCellValue('I' . $i, $record['stock_out']);
            $sheet->setCellValue('J' . $i, $record['current_stock']);
            $sheet->setCellValue('K' . $i, $record['stock_in_hand']);
            $i++;
        }

        $filename = 'stock_list_' . date('Y-m-d') . '.xls';

        // Output headers for browser download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function otherdistributionbooksstatus()
    {
        $data = [
            'title' => '',
            'subTitle' => '',
            'print'=>$this->StockModel->otherdistributionbooksstatus(),    
        ];

        return view('stock/otherdistributionbooksstatus', $data);
    }
     //free book Initiate Print
    public function markstart()
    {
        $id = $this->request->getPost('id');
        $type = $this->request->getPost('type');
        $result = $this->StockModel->markStart($id, $type);
        return $this->response->setJSON(['status' => $result]);
    }
    public function freemarkcompleted()
    {
        $id = $this->request->getPost('id');
        $type = $this->request->getPost('type');

        $result = $this->StockModel->freeMarkCompleted($id, $type);

        return $this->response->setJSON(['status' => $result]);
    }
    function freebooksdashboard(){

        $data = [
            'title' => '',
            'subTitle' => '',
            'paperback_books' => $this->StockModel->getlistPaperbackBooks(),
        ];

		return view('stock/booksListView', $data);
	}
    function totalfreebookscompleted()
    {
        $data = [
            'title' => '',
            'subTitle' => '',
            'print' => $this->StockModel->otherdistributionbooksstatus(),
        ];
        return view('stock/totalCompletedBooks', $data);
	}
    public function getpaperbackstock()
    {
        $data['get_paperback_stock'] = $this->StockModel->getPaperbackStockDetails();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('stock/paperbackStockView', $data);
    }
    public function lostexcessbooksstatus()
    {
        $data['lost_excess'] = $this->StockModel->getLostExcessBookStatus();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('stock/lostExcessBooksUpdate', $data);
    }
    public function printexcesslostone()
    {
        $book_id = $this->request->getUri()->getSegment(3);
        $data['lost_excess_update'] = $this->StockModel->printExcessLostOneItem($book_id);
        $data['title'] = '';
        $data['subTitle'] = '';

        log_message('debug', 'Exit from print_excess_lost_one_item()');

        return view('stock/lostExcessBooksStatusView', $data);
    }

    public function printexcesslostall()
    {
        $book_id = $this->request->getUri()->getSegment(3);
        $data['lost_excess_update_all'] = $this->StockModel->printExcessLostAllItem($book_id);
        $data['title'] = '';
        $data['subTitle'] = '';

        log_message('debug', 'Exit from print_excess_lost_one_item()');

        return view('stock/lostExcessBooksStatusView', $data);
    }
    public function savequantity()
    {
        $data = [
            'book_id'       => $this->request->getPost('book_id'),
            'quantity'      => $this->request->getPost('quantity'),
            'bookfair'      => $this->request->getPost('bookfair'),
            'bookfair2'     => $this->request->getPost('bookfair2'),
            'bookfair3'     => $this->request->getPost('bookfair3'),
            'bookfair4'     => $this->request->getPost('bookfair4'),
            'bookfair5'     => $this->request->getPost('bookfair5'),
            'lost_qty'      => $this->request->getPost('lost_qty'),
            'excess_qty'    =>$this->request->getPost('excess_qty'),
            'stock_in_hand' => $this->request->getPost('stock_in_hand'),
        ];

        $result = $this->StockModel->savequantity($data);

        // return JSON exactly for AJAX
        return $this->response->setJSON(['status' => $result]);
    }

    public function uploadView()
    {
         $data['title'] = '';
        $data['subTitle'] = '';
        return view('stock/bulkStockUpload',$data);
    }

    public function uploadProcess()
    {
        // echo "<pre>";
        // print_r($_POST);
        helper(['form', 'url']);

        $file = $this->request->getFile('excel_file');
        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'Please upload a valid Excel file.');
        }

        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads', $newName);
        $filePath = WRITEPATH . 'uploads/' . $newName;

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error reading Excel: ' . $e->getMessage());
        }

        $matched = [];
        $mismatched = [];

        foreach ($rows as $i => $row) {
            if ($i == 1) continue; // Skip header

            $book_id = trim($row['A'] ?? '');
            $excel_title = trim($row['B'] ?? '');
            $quantity = (int) ($row['C'] ?? 0);
            $discount = (float) ($row['D'] ?? 0);

            if (empty($book_id)) continue;

            // Get details from database
            $dbBook = $this->db->table('book_tbl')
                ->where('book_id', $book_id)
                ->get()
                ->getRowArray();

            if ($dbBook) {
                $db_title = trim($dbBook['book_title']);

                if (strcasecmp($excel_title, $db_title) === 0) {
                    //  Matched
                    $matched[] = [
                        'book_id' => $book_id,
                        'title' => $db_title,
                        'quantity' => $quantity,
                        'discount' => $discount,
                        'price' => $dbBook['paper_back_inr'] ?? 0,
                    ];
                } else {
                    //  Mismatch
                    $mismatched[] = [
                        'book_id' => $book_id,
                        'excel_title' => $excel_title,
                        'db_title' => $db_title,
                        'quantity' => $quantity,
                        'discount' => $discount
                    ];
                }
            } else {
                $mismatched[] = [
                    'book_id' => $book_id,
                    'excel_title' => $excel_title,
                    'db_title' => 'Not Found in DB',
                    'quantity' => $quantity,
                    'discount' => $discount
                ];
            }
        }

      
       session()->set('matched_books', $matched);
       session()->set('mismatched_books', $mismatched);

        $totalTitles = count($matched);

        return view('stock/summary_view', [
            'matched' => $matched,
            'mismatched' => $mismatched,
            'totalTitles'=> $totalTitles,
            'title' => '',
            'subTitle' => '',
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

                $query = $this->db->table('book_tbl')
                        ->select('paper_back_inr')
                        ->where('book_id', $bookId)
                        ->get()
                        ->getRowArray();

                $dbPrice = $query['paper_back_inr'] ?? 0;

                // Find that mismatched book
                foreach ($mismatched as $key => $book) {
                    if ($book['book_id'] == $bookId) {
                        // Move this to matched
                        $matched[] = [
                            'book_id'  => $book['book_id'],
                            'title'    => $titles[$bookId] ?? $book['db_title'],
                            'quantity' => $quantities[$bookId] ?? $book['quantity'],
                            'discount' => $discounts[$bookId] ?? $book['discount'],
                            'price'    => $dbPrice,
                        ];

                        // Remove from mismatched
                        unset($mismatched[$key]);
                        break;
                    }
                }
            }
        }

        // Save updated data in session
        session()->set('accept_books', $matched);
        session()->set('mismatched_books', $mismatched);

        $totalTitles = count($matched);
        // Reload the same view
        return view('stock/summary_view', [
            'matched' => $matched,
            'mismatched' => $mismatched,
            'totalTitles'=> $totalTitles,
            'title' => '',
            'subTitle' => '',
        ]);
    }

    public function BulkstockUpload()
    {
       
        $acceptBooks = session()->get('accept_books');
        // echo "<pre>";
        // print_r($acceptBooks);
         
        $result = $this->StockModel->saveBulkStock($acceptBooks);

        // // Set success flash message
        session()->setFlashdata('success', 
            'bulk Stock saved successfully!! '
        );

        // Redirect back to upload form
        return redirect()->to(base_url('stock/bulkupload'));

    }

    public function bulkbookshopReturn()
    {
         $acceptBooks = session()->get('accept_books');
        // echo "<pre>";
        // print_r($acceptBooks);
         
        $result = $this->StockModel->returnBookshopBulkOrder($acceptBooks);

        // // Set success flash message
        session()->setFlashdata('success', 
            'bulk return saved successfully!! '
        );

        // Redirect back to upload form
        return redirect()->to(base_url('stock/bulkupload'));
    }
     public function bookfairDashboard()
    {
        $model = new BookFairModel();

        $data = [
            'title'       => '',
            'high'        => $model->getPriorityBooks('high'),
            'highmedium'  => $model->getPriorityBooks('highmedium'),
            'all'         => $model->getPriorityBooks('all'),
        ];

        return view('bookfair/bookfairdashboard', $data);
    }
    public function exportExcel()
{
    $priority = $this->request->getGet('priority') ?? 'all';

    $model = new \App\Models\BookFairModel();
    $records = $model->getBooksWithStats($priority);

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $sheet->setCellValue('A1', 'Author');
    $sheet->setCellValue('B1', 'Book');
    $sheet->setCellValue('C1', 'No. of Bookfairs');
    $sheet->setCellValue('D1', 'Allocated Qty');
    $sheet->setCellValue('E1', 'Sales Qty');
    $sheet->setCellValue('F1', 'Recommended Qty');
    $sheet->setCellValue('G1', 'Stock In Hand');

    $row = 2;
    foreach ($records as $r) {

        $recommended = ($r['no_of_bookfairs'] > 0)
            ? ceil($r['sales_qty'] / $r['no_of_bookfairs'])
            : 0;

        $sheet->setCellValue("A{$row}", $r['author_name']);
        $sheet->setCellValue("B{$row}", $r['book_title']);
        $sheet->setCellValue("C{$row}", $r['no_of_bookfairs']);
        $sheet->setCellValue("D{$row}", $r['allocated_qty']);
        $sheet->setCellValue("E{$row}", $r['sales_qty']);
        $sheet->setCellValue("F{$row}", $recommended);
        $sheet->setCellValue("G{$row}", $r['stock_in_hand']);

        $row++;
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $fileName = "bookfair_export_" . $priority . ".xlsx";

    return $this->response
        ->setHeader('Content-Type', 'application/vnd.ms-excel')
        ->setHeader("Content-Disposition", "attachment; filename=\"{$fileName}\"")
        ->setHeader('Cache-Control', 'max-age=0')
        ->setBody($writer->save('php://output'));
}
public function BookfairView($book_id)
    {
        $model = new BookFairModel();

        $data = [
            'title'              => '',
            'book'               => $model->getBookDetails($book_id),
            'allocations'        => $model->getAllocationDetails($book_id),
            'sales'              => $model->getSalesDetails($book_id),
        ];

        return view('BookFair/BookfairView', $data);
    }
    
}