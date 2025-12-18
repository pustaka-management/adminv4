<?php

namespace App\Controllers;

use App\Models\PodModel;

class Pod extends BaseController
{
    protected $podModel;

        public function __construct()
    {
        helper(['form', 'url', 'file', 'email', 'html', 'cookie', 'text']);
        $this->podModel = new PodModel();
    }

    public function publisherDashboard()
    {
        $data['publisher_data'] = $this->podModel->getPODPublishers();
        $data['title'] = 'POD Publisher List';

        return view('printorders/pod/publisherDashboard', $data);
    }

     public function publisherAdd()
    {
       
        $data['title'] = 'Add New Publisher';

        return view('printorders/pod/publisherAdd', $data);
        // echo "print";
    }

    public function PodpublisherSubmit()
    {
        $publisher_data = [
            "publisher_name"      => $this->request->getPost('publisher_name'),
            "address"             => $this->request->getPost('address'),
            "city"                => $this->request->getPost('publisher_city'),
            "contact_person"      => $this->request->getPost('publisher_contact'),
            "contact_mobile"      => $this->request->getPost('publisher_mobile'),
            "cover_reqs"          => $this->request->getPost('cover_reqs'),
            "content_reqs"        => $this->request->getPost('content_reqs'),
            "other_reqs"          => $this->request->getPost('other_reqs'),
            "preferred_transport" => $this->request->getPost('preferred_transport'),
            "rejection_remarks"   => $this->request->getPost('rejection_remarks'),
            "status"              => 1
        ];

        $result = $this->podModel->PodpublisherSubmit($publisher_data);

        return $this->response->setJSON($result);
    }
    public function editPublisher($id)
    {
        $data['title'] = 'Edit Publisher';

        // Fetch existing publisher data
        $data['publisher'] = $this->podModel->getPublisherById($id);

        return view('printorders/pod/publisherEdit', $data);
    }

    public function updatePublisher()
    {
        $publisher_data = [
            "publisher_name"      => $this->request->getPost('publisher_name'),
            "address"             => $this->request->getPost('address'),
            "city"                => $this->request->getPost('publisher_city'),
            "contact_person"      => $this->request->getPost('publisher_contact'),
            "contact_mobile"      => $this->request->getPost('publisher_mobile'),
            "cover_reqs"          => $this->request->getPost('cover_reqs'),
            "content_reqs"        => $this->request->getPost('content_reqs'),
            "other_reqs"          => $this->request->getPost('other_reqs'),
            "preferred_transport" => $this->request->getPost('preferred_transport'),
            "rejection_remarks"   => $this->request->getPost('rejection_remarks')
        ];

        $id = $this->request->getPost('id');

        $result = $this->podModel->updatePublisher($id, $publisher_data);

        return $this->response->setJSON($result);
    }


     public function PodDashboard()
    {
       
        $data['title'] = '';
        $data['invoice'] = $this->podModel->getPODInvoiceData();
        $data['dashboard'] = $this->podModel->getPODDashboardData();
        $data['pending_books']=$this->podModel->getPendingBooksData();

        // echo "<pre>";
        // print_r( $data);

        return view('printorders/pod/podDashboard', $data);
        
    }
        public function publisherView($publisher_id)
    {
        $data['title'] = '';
        $data['publisher'] = $this->podModel->getPublisherById($publisher_id);
        $data['books']     = $this->podModel->getBooksByPublisher($publisher_id);
        return view('printorders/pod/publisherView', $data);
    }
    public function bookView($book_id)
    {
        $data['title'] = '';
        $data['book'] = $this->podModel->getBookById($book_id);

        if (empty($data['book'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book not found.');
        }

        return view('printorders/pod/bookView', $data);
    }
 
    public function PodInvoice(){

        $data['title'] = '';
        $data['invoice'] = $this->podModel->getPODInvoiceData();
        $data['summary'] = $this->podModel->GetTotalSummary();
        $data['publisher']= $this->podModel->GetPublisherInvoiceReport();
        $data['month']=$this->podModel->GetMonthlyTotalInvoice();

        // echo "<pre>";
        // print_r( $data['publisher']);

        return view('printorders/pod/podInvoice', $data);
    }

    public function monthlyDetails($month, $type)
    {
        $data['month'] = $month;
        $data['type']  = $type;

        // Get details from model
        $data['title'] = '';
        $data['details'] = $this->podModel->getInvoiceDetailsByMonth($month, $type);

        
        // echo "<pre>";
        // print_r( $data);

        return view('printorders/pod/monthlyDetails', $data);
    }


    public function EndToEndPod()
    {
        $data['title'] = '';
        $data['pod'] = $this->podModel->getPodWork();

        return view('printorders/pod/EndToEndPoddashboard', $data);
    }

    public function markProcess($step)
    {
        $book_id = $this->request->getPost('book_id');

        if (!$book_id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Book ID missing'
            ]);
        }

        $result = $this->podModel->markProcess($step, $book_id);

        return $this->response->setJSON([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? '' : 'Unable to update step'
        ]);
    }

