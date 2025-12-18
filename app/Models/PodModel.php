<?php

namespace App\Models;

use CodeIgniter\Model;

class PodModel extends Model
{
    protected $table = '';



    public function getPODDashboardData(){

        $pod_publisher_sql = "SELECT 
                                COUNT(*) AS total,
                                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS active,
                                SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) AS inactive
                            FROM pod_publisher";
        $pod_publisher_query = $this->db->query($pod_publisher_sql);
        $data['publisher'] = $pod_publisher_query->getResultArray()[0]; 

        $pod_orders_sql = "SELECT 
                                COUNT(*) AS total,
                                SUM(CASE WHEN delivery_flag = 1 THEN 1 ELSE 0 END) AS completed,
                                SUM(CASE WHEN delivery_flag = 0 THEN 1 ELSE 0 END) AS pending
                            FROM pod_publisher_books";
        $pod_orders_query = $this->db->query($pod_orders_sql);
        $data['orders'] = $pod_orders_query->getResultArray()[0]; 

        $pod_invoice_sql = "SELECT 
                                COUNT(*) AS total,
                                SUM(CASE WHEN invoice_flag = 1 THEN 1 ELSE 0 END) AS completed,
                                SUM(CASE WHEN invoice_flag = 0 THEN 1 ELSE 0 END) AS pending
                            FROM pod_publisher_books";
        $pod_invoice_query = $this->db->query($pod_invoice_sql);
        $data['invoice'] = $pod_invoice_query->getResultArray()[0]; 

