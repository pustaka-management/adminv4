<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
         $this->db = \Config\Database::connect();
    }

   public function getUserDashboardData(): array
{
    $db = \Config\Database::connect();
    $result = [];

    // 1. User registration count per year
    $builder = $db->table('users_tbl');
    $builder->select("COUNT(*) as cnt, YEAR(created_at) as year");
    $builder->groupBy("YEAR(created_at)");
    $builder->orderBy("year", "ASC");
    $query = $builder->get()->getResultArray();

    $user_registration_cnt = [];
    $years = [];
    foreach ($query as $row) {
        $user_registration_cnt[] = (int)$row['cnt'];
        $years[] = (string)$row['year'];
    }

    // Ensure current year is present even if empty
    if (empty($years)) {
        $currentYear = date('Y');
        $years = [$currentYear];
        $user_registration_cnt = [0];
    }

    $result['user_registration_cnt'] = $user_registration_cnt;
    $result['year'] = $years;

    // 2. Monthly registration (current month)
    $monthlyCount = $db->table('users_tbl')
        ->where('MONTH(created_at)', date('m'))
        ->where('YEAR(created_at)', date('Y'))
        ->countAllResults();
    $result['monthly_registration'] = $monthlyCount;

    // 3. Weekly registration (last 7 days)
    $weeklyCount = $db->table('users_tbl')
        ->where('created_at >=', date('Y-m-d 00:00:00', strtotime('-7 days')))
        ->countAllResults();
    $result['weekly_registration'] = $weeklyCount;

    // 4. Total registration
    $totalRegistration = $db->table('users_tbl')->countAllResults();
    $result['total_registration'] = $totalRegistration;
    $result['total_users'] = $totalRegistration; // Alias for clarity

    // 5. Login users in last 7 days
    $sevenDaysAgo = date('Y-m-d 00:00:00', strtotime('-7 days'));
    $result['login_users'] = $db->table('users_tbl')
        ->select('user_id, username, email, phone, channel, user_type, otp, created_at')
        ->where('created_at >=', $sevenDaysAgo)
        ->orderBy('created_at', 'DESC')
        ->get()
        ->getResultArray();

    // 6. Login summary by type
    $result['login_summary'] = $db->table('users_tbl')
        ->select("
            DATE(created_at) AS login_date,
            CASE 
                WHEN channel IS NOT NULL THEN 'email_with_google'
                WHEN email IS NOT NULL AND otp IS NULL THEN 'email_with_password'
                WHEN phone IS NOT NULL THEN 'mobile_with_otp'
                ELSE 'other'
            END AS login_type,
            COUNT(*) AS login_count", false)
        ->where('created_at >=', $sevenDaysAgo)
        ->groupBy(['login_date', 'login_type'])
        ->orderBy('login_date', 'DESC')
        ->get()
        ->getResultArray();

    // 7. Users with shipping address
   $usersWithAddressAndPhone = $db->table('user_address AS ua')
    ->join('users_tbl AS u', 'u.user_id = ua.user_id')
    ->where('TRIM(ua.shipping_address1) !=', '')
    ->where('TRIM(u.phone) !=', '')
    ->countAllResults();

	$result['users_with_address_and_phone'] = $usersWithAddressAndPhone;

    // 9. Users who used OTP
    $usersWithOtp = $db->table('users_tbl')
        ->where("TRIM(otp) !=", "")
        ->countAllResults();
    $result['users_with_otp'] = $usersWithOtp;

    // 10. Users who logged in with Google
    $usersWithGoogle = $db->table('users_tbl')
        ->where("TRIM(channel) !=", "")
        ->countAllResults();
    $result['users_with_google'] = $usersWithGoogle;

    return $result;
}

public function getUserDetails($identifier)
{
    $db = \Config\Database::connect();

    // Detect whether identifier is email, phone or user_id
    $pos = strpos($identifier, '@');
    if ($pos === false) {
        if (preg_match('/^[0-9]{10}$/', $identifier)) {
            $sql = "SELECT username, email, user_id, user_type, otp, phone, password, channel, DATE_FORMAT(created_at, '%d-%m-%y') as date 
                    FROM users_tbl WHERE phone = ?";
        } else {
            $sql = "SELECT username, email, user_id, user_type, otp, phone, password, channel, DATE_FORMAT(created_at, '%d-%m-%y') as date 
                    FROM users_tbl WHERE user_id = ?";
        }
    }       else {
        $sql = "SELECT username, email, user_id, user_type, otp, phone, password, channel, DATE_FORMAT(created_at, '%d-%m-%y') as date 
                FROM users_tbl WHERE email = ?";
    }

    $query = $db->query($sql, [$identifier]);
    $tmp = $query->getResult();

    if ($query->getNumRows() == 0) {
        return [
            'user_name' => "--",
            'user_email' => "--",
            'user_id' => "--",
            'user_type' => "--",
            'user_join_date' => "--",
            'channel' => "--",
            'phone' => "--",
            // return empty arrays for other data for consistency
            'subscriptions' => [],
            'purchased_books' => [],
            'purchased_paperbacks' => [],
            'wallet_detail' => [],
            'transaction_detail' => [],
            'free_books' => [],
            'author_books' => [],
            'guest_books' => [],
        ];
    }

    $user = $tmp[0]; // object containing user data
    $userId = $user->user_id;

    // Determine login channel
    $channel = '';
    if (strlen($user->channel) == 0 || $user->channel == '1') {  
        if (strlen($user->phone) > 0 && strlen($user->otp) > 0 && $user->email == '' && $user->password == '') {
            $channel = 'Mobile/OTP'; 
        } elseif ($user->phone == '' && $user->otp == '' && strlen($user->email) > 0 && $user->password == '4732210395731ca375874a1e7c8f62f6') {
            $channel = 'Email/Password (default)';  
        } elseif ($user->phone == '' && $user->otp == '' && strlen($user->email) > 0 && $user->password != '4732210395731ca375874a1e7c8f62f6') {
            $channel = 'Email/Password (custom)';  
        } elseif (strlen($user->phone) > 0 && $user->otp == '' && strlen($user->email) > 0 && $user->password != '4732210395731ca375874a1e7c8f62f6') {
            $channel = 'Email/Password (custom)';  
        } elseif (strlen($user->phone) > 0 && strlen($user->otp) > 0 && strlen($user->email) > 0 && $user->password != '4732210395731ca375874a1e7c8f62f6') {
            $channel = 'Both (custom)';  
        } elseif (strlen($user->phone) > 0 && strlen($user->otp) > 0 && strlen($user->email) > 0 && $user->password == '4732210395731ca375874a1e7c8f62f6') {
            $channel = 'Both (default)';  
        } elseif (strlen($user->phone) > 0 && strlen($user->otp) > 0 && strlen($user->email) > 0) {
            $channel = 'Both (google)'; 
        } else {
            $channel = $user->channel;
        }
    } else {
        $channel = 'Email/Google';
    }

    $result = [
        'user_name' => $user->username,
        'user_email' => $user->email,
        'user_id' => $user->user_id,
        'user_type' => $user->user_type,
        'user_join_date' => $user->date,
        'phone' => $user->phone,
        'channel' => $channel,
    ];

	$sql = "SELECT 
			subscription.order_id, 
			DATE_FORMAT(subscription.date_inserted, '%d-%m-%Y') AS date, 
			DATE_FORMAT(subscription.end_date, '%d-%m-%Y') AS end_date,  -- format d-m-Y for display
			subscription.total_books_applicable,
			subscription.plan_type, 
			plan_tbl.plan_name,
			`order`.net_total  
		FROM 
			subscription
		JOIN 
			plan_tbl ON subscription.subscription_id = plan_tbl.plan_id
		JOIN 
			`order` ON subscription.order_id = `order`.order_id
		WHERE 
			subscription.user_id = ".$result['user_id']."
		ORDER BY 
			subscription.end_date DESC";  // latest end date first

$query = $db->query($sql);
$subscriptions = [];

foreach ($query->getResultArray() as $row) {
    $subscription = []; 
    $subscription['date_subscribed'] = $row['date'];
    $subscription['end_subscribed'] = $row['end_date'];
    $subscription['total_books'] = $row['total_books_applicable'];
    $subscription['order_id'] = $row['order_id']; 
    $subscription['plan_name'] = $row['plan_name'];
    $subscription['net_total'] = $row['net_total'];
    $subscription['plan_type'] = $row['plan_type'];

    // Fetch books
    $sql_book = "SELECT obd.book_id, book_tbl.book_title, author_tbl.author_name, obd.order_date, obd.date_created
                 FROM order_book_details obd
                 JOIN book_tbl ON book_tbl.book_id = obd.book_id
                 JOIN author_tbl ON author_tbl.author_id = obd.author_id
                 WHERE obd.order_id = ".$row['order_id'];

    $query2 = $db->query($sql_book);
    $books = [];

    foreach ($query2->getResultArray() as $b) {
        $books[] = [
            'book_id' => $b['book_id'],
            'book_name' => $b['book_title'],
            'author_name' => $b['author_name'],
            'order_date' => $b['order_date'],
			'date_created' => $b['date_created'],
        ];
    }

    $subscription['books'] = $books;
    $subscriptions[] = $subscription;
}

$result['subscriptions'] = $subscriptions;

		//purchased books
		$sql = "SELECT book_tbl.book_title, DATE_FORMAT(order_book_details.date_created, '%d-%m-%y') as date
						from book_tbl, order_book_details 
						where order_book_details.book_id = book_tbl.book_id 
						and order_book_details.order_type = 2 
						and order_book_details.user_id = ".$result['user_id'];
		$query = $db->query($sql);
		$i = 0;
		$purchased_books = array();
        foreach ($query->getResultArray() as $row)
        {
          	$purchased_book['purchased_book_title'] = $row['book_title'];
		  	$purchased_book['date_purchased'] = $row['date'];
		  	$purchased_books[$i] = $purchased_book;
          	$i++;
		}
		$result['purchased_books'] = $purchased_books;

		//purchased Paperback
		$sql = "SELECT book_tbl.book_title,pod_order_details.order_date, pod_order_details.book_id,pod_order_details.price,pod_order_details.quantity,
				pod_order.order_id,pod_order.tracking_id,pod_order.tracking_url
				FROM pod_order_details,book_tbl ,pod_order
				where pod_order_details.book_id=book_tbl.book_id and pod_order.order_id=pod_order_details.order_id
				and pod_order_details.user_id=".$result['user_id'];
		$query = $db->query($sql);
		$i = 0;
		$purchased_paperbacks = array();
		foreach ($query->getResultArray() as $row)
		{
  			$purchased_paperback['purchased_paperback_title'] = $row['book_title'];
  			$purchased_paperback['purchased_date'] = $row['order_date'];
  			$purchased_paperback['order_id'] = $row['order_id'];
  			$purchased_paperback['tracking_id'] = $row['tracking_id'];
  			$purchased_paperback['tracking_url'] = $row['tracking_url'];
			$purchased_paperback['book_id'] = $row['book_id'];
			$purchased_paperback['price'] = $row['price'];
			$purchased_paperback['quantity'] = $row['quantity'];
  			$purchased_paperbacks[$i] = $purchased_paperback;
  			$i++;
		}
		$result['purchased_paperbacks'] = $purchased_paperbacks;

		//wallet details
		$sql = "SELECT user_wallet.balance_inr as INR,user_wallet.balance_usd as USD,
			user_wallet.user_id as id,users_tbl.username as user
			FROM users_tbl,user_wallet where users_tbl.user_id = user_wallet.user_id 
			and user_wallet.user_id=".$result['user_id'];
		$query = $this->db->query($sql);
		$i = 0;
		$wallet_detail = array();
		foreach ($query->getResultArray() as $row)
		{
			$wallet['balance_inr'] = $row['INR'];
			$wallet['balance_usd'] = $row['USD'];
			$wallet_detail[$i] = $wallet;
			$i++;
		}
		$result['wallet_detail'] = $wallet_detail;

		$sql = "SELECT user_wallet_transaction.currency,user_wallet_transaction.date,user_wallet_transaction.amount, 
		        wallet_transaction_type.transaction_value 
				from user_wallet_transaction, wallet_transaction_type 
				where user_wallet_transaction.transaction_type=wallet_transaction_type.transaction_type 
				and user_wallet_transaction.user_id=".$result['user_id'];
		$query = $db->query($sql);
		$i = 0;
		$transaction_detail = array();
        foreach ($query->getResultArray() as $row)
        {
          $transaction['currency'] = $row['currency'];
		  $transaction['amount'] = $row['amount'];
		  $transaction['date'] = $row['date'];
		  $transaction['transaction_value'] = $row['transaction_value'];
		  $transaction_detail[$i] = $transaction;
          $i++;
		}
		$result['transaction_detail'] = $transaction_detail;

		//free books
		$sql = "SELECT book_tbl.book_title, DATE_FORMAT(free_book_subscription.date_subscribed, '%d-%m-%y') as date 
						from book_tbl, free_book_subscription 
						where free_book_subscription.book_id = book_tbl.book_id 
						and free_book_subscription.user_id = ".$result['user_id'];
		$query = $db->query($sql);
        $i = 0;
        $free_books = array();
        foreach ($query->getResultArray() as $row)
        {
          $free_book['free_book_title'] = $row['book_title'];
		  $free_book['date'] = $row['date'];
		  $free_books[$i] = $free_book;
          $i++;
		}
		$result['free_books'] = $free_books;
        
        //author gift books
        $sql = "SELECT author_tbl.author_name, book_tbl.book_title, DATE_FORMAT(author_gift_books.date, '%d-%m-%y') as date 
                        from author_tbl, author_gift_books, book_tbl
                        where author_gift_books.author_id = author_tbl.author_id 
                        and author_gift_books.book_id = book_tbl.book_id
						and author_gift_books.user_id = ".$result['user_id'];
        $query = $this->db->query($sql);
		$i = 0;
		$author_books = array();
        foreach ($query->getResultArray() as $row)
        {
          $author_book['author_name'] = $row['author_name'];
          $author_book['book_title'] = $row['book_title'];
		  $author_book['gift_date'] = $row['date'];
		  $author_books[$i] = $author_book;
		  $i++;
		}
		$result['author_books'] = $author_books;

        //devices
        $sql = "SELECT device_id1, device_info1, device_id2, device_info2, device_id3, device_info3 
                        FROM user_devices 
                        WHERE  user_id = ".$result['user_id'];
        $query = $db->query($sql);
        $tmp = $query->getResult();
		if (isset($tmp[0]->device_id1))
			$result['device_id1'] = $tmp[0]->device_id1;
		else
			$result['device_id1'] = "";
		if (isset($tmp[0]->device_info1))
			$result['device_info1'] = $tmp[0]->device_info1;
		else
			$result['device_info1'] = "";
		if (isset($tmp[0]->device_id2))
			$result['device_id2'] = $tmp[0]->device_id2;
		else
			$result['device_id2'] = "";
		if (isset($tmp[0]->device_info2))
			$result['device_info2'] = $tmp[0]->device_info2;
		else
			$result['device_info2'] = "";
		if (isset($tmp[0]->device_id3))
			$result['device_id3'] = $tmp[0]->device_id3;
		else
			$result['device_id3'] = "";
		if (isset($tmp[0]->device_info3))
			$result['device_info3'] = $tmp[0]->device_info3;
		else
			$result['device_info3'] = "";

        return $result;
    }

    public function clearUserDevices($user_id)
	{
		$builder = $this->db->table('user_devices');
		$builder->where('user_id', $user_id);
		$builder->set([
			'device_id1' => '',
			'device_info1' => '',
			'device_id2' => '',
			'device_info2' => '',
			'device_id3' => '',
			'device_info3' => ''
		]);

		$builder->update();

		return ($this->db->affectedRows() > 0) ? true : false;
	}

    public function add_plan($user_id, $plan_id)
    {
        $db = \Config\Database::connect();

        // Fetch plan
        $plan = $db->table('plan_tbl')->where('plan_id', $plan_id)->get()->getRowArray();
        if (!$plan) {
            return false;
        }

        // Fetch user
        $user = $db->table('users_tbl')->where('user_id', $user_id)->get()->getRowArray();
        if (!$user) {
            return false;
        }

        // Calculate values
        $net_total     = $plan['plan_cost'];
        $service_tax   = 0.05 * $net_total;
        $net_revenue   = $net_total - $service_tax;
        $order_id_time = time();

        // Insert into `order`
        $order_data = [
            'order_id'          => $order_id_time,
            'order_status'      => 'Success',
            'payment_mode'      => 'Offline',
            'status_message'    => 'Success',
            'currency'          => 'INR',
            'amount'            => $net_total,
            'billing_name'      => $user['username'],
            'delivery_name'     => $user['username'],
            'user_id'           => $user_id,
            'cart_type'         => 1,
            'service_tax'       => $service_tax,
            'net_revenue'       => $net_revenue,
            'net_total'         => $net_total,
            'date_created'      => date('Y-m-d H:i:s'),
            'coupon_id'         => 'no_coupon',
            'coupon_discount_amt' => 0,
        ];

        $db->table('order')->insert($order_data);
        $insert_id = $db->insertID();

        if (!$insert_id) {
            return false;
        }

        // Fetch real order_id from inserted row
        $order_row = $db->table('order')->where('id', $insert_id)->get()->getRowArray();
        $order_id = $order_row['order_id'] ?? $order_id_time;

        // Calculate subscription dates
        $start_date = date('Y-m-d');
        $end_date   = date('Y-m-d', strtotime("+{$plan['validity_days']} days"));

        // Insert into subscription
        $subscription_data = [
            'order_id'             => $order_id,
            'user_id'              => $user_id,
            'subscription_id'      => $plan['plan_id'],
            'plan_type'            => $plan['plan_type'],
            'number_of_days'       => $plan['validity_days'],
            'start_date'           => $start_date,
            'end_date'             => $end_date,
            'total_books_applicable' => $plan['available_books'],
            'date_inserted'        => date('Y-m-d H:i:s'),
            'status'               => 1,
        ];

        $db->table('subscription')->insert($subscription_data);

        // Prepare email data
        $data = [
            'to'                     => $user['email'],
            'username'              => $user['username'],
            'cc'                    => ['admin@pustaka.co.in'],
            'plan_type'             => $plan['plan_type'],
            'order_id'              => $order_id,
            'validity_days'         => $plan['validity_days'],
            'available_books'       => $plan['available_books'],
            'book_validity_days'    => $plan['book_validity_days'],
            'plan_name'             => $plan['plan_name'],
            'plan_cost'             => $plan['plan_cost'],
            'plan_cost_international' => $plan['plan_cost_international'],
            'invoice_no'            => $insert_id,
            'currency'              => 'INR',
        ];

        // Send mail
        $email_success = $this->send_mail($data, $user);

        return $insert_id > 0 ? true : false;
    }


     public function send_mail($data, $user)
    {
        $email = \Config\Services::email();

        $config = [
            'charset'   => 'utf-8',
            'mailType'  => 'html',
        ];

        $email->initialize($config);

        $email->setFrom('admin@pustaka.co.in', 'Pustaka');
        $email->setTo($data['to']);

        if (isset($data['cc'])) {
            $email->setCC(is_array($data['cc']) ? implode(',', $data['cc']) : $data['cc']);
        }

        $email->setSubject("Your Pustaka Purchase");

		$message = "<html lang=\"en\">
			<head>
			<meta charset=\"utf-8\"/>
			<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
			<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />
			<meta name=\"x-apple-disable-message-reformatting\" />
			<!--[if !mso]><!-->
			 <meta http-equiv=\"X-UA-Compatible\" content\"IE=edge\" />
			<!--<![endif]-->
			<title></title>
			<!--[if !mso]><!-->
		   <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\" />
			<link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin=\"\" />
		   <link href=\"https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&amp;display=swap\"
		 rel=\"stylesheet\"/>
		   <!--<![endif]-->
		</head>
		<body
		style=\"
		  margin: 0;
		  padding: 0;
		  background-color: #ffffff;
		  color: #000000;
		  font-family: 'Quicksand', sans-serif;
		  font-size: 16px;\"
		data-new-gr-c-s-check-loaded=\"14.1052.0\"
		data-gr-ext-installed=\"\">
		<table
		class=\"main-table\"
		style=\"
		  max-width: 850px;
		  min-width: 350px;
		  margin: 0 auto;
		  padding-left: 20px;
		  padding-right: 20px;\"
		  cellpadding=\"0\"
		  cellspacing=\"0\">
		<tbody>
		  <tr
			style=\"
			  background: linear-gradient(
				  0deg,
				  rgba(0, 41, 107, 0.2),
				  rgba(0, 41, 107, 0.2)
				),
				linear-gradient(135deg, #4685ec 0%, #00296b 100%);\">
			<td style=\"padding: 40px 0px; text-align: center\">
			<img src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/pustaka-logo.png\"
				alt=\"Logo\"
				title=\"Logo\"
				style=\"
				  display: inline-block !important;
				  width: 33%;
				  max-width: 174.9px;
				\"/>
			</td>
		  </tr>
			<tr>
			<td style=\"text-align: center\">
			<h1 style=\"
				text-align: center;
				word-wrap: break-word;
				font-weight: 600;
				font-size: 36px;
				margin-top: 30px;
				margin-bottom: 30px;\">
					Invoice for the Plan
				  </h1>
				</td>
			  </tr>
			  <tr>
			  <td style=\"text-align: right\">
			  <p style=\"font-size: 18px; line-height: 28px; margin: 0\">";

		$message .= "Invoice Date: " . date('d/M/Y');			  
		$message .= "</p>
		<p style=\"font-size: 18px; line-height: 28px; margin: 0\">
		</p>
		  </td>
		</tr>
		<tr>
		  <td style=\"text-align: left; padding-top: 30px; padding-bottom: 20px\">
		<p style=\"font-size: 18px; line-height: 28px\">";
	  $message .= "<p style=\"font-size: 18px; line-height: 28px\">Hi&nbsp;";
	  $message .= $data["username"];
	  $message .= "</p>
				  <p style=\"font-size: 18px; line-height: 28px\">";
	  if ($data['plan_type'] == 2)
			  $message .= 
				"Thanks for purchasing the Audio Book Subscription on Pustaka. This
				  email contains the details of your subscription plan & invoice for
				  the same. No dues are pending for your order. You can start
				  listening to our audio books on our mobile application.";
		  else
			  $message .= 
				"Thanks for purchasing the eBook Subscription on Pustaka. This
				  email contains the details of your subscription plan & invoice for
				  the same. No dues are pending for your order. You can start
				  reading our ebooks on our mobile application or from website.";
		  $message .= 
			  "</p>
				  <p style=\"font-size: 18px; line-height: 28px\">
					Details of your plan:
				  </p>
				</td>
			  </tr>
			  <tr>
				<td>
				  <table
					style=\"
					  width: 100%;
					  text-align: left;
					  border-collapse: collapse;
					  font-size: 18px;
					  line-height: 28px;
					\">
					<thead>
					  <tr style=\"border-bottom: 1px solid #c4c4c4\">
						<th
						  style=\"
							font-weight: 400;
							font-weight: 500;
							font-size: 18px;
							padding-bottom: 10px;
						  \">
						  Product Name
						</th>
						<th
						  style=\"
							font-weight: 500;
							font-size: 18px;
							padding-bottom: 10px;
							text-align: right;
						  \">
						  Total Price
						</th>
					  </tr>
					</thead>
					<tbody
					  style=\"border-bottom: 1px solid #c4c4c4; padding-bottom: 30px\">
					  <tr>
						<td style=\"padding-bottom: 15px; padding-top: 15px\">
						  <div>
							<div style=\"display: inline; width: 60px; float: left\">
							  <img
								src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/pustaka-plan-icon.png\"
								style=\"width: 50px\"
							  />
							</div>
							<div style=\"display: inline; margin-left: 15px\">
							  <div style=\"font-size: 22px\">";
		  $message .= $data['plan_name'];
		  $message .= "</div>
						 <p style=\"margin: 0; color: #212121; font-size: 15px\">";
		  $message .= "Validity - " . strval($data['validity_days']) . " days, " . strval($data['available_books']);
		  $message .= " Books, Each Book Validity - " . $data['book_validity_days'] . " days";
		  $message .= "</p>
							</div>
						  </div>
						</td>
						<td style=\"text-align: right\">";
		//   $plan_cost_without_gst = $data['plan_cost']+1.05;
		  $gst_percentage = 18;
		  $plan_cost_without_gst = round($data['plan_cost'] / (1 + ($gst_percentage / 100)),2); 
		  $gst_amount = round($data['plan_cost'] - $plan_cost_without_gst,2); 


		  if ($data['currency'] == "INR")
			  $message .= "&#8377;" . $plan_cost_without_gst;
		  else
			  $message .= "$" + $data['plan_cost_international'];
		  $message .= "</td>
					  </tr>
					</tbody>
				  </table>
				</td>
			  </tr>
			  <tr>
				<td>
				  <table
					style=\"
					  max-width: 850px;
					  min-width: 290px;
					  text-align: left;
					  margin-left: auto;
					  font-size: 18px;
					  line-height: 28px;
					  margin-top: 30px;
					  margin-bottom: 40px;
					  border-collapse: collapse;
					\">
					<tbody>
					  <tr>
						<td style=\"font-weight: 600; padding-bottom: 15px\">
						  GST @ 18%
						</td>
						<td style=\"text-align: right\">";
		  if ($data['currency'] == "INR")
			  $message .= "&#8377;" . $gst_amount;
		  else
			  $message .= "$ N/A";
		  $message .= "</td>
					  </tr>
					  <tr
						style=\"
						  border-bottom: 1px solid #c4c4c4;
						  border-top: 1px solid #c4c4c4;
						\">
						<td
						  style=\"
							font-weight: 600;
							color: #00296b;
							padding-bottom: 15px;
							padding-top: 15px;
						  \">
						  Total Amount Paid 
						</td>
						<td
						  style=\"text-align: right; color: #00296b; font-weight: 600\">";
		  if ($data['currency'] == "INR")
			  $message .= "&#8377;" . $data['plan_cost'];
		  else
			  $message .= "$" . $data['plan_cost_international'];
		  $message .= "</td>
					  </tr>
					</tbody>
				  </table>
				</td>
			  </tr>
			  <tr style=\"display: table; margin-bottom: 50px; margin-top: 10px\">
				<td style=\"padding-right: 50px; width: 50%\">
				  <table
					style=\"
					  text-align: left;
					  font-size: 18px;
					  line-height: 28px;
					  border-collapse: collapse;
					  width: 100%;
					\">
					<thead>
					  <tr style=\"border-bottom: 1px solid #c4c4c4\">
						<th style=\"font-weight: 600; padding-bottom: 10px\">
						  Billing Details
						</th>
					  </tr>
					</thead>
					<tbody>
					  <tr>
						<td style=\"padding-top: 10px\">";
		  $message .= $user["username"];
		  $message .= "</td>
					  </tr>
					  <tr>
						<td>";
		  if (strlen($user["city"]) == 0)
			  $user["city"] = "";
		  if ($user["zipcode"] == 0)
			  $zipcode = "";
		  else
			  $zipcode = strval($user["zipcode"]);
		  $message .= $user["address"] . " ";
          $message .= $user["city"] . " - " . strval($zipcode); 
		  $message .= "</td>
					  </tr>
					</tbody>
				  </table>
				</td>
				<td style=\"width: 50%; vertical-align: top\">
				  <table
					style=\"
					  text-align: left;
					  font-size: 18px;
					  line-height: 28px;
					  border-collapse: collapse;
					  width: 100%;
					\">
					<thead>
					  <tr style=\"border-bottom: 1px solid #c4c4c4\">
						<th style=\"font-weight: 600; padding-bottom: 10px\">
						  Payment Details
						</th>
					  </tr>
					</thead>
					<tbody>
					  <tr>
						<td style=\"padding-top: 10px\">Payment Gateway: Offline
						</td>
					  </tr>
					</tbody>
				  </table>
				</td>
			  </tr>
			  <tr
				style=\"
				  display: table;
				  margin-top: 30px;
				  margin-bottom: 60px;
				  width: 50%;
				\">
				<td style=\"padding-right: 50px; vertical-align: top\">
				  <table
					style=\"
					  text-align: left;
					  font-size: 18px;
					  line-height: 28px;
					  border-collapse: collapse;
					  width: 100%;
					\">
					<thead>
					  <tr style=\"border-bottom: 1px solid #c4c4c4\">
						<th style=\"font-weight: 600; padding-bottom: 10px\">
						  Order Details
						</th>
					  </tr>
					</thead>
					<tbody>
					  <tr>
						<td style=\"padding-top: 10px\">Order no:";
		  $message .= $data['order_id'];
		  $message .= "</td>
					  </tr>
					  <tr>
						<td>Invoice no: ";
		  $message .= $data['invoice_no'];
		  $message .= "</td>
					  </tr>
					</tbody>
				  </table>
				</td>
			  </tr>
			  <tr>
				<td style=\"text-align: center\">
				  <a
					href=";
		  if ($data['plan_type'] == 1)
			  $message .= "https://www.pustaka.co.in/ebooks";
		  else
			  $message .= "https://www.pustaka.co.in/audiobooks";
		  $message .= "style=\"
					  display: inline-block;
					  font-family: Quicksand, sans-serif;
					  font-size: 16px;
					  padding: 16px 25px;
					  line-height: normal;
					  font-weight: 600;
					  text-align: center;
					  border-radius: 8px;
					  background-color: #00296b;
					  color: #fff;
					  cursor: pointer;
					  box-sizing: border-box;
					  text-decoration: none;
					  position: relative;
					  transition: all 0.15s;
					  outline: none;
					  border: 0;\">
					Rent Books Now
				  </a>
				</td>
			  </tr>
			  <tr style=\"display: table; margin: 0 auto\">
				<td style=\"text-align: center; padding-top: 20px; font-size: 20px\">
				  Notice Something Wrong?
				  <a
					href=\"https://www.pustaka.co.in/contact-us\"
					style=\"
					  font-size: 20px;
					  line-height: normal;
					  font-weight: 500;
					  color: #00296b;
					  cursor: pointer;
					  text-decoration: none;
					  transition: all 0.15s;
					\">
					Contact us</a>
				</td>
			  </tr>
			  <tr style=\"display: table; margin: 0 auto; margin-bottom: 30px\">
				<td style=\"text-align: center; padding-top: 20px; font-size: 20px\">
				  Want to Learn how it works?
				  <a href=\"https://www.pustaka.co.in/how-it-works\"
					style=\"
					  font-size: 20px;
					  line-height: normal;
					  font-weight: 500;
					  color: #00296b;
					  cursor: pointer;
					  text-decoration: none;
					  transition: all 0.15s;
					\">
					Click here</a>
				</td>
			  </tr>
			  <tr style=\"display: table; margin: 0 auto; margin-bottom: 50px\">
				<td style=\"text-align: center; padding-top: 20px\">
				<a href=\"https://apps.apple.com/us/app/pustaka-ebook-audio-print/id1627967801?uo=2\" target=\"_blank\">
				  <img src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/app-store-badge.png\"
					alt=\"Logo\"
					title=\"Logo\"
					style=\"height: 50px; margin-right: 20px\"/>
				</td>
				<td style=\"text-align: center; padding-top: 20px\">
				 <a href=\"https://play.google.com/store/apps/details?id=com.pustaka.ebooks\" target=\"_blank\">
				  <img
					src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/play-store-badge.png\"
					alt=\"Logo\"
					title=\"Logo\"
					style=\"height: 50px\"/>
				</td>
			  </tr>
			  <tr style=\"background-color: #f9f9f9\">
				<td style=\"text-align: center\">
				  <table style=\"text-align: center; padding: 20px; margin: 0 auto\">
					<tbody>
					  <tr>
						<td>
						  <a href=\"https://www.facebook.com/PustakaDigitalMedia\"
							><img
							  src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/facebook.png\"
							  style=\"width: 10px\"
						  /></a>
						</td>
						<td style=\"padding-left: 30px; padding-right: 30px\">
						  <a href=\"https://twitter.com/pustakabook\"
							><img
							  src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/twitter.png\"
							  style=\"width: 20px\"
						  /></a>
						</td>
						<td style=\"padding-right: 30px\">
						  <a href=\"https://www.instagram.com/pustaka_ebooks/\">
						  <img
							  src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/instagram.png\"
							  style=\"width: 20px\"/></a>
						</td>
						<td>
						  <a href=\"https://in.pinterest.com/pustakadigital/_created/\">
						  <img
							  src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/pinterest.png\"
							  style=\"width: 17px\"/></a>
						</td>
					  </tr>
					</tbody>
				  </table>
				  <table
					style=\"text-align: center; padding-bottom: 20px; margin: 0 auto\">
					<tbody>
					  <tr>
						<td style=\"padding-right: 30px\">
						  <a
							href=\"tel:9980387852\"
							style=\"
							  font-size: 18px;
							  color: #212121;
							  text-decoration: none;\">
							<img
							  src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/call.png\"
							  style=\"
								width: 20px;
								padding-right: 6px;
								vertical-align: sub;\"/>9980387852</a>
						</td>
						<td>
						  <a
							href=\"mailto:admin@pustaka.co.in\"
							style=\"
							  font-size: 18px;
							  color: #212121;
							  text-decoration: none;\">
							  <img
							  src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/mail.png\"
							  style=\"
								width: 20px;
								padding-right: 6px;
								vertical-align: sub;\"/>admin@pustaka.co.in</a>
						</td>
					  </tr>
					</tbody>
				  </table>
				</td>
			  </tr>
			</tbody>
		  </table>
			</body>
		  </html>";    

  $email_subject = 'Your Pustaka Purchase';

$email->setSubject($email_subject);
$email->setMessage($message);
$email->setFrom('admin@pustaka.co.in', 'Pustaka');

try {
    if ($email->send()) {
        return 1;
    } else {
        log_message('error', 'Email failed to send: ' . $email->printDebugger(['headers']));
        return 0;
    }
} catch (\Exception $e) {
    log_message('error', 'Email sending error: ' . $e->getMessage());
    return 0;
}
	}

public function checkOrCreateUser($email)
{
    $builder = $this->db->table('users_tbl');

    // Check if user exists
    $user = $builder->where('email', $email)->get()->getRowArray();

    if ($user && isset($user['user_id'])) {
        // User already exists
        return $user['user_id'];
    } else {
		 return 0;
    }
}


	public function CreateUser($email,$name,$mobile)
	{
		$builder = $this->db->table('users_tbl');

		// Check if user exists
		$user = $builder->where('email', $email)->get()->getRowArray();

		if ($user && isset($user['user_id'])) {
			// User already exists
			return $user['user_id'];
		} else {
			// Insert new user
			$builder->insert([
				'username'    => $name,
				'phone'	      =>$mobile,
				'password'    =>"97e2d1d9d9b00051bb7337d1e3013426",
			    'email'       => $email,
			    'created_at'  => date('Y-m-d H:i:s'), 
			]);
			return $this->db->insertID();
		}
	}

	public function getContactUs()
{
    $db = \Config\Database::connect();
    $builder = $db->table('contact_us as c');

    $builder->select('c.id, u.user_id, u.username, u.email, c.date_created, c.subject, c.message');
    $builder->join('users_tbl as u', 'c.user_id = u.user_id', 'left');
    $builder->orderBy('c.id', 'DESC');

    $query = $builder->get();
    return $query->getResultArray();
}
	public function deleteContactUs($id)
{
    return $this->db->table('contact_us')->delete(['id' => $id]);
}

	public function cancelSubscription()
{
    $db = \Config\Database::connect();

    // Step 1: Get the correct subscription records (your original working query)
    $cancel_sql = "
        SELECT *
        FROM (
            SELECT 
                r.*,
                ROW_NUMBER() OVER (PARTITION BY r.user_id, r.plan_id ORDER BY r.created_at ASC) AS rn
            FROM razorpay_subscription r
            WHERE 
                (r.user_id, r.plan_id) IN (
                    SELECT user_id, plan_id
                    FROM razorpay_subscription
                    WHERE cancel_flag = 0
                    GROUP BY user_id, plan_id
                    HAVING COUNT(DISTINCT razorpay_subscription_id) > 1
                )
                OR (r.razorpay_status = 'CANCEL' AND r.cancel_flag = 0)
        ) t
        WHERE t.rn = 1
        ORDER BY t.user_id, t.plan_id
    ";

    $cancel_query = $db->query($cancel_sql);
    $subscriptions = $cancel_query->getResultArray();

    // Step 2: If no results, return immediately
    if (empty($subscriptions)) {
        return [];
    }

    // Step 3: Collect all user_ids and plan_ids from the results
    $userIds = array_column($subscriptions, 'user_id');
    $planIds = array_column($subscriptions, 'plan_id');

    // Step 4: Fetch user and plan details in bulk
    $userQuery = $db->table('users_tbl')
        ->select('user_id, username')
        ->whereIn('user_id', $userIds)
        ->get()
        ->getResultArray();

    $planQuery = $db->table('plan_tbl')
        ->select('plan_id, plan_name')
        ->whereIn('plan_id', $planIds)
        ->get()
        ->getResultArray();

    // Step 5: Convert to associative arrays for fast lookup
    $users = array_column($userQuery, 'username', 'user_id');
    $plans = array_column($planQuery, 'plan_name', 'plan_id');

    // Step 6: Merge data
    foreach ($subscriptions as &$sub) {
        $sub['username'] = $users[$sub['user_id']] ?? '';
        $sub['plan_name'] = $plans[$sub['plan_id']] ?? '';
    }

    return $subscriptions;
}

public function markSubscriptionCancelled($id)
{
    $db = \Config\Database::connect();

    return $db->table('razorpay_subscription')
              ->where('razorpay_subscription_id', $id)
              ->update([
                  'cancel_flag' => 1,
                  'cancel_at'   => date('Y-m-d H:i:s')
              ]);
}

}

