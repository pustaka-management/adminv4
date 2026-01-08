<?php

namespace App\Models;

use CodeIgniter\Model;

class ProspectiveManagementModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }
       public function saveProspectData($data)
    {
        $this->db->table('prospectors_details')->insert($data);
        return $this->db->insertID(); 
    }

    public function saveProspectRemark($remarkData)
    {
        return $this->db->table('prospectors_remark_details')->insert($remarkData);
    }
    public function getAllProspects()
    {
        return $this->db->table('prospectors_details')
                        ->where('prospectors_status', 0) 
                        ->orderBy('id', 'DESC')
                        ->get()
                        ->getResultArray();
    }
    // Get one prospect by ID (fetch only)
    public function getProspectById($id)
    {
        return $this->db->table('prospectors_details')
                        ->where('id', $id)
                        ->get()
                        ->getRowArray();
    }
        public function getProspectPlanById($id)
    {
        return $this->db->table('prospectors_details p')
            ->select('p.*, pp.cost AS plan_cost')
            ->join('publishing_plan_details pp', 'pp.plan_name = p.recommended_plan', 'left')
            ->where('p.id', $id)
            ->get()
            ->getRowArray();
    }
    public function updateProspectFromPost($id, $request)
{
    $db      = \Config\Database::connect();
    $builder = $db->table('prospectors_details');
    $old     = $builder->where('id', $id)->get()->getRowArray();

    if (!$old) return false;

    // INPUT VALUES
   
    $emailFlag = $request->getPost('email_sent_flag');
    $emailDate = trim($request->getPost('email_sent_date'));

    $callFlag  = $request->getPost('initial_call_flag');
    $callDate  = trim($request->getPost('initial_call_date'));

   
    // EMAIL DATE LOGIC
   
    if ($emailFlag == 1) {
        // If date entered → use it
        // If no date entered → keep OLD date
        $finalEmailDate = $emailDate !== "" ? $emailDate : $old['email_sent_date'];
    } else {
        // If flag = NO → clear date
        $finalEmailDate = null;
    }

   
    // CALL DATE LOGIC
   
    if ($callFlag == 1) {
        $finalCallDate = $callDate !== "" ? $callDate : $old['initial_call_date'];
    } else {
        $finalCallDate = null;
    }

   
    // DATA BUILD
   
    $data = [
        'name'                => $request->getPost('name'),
        'phone'               => $request->getPost('phone'),
        'email'               => $request->getPost('email'),
        'source_of_reference' => $request->getPost('source_of_reference'),
        'author_status'       => $request->getPost('author_status'),
        'recommended_plan'    => $request->getPost('recommended_plan'),

        'email_sent_flag'     => $emailFlag,
        'email_sent_date'     => $finalEmailDate,

        'initial_call_flag'   => $callFlag,
        'initial_call_date'   => $finalCallDate,

        'last_update_date'    => date('Y-m-d H:i:s'),
    ];

   
    // UPDATE ONLY CHANGED VALUES
   
    $changes = [];
    foreach ($data as $key => $newValue) {
        $oldValue = $old[$key] ?? null;

        // Convert to string for comparison
        if ((string)$oldValue !== (string)$newValue) {
            $changes[$key] = $newValue;
        }
    }

    // If no changes → do nothing
    if (empty($changes)) {
        return 0;
    }

    // Perform update
    $builder->where('id', $id)->update($changes);

   
    // REMARK LOGIC
   
    $remarks = trim($request->getPost('remarks'));
    $finalRemark = "";
    $createDate = null;

    // Plan change?
    if ($old['recommended_plan'] != $data['recommended_plan']) {
        $planChange = "Plan: {$old['recommended_plan']} → {$data['recommended_plan']}";
        $finalRemark = $remarks ? ($remarks . " | " . $planChange) : $planChange;
        $createDate = date('Y-m-d H:i:s');
    }
    elseif (!empty($remarks)) {
        $finalRemark = $remarks;
        $createDate = date('Y-m-d H:i:s');
    }

    if (!empty($finalRemark)) {
        $db->table('prospectors_remark_details')->insert([
            'prospectors_id' => $id,
            'title'          => $old['title'] ?? null,
            'remarks'        => $finalRemark,
            'create_date'    => $createDate,
            'created_by'     => session()->get('username') ?? 'System',
        ]);
    }

    return true;
}
  public function updateInprogressFromPost($id, $request)
{
    $db      = \Config\Database::connect();
    $builder = $db->table('prospectors_details');

    $old = $builder->where('id', $id)->get()->getRowArray();
    if (!$old) return false;

    $postedStatus = $request->getPost('prospectors_status');
    $prospectors_status = 0;
    if ($postedStatus == 1) $prospectors_status = 1;
    elseif ($postedStatus == 2) $prospectors_status = 2;
    elseif ($postedStatus == 3) $prospectors_status = 3; // Hold

    // Email & Call Dates
    $email_sent_flag   = $request->getPost('email_sent_flag');
    $posted_email_date = $request->getPost('email_sent_date');
    $final_email_date  = ($email_sent_flag == "1") ? $posted_email_date : $old['email_sent_date'];

    $initial_call_flag = $request->getPost('initial_call_flag');
    $posted_call_date  = $request->getPost('initial_call_date');
    $final_call_date   = ($initial_call_flag == "1") ? $posted_call_date : $old['initial_call_date'];

    $data = [
        'name'                => $request->getPost('name'),
        'phone'               => $request->getPost('phone'),
        'email'               => $request->getPost('email'),
        'source_of_reference' => $request->getPost('source_of_reference'),
        'author_status'       => $request->getPost('author_status'),
        'recommended_plan'    => $request->getPost('recommended_plan'),
        'email_sent_flag'     => $email_sent_flag,
        'initial_call_flag'   => $initial_call_flag,
        'email_sent_date'     => $final_email_date,
        'initial_call_date'   => $final_call_date,
        'prospectors_status'  => $prospectors_status,
        'last_update_date'    => date('Y-m-d H:i:s'),
    ];

    // Detect changes
    $hasChanges = false;
    foreach ($data as $key => $value) {
        if ((string)($old[$key] ?? '') !== (string)$value) {
            $hasChanges = true;
            break;
        }
    }

    if ($hasChanges) $builder->where('id', $id)->update($data);

    // Remark Logic
    $remarks = trim($request->getPost('remarks'));
    $finalRemark = '';
    $createDate = null;

    if ($prospectors_status == 3) {
        $finalRemark = !empty($remarks) ? $remarks . " | Prospect put on Hold" : "Prospect put on Hold";
        $createDate  = date('Y-m-d H:i:s');
    } elseif ($old['recommended_plan'] != $data['recommended_plan']) {
        $planChangeText = "Plan changed: {$old['recommended_plan']} → {$data['recommended_plan']}";
        $finalRemark = $remarks ? $remarks . " | " . $planChangeText : $planChangeText;
        $createDate  = date('Y-m-d H:i:s');
    } elseif (!empty($remarks)) {
        $finalRemark = $remarks;
        $createDate  = date('Y-m-d H:i:s');
    }

    if (!empty($finalRemark)) {
        $db->table('prospectors_remark_details')->insert([
            'prospectors_id' => $id,
            'title'          => $old['title'] ?? null,
            'remarks'        => $finalRemark,
            'create_date'    => $createDate,
            'created_by'     => session()->get('username') ?? 'System',
        ]);
    }

    return $hasChanges;
}

    public function updateProspectStatus($id, $status)
    {
        return $this->db->table('prospectors_details')
                        ->where('id', $id)
                        ->update(['prospectors_status' => $status, 'last_update_date' => date('Y-m-d H:i:s')]);
    }
    public function getProspectsByStatus($status)
    {
        return $this->db->table('prospectors_details')
                        ->where('prospectors_status', $status)
                        ->orderBy('id', 'DESC')
                        ->get()
                        ->getResultArray();
    }
    public function getDeniedProspects()
    {
        return $this->db->table('prospectors_details')
                        ->where('prospectors_status', 2)
                        ->orderBy('id', 'DESC')
                        ->get()
                        ->getResultArray();
    }

    public function getRemarksByProspectId($prospectId)
    {
        return $this->db->table('prospectors_remark_details')
            ->where('prospectors_id', $prospectId)
            ->groupStart()
                ->groupStart()
                    ->where('remarks IS NOT NULL')
                    ->where('remarks !=', '')
                ->groupEnd()
                ->orGroupStart()
                    ->where('payment_description IS NOT NULL')
                    ->where('payment_description !=', '')
                ->groupEnd()
            ->groupEnd()
            ->orderBy('create_date', 'DESC')
            ->get()
            ->getResultArray();
    }
    public function getProspectCounts()
    {
        $db = \Config\Database::connect();

        $today = date('Y-m-d');
        $month = date('m');

        $data = [];

        // In Progress (status = 0)
        $data['inProgressCount'] = $db->table('prospectors_details')
            ->where('prospectors_status', 0)
            ->countAllResults();

        // Closed (status = 1)
        $data['closedCount'] = $db->table('prospectors_details')
            ->where('prospectors_status', 1)
            ->countAllResults();

        // Denied (status = 2)
        $data['deniedCount'] = $db->table('prospectors_details')
            ->where('prospectors_status', 2)
            ->countAllResults();
           
        $data['holdCount'] = $db->table('prospectors_details')
            ->where('prospectors_status', 3)
            ->countAllResults();

        //Today counts
        $data['todayInProgress'] = $db->table('prospectors_details')
            ->where('DATE(created_at)', $today)
            ->countAllResults();

        //This month counts
        $data['monthInProgress'] = $db->table('prospectors_details')
            ->where('prospectors_status', 0)
            ->where('MONTH(created_at)', $month)
            ->countAllResults();
        return $data;
    }

    public function getPlanCounts()
{
    $db = \Config\Database::connect();
    $plans = ['Silver', 'Gold', 'Platinum', 'Silver++', 'Rhodium', 'Pearl', 'Sapphire'];

    $result = [];
    $total = 0;

    // Subquery to get latest create_date for each (prospector_id + title)
    $subquery = $db->table('prospectors_book_details')
        ->select('MAX(create_date) as latest_date, prospector_id, title')
        ->groupBy('prospector_id, title');

    // Join latest entry to main book details
    $baseQuery = $db->table('prospectors_book_details b')
        ->join('(' . $subquery->getCompiledSelect() . ') latest',
            'latest.prospector_id = b.prospector_id 
             AND latest.title = b.title 
             AND latest.latest_date = b.create_date',
            'inner')
        ->join('prospectors_details p', 'p.id = b.prospector_id', 'left')
        ->where('p.prospectors_status', 1);

    // Loop through plans and count occurrences
    foreach ($plans as $plan) {
        $builder = clone $baseQuery;
        $count = $builder->where('b.plan_name', $plan)->countAllResults();
        $result[$plan] = $count;
        $total += $count;
    }

    $result['totalPlans'] = $total;

    return $result;
}
   public function getPaymentSummary()
{
    $db = \Config\Database::connect();

    // Subquery – Get latest record per (prospector_id + title)
    $subquery = $db->table('prospectors_book_details')
        ->select('prospector_id, title, MAX(create_date) AS latest_date')
        ->groupBy('prospector_id, title');

    //  Join to get only those latest entries
    $builder = $db->table('prospectors_book_details b')
        ->select('b.prospector_id, b.title, b.payment_status, b.payment_amount, b.create_date')
        ->join('(' . $subquery->getCompiledSelect() . ') latest',
            'latest.prospector_id = b.prospector_id 
             AND latest.title = b.title 
             AND latest.latest_date = b.create_date',
            'inner')
        ->join('prospectors_details p', 'p.id = b.prospector_id', 'left')
        ->where('p.prospectors_status', 1) // closed/active prospects only
        ->orderBy('b.create_date', 'DESC');

    $records = $builder->get()->getResultArray();

    //  Initialize counters
    $totalCount = $paidCount = $partialCount = 0;
    $totalRevenue = $paidTotal = $partialTotal = 0.0;

    // Loop through latest records only
    foreach ($records as $row) {
        $amount = (float)($row['payment_amount'] ?? 0);
        $status = strtolower(trim($row['payment_status'] ?? ''));

        // Count total titles
        $totalCount++;
        $totalRevenue += $amount;

        if ($status === 'paid') {
            $paidCount++;
            $paidTotal += $amount;
        } elseif ($status === 'partial') {
            $partialCount++;
            $partialTotal += $amount;
        }
    }

    // Return summary array
    return [
        'totalCount'    => $totalCount,
        'totalRevenue'  => $totalRevenue,
        'paidCount'     => $paidCount,
        'paidTotal'     => $paidTotal,
        'partialCount'  => $partialCount,
        'partialTotal'  => $partialTotal,
    ];
}