    public function  podOrderDetails()
    {
        $data['title'] = 'POD Order Details';
        $data['summary'] = $this->podModel->getBooksCompletedData();
        // $data['order_details'] = $this->podModel->getPODDashboardData($book_id);
        $data['dashboard'] = $this->podModel->getPODDashboardData();
        $data['pending_books']=$this->podModel->getPendingBooksData();

        return view('printorders/pod/podOrdersview', $data);
    }

    public function podBookAdd()
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }

        $model = new PodModel();
        $data['publisher_list'] = $model->getPodPublishers();
        $data['title'] = 'Create POD Order';

        return view('printorders/pod/podAddBook', $data);
    }

    public function podBookPost()
    {
        $model = new PodModel();
        $result = $model->addPodBook();

        return $this->response->setJSON([
            'status' => $result ? 1 : 0
        ]);
    }
    public function completedPodOrders()
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }
        $podModel = new \App\Models\PodModel();
        $data['pending_books'] = $podModel->getPendingBooksData();
        $data['title'] = '';
    
        return view('printorders/pod/completedPodOrdersView', $data);
    }

    public function podBooksCompleted()
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to(base_url('adminv4/index'));
        }

        $podModel = new \App\Models\PodModel();
        $data['completed_books'] = $podModel->getBooksCompletedData();

        // Extract publishers array from returned data
        $data['publishers'] = $data['completed_books']['publishers'];

        $data['title'] = 'POD Publisher Summary';
        return view('printorders/pod/podPublisherSummary', $data);
    }
    public function monthDetailsPage($month)
    {
        $podModel = new \App\Models\PodModel();
        $details = $podModel->getMonthDetails($month);

        $data['month_name'] = $month;
        $data['month_details'] = $details;
        $data['title'] = '';

        return view('printorders/pod/podMonthDetailsPage', $data);
    }
    public function podBookCreateInvoice($book_id = null)
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to(base_url('adminv3'));
        }

        $podModel = new \App\Models\PodModel();

        $data['pod_publisher_book'] = $podModel->getPODPublisherBookDetails($book_id);
        $data['pod_publisher'] = $podModel->getPODPublisherDetails($data['pod_publisher_book']['publisher_id']);
        $data['title'] = '';

        return view('printorders/pod/PodBookCreateInvoice', $data);
    }

    public function createInvoice()
    {
        $podModel = new \App\Models\PodModel();

        $book_id = $this->request->getPost('book_id');
        $invoice_number = $this->request->getPost('invoice_number');

        if (!$book_id || !$invoice_number) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Missing data']);
        }

        $result = $podModel->createInvoice($book_id, $invoice_number);

        if ($result == 1) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'fail']);
        }
    }

    public function podPublisherManage()
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to(base_url('adminv3'));
        }

        $podModel = new \App\Models\PodModel();
        $data['publisher_data'] = $podModel->getPODPublishers();
        $data['title'] = '';

        return view('printorders/pod/PodPublisherManage', $data);
    }
    public function pendingInvoices()
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }

        $podModel = new PodModel();
        $data['pending_invoices'] = $podModel->getPendingInvoices();
        $data['title'] = '';

        return view('printorders/pod/PendingInvoices', $data);
    }

    public function pendingInvoiceDetails($publisher_id)
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }

        $podModel = new PodModel();
        $data['invoice_details'] = $podModel->getPendingInvoicesDetails($publisher_id);
        $data['publisher_name'] = $data['invoice_details'][0]['publisher_name'] ?? '';
        $data['title'] = '';

        return view('printorders/pod/PendingInvoiceDetails', $data);
    }
    public function raisedInvoices()
    {
        $podModel = new \App\Models\PodModel();

        // Get raised invoices where invoice_flag=1 and payment_flag=0
        $data['raised_invoices'] = $podModel->getRaisedInvoices();
        $data['raised_invoice_data']=$podModel->getRaisedInvoicesData();
        $data['title'] = '';

        echo view('printorders/pod/RaisedInvoices', $data);
    }
    public function raisedInvoiceDetails($publisher_id = null)
    {
        if (!$publisher_id) {
            return redirect()->to(base_url('pod/raisedinvoices'));
        }

        $podModel = new \App\Models\PodModel();

        $data['publisher'] = $podModel->getPublisherById($publisher_id);
        $data['books'] = $podModel->getRaisedInvoiceBooks($publisher_id);
        $data['title'] = '';

        return view('printorders/pod/RaisedInvoiceDetails', $data);
    }
    public function paidInvoices()
    {
        $podModel = new PodModel();
        $data['paid_invoices'] = $podModel->getPaidInvoices(); // payment_flag=1
        $data['title'] = '';
       
        return view('printorders/pod/PaidInvoices', $data);
       
    }

    public function paidInvoiceDetails($publisher_id = null)
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to(base_url('adminv3'));
        }

        $podModel = new \App\Models\PodModel();
        $data['publisher'] = $podModel->getPublisherById($publisher_id);
        $data['books'] = $podModel->getPaidInvoiceBooks($publisher_id);
        $data['title'] = '';

        return view('printorders/pod/PaidInvoiceDetails', $data);
    }
    public function addPodWork()
    {
        $podModel = new \App\Models\PodModel();
        $languageModel = new \App\Models\LanguageModel();

        $data['publisher_list'] = $podModel->getPODPublishers();
        $data['lang_details'] = $languageModel->getAllLanguages();
        $data['title'] = "";

        return view('printorders/pod/AddPodWork', $data);
    }
    public function insertPodWork()
    {
        helper('text'); // optional if you need string helper

        $data = [
            'publisher_id'       => $this->request->getPost('publisher_id'),
            'publisher_name'     => $this->request->getPost('publisher_name'),
            'publisher_contact'  => $this->request->getPost('publisher_contact'),
            'publisher_mobile'   => $this->request->getPost('publisher_mobile'),
            'book_title'         => $this->request->getPost('book_title'),
            'cost_per_page'      => $this->request->getPost('cost_per_page'),
            'language'           => $this->request->getPost('lang_id'),
            'url_title'          => $this->request->getPost('url_title'),
            'sample_book_flag'   => $this->request->getPost('sample_book'),
            'layout_dec'         => $this->request->getPost('layout_dec'),
            'color_dec'          => $this->request->getPost('color_dec'),
            'cover_dec'          => $this->request->getPost('cover_dec'),
        ];

        try {
            $builder = $this->db->table('pod_indesign');
            $builder->insert($data);

            if ($this->db->affectedRows() > 0) {
                return $this->response->setJSON(['status' => 1]);
            } else {
                return $this->response->setJSON(['status' => 0, 'message' => 'Insert failed']);
            }
        } catch (\Exception $e) {
            log_message('error', 'POD Insert Error: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function mark_start()               { return $this->updatePodStatus($this->request->getPost('book_id'), 'start_flag'); }
    public function indesign_complete()        { return $this->updatePodStatus($this->request->getPost('book_id'), 'indesign_flag'); }
    public function indesign_qc()              { return $this->updatePodStatus($this->request->getPost('book_id'), 'indesign_qc_flag'); }
    public function cover_complete()           { return $this->updatePodStatus($this->request->getPost('book_id'), 'cover_flag'); }
    public function final_approval()           { return $this->updatePodStatus($this->request->getPost('book_id'), 'final_approval'); }
    public function sample_complete()          { return $this->updatePodStatus($this->request->getPost('book_id'), 'sample_book_flag'); }
    public function file_upload()              { return $this->updatePodStatus($this->request->getPost('book_id'), 'file_upload'); }

      private function updatePodStatus($book_id, $column)
    {
        $updated = $this->podModel->updatePodColumn($book_id, $column);
        if ($updated > 0) {
            return $this->response->setJSON(['status' => 'success']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Update failed or already updated']);
    }

    public function viewBookDetails($book_id)
    {
    $builder = $this->db->table('pod_indesign'); // table name specify pannuthu
    $book = $builder
        ->select('pod_indesign.*, language_tbl.language_name')
        ->join('language_tbl', 'language_tbl.language_id = pod_indesign.language', 'left')
        ->where('pod_book_id', $book_id)
        ->get()
        ->getRowArray(); 

        if (!$book) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Book not found");
        }

        $data['book'] = $book;
        $data['title'] = "POD Book Details";

        return view('printorders/pod/ViewPodBookDetails', $data);
    }
    public function editPublisherBookDetails($book_id = null)
    {
        // Check if user is logged in
        if (!session()->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        if ($book_id === null) {
            $book_id = $this->request->uri->getSegment(3);
        }
        $data['pod_publisher_book_details'] = $this->podModel->editPublisherBookDetails($book_id);
        $data['title'] = "";
        
        // echo "<pre>";
        // print_r( $data['pod_publisher_book_details']);

        return view('printorders/pod/editPublisherBookDetails', $data);
    }
    public function podPublisherBookEdit()
    {
        $postData = $this->request->getPost();
        $result = $this->podModel->editPublisherBook($postData);
        return $this->response->setJSON($result);
    }

     public function mark_payment()
    {
        $book_id = $this->request->getPost('book_id');
        $result =  $this->podModel->mark_payment($book_id);
        return $this->response->setJSON($result);
    }

}