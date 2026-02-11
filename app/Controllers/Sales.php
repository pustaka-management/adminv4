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

    public function ebookAmazonDetails()
    {
        $model = new \App\Models\EbookSalesModel();
        $data['title'] = '';

        $data['counts'] = $model->getAmazonBookAndAuthorCounts();
        $data['orders'] = $model->getAmazonOrderCounts();
        $data['amounts'] = $model->getAmazonAmountSummary();
        $data['topBooks'] = $model->getAmazonTopBooks();
        $data['yearSales'] = $model->getAmazonSalesByYear();
        $data['langSales'] = $model->getAmazonSalesByLanguage();
        $data['genreSales'] = $model->getAmazonSalesByGenre();

        return view('sales/ebook/ebookAmazonDetails', $data);
    }
     public function getAmazonBooks()
    {
        $model = new EbookSalesModel();

        $data = [
            'title' => 'Amazon – Books List',
            'books' => $model->getAmazonBooks(),
        ];

        return view('sales/ebook/Amazonbookslist', $data);
    }

    public function getAmazonBookDetails($book_id)
    {
        $model = new EbookSalesModel();

        $data = [
            'title'        => 'Amazon – Book Details',
            'book'         => $model->getAmazonBookDetails($book_id),
            'transactions' => $model->getAmazonTransactions($book_id),
        ];

        if (!$data['book']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Book not found');
        }

        return view('sales/ebook/AmazonBookDetails', $data);
    }
       public function amazonAuthors()
{
    $model = new EbookSalesModel();

    $data = [
        'title'   => 'Amazon Authors',
        'authors' => $model->getAmazonAuthors()
    ];

    return view('sales/ebook/AmazonAuthors', $data);
}

