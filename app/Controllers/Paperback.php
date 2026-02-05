<?php

namespace App\Controllers;

use App\Models\PustakapaperbackModel;
use App\Models\PodModel;
use App\Models\PaperbackModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



class Paperback extends BaseController
{
    protected $PustakapaperbackModel;
    protected $PodModel;
    protected $PaperbackModel;

    public function __construct()
    {
        $this->PustakapaperbackModel = new PustakapaperbackModel();
        $this->podModel = new PodModel();
        $this->PaperbackModel = new PaperbackModel();
    }

    public function OrdersDashboard(){

        $fy = $this->request->getGet('fy') ?? 'all';
        $data['title'] = '';
        $data['subTitle'] = '';
        $data['dashboard'] = $this->podModel->getPODDashboardData();
        $data['pending_books']=$this->podModel->getPendingBooksData();
        $data['stock'] = $this->PustakapaperbackModel->getPaperbackStockDetails();	
		$data['pending'] = $this->PustakapaperbackModel->totalPendingBooks();	
		$data['orders'] = $this->PustakapaperbackModel->totalPendingOrders();
        $data['orders_dashboard'] = $this->PustakapaperbackModel->ordersDashboardData($fy);
        $data['fy'] = $fy;
        
        // echo "<pre>";
        // print_r($data['orders_dashboard']);
        // echo "</pre>";

        return view('printorders/orderDashboard',$data);
    }

    // online orders
    public function onlineorderbooksstatus()
    {
        $chartFilter = $this->request->getGet('chart_filter') ?? 'all';
        $data['online_orderbooks'] = $this->PustakapaperbackModel->onlineProgressBooks();
        $data['online_summary'] = $this->PustakapaperbackModel->onlineSummary($chartFilter);
        $data['chart_filter'] = $chartFilter;

        $data['title'] = '';
        $data['subTitle'] = '';
        return view('printorders/online/orderbooksStatusview', $data);
    }

    public function onlinemarkshipped()
    {
        $book_id = $this->request->getPost('book_id');
        $online_order_id =$this->request->getPost('order_id');
        $tracking_id =$this->request->getPost('tracking_id');
        $tracking_url =$this->request->getPost('tracking_url');
        $result = $this->PustakapaperbackModel->onlineMarkShipped($book_id,$online_order_id,$tracking_id,$tracking_url);
        return $this->response->setJSON(['status' => $result]);
    }

    public function onlinemarkcancel()
    {
        $online_order_id = $this->request->getPost('online_order_id');
        $book_id = $this->request->getPost('book_id');
        $result = $this->PustakapaperbackModel->onlineMarkCancel($online_order_id,$book_id);
        return $this->response->setJSON(['status' => $result]);
        
    }

    public function onlinetrackingdetails()
    {
        $result = $this->PustakapaperbackModel->onlineTrackingDetails();
        return $this->response->setJSON(['status' => $result]);
    }

    public function onlineordership() 
    {
        $online_order_id = $this->request->getUri()->getSegment(3);
        $book_id = $this->request->getUri()->getSegment(4);

        $data['online_order_id'] = $online_order_id;
        $data['orderbooks'] = $this->PustakapaperbackModel->onlineOrdership($online_order_id, $book_id);
        $data['details'] = $this->PustakapaperbackModel->onlineOrderDetails($online_order_id);
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/online/onlineOrderShip', $data);
    }
    public function onlineorderdetails()
    {
        $order_id = $this->request->getUri()->getSegment(3);
        if (empty($order_id)) {
            $order_id = $this->request->getPost('order_id');
        }
        $data['orderbooks'] = $this->PustakapaperbackModel->onlineOrderDetails($order_id);
        $data['order_id'] = $order_id;
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/online/orderDetailsView', $data);
    }


    public function totalonlineordercompleted()
    {
        $data['online_orderbooks'] = $this->PustakapaperbackModel->onlineProgressBooks();
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('printorders/online/onlineOrderCompleted', $data);
    }

    public function onlinebulkordersship()
    {
        $bulk_order_id = $this->request->getUri()->getSegment(3);
        $data['order_id']   = $bulk_order_id;
        $data['bulk_order'] = $this->PustakapaperbackModel->getOnlinebulkOrdersdetails($bulk_order_id);
        $data['title']      = '';
        $data['subTitle']   = '';
        return view('printorders/online/bulkOrdersShipView', $data);
    }
    public function bulkonlineordershipmentcompleted()
    {
        $order_id     = $this->request->getPost('order_id');
        $book_ids     = json_decode($this->request->getPost('book_ids'), true);
        $tracking_id  = $this->request->getPost('tracking_id');
        $tracking_url = $this->request->getPost('tracking_url');

        $result = $this->PustakapaperbackModel->onlineBulkOrdershipment($order_id, $book_ids, $tracking_id, $tracking_url);
        return $this->response->setJSON(['status' => $result]);
    }


    //paperback ledger details
    public function paperbackledgerbooksdetails()
    {
        $uri = service('uri');
        $data['book_id'] = $uri->getSegment(3);

        // $data['book_id'] = $book_id;
        $data['details'] = $this->PustakapaperbackModel->paperbackLedgerDetails();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/paperbackledger/paperbackBooksDetails', $data);
    }