public function addBook()
{
    $db = \Config\Database::connect();

    // Fetch all active prospectors (prospectors_status = 1)
    $prospectors = $db->table('prospectors_details')
        ->select('id, name')
        ->where('prospectors_status', 1)
        ->orderBy('name', 'ASC')
        ->get()
        ->getResultArray();

    $data = [
        'prospectors' => $prospectors,
    ];

    return view('prospectivemanagement/add_book', $data);
}

public function saveBookDetails()
{
    $request = service('request');
    $db = \Config\Database::connect();

    $prospector_id       = $request->getPost('prospector_id');
    $plan_name           = $request->getPost('plan_name');
    $title               = $request->getPost('title');
    $payment_status      = $request->getPost('payment_status');
    $payment_amount      = $request->getPost('payment_amount');
    $payment_description = $request->getPost('payment_description');
    $payment_date        = $request->getPost('payment_date');

    // Insert into prospectors_book_details table
    $bookData = [
        'prospector_id'       => $prospector_id,
        'plan_name'           => $plan_name,
        'title'               => $title,
        'payment_status'      => $payment_status,
        'payment_amount'      => $payment_amount,
        'payment_date'        => $payment_date,
    ];
    $db->table('prospectors_book_details')->insert($bookData);

    // Insert into prospectors_remark_details table
    $remarkData = [
        'prospectors_id'      => $prospector_id,
        'payment_description' => $payment_description,
        'des_date'            => date('Y-m-d H:i:s'),
        'created_by'          => session()->get('username') ?? 'System',
    ];
    $db->table('prospectors_remark_details')->insert($remarkData);

    return redirect()->to(base_url('prospectivemanagement'))
        ->with('success', 'Book details added successfully!');
}

    // Get book/plan/payment details (title, plan, amount, status)
   public function getProspectorPaidPartialPlans($prospectorId)
{
    $builder = $this->db->table('prospectors_book_details');

    // Subquery to get latest id per title for paid/partial
    $subQuery = $this->db->table('prospectors_book_details')
        ->select('MAX(id) as max_id')
        ->where('prospector_id', $prospectorId)
        ->groupStart()
            ->whereIn('LOWER(payment_status)', ['paid', 'partial'])
        ->groupEnd()
        ->groupBy('title');

    $builder->select('id, prospector_id, title, plan_name, payment_status, payment_amount, payment_date, create_date'); // include prospector_id
    $builder->where('prospector_id', $prospectorId)
            ->whereIn('id', $subQuery);

    $builder->orderBy('create_date', 'DESC'); 
    $query = $builder->get();

    $result = $query->getResultArray();

    // Add readable payment status
    foreach ($result as &$row) {
        $status = strtolower($row['payment_status'] ?? '');
        if ($status === 'paid') {
            $row['payment_status_text'] = 'Fully Paid';
        } elseif ($status === 'partial') {
            $row['payment_status_text'] = 'Partial Payment';
        } else {
            $row['payment_status_text'] = 'Unpaid';
        }
    }

    return $result;
}