public function amazonAuthorBooks($author)
{
    $model = new EbookSalesModel();

    $authorName = urldecode($author);

    $data = [
        'title'  => 'Amazon Books - ' . $authorName,
        'author' => $authorName,
        'books'  => $model->getAmazonBooksByAuthor($authorName)
    ];

    return view('sales/ebook/AmazonAuthorBooks', $data);
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
            'yearWiseSales'    => $model->getOverdriveYearWiseSales(),     
            'genreWiseSales'   => $model->getOverdriveGenreWiseSales(),    
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
            'title' => '',
            'books' => $model->getScribdBooks() // <-- method in your model
        ];

        return view('sales/ebook/scribdBooksList', $data);
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

        return view('sales/ebook/scribdBookTransactions', $data);
    }
    public function scribdAuthors()
    {
        $model = new \App\Models\EbookSalesModel();

        $data = [
            'authors' => $model->getAuthorsWithScribdBooks(),
            'title'   => 'Scribd Authors'
        ];

        return view('sales/ebook/scribdAuthors', $data);
    }

    public function scribdAuthorBooks($authorId)
    {
        $model = new \App\Models\EbookSalesModel();

        $data = [
            'books'  => $model->getBooksScribdByAuthor($authorId),
            'author' => $this->db->table('author_tbl')
                            ->where('author_id', $authorId)
                            ->get()->getRowArray()
        ];

        // Set dynamic title
        $data['title'] = ($data['author']['author_name'] ?? 'Author') . ' - Scribd Books';

        return view('sales/ebook/scribdAuthorBooks', $data);
    }

    // public function scribdOrders()
    // {
    //     $model = new \App\Models\EbookSalesModel();

    //     $data['orders'] = $model->getScribdOrders();
    //     $data['title']  = 'Scribd Orders List';

    //     return view('sales/ebook/scribdOrdersList', $data);
    // }

    public function EbookStorytelDetails()
    {
        $model = new EbookSalesModel();

        $data = [
            'title'          => 'Storytel Dashboard',
            'summary'        => $model->getStorytelSummary(),
            'topBooks'       => $model->getTopStorytelBooks(10),
            'languageSales'  => $model->getStorytelLanguageWiseSales(),

            // Add these two for charts
            'genreSales'     => $model->getStorytelGenreWiseSales(),
            'yearSales'      => $model->getStorytelYearWiseSales(),
        ];

        return view('sales/ebook/ebookStorytelDetails', $data);
    }


    // LIST OF ALL UPLOADED STORYTEL BOOKS
    public function storytelBooks()
    {
        $model = new EbookSalesModel();
        $data = [
            'title' => '',
            'books' => $model->getstorytelBooks()
        ];

        return view('sales/ebook/storytelBooksList', $data);
    }

    // SINGLE STORYTEL BOOK DETAILS + TRANSACTIONS
    public function storytelBookDetails($bookId)
    {
        $model = new EbookSalesModel();
        $data = [
            'title'        => 'Storytel Book Details',
            'book'         => $model->getBookDetails($bookId),
            'transactions' => $model->getTransactions($bookId),
        ];

        return view('sales/ebook/storytelBookTransactions', $data);
    }
        public function storytelAuthors()
    {
        $model = new EbookSalesModel();
        $data['authors'] = $model->getstorytelAuthors();
        $data['title']   = 'Storytel Authors';     // PAGE TITLE
        return view('sales/ebook/storytelAuthors', $data);
    }

    public function storytelBooksByAuthor($authorId)
    {
        $model = new EbookSalesModel();
        $data['books']  = $model->getstorytelBooksByAuthor($authorId);

        // Get author name for title (optional)
        $data['author'] = $model->getstorytelAuthorName($authorId);
        $data['title']   = '';

        return view('sales/ebook/storytelBooks', $data);
    }

    public function ebookGoogleDetails()
    {
        $model = new EbookSalesModel();

        $data = [
            'title'          => 'Google Dashboard',
            'summary'        => $model->getGoogleSummary(),
            'topBooks'       => $model->getTopGoogleBooks(),
            'year_sales'     => $model->getGoogleYearWise(),
            'lang_sales'     => $model->getGoogleLanguageWise(),
            'genre_sales'    => $model->getGoogleGenreWise(),
        ];

        return view('sales/ebook/ebookGoogleDetails', $data);
    }
    // TITLES 
    public function googleTitles()
    {
        $model = new EbookSalesModel();
        $data = [
            'title' => 'Google Titles',
            'books' => $model->getGoogleBooksList()
        ];
        return view('sales/ebook/googleTitles', $data);
    }

    public function googleTitleDetails($bookId)
    {
        $model = new EbookSalesModel();
        $data = [
            'title' => 'Google Book Details',
            'book' => $model->getGoogleBookDetails($bookId),
            'transactions' => $model->getGoogleBookTransactions($bookId)
        ];
        return view('sales/ebook/googleTitleView', $data);
    }

    // AUTHORS 
    public function googleAuthors()
    {
        $model = new EbookSalesModel();
        $data = [
            'title' => 'Google Authors',
            'authors' => $model->getGoogleAuthors()
        ];
        return view('sales/ebook/googleAuthors', $data);
    }

    public function googleAuthorBooks($authorId)
    {
        $model = new EbookSalesModel();
        $data = [
            'title' => 'Books by Author',
            'books' => $model->getGoogleAuthorBooks($authorId)
        ];
        return view('sales/ebook/googleAuthorBooks', $data);
    }
    public function ebookPratilipiDetails()
{
    $model = new EbookSalesModel();

    $booksAuthors = $model->getpratilipiBooksAuthors();
    $orders = $model->getpratilipiOrdersSummary();
    $earnings = $model->getpratilipiEarningsSummary();

    $data = [
        'title' => 'Pratilipi Dashboard',

        // merge into one summary
        'summary' => [
            'total_titles'     => $booksAuthors['total_titles'] ?? 0,
            'total_authors'    => $booksAuthors['total_authors'] ?? 0,

            'total_orders'     => $orders['total_orders'] ?? 0,
            'orders_paid'      => $orders['paid_orders'] ?? 0,
            'orders_pending'   => $orders['pending_orders'] ?? 0,

            'total_revenue'    => $earnings['total_earning'] ?? 0,
            'revenue_paid'     => $earnings['paid_earning'] ?? 0,
            'revenue_pending'  => $earnings['pending_earning'] ?? 0,
        ],

        'topAuthors' => $model->getPratilipiTopAuthors(),
        'yearTransactions' => $model->getpratilipiYearTransactions(),
    ];

    return view('Sales/ebook/ebookPratilipiDetails', $data);
}

    public function pratilipiTitles()
    {
        $model = new EbookSalesModel();
        $data = [
            'title' => 'Pratilipi Titles',
            'books' => $model->getPratilipiBooks(),
        ];

        return view('Sales/ebook/pratilipiTitles', $data);
    }

    public function pratilipiAuthorDetails($authorId)
    {
        $model = new EbookSalesModel();

        $data = [
            'title' => 'Pratilipi Author Details',
            'info' => $model->getPratilipiAuthorInfo($authorId),
            'transactions' => $model->getPratilipiAuthorTransactions($authorId),
        ];

        return view('Sales/ebook/pratilipiAuthorDetails', $data);
    }
    public function pratilipiauthors()
{
    $model = new \App\Models\EbookSalesModel();
    $data['title']   = 'Pratilipi – Authors';
    $data['authors'] = $model->getPratilipiAuthors();

    return view('sales/ebook/pratilipiAuthors', $data);
}

public function pratilipiauthorbooks($authorId)
{
    $model = new \App\Models\EbookSalesModel();
    $data['authorName'] = $model->getAuthorName($authorId);
    $data['books']      = $model->getBooksByAuthor($authorId);
    $data['title']      = 'Pratilipi – Books by ' . $data['authorName'];

    return view('sales/ebook/pratilipiAuthorBooks', $data);
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
