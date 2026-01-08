<?php

namespace App\Controllers;

use App\Models\SalesModel;
use App\Models\EbookSalesModel;
use App\Models\AudiobookSalesModel;
use CodeIgniter\Controller;

class Sales extends BaseController
{
    protected $salesmodel;
    protected $ebooksalesmodel;
    protected $audiobooksalesmodel;

    public function __construct()
    {
        helper(['form', 'url', 'file', 'email', 'html', 'cookie', 'string']);
        $this->salesmodel = new SalesModel();
        $this->ebooksalesmodel = new EbookSalesModel();
        $this->audiobooksalesmodel = new AudiobookSalesModel();
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
    public function offlinesales()
    {
        $chartFilter = $this->request->getGet('chart_filter') ?? 'all';

        $offline_sales = $this->salesmodel->offlinesalesDetails($chartFilter);

        return view('sales/offline/offlineSalesDetails', [
            'offline_sales' => $offline_sales,
            'chart_filter'  => $chartFilter,
            'title'         => '',
            'subTitle'      => ''
        ]);
    }
    public function bookshopsales()
    {
        $chart_filter = $this->request->getGet('chart_filter') ?? 'all';
        $data['chart_filter'] = $chart_filter;
        $data['bookshop_sales'] = $this->salesmodel->bookShopPaperbackDetails($chart_filter);
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('sales/bookshop/bookshopSalesDetails',$data);
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
            'title'            => 'OverDrive eBook Dashboard',
            'subTitle'         => 'Sales Summary',
            'summary'          => $model->getOverdriveEbookSummary(),
            'topBooks'         => $model->getTopSellingOverdriveBooks(10),
            'languageSales'    => $model->getOverdriveLanguageWiseSales(),
            'yearWiseSales'    => $model->getOverdriveYearWiseSales(),     // ⭐ NEW
            'genreWiseSales'   => $model->getOverdriveGenreWiseSales(),    // ⭐ NEW
        ];

        return view('sales/ebook/ebookOverdriveDetails', $data);
    }
    public function overdriveBooks()
    {
        $model = new EbookSalesModel();

        $data = [
            'title' => 'OverDrive Uploaded Books',
            'books' => $model->getOverdriveUploadedBooks()
        ];

        return view('sales/ebook/overdrivebooks', $data);
    }
    public function overdriveBookTransactions($bookId)
    {
        $model = new EbookSalesModel();

        $data = [
            'title' => 'OverDrive Book Transactions',
            'transactions' => $model->getOverdriveBookTransactions($bookId),
            'bookId' => $bookId
        ];

        return view('sales/ebook/overdriveBookTransactions', $data);
    }

    /* TOTAL AUTHORS */

    public function overdriveAuthors()
    {
        $model = new EbookSalesModel();

        $data = [
            'title'   => 'OverDrive Authors',
            'authors' => $model->getOverdriveAuthorWiseBooks()
        ];

        return view('sales/ebook/overdriveauthors', $data);
    }
    public function overdriveAuthorBooks($authorId)
{
    $model = new EbookSalesModel();

    $data = [
        'title'    => 'OverDrive Author Books',
        'books'    => $model->getOverdriveBooksByAuthor($authorId),
        'authorId' => $authorId
    ];

    return view('sales/ebook/overdriveAuthorBooks', $data);
}
public function overdriveOrders()
{
    $model = new EbookSalesModel();

    $data = [
        'title'  => 'OverDrive Orders',
        'orders' => $model->getOverdriveOrders()
    ];

    return view('sales/ebook/overdriveorders', $data);
}



    public function EbookScribdDetails()
{
    $model = new EbookSalesModel();
    $dashboard = $model->getScribdDashboardData();

    $data = [
        'title'           => 'Scribd eBook Dashboard',
        'subTitle'        => 'Sales Summary',
        'summary'         => $dashboard['summary'] ?? [],
        'top_books'       => $dashboard['top_books'] ?? [],
        'language_sales'  => $dashboard['language_sales'] ?? [],
        'year_sales'      => $dashboard['year_sales'] ?? [],
        'genre_sales'     => $dashboard['genre_sales'] ?? [],
    ];

    return view('sales/ebook/ebookScribdDetails', $data);
}
    /*BOOK LIST */
    public function scribdBooks()
    {
        $model = new EbookSalesModel();

        $data = [
            'title' => 'Scribd Books',
            'books' => $model->getScribdBooks() // <-- method in your model
        ];

        return view('sales/scribdBooksList', $data);
    }

