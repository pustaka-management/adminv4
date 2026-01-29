<?php

namespace App\Controllers;

use App\Models\RoyaltyModel;
use CodeIgniter\Controller;
use App\Controllers\BaseController;

class Royalty extends BaseController
{
    protected $royaltyModel;
    protected $helpers = ['form', 'url', 'file', 'email', 'html', 'cookie', 'string'];

    public function __construct()
    {
        helper($this->helpers);

        $this->royaltyModel = new RoyaltyModel();
        session();
    }

	public function index()
	{
		$data = [];
		$data['title'] = 'Royalty Dashboard';
		$data['subTitle'] = '';
		$data['overall'] = $this->royaltyModel->getdashboardOverallData();
		$data['quarterly'] = $this->royaltyModel->getdashboardquarterData();

		$data['consolidation']=$this->royaltyModel->getTopayQuarterData();

		// echo "<pre>";
		// print_r( $data['consolidation']);

		return view('royalty/RoyaltyDashboard',$data);
	}

    public function getroyaltybreakup($copyright_owner)
    {
        $data['title'] = '';
        $data['subTitle'] = 'Breakup Royalty Summary';

		$builder = $this->db->table('site_config'); // Query builder from $this->db
		$builder->where('category', 'prevmonth');
		$query = $builder->get();
		$site_config_data = $query->getRowArray();

		$prev_month_end=$site_config_data['value'];
		
        $data['ebook_details'] = $this->royaltyModel->getebookbreakupDetails($copyright_owner,$prev_month_end);
        $data['audiobook_details'] = $this->royaltyModel->getaudiobreakupDetails($copyright_owner,$prev_month_end);
        $data['paperback_details'] = $this->royaltyModel->getpaperbackDetails($copyright_owner,$prev_month_end);
        $data['author_id'] = $copyright_owner;
        $data['details']= $this->royaltyModel->publisherDetails($copyright_owner);
		$data['summary'] = $this->royaltyModel->getRoyaltyConsolidatedDataByCopyrightOwner($copyright_owner);

        return view('royalty/royaltybreakupview', $data);
        
    }

    public function transactiondetails()
    {
        $data = [];
        $data['title'] = 'Transaction Details';
        $data['subTitle'] = '';

        $bookType = $this->request->getGet('book_type');
        $year = $this->request->getGet('year');
        $months = $this->request->getGet('months'); 
        $status = $this->request->getGet('status') ?? ''; 

        $transactions = [];

        if ($bookType && $year && is_array($months)) {
            if ($bookType === 'ebook') {
                $allData = $this->royaltyModel->getEbookDetails($status);
            } else {
                $allData = $this->royaltyModel->getAudioBookDetails($status);
            }

            foreach ($months as $month) {
                $month = (int) $month;
                $monthName = date('F', mktime(0, 0, 0, $month, 10)); 
                $key = "{$monthName}-{$year}"; 

                if (isset($allData[$key])) {
                    $monthData = $allData[$key];

                    foreach ($monthData as $channel => $value) {
                        $transactions[$key][$channel] = [
                            'revenue' => isset($value['revenue']) ? (float) $value['revenue'] : 0,
                            'royalty' => isset($value['royalty']) ? (float) $value['royalty'] : 0
                        ];
                    }
                }
            }
        }

        $data['transactions'] = $transactions;

        return view('royalty/transactionDetails', $data);
    }

