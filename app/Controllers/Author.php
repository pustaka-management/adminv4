<?php

namespace App\Controllers;

use App\Models\AuthorModel;
use CodeIgniter\Controller;
use App\Controllers\BaseController;

class Author extends BaseController
{
    protected $authorModel;
    protected $helpers = ['form', 'url', 'file', 'email', 'html', 'cookie', 'string'];

    public function __construct()
    {
        helper($this->helpers);

        $this->authorModel = new AuthorModel();
        session();
    }


    public function authordashboard()
    {
        if (!$this->session->get('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        $data['royalty_author_launches'] = $this->authorModel->getRoyaltyDistribution();
        $data['free_author_launches'] = $this->authorModel->getFreeDistribution();
        $data['magpub_author_launches'] = $this->authorModel->getMagpubDistribution();
        $data['authors_count'] = $this->authorModel->getDashboardData();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('author/authorsDashboard', $data);
    }
    public function royaltyauthordashboard()
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }
        $data['get_language_wise_author_count'] = $this->authorModel->getRoyaltyDashboardData();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('author/royaltyAuthorsDashboard', $data);
    }
    public function freeauthordashboard()
    {
        $session = session();

        if (!$session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        $data['get_language_wise_author_count'] = $this->authorModel->getFreeDashboardData();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('author/freeAuthorsDashboard', $data);
    }
    public function magpubauthordashboard()
    {
        $session = session();

        if (!$session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        $data['get_language_wise_author_count'] = $this->authorModel->getMagpubDashboardData();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('author/magpubAuthorsDashboard', $data);
    }
    public function manageauthors()
    {
        if (!session()->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }
        $data['authors_metadata'] = $this->authorModel->getAuthorsMetadata();
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('author/manageAuthors', $data);
    }
    public function editauthor()
    {
        $session = session();

        if (!$session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }
        $author_id = $this->request->getUri()->getSegment(3);
        $data = $this->authorModel->editAuthor($author_id);
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('author/editAuthorView', $data);
    }
    public function addauthorcopyrightdetails($author_id)
    {
        $data['title'] = '';
        $data['subTitle'] = '';
        $data['author_id']=$author_id;

        return view('author/addCopyrightOwner', $data);
    }
    public function saveauthorcopyrightdetails()
    {
        $copyrightOwner = $this->request->getPost('copyright_owner');
        $authorId = $this->request->getPost('author_id');

        if (!$copyrightOwner || !$authorId) {
            return redirect()->back()->with('error', 'Form data missing.');
        }

        $data = [
            'copyright_owner' => $copyrightOwner,
            'author_id' => $authorId,
            'date_created' => date('Y-m-d H:i:s')
        ];

        if ($this->authorModel->addCopyrightMapping($data)) {
            return redirect()->back()->with('success', 'Copyright owner added successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to insert record.');
        }
    }

    public function authorpublishdetails($author_id = null, $author_name = null)
    {
        if (!session()->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        $data['book_details'] = $this->authorModel->getauthorPubDetailsDashboard($author_id);
        $data['author_name'] = urldecode($author_name);
        $data['title'] = '';
        $data['subTitle'] = '';

        return view('author/authorPublishDetails', $data);
    }
    public function authorDetails()
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }
        $author_id = $this->request->getUri()->getSegment(3);    
        $data['title'] = '';
        $data['subTitle'] = '';
        $data['author_id'] = $author_id;
        $data['author_details'] = $this->authorModel->getAuthorDetailsDashboardData($author_id);
        $data['ebook_count'] = $this->authorModel->getAuthorEbookDetails($author_id);
        $data['audio_count'] = $this->authorModel->getAuthorAudiobkDetails($author_id);
        $data['paperback'] = $this->authorModel->getAuthorPaperbackDetails($author_id);
        $data['count'] = $this->authorModel->booksTotalCount($author_id);
        $data['copyright_owner'] = $this->authorModel->copyrightOwnerDetails($author_id);    
        $data['royalty'] = $this->authorModel->authorWiseRoyalty($author_id);
        $data['channel_wise'] = $this->authorModel->authorWiseRoyalty($author_id);
        $data['channel_chart'] = $this->authorModel->channelWiseChart($author_id);
        $data['author'] = $this->authorModel->royaltySettlement($author_id);
        $data['settlements'] = $this->authorModel->authorRoyaltySettlementDetails($author_id);
        $data['list_details'] = $this->authorModel->authorSettlementList($author_id);
        $data['bookwise'] = $this->authorModel->authorBookroyaltyDetails($author_id);
        $data['pending'] = $this->authorModel->authorPendings($author_id);
    
        // echo "<pre>";
        // print_r($data['list_details']);
        
        return view('author/authorDetails', $data);
    }

    public function authorpustakadetails()
    {
        $data['title'] = '';
        $data['subTitle'] = '';
        $author_id = $this->request->getUri()->getSegment(3);
        $data['pustakabooks'] = $this->authorModel->authorPustakaDetails($author_id);
        return view('author/authorPustakaDetails', $data);

    }

    public function authorstoryteldetails()
    {
        $data['title'] = '';
        $data['subTitle'] = '';
        $author_id = $this->request->getUri()->getSegment(3);
        $data['storytel'] = $this->authorModel->authorStorytelDetails($author_id);
        return view('author/authorStorytelDetails', $data);
    }

    public function authoroverdrivedetails()
    {
        $data['title'] = '';
        $data['subTitle'] = '';
        $author_id = $this->request->getUri()->getSegment(3);
        $data['overdrive'] = $this->authorModel->authorOverdriveDetails($author_id);
        return view('author/authorOverdriveDetails', $data);

    }

    public function authorpratilipidetails()
    {
        $data['title'] = '';
        $data['subTitle'] = '';
        $author_id = $this->request->getUri()->getSegment(3);
        $data['pratilipi'] = $this->authorModel->authorPratilipiDetails($author_id);
        return view('author/authorPratilipiDetails', $data);
    }

    public function authorscribddetails()
    {
        $data['title'] = '';
        $data['subTitle'] = '';
        $author_id = $this->request->getUri()->getSegment(3);
        $data['scribd'] = $this->authorModel->authorScribdDetails($author_id);
        return view('author/authorScribdDetails', $data);

    }
    public function authoramazondetails()
    {
        $data['title'] = '';
        $data['subTitle'] = '';
        $author_id = $this->request->getUri()->getSegment(3);
        $data['channel_wise'] = $this->authorModel->authorAmazonDetails($author_id);
        return view('author/authorAmazonDetails', $data);
    }
    public function authorsgoogledetails()
    {
        $data['title'] = '';
        $data['subTitle'] = '';
        $author_id = $this->request->getUri()->getSegment(3);
        $data['googlebooks']=$this->authorModel->authorGoogleDetails($author_id);

        
        return view('author/authorGoogleDetails',$data);
    }
    public function editauthorbasicdetails()
    {
        if (!$this->session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }
        $author_id = $this->request->getUri()->getSegment(3);
        $data = $this->authorModel->editAuthor($author_id);
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('author/editAuthorBasicDetailsView', $data);
    }
    public function editauthorbasicdetailspost()
    {
        $post = $this->request->getPost();
        $result = $this->authorModel->editAuthorBasicDetails($post);
        return $this->response->setJSON(['status' => $result]);
    }
    public function editauthoragreementdetails()
    {
        if (!$this->session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }
        $author_id = $this->request->getUri()->getSegment(3);
        $data = $this->authorModel->editAuthor($author_id);
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('author/editAuthorAgreementDetailsView', $data);
    }

    public function editauthoragreementdetailspost()
    {
        $post = $this->request->getPost();
        $result = $this->authorModel->editAuthorAgreementDetails($post);
        return $this->response->setJSON(['status' => $result]);
    }

    public function editauthorpublisherdetails()
    {
        if (!$this->session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }
        $author_id = $this->request->getUri()->getSegment(3);
        $data = $this->authorModel->editAuthor($author_id);
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('author/editAuthorPublisherDetailsView', $data);
    }

    public function editauthorpublisherdetailspost()
    {
        $post = $this->request->getPost();
        $result = $this->authorModel->editAuthorPublisherDetails($post);
        return $this->response->setJSON(['status' => $result]);
    }

    public function editauthorbankdetails()
    {
        if (!$this->session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }     
        $author_id = $this->request->getUri()->getSegment(3);
        $data = $this->authorModel->editAuthor($author_id);
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('author/editAuthorBankDetailsView', $data);
    }
    public function editauthorbankdetailspost()
    {
        $post = $this->request->getPost();
        $result = $this->authorModel->editAuthorBankDetails($post);
        return $this->response->setJSON(['status' => $result]);
    }
    public function editauthornamedetails()
    {
        if (!$this->session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        $author_id = $this->request->getUri()->getSegment(3);
        $data = $this->authorModel->editAuthor($author_id);
        $data['author_id'] = $author_id;
        $data['title'] = '';
        $data['subTitle'] = '';

        $db = \Config\Database::connect();
        $data['languages'] = $db->table('language_tbl')
                            ->select('language_id, language_name')
                            ->orderBy('language_name', 'ASC')
                            ->get()
                            ->getResultArray();

        return view('author/editAuthorNameDetailsView', $data);
    }

    public function editauthornamedetailspost()
    {
        $post = $this->request->getPost('author_language_details'); 

        if (!$post) {
            return $this->response->setJSON(['status' => 0, 'message' => 'No data received']);
        }

        $result = $this->authorModel->updateAuthorLanguageDetails($post);
        return $this->response->setJSON(['status' => $result]);
    }

    public function addauthornamelanguagepost()
    {
        $data = [
            'author_id' => $this->request->getPost('author_id'),
            'language_id' => $this->request->getPost('language_id'),
            'display_name1' => $this->request->getPost('display_name1'),
            'display_name2' => $this->request->getPost('display_name2'),
            'regional_author_name' => $this->request->getPost('regional_author_name')
        ];

        if ($this->authorModel->addAuthorLanguageName($data)) {
            return $this->response->setJSON(['status' => true, 'message' => 'Author language added successfully']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Failed to add author language']);
        }
    }

    public function editauthorlinks()
    {
        if (!$this->session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        $author_id = $this->request->getUri()->getSegment(3);
        $data['author_link_data'] = $this->authorModel->getEditAuthorLinkData($author_id);
        $data['author_id'] = $author_id;
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('author/editAuthorLinks', $data);
    }
    public function editauthorlinkpost()
    {
        $post = $this->request->getPost();
        $result = $this->authorModel->editAuthorLinks($post);
        return $this->response->setJSON(['status' => $result]);
    }
    public function editauthorsocialmedialinks()
    {
        if (!$this->session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }

        $author_id = $this->request->getUri()->getSegment(3);
        $data['author_link_data'] = $this->authorModel->getEditAuthorLinkData($author_id);
        $data['author_id'] = $author_id;
        $data['title'] = '';
        $data['subTitle'] = '';
        return view('author/editAuthorSocialMediaLinks', $data);
    }
    public function editauthorsociallinkpost()
    {
        $post = $this->request->getPost();
        $result = $this->authorModel->editAuthorSocialLinks($post);
        return $this->response->setJSON(['status' => $result]);
    }
    public function editauthorpost()
    {
        $result = $this->authorModel->editAuthorPost();
        return $this->response->setJSON($result);
    }
    public function addauthor()
    {
        if (!session()->has('user_id'))
            return redirect()->to('/adminv4/index');

        $data['title'] = '';
        $data['subTitle'] = '';
        $data['publishers'] = $this->authorModel->getActivePublishers();

        return view('author/addAuthorView', $data);
    }
    public function addauthorpost()
    {
        $post = $this->request->getPost();

        try {
            $result = $this->authorModel->addAuthor($post);
            return $this->response->setJSON(['status' => $result]);
        } catch (\Throwable $e) {
            log_message('error', 'AddAuthor Error: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 0, 'error' => $e->getMessage()]);
        }
    }
    public function getpublishercopyrightowner()
    {
        $publisher_id = $this->request->getPost('publisher_id');

        if (!$publisher_id)
            return $this->response->setJSON(['status' => 0, 'error' => 'Missing publisher ID']);

        $db = \Config\Database::connect();
        $query = $db->query("SELECT copyright_owner FROM publisher_tbl WHERE publisher_id = ?", [$publisher_id]);

        if ($query->getNumRows() > 0) {
            return $this->response->setJSON([
                'status' => 1,
                'copyright_owner' => $query->getRow()->copyright_owner
            ]);
        }

        return $this->response->setJSON(['status' => 0, 'error' => 'Publisher not found']);
    }
    public function activateauthordetails()
    {
        $session = session();

        if (!$session->has('user_id')) {
            return redirect()->to('/adminv4/index');
        }
        $author_id = $this->request->getUri()->getSegment(3);
        $data['title'] = '';
        $data['subTitle'] = '';
        $data['author'] = $this->authorModel->getActivateAuthorDetails($author_id);

        return view('author/activateAuthorDetails', $data);
    }

    public function activateauthor()
    {
        $author_id = $this->request->getUri()->getSegment(3);
        $author_id = $this->request->getPost('author_id'); 
        $send_mail_flag = $this->request->getPost('send_mail');

        $result = $this->authorModel->activateAuthor($author_id, $send_mail_flag);
        return $this->response->setJSON(['status' => $result]);
    }

    public function deactivateauthor()
    {
        $author_id = $this->request->getUri()->getSegment(3);
        $result = $this->authorModel->deactivateAuthor($author_id);
        return $this->response->setJSON(['status' => $result]);
    }

    public function deleteauthor()
    {
        $author_id = $this->request->getUri()->getSegment(3);
        $result = $this->authorModel->deleteAuthor($author_id);
        return $this->response->setJSON(['status' => $result]);
    }
}