public function getProspectorGeneralRemarks($prospectorId)
{
    return $this->db->table('prospectors_remark_details')
        ->select('payment_description, remarks, created_by, create_date, title')
        ->where('prospectors_id', $prospectorId)
        ->orderBy('create_date', 'DESC')
        ->get()
        ->getResultArray();
}



 public function getBookByProspectorAndId($prospector_id, $id)
{
    $db = \Config\Database::connect();
    return $db->table('prospectors_book_details b')
              ->select('b.*, p.name as prospector_name, pp.cost')
              ->join('prospectors_details p', 'p.id = b.prospector_id', 'left')
              ->join('publishing_plan_details pp', 'pp.plan_name = b.plan_name', 'left')
              ->where('b.prospector_id', $prospector_id)
              ->where('b.id', $id)
              ->get()
              ->getRowArray();
}

    public function updateBookByTitle($title, $data)
{
    $db = \Config\Database::connect(); // manually connect
    return $db->table('prospectors_book_details')
              ->where('title', $title)
              ->insert($data);
}
    public function insertRemarkDetails($data)
    {
        return $this->db->table('prospectors_remark_details')->insert($data);
    }
    public function getProspectByBookTitle($title)
{
    return $this->db->table('prospectors_book_details b')
        ->select('p.id, p.name')
        ->join('prospectors_details p', 'p.id = b.prospector_id', 'left')
        ->where('b.title', $title)
        ->get()
        ->getRowArray();
}
public function getPlansByProspectorAndId($prospector_id, $id)
{
    $builder = $this->db->table('prospectors_book_details');
    $builder->select('id, prospector_id, title, plan_name, payment_status, payment_amount, payment_date, create_date');
    $builder->where('prospector_id', $prospector_id);
    $builder->where('id', $id);
    $builder->groupStart()
            ->whereIn('LOWER(payment_status)', ['paid', 'partial'])
            ->groupEnd();
    $builder->orderBy('create_date', 'DESC');

    $plans = $builder->get()->getResultArray();

    foreach ($plans as &$plan) {
        $status = strtolower($plan['payment_status'] ?? '');
        if ($status === 'paid') {
            $plan['payment_status_text'] = 'Fully Paid';
        } elseif ($status === 'partial') {
            $plan['payment_status_text'] = 'Partial Payment';
        } else {
            $plan['payment_status_text'] = 'Unpaid';
        }
    }

    return $plans;
}