	public function paynow() 
	{
        $copyright_owner = $this->request->getPost('copyright_owner');

        // $copyright_owner =4843;

        if ($copyright_owner) {
            $builder = $this->db->table('site_config'); // Query builder from $this->db
            $builder->where('category', 'prevmonth');
            $query = $builder->get();
            $site_config_data = $query->getRowArray();

            $time = strtotime($site_config_data['value']);
            $month_end = date('d-m-Y', $time);
		
            $paynow_data = $this->royaltyModel->getRoyaltyConsolidatedDataByCopyrightOwner($copyright_owner);
            // echo "<pre>";
            // print_r($paynow_data);

            $site_config = $this->royaltyModel->getSiteConfig();
            // print_r($site_config);

			

			$results = [
			$this->sendRoyaltySettlementEmail($copyright_owner, $paynow_data, $site_config),
            $this->royaltyModel->updateRoyaltySettlement($copyright_owner,$paynow_data, $site_config),
            $this->royaltyModel->markRoyaltyConsolidationToPaid($copyright_owner, $month_end),
            $this->royaltyModel->markPustakaToPaid($copyright_owner, $month_end),
            $this->royaltyModel->markAmazonToPaid($copyright_owner, $month_end),
            $this->royaltyModel->markScribdToPaid($copyright_owner, $month_end),
            $this->royaltyModel->markGoogleToPaid($copyright_owner, $month_end),
            $this->royaltyModel->markOverdriveToPaid($copyright_owner, $month_end),
            $this->royaltyModel->markStoryTelToPaid($copyright_owner, $month_end),
            $this->royaltyModel->markPratilipiToPaid($copyright_owner, $month_end),
            $this->royaltyModel->markAudibleToPaid($copyright_owner, $month_end),
            $this->royaltyModel->markKukufmToPaid($copyright_owner, $month_end),
            $this->royaltyModel->markYoutubeToPaid($copyright_owner, $month_end),
            $this->royaltyModel->markKoboToPaid($copyright_owner, $month_end),
        ];

			// Check if all are "Success"
			$allSuccess = !in_array(false, array_map(function($res) {
				return trim(strtolower($res)) === 'success';
			}, $results), true);

			// Pass result to view
			return view('royalty/paynowprocessing', ['allSuccess' => $allSuccess]);

		} else {
			session()->setFlashdata('error', 'Invalid Request.');
			return redirect()->to(base_url('royalty/royaltyconsolidation'));
		}
    }


		// 🔧 MailHog config for local testing
		// $config['protocol']   = 'smtp';
		// $config['smtp_host']  = 'localhost';
		// $config['smtp_port']  = 1025;
		// $config['mailtype']   = 'html';
		// $config['charset']    = 'UTF-8';

