<?php

namespace App\Controllers;
use DateTime; 
use App\Models\UserModel;
use App\Models\BookModel;
use App\Models\PlanModel;

class User extends BaseController
{
    
    protected $userModel;
    protected $planModel;

    public function __construct()
    {
        helper(['form', 'url', 'file', 'email', 'html', 'cookie', 'text']);
        $this->userModel = new UserModel();
        $this->planModel = new PlanModel();
    }

        public function userDashboard()
    {
        $data = $this->userModel->getUserDashboardData();
        $data['contact_us'] = $this->userModel->getContactUs();
    
        $data['title'] = 'User Dashboard';
        $data['subTitle'] = 'Overview of all users';

        // echo "<pre>";
        // print_r($data);
        return view('User/userDashboard', $data);
    }


    public function getUserDetails($user_id = null)
{
    helper(['form']);

    // If called from POST search form
    $identifier = $this->request->getPost('identifier');

    if ($user_id) {
        // called via dashboard View button
        $identifier = $user_id;
    }

    if (!$identifier) {
        return redirect()->back()->with('error', 'Please provide a user identifier.');
    }

    $userModel = new UserModel();
    $planModel = new PlanModel();

    // Fetch user data
    $data['display'] = $userModel->getUserDetails($identifier);

    // SORT SUBSCRIPTIONS: Active first, latest end_date first
    if (!empty($data['display']['subscriptions'])) {
        usort($data['display']['subscriptions'], function($a, $b) {
            $today = new \DateTime();
            $statusA = ($a['end_subscribed'] < $today->format('Y-m-d') || count($a['books']) >= $a['total_books']) ? 1 : 0;
            $statusB = ($b['end_subscribed'] < $today->format('Y-m-d') || count($b['books']) >= $b['total_books']) ? 1 : 0;

            if ($statusA !== $statusB) return $statusA - $statusB;
            return strcmp($b['end_subscribed'], $a['end_subscribed']);
        });
    }

    $data['plans'] = $planModel->getUserplans();
    $data['title'] = 'User Dashboard';
    $data['subTitle'] = 'Overview of all users';

    return view('User/userDetails', $data);
}
     public function clearUserDevices()
    {
        $userId = $this->request->getPost('user_id');

        $userModel = new \App\Models\UserModel();
        $result = $userModel->clearUserDevices($userId);

        return $this->response->setJSON(['status' => $result ? 1 : 0]);
    }

    public function addPlanForUser()
    {
        try {
            $userId = $this->request->getPost('user_id');
            $planId = $this->request->getPost('plan_id');

            $userModel = new \App\Models\UserModel();
            $result = $userModel->add_plan($userId, $planId);

            return $this->response->setJSON([
                'status' => $result ? 1 : 0,
                'message' => $result ? 'Plan added successfully.' : 'Plan add failed.'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 0,
                'message' => 'Exception: ' . $e->getMessage()
            ]);
        }
    }
    
    public function authorGiftBooks(){

        $bookModel = new BookModel();
        $data['title'] = 'Gift Books';
        $data['subTitle'] = 'Author Gift Books';

        $data['books'] = $bookModel->getBooksDetails();

        return view('author/giftbook', $data);
    }

