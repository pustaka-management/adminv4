<?php

namespace App\Controllers;

    use App\Models\EbookModel;
    use App\Models\AudiobookModel;
    use App\Models\PaperbackModel;
    use App\Models\BookModel;
    use App\Models\AuthorModel;
    use App\Models\LanguageModel;
    use App\Models\GenreModel;
    use App\Models\TypeModel;
    use App\Models\NarratorModel;

class Book extends BaseController
{
    protected $ebookModel;
    protected $audiobookModel;
    protected $paperbackModel;
    protected $bookModel;
    protected $authorModel;
    protected $languageModel;
    protected $genreModel;
    protected $typeModel;
    protected $NarratorModel;

    public function __construct()
{
        $this->ebookModel      = new EbookModel();
        $this->audiobookModel  = new AudiobookModel();
        $this->paperbackModel  = new PaperbackModel();
        $this->bookModel       = new BookModel();
        $this->authorModel     = new AuthorModel();
        $this->languageModel   = new LanguageModel();
        $this->genreModel      = new GenreModel();
        $this->typeModel       = new TypeModel();
        $this->narratorModel   = new NarratorModel();

        helper(['url']);
        session();
}
   public function bookDashboard()
{
        if (!session()->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        // Prepare all data before sending to view
        $dashboardData          = $this->ebookModel->getBookDashboardData();
        $bookStatistics         = $this->ebookModel->getBookDashboardMonthlyStatistics();
        $audiobookDashboardData = $this->audiobookModel->getBookDashboardData();
        $PaperbackBooksData     = $this->paperbackModel->getPaperbackBooksData();

       $data = [
            'title'                     => 'Book Dashboard',
            'subTitle'                  => 'Monthly statistics and book overview',
            'dashboard_data'            => $dashboardData,
            'book_statistics'           => $bookStatistics,
            'dashboard'                 => $audiobookDashboardData,
            'paperback'                 => $PaperbackBooksData
        ];

            // echo "<pre>";
            // print_r( $data);
        return view('Book/BookDashboard', $data);
}
    public function ebookPreCurrMonthDetails()
    {
        if (!session()->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        $ebookModel = new EbookModel();

        // Fetch author counts and book details
        $curr = $ebookModel->getBookDashboardCurrMonthData();
        $prev = $ebookModel->getBookDashboardPrevMonthData();

        $data = [
            'title'                     => 'Book Dashboard',
            'subTitle'                  => 'Author counts & book details',
            'dashboard_curr_month_data'   => $curr['authors'],
            'dashboard_prev_month_data'   => $prev['authors'],
            'dashboard_curr_month_books'  => $curr['books'],
            'dashboard_prev_month_books'  => $prev['books'],

            'currBooksCount' => $curr['summary']['total_books'],
            'currPagesCount' => $curr['summary']['total_pages'],

            'prevBooksCount' => $prev['summary']['total_books'],
            'prevPagesCount' => $prev['summary']['total_pages']
        ];

        return view('Book/EbookPreCurrMonthDetails', $data);
    }
    public function paperbackPreCurrMonthDetails()
    {
        if (!session()->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        $paperbackModel = new PaperbackModel();

        $currMonthData = $paperbackModel->getPaperbackCurrMonthData();
        $prevMonthData = $paperbackModel->getPaperbackPrevMonthData();

        $data = [
            'title' => 'Paperback Book Dashboard',
            'subTitle' => 'Authors & Paperback Books',
            'dashboard_curr_month_data'  => $currMonthData['authors'],
            'dashboard_prev_month_data'  => $prevMonthData['authors'],
            'dashboard_curr_month_books' => $currMonthData['books'],
            'dashboard_prev_month_books' => $prevMonthData['books'],
            'curr_month_summary'         => $currMonthData['summary'],
            'prev_month_summary'         => $prevMonthData['summary']
        ];

        return view('Book/PaperbackPreCurrMonthDetails', $data);
    }

    public function getEbooksStatus()
{
        if (!session()->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        $data = [
            'title'                      => 'E-Book Status Overview',
            'subTitle'                   => 'Detailed status of all eBooks',
            'in_progress_dashboard_data' => $this->ebookModel->getInProgressDashboardData(),
            'ebooks_data'                => $this->ebookModel->getEbooksStatusDetails(),
        ];

        return view('Book/EbookStatusView', $data);
}
    public function notStartedBooks()
{
    if (!session()->has('user_id')) {
        return redirect()->to('/adminv4/index');
    }

    $data = [
        'title'            => 'Books Not Yet Started',
        'subTitle'         => 'Detailed Start Work eBooks',
        'in_progress_dashboard_data' => $this->ebookModel->getInProgressDashboardData(),
        'ebooks_data'                => $this->ebookModel->getEbooksStatusDetails(),
    ];

    return view('Book/NotStartedBooks', $data);
}


    public function Ebooks()
{
    $model = $this->ebookModel;
    $dashboardData = $model->getBookDashboardData();
    $data = [
        'title'        => 'All E-Books',
        'subTitle'     => 'List of uploaded and active eBooks',
        'dashboard_data' => $dashboardData, 
        'e_books'      => $model->getEbookData(),
        'languageData' => $model->getLanguageWiseBookCount(),
        'genreData'    => $model->getGenreWiseBookCount(),
        'categoryData' => $model->getBookCategoryCount(),
        'authorData'   => $model->getAuthorWiseBookCount()
    ];

    return view('Book/Ebooks', $data);
}
    public function paperBackSummary()
{
    if (!session()->has('user_id')) {
        return redirect()->to('/adminv4/index');
    }
    $model = $this->paperbackModel;

    $data = [
        'title'                      => 'Paperback Dashboard',
        'subTitle'                   => 'Overview of Paperback Activities',
        'languageData'               => $model->getLanguageWiseBookCount(),
        'genreData'                  => $model->getGenreWiseBookCount(),
        'categoryData'               => $model->getBookCategoryCount(),
        'authorData'                 => $model->getAuthorWiseBookCount(),
        'paperback'                 => $model->getPaperbackBooksData(),
        'colors'                     => ["#FF9F29", "#487FFF", "#45B369", "#9935FE", "#FF6384", "#36A2EB"]
    ];

    return view('Book/Paperbackbook', $data);
}


   public function audioBookDashboard()
{
    if (!session()->has('user_id')) {
        return redirect()->to('/adminv4/index');
    }

    $model = $this->audiobookModel;
    $dashboardData = $model->getBookDashboardData();

    $data = [
        'title'                      => 'Audio Books Dashboard',
        'subTitle'                   => 'Overview of Audiobook Activities',
        'audio_books_dashboard_data' => $model->getAudioBookDashboardData(),
        'languageData'               => $model->getLanguageWiseBookCount(),
        'genreData'                  => $model->getGenreWiseBookCount(),
        'categoryData'               => $model->getBookCategoryCount(),
        'authorData'                 => $model->getAuthorWiseBookCount(),
        'dashboard'                  => $dashboardData,
        'colors'                     => ["#FF9F29", "#487FFF", "#45B369", "#9935FE", "#FF6384", "#36A2EB"]
    ];

    return view('Book/Audiobookdashboard', $data);
}

    public function paperbackdashboard()
{
        if (!session()->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        $data = [
            'title'     => 'InDesign Books Dashboard',
            'subTitle'  => '',
            'books'     => $this->paperbackModel->paperbackIndesignProcessing(),
            'count'     => $this->paperbackModel->indesignProcessingCount(),
            'languageData' => $this->paperbackModel->getLanguageWiseBookCount(),
            'genreData' => $this->paperbackModel->getGenreWiseBookCount(),
            'categoryData' => $this->paperbackModel->getBookCategoryCount(),
            'authorData'   => $this->paperbackModel->getAuthorWiseBookCount(),
            'colors'       => ["#FF9F29", "#487FFF", "#45B369", "#9935FE", "#FF6384", "#36A2EB"]
    ];

        return view('Book/IndesignbookDashboard', $data);
}
   public function getHoldBookDetails()
{
    $EbookModel = new \App\Models\EbookModel();

    $data = [
        'title'       => 'Hold Book Details',
        'subTitle'    => 'List of books currently on hold',
        'ebooks_data' => $EbookModel->getEbooksStatusDetails(),
        'holdbook'    => $EbookModel->getHoldBookDetails()
    ];

    return view('Book/HoldbookDetails', $data);
}
    public function ebooksMarkStart()
{
        $book_id = $this->request->getPost('book_id');

        if (!$book_id) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Invalid Book ID']);
        }

        $db = db_connect();
        $builder = $db->table('books_processing');
        $builder->where('book_id', $book_id);
        $builder->update(['start_flag' => 1]);

        return $this->response->setJSON([
            'status' => ($db->affectedRows() > 0 ? 1 : 0)
        ]);
}
    public function getInactiveBooks()
{
    $ebookModel = new \App\Models\EbookModel();

    // Fetch inactive books
    $data['in_active'] = $ebookModel->getInactiveBooks();

    $data['title'] = 'Inactive Books';
    $data['subTitle'] = 'List of books currently marked as inactive';

    return view('Book/getInactiveBooks', $data);
}

    public function addBook()
{
    $session = session();
    if (!$session->has('user_id')) {
        return redirect()->to('/adminv4/index');
    }

    $langModel         = new \App\Models\LanguageModel();
    $genreModel        = new \App\Models\GenreModel();
    $authorModel       = new \App\Models\AuthorModel();
    $adminModel        = new \App\Models\AdminModel();
    $ebookModel        = new \App\Models\EbookModel();

    $data = [
        'title'         => 'Add New Book',              
        'subTitle'      => 'Fill in the book details',   
        'lang_details'  => $langModel->getAllLanguages(),
        'genre_details' => $genreModel->getAllGenres(),
        'author_list'   => $authorModel->getAuthorDetails(),
        'admin_users'   => $adminModel->getAdminUsers(),
        'book_stages'   => $ebookModel->getAllStages()
    ];

    return view('Book/AddBook', $data);
}
public function checkBookUrl()
{
    $url_title = $this->request->getPost('url_title');

    $exists = $this->db->table('book_tbl')
        ->where('url_name', $url_title)
        ->countAllResults() > 0;

    return $this->response->setJSON(['exists' => $exists]);
}
    public function fillDataView($bookId = null)
{
    if (!session()->has('user_id')) {
        return redirect()->to('/adminv4/index');
    }

    $ebookModel = new EbookModel();
    $data['fill_data_info'] = $ebookModel->getFillData($bookId);
    $data['book_id'] = $bookId;
    $data['title'] = "Fill Book Data";  
    $data['subTitle'] = "Update details for Book ID: " . $bookId;

    return view('Book/FillDataView', $data);
}
    public function fillData()
{
        log_message('debug', 'Inside fillData in controller');

        $ebookModel = new EbookModel();
        $result = $ebookModel->fillData();
        return $this->response->setJSON(['status' => $result]);
}
     public function addToTest()
    {
        $ebookModel = new \App\Models\EbookModel();

        $userId = $this->request->getPost('user_id');
        $bookId = $this->request->getPost('book_id');

        $result = $ebookModel->addToTest($userId, $bookId);
        return $this->response->setBody((string)$result);
    }
    public function holdInProgress()
    {
        $bookId = $this->request->getPost('book_id');
        $model  = new EbookModel();

        $result = $model->holdInProgress($bookId);

        return $this->response->setJSON($result);
    }   
    public function activateBookPage($book_id = null)
    {
        $ebookModel = new EbookModel();
        $mdl_data = $ebookModel->getBookDetails($book_id);

        $data = [
            "book_details"              => $mdl_data["book_details"],
            "author_details"            => $mdl_data["author_details"],
            "user_details"              => $mdl_data["user_details"],
            "publisher_details"         => $mdl_data["publisher_details"],
            "copyright_mapping_details" => $mdl_data["copyright_mapping_details"],
            "title"                     => "Activate Book: " . $mdl_data["book_details"]["book_title"],
            "subTitle"                  => "Author: " . $mdl_data["author_details"]["author_name"]
        ];

        if (isset($mdl_data["narrator_details"])) {
            $data["narrator_details"] = $mdl_data["narrator_details"];
            $data["audio_chapters"]   = $mdl_data["audio_chapters"];
        }

        return view('Book/ActivateBookView', $data);
    }
    public function activateBook()
    {
        $book_id = $this->request->getPost('book_id');
        $send_mail_flag = $this->request->getPost('send_mail');

        if (!$book_id) {
            return $this->response->setJSON([
                'status' => 0
            ]);
        }

        $ebookModel = new \App\Models\EbookModel();
        $result = $ebookModel->activateBook($book_id, $send_mail_flag);

        return $this->response->setJSON([
            'status' => ($result ? 1 : 0)
        ]);
    }

    public function addBookPost()
    {
        $bookModel = new \App\Models\EbookModel();

        $result = $bookModel->addBook($this->request->getPost());

        return $this->response->setJSON([
            'result' => $result ? true : false
        ]);
    }
    public function ebookEditPost()
    {
        $book_id = $this->request->getPost('book_id');
        $postData = $this->request->getPost();

        $model = new EbookModel();
        $updated = $model->updateBookPost($book_id, $postData);

        return $this->response->setJSON(['result' => $updated]);
    }
    public function browseInProgressBooks()
    {
        $session = session();
        if (!$session->get('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        $ebookModel = new \App\Models\EbookModel();

        $data = [
            'title'    => 'Browse In-Progress Books',
            'subTitle' => 'View and track the current status of all books',
            'books'    => $ebookModel->getBrowseBooksData()
        ];

        return view('Book/browseInProgressBooks', $data);
    }
    public function markScanComplete()
    {
        $book_id = $this->request->getPost('book_id');

        $db = db_connect();
        $builder = $db->table('books_processing');
        $builder->where('book_id', $book_id);
        $builder->update(['scan_flag' => 1]);

        return $this->response->setJSON([
            'status' => ($db->affectedRows() > 0 ? 1 : 0)
        ]);
    }
    
    public function markOcrComplete()
    {
        $book_id = $this->request->getPost('book_id');

        $db = db_connect();
        $builder = $db->table('books_processing');
        $builder->where('book_id', $book_id);
        $builder->update(['ocr_flag' => 1]);

        return $this->response->setJSON([
            'status' => ($db->affectedRows() > 0 ? 1 : 0)
        ]);
    }

    public function markLevel1Complete()
    {
        $book_id = $this->request->getPost('book_id');

        $db = db_connect();
        $builder = $db->table('books_processing');
        $builder->where('book_id', $book_id);
        $builder->update(['level1_flag' => 1]);

        return $this->response->setJSON([
            'status' => ($db->affectedRows() > 0 ? 1 : 0)
        ]);
    }

    public function markLevel2Complete()
    {
        $book_id = $this->request->getPost('book_id');

        $db = db_connect();
        $builder = $db->table('books_processing');
        $builder->where('book_id', $book_id);
        $builder->update(['level2_flag' => 1]);

        return $this->response->setJSON([
            'status' => ($db->affectedRows() > 0 ? 1 : 0)
        ]);
    }

    public function markCoverComplete()
    {
        $book_id = $this->request->getPost('book_id');

        $db = db_connect();
        $builder = $db->table('books_processing');
        $builder->where('book_id', $book_id);
        $builder->update(['cover_flag' => 1]);

        return $this->response->setJSON([
            'status' => ($db->affectedRows() > 0 ? 1 : 0)
        ]);
    }

    public function markBookGenerationComplete()
    {
            {
            $book_id = $this->request->getPost('book_id');

            $db = db_connect();
            $builder = $db->table('books_processing');
            $builder->where('book_id', $book_id);
            $builder->update(['book_generation_flag' => 1]);

            return $this->response->setJSON([
                'status' => ($db->affectedRows() > 0 ? 1 : 0)
            ]);
        }
    }

    public function markUploadComplete()
    {
        {
        $book_id = $this->request->getPost('book_id');

        $db = db_connect();
        $builder = $db->table('books_processing');
        $builder->where('book_id', $book_id);
        $builder->update(['upload_flag' => 1]);

        return $this->response->setJSON([
            'status' => ($db->affectedRows() > 0 ? 1 : 0)
        ]);
        }
    }

    public function markCompleted()
    {
        $book_id = $this->request->getPost('book_id');

        $builder = $this->db->table('books_processing');
        $builder->set('completed', 1);
        $builder->set('completed_date', 'NOW()', false);
        $builder->where('book_id', $book_id)->update();

        return $this->response->setJSON([
            'status' => ($this->db->affectedRows() > 0 ? 1 : 0)
        ]);
    }
    public function addAudioBook()
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }
        $languageModel = new \App\Models\LanguageModel();
        $genreModel    = new \App\Models\GenreModel();
        $authorModel   = new \App\Models\AuthorModel();
        $narratorModel = new \App\Models\NarratorModel();

            $data = [
                'title'         => 'Add Audio Book',
                'subTitle'      => 'Fill in the details below to create a new audio book',
                'lang_details'  => $languageModel->getAllLanguages(),
                'genre_details' => $genreModel->getAllGenres(),
                'author_list'   => $authorModel->getAuthorDetails(),
                'narrator_list' => $narratorModel->getAllNarrators(),
            ];

            return view('Book/AddAudioBook', $data);
        }
    public function addAudioBookPost()
    {
        $audiobookModel = new \App\Models\AudiobookModel();
        $postData = $this->request->getPost();

        $result = $audiobookModel->addAudioBook($postData);

        return $this->response->setJSON([
            'success' => $result ? true : false,
            'message' => $result ? 'Audio Book added successfully' : 'Failed to add audio book'
        ]);
    }
    public function audioBookChapters($book_id = null)
    {
        $session = session();

        // Check login session
        if (!$session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }
        if (!$book_id) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book ID not provided');
        }

        $AudiobookModel = new \App\Models\AudiobookModel();

        $data = [
            'title'           => 'Audio Book Chapters',
            'subTitle'        => 'Manage chapters for the selected audio book',
            'audio_book_info' => $AudiobookModel->getAudioBookChaptersData($book_id), // Pass $book_id here
            'book_id'         => $book_id, 
        ];

        return view('Book/AudioBookChapters', $data);
    }

    public function addAudioBookChapter()
    {
        $AudiobookModel = new \App\Models\AudiobookModel();

        $result = $AudiobookModel->addAudioBookChapter($this->request->getPost());

        return $this->response->setJSON([
            'success' => $result ? true : false,
            'message' => $result ? 'Chapter added successfully' : 'Failed to add chapter'
        ]);
    }

    public function editAudioBookChapter()
    {
        $db = \Config\Database::connect();
        $table = 'audio_book_details';

        $id = $this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request. Missing chapter ID'
            ]);
        }

        $update_data = [
            "chapter_id"           => $this->request->getPost('chp_id'),
            "chapter_name"         => $this->request->getPost('regional_name'),
            "chapter_name_english" => $this->request->getPost('title'),
            "chapter_url"          => $this->request->getPost('file_path'),
            "chapter_duration"     => $this->request->getPost('chapter_duration'),
        ];

        $builder = $db->table($table)->where('id', $id);
        $builder->update($update_data);
        $affectedRows = $db->affectedRows();
        return $this->response->setJSON([
            'success' => $builder ? true : false,
            'message' => $affectedRows > 0 ? 'Chapter updated successfully' : 'No changes made'
        ]);
    }
    public function pustakaDetails()
    {
        // Create the model instance
        $ebookModel = new \App\Models\EbookModel();

        if (session()->has('user_id')) {
            $data['title'] = "Pustaka Details";
            $data['subTitle'] = "Overview of all books and their status";
            $data['pustaka'] = $ebookModel->pusDetails();
            $data['dashboard_data'] = $ebookModel->getBookDashboardData();

            return view('Book/PustakaDetails', $data);
        } else {
            return redirect()->to(site_url('adminv4/index'));
        }
    }
    public function audioDetails()
    {
        if (!session()->has('user_id')) {
            return redirect()->to(site_url('adminv4/index'));
        }
         $ebookModel = new \App\Models\EbookModel();
         $AudiobookModel = new \App\Models\AudiobookModel();

        $data['title'] = "Audio Book Details";
        $data['subTitle'] = "Overview of all audiobooks and their status";
        $data['audio'] = $AudiobookModel->audioDetails();
        $data['dashboard_data'] = $ebookModel->getBookDashboardData();

        return view('Book/audioDetails', $data);
    }
    public function paperbackDetails()
{
    if (!session()->has('user_id')) {
        return redirect()->to(site_url('adminv4/index'));
    }
    $ebookModel = new \App\Models\EbookModel();
    $paperbackModel = new \App\Models\PaperbackModel();

    $data['title'] = "Paperback Details";
    $data['subTitle'] = "Overview of all paperback books";
    $data['paperback'] = $paperbackModel->paperbackDetails();
    $data['dashboard_data'] = $ebookModel->getBookDashboardData();

    return view('Book/PaperbackDetails', $data);
}
    public function amazonDetails()
        {
            $session = session();

            if ($session->has('user_id')) {
                $EbookModel = new \App\Models\EbookModel();
                $LanguageModel = new \App\Models\LanguageModel();

                $data = [
                    'title'        => 'Amazon Book Details',
                    'subTitle'     => 'List of Amazon Books and Related Details',
                    'amazon'       => $EbookModel->amzDetails(),
                    'amazon_books' => $LanguageModel->bookDetails()
                ];

                return view('Book/Amazon/AmazonDetails', $data);
            } else {
                return redirect()->to(site_url('adminv4/index'));
            }
        }
    public function getActiveBooks()
    {
        $ebookModel = new \App\Models\EbookModel();

        // Fetch active books
        $data['active'] = $ebookModel->getActiveBooks();

        $data['title'] = 'Active Books';
        $data['subTitle'] = 'List of books currently marked as active';

        return view('Book/getActiveBooks', $data);
    }   
    public function amazonUnpublishedTamil()
    {
        if (session()->get('user_id')) {
            $bookModel = new \App\Models\EBookModel(); 
            
            // Amazon unpublished tamil details
            $data['amazon'] = $bookModel->amzonDetails();
            $data['title'] = "Amazon Pending Tamil Books";
            $data['subTitle'] = "List of Tamil Books and Magazines not yet published in Amazon";

            return view('Book/Amazon/amazonUnpublishedTamil', $data);
        } else {
            return redirect()->to(site_url('adminv4/index'));
        }
    }
    public function amazonUnpublishedMalayalam()
    {
        if (session()->get('user_id')) {
            $bookModel = new EBookModel();
            $data['amazon']   = $bookModel->amzonDetails();
            $data['title']    = "Amazon Pending Books";
            $data['subTitle'] = "Malayalam";

            return view('Book/Amazon/amazonUnpublishedMalayalam', $data);
        } else {
            return redirect()->to(site_url('adminv4/index'));
        }
    }


