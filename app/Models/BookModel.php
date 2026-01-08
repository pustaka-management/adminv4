<?php

namespace App\Models;

use CodeIgniter\Model;

class BookModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        helper('date');
    }

    
    public function getBooksDetails()
    {
        $sql = "SELECT 
                    book_tbl.book_id, 
                    book_tbl.book_title, 
                    book_tbl.regional_book_title,
                    book_tbl.copyright_owner,
                    book_tbl.paper_back_pages AS number_of_page, 
                    book_tbl.paper_back_inr, 
                    book_tbl.author_name as author_id,
                    author_tbl.author_name
                FROM 
                    book_tbl
                JOIN 
                    author_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE 
                    book_tbl.status = 1";

        $query = $this->db->query($sql);
        return $query->getResultArray();
       
    }
    public function getAllStages(): array
    {
        return [
            1 => "Scan",
            2 => "OCR",
            3 => "Level 1 Proof Read",
            4 => "Level 2 Proof Read",
            5 => "Book Generation",
        ];
    }

    public function getEditBookDetails($book_id)
    {
        $result = [];

        //  Book details
        $book_details_sql   = "SELECT * FROM book_tbl WHERE book_id = ?";
        $book_details_query = $this->db->query($book_details_sql, [$book_id]);
        $book_details       = $book_details_query->getRowArray();

        if (! $book_details) {
            return [];
        }

        //  Language
        $lang_details_sql   = "SELECT * FROM language_tbl WHERE language_id = ?";
        $lang_details_query = $this->db->query($lang_details_sql, [$book_details['language']]);
        $lang_details       = $lang_details_query->getRowArray();
        $book_details['language'] = $lang_details['language_name'] ?? '';

        //  Genre
        $genre_details_sql   = "SELECT * FROM genre_details_tbl WHERE genre_id = ?";
        $genre_details_query = $this->db->query($genre_details_sql, [$book_details['genre_id']]);
        $genre_details       = $genre_details_query->getRowArray();
        $book_details['genre_id'] = $genre_details['genre_name'] ?? '';

        //  Created By
        $created_by_sql   = "SELECT * FROM users_tbl WHERE user_id = ?";
        $created_by_query = $this->db->query($created_by_sql, [$book_details['created_by']]);
        $created_by       = $created_by_query->getRowArray();
        $book_details['created_by'] = $created_by['username'] ?? '';

        $result['book_details'] = $book_details;

        //  Author
        $author_sql   = "SELECT * FROM author_tbl WHERE author_id = ?";
        $author_query = $this->db->query($author_sql, [$book_details['author_name']]);
        $author       = $author_query->getRowArray();

        if ($author) {
        $author['status'] = ($author['status'] == 0) ? "Inactive" : "Active";
        $result['author_details'] = $author;
        }

        //  User Details (Copyright owner)
        $user_sql   = "SELECT * FROM users_tbl WHERE user_id = ?";
        $user_query = $this->db->query($user_sql, [$book_details['copyright_owner']]);
        $user       = $user_query->getRowArray();
        $result['user_details'] = $user ?? [];

        //  Publisher
        $publisher_sql   = "SELECT * FROM publisher_tbl WHERE copyright_owner = ?";
        $publisher_query = $this->db->query($publisher_sql, [$book_details['copyright_owner']]);
        $publisher       = $publisher_query->getRowArray();
        $result['publisher_details'] = $publisher ?? [];

        //  Copyright Mapping
        $copyright_sql   = "SELECT * FROM copyright_mapping WHERE copyright_owner = ?";
        $copyright_query = $this->db->query($copyright_sql, [$book_details['copyright_owner']]);
        $copyrights      = $copyright_query->getResultArray();
        $result['copyright_mapping_details'] = $copyrights;

        //  Narrator + Audio Chapters (if audiobook)
        if ($book_details['type_of_book'] == 3) {
        $narrator_sql   = "SELECT * FROM narrator_tbl WHERE narrator_id = ?";
        $narrator_query = $this->db->query($narrator_sql, [$book_details['narrator_id']]);
        $narrator       = $narrator_query->getRowArray();
        $result['narrator_details'] = $narrator ?? [];

        $audio_sql   = "SELECT * FROM audio_book_details WHERE book_id = ?";
        $audio_query = $this->db->query($audio_sql, [$book_id]);
        $audio       = $audio_query->getResultArray();
        $result['audio_chapters'] = $audio;
        }

    return $result;
    }
    public function getBookDetailsForEdit($book_id)
{
    $db = \Config\Database::connect();
    $result = [];

    // Book Details
    $book_query = $db->query("
        SELECT 
            b.*, 
            l.language_name, 
            g.genre_name
        FROM 
            book_tbl b
        LEFT JOIN 
            language_tbl l ON b.language = l.language_id
        LEFT JOIN 
            genre_details_tbl g ON b.genre_id = g.genre_id
        WHERE 
            b.book_id = ?
    ", [$book_id]);

    $book_details = $book_query->getRowArray();

    if (!$book_details) return [];

    $result['book_details'] = $book_details;

    //  Author Details
    $author_id = $book_details['author_name'] ?? null;
    $author_details = [];
    if ($author_id) {
        $author_query = $db->query("SELECT * FROM author_tbl WHERE author_id = ?", [$author_id]);
        $author_details = $author_query->getRowArray() ?: [];
        if ($author_details) {
            $author_details['status'] = ($author_details['status'] == 0) ? 'Inactive' : 'Active';
        }
    }
    $result['author_details'] = $author_details;

    // User Details (Copyright Owner)
    $copyright_owner = $book_details['copyright_owner'] ?? null;
    $paperback_owner = $book_details['paper_back_copyright_owner'] ?? null;

    $user_details = [];

    if ($copyright_owner || $paperback_owner) {
        $user_ids = array_unique(array_filter([$copyright_owner, $paperback_owner]));

        // Fetch details for all owners in one query
        $placeholders = implode(',', array_fill(0, count($user_ids), '?'));
        $user_query = $db->query("SELECT * FROM users_tbl WHERE user_id IN ($placeholders)", $user_ids);
        $user_details = $user_query->getResultArray() ?: [];
    }

    $result['user_details'] = $user_details;

    // Publisher Details
    $publisher_details = [];
    if ($copyright_owner) {
        $publisher_query = $db->query("SELECT * FROM publisher_tbl WHERE copyright_owner = ?", [$copyright_owner]);
        $publisher_details = $publisher_query->getRowArray() ?: [];
    }
    $result['publisher_details'] = $publisher_details;

    // Copyright Mapping
   
    $copyright_owner_id    = $book_details['copyright_owner'] ?? null;
    $paperback_owner_id    = $book_details['paper_back_copyright_owner'] ?? null;

    $copy_right_owner_name = '-';
    $paper_back_owner_name = '-';
    $copy_right_owner_id   = null;
    $paper_back_owner_id   = null;

    // Helper function: Get author from copyright mapping
    $getAuthorFromMapping = function($ownerId) use ($db) {
    if (!$ownerId) return ['id' => null, 'name' => '-'];

    // Get mapping
    $mapQuery = $db->query("SELECT * FROM copyright_mapping WHERE copyright_owner = ?", [$ownerId]);
    $map = $mapQuery->getRowArray();

    if (!$map) return ['id' => null, 'name' => '-'];

    // Get author details
    $authorQuery = $db->query("SELECT author_id, author_name, copyright_owner FROM author_tbl WHERE author_id = ?", [$map['author_id']]);
    $author = $authorQuery->getRowArray();

    if ($author) {
        return [
            'id'   => $author['copyright_owner'],
            'name' => $author['author_name']
        ];
    }

    return ['id' => null, 'name' => '-'];
};

    // Get both owners
    $copy_result       = $getAuthorFromMapping($copyright_owner_id);
    $paperback_result  = $getAuthorFromMapping($paperback_owner_id);

    $copy_right_owner_name = $copy_result['name'];
    $copy_right_owner_id   = $copy_result['id'];

    $paper_back_owner_name = $paperback_result['name'];
    $paper_back_owner_id   = $paperback_result['id'];

    // Combine both copyright mapping details
    $copyright_mapping_query = $db->query("
        SELECT * FROM copyright_mapping
        WHERE copyright_owner IN (?, ?)
    ", [$copyright_owner_id, $paperback_owner_id]);

    $copyright_mapping_details = $copyright_mapping_query->getResultArray();
    $result['copyright_mapping_details'] = $copyright_mapping_details;

    // Add owner info to result
    $result['copy_right_owner_id']   = $copy_right_owner_id;
    $result['copy_right_owner_name'] = $copy_right_owner_name;
    $result['paper_back_owner_id']   = $paper_back_owner_id;
    $result['paper_back_owner_name'] = $paper_back_owner_name;

    // Narrator + Audio Chapters (if audiobook)
    if (($book_details['type_of_book'] ?? null) == 3) {
        $narrator_query = $db->query("SELECT * FROM narrator_tbl WHERE narrator_id = ?", [$book_details['narrator_id']]);
        $narrator_details = $narrator_query->getRowArray() ?: [];
        $result['narrator_details'] = $narrator_details;

        $audio_query = $db->query("SELECT * FROM audio_book_details WHERE book_id = ?", [$book_id]);
        $audio_chapters = $audio_query->getResultArray() ?: [];
        $result['audio_chapters'] = $audio_chapters;
    }

    return $result;
}


    public function editBookBasicDetails($postData)
    {
        $db = \Config\Database::connect();

        $book_id             = (int)$postData['book_id'];
        $book_title          = $postData['book_title'];
        $regional_book_title = $postData['regional_book_title'];
        $url_name            = $postData['url_name'];
        $author_id           = (int)$postData['author_id'];
        $language_id         = (int)$postData['language_id'];
        $genre_id            = (int)$postData['genre_id'];
        $category_name       = $postData['category_name'];
        $num_pages           = (int)$postData['num_pages'];
        $cost_inr            = (float)$postData['cost_inr'];
        $cost_usd            = (float)$postData['cost_usd'];
        $description         = $postData['description'];
        $ebook_remarks       = $postData['ebook_remarks'];
        $book_id_mapping     = $postData['book_id_mapping'] ?: null;

        // Fetch current book details
        $book_details = $db->table('book_tbl')->where('book_id', $book_id)->get()->getRowArray();
        if (!$book_details) return 0;

        $link_change = ($book_details['language'] != $language_id || $book_details['genre_id'] != $genre_id);

        // Determine copyright_owner
        if ($book_details['author_name'] != $author_id) {
            $auth_details = $db->table('author_tbl')->where('author_id', $author_id)->get()->getRowArray();
            $copyright_owner = $auth_details['copyright_owner'] ?? $book_details['copyright_owner'];
        } else {
            $copyright_owner = $book_details['copyright_owner'];
        }

        // Cover & EPUB path if link changed
        if ($link_change) {
            $lang_map = [1 => 'tam', 2 => 'kan', 3 => 'tel', 4 => 'mal', 5 => 'eng'];
            $language_url_name = $lang_map[$language_id] ?? 'eng';
            $genre_details = $db->table('genre_details_tbl')->where('genre_id', $genre_id)->get()->getRowArray();
            $genre_url_name = $genre_details['url_name'] ?? 'general';

            $cover_file_path = "{$language_url_name}/cover/{$genre_url_name}/{$url_name}.jpg";
            $epub_file_path  = "{$language_url_name}/epub/{$genre_url_name}/{$url_name}.epub";
        }

        // Prepare update array
        $updateData = [
            'book_title' => $book_title,
            'regional_book_title' => $regional_book_title,
            'url_name' => $url_name,
            'author_name' => $author_id,
            'language' => $language_id,
            'genre_id' => $genre_id,
            'description' => $description,
            'ebook_remarks' => $ebook_remarks,
            'book_id_mapping' => $book_id_mapping,
            'book_category' => $category_name,
            'number_of_page' => $num_pages,
            'cost' => $cost_inr,
            'book_cost_international' => $cost_usd,
            'copyright_owner' => $copyright_owner,
        ];

        if ($link_change) {
            $updateData['cover_image'] = $cover_file_path;
            $updateData['epub_url'] = $epub_file_path;
        }

        $db->table('book_tbl')->where('book_id', $book_id)->update($updateData);

        return ($db->affectedRows() > 0) ? 1 : 0;
    }
    // Edit Book URL Details
    public function editBookUrlDetails(array $postData)
    {
        $book_id = $postData['book_id'];
        $cover_image = $postData['cover_image'];
        $epub_url = $postData['epub_url'];

        $sql = "UPDATE book_tbl SET cover_image = ?, epub_url = ? WHERE book_id = ?";
        $this->db->query($sql, [$cover_image, $epub_url, $book_id]);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    // Edit ISBN Details
    public function editBookIsbnDetails(array $postData)
    {
        $book_id = $postData['book_id'];
        $isbn_number = $postData['isbn_number'];
        $royalty = $postData['royalty'];
        $agreement_flag = $postData['agreement_flag'];

        $sql = "UPDATE book_tbl SET isbn_number = ?, royalty = ?, agreement_flag = ? WHERE book_id = ?";
        $this->db->query($sql, [$isbn_number, $royalty, $agreement_flag, $book_id]);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    // Edit Paperback Details
    public function editBookPaperbackDetails(array $postData)
    {
        $book_id = $postData['book_id'];
        $sql = "UPDATE book_tbl SET 
                paper_back_flag = ?,
                paper_back_agreement_flag = ?, 
                paper_back_royalty = ?, 
                paper_back_copyright_owner = ?, 
                paper_back_isbn = ?, 
                paper_back_inr = ?, 
                paper_back_pages = ?, 
                paper_back_weight = ?, 
                paper_back_remarks = ?, 
                paper_back_readiness_flag = ?, 
                paper_back_desc = ?, 
                paper_back_author_desc = ? 
                WHERE book_id = ?";

        $params = [
            $postData['paper_back_flag'],
            $postData['paper_back_agreement_flag'], 
            $postData['paper_back_royalty'], 
            $postData['paper_back_copyright_owner'],
            $postData['paper_back_isbn'],
            $postData['paper_back_inr'],
            $postData['paper_back_pages'],
            $postData['paper_back_weight'],
            $postData['paper_back_remarks'],
            $postData['paper_back_readiness_flag'],
            $postData['paper_back_desc'],
            $postData['paper_back_author_desc'],
            $book_id
        ];

        $this->db->query($sql, $params);
        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    // Edit Audiobook Details
    public function editBookAudiobookDetails(array $postData)
    {
        $book_id = $postData['book_id'];
        $sql = "UPDATE book_tbl SET 
                agreement_flag = ?, 
                narrator_id = ?, 
                copyright_owner = ?, 
                royalty = ?, 
                number_of_page = ?, 
                cost = ?, 
                book_cost_international = ?, 
                rental_cost_inr = ?, 
                description = ?
                WHERE book_id = ?";

        $params = [
            $postData['audiobook_agreement_flag'],
            $postData['narrator_id'],
            $postData['audiobook_copyright_owner'],
            $postData['audiobook_royalty'],
            $postData['audiobook_duration'],
            $postData['audiobook_inr'],
            $postData['audiobook_usd'],
            $postData['rental_cost_inr'],
            $postData['description'],
            $book_id
        ];

        $this->db->query($sql, $params);
        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    // Edit In-Progress Book
    public function editInProgressBook(array $postData)
    {
        $book_id = $postData['book_id'];
        $book = $this->db->query("SELECT * FROM book_tbl WHERE book_id = ?", [$book_id])->getRowArray();
        if (!$book) return 0;

        $languageMap = [1=>'tam',2=>'kan',3=>'tel',4=>'mal',5=>'eng'];
        $language = $languageMap[$postData['lang_id']] ?? 'eng';

        $genre = $this->db->query("SELECT * FROM genre_details_tbl WHERE genre_id = ?", [$postData['genre_id']])->getRowArray();
        $genre_url_name = $genre['url_name'] ?? 'general';
        $url_title = $postData['url_title'];

        $cover_file_path = $language.'/cover/'.$genre_url_name.'/'.$url_title.'.jpg';
        $epub_file_path = $language.'/epub/'.$genre_url_name.'/'.$url_title.'.epub';
        $book_file_path = $language.'/book/'.$genre_url_name.'/'.$url_title.'/';

        $sql = "UPDATE book_tbl SET 
                author_name = ?, book_title = ?, regional_book_title = ?, language = ?, 
                description = ?, book_category = ?, genre_id = ?, cover_image = ?, 
                epub_url = ?, download_link = ?, type_of_book = ?, url_name = ?, 
                number_of_page = ?, cost = ?, book_cost_international = ? 
                WHERE book_id = ?";

        $params = [
            $postData['author_id'],
            $postData['title'],
            $postData['regional_title'],
            $postData['lang_id'],
            $postData['desc_text'],
            $postData['book_category'],
            $postData['genre_id'],
            $cover_file_path,
            $epub_file_path,
            $book_file_path,
            $postData['type_of_book'],
            $url_title,
            $postData['final_page_no'],
            $postData['cost_inr'],
            $postData['cost_usd'],
            $book_id
        ];

        $this->db->query($sql, $params);
        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }
    
    public function audioBookDetailsForEdit($book_id)
    {
        $db = \Config\Database::connect();

        // Book Details
        $book = $db->table('book_tbl')
                   ->where('book_id', $book_id)
                   ->get()->getRowArray();

        $result['book_details'] = $book;

        // Author Details
        $author = $db->table('author_tbl')
                     ->where('author_id', $book['author_name'])
                     ->get()->getRowArray();

        $author['status'] = $author['status'] == 0 ? 'Inactive' : 'Active';
        $result['author_details'] = $author;

        // User Details
        $user = $db->table('users_tbl')
                   ->where('user_id', $book['copyright_owner'])
                   ->get()->getRowArray();

        $result['user_details'] = $user;

        // Publisher Details
        $publisher = $db->table('publisher_tbl')
                        ->where('copyright_owner', $book['copyright_owner'])
                        ->get()->getRowArray();

        $result['publisher_details'] = $publisher;

        // Copyright Mapping
        $mapping = $db->table('copyright_mapping')
                      ->where('copyright_owner', $book['copyright_owner'])
                      ->get()->getResultArray();

        $result['copyright_mapping_details'] = $mapping;

        // If audiobook
        if ($book['type_of_book'] == 3)
        {
            $narrator = $db->table('narrator_tbl')
                           ->where('narrator_id', $book['narrator_id'])
                           ->get()->getRowArray();

            $result['narrator_details'] = $narrator;

            $audio_chapters = $db->table('audio_book_details')
                                 ->where('book_id', $book_id)
                                 ->get()->getResultArray();

            $result['audio_chapters'] = $audio_chapters;
        }

        return $result;
    }
    // UPDATE AUDIOBOOK DETAILS
    
   public function editAudioBookDetails($request)
{
    $db = \Config\Database::connect();

    $data = [
        'narrator_id'              => $request->getPost('narrator_id'),
        'agreement_flag'           => $request->getPost('audiobook_agreement_flag'),
        'royalty'                  => $request->getPost('audiobook_royalty'),
        'copyright_owner'          => $request->getPost('audiobook_copyright_owner'),
        'number_of_page'           => $request->getPost('audiobook_duration'),
        'cost'                     => $request->getPost('audiobook_inr'),
        'book_cost_international'  => $request->getPost('audiobook_usd'),
        'rental_cost_inr'          => $request->getPost('rental_cost_inr'),
        'description'              => $request->getPost('description'),
    ];

    $db->table('book_tbl')
       ->where('book_id', $request->getPost('book_id'))
       ->update($data);

    return $db->affectedRows() > 0 ? 1 : 0;
}



}