         $pod_full_sql = "SELECT 
                                COUNT(*) AS total,
                                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS completed,
                                SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) AS pending
                            FROM pod_indesign";
        $pod_full_query = $this->db->query($pod_full_sql);
        $data['pod'] = $pod_full_query->getResultArray()[0];

        return $data;
    }

    public function getPODPublishers()
    {

        $pod_publisher_sql = "SELECT * FROM pod_publisher WHERE status=1";
        $pod_publisher_query = $this->db->query($pod_publisher_sql);
        $data['publisher'] = $pod_publisher_query->getResultArray(); //  fixed

    
        $sql = "SELECT * FROM pod_publisher WHERE status=1";
        $query = $this->db->query($sql);
        $result = $query->getResultArray(); //  fixed

        $data['publisher_details'] = $result[0] ?? null; // avoid undefined index

        return $data;
    }
    // Get books of a publisher
    public function getBooksByPublisher($publisher_id)
    {
        return $this->db->table('pod_publisher_books')
                        ->where('publisher_id', $publisher_id)
                        ->orderBy('book_id', 'ASC')
                        ->get()
                        ->getResultArray();
    }

    public function PodpublisherSubmit($publisher_data)
    {
        $db = db_connect();

        if ($db->table('pod_publisher')->insert($publisher_data)) {
            return ['status' => 'success'];
        }
        return ['status' => 'error'];
    }
    public function getPublisherById($id)
    {
        return $this->db->table('pod_publisher')->where('id', $id)->get()->getRowArray();
    }

    public function updatePublisher($id, $data)
    {
        $db = db_connect();
        if ($db->table('pod_publisher')->where('id', $id)->update($data)) {
            return ['status' => 'success'];
        }
        return ['status' => 'error'];
    }
    public function getBookById($book_id)
    {
        return $this->db->table('pod_publisher_books')
                        ->where('book_id', $book_id)
                        ->get()
                        ->getRowArray();
    }

    public function getPODInvoiceData(){
        $pod_pending_sql = "SELECT count(*) as pending_invoice, sum(invoice_value) as pending_total 
                           FROM pod_publisher_books 
                           where invoice_flag=0 and payment_flag=0";
        $pod_pending_query = $this->db->query($pod_pending_sql);
        $data['pending'] = $pod_pending_query->getResultArray()[0]; 

        $pod_raised_sql = "SELECT count(*) as raised_invoice,sum(invoice_value) as raised_total 
                           FROM pod_publisher_books 
                           where qc_flag=1 and invoice_flag=1 and payment_flag=0";
        $pod_raised_query = $this->db->query($pod_raised_sql);
        $data['raised'] = $pod_raised_query->getResultArray()[0]; 

        $pod_paid_sql = "SELECT count(*) paid_invoice,
                         sum(invoice_value) as paid_total 
                         FROM pod_publisher_books where payment_flag=1";
        $pod_paid_query = $this->db->query($pod_paid_sql);
        $data['paid'] = $pod_paid_query->getResultArray()[0]; 

        $pod_pending_invoice_sql = "SELECT pod_publisher.publisher_name, pod_publisher.igst_flag, pod_publisher_books.* FROM pod_publisher_books, pod_publisher  
									WHERE  pod_publisher_books.invoice_flag = 0 
									and pod_publisher_books.publisher_id = pod_publisher.id";
		$pod_pending_invoice_query = $this->db->query($pod_pending_invoice_sql);
		$data['pending_invoice_list'] = $pod_pending_invoice_query->getResultArray();

        return $data;
    }

    function GetTotalSummary(){

		$pod_totalinvoice_sql = "SELECT sum(pod_publisher_books.invoice_value) as total_invoice
										FROM pod_publisher_books, pod_publisher 
										WHERE pod_publisher_books.publisher_id = pod_publisher.id 
										and pod_publisher_books.invoice_flag=1";
		$pod_totalinvoice_query = $this->db->query($pod_totalinvoice_sql);
		$tmp =$pod_totalinvoice_query->getResultArray()[0];
		$data['TotalInvoice']=$tmp['total_invoice'];

		$pod_totalpaid_sql = "SELECT  sum(pod_publisher_books.invoice_value) as total_paid
							FROM pod_publisher_books, pod_publisher
							WHERE pod_publisher_books.publisher_id = pod_publisher.id 
							and pod_publisher_books.invoice_flag=1 and pod_publisher_books.payment_flag=1";
		$pod_totalpaid_query = $this->db->query($pod_totalpaid_sql);
		$tmp=$pod_totalpaid_query->getResultArray()[0];
		$data['TotalPaid'] =$tmp['total_paid'];

		$pod_totalpending_sql = "SELECT sum(pod_publisher_books.invoice_value) as total_pending
								FROM pod_publisher_books, pod_publisher
								WHERE pod_publisher_books.publisher_id =pod_publisher.id 
								and pod_publisher_books.invoice_flag=1
								and pod_publisher_books.payment_flag=0";
		$pod_totalpending_query = $this->db->query($pod_totalpending_sql);
		$tmp =$pod_totalpending_query->getResultArray()[0];
		$data['TotalPending'] = $tmp['total_pending'];

        $pod_totalamount_sql = "SELECT 
                                    SUM(cgst) AS total_cgst,
                                    SUM(sgst) AS total_sgst,
                                    SUM(igst) AS total_igst
                                FROM pod_publisher_books where invoice_flag=1";

        $pod_totalamount_query = $this->db->query($pod_totalamount_sql);
        $result = $pod_totalamount_query->getRowArray(); 
       
        $data['cgst'] = $result['total_cgst'] ?? 0;
        $data['sgst'] = $result['total_sgst'] ?? 0;
        $data['igst'] = $result['total_igst'] ?? 0;

        $pod_pending_sql = "SELECT sum(pod_publisher_books.invoice_value) as total_invoice
										FROM pod_publisher_books, pod_publisher 
										WHERE pod_publisher_books.publisher_id = pod_publisher.id 
										and pod_publisher_books.invoice_flag=0";
		$pod_pending_query = $this->db->query($pod_pending_sql);
		$tmp =$pod_pending_query->getResultArray()[0];
		$data['pending']=$tmp['total_invoice'];

        $data['overallInvoice']=$data['TotalInvoice'] +$data['pending'];

        return $data;	
	}
     
    function GetPublisherInvoiceReport()
    {
        $pod_total_invoice_sql = "SELECT pod_publisher.publisher_name,
                                        pod_publisher_books.publisher_id, 
                                        SUM(pod_publisher_books.invoice_value) AS amt 
                                FROM pod_publisher_books, pod_publisher 
                                WHERE pod_publisher_books.publisher_id = pod_publisher.id 
                                    AND pod_publisher_books.invoice_flag=1 
                                GROUP BY pod_publisher_books.publisher_id";
        $pod_total_invoice_query = $this->db->query($pod_total_invoice_sql)->getResultArray();

        $pod_total_invoices = [];

        foreach ($pod_total_invoice_query as $total_invoice) {
            $tmp['publisher_name']       = $total_invoice['publisher_name'];
            $tmp['total_invoice_amount'] = indian_format($total_invoice['amt'], 2);
            $tmp['pending_amount']       = 0;
            $tmp['paid_amount']          = 0;
            $tmp['publisher_id']         =$total_invoice['publisher_id'];
            $publisher_id                = $total_invoice['publisher_id'];
            $pod_total_invoices[$publisher_id] = $tmp;
        }

        // Pending invoices
        $pod_pending_invoice_sql = "SELECT pod_publisher.publisher_name,
                                        pod_publisher_books.publisher_id, 
                                        SUM(pod_publisher_books.invoice_value) AS amt1
                                    FROM pod_publisher_books, pod_publisher 
                                    WHERE pod_publisher_books.publisher_id = pod_publisher.id 
                                    AND pod_publisher_books.invoice_flag=1
                                    AND pod_publisher_books.payment_flag=0 
                                    GROUP BY pod_publisher_books.publisher_id";
        $pod_pending_invoice_query = $this->db->query($pod_pending_invoice_sql)->getResultArray();

        foreach ($pod_pending_invoice_query as $pending_invoice) {
            $publisher_id = $pending_invoice['publisher_id'];
            if (isset($pod_total_invoices[$publisher_id])) {
                $tmp = $pod_total_invoices[$publisher_id];
                $tmp['pending_amount'] = indian_format($pending_invoice['amt1'], 2);
                $pod_total_invoices[$publisher_id] = $tmp;
            }
        }

        // Paid invoices
        $pod_paid_invoice_sql = "SELECT pod_publisher.publisher_name,
                                        pod_publisher_books.publisher_id, 
                                        SUM(pod_publisher_books.invoice_value) AS amt2
                                FROM pod_publisher_books, pod_publisher
                                WHERE pod_publisher_books.publisher_id = pod_publisher.id 
                                AND pod_publisher_books.invoice_flag=1 
                                AND pod_publisher_books.payment_flag=1 
                                GROUP BY pod_publisher_books.publisher_id";
        $pod_paid_invoice_query = $this->db->query($pod_paid_invoice_sql)->getResultArray();

        foreach ($pod_paid_invoice_query as $paid_invoice) {
            $publisher_id = $paid_invoice['publisher_id'];
            if (isset($pod_total_invoices[$publisher_id])) {
                $tmp = $pod_total_invoices[$publisher_id];
                $tmp['paid_amount'] = indian_format($paid_invoice['amt2'], 2);
                $pod_total_invoices[$publisher_id] = $tmp;
            }
        }

        return $pod_total_invoices;
    }

    
    function GetMonthlyTotalInvoice()
    {
        //  Total invoices month-wise
        $monthly_total_invoice_sql = "SELECT 
                                        DATE_FORMAT(invoice_date,'%M %Y') AS month_name,
                                        DATE_FORMAT(invoice_date,'%Y-%m') AS month_order, 
                                        SUM(pod_publisher_books.invoice_value) AS total_amount,
                                        count( pod_publisher_books.invoice_number) as total_invoice
                                    FROM pod_publisher_books
                                    INNER JOIN pod_publisher ON pod_publisher_books.publisher_id = pod_publisher.id
                                    WHERE pod_publisher_books.invoice_flag = 1
                                    GROUP BY YEAR(invoice_date), MONTH(invoice_date)
                                    ORDER BY YEAR(invoice_date) DESC, MONTH(invoice_date) DESC";
        $monthly_total_invoice_query = $this->db->query($monthly_total_invoice_sql)->getResultArray();

        $monthly_total_invoices = [];

        foreach ($monthly_total_invoice_query as $monthly_total) {
            $tmp['month_name']             = $monthly_total['month_name'];
            $tmp['month_order']              = $monthly_total['month_order'];
            $tmp['monthly_total_amount']   = indian_format($monthly_total['total_amount'], 2);
            $tmp['monthly_pending_amount'] = 0;
            $tmp['monthly_paid_amount']    = 0;
            $month_key                     = $monthly_total['month_order']; // use YYYY-MM as key
            $tmp['total_invoice']           =$monthly_total['total_invoice'];
            $tmp['pending_invoice']         =0;
            $tmp['paid_invoice']            =0;
            $monthly_total_invoices[$month_key] = $tmp;
        }

        //  Pending invoices month-wise
        $monthly_pending_invoice_sql = "SELECT 
                                            DATE_FORMAT(invoice_date,'%M %Y') AS month_name,
                                            DATE_FORMAT(invoice_date,'%Y-%m') AS month_order,
                                            SUM(pod_publisher_books.invoice_value) AS pending_amount,
                                            count(pod_publisher_books.invoice_number) as pending_invoice
                                        FROM pod_publisher_books
                                        INNER JOIN pod_publisher ON pod_publisher_books.publisher_id = pod_publisher.id
                                        WHERE pod_publisher_books.invoice_flag=1
                                        AND pod_publisher_books.payment_flag=0 
                                        GROUP BY YEAR(invoice_date), MONTH(invoice_date)
                                        ORDER BY YEAR(invoice_date) DESC, MONTH(invoice_date) DESC";
        $monthly_pending_invoice_query = $this->db->query($monthly_pending_invoice_sql)->getResultArray();

        foreach ($monthly_pending_invoice_query as $monthly_pending) {
            $month_key = $monthly_pending['month_order'];
            if (isset($monthly_total_invoices[$month_key])) {
                $tmp = $monthly_total_invoices[$month_key];
                $tmp['monthly_pending_amount'] = indian_format($monthly_pending['pending_amount'], 2);
                $tmp['pending_invoice']         =$monthly_pending['pending_invoice'];
                $monthly_total_invoices[$month_key] = $tmp;
            }
        }

        //  Paid invoices month-wise
        $monthly_paid_invoice_sql = "SELECT 
                                        DATE_FORMAT(invoice_date,'%M %Y') AS month_name,
                                        DATE_FORMAT(invoice_date,'%Y-%m') AS month_order, 
                                        SUM(pod_publisher_books.invoice_value) AS paid,
                                         count(pod_publisher_books.invoice_number) as paid_invoice
                                    FROM pod_publisher_books
                                    INNER JOIN pod_publisher ON pod_publisher_books.publisher_id = pod_publisher.id
                                    WHERE pod_publisher_books.invoice_flag=1 
                                    AND pod_publisher_books.payment_flag=1  
                                    GROUP BY YEAR(invoice_date), MONTH(invoice_date)
                                    ORDER BY YEAR(invoice_date) DESC, MONTH(invoice_date) DESC";
        $monthly_paid_invoice_query = $this->db->query($monthly_paid_invoice_sql)->getResultArray();

        foreach ($monthly_paid_invoice_query as $monthly_paid) {
            $month_key = $monthly_paid['month_order'];
            if (isset($monthly_total_invoices[$month_key])) {
                $tmp = $monthly_total_invoices[$month_key];
                $tmp['monthly_paid_amount'] = indian_format($monthly_paid['paid'], 2);
                $tmp['paid_invoice']         =$monthly_paid['paid_invoice'];
                $monthly_total_invoices[$month_key] = $tmp;
            }
        }

        return $monthly_total_invoices;
    }

     function getPendingBooksData(){
		$pod_books_not_started_sql = "SELECT pod_publisher.publisher_name, pod_publisher_books.book_id, 
									  pod_publisher_books.book_title, pod_publisher_books.delivery_date, pod_publisher_books.num_copies, pod_publisher_books.total_num_pages 
									  FROM pod_publisher_books, pod_publisher WHERE pod_publisher_books.start_flag=0 
									  and pod_publisher_books.publisher_id = pod_publisher.id";
      	$pod_books_not_started_query = $this->db->query($pod_books_not_started_sql);
		$data['books_not_started'] = $pod_books_not_started_query->getResultArray();
        $data['NotStarted'] = count($data['books_not_started']);


		$pod_books_pending_sql = "SELECT pod_publisher.publisher_name, pod_publisher_books.* FROM pod_publisher_books, pod_publisher 
								  WHERE pod_publisher_books.start_flag = 1
								  and pod_publisher_books.delivery_flag = 0
								  and pod_publisher_books.publisher_id = pod_publisher.id
								  order by pod_publisher_books.delivery_date";
		$pod_books_pending_query = $this->db->query($pod_books_pending_sql);
		$data['books_pending'] = $pod_books_pending_query->getResultArray();
        $data['PendingCount'] = count($data['books_pending']);


		$pod_completed_orders_sql = "SELECT pod_publisher.publisher_name, pod_publisher_books.* FROM pod_publisher_books, pod_publisher 
								  WHERE pod_publisher_books.start_flag = 1
								  and pod_publisher_books.delivery_flag = 1
								  and pod_publisher_books.publisher_id = pod_publisher.id
								  order by pod_publisher_books.delivery_date";
		$pod_completed_orders_query = $this->db->query($pod_completed_orders_sql);
		$data['completed_orders'] = $pod_completed_orders_query->getResultArray();

		$pod_start_flag_sql = "SELECT count(*) as cnt from pod_publisher_books where start_flag=0";
		$pod_start_flag_query = $this->db->query($pod_start_flag_sql);
		$tmp = $pod_start_flag_query->getResultArray();
		$data['start_flag_cnt'] = $tmp[0]['cnt'];

		$pod_files_ready_flag_sql = "SELECT count(*) as cnt from pod_publisher_books where start_flag=1 and files_ready_flag=0";
		$pod_files_ready_flag_query = $this->db->query($pod_files_ready_flag_sql);
		$tmp = $pod_files_ready_flag_query->getResultArray();
		$data['files_ready_flag_cnt'] = $tmp[0]['cnt']; 

        $pod_cover_flag_sql = "SELECT count(*) as cnt from pod_publisher_books where files_ready_flag=1 and cover_flag=0";
		$pod_cover_flag_query = $this->db->query($pod_cover_flag_sql);
		$tmp = $pod_cover_flag_query->getResultArray();
		$data['cover_flag_cnt'] = $tmp[0]['cnt']; 

		$pod_cover_copy_sql = "SELECT sum(num_copies) as copies FROM pod_publisher_books where files_ready_flag=1 and cover_flag=0";
		$pod_cover_copy_query = $this->db->query($pod_cover_copy_sql);
		$tmp = $pod_cover_copy_query->getResultArray();
		$data['cover_copy'] = $tmp[0]['copies']; 

		$pod_content_flag_sql = "SELECT count(*) as cnt from pod_publisher_books where files_ready_flag=1 and content_flag=0";
		$pod_content_flag_query = $this->db->query($pod_content_flag_sql);
		$tmp = $pod_content_flag_query->getResultArray();
		$data['content_flag_cnt'] = $tmp[0]['cnt']; 

        $pod_content_page_sql = "SELECT sum(total_num_pages * num_copies) as pages from pod_publisher_books where files_ready_flag=1 and content_flag=0";
		$pod_content_page_query = $this->db->query($pod_content_page_sql);
		$tmp = $pod_content_page_query->getResultArray();
		$data['content_page'] = $tmp[0]['pages']; 

		$pod_lamination_flag_sql = "SELECT count(*) as cnt from pod_publisher_books where cover_flag=1 and lamination_flag=0";
		$pod_lamination_flag_query = $this->db->query($pod_lamination_flag_sql);
		$tmp = $pod_lamination_flag_query->getResultArray();
		$data['lamination_flag_cnt'] = $tmp[0]['cnt'];  

		$pod_binding_flag_sql = "SELECT count(*) as cnt from pod_publisher_books where cover_flag=1 and content_flag=1 and binding_flag=0";
		$pod_binding_flag_query = $this->db->query($pod_binding_flag_sql);
		$tmp = $pod_binding_flag_query->getResultArray();
		$data['binding_flag_cnt'] = $tmp[0]['cnt'];

		$pod_finalcut_flag_sql = "SELECT count(*) as cnt from pod_publisher_books where binding_flag=1 and finalcut_flag=0";
		$pod_finalcut_flag_query = $this->db->query($pod_finalcut_flag_sql);
		$tmp = $pod_finalcut_flag_query->getResultArray();
		$data['finalcut_flag_cnt'] = $tmp[0]['cnt'];
		
		$pod_qc_flag_sql = "SELECT count(*) as cnt from pod_publisher_books where finalcut_flag=1 and qc_flag=0";
		$pod_qc_flag_query = $this->db->query($pod_qc_flag_sql);
		$tmp = $pod_qc_flag_query->getResultArray();
		$data['qc_flag_cnt'] = $tmp[0]['cnt'];
		 
		$pod_invoice_flag_sql = "SELECT count(*) as cnt from pod_publisher_books where invoice_flag=0";
		$pod_invoice_flag_query = $this->db->query($pod_invoice_flag_sql);
		$tmp = $pod_invoice_flag_query->getResultArray();
		$data['invoice_flag_cnt'] = $tmp[0]['cnt'];

		$pod_packing_flag_sql = "SELECT count(*) as cnt from pod_publisher_books where invoice_flag=1 and packing_flag=0";
		$pod_packing_flag_query = $this->db->query($pod_packing_flag_sql);
		$tmp = $pod_packing_flag_query->getResultArray();
		$data['packing_flag_cnt'] = $tmp[0]['cnt'];
		
		return $data;
	}


        public function markProcess($step, $book_id)
    {
        $db      = \Config\Database::connect();
        $builder = $db->table('pod_publisher_books');

        $flagMap = [
            'start'               => 'start_flag',
            'filesready_complete' => 'files_ready_flag',
            'cover_complete'      => 'cover_flag',
            'content_complete'    => 'content_flag',
            'lamination_complete' => 'lamination_flag',
            'binding_complete'    => 'binding_flag',
            'finalcut_complete'   => 'finalcut_flag',
            'qc_complete'         => 'qc_flag',
            'packing_complete'    => 'packing_flag',
            'delivery_complete'   => 'delivery_flag',
        ];

        if (!array_key_exists($step, $flagMap)) {
            return false;
        }

        $data = [$flagMap[$step] => 1];

        if ($step === 'delivery_complete') {
            $data['actual_delivery_date'] = date('Y-m-d H:i:s');
        }

        return $builder->where('book_id', $book_id)->update($data);
    }

    function getPodWork(){

		$sql="SELECT publisher_name,pod_book_id,book_title FROM pod_indesign where status=0 and start_flag=0";
		$query=$this->db->query($sql);
		$data['book_not_start'] =$query->getResultArray();

		$sql="SELECT pod_indesign.*,language_tbl.language_name
			FROM pod_indesign,language_tbl
			where pod_indesign.language =language_tbl.language_id
			and pod_indesign.status=0 and pod_indesign.start_flag=1";
		$query=$this->db->query($sql);
		$data['status_details'] =$query->getResultArray();

		$sql="SELECT pod_indesign.*,language_tbl.language_name
			FROM pod_indesign,language_tbl
			where pod_indesign.language =language_tbl.language_id
			and pod_indesign.status=1 and pod_indesign.start_flag=1";
		$query=$this->db->query($sql);
		$data['completed'] =$query->getResultArray();

        return $data;
	}
    public function addPodBook()
    {
        $post = service('request')->getPost();

        $publisher = $this->db->table('pod_publisher')
            ->where('id', $post['publisher_id'])
            ->get()->getRowArray();

        $num_pages1 = $post['num_pages_quote1'];
        $num_pages2 = $post['num_pages_quote2'];
        $cost1 = $post['cost_per_page1'];
        $cost2 = $post['cost_per_page2'];

        $book_cost = ($num_pages1 * $cost1) + ($num_pages2 * $cost2) + $post['fixed_charge_book'];
        $invoice_value = ($book_cost * $post['num_copies']) + $post['design_charges'];

        if ($post['transport_charges'] > 1000) {
            $invoice_value += $post['transport_charges'];
        }

        $sub_total = $invoice_value / 1.12;
        $gst = $invoice_value - $sub_total;

        if (!empty($publisher) && $publisher['igst_flag'] == 1) {
            $igst = $gst;
            $sgst = $cgst = 0;
        } else {
            $sgst = $cgst = $gst / 2;
            $igst = 0;
        }

        if ($post['transport_charges'] < 1000) {
            $invoice_value += $post['transport_charges'];
        }

        $data = [
            'publisher_id' => $post['publisher_id'],
            'custom_publisher_name' => $post['custom_publisher_name'],
            'publisher_reference' => $post['publisher_reference'],
            'book_title' => $post['book_title'],
            'total_num_pages' => $post['total_num_pages'],
            'num_copies' => $post['num_copies'],
            'book_size' => $post['book_size'],
            'cover_paper' => $post['cover_paper'],
            'cover_gsm' => $post['cover_gsm'],
            'content_paper' => $post['content_paper'],
            'content_gsm' => $post['content_gsm'],
            'content_colour' => $post['content_colour'],
            'lamination_type' => $post['lamination_type'],
            'binding_type' => $post['binding_type'],
            'content_location' => $post['content_location'],
            'num_pages_quote1' => $num_pages1,
            'cost_per_page1' => $cost1,
            'num_pages_quote2' => $num_pages2,
            'cost_per_page2' => $cost2,
            'fixed_charge_book' => $post['fixed_charge_book'],
            'delivery_date' => $post['delivery_date'],
            'transport_charges' => $post['transport_charges'],
            'design_charges' => $post['design_charges'],
            'remarks' => $post['remarks'],
            'ship_address' => $post['ship_address'],
            'sub_total' => $sub_total,
            'sgst' => $sgst,
            'cgst' => $cgst,
            'igst' => $igst,
            'invoice_value' => $invoice_value
        ];

        return $this->db->table('pod_publisher_books')->insert($data);
    }
    public function editPublisherBookDetails($book_id)
{
    $db = \Config\Database::connect();

    // Get book and publisher details
    $pod_publisher_edit_sql = "
        SELECT * FROM pod_publisher_books 
        JOIN pod_publisher ON pod_publisher.id = pod_publisher_books.publisher_id 
        WHERE pod_publisher_books.book_id = " . (int)$book_id;

    $pod_publisher_edit_query = $db->query($pod_publisher_edit_sql);
    $data['pod_publisher_book'] = $pod_publisher_edit_query->getRowArray();

    // Get all active publishers
    $pod_publisher_sql = "SELECT * FROM pod_publisher WHERE status = 1";
    $pod_publisher_query = $db->query($pod_publisher_sql);
    $data['pod_publishers'] = $pod_publisher_query->getResultArray();

    return $data;
}
// Update book details
    public function editPublisherBook($postData)
    {
        $db = \Config\Database::connect();
        $publisherId = $postData['publisher_id'];
        $bookId      = $postData['book_id'];

        // Fetch publisher
        $podPublisher = $db->table('pod_publisher')->where('id', $publisherId)->get()->getRowArray();

        // Calculate costs
        $book_cost1 = $postData['num_pages_quote1'] * $postData['cost_per_page1']; 
        $book_cost2 = $postData['num_pages_quote2'] * $postData['cost_per_page2'];
        $book_cost  = $book_cost1 + $book_cost2 + $postData['fixed_charge_book'];
        $invoice_value = $book_cost * $postData['num_copies'] + $postData['design_charges'];

        if ($postData['transport_charges'] > 1000) {
            $invoice_value += $postData['transport_charges'];
        }

        $sub_total = $invoice_value / 1.12;
        $gst = $invoice_value - $sub_total;

        if (!empty($podPublisher['igst_flag']) && $podPublisher['igst_flag'] == 1) {
            $igst = $gst; $sgst = 0; $cgst = 0;
        } else {
            $sgst = $gst / 2;
            $cgst = $sgst;
            $igst = 0;
        }

        if ($postData['transport_charges'] < 1000) {
            $invoice_value += $postData['transport_charges'];
        }

        // Prepare update data
        $publisherBookData = [
            "publisher_id" => $publisherId,
            "custom_publisher_name" => $postData['custom_publisher_name'],
            "publisher_reference" => $postData['publisher_reference'],
            "book_title" => $postData['book_title'],
            "total_num_pages" => $postData['total_num_pages'],
            "num_copies" => $postData['num_copies'],
            "book_size" => $postData['book_size'],
            "cover_paper" => $postData['cover_paper'],
            "cover_gsm" => $postData['cover_gsm'],
            "content_paper" => $postData['content_paper'],
            "content_gsm" => $postData['content_gsm'],
            "content_colour" => $postData['content_colour'],
            "lamination_type" => $postData['lamination_type'],
            "binding_type" => $postData['binding_type'],
            "content_location" => $postData['content_location'],
            "num_pages_quote1" => $postData['num_pages_quote1'],
            "cost_per_page1" => $postData['cost_per_page1'],
            "num_pages_quote2" => $postData['num_pages_quote2'],
            "cost_per_page2" => $postData['cost_per_page2'],
            "fixed_charge_book" => $postData['fixed_charge_book'],
            "delivery_date" => $postData['delivery_date'],
            "transport_charges" => $postData['transport_charges'],
            "design_charges" => $postData['design_charges'],
            "remarks" => $postData['remarks'],
            'sub_total' => $sub_total,
            'sgst' => $sgst,
            'cgst' => $cgst,
            'igst' => $igst,
            'invoice_value' => $invoice_value,
            'ship_address' => $postData['ship_address']
        ];

        // Update
        $db->table('pod_publisher_books')->where('book_id', $bookId)->update($publisherBookData);

        return 1;
    }