    public function checkOrCreate() 
{
    $data = $this->request->getJSON(true);
    $email = $data['email'] ?? null;

    if (!$email) {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Email required'
        ]);
    }

    $userModel = new UserModel();
    $userId = $userModel->checkOrCreateUser($email);

    return $this->response->setJSON([
        'status'  => 'success',
        'user_id' => $userId
    ]);
}

    public function CreateUser()
    {
        $request = $this->request->getJSON(true); // Get JSON POST data

        $email  = isset($request['email']) ? trim($request['email']) : '';
        $name   = isset($request['name']) ? trim($request['name']) : '';
        $mobile = isset($request['mobile']) ? trim($request['mobile']) : '';

        if (!$email || !$name || !$mobile) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Email, Name, and Mobile are required.'
            ], 400);
        }

        $userModel = new UserModel();
        $userId = $userModel->CreateUser($email,$name,$mobile);
        if ($userId) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User created successfully',
                'user_id' => $userId
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to create user'
            ]);
        }

    }


   public function submitGiftBook()
    {
        $data = $this->request->getJSON(true);

        $email      = $data['email'] ?? null;
        $userId     = $data['user_id'] ?? null;
        $bookId     = $data['book_id'] ?? null;
        $bookTitle  = $data['book_title'] ?? null;
        $authorId   = $data['author_id'] ?? null;
        $authorName = $data['author_name'] ?? null;

        if (!$userId || !$bookId || !$authorId || !$email) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Missing required fields.'
            ]);
        }

        // Insert into DB
        $db = \Config\Database::connect();

         //  Fetch username from users table
            $userQuery = $db->table('users_tbl')
                ->select('username')
                ->where('user_id', $userId)
                ->get()
                ->getRow();

            $username = $userQuery ? $userQuery->username : 'Reader';


        $builder = $db->table('author_gift_books');

        $insertData = [
            'user_id'   => $userId,
            'book_id'   => $bookId,
            'author_id' => $authorId,
            'date'      => date('Y-m-d H:i:s'),
        ];

        if ($builder->insert($insertData)) {
            //call email function after successful insert
            $sendStatus = $this->sendEbookGiftEmail($email, $username,$authorName,$bookTitle);


            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Gift book inserted successfully & email sent!',
                'id'      => $db->insertID(),
                'email_status' => $sendStatus
            ]);
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Failed to insert gift book.'
            ]);
        }
    }

       public function deleteContactUs($id)
{
    $userModel = new \App\Models\UserModel();

    // Delete contact by ID
    $userModel->deleteContactUs($id);

    // Redirect to the route
    return redirect()->to(base_url('user/userdashboard'))
                     ->with('message', 'Contact deleted successfully.');
}