    /** SINGLE BOOK DETAILS */
   public function scribdBookDetails($bookId)
    {
        $model = new EbookSalesModel();

        $data = [
            'title'        => 'Scribd Book Details',
            'book'         => $model->getBookById($bookId),           // single book info
            'txns'         => $model->getTransactionsByBook($bookId)  // transactions list
        ];

        return view('sales/scribdBookTransactions', $data);
    }

    public function EbookStorytelDetails()
    {
        $model = new EbookSalesModel();

        $data = [
            'title'          => 'Storytel Dashboard',
            'summary'        => $model->getStorytelSummary(),
            'topBooks'       => $model->getTopStorytelBooks(10),
            'languageSales'  => $model->getStorytelLanguageWiseSales(),
        ];

        return view('sales/ebook/ebookStorytelDetails', $data);
    }
    public function ebookGoogleDetails()
    {
        $model = new EbookSalesModel();

        $data = [
            'title'          => 'Storytel Dashboard',
            'summary'        => $model->getGoogleSummary(),
            'topBooks'       => $model->getTopGoogleBooks(),
            'languageSales'  => $model->getLanguageWiseSales(),
        ];

        return view('sales/ebook/ebookGoogleDetails', $data);
    }
    public function AudibleAudiobookDetails()
    {
        $model = new AudiobookSalesModel();

        $data = [
            'title'         => 'Audible Audiobook Dashboard',
            'subTitle'      => 'Sales Summary',
            'summary'       => $model->getAudibleSummary(),
            'topBooks'      => $model->getTopSellingAudibleBooks(10),
            'languageSales' => $model->getAudibleLanguageWiseSales(),
        ];

        return view('sales/audiobook/audibleDetails', $data);
    }
    public function AudiobookOverdriveDetails()
    {
        $model = new AudiobookSalesModel(); 

        $data = [
            'title'         => 'OverDrive AudioBook Dashboard',
            'subTitle'      => 'Audio Sales Summary',
            'summary'       => $model->getOverdriveAudioSummary(),
            'topBooks'      => $model->getTopSellingOverdriveAudiobooks(10),
            'languageSales' => $model->getOverdriveAudiobookLanguageWiseSales(),
        ];

        return view('sales/audiobook/audiobookOverdriveDetails', $data);
    }
    public function audiobookGoogleDetails()
    {
        $model = new AudiobookSalesModel();

        $data = [
            'title'          => 'Google Audiobook Dashboard',
            'summary'        => $model->getGoogleAudiobookSummary(),
            'topBooks'       => $model->getTopGoogleAudiobooks(),
            'languageSales'  => $model->getGoogleAudiobookLanguageSales(),
        ];

        return view('sales/audiobook/audiobookGoogleDetails', $data);
    }
    public function AudiobookStorytelDetails()
    {
        $model = new AudiobookSalesModel();

        $data = [
            'title'          => 'Storytel Audiobook Dashboard',
            'summary'        => $model->getStorytelAudioSummary(),
            'topBooks'       => $model->getTopStorytelAudioBooks(10),
            'languageSales'  => $model->getStorytelAudioLanguageWiseSales(),
        ];

        return view('sales/audiobook/audiobookStorytelDetails', $data);
    }

    public function youtubeDetails()
    {
        $model = new \App\Models\AudiobookSalesModel();

        $data = [
            'title'         => 'YouTube Revenue Dashboard',
            'summary'       => $model->getYoutubeSummary(),
            'topBooks'      => $model->getTopYoutubeBooks(10),
            'languageSales' => $model->getYoutubeLanguageSales(),
        ];

        return view('sales/audiobook/youtubeDetails', $data);
    }
    public function kukuFMDetails()
    {
        $model = new \App\Models\AudiobookSalesModel();

        $bookStats   = $model->getBookStats();
        $txnSummary  = $model->getTransactionSummary();

        $data = [
            'title'           => 'KukuFM Dashboard',
            'bookStats'       => $bookStats,
            'txnSummary'      => $txnSummary,
            'topBooks'        => $model->getTopKukuBooks(10),
            'languageCounts'  => $bookStats['languageCounts']
        ];

        return view('sales/audiobook/kukufmDetails', $data);
    }



}