public function getBooksCompletedData()
    {
        $db = \Config\Database::connect();
        $data = [];

        // Total books
        $query = $db->query("SELECT COUNT(*) as cnt, SUM(num_copies) as copies_cnt FROM pod_publisher_books");
        $tmp = $query->getRowArray();
        $data['total_order_count'] = $tmp['cnt'];
        $data['total_copies_count'] = $tmp['copies_cnt'];

        // Completed books
        $query = $db->query("SELECT COUNT(*) as cnt, SUM(num_copies) as copies_cnt FROM pod_publisher_books WHERE delivery_flag=1");
        $tmp = $query->getRowArray();
        $data['completed_order_count'] = $tmp['cnt'];
        $data['completed_copies_count'] = $tmp['copies_cnt'];

        // Pending books
        $query = $db->query("SELECT COUNT(*) as cnt, SUM(num_copies) as copies_cnt FROM pod_publisher_books WHERE delivery_flag=0");
        $tmp = $query->getRowArray();
        $data['pending_order_count'] = $tmp['cnt'];
        $data['pending_copies_count'] = $tmp['copies_cnt'];

        // Percentages
        $data['completed_order_percentage'] = $data['completed_copies_count'] > 0 
            ? round($data['completed_copies_count'] / ($data['completed_copies_count'] + $data['pending_copies_count']) * 100, 2) 
            : 0;
        $data['pending_order_percentage'] = 100 - $data['completed_order_percentage'];

       $current_year = date('Y');
        $current_month = date('m');

        // Monthly delivery (June onwards, excluding current month)
        $monthly_sql = "
           SELECT DATE_FORMAT(temp.trans_date, '%M %Y') AS month_name, 
									SUM(del_num_bks) AS monthly_planned_delivery_books, 
									SUM(act_del_num_bks) AS monthly_actual_delivery_books,
									SUM(del_num_pgs) AS monthly_planned_delivery_pages,
									SUM(act_del_num_pgs) AS monthly_actual_delivery_pages 
								FROM (
									SELECT delivery_date AS trans_date, 
										num_copies AS del_num_bks, 
										0 AS act_del_num_bks, 
										0 AS del_num_pgs, 
										0 AS act_del_num_pgs
									FROM `pod_publisher_books` 
									WHERE delivery_date >= '$current_year-06-01' AND delivery_flag = 1
									UNION ALL 
									SELECT actual_delivery_date AS trans_date, 
										0 AS del_num_bks, 
										num_copies AS act_del_num_bks, 
										0 AS del_num_pgs, 
										0 AS act_del_num_pgs
									FROM `pod_publisher_books` 
									WHERE actual_delivery_date >= '$current_year-06-01' AND delivery_flag = 1
									UNION ALL 
									SELECT actual_delivery_date AS trans_date, 
										0 AS del_num_bks, 
										0 AS act_del_num_bks, 
										total_num_pages * num_copies AS del_num_pgs, 
										0 AS act_del_num_pgs
									FROM `pod_publisher_books` 
									WHERE delivery_date >= '$current_year-06-01' AND delivery_flag = 1
									UNION ALL 
									SELECT actual_delivery_date AS trans_date, 
										0 AS del_num_bks, 
										0 AS act_del_num_bks, 
										0 AS del_num_pgs, 
										total_num_pages * num_copies AS act_del_num_pgs
									FROM `pod_publisher_books` 
									WHERE actual_delivery_date >= '$current_year-06-01' AND delivery_flag = 1
								) temp 
								GROUP BY month_name 
								ORDER BY temp.trans_date
        ";

        $query = $db->query($monthly_sql);
        $data['monthly_delivery'] = $query->getResultArray();

        // Completed monthly units (sum of copies)
        $builder = $db->table('pod_publisher_books');
        $builder->selectSum('num_copies', 'copies');
        $builder->where('delivery_flag', 1);
        $builder->where('MONTH(actual_delivery_date) = MONTH(CURRENT_DATE())', null, false);
        $query = $builder->get();
        $row = $query->getRowArray();
        $data['completed_orders_monthly_units'] = $row['copies'] ?? 0;

        // Completed monthly orders (count)
        $builder = $db->table('pod_publisher_books');
        $builder->selectCount('*', 'cnt');
        $builder->where('delivery_flag', 1);
        $builder->where('MONTH(actual_delivery_date) = MONTH(CURRENT_DATE())', null, false);
        $query = $builder->get();
        $row = $query->getRowArray();
        $data['completed_orders_monthly'] = $row['cnt'] ?? 0;

        // Daily delivery from first day of month
        $query = $db->query("SELECT LAST_DAY(NOW() - INTERVAL 1 MONTH) + INTERVAL 1 DAY AS first_day");
        $first_day = $query->getRow()->first_day;

        $daily_sql = "SELECT DATE_FORMAT(temp.trans_date, '%d %M %Y') AS daily_name,
                             SUM(act_del_num_bks) AS daily_actual_delivery_books,
                             SUM(act_del_num_pgs) AS daily_actual_delivery_pages
                      FROM (
                          SELECT actual_delivery_date AS trans_date, num_copies AS act_del_num_bks, 0 AS act_del_num_pgs
                          FROM pod_publisher_books WHERE actual_delivery_date >= '$first_day' AND delivery_flag=1
                          UNION ALL
                          SELECT actual_delivery_date, 0, total_num_pages*num_copies
                          FROM pod_publisher_books WHERE actual_delivery_date >= '$first_day' AND delivery_flag=1
                      ) temp
                      GROUP BY daily_name
                      ORDER BY temp.trans_date DESC";
        $query = $db->query($daily_sql);
        $data['daily_delivery'] = $query->getResultArray();

        // Publisher orders summary
        $publisher_sql = "SELECT pod_publisher.id AS publisher_id, pod_publisher.publisher_name, 
                                 SUM(pod_publisher_books.num_copies) AS num_copies, COUNT(*) AS num_orders
                          FROM pod_publisher_books
                          JOIN pod_publisher ON pod_publisher_books.publisher_id = pod_publisher.id
                          GROUP BY pod_publisher.id
                          ORDER BY num_copies DESC";
        $query = $db->query($publisher_sql);
        $publisher_orders = [];
        foreach ($query->getResultArray() as $row) {
            $publisher_orders[$row['publisher_id']] = [
                'publisher_name' => $row['publisher_name'],
                'num_copies_total' => $row['num_copies'],
                'num_orders_total' => $row['num_orders'],
                'num_orders_pending' => 0,
                'num_copies_pending' => 0
            ];
        }

        // Pending orders per publisher
        $pending_sql = "SELECT pod_publisher.id AS publisher_id, pod_publisher.publisher_name, 
                               SUM(pod_publisher_books.num_copies) AS num_copies, COUNT(*) AS num_orders
                        FROM pod_publisher_books
                        JOIN pod_publisher ON pod_publisher_books.publisher_id = pod_publisher.id
                        WHERE pod_publisher_books.delivery_flag = 0
                        GROUP BY pod_publisher.id
                        ORDER BY num_copies DESC";
        $query = $db->query($pending_sql);
        foreach ($query->getResultArray() as $row) {
            $publisher_id = $row['publisher_id'];
            if (isset($publisher_orders[$publisher_id])) {
                $publisher_orders[$publisher_id]['num_orders_pending'] = $row['num_orders'];
                $publisher_orders[$publisher_id]['num_copies_pending'] = $row['num_copies'];
            } else {
                $publisher_orders[$publisher_id] = [
                    'publisher_name' => $row['publisher_name'],
                    'num_copies_total' => 0,
                    'num_orders_total' => 0,
                    'num_orders_pending' => $row['num_orders'],
                    'num_copies_pending' => $row['num_copies'],
                ];
            }
        }
        $data['pod_publisher_orders'] = $publisher_orders;

        // Future orders
        $future_sql = "SELECT pod_publisher.publisher_name, pod_publisher_books.*
                       FROM pod_publisher_books
                       JOIN pod_publisher ON pod_publisher_books.publisher_id = pod_publisher.id
                       WHERE delivery_date > NOW()
                       ORDER BY delivery_date";
        $query = $db->query($future_sql);
        $data['future_orders'] = $query->getResultArray();

        // Publisher names
        $query = $db->query("SELECT publisher_name FROM pod_publisher");
        $publishers = array_map(fn($row) => $row['publisher_name'], $query->getResultArray());
        $data['publishers'] = $publishers;

        // Delivered pages & covers
        $query = $db->query("SELECT book_size, content_paper, SUM(total_num_pages*num_copies) AS total_pages 
                             FROM pod_publisher_books WHERE delivery_flag=1 GROUP BY book_size, content_paper");
        $data['pod_pages_delivered'] = $query->getResultArray();

        $query = $db->query("SELECT book_size, cover_paper, SUM(num_copies) AS total_covers 
                             FROM pod_publisher_books WHERE delivery_flag=1 GROUP BY book_size, cover_paper");
        $data['pod_covers_delivered'] = $query->getResultArray();

        // Pending pages & covers
        $query = $db->query("SELECT book_size, content_paper, SUM(total_num_pages*num_copies) AS total_pages 
                             FROM pod_publisher_books WHERE delivery_flag=0 GROUP BY book_size, content_paper");
        $data['pod_pages_pending'] = $query->getResultArray();

        $query = $db->query("SELECT book_size, cover_paper, SUM(num_copies) AS total_covers 
                             FROM pod_publisher_books WHERE delivery_flag=0 GROUP BY book_size, cover_paper");
        $data['pod_covers_pending'] = $query->getResultArray();

        return $data;
    }
    
public function getMonthDetails($month)
{
    $db = \Config\Database::connect();

    $sql = "SELECT book_title, num_copies, actual_delivery_date
            FROM pod_publisher_books
            WHERE DATE_FORMAT(actual_delivery_date, '%M %Y') = ?";
    $query = $db->query($sql, [$month]);

    return $query->getResultArray();
}
public function getPODPublisherBookDetails($book_id)
{
    return $this->db->table('pod_publisher_books')
                    ->where('book_id', $book_id)
                    ->get()
                    ->getRowArray();
}

public function getPODPublisherDetails($publisher_id)
{
    return $this->db->table('pod_publisher')
                    ->where('id', $publisher_id)
                    ->get()
                    ->getRowArray();
}
public function createInvoice($book_id, $invoice_number)
{
    $db = \Config\Database::connect();

    $builder = $db->table('pod_publisher_books');

    $builder->set('invoice_flag', 1);
    $builder->set('invoice_number', $invoice_number);
    $builder->set('invoice_date', date('Y-m-d H:i:s')); // current timestamp
    $builder->where('book_id', $book_id);
    $builder->update();

    if ($db->affectedRows() > 0) {
        return 1;
    } else {
        return 0;
    }
}
 // Get pending invoices grouped by publisher
    public function getPendingInvoices()
    {
        return $this->db->table('pod_publisher_books pb')
            ->select('pb.publisher_id, p.publisher_name, SUM(pb.invoice_value) as total_invoice_value, COUNT(pb.book_id) as total_books')
            ->join('pod_publisher p', 'p.id = pb.publisher_id', 'left')
            ->where('pb.invoice_flag', 0)
            ->groupBy('pb.publisher_id, p.publisher_name')
            ->get()
            ->getResultArray();
    }

    public function getRaisedInvoices()
{
    $builder = $this->db->table('pod_publisher_books b')
        ->select('b.publisher_id, p.publisher_name, COUNT(b.book_id) as total_books, SUM(b.invoice_value) as total_invoice_value')
        ->join('pod_publisher p', 'p.id = b.publisher_id')
        ->where('b.invoice_flag', 1)
        ->where('b.payment_flag', 0)
        ->groupBy('b.publisher_id');

    return $builder->get()->getResultArray();
}
    // Get details for a specific publisher
    public function getPendingInvoicesDetails($publisher_id)
    {
        return $this->db->table('pod_publisher_books pb')
            ->select('pb.*, p.publisher_name')
            ->join('pod_publisher p', 'p.id = pb.publisher_id', 'left')
            ->where('pb.invoice_flag', 0)
            ->where('pb.publisher_id', $publisher_id)
            ->get()
            ->getResultArray();
    }
        // Get books for raised invoices
        public function getRaisedInvoiceBooks($publisher_id)
        {
            return $this->db->table('pod_publisher_books')
                ->select('book_id, book_title, num_copies, invoice_value, invoice_number, invoice_date')
                ->where('publisher_id', $publisher_id)
                ->where('invoice_flag', 1)
                ->where('payment_flag', 0)
                ->get()
                ->getResultArray();
        }
        // Get all paid invoices for a publisher
        public function getPaidInvoices()
        {
            $builder = $this->db->table('pod_publisher_books b')
            ->select('b.publisher_id, p.publisher_name, COUNT(b.book_id) as total_books, SUM(b.invoice_value) as total_invoice_value')
            ->join('pod_publisher p', 'p.id = b.publisher_id')
            ->where('b.invoice_flag', 1)
            ->where('b.payment_flag', 1)
            ->groupBy('b.publisher_id');

        return $builder->get()->getResultArray();
}
public function getPaidInvoiceBooks($publisher_id)
        {
            return $this->db->table('pod_publisher_books')
                ->select('book_id, book_title, num_copies, invoice_value, invoice_number, invoice_date, payment_date')
                ->where('publisher_id', $publisher_id)
                ->where('invoice_flag', 1)
                ->where('payment_flag', 1)
                ->get()
                ->getResultArray();
        }
    public function insertPodWork()
    {
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

        $builder = $this->db->table('pod_indesign');
        $builder->insert($data);

        if ($this->db->affectedRows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }
   public function updatePodColumn($book_id, $column)
{
    $tableName = 'pod_indesign';

    log_message('debug', "Updating POD column {$column} for book_id {$book_id}");

    $builder = $this->db->table($tableName);

    // Add condition
    $builder->where('pod_book_id', $book_id);

    // Prepare update data
    $updateData = [
        $column => 1
    ];

    // If file_upload → update date + status
    if ($column === 'file_upload') {
        $updateData['completed_date'] = date('Y-m-d H:i:s');
        $updateData['status'] = 1;   // <-- Correct way to add status
    }

    // Run update
    $builder->update($updateData);

    return $this->db->affectedRows();
}



public function getRaisedInvoicesData()
{
    $db = \Config\Database::connect();

    $sql = "SELECT p.publisher_name, p.igst_flag, b.*
            FROM pod_publisher_books b
            JOIN pod_publisher p ON b.publisher_id = p.id
            WHERE b.invoice_flag = 1 
              AND b.payment_flag = 0";

    $query = $db->query($sql);
    return $query->getResultArray();  // CI4 equivalent of result_array()
}

public function mark_payment($book_id)
{
    $db = \Config\Database::connect();
    $builder = $db->table('pod_publisher_books');

    $builder->set('payment_flag', 1);
    $builder->set('payment_date', 'NOW()', false);
    $builder->where('book_id', $book_id);
    $builder->update();

    return ($db->affectedRows() > 0) ? 1 : 0;

}

  public function getDashboardData()
{
    $data = [];

    // Publisher status - simple counts
    $sql1 = "
        SELECT 
            COUNT(id) AS total_podpublisher,
            COUNT(CASE WHEN status = '1' THEN id END) AS active_podpublisher,
            COUNT(CASE WHEN status = '0' THEN id END) AS pending_podpublisher,
            COUNT(CASE WHEN status = '2' THEN id END) AS inactive_podpublisher
        FROM pod_publisher";
    $data['publisher_status'] = $this->db->query($sql1)->getRowArray();

    // Optimized: pod_order summary
    $sql2 = "
                SELECT
                SUM(num_copies_today) AS date_quantity,
                SUM(num_copies_week) AS week_quantity,
                SUM(num_copies_month) AS month_quantity,
                SUM(num_copies_prev_month) AS prev_month,

                SUM(invoice_today) AS date_price,
                SUM(invoice_week) AS week_price,
                SUM(invoice_month) AS month_price,
                SUM(invoice_prev_month) AS prev_month_price,

                SUM(orders_today) AS date_orders,
                SUM(orders_week) AS week_orders,
                SUM(orders_month) AS month_orders,
                SUM(orders_prev_month) AS prev_month_orders

            FROM (
                SELECT
                    -- Copies
                    CASE WHEN DATE(delivery_date) = CURDATE() THEN num_copies ELSE 0 END AS num_copies_today,
                    CASE WHEN YEAR(delivery_date) = YEAR(CURDATE()) AND WEEK(delivery_date) = WEEK(CURDATE()) THEN num_copies ELSE 0 END AS num_copies_week,
                    CASE WHEN YEAR(delivery_date) = YEAR(CURDATE()) AND MONTH(delivery_date) = MONTH(CURDATE()) THEN num_copies ELSE 0 END AS num_copies_month,
                    CASE WHEN YEAR(delivery_date) = YEAR(CURDATE() - INTERVAL 1 MONTH)
                        AND MONTH(delivery_date) = MONTH(CURDATE() - INTERVAL 1 MONTH)
                        THEN num_copies ELSE 0 END AS num_copies_prev_month,

                    -- Price
                    CASE WHEN DATE(delivery_date) = CURDATE() THEN invoice_value ELSE 0 END AS invoice_today,
                    CASE WHEN YEAR(delivery_date) = YEAR(CURDATE()) AND WEEK(delivery_date) = WEEK(CURDATE()) THEN invoice_value ELSE 0 END AS invoice_week,
                    CASE WHEN YEAR(delivery_date) = YEAR(CURDATE()) AND MONTH(delivery_date) = MONTH(CURDATE()) THEN invoice_value ELSE 0 END AS invoice_month,
                    CASE WHEN YEAR(delivery_date) = YEAR(CURDATE() - INTERVAL 1 MONTH)
                        AND MONTH(delivery_date) = MONTH(CURDATE() - INTERVAL 1 MONTH)
                        THEN invoice_value ELSE 0 END AS invoice_prev_month,

                    -- Orders Count
                    CASE WHEN DATE(delivery_date) = CURDATE() THEN 1 ELSE 0 END AS orders_today,
                    CASE WHEN YEAR(delivery_date) = YEAR(CURDATE()) AND WEEK(delivery_date) = WEEK(CURDATE()) THEN 1 ELSE 0 END AS orders_week,
                    CASE WHEN YEAR(delivery_date) = YEAR(CURDATE()) AND MONTH(delivery_date) = MONTH(CURDATE()) THEN 1 ELSE 0 END AS orders_month,
                    CASE WHEN YEAR(delivery_date) = YEAR(CURDATE() - INTERVAL 1 MONTH)
                        AND MONTH(delivery_date) = MONTH(CURDATE() - INTERVAL 1 MONTH)
                        THEN 1 ELSE 0 END AS orders_prev_month

                FROM pod_publisher_books
                WHERE delivery_flag = 1
            ) AS filtered_orders";
    $data['pod_order'] = $this->db->query($sql2)->getRowArray();

    // Optimized: author_order summary
    $sql3 = "
       SELECT
                -- Quantities
                SUM(qty_today) AS date_quantity,
                SUM(qty_week) AS week_quantity,
                SUM(qty_month) AS month_quantity,
                SUM(qty_prev_month) AS prev_month,

                -- Prices
                SUM(price_today) AS date_price,
                SUM(price_week) AS week_price,
                SUM(price_month) AS month_price,
                SUM(price_prev_month) AS prev_month_price,

                -- ORDER COUNTS (DISTINCT order_id)
                COUNT(DISTINCT CASE 
                    WHEN DATE(ship_date) = CURDATE() THEN order_id 
                END) AS today_orders,

                COUNT(DISTINCT CASE 
                    WHEN YEAR(ship_date) = YEAR(CURDATE()) 
                    AND WEEK(ship_date) = WEEK(CURDATE()) THEN order_id 
                END) AS week_orders,

                COUNT(DISTINCT CASE 
                    WHEN YEAR(ship_date) = YEAR(CURDATE()) 
                    AND MONTH(ship_date) = MONTH(CURDATE()) THEN order_id 
                END) AS month_orders,

                COUNT(DISTINCT CASE 
                    WHEN YEAR(ship_date) = YEAR(CURDATE() - INTERVAL 1 MONTH)
                    AND MONTH(ship_date) = MONTH(CURDATE() - INTERVAL 1 MONTH) THEN order_id 
                END) AS prev_month_orders,

                -- TITLE COUNTS (each row = 1 title)
                COUNT(CASE 
                    WHEN DATE(ship_date) = CURDATE() THEN 1 
                END) AS today_titles,

                COUNT(CASE 
                    WHEN YEAR(ship_date) = YEAR(CURDATE()) 
                    AND WEEK(ship_date) = WEEK(CURDATE()) THEN 1 
                END) AS week_titles,

                COUNT(CASE 
                    WHEN YEAR(ship_date) = YEAR(CURDATE()) 
                    AND MONTH(ship_date) = MONTH(CURDATE()) THEN 1 
                END) AS month_titles,

                COUNT(CASE 
                    WHEN YEAR(ship_date) = YEAR(CURDATE() - INTERVAL 1 MONTH)
                    AND MONTH(ship_date) = MONTH(CURDATE() - INTERVAL 1 MONTH) THEN 1 
                END) AS prev_month_titles

            FROM (
                SELECT
                    order_id,
                    ship_date,
                    quantity,
                    price,

                    -- Quantities
                    CASE WHEN DATE(ship_date) = CURDATE() THEN quantity ELSE 0 END AS qty_today,
                    CASE WHEN YEAR(ship_date) = YEAR(CURDATE()) AND WEEK(ship_date) = WEEK(CURDATE()) THEN quantity ELSE 0 END AS qty_week,
                    CASE WHEN YEAR(ship_date) = YEAR(CURDATE()) AND MONTH(ship_date) = MONTH(CURDATE()) THEN quantity ELSE 0 END AS qty_month,
                    CASE WHEN YEAR(ship_date) = YEAR(CURDATE() - INTERVAL 1 MONTH) 
                        AND MONTH(ship_date) = MONTH(CURDATE() - INTERVAL 1 MONTH) THEN quantity ELSE 0 END AS qty_prev_month,

                    -- Prices
                    CASE WHEN DATE(ship_date) = CURDATE() THEN price ELSE 0 END AS price_today,
                    CASE WHEN YEAR(ship_date) = YEAR(CURDATE()) AND WEEK(ship_date) = WEEK(CURDATE()) THEN price ELSE 0 END AS price_week,
                    CASE WHEN YEAR(ship_date) = YEAR(CURDATE()) AND MONTH(ship_date) = MONTH(CURDATE()) THEN price ELSE 0 END AS price_month,
                    CASE WHEN YEAR(ship_date) = YEAR(CURDATE() - INTERVAL 1 MONTH) 
                        AND MONTH(ship_date) = MONTH(CURDATE() - INTERVAL 1 MONTH) THEN price ELSE 0 END AS price_prev_month

                FROM pod_author_order_details
                WHERE status = 1
            ) AS filtered_shipments";
    $data['author_order'] = $this->db->query($sql3)->getRowArray();

    return $data;
    }

    public function getOrderDashboardData()
    {
        $data = [];

        $queries = [
            'online' => "
                SELECT 
                        SUM( CASE   WHEN DATE(t.ship_date) = CURDATE() THEN t.order_total - t.discount ELSE 0 END) AS today_amount,
                        SUM( CASE  WHEN YEAR(t.ship_date) = YEAR(CURDATE())  AND WEEK(t.ship_date) = WEEK(CURDATE()) THEN t.order_total - t.discount ELSE 0 END) AS week_amount,
                        SUM(
                        CASE 
                                WHEN YEAR(t.ship_date) = YEAR(CURDATE())
                                AND MONTH(t.ship_date) = MONTH(CURDATE())
                                THEN t.order_total - t.discount
                                ELSE 0
                            END
                        ) AS month_amount,

                        -- Previous Month Amount
                        SUM(
                            CASE 
                                WHEN YEAR(t.ship_date) = YEAR(CURDATE() - INTERVAL 1 MONTH)
                                AND MONTH(t.ship_date) = MONTH(CURDATE() - INTERVAL 1 MONTH)
                                THEN t.order_total - t.discount
                                ELSE 0
                            END
                        ) AS prev_month_amount,

                        -- Today Quantity
                        SUM(
                            CASE 
                                WHEN DATE(t.ship_date) = CURDATE() 
                                THEN t.total_quantity
                                ELSE 0
                            END
                        ) AS date_quantity,

                        -- Week Quantity
                        SUM(
                            CASE 
                                WHEN YEAR(t.ship_date) = YEAR(CURDATE())
                                AND WEEK(t.ship_date) = WEEK(CURDATE())
                                THEN t.total_quantity
                                ELSE 0
                            END
                        ) AS week_quantity,

                        -- Month Quantity
                        SUM(
                            CASE 
                                WHEN YEAR(t.ship_date) = YEAR(CURDATE())
                                AND MONTH(t.ship_date) = MONTH(CURDATE())
                                THEN t.total_quantity
                                ELSE 0
                            END
                        ) AS month_quantity,

                        -- Previous Month Quantity
                        SUM(
                            CASE 
                                WHEN YEAR(t.ship_date) = YEAR(CURDATE() - INTERVAL 1 MONTH)
                                AND MONTH(t.ship_date) = MONTH(CURDATE() - INTERVAL 1 MONTH)
                                THEN t.total_quantity
                                ELSE 0
                            END
                        ) AS prev_month_quantity


                    FROM (
                        SELECT 
                            pod.order_id,
                            MAX(pod_order_details.ship_date) AS ship_date,
                            SUM(pod_order_details.quantity * pod_order_details.price) AS order_total,
                            SUM(pod_order_details.quantity) AS total_quantity,
                            pod.discount
                        FROM pod_order_details
                        JOIN pod_order pod 
                            ON pod_order_details.order_id = pod.order_id
                        WHERE pod_order_details.status = 1 
                        AND pod_order_details.user_id IS NOT NULL   
                        GROUP BY pod.order_id
                    ) t
            ",

            'offline' => "
                SELECT 
                    SUM(CASE WHEN DATE(pod.ship_date) = CURDATE() THEN pod.quantity ELSE 0 END) AS date_quantity,
                    SUM(CASE WHEN YEAR(pod.ship_date) = YEAR(CURDATE()) AND WEEK(pod.ship_date) = WEEK(CURDATE()) THEN pod.quantity ELSE 0 END) AS week_quantity,
                    SUM(CASE WHEN YEAR(pod.ship_date) = YEAR(CURDATE()) AND MONTH(pod.ship_date) = MONTH(CURDATE()) THEN pod.quantity ELSE 0 END) AS month_quantity,
                    SUM(CASE WHEN YEAR(pod.ship_date) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(pod.ship_date) = MONTH(CURDATE() - INTERVAL 1 MONTH) THEN pod.quantity ELSE 0 END) AS prev_month,
                    SUM(CASE WHEN DATE(pod.ship_date) = CURDATE() THEN pod.quantity * b.paper_back_inr ELSE 0 END) AS date_total,
                    SUM(CASE WHEN YEAR(pod.ship_date) = YEAR(CURDATE()) AND WEEK(pod.ship_date) = WEEK(CURDATE()) THEN pod.quantity * b.paper_back_inr ELSE 0 END) AS week_total,
                    SUM(CASE WHEN YEAR(pod.ship_date) = YEAR(CURDATE()) AND MONTH(pod.ship_date) = MONTH(CURDATE()) THEN pod.quantity * b.paper_back_inr ELSE 0 END) AS month_total,
                    SUM(CASE WHEN YEAR(pod.ship_date) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(pod.ship_date) = MONTH(CURDATE() - INTERVAL 1 MONTH) THEN pod.quantity * b.paper_back_inr ELSE 0 END) AS prev_month_total
                FROM pustaka_offline_orders_details pod
                JOIN book_tbl b ON pod.book_id = b.book_id
                WHERE pod.ship_status = 1
            ",

            'amazon' => "
                SELECT 
                    SUM(CASE WHEN DATE(ship_date) = CURDATE() THEN quantity ELSE 0 END) AS date_quantity,
                    SUM(CASE WHEN YEAR(ship_date) = YEAR(CURDATE()) AND WEEK(ship_date) = WEEK(CURDATE()) THEN quantity ELSE 0 END) AS week_quantity,
                    SUM(CASE WHEN YEAR(ship_date) = YEAR(CURDATE()) AND MONTH(ship_date) = MONTH(CURDATE()) THEN quantity ELSE 0 END) AS month_quantity,
                    SUM(CASE WHEN YEAR(ship_date) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(ship_date) = MONTH(CURDATE() - INTERVAL 1 MONTH) THEN quantity ELSE 0 END) AS prev_month,
                    SUM(CASE WHEN DATE(ship_date) = CURDATE() THEN quantity * bt.paper_back_inr ELSE 0 END) AS date_total,
                    SUM(CASE WHEN YEAR(ship_date) = YEAR(CURDATE()) AND WEEK(ship_date) = WEEK(CURDATE()) THEN quantity * bt.paper_back_inr ELSE 0 END) AS week_total,
                    SUM(CASE WHEN YEAR(ship_date) = YEAR(CURDATE()) AND MONTH(ship_date) = MONTH(CURDATE()) THEN quantity * bt.paper_back_inr ELSE 0 END) AS month_total,
                    SUM(CASE WHEN YEAR(ship_date) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(ship_date) = MONTH(CURDATE() - INTERVAL 1 MONTH) THEN quantity * bt.paper_back_inr ELSE 0 END) AS prev_month_total
                FROM amazon_paperback_orders
                JOIN book_tbl bt ON amazon_paperback_orders.book_id = bt.book_id
                WHERE ship_status = 1
            ",

            'flipkart' => "
                SELECT 
                    SUM(CASE WHEN DATE(ship_date) = CURDATE() THEN quantity ELSE 0 END) AS date_quantity,
                    SUM(CASE WHEN YEAR(ship_date) = YEAR(CURDATE()) AND WEEK(ship_date) = WEEK(CURDATE()) THEN quantity ELSE 0 END) AS week_quantity,
                    SUM(CASE WHEN YEAR(ship_date) = YEAR(CURDATE()) AND MONTH(ship_date) = MONTH(CURDATE()) THEN quantity ELSE 0 END) AS month_quantity,
                    SUM(CASE WHEN YEAR(ship_date) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(ship_date) = MONTH(CURDATE() - INTERVAL 1 MONTH) THEN quantity ELSE 0 END) AS prev_month,
                    SUM(CASE WHEN DATE(ship_date) = CURDATE() THEN quantity * bt.paper_back_inr ELSE 0 END) AS date_total,
                    SUM(CASE WHEN YEAR(ship_date) = YEAR(CURDATE()) AND WEEK(ship_date) = WEEK(CURDATE()) THEN quantity * bt.paper_back_inr ELSE 0 END) AS week_total,
                    SUM(CASE WHEN YEAR(ship_date) = YEAR(CURDATE()) AND MONTH(ship_date) = MONTH(CURDATE()) THEN quantity * bt.paper_back_inr ELSE 0 END) AS month_total,
                    SUM(CASE WHEN YEAR(ship_date) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(ship_date) = MONTH(CURDATE() - INTERVAL 1 MONTH) THEN quantity * bt.paper_back_inr ELSE 0 END) AS prev_month_total
                FROM flipkart_paperback_orders
                JOIN book_tbl bt ON flipkart_paperback_orders.book_id = bt.book_id
                WHERE ship_status = 1
            ",

            'author' => "
                SELECT 
                    SUM(CASE WHEN DATE(order_date) = CURDATE() THEN book_final_royalty_value_inr ELSE 0 END) AS date_royalty,
                    SUM(CASE WHEN YEAR(order_date) = YEAR(CURDATE()) AND WEEK(order_date) = WEEK(CURDATE()) THEN book_final_royalty_value_inr ELSE 0 END) AS week_royalty,
                    SUM(CASE WHEN YEAR(order_date) = YEAR(CURDATE()) AND MONTH(order_date) = MONTH(CURDATE()) THEN book_final_royalty_value_inr ELSE 0 END) AS month_royalty,
                    SUM(CASE WHEN YEAR(order_date) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(order_date) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) THEN book_final_royalty_value_inr ELSE 0 END) AS prev_month_royalty
                FROM author_transaction
                WHERE order_type=15 and pay_status='O'
            ",

            'bookshop' => "
                SELECT 
                    SUM(CASE WHEN DATE(pod.shipped_date) = CURDATE() THEN pod.quantity ELSE 0 END) AS date_quantity,
                    SUM(CASE WHEN YEAR(pod.shipped_date) = YEAR(CURDATE()) AND WEEK(pod.shipped_date) = WEEK(CURDATE()) THEN pod.quantity ELSE 0 END) AS week_quantity,
                    SUM(CASE WHEN YEAR(pod.shipped_date) = YEAR(CURDATE()) AND MONTH(pod.shipped_date) = MONTH(CURDATE()) THEN pod.quantity ELSE 0 END) AS month_quantity,
                    SUM(CASE WHEN YEAR(pod.shipped_date) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(pod.shipped_date) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) THEN pod.quantity ELSE 0 END) AS prev_month,
                    SUM(CASE WHEN DATE(pod.shipped_date) = CURDATE() THEN pod.quantity * b.paper_back_inr ELSE 0 END) AS date_total,
                    SUM(CASE WHEN YEAR(pod.shipped_date) = YEAR(CURDATE()) AND WEEK(pod.shipped_date) = WEEK(CURDATE()) THEN pod.quantity * b.paper_back_inr ELSE 0 END) AS week_total,
                    SUM(CASE WHEN YEAR(pod.shipped_date) = YEAR(CURDATE()) AND MONTH(pod.shipped_date) = MONTH(CURDATE()) THEN pod.quantity * b.paper_back_inr ELSE 0 END) AS month_total,
                    SUM(CASE WHEN YEAR(pod.shipped_date) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(pod.shipped_date) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) THEN pod.quantity * b.paper_back_inr ELSE 0 END) AS prev_month_total
                FROM pod_bookshop_order_details pod
                JOIN book_tbl b ON pod.book_id = b.book_id
                WHERE pod.ship_status = 1
            ",

            'stock'=> "
                SELECT 
                    COUNT(DISTINCT CASE  WHEN DATE(transaction_date) = CURDATE()  THEN book_id END) AS today_count,
                    SUM(CASE WHEN DATE(transaction_date) = CURDATE()  THEN stock_in  ELSE 0 END) AS today_stock_in,
                    COUNT(DISTINCT CASE WHEN YEAR(transaction_date) = YEAR(CURDATE()) AND WEEK(transaction_date) = WEEK(CURDATE()) THEN book_id END) AS week_count,
                    SUM(CASE WHEN YEAR(transaction_date) = YEAR(CURDATE()) AND WEEK(transaction_date) = WEEK(CURDATE()) THEN stock_in ELSE 0 END) AS week_stock_in,
                    COUNT(DISTINCT CASE WHEN YEAR(transaction_date) = YEAR(CURDATE())  AND MONTH(transaction_date) = MONTH(CURDATE()) THEN book_id  END) AS month_count,
                    SUM(CASE WHEN YEAR(transaction_date) = YEAR(CURDATE()) AND MONTH(transaction_date) = MONTH(CURDATE()) THEN stock_in  ELSE 0  END) AS month_stock_in,
                    COUNT(DISTINCT CASE WHEN YEAR(transaction_date) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(transaction_date) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) THEN book_id END) AS previous_month_count,
                    SUM(CASE  WHEN YEAR(transaction_date) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(transaction_date) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) THEN stock_in ELSE 0 END) AS previous_month_stock_in
                FROM pustaka_paperback_stock_ledger
                WHERE channel_type = 'STK'"
        ];

       foreach ($queries as $key => $sql) {
            $query  = $this->db->query($sql);
            $result = $query->getResultArray();
            $data[$key] = !empty($result) ? $result[0] : null;
        }

        return $data;

    }

    public function getInvoiceDetailsByMonth($month, $type)
    {
        $sql = "SELECT 
                    DATE_FORMAT(invoice_date, '%M %Y') AS month_name,
                    pod_publisher.publisher_name,
                    pod_publisher_books.book_title,
                    pod_publisher_books.invoice_number,
                    pod_publisher_books.invoice_value AS amount,
                    pod_publisher_books.payment_flag,
                    pod_publisher_books.invoice_date,
                    pod_publisher_books.total_num_pages,
                    pod_publisher_books.num_copies
                FROM pod_publisher_books
                INNER JOIN pod_publisher 
                    ON pod_publisher_books.publisher_id = pod_publisher.id
                WHERE pod_publisher_books.invoice_flag = 1
                AND DATE_FORMAT(pod_publisher_books.invoice_date, '%Y-%m') = ?";

        if ($type == "pending") {
            $sql .= " AND pod_publisher_books.payment_flag = 0";
        }

        if ($type == "paid") {
            $sql .= " AND pod_publisher_books.payment_flag = 1";
        }

        $sql .= " ORDER BY invoice_date ASC";

        return $this->db->query($sql, [$month])->getResultArray();
    }

}