function sendEbookGiftEmail($recipientEmail, $recipientName, $authorName, $bookTitle)
{
    $email = \Config\Services::email();

    $message = "<html lang=\"en\">
        <head>
            <meta charset=\"utf-8\"/>
            <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />
            <meta name=\"x-apple-disable-message-reformatting\" />
            <title></title>
            <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\" />
            <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin />
            <link href=\"https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap\" rel=\"stylesheet\"/>
        </head>
        <body style=\"
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #000000;
            font-family: 'Quicksand', sans-serif;
            font-size: 16px;\">
            <table style=\"
                max-width: 850px;
                min-width: 350px;
                margin: 0 auto;
                padding: 20px;\"
                cellpadding=\"0\"
                cellspacing=\"0\">
                <tbody>
                    <tr style=\"
                        background: linear-gradient(135deg, #4685ec 0%, #00296b 100%);
                        \">
                        <td style=\"padding: 30px; text-align: center\">
                            <img src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/pustaka-logo-white-3x.png\"
                            alt=\"Pustaka Logo\" title=\"Pustaka\" style=\"width: 33%; max-width: 170px;\"/>
                        </td>
                    </tr>

                    <tr>
                        <td style=\"padding: 20px\">
                            <h2 style=\"text-align: center; font-weight: 600; font-size: 28px; margin: 20px 0;\">Gifted Ebook from Author</h2>
                            <p style=\"font-size: 18px; line-height: 28px;\">Dear " . (!empty($recipientName) ? $recipientName : "Sir/Madam") . ",</p>
                            <p style=\"font-size: 18px; line-height: 28px;\">At the request of $authorName, we have added the following ebooks as a complimentary gift from the author:</p>
                            <ol style=\"font-size: 18px; line-height: 28px; padding-left: 25px; list-style-type: none;\">
                                <li>$bookTitle</li>
                            </ol>

                            <p style=\"font-size: 18px; line-height: 28px;\">
                                If you are a new user, we have already registered your Email-Id with<br/>
                                Your default password is <b>pustaka123</b> (you can change it using <i>Forgot Password</i> option).
                            </p>
                            <p style=\"font-size: 18px; line-height: 28px;\">
                                If you are an existing user, please use your existing credentials.<br/>
                            </p>

                            <p style=\"font-size: 18px; line-height: 28px;\">
                                You can also log in using Google ID so that you don’t have to remember the password.
                            </p>

                            <h3 style=\"margin-top: 25px; font-size: 22px;\">How to Read on Laptop/Browser:</h3>
                            <ol style=\"font-size: 18px; line-height: 28px; padding-left: 25px;\">
                                <li>Go to <a href=\"https://www.pustaka.co.in\" target=\"_blank\">www.pustaka.co.in</a></li>
                                <li>Click <b>Login</b> on the top right corner and provide your email ID and password</li>
                                <li>Click <b>My Library</b> on the top menu</li>
                                <li>Click the book cover to open and read</li>
                            </ol>

                            <h3 style=\"margin-top: 25px; font-size: 22px;\">How to Read on Mobile App:</h3>
                            <ol style=\"font-size: 18px; line-height: 28px; padding-left: 25px;\">
                                <li>Install the mobile app from Play Store or App Store</li>
                                <li>Android: <a href=\"https://play.google.com/store/apps/details?id=com.pustaka.ebooks\">Download Here</a></li>
                                <li>iOS: <a href=\"https://apps.apple.com/us/app/pustaka-ebook-audio-print/id1627967801\">Download Here</a></li>
                                <li>Login with your email ID and password</li>
                                <li>Click <b>My Library</b> and then the book cover to open</li>
                            </ol>

                            <p style=\"font-size: 18px; line-height: 28px;\">Please contact us if you have any questions.</p>

                            <p style=\"font-size: 18px; line-height: 28px;\">Regards,<br/>Pustaka Support</p>
                        </td>
                    </tr>
                    
                    <!-- App Store & Play Store Badges -->
                    <tr>
                        <td style=\"text-align: center; padding-top: 20px\">
                            <a href=\"https://apps.apple.com/us/app/pustaka-ebook-audio-print/id1627967801\" target=\"_blank\" rel=\"noopener noreferrer\">
                                <img
                                src=\"https://d290ueh5ca9g3w.cloudfront.net/images/app-store-badge.png\"
                                alt=\"App Store\"
                                style=\"height: 50px; margin-right: 20px\"/>
                            </a>
                            <a href=\"https://play.google.com/store/apps/details?id=com.pustaka.ebooks\" target=\"_blank\" rel=\"noopener noreferrer\">
                                <img
                                    src=\"https://d290ueh5ca9g3w.cloudfront.net/images/play-store-badge.png\"
                                    alt=\"Play Store\"
                                    style=\"height: 50px\"/>
                            </a>
                        </td>
                    </tr>

                    <!-- Social Media Icons -->
                    <tr style=\"background-color: #f9f9f9\">
                        <td style=\"text-align: center; padding: 20px;\">
                            <table align=\"center\" cellpadding=\"10\" cellspacing=\"0\">
                                <tr>
                                    <td>
                                        <a href=\"https://www.facebook.com/PustakaDigitalMedia\">
                                            <img src=\"https://d290ueh5ca9g3w.cloudfront.net/images/facebook.png\" alt=\"Facebook\" style=\"width: 20px\"/>
                                        </a>
                                    </td>
                                    <td>
                                        <a href=\"https://twitter.com/pustakabook\">
                                            <img src=\"https://d290ueh5ca9g3w.cloudfront.net/images/twitter.png\" alt=\"Twitter\" style=\"width: 20px\"/>
                                        </a>
                                    </td>
                                    <td>
                                        <a href=\"https://www.instagram.com/pustaka_ebooks/\">
                                            <img src=\"https://d290ueh5ca9g3w.cloudfront.net/images/instagram.png\" alt=\"Instagram\" style=\"width: 20px\"/>
                                        </a>
                                    </td>
                                    <td>
                                        <a href=\"https://in.pinterest.com/pustakadigital/_created/\">
                                            <img src=\"https://d290ueh5ca9g3w.cloudfront.net/images/pinterest.png\" alt=\"Pinterest\" style=\"width: 20px\"/>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </body>
    </html>";

    $email->setFrom('admin@pustaka.co.in', 'Pustaka Admin');
    $email->setTo($recipientEmail);
    $email->setCC('admin@pustaka.co.in');
    $email->setSubject('Gifted Ebook from Author');
    $email->setMessage($message);

    $sent = $email->send();

    return $sent ? 'Success' : $email->printDebugger(['headers', 'subject']);
}

public function cancelSubscription()
{
    $data['cancel'] = $this->userModel->cancelSubscription();
    $data['cancel_count'] = count($data['cancel']);
    $this->session->set('cancel_count', $data['cancel_count']);

    $data['title'] = '';
    $data['subTitle'] = '';

    // echo "<pre>";
    // print_r($data['cancel']);

    return view('User/CancelSubscription',$data);

}

    public function markSubscriptionCancelled($id)
    {
        $userModel = new \App\Models\UserModel();

        $userModel->markSubscriptionCancelled($id);

        return redirect()->to(base_url('user/cancelsubscription'))
                        ->with('message', 'subscription mark cancel successfully.');
    }



}