    //offline//
    public function offlineorderbooksdashboard()
    {
        $data['paperback_books'] = $this->PustakapaperbackModel->offlinePaperbackBooks();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/offline/offlineOrderBooksDashboard', $data);
    }

    // Order List
    public function offlineorderbookslist()
    {
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }

        $selected_book_list = $this->request->getPost('selected_book_list');

        $data['offline_selected_book_id']   = $selected_book_list;
        $data['offline_selected_books_data'] = $this->PustakapaperbackModel->offlineSelectedBooksList($selected_book_list);
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/offline/offlineOrderbooksList', $data);
    }

    // Stock / Quantity View
    public function offlineorderstock()
    {
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }

        $num_of_books      = $this->request->getPost('num_of_books');
        $selected_book_list = $this->request->getPost('selected_book_list');

        $ship_date       = $this->request->getPost('ship_date');
        $courier_charges = $this->request->getPost('courier_charges');
        $payment_type    = $this->request->getPost('payment_type');
        $payment_status  = $this->request->getPost('payment_status');
        $customer_name   = $this->request->getPost('customer_name');
        $address         = $this->request->getPost('address');
        $mobile_no       = $this->request->getPost('mobile_no');
        $email           = $this->request->getPost('email');
        $remarks         = $this->request->getPost('remarks');
        $city            = $this->request->getPost('city');

        $book_ids  = [];
        $book_qtys = [];
        $book_dis  = [];
        $tot_amt   = [];

        for ($i = 1; $i <= $num_of_books; $i++) {
            $book_ids[]  = $this->request->getPost('book_id' . $i);
            $book_qtys[] = $this->request->getPost('bk_qty' . $i);
            $book_dis[]  = $this->request->getPost('bk_dis' . $i);
            $tot_amt[]   = $this->request->getPost('tot_amt' . $i);
        }

        $data['offline_selected_book_id'] = $selected_book_list;
        $data['offline_paperback_stock']  = $this->PustakapaperbackModel->offlineSelectedBooksList($selected_book_list);
        $data['book_qtys']   = $book_qtys;
        $data['book_dis']    = $book_dis;
        $data['tot_amt']     = $tot_amt;
        $data['ship_date']   = $ship_date;
        $data['courier_charges'] = $courier_charges;
        $data['payment_type']    = $payment_type;
        $data['payment_status']  = $payment_status;
        $data['customer_name']   = $customer_name;
        $data['address']         = $address;
        $data['mobile_no']       = $mobile_no;
        $data['city']            = $city;
        $data['email']   = $email;
        $data['remarks'] = $remarks;

        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/offline/offlineorderQuantityView', $data);
    }

    // Submit Order
    public function offlineorderbookssubmit()
    {
        $result = $this->PustakapaperbackModel->offlineOrderbooksDetailsSubmit();

        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/offline/offlineOrderBooksSubmitView', $data);
    }

    // Orders In Progress
    public function offlineorderbooksstatus()
    {
        $chartFilter = $this->request->getGet('chart_filter') ?? 'all';
        $data['offline_orderbooks'] = $this->PustakapaperbackModel->offlineProgressBooks();
        $data['offline_summary'] = $this->PustakapaperbackModel->offlineSummary($chartFilter);
        $data['chart_filter'] = $chartFilter;
        $data['title'] = '';
        $data['subTitle'] = '';

        // echo "<pre>";
        // print_r($data['offline_summary']);

        return view('printorders/offline/offlineOrderbooksStatusView', $data);
    }

    // Order Details
    public function offlineorderdetails($order_id)
    {
        $data['order_id']   = $order_id;
        $data['orderbooks'] = $this->PustakapaperbackModel->offlineOrderDetails($order_id);
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/offline/offlineOrderDetailsView', $data);
    }

    // Mark Shipped
    public function offlinemarkshipped()
    {
        $result=$this->PustakapaperbackModel->offlineMarkShipped();
        echo $result;
        exit;
    }

    // Mark Cancel
    public function offlinemarkcancel()
    {
        $result=$this->PustakapaperbackModel->offlineMarkCancel();
        echo $result;
        exit;
    }

    // Mark Paid
    public function offlinemarkpay()
    {
        $result = $this->PustakapaperbackModel->offlineMarkPay();
        echo $result;
        exit;
    }

    // Mark Return
    public function offlinemarkreturn()
    {
        $result = $this->PustakapaperbackModel->offlineMarkReturn();
        echo $result; 
        exit;  
    }

    // Tracking Details
    public function offlinetrackingdetails()
    {
        $result = $this->PustakapaperbackModel->offlineTrackingDetails();
        echo $result;
        exit;
    }

    // Ship Order
    public function offlineordership($offline_order_id = null, $book_id = null)
    {
        $data['orderbooks'] = $this->PustakapaperbackModel->offlineOrderShip($offline_order_id, $book_id);
        $data['details']    = $this->PustakapaperbackModel->offlineOrderDetails($offline_order_id);
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/offline/offlineOrderShip', $data);
    }


    // Completed Orders
    public function totalofflineordercompleted()
    {
        $data['offline_orderbooks'] = $this->PustakapaperbackModel->offlineProgressBooks();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/offline/offlineTotalCompletedBooks', $data);
    }
    public function offlinebulkordersship($bulk_order_id)
    {
        $bulk_order_id = trim(preg_replace('/\s+/', '', $bulk_order_id));
    
        $data['order_id']   = $bulk_order_id;
        $data['bulk_order'] = $this->PustakapaperbackModel->getBulkOrdersDetails($bulk_order_id); 
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/offline/offlineBulkOrdersShipView', $data);
    }


	public function bulkordershipmentcompleted()
    {
        $order_id     = $this->request->getPost('order_id');
        $book_ids     = json_decode($this->request->getPost('book_ids'), true); 
        $tracking_id  = $this->request->getPost('tracking_id');
        $tracking_url = $this->request->getPost('tracking_url');

        $result = $this->PustakapaperbackModel->bulkOrderShipment($order_id, $book_ids, $tracking_id, $tracking_url);

        return $this->response->setJSON(['status' => $result]);
    }


   function initiateprintdashboard($book_id)
   {
        $data['book_id'] = $book_id;
		$data['initiate_print'] = $this->PustakapaperbackModel->getBooksStock($book_id);
        $data['title'] = '';
        $data['subTitle'] = '';
	
        return view('printorders/initiateprint/initiatePrintDashboard',$data);
        
	}
    function paperbackprintstatus()
	{
		$data['print'] = $this->PustakapaperbackModel->getInitiatePrintStatus();
        $data['title'] = '';
        $data['subTitle'] = '';

        // echo "<pre>";
		// print_r($data['amazon_summary']);

        return view('printorders/initiateprint/paperbackPrintStatusView',$data);
        
	}
    public function updatequantity() 
    {
        $book_id = $this->request->getPost('book_id');
        $quantity = $this->request->getPost('quantity');

        $result = $this->PustakapaperbackModel->updateQuantity($book_id, $quantity);
        echo $result;
    }
    public function initiateprintbooksdashboard()
    {
        $data['paperback_books'] = $this->PustakapaperbackModel->getPaperbackBooks();
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('printorders/initiateprint/initiatePrintBooksDashboard', $data);
    }

    public function initiateprintbookslist()
    {

        $selected_book_list = $this->request->getPost('selected_book_list');

        log_message('debug', 'Selected Books list.... ' . $selected_book_list);

        $data['selected_book_id'] = $selected_book_list;
        $data['selected_books_data'] = $this->PustakapaperbackModel->getPaperbackSelectedBooksList($selected_book_list);
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/initiateprint/initiatePrintBooksList', $data);
    }

    public function uploadquantitylist()
    {
        $result = $this->PustakapaperbackModel->uploadQuantityList();
        return $this->response->setJSON(['status' => $result]);
    }
     public function editinitiateprint()
    {
        $data['initiate_print'] = $this->PustakapaperbackModel->editInitiatePrint();
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('printorders/initiateprint/editInitiatePrintView', $data);
    }

    public function editquantity()
    {
        $result = $this->PustakapaperbackModel->editQuantity();

        return $this->response->setJSON([
            'status' => $result
        ]);
    }

    public function deleteinitiateprint()
    {
        $id = $this->request->getPost('id');
        $result = $this->PustakapaperbackModel->deleteInitiatePrint($id);
        return $this->response->setJSON([
            'status' => $result
        ]);
    }

    public function totalinitiateprintcompleted()
    {
        $data['print'] = $this->PustakapaperbackModel->getInitiatePrintStatus();
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('printorders/initiateprint/totalCompletedBooks', $data);
    }
    function markstart()
	{
		
        $id = $this->request->getPost('id');
        $type = $this->request->getPost('type');
        $result = $this->PustakapaperbackModel->markStart($id, $type);
        return $this->response->setJSON(['status' => $result]);
	}

	function markcovercomplete()
	{
        $id = $this->request->getPost('id');
        $type = $this->request->getPost('type');
        $result = $this->PustakapaperbackModel->markCoverComplete($id, $type);
        return $this->response->setJSON(['status' => $result]);
	}

	function markcontentcomplete()
	{
        $id = $this->request->getPost('id');
        $type = $this->request->getPost('type');
        $result = $this->PustakapaperbackModel->markContentComplete($id, $type);
        return $this->response->setJSON(['status' => $result]);
	}

	function marklaminationcomplete()
	{
        $id = $this->request->getPost('id');
        $type = $this->request->getPost('type');
        $result = $this->PustakapaperbackModel->markLaminationComplete($id, $type);
        return $this->response->setJSON(['status' => $result]);
	}

	function markbindingcomplete()
	{
        $id = $this->request->getPost('id');
        $type = $this->request->getPost('type');
        $result = $this->PustakapaperbackModel->markBindingComplete($id, $type);
        return $this->response->setJSON(['status' => $result]);

	}

	function markfinalcutcomplete()
	{
        $id = $this->request->getPost('id');
        $type = $this->request->getPost('type');
        $result = $this->PustakapaperbackModel->markFinalcutComplete($id, $type);
        return $this->response->setJSON(['status' => $result]);

	}

	function markqccomplete()
	{
        $id = $this->request->getPost('id');
        $type = $this->request->getPost('type');
        $result = $this->PustakapaperbackModel->markQcComplete($id, $type);
        return $this->response->setJSON(['status' => $result]);

	}

	function markcompleted()
	{
        $id = $this->request->getPost('id');
        $type = $this->request->getPost('type');
        $result = $this->PustakapaperbackModel->markCompleted($id, $type);
        return $this->response->setJSON(['status' => $result]);

	}
    //amazon order//
    public function paperbackamazonorder()
    {

        $data['amazon_order'] = $this->PustakapaperbackModel->getAmazonPaperbackOrder();
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('printorders/amazon/paperbackOrderView', $data);
    }

    public function pustakaamazonorderbookslist()
    {

        $selected_book_list = $this->request->getPost('selected_book_list');

        log_message('debug', 'Selected Books list.... ' . $selected_book_list);
        $data['amazon_selected_book_id'] = $selected_book_list;
        $data['amazon_selected_books_data'] = $this->PustakapaperbackModel->getAmazonSelectedBooksList($selected_book_list);
        $data['title'] = '';
        $data['subTitle'] = '';


        return view('printorders/amazon/orderbooksList', $data);
    }

    public function pustakaamazonorderstock()
    {

        $num_of_books = $this->request->getPost('num_of_books');
        $selected_book_list = $this->request->getPost('selected_book_list');
        $ship_type = $this->request->getPost('shipping_type');
        $ship_date = $this->request->getPost('ship_date');
        $order_id = $this->request->getPost('order_id');

        $book_ids = [];
        $book_qtys = [];
        $j = 1;

        for ($i = 0; $i < $num_of_books; $i++) {
            $tmp = 'book_id' . $j;
            $tmp1 = 'bk_qty' . $j++;
            $book_ids[$i] = $this->request->getPost($tmp);
            $book_qtys[$i] = $this->request->getPost($tmp1);
        }

        $data['amazon_selected_book_id'] = $selected_book_list;
        $data['amazon_paperback_stock'] = $this->PustakapaperbackModel->getAmazonStockDetails($selected_book_list);
        $data['book_qtys'] = $book_qtys;
        $data['ship_type'] = $ship_type;
        $data['ship_date'] = $ship_date;
        $data['order_id'] = $order_id;
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/amazon/orderQuantityView', $data);
    }

    public function amazonorderbookssubmit()
    {
        $num_of_books = $this->request->getPost('num_of_books');
        $selected_book_list = $this->request->getPost('selected_book_list');

        $this->PustakapaperbackModel->amazonOrderbooksDetailsSubmit($num_of_books);
        $data['title'] = '';
        $data['subTitle'] = '';


        return view('printorders/amazon/orderbooksSubmitView', $data);
    }

    public function amazonorderbooksstatus()
    {
        $filter = $this->request->getGet('filter') ?? 'all';
        $data['amazon_orderbooks'] = $this->PustakapaperbackModel->amazonInProgressBooks();
        $data['amazon_summary'] = $this->PustakapaperbackModel->amazonSummary($filter);
          $data['filter'] = $filter;
        $data['title'] = '';
        $data['subTitle'] = '';

        // echo "<pre>";
		// print_r($data['amazon_summary']);

        return view('printorders/amazon/orderbooksStatusView', $data);
    }

    public function markshipped()
    {
        $result = $this->PustakapaperbackModel->markShipped();     
        echo $result;
    }

    public function markcancel()
    {
        $result = $this->PustakapaperbackModel->markCancel();
        echo $result;
    }

    public function markreturn()
    {
        $result = $this->PustakapaperbackModel->markReturn();
        echo $result;
    }

    public function amazonorderdetails($order_id)
    {
        $data['order_id'] = $order_id;
        $data['orderbooks'] = $this->PustakapaperbackModel->amazonOrderDetails($order_id);
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('printorders/amazon/orderDetailsView', $data);
    }


    public function totalamazonordercompleted()
    {
        $data['amazon_orderbooks'] = $this->PustakapaperbackModel->amazonInProgressBooks();
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('printorders/amazon/totalCompletedBooks', $data);
    }
    //author order//
    public function authororderbooks()
    {
        $author_id = $this->request->getUri()->getSegment(3);
        $data['pustaka_url'] = config('App')->pustaka_url;
        $data['pod_author_books_data'] = $this->PustakapaperbackModel->getAuthorBooksList($author_id);
        $data['author_id'] = $author_id;
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/author/orderBooksListView', $data);
    }
    public function authororderqtylist()
    {
        $selected_bk_list = $this->request->getPost('selected_bk_list');
        $author_id = $this->request->getPost('author_id');

        log_message('debug', 'Author Order: Selected Books list.... ' . $selected_bk_list);

        $data['pustaka_url'] = config('App')->pustaka_url;
        $data['author_id'] = $author_id;
        $data['title'] = '';
        $data['subTitle'] = '';
        $data['pod_selected_book_id'] = $selected_bk_list;
        $data['pod_selected_books_data'] = $this->PustakapaperbackModel->getSelectedBooksList($selected_bk_list);
        $data['pod_author_addr_details'] =$this->PustakapaperbackModel->getAuthorAddress($author_id);

        return view('printorders/author/orderSelectedBookList', $data);
    }
    public function authororderbookssubmit()
    { 

        // echo"<pre>";
        // print_r($_POST);

        $result = $this->PustakapaperbackModel->authorOrderBooksDetailsSubmit();
        $data['order_id'] = $result['order_id'];
        $data['title']    = '';
        $data['subTitle'] = '';
        return view('printorders/author/orderbooksSubmitView', $data);
    }


    function authorlistdetails(){

		$data['orderbooks'] = $this->PustakapaperbackModel->getAuthorList();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/author/authorListView',$data);
	}

    public function authororderbooksstatus()
    {
        $chartFilter = $this->request->getGet('chart_filter') ?? 'all';
        $data['author_order'] = $this->PustakapaperbackModel->getAuthorOrderDetails();
        $data['orders'] = $this->PustakapaperbackModel->authorInProgressOrder();
        $data['summary'] = $this->PustakapaperbackModel->authorSummary($chartFilter);
        $data['chart_filter'] = $chartFilter;
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/author/orderbooksStatusView', $data);
    }

    public function authorordermarkstart()
    {
        $orderId = $this->request->getPost('order_id');
        $bookId  = $this->request->getPost('book_id');
        if (empty($orderId) || empty($bookId)) {
            return $this->response->setJSON(['status' => 0, 'error' => 'Invalid input data']);
        }
        $result = $this->PustakapaperbackModel->authorOrderMarkStart($orderId, $bookId);
        return $this->response->setJSON(['status' => $result ? 1 : 0]);
    }


    public function markfilesreadycompleted()
    {
        $orderId = $this->request->getPost('order_id');
        $bookId  = $this->request->getPost('book_id');
        $result = $this->PustakapaperbackModel->markFilesReadyCompleted($orderId, $bookId);
        return $this->response->setJSON(['status' => $result]);
    }

    public function markcovercompleted()
    {
        $orderId = $this->request->getPost('order_id');
        $bookId  = $this->request->getPost('book_id');
        $result = $this->PustakapaperbackModel->markCoverCompleted($orderId, $bookId);
        return $this->response->setJSON(['status' => $result]);
    }


    public function markcontentcompleted()
    {
        $orderId = $this->request->getPost('order_id');
        $bookId  = $this->request->getPost('book_id');
        $result = $this->PustakapaperbackModel->markContentCompleted($orderId, $bookId);
        return $this->response->setJSON(['status' => $result]);
    }

    public function marklaminationcompleted()
    {
        $orderId = $this->request->getPost('order_id');
        $bookId  = $this->request->getPost('book_id');
        $result = $this->PustakapaperbackModel->markLaminationCompleted($orderId, $bookId);
        return $this->response->setJSON(['status' => $result]);
    }

    public function markbindingcompleted()
    {
        $orderId = $this->request->getPost('order_id');
        $bookId  = $this->request->getPost('book_id');
        $result = $this->PustakapaperbackModel->markBindingCompleted($orderId, $bookId);
        return $this->response->setJSON(['status' => $result]);
    }

    public function markfinalcutcompleted()
    {
        $orderId = $this->request->getPost('order_id');
        $bookId  = $this->request->getPost('book_id');
        $result = $this->PustakapaperbackModel->markFinalCutCompleted($orderId, $bookId);
        return $this->response->setJSON(['status' => $result]);
    }

    public function markqccompleted()
    {
        $orderId = $this->request->getPost('order_id');
        $bookId  = $this->request->getPost('book_id');
        $result = $this->PustakapaperbackModel->markQcCompleted($orderId, $bookId);
        return $this->response->setJSON(['status' => $result]);
    }

    public function authorordermarkcompleted()
    {
        $orderId = $this->request->getPost('order_id');
        $bookId  = $this->request->getPost('book_id');
        $result = $this->PustakapaperbackModel->authorOrderMarkCompleted($orderId, $bookId);
        return $this->response->setJSON(['status' => $result]);
    }

    public function createauthorinvoice()
    {
        $uri = service('uri');
        $orderId = service('uri')->getSegment(3);
        $data['author'] = $this->PustakapaperbackModel->authorInvoiceDetails($orderId);
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('printorders/author/authorInvoiceView', $data);
    }

    public function createinvoice()
    {
        $orderId = $this->request->getPost('order_id');
        $result= $this->PustakapaperbackModel->createInvoice( $orderId);
        return $this->response->setJSON(['status' => $result]);
    }

    public function authormarkcancel()
    {
        $orderId = $this->request->getPost('order_id');
        $result = $this->PustakapaperbackModel->authorMarkCancel($orderId);
        return $this->response->setJSON(['status' => $result]);
    }
    public function authorordership()
    {
        $order_id = $this->request->getPost('order_id') ?? service('uri')->getSegment(3);
        if (empty($order_id)) {
            return redirect()->back()->with('error', 'Missing Order ID.');
        }
        $data['orderbooks'] = $this->PustakapaperbackModel->authorOrderShip();
        $data['details']    = $this->PustakapaperbackModel->authorOrderDetails($order_id);
        $data['order_id']   = $order_id;
        $data['title']      = '';
        $data['subTitle']   = '';

        return view('printorders/author/authorOrderShip', $data);
    }

    public function authormarkshipped()
    {
        $orderId = $this->request->getPost('order_id');
        $trackingId = $this->request->getPost('tracking_id');
        $trackingUrl = $this->request->getPost('tracking_url');

        $result = $this->PustakapaperbackModel->authorMarkShipped($orderId,$trackingId,$trackingUrl);
        return $this->response->setJSON(['status' => $result]);
    }
    public function authororderdetails()
    {
        $order_id = $this->request->getPost('order_id');
        if (empty($order_id)) {
            $order_id = $this->request->getUri()->getSegment(3);
        }
        $data['orderbooks'] = $this->PustakapaperbackModel->authorOrderDetails($order_id);
        $data['title']      = '';
        $data['subTitle']   = '';

        return view('printorders/author/authorOrderDetails', $data);
    }

    public function authormarkpay()
    {
        $orderId = $this->request->getPost('order_id');
        $result = $this->PustakapaperbackModel->authorMarkPay($orderId);
        return $this->response->setJSON(['status' => $result]);
    }

    public function totalauthorordercompleted()
    {
        $data['orders'] = $this->PustakapaperbackModel->authorInProgressOrder();
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('printorders/author/totalCompletedOrders', $data);
    }
    //bookshop orders//
    public function bookshopordersdashboard()
    {
        $data['bookshop'] = $this->PustakapaperbackModel->getBookshopOrdersDetails();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/bookshop/paperbackOrderView', $data);
    }

    public function bookshoporderbooks()
    {
        $selected_book_list = $this->request->getPost('book_ids');
        $data['selected_books_data'] = $this->PustakapaperbackModel->offlineSelectedBooksList($selected_book_list);
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/bookshop/orderBooksList', $data);
    }

    public function submitbookshoporders()
    {
        $postData = $this->request->getPost();
        $result = $this->PustakapaperbackModel->submitBookshopOrders($postData);
        return $this->response->setJSON($result);
        
    }
    
    public function bookshoporderbooksstatus()
    {
        $chart_filter = $this->request->getGet('chart_filter') ?? 'all';
        $data['chart_filter'] = $chart_filter;
        $data['bookshop_status'] = $this->PustakapaperbackModel->bookshopProgressBooks();
        $data['bookshop_summary'] = $this->PustakapaperbackModel->bookshopSummary($chart_filter);
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/bookshop/orderbooksStatusView', $data);
    }

    public function bookshopordership($order_id)
    {

        $data['order_id'] = $order_id;
        $data['ship'] = $this->PustakapaperbackModel->bookshopOrderShip($order_id);
        $data['orderbooks'] = $this->PustakapaperbackModel->bookshopOrderDetails($order_id);
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/bookshop/bookshopOrderShip', $data);
    }

    public function bookshopmarkshipped()
    {
        $order_id= $this->request->getPost('order_id');
        $tracking_id= $this->request->getPost('tracking_id');
        $tracking_url= $this->request->getPost('tracking_url');
        
        $result = $this->PustakapaperbackModel->bookshopMarkShipped($order_id, $tracking_id, $tracking_url);
        return $this->response->setJSON(['status' => $result]);
    }

    public function bookshopmarkcancel()
    {
        $order_id = $this->request->getPost('order_id');
        $result = $this->PustakapaperbackModel->bookshopMarkCancel($order_id);
        return $this->response->setJSON(['status' => $result]);
    }


    public function bookshopmarkpay()
    {
        $orderId= $this->request->getPost('order_id');
        $result = $this->PustakapaperbackModel->bookshopMarkPay($orderId);
        return $this->response->setJSON(['status' => $result]);
    }

    public function bookshoporderdetails($order_id)
    {
        $data['order_id'] = $order_id;
        $data['orderbooks'] = $this->PustakapaperbackModel->bookshopOrderDetails($order_id);
        $data['title'] = '';
        $data['subTitle'] = '';

        return  view('printorders/bookshop/orderDetailView', $data);
    }

    public function totalbookshopordercompleted()
    {
        $data['orderbooks'] = $this->PustakapaperbackModel->bookshopProgressBooks();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/bookshop/totalCompletedBooks', $data);
    }

    public function createbookshoporder($order_id)
    {
        $data['order_id'] = $order_id;
        $data['bookshop'] = $this->PustakapaperbackModel->bookshopInvoiceDetails($order_id);
        $data['title'] = '';
        $data['subTitle'] = '';

        return  view('printorders/bookshop/bookshopInvoiceView', $data);
    }

    public function createbookshopinvoice()
    {
        $post = $this->request->getPost();
        $result = $this->PustakapaperbackModel->createBookshopInvoice($post);
        return $this->response->setJSON($result);
    }

    public function bookshopdetails()
    {
        
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('printorders/bookshop/addBookshopDetails', $data);

    }
    public function addbookshop()
    {
        $post = $this->request->getPost();   
        $result = $this->PustakapaperbackModel->addBookshop($post);

        if ($result == 1) {
            return $this->response->setJSON([
                'status' => 1,
                'message' => 'Bookshop added successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 0,
                'message' => 'Failed to add bookshop'
            ]);
        }
    }
    //flipkart orders//
    public function paperbackflipkartorder()
    {
        $data['flipkart_order'] = $this->PustakapaperbackModel->getFlipkartPaperbackOrder();
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('printorders/flipkart/paperbackOrderView', $data);
    }

    public function pustakaflipkartorderbookslist()
    {

        $selected_book_list = $this->request->getPost('selected_book_list');
        log_message('debug', 'Selected Books list.... ' . $selected_book_list);

        $data['flipkart_selected_book_id'] = $selected_book_list;
        $data['flipkart_selected_books_data'] = $this->PustakapaperbackModel->getFlipkartSelectedBooksList($selected_book_list);
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/flipkart/orderbooksList', $data);
    }

    public function pustakaflipkartorderstock()
    {

        $num_of_books       = $this->request->getPost('num_of_books');
        $selected_book_list = $this->request->getPost('selected_book_list');
        $ship_date          = $this->request->getPost('ship_date');
        $order_id           = $this->request->getPost('order_id');

        $j = 1;
        $book_ids  = [];
        $book_qtys = [];

        for ($i = 0; $i < $num_of_books; $i++) {
            $tmp  = 'book_id' . $j;
            $tmp1 = 'bk_qty' . $j++;
            $book_ids[$i]  = $this->request->getPost($tmp);
            $book_qtys[$i] = $this->request->getPost($tmp1);
        }

        $data['flipkart_selected_book_id'] = $selected_book_list;
        $data['flipkart_paperback_stock']  = $this->PustakapaperbackModel->getFlipkartStockDetails($selected_book_list);
        $data['book_qtys'] = $book_qtys;
        $data['ship_date'] = $ship_date;
        $data['order_id']  = $order_id;
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/flipkart/orderQuantityView', $data);
    }

    public function flipkartorderbookssubmit()
    {
        $num_of_books       = $this->request->getPost('num_of_books');
        $selected_book_list = $this->request->getPost('selected_book_list');
        $data['title'] = '';
        $data['subTitle'] = '';
        $result = $this->PustakapaperbackModel->flipkartOrderbooksDetailsSubmit($num_of_books);

        return view('printorders/flipkart/orderbooksSubmitView', $data);
    }

    public function flipkartorderbooksstatus()
    {
        $data['flipkart_orderbooks'] = $this->PustakapaperbackModel->flipkartInprogressBooks();
        $data['flipkart_summary'] =$this->PustakapaperbackModel->flipkartSummary();
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('printorders/flipkart/orderbooksStatusView', $data);
    }

    public function flipkartmarkshipped()
    {
        $flipkart_order_id = $_POST['flipkart_order_id'];
        $book_id = $_POST['book_id'];
        $result = $this->PustakapaperbackModel->flipkartMarkShipped($flipkart_order_id, $book_id);
        return $this->response->setJSON(['status' => $result]);
    }

    public function flipkartmarkcancel()
    {
        $flipkart_order_id = $_POST['flipkart_order_id'];
        $book_id = $_POST['book_id'];
        $result = $this->PustakapaperbackModel->flipkartMarkCancel($flipkart_order_id, $book_id);
        return $this->response->setJSON(['status' => $result]);
    }

    public function flipkartmarkreturn()
    {
        $flipkart_order_id = $_POST['flipkart_order_id'];
        $book_id = $_POST['book_id'];
        $result = $this->PustakapaperbackModel->flipkartMarkReturn($flipkart_order_id, $book_id);
        return $this->response->setJSON(['status' => $result]);
    }

    public function totalflipkartordercompleted()
    {
        $data['flipkart_orderbooks'] = $this->PustakapaperbackModel->flipkartInprogressBooks();
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('printorders/flipkart/totalCompletedBooks', $data);
    }

    public function flipkartorderdetails($order_id)
    {
        $data['order_id'] = $order_id;
        $data['orderbooks'] = $this->PustakapaperbackModel->flipkartOrderDetails($order_id);
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('printorders/flipkart/orderDetailsView', $data);
    }
    //bookfair sale or return//

    // Show Add Order Form
    public function addSaleOrReturnOrder()
    {
         $data['title'] = '';
        $data['bookshops'] = $this->PaperbackModel->getBookshops();
        $data['combos']    = $this->PaperbackModel->getCombos();

        return view('printorders/bookfair/saveSaleOrReturnOrder', $data);
    }
     public function getBookshopTransport()
    {
        $id = $this->request->getPost('bookshop_id');
        return $this->response->setJSON(
            $this->PaperbackModel->getBookshopTransport($id)
        );
    }

    public function saveSaleOrReturnOrder()
    {
        $comboId = $this->request->getPost('combo_id');

        if(!$comboId){
            return redirect()->back()->with('error','Please select combo.');
        }

        $orderId = time();

        // AUTO CREATE DATETIME
        $createDate = date('Y-m-d H:i:s');

        $orderData = [
            'order_id' => $orderId,
            'bookshop_id' => $this->request->getPost('bookshop_id'),
            'combo_id' => $comboId,
            'book_fair_name' => $this->request->getPost('book_fair_name'),
            'create_date' => $createDate,
            'preferred_transport' => $this->request->getPost('preferred_transport'),
            'preferred_transport_name' => $this->request->getPost('preferred_transport_name'),
            'remark' => $this->request->getPost('remark'),
            'status' => 0
        ];

        try {

            $this->PaperbackModel->createOrder($orderData,$comboId);

            return redirect()->back()->with('success','Order Created Successfully');

        } catch (\Throwable $e) {

            return redirect()->back()->with('error',$e->getMessage());

        }
    }

    // LIST ORDERS (status=0)
    public function bookfairBookshopOrdersDashboard()
    {
        $data['title']  = 'Bookfair Orders Dashboard';
        $data['orders'] = $this->PaperbackModel->getPendingOrders();

        return view('printorders/bookfair/bookFairDashboard', $data);
    }

    // RETURN PAGE
    public function bookfairBookshopreturnView($orderId)
    {
        $data = $this->PaperbackModel->getReturnOrder($orderId);

        $data['title'] = 'Return Bookfair Order';

        return view('printorders/bookfair/bookfairBookShopReturn', $data);
    }

    // SAVE RETURN
    public function bookfairBookshopsaveReturn()
    {
        $orderId   = $this->request->getPost('order_id');
        $returnQty = $this->request->getPost('return_qty');  // array keyed by book_id
        $discount  = (float) $this->request->getPost('discount') ?? 0; // single order discount

        $this->PaperbackModel->processReturn($orderId, $returnQty, $discount);

        return redirect()->to('paperback/ordersdashboard')
            ->with('success','Return Completed');
    }
    public function bookfairBookshopShippedOrders()
    {
        $data['title']  = 'Bookfair – Shipped Orders';
        $data['orders'] = $this->PaperbackModel->getBookfairOrders(1);

        return view('printorders/bookfair/bookfairBookshopShippedOrders', $data);
    }

    public function bookfairBookshopSoldOrders()
    {
        $data['title']  = 'Bookfair - Sold Orders';
        $data['orders'] = $this->PaperbackModel->getBookfairOrders(2);

        return view('printorders/bookfair/bookfairBookshopSoldOrders', $data);
    }
    public function bookfairBookshopOrderDetails($orderId)
    {
        $data['title'] = 'Bookfair BookShop Sold Order Details';

        $data['order'] = $this->PaperbackModel->getBookfairOrderDetails($orderId);

        if (empty($data['order'])) {
            return redirect()->back()->with('error','Order not found');
        }

        return view('printorders/bookfair/bookfairBookshopOrderDetails',$data);
    }
    public function bookfairShippedOrderDetails($orderId)
    {
        $data['title'] = 'Bookfair BookShop Shipped Order Details';
        $data['order'] = $this->PaperbackModel->getBookfairOrderDetails($orderId);

        if (empty($data['order'])) {
            return redirect()->back()->with('error','Order not found');
        }

        return view('printorders/bookfair/bookfairShippedOrderDetails',$data);
    }
    public function bookfairsaleorreturnview()
    {
        $data['bookfair_sales'] = $this->PaperbackModel->getBookfairSalesDetails();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/bookfair/bookfairSaleOrReturnView', $data);
    }

    public function bookfairdetailsview($order_id)
    {
        $data['order_id'] = $order_id;
        $data['bookfair_details'] = $this->PaperbackModel->getBookFairdetails($order_id);
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('printorders/bookfair/bookfairDetailsView', $data);

    }
    public function ship($order_id)
    {
        $model = new PaperbackModel();

        $result = $model->shipBookfairOrder($order_id);

        if ($result) {
            return redirect()
                ->to(base_url('paperback/bookfairsaleorreturnview'))
                ->with('success', 'Order shipped successfully');
        } else {
            return redirect()
                ->back()
                ->with('error', 'Order shipping failed');
        }
    }
    
    public function downloadbookfairexcel($order_id)
    {
        $result = $this->PaperbackModel->getBookFairdetails($order_id);

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
            'Sending Date',
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
            $sheet->setCellValue(
                'H'.$rowNum,
                date('d-m-Y', strtotime($row['sending_date']))
            );
            $sheet->setCellValue('I'.$rowNum, $total);

            $rowNum++;
        }

        // Grand total row
        $sheet->setCellValue('H'.$rowNum, 'Grand Total');
        $sheet->setCellValue('I'.$rowNum, $grandTotal);

        $fileName = 'Bookfair_Books_'.$order_id.'.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
    
}