public function getRemarksByProspectorAndTitle($prospectorId, $title = null)
{
    $builder = $this->db->table('prospectors_remark_details');
    $builder->select('payment_description, des_date, remarks, create_date, created_by');
    $builder->where('prospectors_id', $prospectorId);

    // Remove title filter completely → always return all remarks
    return $builder->get()->getResultArray();
}

public function getProspectNameById($prospectorId)
{
    $sql = "SELECT name FROM prospectors_details WHERE id = ?";
    $query = $this->db->query($sql, [$prospectorId]);
    $result = $query->getRowArray();

    return $result['name'] ?? 'N/A';
}

public function getPlanSummaryHome()
{
    $db = \Config\Database::connect();

    // Fixed 7 plan names 
    $allPlans = [
        'Silver',
        'Gold',
        'Platinum',
        'Rhodium',
        'Silver++',
        'Pearl',
        'Sapphire'
    ];

    // Fetch summary
    $query = $db->table('publishing_plan_details pp')
        ->select('pp.plan_name, pp.cost AS plan_unit_cost, 
                  COUNT(DISTINCT b.title) AS total_titles,
                  COALESCE(SUM(b.payment_amount), 0) AS total_paid')
        ->join('prospectors_book_details b', 'b.plan_name = pp.plan_name', 'left')
        ->join('prospectors_details pd', 'pd.id = b.prospector_id', 'left')
        ->where('pd.prospectors_status', 1)
        ->groupBy('pp.plan_name, pp.cost')
        ->get()
        ->getResultArray();

    $dbPlans = [];
    foreach ($query as $p) {
        $dbPlans[$p['plan_name']] = $p;
    }

    // Final output
    $plans = [];
    foreach ($allPlans as $planName) {

        $row = $dbPlans[$planName] ?? [
            'plan_name'      => $planName,
            'plan_unit_cost' => 0,
            'total_titles'   => 0,
            'total_paid'     => 0
        ];

        // Today
        $row['today_count'] = $db->table('prospectors_book_details b')
            ->join('prospectors_details pd', 'pd.id = b.prospector_id', 'left')
            ->where('b.plan_name', $planName)
            ->where('pd.prospectors_status', 1)
            ->where('DATE(b.create_date)', date('Y-m-d'))
            ->countAllResults();

        // This Month
        $row['month_count'] = $db->table('prospectors_book_details b')
            ->join('prospectors_details pd', 'pd.id = b.prospector_id', 'left')
            ->where('b.plan_name', $planName)
            ->where('pd.prospectors_status', 1)
            ->where('MONTH(b.create_date)', date('m'))
            ->where('YEAR(b.create_date)', date('Y'))
            ->countAllResults();

        // Previous Month
        $row['prev_month_count'] = $db->table('prospectors_book_details b')
            ->join('prospectors_details pd', 'pd.id = b.prospector_id', 'left')
            ->where('b.plan_name', $planName)
            ->where('pd.prospectors_status', 1)
            ->where('MONTH(b.create_date)', date('m', strtotime('-1 month')))
            ->where('YEAR(b.create_date)', date('Y', strtotime('-1 month')))
            ->countAllResults();

        // Total plan cost (unit cost × title count)
        $row['plan_cost'] = ($row['plan_unit_cost'] ?? 0) * ($row['total_titles'] ?? 0);

        $plans[] = $row;
    }

    return $plans;
}



}