    function sendRoyaltySettlementEmail($copyright_owner, $paynow_data, $site_config)
   {
    
		$email = \Config\Services::email();

		
		 
		$message ="<html lang=\"en\">
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
			<body style=\"
				margin: 0;
				padding: 0;
				background-color: #ffffff;
				color: #000000;
				font-family: 'Quicksand', sans-serif;
				font-size: 16px;\"
				data-new-gr-c-s-check-loaded=\"14.1052.0\"
				data-gr-ext-installed=\"\">
				<table class=\"main-table\" style=\"
					max-width: 850px;
					min-width: 350px;
					margin: 0 auto;
					padding-left: 20px;
					padding-right: 20px;\"
					cellpadding=\"0\"
					cellspacing=\"0\">
					<tbody>
						<tr style=\"
							background: linear-gradient(0deg,
							rgba(0, 41, 107, 0.2),
							rgba(0, 41, 107, 0.2)),
							linear-gradient(135deg, #4685ec 0%, #00296b 100%);\">
							<td style=\"padding: 40px 0px; text-align: center\">
								<img src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/pustaka-logo-white-3x.png\"
								alt=\"Logo\"
								title=\"Logo\"
								style=\"display: inline-block !important;
									width: 33%;
									max-width: 174.9px;\"/>
							</td>
						</tr>
						<tr>
							<td style=\"text-align: center\">
							<h1 style=\"text-align: center;
								word-wrap: break-word;
								font-weight: 600;
								font-size: 36px;
								margin-top: 30px;
								margin-bottom: 30px;\">
								Your Royalty Settlement
							</h1>
							</td>
						</tr>
						<tr>
						<td style=\"text-align: right\">
							<p style=\"font-size: 18px; line-height: 28px; margin: 0\">";
			$message .= "Date: " . date('d/M/Y');
			$message .= "</p>
							<p style=\"font-size: 18px; line-height: 28px; margin: 0\"></p>
						</td>
						</tr>
						<tr>
							<td style=\"text-align: left; padding-top: 30px; padding-bottom: 20px\">
							<p style=\"font-size: 18px; line-height: 28px\">";
			$message .="<p>Dear ". $paynow_data['publisher_name'];
			$message .= ",</p>
							<p>Greetings!!!</p>
							<p>Thanks for your association with Pustaka. Since your royalty had exceeded the minimum payout of INR 500.00, we have done the royalty settlement for you.</p>
							<p style=\"font-size: 18px; line-height: 28px\">Here are the details of payout:</p>
							<p style=\"font-size: 22px; line-height: 28px\">Settlement as of:";
			$tmp_date = $site_config['settlement_date'];
			$message .= $tmp_date;
			$message .= "</p></td>
						</tr>
						<tr>
							<td>
							<table style=\"
									width: 100%;
									text-align: left;
									border-collapse: collapse;
									font-size: 18px;
									line-height: 28px;\">
								<thead>
									<tr style=\"border-bottom: 1px solid #c4c4c4\">
									<th style=\"
										font-weight: 400;
										font-weight: 500;
										font-size: 18px;
										padding-bottom: 10px;\">
									Type of Book
									</th>
									<th style=\"
										font-weight: 500;
										font-size: 18px;
										padding-bottom: 10px;
										text-align: right;\">
									Royalty
									</th>
									</tr>
								</thead>
								<tbody style=\"border-bottom: 1px solid #c4c4c4; padding-bottom: 30px\">
									<tr>
										<td style=\"padding-bottom: 15px; padding-top: 15px\">
										<div>
											<div style=\"display: inline; margin-left: 15px\">
												<div style=\"font-size: 22px\">
													Ebooks
												</div>
												<p style=\"margin: 0; color: #212121; font-size: 15px\">&ensp;Pustaka: ";
			$message .= "&#8377;" . number_format($paynow_data['pustaka_ebooks'],2);
			$message .= "<br>&ensp;Amazon: ";			  
			$message .= "&#8377;" . number_format($paynow_data['amazon_ebooks'],2);
			$message .= "<br>&ensp;Scribd: ";
			$message .= "&#8377;" . number_format($paynow_data['scribd_ebooks'],2);
			$message .= "<br>&ensp;Overdrive: ";
			$message .= "&#8377;" . number_format($paynow_data['overdrive_ebooks'],2);
			$message .= "<br>&ensp;Google: ";
			$message .= "&#8377;" . number_format($paynow_data['google_ebooks'],2);
			$message .= "<br>&ensp;StoryTel: ";
			$message .= "&#8377;" . number_format($paynow_data['storytel_ebooks'],2);
			$message .= "<br>&ensp;Pratilipi: ";
			$message .= "&#8377;" . number_format($paynow_data['pratilipi_ebooks'],2);
			$message .= "<br>&ensp;Kobo: ";
			$message .= "&#8377;" . number_format($paynow_data['kobo_ebooks'],2);
			$message .= "</p>
											</div>
										</div>
										</td>
										<td style=\"text-align: right\">";
			$message .= "&#8377;" . number_format($paynow_data['ebooks_outstanding'],2);
			$message .= "</td>
									</tr>
									<tr>
										<td style=\"padding-bottom: 15px; padding-top: 15px\">
											<div>
												<div style=\"display: inline; margin-left: 15px\">
													<div style=\"font-size: 22px\">
														Audiobooks
													</div>
													<p style=\"margin: 0; color: #212121; font-size: 15px\">&ensp;Pustaka: ";
			$message .= "&#8377;" . number_format($paynow_data['pustaka_audiobooks'],2);
			$message .= "<br>&ensp;Audible: ";			  
			$message .= "&#8377;" . number_format($paynow_data['audible_audiobooks'],2);
			$message .= "<br>&ensp;Overdrive: ";
			$message .= "&#8377;" . number_format($paynow_data['overdrive_audiobooks'],2);
			$message .= "<br>&ensp;Google: ";
			$message .= "&#8377;" . number_format($paynow_data['google_audiobooks'],2);
			$message .= "<br>&ensp;StoryTel: ";
			$message .= "&#8377;" . number_format($paynow_data['storytel_audiobooks'],2);
			$message .= "<br>&ensp;KukuFM: ";
			$message .= "&#8377;" . number_format($paynow_data['kukufm_audiobooks'],2);
			$message .= "<br>&ensp;YouTube: ";
			$message .= "&#8377;" . number_format($paynow_data['youtube_audiobooks'],2);
			$message .= "</p>
												</div>
											</div>
										</td>
										<td style=\"text-align: right\">";
			$message .= "&#8377;" . number_format($paynow_data['audiobooks_outstanding'],2);
			$message .= "</td>
									</tr>";

			if ($paynow_data['bonus_percentage']!=0)
			{
				$message .= "<tr>
										<td style=\"padding-bottom: 15px; padding-top: 15px\">
										<div>
											<div style=\"display: inline; margin-left: 15px\">
												<div style=\"font-size: 22px\">
													Bonus @ ";
				$message .= $paynow_data['bonus_percentage'] . "%";
				$message .= "</div><p style=\"margin: 0; color: #212121; font-size: 15px\">&ensp;Only for ebooks & audiobooks ";
				$message .= "<br>&ensp;Ebooks: ";
				$message .= "&#8377;" . number_format($paynow_data['ebooks_outstanding'],2);
				$message .= "&ensp;Audiobooks: ";			  
				$message .= "&#8377;" . number_format($paynow_data['audiobooks_outstanding'],2);
				$message .= "<br>&ensp;Total: ";
				$message .= "&#8377;" . number_format($paynow_data['bonus_value'],2);
				$message .= "</p>
											</div>
										</div>
										</td>
										<td style=\"text-align: right\">";

				$message .= "&#8377;" . number_format($paynow_data['bonus_value'],2);
				$message .= "</td>
									</tr>";
			}
				$message .= "<tr>
									<td style=\"padding-bottom: 15px; padding-top: 15px\">
										<div>
											<div style=\"display: inline; margin-left: 15px\">
												<div style=\"font-size: 22px\">
													Paperback
												</div>
											<p style=\"margin: 0; color: #212121; font-size: 15px\">&ensp;Pustaka Paperback Consolidated: ". number_format($paynow_data['paperback_amount'],2);
				$message .= "</p>
			
											</div>
										</div>
										</td>
										<td style=\"text-align: right\">";

			$message .= "&#8377;" . number_format($paynow_data['paperback_amount'],2);
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
							border-collapse: collapse;\">
							<tbody>
							<tr>
								<td style=\"font-weight: 600; padding-bottom: 15px\">
									Subtotal:
								</td>
								<td style=\"text-align: right\">";
			$message .= "&#8377;" . number_format($paynow_data['total_outstanding'],2);
			$message .= "</td>
							</tr>
							<tr>
							<td style=\"font-weight: 600; padding-bottom: 15px\">
								TDS Deductions:
							</td>
							<td style=\"text-align: right\">";
			$message .= "&#8377;" . number_format($paynow_data['tds_value'],2);
			if (!empty($paynow_data['excess_payment']) && $paynow_data['excess_payment'] > 0) {
			$message .= "</td>
							</tr>
							<tr>
							<td style=\"font-weight: 600; padding-bottom: 15px\">
								Other Deductions:
							</td>
							<td style=\"text-align: right\">";
			$message .= "&#8377;" . number_format($paynow_data['excess_payment'],2);
			}
			$message .= "
						</td>
						</tr>
						<tr style=\"
							border-bottom: 1px solid #c4c4c4;
							border-top: 1px solid #c4c4c4;\">
						<td style=\"
							font-weight: 600;
							color: #00296b;
							padding-bottom: 15px;
							padding-top: 15px;\">
							Total Amount Paid: 
						</td>
						<td style=\"text-align: right; color: #00296b; font-weight: 600\">";
			$message .= "&#8377;" . number_format($paynow_data['total_after_tds'],2);
			$message .= "</td>
						</tr>
					</tbody>
				</table>
				</td>
				</tr>

				<tr style=\"display: table; margin-top: 20px; margin-bottom: 10px;\">
					<td style=\"width: 100%; vertical-align: top;\">
						<table style=\"
							text-align: left;
							font-size: 18px;
							line-height: 28px;
							border-collapse: collapse;
							width: 100%;\">
			                <thead>";
							if (!empty($paynow_data['excess_payment']) && $paynow_data['excess_payment'] > 0) {
							$message .= "<th style=\"font-weight: 600; padding-bottom: 10px;\">Note For Other Deductions:</th>
											<td style=\"font-weight: 500; padding-bottom: 10px;\">";
							$message .=   $site_config['excess_commands'];;
								}
			                $message .= "</td>
							</thead>
						</table>
					</td>
				</tr>

				<tr style=\"display: table; margin-bottom: 50px; margin-top: 10px\">
				<td style=\"width: 100vw; vertical-align: top\">
					<table
						style=\"
						text-align: left;
						font-size: 18px;
						line-height: 28px;
						border-collapse: collapse;
						width: 100%;\">
						<thead>
						<tr style=\"border-bottom: 1px solid #c4c4c4\">
							<th style=\"font-weight: 600; padding-bottom: 10px\">Deposit details:</th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td style=\"font-weight: 500; padding-top: 10px\">
								Bank Account Name:";
			$message .= $paynow_data['bank_acc_name'];
			$message .= "	</td>
						</tr>
						<tr>
							<td style=\"font-weight: 500; padding-top: 10px\">
								Bank Account Number:";
			$message .= $paynow_data['bank_acc_no'];
			$message .= "	</td>
						</tr>
						<tr>
							<td style=\"font-weight: 500; padding-top: 10px\">
								IFSC Code:";
			$message .= $paynow_data['ifsc_code'];
			$message .= "	</td>
						</tr>
						<tr>
							<td style=\"font-weight: 500; padding-top: 10px\">
								Mode of transfer:";
			$message .= $site_config['settlement_type'];
			$message .= "	</td>
						</tr>
						<tr>
							<td style=\"font-weight: 500; padding-top: 10px\">
								Transfer details:";
			$message .= $site_config['settlement_bank_transaction'];
			$message .= "	</td>
						</tr>
						</tbody>
					</table>
					</td>
				</tr>
				<tr>
				<td style=\"text-align: center\">
				<a
					href=\"https://dashboard.pustaka.co.in\"
					style=\"
					margin-bottom: 20px;
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
					border: 0;
					\">
					Visit Dashboard for more details
				</a>
				</td>
			</tr>
			<tr style=\"background-color: #f9f9f9\">
				<td style=\"text-align: center\">
				<table style=\"text-align: center; padding: 20px; margin: 0 auto\">
					<tbody>
					<tr>
						<td>
						<a href=\"https://www.facebook.com/PustakaDigitalMedia\"
							><img src=\"https://d290ueh5ca9g3w.cloudfront.net/images/facebook.png\" style=\"width: 10px\"
						/></a>
						</td>
						<td style=\"padding-left: 30px; padding-right: 30px\">
						<a href=\"https://twitter.com/pustakabook\"
							><img src=\"https://d290ueh5ca9g3w.cloudfront.net/images/twitter.png\" style=\"width: 20px\"
						/></a>
						</td>
						<td style=\"padding-right: 30px\">
						<a href=\"https://www.instagram.com/pustaka_ebooks/\"
							><img src=\"https://d290ueh5ca9g3w.cloudfront.net/images/instagram.png\" style=\"width: 20px\"
						/></a>
						</td>
						<td>
						<a href=\"https://in.pinterest.com/pustakadigital/_created/\"
							><img src=\"https://d290ueh5ca9g3w.cloudfront.net/images/pinterest.png\" style=\"width: 17px\"
						/></a>
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
							text-decoration: none;
							\"><img
							src=\"https://d290ueh5ca9g3w.cloudfront.net/images/call.png\"
							style=\"
								width: 20px;
								padding-right: 6px;
								vertical-align: sub;
							\"/>9980387852</a>
						</td>
						<td>
						<a
							href=\"mailto:accounts@pustaka.co.in\"
							style=\"
							font-size: 18px;
							color: #212121;
							text-decoration: none;
							\"><img
							src=\"https://d290ueh5ca9g3w.cloudfront.net/images/mail.png\"
							style=\"
								width: 20px;
								padding-right: 6px;
								vertical-align: sub;
							\"/>accounts@pustaka.co.in</a>
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

			

			$email->setFrom('accounts@pustaka.co.in', 'Pustaka Accounts');
			$email->setTo($paynow_data['email_id']);
			// $email->setTo('sinekarajendran03@gmail.com');
			$email->setCC('accounts@pustaka.co.in');
			$email->setSubject('Your Royalty Details');
			$email->setMessage($message);
			$sent = $email->send();

			if ($sent) {
				return 'Success';
			} else {
				return $email->printDebugger(['headers', 'subject']);
			}


    } 
	
	public function royaltyquaterlyreport($channel = 'ebook')
	{
		$data = [];
		$data['title'] = 'Royalty Quarterly Report';
		$data['subTitle'] = '';

		$data['royalty_report'] = $this->royaltyModel->getRoyaltyConsolidatedQuarterData();
		$data['channel'] = strtolower($channel);
		$data['type'] = 'quarterly';	

		
		// echo "<pre>"; 
		// print_r($data['royalty_report']);

		return view('royalty/royaltytypewiseview', $data);
	}

	public function royaltyquaterfullreport()
	{
		$data = [];
		$data['title'] = 'Royalty Quarterly Full Report';
		$data['subTitle'] = '';

		$royaltyResult = $this->royaltyModel->getRoyaltyConsolidatedQuarterData();

		// Separate total bonus and royalty data
		$total_bonus_sum = $royaltyResult['total_bonus_sum'] ?? 0;
		unset($royaltyResult['total_bonus_sum']);

		$data['type'] = 'quarterly';
		$data['royalty'] = $royaltyResult; // all the royalty data rows
		$data['total_bonus_sum'] = $total_bonus_sum;


		// echo "<pre>"; 
		// print_r($data['royalty']);

		return view('royalty/royaltyconsolidationview', $data);
	}

	public function royaltyconsolidationreport($channel = 'ebook')
	{
		$data = [];
		$data['title'] = 'Royalty Quarterly Report';
		$data['subTitle'] = '';

		$data['royalty_report'] = $this->royaltyModel->getRoyaltyConsolidatedData();
		$data['channel'] = strtolower($channel);
		$data['type'] = 'consolidation';
		
		// echo "<pre>"; 
		// print_r($data['royalty_report']);

		return view('royalty/royaltytypewiseview', $data);
	}
  
	  public function royaltyconsolidation()
    {
        $data['title'] = 'Royalty Consolidation';
        $data['subTitle'] = 'Outstanding Royalty Summary';
		

		$royaltyResult = $this->royaltyModel->getRoyaltyConsolidatedData();

		// Separate total bonus and royalty data
		$total_bonus_sum = $royaltyResult['total_bonus_sum'] ?? 0;
		unset($royaltyResult['total_bonus_sum']);

		$data['type'] = 'consolidation';
		$data['royalty'] = $royaltyResult; // all the royalty data rows
		$data['total_bonus_sum'] = $total_bonus_sum;



        // echo "<pre>";
        // print_r($data['royalty'] );
        return view('royalty/royaltyconsolidationview', $data);
    }


}

