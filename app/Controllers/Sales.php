<?php

namespace App\Controllers;

use App\Models\SalesModel;
use CodeIgniter\Controller;

class Sales extends BaseController
{
    protected $salesmodel;

    public function __construct()
    {
        helper(['form', 'url', 'file', 'email', 'html', 'cookie', 'string']);
        $this->salesmodel = new SalesModel();
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
}