    public function amazonUnpublishedEnglish()
    {
        if (session()->get('user_id')) {
            $bookModel = new EBookModel();
            $data['amazon']   = $bookModel->amzonDetails();
            $data['title']    = "Amazon Pending Books";
            $data['subTitle'] = "English";

            return view('Book/Amazon/amazonUnpublishedEnglish', $data);
        } else {
            return redirect()->to(site_url('adminv4/index'));
        }
    }
    public function scribdDetails()
    {
        // Check if admin/user session exists
        if (!session()->has('user_id')) {
            return redirect()->to(site_url('adminv4/index'));
        }

        $ebookModel = new \App\Models\EbookModel();

        $data = [
            'title'    => 'Scribd Details',
            'subTitle' => 'Published & Pending Books Overview',
            'scribd'   => $ebookModel->scribdDetails(),
        ];

        return view('Book/Scribd/ScribdDetails', $data);
    }
    public function scribdUnpublishedTamil()
    {
        $session = session();

        if ($session->has('user_id')) {
            $data['scribd'] = $this->ebookModel->scribdDetails(); 
            $data['title'] = 'Scribd Pending Tamil Books';
            $data['subTitle'] = 'View details of unpublished Tamil books on Scribd';

            return view('Book/Scribd/ScribdUnpublishedTamil', $data);
        } else {
            return redirect()->to(base_url());
        }
    }
    public function scribdUnpublishedKannada()
    {
        $session = session();

        if ($session->has('user_id')) {
            $data['scribd'] = $this->ebookModel->scribdDetails(); 
            $data['title'] = 'Scribd Pending Kannada Books';
            $data['subTitle'] = 'View details of unpublished Tamil books on Scribd';

            return view('Book/Scribd/ScribdUnpublishedKannada', $data);
        } else {
            return redirect()->to(base_url());
        }
    }
    public function scribdUnpublishedTelugu()
    {
        $session = session(); 

        if ($session->has('user_id')) {
            $data['scribd'] = $this->ebookModel->scribdDetails(); 
            $data['title'] = 'Scribd Pending Telugu Books';
            $data['subTitle'] = 'View details of unpublished Tamil books on Scribd';

            return view('Book/Scribd/ScribdUnpublishedTelugu', $data);
        } else {
            return redirect()->to(base_url());
        }
    }
     public function scribdUnpublishedMalayalam()
    {
        $session = session(); 

        if ($session->has('user_id')) {
            $data['scribd'] = $this->ebookModel->scribdDetails(); 
            $data['title'] = 'Scribd Pending Malayalam Books';
            $data['subTitle'] = 'View details of unpublished Tamil books on Scribd';

            return view('Book/Scribd/ScribdUnpublishedMalayalam', $data);
        } else {
            return redirect()->to(base_url());
        }
    }
     public function scribdUnpublishedEnglish()
    {
        $session = session(); 

        if ($session->has('user_id')) {
            $data['scribd'] = $this->ebookModel->scribdDetails(); 
            $data['title'] = 'Scribd Pending Telugu Books';
            $data['subTitle'] = 'View details of unpublished English books on Scribd';

            return view('Book/Scribd/ScribdUnpublishedEnglish', $data);
        } else {
            return redirect()->to(base_url());
        }
    }  
    public function storytelDetails()
    {
    if (!session()->has('user_id')) {
        return redirect()->to(site_url('adminv4/index'));
    }

    $ebookModel = new \App\Models\EbookModel();

    $data = [
        'title'    => 'Storytel Details',
        'subTitle' => 'Published & Pending Books Overview',
        'storytel' => $ebookModel->storytelDetails(), 
    ];

    return view('Book/Storytel/StorytelDetails', $data);
    }
    public function storytelUnpublishedTamil()
    {
        $session = session();

        if ($session->has('user_id')) {
            $data['storytel'] = $this->ebookModel->storytelDetails(); 
            $data['title'] = 'storytel Pending Tamil Books';
            $data['subTitle'] = 'View details of unpublished Tamil books on storytel';

            return view('Book/storytel/StorytelUnpublishedTamil', $data);
        } else {
            return redirect()->to(base_url());
        }
    }
    public function storytelUnpublishedKannada()
    {
        $session = session();

        if ($session->has('user_id')) {
            $data['storytel'] = $this->ebookModel->storytelDetails(); 
            $data['title'] = 'storytel Pending Kannada Books';
            $data['subTitle'] = 'View details of unpublished Kannada books on storytel';

            return view('Book/Storytel/StorytelUnpublishedKannada', $data);
        } else {
            return redirect()->to(base_url());
        }
    }
    public function storytelUnpublishedTelugu()
    {
        $session = session(); 

        if ($session->has('user_id')) {
            $data['storytel'] = $this->ebookModel->storytelDetails(); 
            $data['title'] = 'storytel Pending Telugu Books';
            $data['subTitle'] = 'View details of unpublished Telugu books on storytel';

            return view('Book/Storytel/StorytelUnpublishedTelugu', $data);
        } else {
            return redirect()->to(base_url());
        }
    }
     public function storytelUnpublishedMalayalam()
    {
        $session = session(); 

        if ($session->has('user_id')) {
            $data['storytel'] = $this->ebookModel->storytelDetails(); 
            $data['title'] = 'storytel Pending Malayalam Books';
            $data['subTitle'] = 'View details of unpublished Malayalam books on storytel';

            return view('Book/Storytel/StorytelUnpublishedMalayalam', $data);
        } else {
            return redirect()->to(base_url());
        }
    }
     public function storytelUnpublishedEnglish()
    {
        $session = session(); 

        if ($session->has('user_id')) {
            $data['storytel'] = $this->ebookModel->storytelDetails(); 
            $data['title'] = 'storytel Pending Telugu Books';
            $data['subTitle'] = 'View details of unpublished English books on storytel';

            return view('Book/Storytel/StorytelUnpublishedEnglish', $data);
        } else {
            return redirect()->to(base_url());
        }
    }  
   public function googleDetails()
    {
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }

        $data = [
            'google'   => $this->ebookModel->googleDetails(),
            'title'    => 'Google Books Details',
            'subTitle' => 'Published & Pending Books Overview',
        ];

        // // Debug output
        // echo '<pre>';
        // print_r($data['google']);
        // echo '</pre>';
        // exit; // Stop execution to view the output

        return view('Book/Google/GoogleDetails', $data);
    }
    public function googleUnpublishedTamil()
    {
    if (!session()->has('user_id')) {
        return redirect()->to(base_url('adminv4'));
    }

    $googleData = $this->ebookModel->googleDetails();

    $data = [
        'unpublishedBooks' => $googleData['google_tml_unpublished'] ?? [],
        'title'            => 'Google Pending Tamil Books',
        'subTitle'         => 'View details of unpublished Tamil books on Google',
    ];

    return view('Book/Google/GoogleUnpublishedTamil', $data);
    }
    // Unpublished Kannada books
    public function googleUnpublishedKannada()
    {
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }

        $data = [
            'google'   => $this->ebookModel->googleDetails(),
            'title'    => 'Google Pending Kannada Books',
            'subTitle' => 'View details of unpublished Kannada books on Google',
        ];

        return view('Book/Google/GoogleUnpublishedKannada', $data);
    }

    // Unpublished Telugu books
    public function googleUnpublishedTelugu()
    {
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }

        $data = [
            'google'   => $this->ebookModel->googleDetails(),
            'title'    => 'Google Pending Telugu Books',
            'subTitle' => 'View details of unpublished Telugu books on Google',
        ];

        return view('Book/Google/GoogleUnpublishedTelugu', $data);
    }
    // Unpublished Malayalam books
    public function googleUnpublishedMalayalam()
    {   
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }

        $data = [
            'google'   => $this->ebookModel->googleDetails(),
            'title'    => 'Google Pending Malayalam Books',
            'subTitle' => 'View details of unpublished Malayalam books on Google',
        ];

        return view('Book/Google/GoogleUnpublishedMalayalam', $data);
    }
    // Unpublished English books
    public function googleUnpublishedEnglish()
    {
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }

        $data = [
            'google'   => $this->ebookModel->googleDetails(),
            'title'    => 'Google Pending English Books',
            'subTitle' => 'View details of unpublished English books on Google',
        ];

        return view('Book/Google/GoogleUnpublishedEnglish', $data);
    }
    // Paperback Books List
    public function paperbackBooksList()
    {
    if (!session()->has('user_id')) {
        return redirect()->to(base_url('adminv4'));
    }

    $data['paperback_books_data'] = $this->paperbackModel->getPaperbackBooksList();
    $data['title'] = 'Paperback Books List';
    $data['subTitle'] = 'Manage your paperback books';

    return view('Book/paperbackBookList', $data);
    }

    // Selected Book List
    public function selectedBookList()
    {
        $selectedBookList = $this->request->getPost('selected_book_list');
        $data['selected_book_id'] = $selectedBookList;
        $data['selected_books_data'] = $this->paperbackModel->selectedBookList($selectedBookList);

        $data['title'] = 'Selected Books';
        $data['subTitle'] = 'Details of selected Paperback books';

        return view('Book/pbSelectedBookList', $data);
    }   
    
    public function bookListSubmit()
    {
        log_message('debug', 'BookListSubmit called');
        
        $num_of_books       = $this->request->getPost('num_of_books');
        $selected_book_list = $this->request->getPost('selected_book_list');
        $postData           = $this->request->getPost();

        log_message('debug', 'POST data: ' . print_r($postData, true));

        $result = $this->paperbackModel->bookListSubmit($num_of_books, $selected_book_list, $postData);

        return $this->response->setBody((string)$result);
    }
   public function indesignMarkStart()
    {
        $book_id = $this->request->getPost('book_id');
        $result =$this->paperbackModel->indesignMarkStart($book_id);
        return $this->response->setBody((string)$result);
    }

    public function markLevel3Completed()
    {
        $book_id = $this->request->getPost('book_id');
        $result =$this->paperbackModel->markLevel3Completed($book_id);
        return $this->response->setBody((string)$result);
    }

    public function markIndesignCompleted()
    {
        $book_id = $this->request->getPost('book_id');
        $result =$this->paperbackModel->markIndesignCompleted($book_id);
        return $this->response->setBody((string)$result);
    }

    public function markIndesignQcCompleted()
    {
        $book_id = $this->request->getPost('book_id');
        $result =$this->paperbackModel->markIndesignQcCompleted($book_id);
        return $this->response->setBody((string)$result);
    }

    public function markReQcCompleted()
    {
        $book_id = $this->request->getPost('book_id');
        $result =$this->paperbackModel->markReQcCompleted($book_id);
        return $this->response->setBody((string)$result);
    }

    public function markIndesignCoverCompleted()
    {
        $book_id = $this->request->getPost('book_id');
        $result =$this->paperbackModel->markIndesignCoverCompleted($book_id);
        return $this->response->setBody((string)$result);
    }

    public function markIsbnReadyCompleted()
    {
        $book_id = $this->request->getPost('book_id');
        $result =$this->paperbackModel->markIsbnReadyCompleted($book_id);
        return $this->response->setBody((string)$result);
    }

    public function markFinalQcCompleted()
    {
        $book_id = $this->request->getPost('book_id');
        $result =$this->paperbackModel->markFinalQcCompleted($book_id);
        return $this->response->setBody((string)$result);
    }

    public function markFileUploadCompleted()
    {
        $book_id = $this->request->getPost('book_id');
        $result =$this->paperbackModel->markFileUploadCompleted($book_id);
        return $this->response->setBody((string)$result);
    }
    public function completedBooksSubmit($book_id = null)
    {
        $model = new \App\Models\paperbackModel();
        $data['completed'] = $model->completedBooksSubmit($book_id);

        $data['title'] = 'Completed Book Details';
        $data['subTitle'] = 'View the details of the completed book';

        return view('Book/CompletedBooksSubmit', $data);
    }
   public function indesignMarkCompleted()
    {
        $model = new \App\Models\paperbackModel();
        $book_id = $this->request->getPost('book_id');
        $pages = $this->request->getPost('pages');
        $price = $this->request->getPost('price');
        $royalty = $this->request->getPost('royalty');
        $copyright_owner = $this->request->getPost('copyright_owner');
        $isbn = $this->request->getPost('isbn');
        $paper_back_desc = $this->request->getPost('paper_back_desc');
        $paper_back_author_desc = $this->request->getPost('paper_back_author_desc');

        $result = $model->indesignMarkCompleted(
            $book_id,
            $pages,
            $price,
            $royalty,
            $copyright_owner,
            $isbn,
            $paper_back_desc,
            $paper_back_author_desc
        );

        return $this->response->setBody((string) $result);
    }

    public function paperbackReworkBook()
    {
        $model = new \App\Models\paperbackModel();
        $data['rework'] = $model->paperbackReworkBook();

        $data['title'] = 'Paperback Rework Books';
        $data['subTitle'] = 'Manage books that need rework in Indesign';
        return view('Book/ReworkBookList', $data);
    }
    public function reworkSelectedBooks()
    {
        $selected_book_list = $this->request->getPost('selected_book_list');
        $model = new \App\Models\paperbackModel();
        $data = [
            'title' => 'Paperback Rework Books',
            'subTitle' => 'Manage and rework selected paperback books',
            'selected_book_id' => $selected_book_list,
            'selected_books_data' => $model->selectedBookList($selected_book_list)
        ];   
        return view('Book/ReworkSelectedBooks', $data);
    }
    public function reworkBookSubmit()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('indesign_processing');

        $num_of_books = $this->request->getPost('num_of_books');
        $affected_rows = 0;

        for ($i = 1; $i <= $num_of_books; $i++) {
            $book_id   = $this->request->getPost('book_id' . $i);
            $author_id = $this->request->getPost('author_id' . $i);

            if (!$book_id) continue;

            $existing_book = $builder->where('book_id', $book_id)->get()->getRowArray();

            if (!$existing_book) {
                $builder->insert([
                    'book_id'       => $book_id,
                    'author_id'     => $author_id,
                    'created_date' => date('Y-m-d H:i:s'),
                    'start_flag'=>1,
                    'level3_flag'=>1,
                    'indesign_flag'=>1,
                    'indesign_qc_flag'=>1,
                    're_qc_flag'=>1,
                    'indesign_cover_flag'=>1,
                    'isbn_ready_flag'=>1,
                    'final_qc_flag'=>1,
                    'file_upload_flag'=>1,
                    'completed_flag'=>1,
                    'completed_date' => date('Y-m-d H:i:s'),
                    'rework_flag' => 1,
                ]);
            } else {
                $builder->where('book_id', $book_id)->update([
                    'author_id'     => $author_id,
                    'created_date'  => date('Y-m-d H:i:s'),
                    'rework_flag'   => 1,
                    're_completed_flag' => 0,
                ]);
            }

            $affected_rows += $db->affectedRows();
        }

        return $this->response->setBody((string)($affected_rows > 0 ? 1 : 0));
    }
    public function reworkBookView()
    {
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }
        $data['books_data'] = $this->paperbackModel->paperbackReworkProcessing();
        $data['count']      = $this->paperbackModel->reworkProcessingCount();

        $data['title']    = 'Rework Books';
        $data['subTitle'] = 'Manage books sent for rework and processing';

        return view('Book/ReworkBooksView', $data);
    }
    public function reworkMarkStart()
    {
        $book_id = $this->request->getPost('book_id');
        $db = \Config\Database::connect();
        $builder = $db->table('indesign_processing');

        $builder->where('book_id', $book_id);
        $builder->update(['rework_start_flag' => 1]);
        return $this->response->setJSON($db->affectedRows() > 0 ? 1 : 0);
    }
    public function markReProofingCompleted()
    {
        $book_id = $this->request->getPost('book_id');

        $db = \Config\Database::connect();
        $builder = $db->table('indesign_processing');

        $builder->where('book_id', $book_id);
        $builder->update(['re_proofing_flag' => 1]);

        return $this->response->setJSON($db->affectedRows() > 0 ? 1 : 0);
    }

    public function markReIndesignCompleted()
    {
        $book_id = $this->request->getPost('book_id');

        $db = \Config\Database::connect();
        $builder = $db->table('indesign_processing');

        $builder->where('book_id', $book_id);
        $builder->update(['re_indesign_flag' => 1]);

        return $this->response->setJSON($db->affectedRows() > 0 ? 1 : 0);
    }
    public function markReFileuploadCompleted()
    {
        $book_id = $this->request->getPost('book_id');

        $db = \Config\Database::connect();
        $builder = $db->table('indesign_processing');

        $builder->where('book_id', $book_id);
        $builder->update(['re_fileupload_flag' => 1]);

        return $this->response->setJSON($db->affectedRows() > 0 ? 1 : 0);
    }
    public function reworkCompletedSubmit($book_id = null)
    {
        $paperbackModel = new PaperbackModel();
        $completed = $book_id ? $paperbackModel->completedBooksSubmit($book_id) : [];
        $data = [
            'completed' => $completed,
            'title' => 'Paperback Rework Completed',
            'subTitle' => 'Fill in the details to mark the paperback as completed'
        ];

        return view('Book/ReworkCompletedSubmit', $data);
    }
    public function markReworkCompleted()
    {
        $db = \Config\Database::connect();
        $book_id = $this->request->getPost('book_id');
        $db->table('indesign_processing')
            ->where('book_id', $book_id)
            ->update([
                're_completed_flag' => 1,
                'rework_completed_date' => date('Y-m-d H:i:s'),
                'rework_flag' => 0
            ]);
        $db->table('book_tbl')
            ->where('book_id', $book_id)
            ->update([
                'paper_back_readiness_flag' => 1,
                'paper_back_pages' => $this->request->getPost('pages'),
                'paper_back_inr' => $this->request->getPost('price'),
                'paper_back_royalty' => $this->request->getPost('royalty'),
                'paper_back_copyright_owner' => $this->request->getPost('copyright_owner'),
                'paper_back_isbn' => $this->request->getPost('isbn')
            ]);
        $affectedRows = $db->affectedRows();
        return $this->response->setJSON($affectedRows > 0 ? 1 : 0);
    }

    public function overdriveDetails()
    {
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }

        $data = [
            'overdrive' => $this->ebookModel->overdriveDetails(),
            'title'     => 'Overdrive Published Books',
            'subTitle'  => 'View details of published Overdrive books',
        ];

        return view('Book/Overdrive/OverdriveDetails', $data);
    }

    public function overdriveUnpublishedTamil()
    {
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }

        $data = [
            'overdrive' => $this->ebookModel->overdriveDetails(),
            'title'     => 'Overdrive Unpublished Tamil Books',
            'subTitle'  => 'View details of unpublished Tamil books on Overdrive',
        ];

        return view('Book/Overdrive/OverdriveUnpublishedTamil', $data);
    }

    public function overdriveUnpublishedKannada()
    {
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }

        $data = [
            'overdrive' => $this->ebookModel->overdriveDetails(),
            'title'     => 'Overdrive Unpublished Kannada Books',
            'subTitle'  => 'View details of unpublished Kannada books on Overdrive',
        ];

        return view('Book/Overdrive/OverdriveUnpublishedKannada', $data);
    }

    public function overdriveUnpublishedMalayalam()
    {
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }

        $data = [
            'overdrive' => $this->ebookModel->overdriveDetails(),
            'title'     => 'Overdrive Unpublished Malayalam Books',
            'subTitle'  => 'View details of unpublished Malayalam books on Overdrive',
        ];

        return view('Book/Overdrive/OverdriveUnpublishedMalayalam', $data);
    }

    public function overdriveUnpublishedEnglish()
    {
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('adminv4'));
        }

        $data = [
            'overdrive' => $this->ebookModel->overdriveDetails(),
            'title'     => 'Overdrive Unpublished English Books',
            'subTitle'  => 'View details of unpublished English books on Overdrive',
        ];

        return view('Book/Overdrive/OverdriveUnpublishedEnglish', $data);
    }
    public function pratilipiDetails()
    {
        $session = session();
        if ($session->has('user_id')) {
            $data = [
                'title'     => 'Pratilipi Books',
                'subTitle'  => 'Published and Unpublished Book Details',
                'pratilipi' => $this->ebookModel->pratilipiDetails()
            ];
            return view('Book/Pratilipi/PratilipiDetails', $data);
        }
        return redirect()->to(base_url());
    }

    public function pratilipiUnpublishedTamil()
    {
        $session = session();
        if ($session->has('user_id')) {
            $data = [
                'title'    => 'Pratilipi',
                'subTitle' => 'Unpublished Tamil Books',
                'pratilipi' => $this->ebookModel->pratilipiDetails()
            ];

            return view('Book/Pratilipi/PratilipiUnpublishedTamil', $data);
        }
        return redirect()->to(base_url());
    }

    public function pratilipiUnpublishedKannada()
    {
        $session = session();
        if ($session->has('user_id')) {
            $data = [
                'title'    => 'Pratilipi',
                'subTitle' => 'Unpublished Kannada Books',
                'pratilipi' => $this->ebookModel->pratilipiDetails()
            ];

            return view('Book/Pratilipi/PratilipiUnpublishedKannada', $data);
        }
        return redirect()->to(base_url());
    }

    public function pratilipiUnpublishedTelugu()
    {
        $session = session();
        if ($session->has('user_id')) {
            $data = [
                'title'    => 'Pratilipi',
                'subTitle' => 'Unpublished Telugu Books',
                'pratilipi' => $this->ebookModel->pratilipiDetails()
            ];

            return view('Book/Pratilipi/PratilipiUnpublishedTelugu', $data);
        }
        return redirect()->to(base_url());
    }

    public function pratilipiUnpublishedMalayalam()
    {
        $session = session();
        if ($session->has('user_id')) {
            $data = [
                'title'    => 'Pratilipi',
                'subTitle' => 'Unpublished Malayalam Books',
                'pratilipi' => $this->ebookModel->pratilipiDetails()
            ];

            return view('Book/Pratilipi/PratilipiUnpublishedMalayalam', $data);
        }
        return redirect()->to(base_url());
    }

    public function pratilipiUnpublishedEnglish()
    {
        $session = session();
        if ($session->has('user_id')) {
            $data = [
                'title'    => 'Pratilipi',
                'subTitle' => 'Unpublished English Books',
                'pratilipi' => $this->ebookModel->pratilipiDetails()
            ];

            return view('Book/Pratilipi/PratilipiUnpublishedEnglish', $data);
        }
        return redirect()->to(base_url());
    }
    public function overdriveAudiobookDetails()
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to(base_url());
        }

        $data = [
            'title'    => 'Overdrive Audiobooks',
            'subTitle' => 'View all published and unpublished audiobooks',
            'overdrive'=> $this->audiobookModel->overdriveAudioDetails() // summary counts
        ];

        return view('Book/Audio/OverdriveAudiobook', $data);
    }
    public function overaudioUnpublished($language)
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to(base_url());
        }
        $languages = [
            'tamil'     => 1,
            'kannada'   => 2,
            'telugu'    => 3,
            'malayalam' => 4,
            'english'   => 5
        ];

        if (!array_key_exists($language, $languages)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $langId = $languages[$language];

        $data['title']    = 'Overdrive Audiobooks';
        $data['subTitle'] = 'Unpublished audiobooks in ' . ucfirst($language);
        $data['language'] = ucfirst($language);
        $data['books']    = $this->audiobookModel->overdriveAudioDetails($langId); // unpublished books for language

        return view('Book/Audio/OverdriveAudioUnpublished', $data);
    }
    public function pustakaAudioDetails()
    {
        $ebookModel = new \App\Models\EbookModel(); 
        $audiobookModel = new \App\Models\AudiobookModel();



        if (session()->has('user_id')) {
            $data['title'] = "Pustaka Audio Details";
            $data['subTitle'] = "Overview of all books and their status";
            $data['pustaka'] = $audiobookModel->pustakaAudioDetails();
            $data['dashboard_data'] = $ebookModel->getBookDashboardData();

            return view('Book/PustakaAudioDetails', $data);
        } else {
            return redirect()->to(site_url('adminv4/index'));
        }
    }
    public function googleAudioDetails()
    {
        $session = session();

        if ($session->has('user_id')) {
            $data['title'] = "Google Audio Books";        
            $data['subTitle'] = "Channel wise details";   
            $data['google'] = $this->audiobookModel->googleAudioDetails();

            return view('Book/Audio/GoogleAudioDetails', $data);
        } else {
            return redirect()->to(base_url());
        }
    }
    public function googleAudioUnpublished($langKey = null)
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }
        $languages = [
            'tamil'     => 1,
            'kannada'   => 2,
            'telugu'    => 3,
            'malayalam' => 4,
            'english'   => 5
        ];
        if (!isset($languages[$langKey])) {
            return redirect()->back()->with('error', 'Invalid language selected');
        }

        $langId = $languages[$langKey];

        $data = [
            'title'    => 'Google Audio Unpublished - ' . ucfirst($langKey),
            'subTitle' => 'Unpublished Google Audio Books in ' . ucfirst($langKey),
            'books'    => $this->audiobookModel->googleAudioDetails($langId),
            'langKey'  => $langKey
        ];

        return view('Book/Audio/GoogleAudioUnpublished', $data);
    }
    public function storytelAudioDetails()
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        $data = [
            'title'    => 'Storytel Audio Dashboard',
            'subTitle' => 'Published and Unpublished Storytel Audio Books',
            'storytell' => $this->audiobookModel->storytelAudioDetails()
        ];

        return view('Book/Audio/StorytelAudioDetails', $data);
    }
    public function storytelAudioUnpublished($langKey = null)
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }
        $languages = [
            'tamil'     => 1,
            'kannada'   => 2,
            'telugu'    => 3,
            'malayalam' => 4,
            'english'   => 5
        ];

        if (!isset($languages[$langKey])) {
            return redirect()->back()->with('error', 'Invalid language selected');
        }
        $langId = $languages[$langKey];
        $storytelData = $this->audiobookModel->storytelAudioDetails();

        $data = [
            'title'    => 'Storytel Audio Unpublished - ' . ucfirst($langKey),
            'subTitle' => 'Unpublished Storytel Audio Books in ' . ucfirst($langKey),
            'books'    => $storytelData['storytel_' . $langKey . '_books'] ?? [],
            'langKey'  => $langKey
        ];

        return view('Book/Audio/StorytelAudioUnpublished', $data);
    }
    public function AmazonPaperbackDetails()
    {
        $data = [
            'title'    => 'Amazon Paperback Dashboard',
            'subTitle' => 'Published and Unpublished Books Language-wise',
            'amazon'   => $this->paperbackModel->getPaperbackSummary()
        ];

        return view('Book/Amazon/AmazonPaperbackDetails', $data);
    }
    public function amazonUnpublishedBooks($langId)
    {
        $languages = [
            1 => 'tamil',
            2 => 'kannada',
            3 => 'telugu',
            4 => 'malayalam',
            5 => 'english'
        ];

        $langName = $languages[$langId] ?? null;
        if (!$langName) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Language not found");
        }

        $books = $this->paperbackModel->getUnpublishedBooksByLanguage($langId);

        $data = [
            'title'    => ucfirst($langName) . ' Unpublished Books',
            'subTitle' => 'Amazon Paperback unpublished books details',
            'amazon'   => [$langName => $books]
        ];

        return view('Book/Amazon/AmazonPaperbackUnpublished', $data);
    }
    public function flipkartPaperbackDetails()
    {
        $model = new \App\Models\paperbackModel();

        $data = [
            'title'     => 'Flipkart Paperback Books',
            'subTitle'  => 'Flipkart Published & Unpublished Summary',
            'flipkart'  => $model->getFlipkartPaperbackSummary()
        ];

        return view('Book/Flipkart/FlipkartPaperbackDetails', $data);
    }

    // Unpublished books for a specific language
    public function flipkartUnpublishedBooks($langId)
    {
        $languages = [
            1 => 'tamil',
            2 => 'kannada',
            3 => 'telugu',
            4 => 'malayalam',
            5 => 'english'
        ];

        $langName = $languages[$langId] ?? null;
        if (!$langName) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Language not found");
        }

        $books = $this->paperbackModel->getFlipkartUnpublishedBooksByLanguage($langId);

        $data = [
            'title'    => ucfirst($langName) . ' Unpublished Books',
            'subTitle' => 'Flipkart Paperback unpublished books details',
            'flipkart' => [$langName => $books]
        ];

        return view('Book/Flipkart/FlipkartPaperbackUnpublished', $data);
    }
    public function editBook($book_id = null)
    {
        if (! session()->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        if (! $book_id) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book ID missing');
        }
       $mdl_data = $this->bookModel->getBookDetailsForEdit($book_id);

        $data = [
            'book_details'              => $mdl_data['book_details'] ?? [],
            'author_details'            => $mdl_data['author_details'] ?? [],
            'user_details'              => $mdl_data['user_details'] ?? [],
            'publisher_details'         => $mdl_data['publisher_details'] ?? [],
            'copyright_mapping_details' => $mdl_data['copyright_mapping_details'] ?? [],
            'copy_right_owner_id'       => $mdl_data['copy_right_owner_id'] ?? '',
            'copy_right_owner_name'     => $mdl_data['copy_right_owner_name'] ?? '-',
            'paper_back_owner_id'       => $mdl_data['paper_back_owner_id'] ?? '',
            'paper_back_owner_name'     => $mdl_data['paper_back_owner_name'] ?? '-',
            'title'                     => '',
        ];

        if (array_key_exists('narrator_details', $mdl_data)) {
            $data['narrator_details'] = $mdl_data['narrator_details'];
            $data['audio_chapters']   = $mdl_data['audio_chapters'] ?? [];
        }

        return view('Book/EditBook', $data);
    }

    public function editBookBasicDetails($book_id = null)
    {
        if (!session()->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        if (!$book_id) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book ID missing');
        }

        $book_data = $this->bookModel->getBookDetailsForEdit($book_id);

        if (empty($book_data)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book not found');
        }

        $data = [
            'book_details'   => $book_data['book_details'],
            'author_details' => $book_data['author_details'],
            'user_details'   => $book_data['user_details'],
            'publisher_details' => $book_data['publisher_details'],
            'copyright_mapping_details' => $book_data['copyright_mapping_details'],
            'narrator_details' => $book_data['narrator_details'] ?? [],
            'audio_chapters' => $book_data['audio_chapters'] ?? [],
            'author_list'    => $this->authorModel->getAuthorDetails(),
            'language_list'  => $this->languageModel->getAllLanguages(),
            'genre_list'     => $this->genreModel->getAllGenres(),
            'type_list'      => $this->typeModel->getAllTypes(),
            'title'                     => '',          
            'subTitle'                  => '', 
        ];

        return view('Book/EditBasicDetails', $data);
    }
    public function editBookBasicDetailsPost()
    {
        if (!session()->has('user_id')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $postData = $this->request->getPost();
        $book_id = $postData['book_id'] ?? null;

        if (!$book_id) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Book ID missing']);
        }

        $result = $this->bookModel->editBookBasicDetails($postData);

        if ($result == 1) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Edited Book Details Successfully']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No changes were made or an error occurred']);
        }
    }
    public function editBookUrlDetails($book_id = null)
    {
        // Check session
        if (!session()->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        if (!$book_id) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book ID missing');
        }

        $book_data = $this->bookModel->getBookDetailsForEdit($book_id);

        if (empty($book_data)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book not found');
        }

        $data = [
            'book_details' => $book_data['book_details'],
            'author_details' => $book_data['author_details'] ?? [],
            'user_details' => $book_data['user_details'] ?? [],
            'publisher_details' => $book_data['publisher_details'] ?? [],
            'copyright_mapping_details' => $book_data['copyright_mapping_details'] ?? [],
            'narrator_details' => $book_data['narrator_details'] ?? [],
            'audio_chapters' => $book_data['audio_chapters'] ?? [],
            'title'                     => '',          
            'subTitle'                  => '', 
        ];

        return view('Book/EditUrlDetails', $data);
    }
    public function editUrlDetailsPost()
    {
        if (!session()->has('user_id')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        $postData = $this->request->getPost();
        if (empty($postData['book_id'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Book ID missing'
            ]);
        }

        $result = $this->bookModel->editBookUrlDetails($postData);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Book URL details updated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No changes were made or an error occurred'
            ]);
        }
    }
    public function editBookIsbnDetails($book_id = null)
    {
        if (!session()->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        if (!$book_id) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book ID missing');
        }
        $book_data = $this->bookModel->getBookDetailsForEdit($book_id);

        if (empty($book_data)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book not found');
        }

        $data = [
        'book_details' => $book_data['book_details'],
        'title'        => '',
        'subTitle'     => '',
        ];
        return view('Book/EditIsbnDetails', $data);
    }
    public function editBookIsbnDetailsPost()
    {
        if (!session()->has('user_id')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        $postData = $this->request->getPost();

        if (empty($postData['book_id'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Book ID missing'
            ]);
        }

        $result = $this->bookModel->editBookIsbnDetails($postData);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Book ISBN details updated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No changes were made or an error occurred'
            ]);
        }
    }

    public function editPaperbackDetails($id)
    {
        $bookModel = new BookModel();
        $mdl_data = $bookModel->getBookDetailsForEdit($id);

        if (!$mdl_data) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Book not found");
        }
        $data = [
            'book_details' => $mdl_data['book_details'],
            'title'        => '',
            'subTitle'     => '',
        ];

        return view('Book/EditPaperbackDetails', $data);
    }


    public function editBookPaperbackDetailsPost()
    {
        $bookModel = new BookModel();
        $result = $bookModel->editBookPaperbackDetails($this->request->getPost());

        return $this->response->setJSON($result);
    }
    public function audibleDetails()
    {
        $session = session();
        $audiobookModel = new \App\Models\AudiobookModel(); 

        if ($session->has('user_id')) {
            $data = [
                'audible'  => $audiobookModel->audibleAudioDetails(),
                'title'    => '',
                'subTitle' => ''
            ];

            return view('Book/Audio/AudibleAudioDetails', $data);
        } else {
            return redirect()->to(base_url('/')); 
        }
    }
    public function audibleUnpublished($langId)
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to(base_url('/'));
        }

        $languages = [
            1 => 'tamil',
            2 => 'kannada',
            3 => 'telugu',
            4 => 'malayalam',
            5 => 'english'
        ];

        $langName = $languages[$langId] ?? null;
        if (!$langName) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Language not found");
        }

        $bookModel = new AudiobookModel();
        $books = $bookModel->audibleAudioDetails($langId);
        $data = [
            'title'    => ucfirst($langName) . ' Unpublished Audible Books',
            'subTitle' => 'Audible unpublished audio books details',
            'books'    => $books[$langName] ?? [],  
            'langName' => $langName
        ];

        return view('Book/Audio/AudibleUnpublished', $data);
    }
    public function kukufmDetails()
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to(base_url());
        }

        $data['kukufm'] = $this->audiobookModel->kukufmAudioDetails();

        $data['title'] = "Kukufm Audio Books Dashboard";
        $data['subTitle'] = "Overview of all Kukufm audio books";
        // Optional debug
        // dd($data['kukufm']);
        return view('Book/Audio/KukufmAudioDetails', $data);
    }
    public function kukufmUnpublished($langId)
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to(base_url('/'));
        }
        $languages = [
            1 => 'tamil',
            2 => 'kannada',
            3 => 'telugu',
            4 => 'malayalam',
            5 => 'english'
        ];
        $langName = $languages[$langId] ?? null;
        if (!$langName) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Language not found");
        }

        $bookModel = new \App\Models\Audiobookmodel();
        $books = $bookModel->kukufmAudioDetails($langId);

        $data = [
            'title'    => ucfirst($langName) . ' Unpublished Kukufm Books',
            'subTitle' => 'List of unpublished Kukufm audio books in ' . ucfirst($langName),
            'books'    => $books[$langName] ?? [], 
            'langName' => $langName
        ];

        return view('Book/Audio/KukufmUnpublished', $data);
    }
    public function youtubeDetails()
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to(base_url());
        }
        $youtube = $this->audiobookModel->youtubeAudioDetails();
        $languagesKeys = ['tam', 'kan', 'tel', 'mal', 'eng'];
        foreach ($languagesKeys as $key) {
            if (!isset($youtube['you_' . $key . '_cnt'])) {
                $youtube['you_' . $key . '_cnt'] = 0;
            }
            if (!isset($youtube['you_' . $key . '_unpub_cnt'])) {
                $youtube['you_' . $key . '_unpub_cnt'] = 0;
            }
        }

        $data['youtube'] = $youtube;
        $data['title']   = "YouTube Audio Books Dashboard";
        $data['subTitle']= "Overview of all YouTube audio books";

        return view('Book/Audio/YouTubeAudioDetails', $data);
    }
    public function youtubeUnpublished($langId)
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to(base_url('/'));
        }

        $languages = [
            1 => 'tam',  
            2 => 'kan',  
            3 => 'tel',  
            5 => 'eng', 
        ];

        $langNames = [
            'tam' => 'Tamil',
            'kan' => 'Kannada',
            'tel' => 'Telugu',
            'eng' => 'English',
        ];

        $langKey = $languages[$langId] ?? null;
        if (!$langKey) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Language not found");
        }

        $youtube = $this->audiobookModel->youtubeAudioDetails();
        $books = $youtube['you_' . $langKey . '_books'] ?? [];

        $data = [
            'books'    => $books,
            'langName' => $langNames[$langKey] ?? $langKey,
            'title'    => $langNames[$langKey] . ' Unpublished YouTube Audio Books',
            'subTitle' => 'List of unpublished YouTube audio books in ' . ($langNames[$langKey] ?? $langKey),
        ];

        return view('Book/Audio/YouTubeUnpublished', $data);
    }
    public function editAudioBookDetails($book_id)
    {
        if (!session()->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        $bookModel = new BookModel();
        $narratorModel = new narratorModel();

        $mdl_data = $bookModel->audioBookDetailsForEdit($book_id);

        $data = [
    'title'                => '',
    'book_details'         => $mdl_data['book_details'],
    'narrator_details'     => $mdl_data['narrator_details'] ?? null,
    'audio_chapters'       => $mdl_data['audio_chapters'] ?? [],
    'author_details'       => $mdl_data['author_details'],
    'user_details'         => $mdl_data['user_details'],
    'publisher_details'    => $mdl_data['publisher_details'],
    'copyright_mapping_details' => $mdl_data['copyright_mapping_details'],
    'narrator_list'        => $narratorModel->getAllNarrators(),
];
           return view('Book/editAudiobookDetails', $data);
             
    }

    public function editAudioBookDetailsPost()
    {
        $bookModel = new BookModel();
        $result = $bookModel->editAudioBookDetails($this->request);
        return $this->response->setJSON($result);
    }
}