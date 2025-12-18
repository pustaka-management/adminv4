<?php

namespace App\Controllers;

use App\Models\SalesModel;
use App\Models\EbookSalesModel;
use CodeIgniter\Controller;

class Sales extends BaseController
{
    protected $salesmodel;
    protected $ebooksalesmodel;

    public function __construct()
    {
        helper(['form', 'url', 'file', 'email', 'html', 'cookie', 'string']);
        $this->salesmodel = new SalesModel();
        $this->ebooksalesmodel = new EbookSalesModel();
    }

    public function salesdashboard()
    {
        $data['total'] = $this->salesmodel->salesDashboardDetails();
        $data['channelwise'] = $this->salesmodel->channelwisesales();
        $data['pustaka'] = $this->salesmodel->paperbackDetails();
        $data['pod'] = $this->salesmodel->podsalesDetails();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('sales/newsalesDashboard', $data);
    }

    public function salesreports()
    {
        $this->salesmodel = new \App\Models\SalesModel();
        $data['over_all'] = $this->salesmodel->getOverallSales();
        $data['channelwise'] = $this->salesmodel->channelwisesales();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('sales/salesdetails', $data);
    }

    public function ebooksales()
    {
        $data['ebook'] = $this->salesmodel->ebooksalesDetails();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('sales/ebookSalesDetails', $data);
    }

    public function audiobooksales()
    {
        $data['audiobook'] = $this->salesmodel->audiobookSalesDetails();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('sales/audioBookSalesdetails',$data);
    }

    public function paperbacksales()
    {
        $data['pustaka'] = $this->salesmodel->paperbackDetails();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('sales/paperbackSalesDetails',$data);
    }

    public function amazonpaperback()
    {
        $data['amazonpaperback'] = $this->salesmodel->amazonpaperbackDetails();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('sales/amazon/amazonPaperbackDetails',$data);
    }
    public function amazonpaperbackrevenue()
    {
        $data['paperback_revenue'] = $this->salesmodel->amazonPaperbackRevenueDetails();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('sales/amazon/amazonPaperbackRevenueDetails',$data);
    
    }
    public function amazonpbkbookdetails($book_id)
    {
        $data['paperback_bookdetails'] = $this->salesmodel->getAmazonPaperbackBookDetails($book_id);
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('sales/amazon/amazonBookwiseDetails',$data);
    }
   public function EbookAmazondetails()
{
    $model = new EbookSalesModel();

    $data['summary'] = $model->getAmazonEbookSummary();

    // Top 10 selling and returned books
    $books = $model->getTopSellingAndReturnedBooks(10);
    $data['top_selling_books']  = $books['top_selling_books'];
    $data['top_returned_books'] = $books['top_returned_books'];

    $data['title']    = 'Amazon E-Book';
    $data['subTitle'] = 'Sales Summary';

    return view('sales/ebook/ebookAmazonDetails', $data);
}

public function EbookOverdriveDetails()
{
    $model = new EbookSalesModel();

    $data = [
        'title'        => 'OverDrive eBook Dashboard',
        'subTitle'     => 'Sales Summary',
        'summary'      => $model->getOverdriveEbookSummary(),
        'topBooks'     => $model->getTopSellingOverdriveBooks(10),
        'topRetailers' => $model->getTopOverdriveRetailers(10),
    ];

    return view('sales/ebook/ebookOverdriveDetails', $data);
}



}
