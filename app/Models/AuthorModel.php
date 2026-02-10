<?php 
namespace App\Models;

use CodeIgniter\Model;
use Config\Services;

class AuthorModel extends Model
{
    protected $table      = 'author_tbl';
    protected $primaryKey = 'author_id';
    protected $returnType = 'array';
    protected $db; 
    
    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function getAuthorDetails()
    {
        $query = $this->db->table($this->table)
                          ->orderBy('author_name')
                          ->get();

        return $query->getResult();
    }

    public function getRoyaltyDistribution()
    {
        $royalty_author_distribution_query = $this->db->query("SELECT COUNT(*) num_authors, DATE_FORMAT(created_at, '%m-%y') as month_on_board 
            FROM author_tbl 
            WHERE author_type = '1' 
            GROUP BY month_on_board 
            ORDER BY created_at ASC");

        $i = 0;
        $month_on_board = [];
        $num_authors = [];

        foreach ($royalty_author_distribution_query->getResultArray() as $row) {
            $month_on_board[$i] = $row['month_on_board'];
            $num_authors[$i] = $row['num_authors'];
            $i++;
        }

        $result['month_details'] = $month_on_board;
        $result['authors'] = $num_authors;

        return $result;
    }

    public function getFreeDistribution()
    {
        $free_author_distribution_query = $this->db->query("SELECT COUNT(*) num_authors, DATE_FORMAT(created_at, '%m-%y') as month_on_board 
            FROM author_tbl 
            WHERE author_type = '2' 
            GROUP BY month_on_board 
            ORDER BY created_at ASC");

        $i = 0;
        $free_month_on_board = [];
        $free_num_authors = [];

        foreach ($free_author_distribution_query->getResultArray() as $row) {
            $free_month_on_board[$i] = $row['month_on_board'];
            $free_num_authors[$i] = $row['num_authors'];
            $i++;
        }

        $result1['free_month_details'] = $free_month_on_board;
        $result1['free_authors'] = $free_num_authors;

        return $result1;
    }

    public function getMagpubDistribution()
    {
        $magpub_author_distribution_query = $this->db->query("SELECT COUNT(*) num_authors, DATE_FORMAT(created_at, '%m-%y') as month_on_board 
            FROM author_tbl 
            WHERE author_type = '3' 
            GROUP BY month_on_board 
            ORDER BY created_at ASC");

        $i = 0;
        $magpub_month_on_board = [];
        $magpub_num_authors = [];

        foreach ($magpub_author_distribution_query->getResultArray() as $row) {
            $magpub_month_on_board[$i] = $row['month_on_board'];
            $magpub_num_authors[$i] = $row['num_authors'];
            $i++;
        }

        $result2['magpub_month_details'] = $magpub_month_on_board;
        $result2['magpub_authors'] = $magpub_num_authors;

        return $result2;
    }

    public function getDashboardData()
    {
        //  Correct SQL query
        $query = $this->db->query("
            SELECT author_type, status, COUNT(*) AS author_cnt
            FROM author_tbl
            GROUP BY author_type, status
        ");

        //  Initialize all result keys
        $result = [
            'active_royalty_auth_cnt' => 0,
            'inactive_royalty_auth_cnt' => 0,
            'withdraw_royalty_auth_cnt' => 0,
            'active_free_auth_cnt' => 0,
            'inactive_free_auth_cnt' => 0,
            'withdraw_free_auth_cnt' => 0,
            'active_mag_auth_cnt' => 0,
            'inactive_mag_auth_cnt' => 0,
            'withdraw_mag_auth_cnt' => 0,
            'non_matching_condition' => 0,

            //  Added summary totals
            'sum_active_auth_cnt' => 0,
            'sum_inactive_auth_cnt' => 0,
            'sum_withdraw_auth_cnt' => 0,

            'total_authors' => 0
        ];

        foreach ($query->getResultArray() as $row) {
            $author_type = $row['author_type'];
            $author_status = $row['status'];
            $author_count = (int)$row['author_cnt'];

            //  Author type and status-based counts
            if ($author_type == "1") { // Royalty
                if ($author_status == "1") $result['active_royalty_auth_cnt'] = $author_count;
                elseif ($author_status == "0") $result['inactive_royalty_auth_cnt'] = $author_count;
                elseif ($author_status == "2") $result['withdraw_royalty_auth_cnt'] = $author_count;
            } elseif ($author_type == "2") { // Free
                if ($author_status == "1") $result['active_free_auth_cnt'] = $author_count;
                elseif ($author_status == "0") $result['inactive_free_auth_cnt'] = $author_count;
                elseif ($author_status == "2") $result['withdraw_free_auth_cnt'] = $author_count;
            } elseif ($author_type == "3") { // Magazine
                if ($author_status == "1") $result['active_mag_auth_cnt'] = $author_count;
                elseif ($author_status == "0") $result['inactive_mag_auth_cnt'] = $author_count;
                elseif ($author_status == "2") $result['withdraw_mag_auth_cnt'] = $author_count;
            } else {
                $result['non_matching_condition'] += $author_count;
            }

            //  Add to totals by status
            if ($author_status == "1")
                $result['sum_active_auth_cnt'] += $author_count;
            elseif ($author_status == "0")
                $result['sum_inactive_auth_cnt'] += $author_count;
            elseif ($author_status == "2")
                $result['sum_withdraw_auth_cnt'] += $author_count;

            $result['total_authors'] += $author_count;

        }


        return $result;
    }


    public function getRoyaltyDashboardData()
    {
        $db = \Config\Database::connect();

        // Get active authors (type 1)
        $query = $db->query("
            SELECT l.language_name, COUNT(*) AS cnt
            FROM author_language al
            JOIN language_tbl l ON al.language_id = l.language_id
            JOIN author_tbl a ON a.author_id = al.author_id
            WHERE a.author_type = 1
            AND a.status = '1'
            GROUP BY al.language_id
        ");

        // Inactive authors
        $inactive_query = $db->query("
            SELECT COUNT(*) AS cnt
            FROM author_tbl
            WHERE status = '0' AND author_type = 1
        ");

        // Cancelled authors
        $cancelled_query = $db->query("
            SELECT COUNT(*) AS cnt
            FROM author_tbl
            WHERE status = '2' AND author_type = 1
        ");

        $cnt = [];
        $lang_name = [];
        $i = 0;

        foreach ($query->getResultArray() as $row) {
            $cnt[$i] = $row['cnt'];
            $lang_name[$i] = $row['language_name'];
            $i++;
        }

        $result['lang_name'] = $lang_name;
        $result['author_cnt'] = $cnt;
        $result['inactive_cnt'] = $inactive_query->getResultArray();
        $result['cancelled_cnt'] = $cancelled_query->getResultArray();

        return $result;
    }

   public function getFreeDashboardData()
    {
        $db = \Config\Database::connect();

        // Active authors by language
        $query = $db->query("
            SELECT l.language_name, COUNT(*) AS cnt
            FROM author_language al
            JOIN language_tbl l ON al.language_id = l.language_id
            JOIN author_tbl a ON a.author_id = al.author_id
            WHERE a.author_type = 2
            AND a.status = '1'
            GROUP BY al.language_id
        ");

        // Inactive authors
        $inactive_query = $db->query("
            SELECT COUNT(*) AS cnt
            FROM author_tbl
            WHERE status = '0' AND author_type = 2
        ");

        // Cancelled authors
        $cancelled_query = $db->query("
            SELECT COUNT(*) AS cnt
            FROM author_tbl
            WHERE status = '2' AND author_type = 2
        ");

    
        $cnt = [];
        $lang_name = [];

        foreach ($query->getResultArray() as $row) {
            $cnt[] = (int) $row['cnt'];
            $lang_name[] = $row['language_name'];
        }

        $inactive_cnt_row  = $inactive_query->getRowArray();
        $cancelled_cnt_row = $cancelled_query->getRowArray();

        return [
            'lang_name'      => $lang_name,
            'author_cnt'     => $cnt,
            'inactive_cnt'   => isset($inactive_cnt_row['cnt']) ? (int) $inactive_cnt_row['cnt'] : 0,
            'cancelled_cnt'  => isset($cancelled_cnt_row['cnt']) ? (int) $cancelled_cnt_row['cnt'] : 0,
        ];
    }

    public function getMagpubDashboardData()
    {
        $db = \Config\Database::connect();

        // Main language count query
        $query = $db->query("
            SELECT l.language_name, COUNT(*) AS cnt
            FROM author_language al
            JOIN language_tbl l ON al.language_id = l.language_id
            JOIN author_tbl a ON a.author_id = al.author_id
            WHERE a.author_type = 3 and a.status='1'
            GROUP BY al.language_id
        ");

        // Inactive authors count
        $inactive_query = $db->query("
            SELECT COUNT(*) AS cnt
            FROM author_tbl
            WHERE status = '0' AND author_type = 3
        ");

        // Cancelled authors count
        $cancelled_query = $db->query("
            SELECT COUNT(*) AS cnt
            FROM author_tbl
            WHERE status = '2' AND author_type = 3
        ");

        $cnt = [];
        $lang_name = [];
        $i = 0;

        foreach ($query->getResultArray() as $row) {
            $cnt[$i] = $row['cnt'];
            $lang_name[$i] = $row['language_name'];
            $i++;
        }

        $result['lang_name'] = $lang_name;
        $result['author_cnt'] = $cnt;
        $result['inactive_cnt'] = $inactive_query->getRowArray()['cnt'] ?? 0;
        $result['cancelled_cnt'] = $cancelled_query->getRowArray()['cnt'] ?? 0;

        return $result;
    }


    public function getAuthorsMetadata()
    {
        $sql = "SELECT author_tbl.author_id, author_tbl.author_name, author_tbl.status, language_tbl.language_name
                FROM author_tbl
                LEFT JOIN author_language ON author_tbl.author_id = author_language.author_id
                LEFT JOIN language_tbl ON author_language.language_id = language_tbl.language_id";

        $author_type_where = '';
        $language_where = '';
        
        $uri = service('uri');
        $segment3 = $uri->getSegment(3);
        $segment4 = $uri->getSegment(4);

        if ($segment3 != "") {
            $author_types = [
                "royalty" => 1,
                "free" => 2,
                "magpub" => 3
            ];
            $author_type_where = " WHERE author_tbl.author_type = " . $author_types[$segment3];
        }

        if ($segment4 != "") {
            $languages = [
                "tamil" => 1,
                "kannada" => 2,
                "telugu" => 3,
                "malayalam" => 4,
                "english" => 5
            ];

            if (($segment4 != "inactive") && ($segment4 != "cancelled")) {
                $language_where = ($author_type_where ? " AND " : " WHERE ") . 
                                "language_tbl.language_id = " . $languages[$segment4] . 
                                " AND author_tbl.status = '1'";
            } elseif ($segment4 != "cancelled") {
                $language_where = ($author_type_where ? " AND " : " WHERE ") . "author_tbl.status = '0'";
            } else {
                $language_where = ($author_type_where ? " AND " : " WHERE ") . "author_tbl.status = '2'";
            }
        }


        $sql .= $author_type_where . $language_where;
        if (strpos($sql, "author_tbl.status") === false) {
            $sql .= ($author_type_where || $language_where ? " AND " : " WHERE ") . "author_tbl.status = '1'";
        }

        $query = $this->db->query($sql);
        log_message('debug', $this->db->getLastQuery());

        $result_array = $query->getResultArray();
        $result = null;

        if (!empty($result_array)) {
            $i = 0;
            foreach ($result_array as $row) {
                $result['author_name'][$i] = $row['author_name'];
                $result['author_id'][$i] = $row['author_id'];
                $result['status'][$i] = $row['status'];
                $result['lang_name'][$i] = $row['language_name'];
                $i++;
            }
        }

        return $result;
    }
    public function addAuthor($post)
    {
        $email = $post['email'] ?? '';
        $author_type = $post['author_type'] ?? 0;
        $publisher_id = $post['publisher_id'] ?? null;
        $copyright_owner = null;
        $user_id = null; 

        try {
            // AUTHOR TYPE 1
            if ($author_type == 1) {

                $user_query = $this->db->query("SELECT * FROM users_tbl WHERE email = ?", [$email]);
                
                if ($user_query->getNumRows() == 1) {
                    $user_details = $user_query->getRowArray();
                    $copyright_owner = $user_details['user_id'];
                    $user_id = $user_details['user_id'];

                    $this->db->table('users_tbl')
                            ->where('user_id', $user_id)
                            ->update(['user_type' => 2]);

                } else {
                    $password = md5("books123");
                    $user_data = [
                        "username" => $post['author_name'] ?? '',
                        "password" => $password,
                        "email" => $email,
                        "user_type" => 2
                    ];
                    $this->db->table("users_tbl")->insert($user_data);
                    $user_id = $this->db->insertID(); 
                    $copyright_owner = $user_id;
                }

            } elseif ($author_type == 2) {
                $copyright_owner = 1100;
            } elseif ($author_type == 3 && !empty($publisher_id)) {
                $pub_query = $this->db->query("SELECT copyright_owner FROM publisher_tbl WHERE publisher_id = ?", [$publisher_id]);
                if ($pub_query->getNumRows() > 0)
                    $copyright_owner = $pub_query->getRow()->copyright_owner;
            }

            if (empty($user_id)) {
                $user_id = 0;
            }

            // Duplicate check
            $url_name = $post['author_url'] ?? '';
            $builder = $this->db->table('author_tbl');
            $builder->where('url_name', $url_name);

            if ($builder->countAllResults() > 0)
                return 2;

            $author_image = '';
            if (!empty($url_name)) {
                $author_image = 'author/' . $url_name . '.jpg';
            }

            // Insert author
            $author_data = [
                "author_name" => $post['author_name'] ?? '',
                "url_name" => $url_name,
                "author_type" => $author_type,
                "author_image" => $author_image,
                "copy_right_owner_name" => $post['copyright_owner'] ?? '',
                "copyright_owner" => $copyright_owner,
                "relationship" => $post['relationship'] ?? '',
                "mobile" => $post['mob_no'] ?? '',
                "email" => $email,
                "address" => $post['address'] ?? '',
                "agreement_details" => $post['agreement_details'] ?? '',
                "agreement_ebook_count" => $post['agreement_ebook_count'] ?: 0,
                "agreement_audiobook_count" => $post['agreement_audiobook_count'] ?: 0,
                "agreement_paperback_count" => $post['agreement_paperback_count'] ?: 0,
                "fb_url" => $post['fbook_url'] ?? '',
                "twitter_url" => $post['twitter_url'] ?? '',
                "blog_url" => $post['blog_url'] ?? '',
                "description" => $post['pustaka_author_desc'] ?? '',
                "gender" => $post['author_gender'] ?? 'M',
                "user_id" => $user_id 
            ];

            $this->db->table('author_tbl')->insert($author_data);
            $author_id = $this->db->insertID();

            // Add as publisher (type 1)
            if ($author_type == 1) {
                $publisher_data = [
                    "publisher_name" => $post['author_name'] ?? '',
                    "publisher_image" => $author_image,
                    "mobile" => $post['mob_no'] ?? '',
                    "email_id" => $email,
                    "address"  => $post['address'] ?? '',
                    "copyright_owner" => $copyright_owner,
                    "bank_acc_no" => $post['acc_no'] ?? '',
                    "ifsc_code" => $post['ifsc_code'] ?? '',
                    "pan_number" => $post['pan_no'] ?? '',
                    "bank_acc_type" => $post['bank_name'] ?? '',
                    "status" => 1,
                    "created_at" => date("Y-m-d H:i:s")
                ];
                $this->db->table("publisher_tbl")->insert($publisher_data);
            }

            // Copyright mapping
            $this->db->table("copyright_mapping")->insert([
                "author_id" => $author_id,
                "copyright_owner" => $copyright_owner
            ]);

            // Regional names
            $langs = [
                ['prefix' => 'tam', 'lang_id' => 1],
                ['prefix' => 'kan', 'lang_id' => 2],
                ['prefix' => 'tel', 'lang_id' => 3],
                ['prefix' => 'mal', 'lang_id' => 4],
                ['prefix' => 'eng', 'lang_id' => 5],
            ];

            foreach ($langs as $lang) {
                $first = $post[$lang['prefix'] . '_fir_name'] ?? '';
                $last = $post[$lang['prefix'] . '_lst_name'] ?? '';
                if ($first !== '') {
                    $regional_name = trim($first . ' ' . $last);
                    $this->db->table('author_language')->insert([
                        "display_name1" => $first,
                        "display_name2" => $last,
                        "regional_author_name" => $regional_name,
                        "author_id" => $author_id,
                        "language_id" => $lang['lang_id']
                    ]);
                }
            }

            return $author_id > 0 ? 1 : 0;

        } catch (\Throwable $e) {
            log_message('error', 'AddAuthor Model Error: ' . $e->getMessage());
            return 0;
        }
    }


    public function getActivePublishers()
    {
        return $this->db->table('publisher_tbl')
            ->select('publisher_id, publisher_name')
            ->where('multiple_author_flag', 1)
            ->orderBy('publisher_name', 'ASC')
            ->get()
            ->getResultArray();
    }
    public function editAuthor($author_id)
    {
        $author_sql = "SELECT * FROM `author_tbl` WHERE author_id = " . $author_id;
        $author_query1 = $this->db->query($author_sql);
        $author_result = $author_query1->getResultArray();
        if (count($author_result) > 0) {
            $author_details = $author_result[0];
        } else {
            $author_details = [];
        }
        $result["author_details"] = $author_details;

        $author_lang_sql = "SELECT * FROM `author_language` WHERE author_id = " . $author_id;
        $author_lang_query = $this->db->query($author_lang_sql);
        $author_lang_result = $author_lang_query->getResultArray();
        if (count($author_lang_result) > 0) {
            $author_lang_details = $author_lang_result[0];
        } else {
            $author_lang_details = [];
        }
        $result["author_lang_details"] = $author_lang_details;

        $publisher_sql = "SELECT * FROM `publisher_tbl` WHERE copyright_owner = " . ($author_details['copyright_owner'] ?? 0);
        $publisher_query = $this->db->query($publisher_sql);
        if ($publisher_query->getNumRows() == 1) {
            $publisher_details = $publisher_query->getResultArray()[0];
            $result["publisher_details"] = $publisher_details;
        } else {
            $result["publisher_details"] = NULL;
        }

        $copyright_mapping_sql = "SELECT * FROM `copyright_mapping` WHERE author_id = " . $author_id;
        $copyright_mapping_query = $this->db->query($copyright_mapping_sql);
        $copyright_details = $copyright_mapping_query->getResultArray();
        $result["copyright_mapping_details"] = $copyright_details;

        $user_sql = "SELECT * FROM `users_tbl` WHERE user_id = " . ($author_details['copyright_owner'] ?? 0);
        $user_query = $this->db->query($user_sql);
        $user_result = $user_query->getResultArray();
        if (count($user_result) > 0) {
            $user_details = $user_result[0];
        } else {
            $user_details = [];
        }
        $result["user_details"] = $user_details;
        $author_lang_sql="SELECT 
                                author_language.*,
                                language_tbl.*
                            FROM 
                                author_language
                            JOIN 
                                language_tbl 
                                ON language_tbl.language_id = author_language.language_id
                            WHERE 
                                author_language.author_id= " . $author_id;
        $author_lang_query = $this->db->query($author_lang_sql);
        $author_lang_details = $author_lang_query->getResultArray();
        $result["author_language_details"] = $author_lang_details;

        return $result;
    }

    public function addCopyrightMapping($data)
    {
        $db = \Config\Database::connect();
        return $db->table('copyright_mapping')->insert($data);
    }

    public function editAuthorBasicDetails($post)
    {
        $update_data = [
            "author_name" => $post["author_name"],
            "url_name" => $post["url_name"],
            "author_image" => $post["author_image"],
            "description" => $post["description"],
            "gender" => $post["author_gender"],
            "author_type" => $post["author_type"],
            "copy_right_owner_name" => $post["copy_right_owner_name"],
            "copyright_owner" => $post["copyright_owner"],
            "address" => $post["address"],
            "user_id" => $post["user_id"]
        ];

        $builder = $this->db->table('author_tbl');
        $builder->where('author_id', $post['author_id']);
        $builder->update($update_data);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }


    public function editAuthorAgreementDetails($post)
    {
        $update_data = [
            "agreement_details" => $post["agreement_details"],
            "agreement_ebook_count" => $post["agreement_ebook_count"],
            "agreement_audiobook_count" => $post["agreement_audiobook_count"],
            "agreement_paperback_count" => $post["agreement_paperback_count"]
        ];

        $builder = $this->db->table('author_tbl');
        $builder->where('author_id', $post['author_id']);
        $builder->update($update_data);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function editAuthorBankDetails($post)
    {
        $update_data = [
            "bank_acc_no" => $post["bank_acc_no"],
            "bank_acc_name" => $post["bank_acc_name"],
            "bank_acc_type" => $post["bank_acc_type"],
            "ifsc_code" => $post["ifsc_code"],
            "pan_number" => $post["pan_number"],
            "bonus_percentage" => $post["bonus_percentage"]
        ];

        $builder = $this->db->table('publisher_tbl');
        $builder->where('copyright_owner', $post['copyright_owner']);
        $builder->update($update_data);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function editAuthorPublisherDetails($post)
    {    
        $update_data = [
            "mobile" => $post["mobile"],
            "email_id" => $post["email_id"],
            "address" => $post["address"],
            "publisher_url_name" => $post["publisher_url_name"],
            "publisher_image" => $post["publisher_image"]
        ];

        $builder = $this->db->table('publisher_tbl');
        $builder->where('copyright_owner', $post['copyright_owner']);
        $builder->update($update_data);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }
    public function updateAuthorLanguageDetails($rows)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('author_language');

        foreach ($rows as $row) {
            $builder->where('id', $row['id']);
            $builder->update([
                'language_id' => $row['language_id'],
                'display_name1' => $row['display_name1'],
                'display_name2' => $row['display_name2'],
                'regional_author_name' => $row['regional_author_name']
            ]);
        }

        return true;
    }

    public function addAuthorLanguageName($data)
    {
        $db = \Config\Database::connect(); 
        return $db->table('author_language')->insert($data);
    }

    public function editAuthorOld($author_id)
    {
        $author_sql = "SELECT * FROM `author_tbl` WHERE author_id = " . $author_id;
        $author_query1 = $this->db->query($author_sql);
        $author_details = $author_query1->getResultArray()[0];

        $author_lang_sql = "SELECT * FROM `author_language` WHERE author_id = " . $author_id;
        $author_lang_query = $this->db->query($author_lang_sql);
        $author_lang_details = $author_lang_query->getResultArray()[0];

        $publisher_sql = "SELECT * FROM `publisher_tbl` WHERE copyright_owner = " . $author_details['copyright_owner'];
        $publisher_query = $this->db->query($publisher_sql);
        $publisher_details = $publisher_query->getResultArray()[0];

        $copyright_mapping_sql = "SELECT copyright_mapping.copyright_owner, author_tbl.author_name FROM `copyright_mapping`, `author_tbl` WHERE copyright_mapping.copyright_owner = " . $author_details['copyright_owner'] . " and author_tbl.author_id = copyright_mapping.author_id";
        $copyright_mapping_query = $this->db->query($copyright_mapping_sql);
        $copyright_details = $copyright_mapping_query->getResultArray();

        $user_sql = "SELECT * FROM `users_tbl` WHERE user_id = " . $author_details['copyright_owner'];
        $user_query = $this->db->query($user_sql);
        $user_details = $user_query->getResultArray()[0];

        $result = [];
        $result['author_name'] = $author_details['author_name'];
        $result['url_name'] = $author_details['url_name'];
        $result['author_image'] = $author_details['author_image'];
        $result['gender'] = $author_details['gender'];
        $result['fbook_url'] = $author_details['fb_url'];
        $result['author_type'] = $author_details['author_type'];
        $result['twitter_url'] = $author_details['twitter_url'];
        $result['blog_url'] = $author_details['blog_url'];
        $result['description'] = $author_details['description'];
        $result['copyright_owner'] = $author_details['copy_right_owner_name'];
        $result['relationship'] = $author_details['relationship'];
        $result['mobile'] = $author_details['mobile'];
        $result['email'] = $author_details['email'];
        $result['address'] = $author_details['address'];

        $result['pan_number'] = $publisher_details['pan_number'];
        $result['bank_acc_no'] = $publisher_details['bank_acc_no'];
        $result['bank_acc_name'] = $publisher_details['bank_acc_name'];
        $result['ifsc_code'] = $publisher_details['ifsc_code'];
        $result['bank_account_type'] = $publisher_details['bank_acc_type'];

        $result['copyright_details'] = $copyright_details;

        $result['username'] = $user_details['username'];
        if ($user_details['password'] == '4732210395731ca375874a1e7c8f62f6')
            $result['password'] = "Deafult Password";
        else
            $result['password'] = "No Deafult Password";

        if ($user_details['user_type'] == 1)
            $result['user_type'] = "Normal User";
        elseif ($user_details['user_type'] == 2)
            $result['user_type'] = "Author";
        else
            $result['user_type'] = $user_details['user_type'];

        if (isset($author_lang_details)) {
            if ($author_lang_details['langauge_id'] == 1) {
                $result['tam_fir_name'] = $author_lang_details['display_name1'];
                $result['tam_lst_name'] = $author_lang_details['display_name2'];
            } elseif ($author_lang_details['langauge_id'] == 2) {
                $result['kan_fir_name'] = $author_lang_details['display_name1'];
                $result['kan_lst_name'] = $author_lang_details['display_name2'];
            } elseif ($author_lang_details['langauge_id'] == 3) {
                $result['tel_fir_name'] = $author_lang_details['display_name1'];
                $result['tel_lst_name'] = $author_lang_details['display_name2'];
            } elseif ($author_lang_details['langauge_id'] == 4) {
                $result['mal_fir_name'] = $author_lang_details['display_name1'];
                $result['mal_lst_name'] = $author_lang_details['display_name2'];
            } elseif ($author_lang_details['langauge_id'] == 5) {
                $result['eng_fir_name'] = $author_lang_details['display_name1'];
                $result['eng_lst_name'] = $author_lang_details['display_name2'];
            }
        }

        return $result;
    }

    public function getEditAuthorLinkData($author_id)
    {
        $sql = "SELECT * FROM author_tbl WHERE author_id = $author_id";
        $query = $this->db->query($sql);
        $link_data = $query->getResultArray();
        $result['link_data'] = $link_data[0];
        return $result;
    }
    public function editAuthorLinks($post)
    {
       
        $data = [
            "author_id" => $post['author_id'],
            "amazon_link" => $post['amazon_link'],
            "scribd_link" => $post['scribd_link'],
            "googlebooks_link" => $post['google_link'],
            "storytel_link" => $post['storytel_link'],
            "overdrive_link" => $post['overdrive_link'],
            "pinterest_link" => $post['pinterest_link'],
            "pratilipi_link" => $post['pratilipi_link'],
            "audible_link" => $post['audible_link'],
            "odilo_link" => $post['odilo_link']
        ];

        $builder = $this->db->table('author_tbl');
        $builder->where('author_id', $post['author_id']);
        $builder->update($data);

        return 1;
    }
    public function editAuthorSocialLinks($post)
    {
       
        $data = [
            "author_id" => $post['author_id'],
            "fb_url" => $post['fb_url'],
            "twitter_url" => $post['twitter_url'],
            "blog_url" => $post['blog_url'],
        ];

        $builder = $this->db->table('author_tbl');
        $builder->where('author_id', $post['author_id']);
        $builder->update($data);

        return 1;
    }

    public function editAuthorPost()
    {
        $post = service('request')->getPost();

        $data = [
            "author_name" => $post['author_name'],
            "url_name" => $post['author_url'],
            "author_type" => $post['author_type'],
            "author_image" => $post['author_image'],
            "copy_right_owner_name" => $post['copyright_owner'],
            "relationship" => $post['relationship'],
            "mobile" => $post['mob_no'],
            "email" => $post['email'],
            "address" => $post['address'],
            "fb_url" => $post['fbook_url'],
            "twitter_url" => $post['twitter_url'],
            "blog_url" => $post['blog_url'],
            "description" => $post['pustaka_author_desc'],
            "gender" => $post['gender']
        ];

        $builder = $this->db->table('author_tbl');
        $builder->where('author_id', $post['author_id']);
        $builder->update($data);

        $author_sql = "SELECT copyright_owner FROM `author_tbl` WHERE author_id = " . $post['author_id'];
        $author_query1 = $this->db->query($author_sql);
        $author_details = $author_query1->getResultArray()[0];

        $data1 = [
            "bank_acc_no" => $post['acc_no'],
            "ifsc_code" => $post['ifsc_code'],
            "pan_number" => $post['pan_no'],
            "bank_acc_name" => $post['acc_name'],
            "bank_acc_type" => $post['bank_name']
        ];

        $builder = $this->db->table('publisher_tbl');
        $builder->where('copyright_owner', $author_details['copyright_owner']);
        $builder->update($data1);

        // Update author_language
        $lang_fields = [
            'tam' => 1, 'kan' => 2, 'tel' => 3, 'mal' => 4, 'eng' => 5
        ];

        foreach ($lang_fields as $prefix => $lang_id) {
            if (!empty($post[$prefix . '_fir_name'])) {
                $regional_name = $post[$prefix . '_fir_name'] . ' ' . $post[$prefix . '_lst_name'];
                $tmp = [
                    "display_name1" => $post[$prefix . '_fir_name'],
                    "display_name2" => $post[$prefix . '_lst_name'],
                    "regional_author_name" => $regional_name,
                    "author_id" => $post['author_id'],
                    "language_id" => $lang_id
                ];
                $builder = $this->db->table('author_language');
                $builder->where('author_id', $post['author_id']);
                $builder->update($tmp);
            }
        }

        return 1;
    }    
    public function copyrightOwnerDetails($author_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT copyright_mapping.copyright_owner, publisher_tbl.publisher_name
                FROM copyright_mapping, publisher_tbl
                WHERE copyright_mapping.copyright_owner = publisher_tbl.copyright_owner
                AND copyright_mapping.author_id = $author_id";

        $query = $db->query($sql);

        return $query->getResultArray();
    }
    public function getauthorPubDetailsDashboard($author_id)
    {
        $db = db_connect();

        $channel_pub_query = "SELECT 
                book_tbl.book_id, 
                book_tbl.book_title, 
                book_tbl.book_category, 
                book_tbl.number_of_page, 
                book_tbl.created_at, 
                book_tbl.status, 
                book_tbl.regional_book_title AS regional_book_title, 
                book_tbl.activated_at AS pub_dt, 
                language_tbl.language_name, 
                book_tbl.download_link, 
                scribd_books.doc_id AS scribd_bk_link, 
                amazon_books.ASIN AS amazon_bk_link, 
                google_books.play_store_link, 
                overdrive_books.sample_link AS overdrive_bk_link,
                storytel_books.isbn AS storytel_isbn
            FROM 
                book_tbl
                LEFT JOIN scribd_books ON book_tbl.book_id = scribd_books.book_id
                LEFT JOIN amazon_books ON book_tbl.book_id = amazon_books.book_id
                LEFT JOIN google_books ON book_tbl.book_id = google_books.book_id
                LEFT JOIN overdrive_books ON book_tbl.book_id = overdrive_books.book_id
                LEFT JOIN storytel_books ON book_tbl.book_id = storytel_books.book_id
                LEFT JOIN language_tbl ON book_tbl.language = language_tbl.language_id
            WHERE 
                book_tbl.author_name = ?
            GROUP BY 
                book_tbl.book_id
            ORDER BY 
                book_tbl.book_id";

        $channel_pub = $db->query($channel_pub_query, [$author_id]);
        return $channel_pub->getResult();
    }
    public function getAuthorDetailsDashboardData($author_id)
    {
        $sql = "SELECT 
                *,
                DATE_FORMAT(author_tbl.created_at, '%d %M, %Y') AS formatted_created_at,
                author_tbl.email AS author_email,
                author_tbl.address AS author_address,
                author_tbl.agreement_details,
                author_tbl.copy_right_owner_name,
                GROUP_CONCAT(DISTINCT publisher_tbl.publisher_name SEPARATOR ', ') AS publisher_names
                FROM 
                    author_tbl
                    LEFT JOIN users_tbl ON author_tbl.user_id = users_tbl.user_id
                    JOIN copyright_mapping ON author_tbl.author_id = copyright_mapping.author_id
                    JOIN publisher_tbl ON copyright_mapping.copyright_owner = publisher_tbl.copyright_owner
                WHERE 
                    author_tbl.author_id =$author_id
                GROUP BY 
                    author_tbl.author_id";

        $query = $this->db->query($sql);
        $tmp = $query->getResultArray();
        $result['basic_author_details'] = $tmp[0];

        if ($tmp[0]['password'] == "4732210395731ca375874a1e7c8f62f6") {
            $result['user_password'] = "default";
        } else {
            $result['user_password'] = $tmp[0]['password'];
        }

        $sql = "SELECT 
            COUNT(*) as book_cnt,
            DATE_FORMAT(book_tbl.activated_at, '%m-%y') AS months
        FROM
            author_tbl,
            book_tbl
        WHERE
            book_tbl.author_name = author_tbl.author_id
            AND author_tbl.author_id = $author_id
            AND DATE_FORMAT(book_tbl.activated_at, '%m-%y') IS NOT NULL
        GROUP BY months";

        $query = $this->db->query($sql);
        $tmp = $query->getResultArray();
        $i = 0;
        $author_graph_data['book_graph_data']['total_book_cnt'] = 0;
        foreach ($tmp as $row) {
            $author_graph_data['book_graph_data']['book_cnt'][$i] = $row['book_cnt'];
            $author_graph_data['book_graph_data']['months'][$i] = $row['months'];
            $author_graph_data['book_graph_data']['total_book_cnt'] += $row['book_cnt'];
            $i++;
        }

        $sql = "SELECT 
            SUM(book_tbl.number_of_page) AS number_of_page,
            DATE_FORMAT(book_tbl.activated_at, '%m-%y') AS months
        FROM
            author_tbl,
            book_tbl
        WHERE
            book_tbl.author_name = author_tbl.author_id
            AND author_tbl.author_id = $author_id
            AND DATE_FORMAT(book_tbl.activated_at, '%m-%y') IS NOT NULL
        GROUP BY months";

        $query = $this->db->query($sql);
        $tmp = $query->getResultArray();
        $i = 0;
        $author_graph_data['page_graph_data']['total_page_cnt'] = 0;
        foreach ($tmp as $row) {
            $author_graph_data['page_graph_data']['page_cnt'][$i] = $row['number_of_page'];
            $author_graph_data['page_graph_data']['months'][$i] = $row['months'];
            $author_graph_data['page_graph_data']['total_page_cnt'] += $row['number_of_page'];
            $i++;
        }
        $result['author_graph_data'] = $author_graph_data;

        // Channel Wise Count
        $pustaka_sql = "SELECT
                            COUNT(distinct(book_tbl.book_id)) AS cnt,
                            author_tbl.url_name
                        FROM
                            author_tbl,
                            book_tbl
                        WHERE
                            author_tbl.author_id = book_tbl.author_name
                            AND author_tbl.author_id = $author_id";
        $pustaka_query = $this->db->query($pustaka_sql);
        $pustaka_data = $pustaka_query->getResultArray();
        $channel_wise_cnt['pustaka'] = $pustaka_data[0]['cnt'];

        $amazon_sql = "SELECT
                            COUNT(distinct(amazon_books.book_id)) AS cnt,
                            author_tbl.amazon_link
                        FROM
                            amazon_books,
                            author_tbl,
                            book_tbl
                        WHERE
                            author_tbl.author_id = amazon_books.author_id
                            AND author_tbl.author_id = $author_id
                            AND amazon_books.book_id in (select book_id from book_tbl where status!=0)";
        $amazon_query = $this->db->query($amazon_sql);
        $amazon_data = $amazon_query->getResultArray();
        $channel_wise_cnt['amazon'] = $amazon_data[0]['cnt'];

        $google_sql = "SELECT
                            COUNT(distinct(google_books.book_id)) AS cnt,
                            author_tbl.googlebooks_link
                        FROM
                            google_books,
                            author_tbl
                        WHERE
                            author_tbl.author_id = google_books.author_id
                            AND author_tbl.author_id = $author_id
                            AND google_books.book_id in (select book_id from book_tbl where status!=0)";
        $google_query = $this->db->query($google_sql);
        $google_data = $google_query->getResultArray();
        $channel_wise_cnt['google'] = $google_data[0]['cnt'];

        $overdrive_sql = "SELECT
                            COUNT(distinct(overdrive_books.book_id)) AS cnt,
                            author_tbl.overdrive_link
                        FROM
                            overdrive_books,
                            author_tbl
                        WHERE
                            author_tbl.author_id = overdrive_books.author_id
                            AND author_tbl.author_id = $author_id
                            AND overdrive_books.book_id in (select book_id from book_tbl where status!=0)";
        $overdrive_query = $this->db->query($overdrive_sql);
        $overdrive_data = $overdrive_query->getResultArray();
        $channel_wise_cnt['overdrive'] = $overdrive_data[0]['cnt'];

        $scribd_sql = "SELECT
                            COUNT(distinct(scribd_books.book_id)) AS cnt,
                            author_tbl.scribd_link
                        FROM
                            scribd_books,
                            author_tbl
                        WHERE
                            author_tbl.author_id = scribd_books.author_id
                            AND author_tbl.author_id = $author_id
                            AND scribd_books.book_id in (select book_id from book_tbl where status!=0)";
        $scribd_query = $this->db->query($scribd_sql);
        $scribd_data = $scribd_query->getResultArray();
        $channel_wise_cnt['scribd'] = $scribd_data[0]['cnt'];

        $storytel_sql = "SELECT
                            COUNT(distinct(storytel_books.book_id)) AS cnt,
                            author_tbl.storytel_link
                        FROM
                            storytel_books,
                            author_tbl
                        WHERE
                            author_tbl.author_id = storytel_books.author_id
                            AND author_tbl.author_id = $author_id
                            AND storytel_books.book_id in (select book_id from book_tbl where status!=0)";
        $storytel_query = $this->db->query($storytel_sql);
        $storytel_data = $storytel_query->getResultArray();
        $channel_wise_cnt['storytel'] = $storytel_data[0]['cnt'];

        $pratilipi_sql = "SELECT
                                COUNT(distinct(pratilipi_books.book_id)) AS cnt,
                                author_tbl.pratilipi_link
                            FROM
                                pratilipi_books,
                                author_tbl
                            WHERE
                                author_tbl.author_id = pratilipi_books.author_id
                                AND author_tbl.author_id = $author_id
                                AND pratilipi_books.book_id in (select book_id from book_tbl where status!=0)";
        $pratilipi_query = $this->db->query($pratilipi_sql);
        $pratilipi_data = $pratilipi_query->getResultArray();
        $channel_wise_cnt['pratilipi'] = $pratilipi_data[0]['cnt'];

        $result['channel_wise_cnt'] = $channel_wise_cnt;

        return $result;
    }
     public function getAuthorEbookDetails($author_id)
    {
        // $author_id = $this->db->escape($author_id);

        $sql = "SELECT 
                    COUNT(*) AS ebook_count
                FROM
                    author_tbl
                JOIN
                    book_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE
                    book_tbl.type_of_book = 1
                    AND author_tbl.author_id = $author_id";

        $query = $this->db->query($sql);
        $data['total_count'] = $query->getRowArray();

        $sql1 = "SELECT 
                    COUNT(*) AS ebook_count
                FROM
                    author_tbl
                JOIN
                    book_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE
                    book_tbl.type_of_book = 1
                    AND author_tbl.author_id = $author_id 
                    AND book_tbl.status = 1";

        $query = $this->db->query($sql1);
        $data['active'] = $query->getRowArray();

        $sql2 = "SELECT 
                    COUNT(*) AS ebook_count
                FROM
                    author_tbl
                JOIN
                    book_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE
                    book_tbl.type_of_book = 1
                    AND author_tbl.author_id = $author_id 
                    AND book_tbl.status = 0";

        $query = $this->db->query($sql2);
        $data['inactive'] = $query->getRowArray();

        $sql3 = "SELECT 
                    COUNT(*) AS ebook_count
                FROM
                    author_tbl
                JOIN
                    book_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE
                    book_tbl.type_of_book = 1
                    AND author_tbl.author_id = $author_id 
                    AND book_tbl.status = 2";

        $query = $this->db->query($sql3);
        $data['suspended'] = $query->getRowArray();

        return $data;
    }

    public function getAuthorAudiobkDetails($author_id)
    {
        $author_id = $this->db->escape($author_id);

        $sql = "SELECT 
                    COUNT(*) AS audio_count
                FROM
                    author_tbl
                JOIN
                    book_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE
                    book_tbl.type_of_book = 3
                    AND author_tbl.author_id = $author_id";

        $query = $this->db->query($sql);
        $data['total_count'] = $query->getRowArray();

        $sql1 = "SELECT 
                    COUNT(*) AS audio_count
                FROM
                    author_tbl
                JOIN
                    book_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE
                    book_tbl.type_of_book = 3
                    AND author_tbl.author_id = $author_id 
                    AND book_tbl.status = 1";

        $query = $this->db->query($sql1);
        $data['active'] = $query->getRowArray();

        $sql2 = "SELECT 
                    COUNT(*) AS audio_count
                FROM
                    author_tbl
                JOIN
                    book_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE
                    book_tbl.type_of_book = 3
                    AND author_tbl.author_id = $author_id 
                    AND book_tbl.status = 0";

        $query = $this->db->query($sql2);
        $data['inactive'] = $query->getRowArray();

        $sql3 = "SELECT 
                    COUNT(*) AS audio_count
                FROM
                    author_tbl
                JOIN
                    book_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE
                    book_tbl.type_of_book = 3
                    AND author_tbl.author_id = $author_id 
                    AND book_tbl.status = 2";

        $query = $this->db->query($sql3);
        $data['suspended'] = $query->getRowArray();

        return $data;
    }

    public function getAuthorPaperbackDetails($author_id)
    {
        $author_id = $this->db->escape($author_id);

        $sql = "SELECT 
                    COUNT(*) AS paperback_count
                FROM
                    author_tbl
                JOIN
                    book_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE
                    book_tbl.paper_back_flag = 1
                    AND author_tbl.author_id = $author_id";

        $query = $this->db->query($sql);
        $data['total_counts'] = $query->getRowArray();

        $sql1 = "SELECT 
                    COUNT(*) AS paperback_count
                FROM
                    author_tbl
                JOIN
                    book_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE
                    book_tbl.paper_back_flag = 1
                    AND author_tbl.author_id = $author_id AND book_tbl.status = 1 AND book_tbl.paper_back_readiness_flag=1";

        $query = $this->db->query($sql1);
        $data['active'] = $query->getRowArray();

        $sql2 = "SELECT 
                    COUNT(*) AS paperback_count
                FROM
                    author_tbl
                JOIN
                    book_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE
                    book_tbl.paper_back_flag = 1
                    AND author_tbl.author_id = $author_id AND book_tbl.status = 0";

        $query = $this->db->query($sql2);
        $data['inactive'] = $query->getRowArray();

        $sql3 = "SELECT 
                    COUNT(*) AS paperback_count
                FROM
                    author_tbl
                JOIN
                    book_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE
                    book_tbl.paper_back_flag = 1
                    AND author_tbl.author_id = $author_id AND book_tbl.status = 2";

        $query = $this->db->query($sql3);
        $data['suspended'] = $query->getRowArray();

        return $data;
    }
    public function booksTotalCount()
    {
        $uri = service('uri');
        $author_id = $uri->getSegment(3);

        $db = \Config\Database::connect();

        $sql = "SELECT count(*) as ad_cnt FROM audible_books WHERE author_id = $author_id";
        $query = $db->query($sql);
        $data['ad_count'] = $query->getRowArray();

        $sql = "SELECT count(*) as eb_cnt FROM book_tbl WHERE author_name = $author_id";
        $query = $db->query($sql);
        $data['eb_count'] = $query->getRowArray();

        $sql = "SELECT count(*) as pb_cnt FROM book_tbl WHERE paper_back_flag = 1 AND author_name = $author_id";
        $query = $db->query($sql);
        $data['pb_count'] = $query->getRowArray();

        return $data;
    }
    public function authorWiseRoyalty($author_id)
    {
        $db = \Config\Database::connect();
        
        // total royalty revenue
        $total_sql = "SELECT 
                        SUM(revenue) AS total_revenue, 
                        SUM(royalty) AS total_royalty
                      FROM 
                        royalty_consolidation
                      WHERE 
                        author_id = $author_id 
                        AND type IN ('ebook', 'audiobook')";

        $query = $db->query($total_sql);
        $data['details'] = $query->getResultArray();

        // ebook total revenue and royalty
        $ebook_sql = "SELECT 
                        SUM(revenue) AS total_revenue, 
                        SUM(royalty) AS total_royalty
                      FROM 
                        royalty_consolidation
                      WHERE 
                        author_id = $author_id
                        AND pay_status IN ('P', 'O') and type='ebook'";

        $query = $db->query($ebook_sql);
        $data['ebook'] = $query->getResultArray();

        // audiobook total revenue and royalty
        $audiobook_sql = "SELECT 
                            SUM(revenue) AS total_revenue, 
                            SUM(royalty) AS total_royalty
                          FROM 
                            royalty_consolidation
                          WHERE 
                            author_id = $author_id
                            AND pay_status IN ('P', 'O') and type='audiobook'";

        $query = $db->query($audiobook_sql);
        $data['audiobook'] = $query->getResultArray();

        // paperback total revenue and royalty
        $paperback_sql = "SELECT 
                            SUM(revenue) AS total_revenue, 
                            SUM(royalty) AS total_royalty
                          FROM 
                            royalty_consolidation
                          WHERE 
                            author_id = $author_id
                            AND pay_status IN ('P', 'O') and type='paperback'";

        $query = $db->query($paperback_sql);
        $data['paperback'] = $query->getResultArray();

        // ebook detailed by channel
        $ebook_details_sql = "SELECT 
                                fy,
                                sum(revenue) AS full_total_revenue,
                                sum(royalty) AS full_total_royalty,
                                SUM(CASE WHEN channel = 'pustaka' THEN royalty ELSE 0 END) AS total_pustaka_royalty,
                                SUM(CASE WHEN channel = 'amazon' THEN revenue ELSE 0 END) AS total_amazon_revenue,
                                SUM(CASE WHEN channel = 'amazon' THEN royalty ELSE 0 END) AS total_amazon_royalty,
                                SUM(CASE WHEN channel = 'overdrive' THEN revenue ELSE 0 END) AS total_overdrive_revenue,
                                SUM(CASE WHEN channel = 'overdrive' THEN royalty ELSE 0 END) AS total_overdrive_royalty,
                                SUM(CASE WHEN channel = 'scribd' THEN revenue ELSE 0 END) AS total_scribd_revenue,
                                SUM(CASE WHEN channel = 'scribd' THEN royalty ELSE 0 END) AS total_scribd_royalty,
                                SUM(CASE WHEN channel = 'google' THEN revenue ELSE 0 END) AS total_google_revenue,
                                SUM(CASE WHEN channel = 'google' THEN royalty ELSE 0 END) AS total_google_royalty,
                                SUM(CASE WHEN channel = 'storytel' THEN revenue ELSE 0 END) AS total_storytel_revenue,
                                SUM(CASE WHEN channel = 'storytel' THEN royalty ELSE 0 END) AS total_storytel_royalty,
                                SUM(CASE WHEN channel = 'pratilipi' THEN revenue ELSE 0 END) AS total_pratilipi_revenue,
                                SUM(CASE WHEN channel = 'pratilipi' THEN royalty ELSE 0 END) AS total_pratilipi_royalty,
                                SUM(CASE WHEN channel = 'kobo' THEN revenue ELSE 0 END) AS total_kobo_revenue,
                                SUM(CASE WHEN channel = 'kobo' THEN royalty ELSE 0 END) AS total_kobo_royalty
                              FROM 
                                royalty_consolidation
                              WHERE 
                                author_id = $author_id AND type = 'ebook'
                              GROUP BY 
                                fy";

        $query = $db->query($ebook_details_sql);
        $data['ebook_details'] = $query->getResultArray();

        // audiobook detailed by channel
        $audiobook_details_sql = "SELECT 
                                    fy,
                                    sum(revenue) AS full_total_revenue,
                                    sum(royalty) AS full_total_royalty,
                                    SUM(CASE WHEN channel = 'pustaka' THEN royalty ELSE 0 END) AS total_pustaka_royalty,
                                    SUM(CASE WHEN channel = 'overdrive' THEN revenue ELSE 0 END) AS total_overdrive_revenue,
                                    SUM(CASE WHEN channel = 'overdrive' THEN royalty ELSE 0 END) AS total_overdrive_royalty,
                                    SUM(CASE WHEN channel = 'google' THEN revenue ELSE 0 END) AS total_google_revenue,
                                    SUM(CASE WHEN channel = 'google' THEN royalty ELSE 0 END) AS total_google_royalty,
                                    SUM(CASE WHEN channel = 'storytel' THEN revenue ELSE 0 END) AS total_storytel_revenue,
                                    SUM(CASE WHEN channel = 'storytel' THEN royalty ELSE 0 END) AS total_storytel_royalty,
                                    SUM(CASE WHEN channel = 'audible' THEN revenue ELSE 0 END) AS total_audible_revenue,
                                    SUM(CASE WHEN channel = 'audible' THEN royalty ELSE 0 END) AS total_audible_royalty,
                                    SUM(CASE WHEN channel = 'kukufm' THEN revenue ELSE 0 END) AS total_kukufm_revenue,
                                    SUM(CASE WHEN channel = 'kukufm' THEN royalty ELSE 0 END) AS total_kukufm_royalty,
                                    SUM(CASE WHEN channel = 'youtube' THEN revenue ELSE 0 END) AS total_youtube_revenue,
                                    SUM(CASE WHEN channel = 'youtube' THEN royalty ELSE 0 END) AS total_youtube_royalty
                                  FROM 
                                    royalty_consolidation
                                  WHERE 
                                    author_id = $author_id AND type ='audiobook'
                                  GROUP BY 
                                    fy";

        $query = $db->query($audiobook_details_sql);
        $data['audiobook_details'] = $query->getResultArray();

        return $data;
    }
    public function channelWiseChart($author_id)
    {
        $uri = service('uri');
        $db = \Config\Database::connect();

        $pustaka_sql = "SELECT
                            year,
                            month,
                            SUM(revenue) AS pustaka_revenue,
                            SUM(royalty) AS pustaka_royalty
                        FROM 
                            royalty_consolidation 
                        WHERE 
                            channel = 'pustaka' AND author_id = $author_id
                        GROUP BY
                            year, month
                        ORDER BY 
                            year, month";
        $query = $db->query($pustaka_sql);
        $data['pustaka'] = $query->getResultArray();

        $amazon_sql = "SELECT
                            year,
                            month,
                            SUM(revenue) AS amazon_revenue,
                            SUM(royalty) AS amazon_royalty
                        FROM 
                            royalty_consolidation 
                        WHERE 
                            channel = 'amazon' AND author_id = $author_id
                        GROUP BY
                            year, month
                        ORDER BY 
                            year, month";
        $query = $db->query($amazon_sql);
        $data['amazon'] = $query->getResultArray();

        $overdrive_sql = "SELECT
                            year,
                            month,
                            SUM(revenue) AS overdrive_revenue,
                            SUM(royalty) AS overdrive_royalty
                        FROM 
                            royalty_consolidation 
                        WHERE 
                            channel = 'overdrive' AND author_id = $author_id
                        GROUP BY
                            year, month
                        ORDER BY 
                            year, month";
        $query = $db->query($overdrive_sql);
        $data['overdrive'] = $query->getResultArray();

        $scribd_sql = "SELECT
                            year,
                            month,
                            SUM(revenue) AS scribd_revenue,
                            SUM(royalty) AS scribd_royalty
                        FROM 
                            royalty_consolidation 
                        WHERE 
                            channel = 'scribd' AND author_id = $author_id
                        GROUP BY
                            year, month
                        ORDER BY 
                            year, month";
        $query = $db->query($scribd_sql);
        $data['scribd'] = $query->getResultArray();

        $google_sql = "SELECT
                            year,
                            month,
                            SUM(revenue) AS google_revenue,
                            SUM(royalty) AS google_royalty
                        FROM 
                            royalty_consolidation 
                        WHERE 
                            channel = 'google' AND author_id = $author_id
                        GROUP BY
                            year, month
                        ORDER BY 
                            year, month";
        $query = $db->query($google_sql);
        $data['google'] = $query->getResultArray();

        $storytel_sql = "SELECT
                            year,
                            month,
                            SUM(revenue) AS storytel_revenue,
                            SUM(royalty) AS storytel_royalty
                        FROM 
                            royalty_consolidation 
                        WHERE 
                            channel = 'storytel' AND author_id = $author_id
                        GROUP BY
                            year, month
                        ORDER BY 
                            year, month";
        $query = $db->query($storytel_sql);
        $data['storytel'] = $query->getResultArray();

        $pratilipi_sql = "SELECT
                            year,
                            month,
                            SUM(revenue) AS pratilipi_revenue,
                            SUM(royalty) AS pratilipi_royalty
                        FROM 
                            royalty_consolidation 
                        WHERE 
                            channel = 'pratilipi' AND author_id = $author_id
                        GROUP BY
                            year, month
                        ORDER BY 
                            year, month";
        $query = $db->query($pratilipi_sql);
        $data['pratilipi'] = $query->getResultArray();

        $audible_sql = "SELECT
                            year,
                            month,
                            SUM(revenue) AS audible_revenue,
                            SUM(royalty) AS audible_royalty
                        FROM 
                            royalty_consolidation 
                        WHERE 
                            channel = 'audible' AND author_id = $author_id
                        GROUP BY
                            year, month
                        ORDER BY 
                            year, month";
        $query = $db->query($audible_sql);
        $data['audible'] = $query->getResultArray();

        $kobo_sql = "SELECT
                            year,
                            month,
                            SUM(revenue) AS kobo_revenue,
                            SUM(royalty) AS kobo_royalty
                        FROM 
                            royalty_consolidation 
                        WHERE 
                            channel = 'kobo' AND author_id = $author_id
                        GROUP BY
                            year, month
                        ORDER BY 
                            year, month";
        $query = $db->query($kobo_sql);
        $data['kobo'] = $query->getResultArray();

        $kukufm_sql = "SELECT
                            year,
                            month,
                            SUM(revenue) AS kukufm_revenue,
                            SUM(royalty) AS kukufm_royalty
                        FROM 
                            royalty_consolidation 
                        WHERE 
                            channel = 'kukufm' AND author_id = $author_id
                        GROUP BY
                            year, month
                        ORDER BY 
                            year, month";
        $query = $db->query($kukufm_sql);
        $data['kukufm'] = $query->getResultArray();

        $youtube_sql = "SELECT
                            year,
                            month,
                            SUM(revenue) AS youtube_revenue,
                            SUM(royalty) AS youtube_royalty
                        FROM 
                            royalty_consolidation 
                        WHERE 
                            channel = 'youtube' AND author_id = $author_id
                        GROUP BY
                            year, month
                        ORDER BY 
                            year, month";
        $query = $db->query($youtube_sql);
        $data['youtube'] = $query->getResultArray();

        return $data;
    }
    public function authorAmazonDetails($author_id)
    {

        $sql = "SELECT
                    amazon_books.title,
                    amazon_books.asin,
                    amazon_books.ku_activation_date,
                    amazon_books.ku_us_activation_date,
                    amazon_books.ku_uk_activation_date,
                    amazon_books.digital_list_price_inr,
                    amazon_books.digital_list_price_usd
                FROM 
                    amazon_books
                JOIN 
                    author_tbl ON author_tbl.author_id = amazon_books.author_id
                WHERE 
                    author_tbl.author_id = $author_id";

        $query = $this->db->query($sql);
        $data['channel'] = $query->getResultArray();

        $sql1 = "SELECT 
                    COUNT(reference_id) AS total_books,
                    SUM(CASE WHEN ku_enabled = 1 THEN 1 ELSE 0 END) AS total_in_enabled,
                    SUM(CASE WHEN ku_us_enabled = 1 THEN 1 ELSE 0 END) AS total_us_enabled,
                    SUM(CASE WHEN ku_uk_enabled = 1 THEN 1 ELSE 0 END) AS total_uk_enabled
                FROM 
                    amazon_books
                WHERE 
                    author_id = $author_id";

        $query = $this->db->query($sql1);
        $data['count'] = $query->getResultArray();

        return $data;
    }
    public function authorGoogleDetails($author_id)
    {

        $sql = "SELECT 
                    DISTINCT (title),
                    publication_date,
                    play_store_link,
                    inr_price_excluding_tax,
                    usd_price_excluding_tax,
                    eur_price_excluding_tax
                FROM 
                    google_books
                WHERE
                    author_id=$author_id";

        $query = $this->db->query($sql);
        $data['google'] = $query->getResultArray();

        $sql1 = "SELECT count(DISTINCT book_id) as total_books FROM google_books where author_id=$author_id";
        $query = $this->db->query($sql1);
        $data['count'] = $query->getResultArray();

        return $data;
    }

    public function authorPustakaDetails($author_id)
    {

        $sql = "SELECT 
                    book_tbl.book_title,
                    book_tbl.created_at,
                    book_tbl.url_name,
                    author_tbl.author_id,
                    book_tbl.isbn_number,
                    book_tbl.cost,
                    book_tbl.book_cost_international,
                    book_tbl.type_of_book,
                    book_tbl.paper_back_flag
                FROM 
                    book_tbl
                JOIN
                    author_tbl ON author_tbl.author_id= book_tbl.author_name
                WHERE 
                    author_tbl.author_id= $author_id";

        $query = $this->db->query($sql);
        $data['pustaka'] = $query->getResultArray();

        $sql0 = "SELECT count(book_id) as total_books FROM book_tbl where author_name= $author_id";
        $query = $this->db->query($sql0);
        $data['total'] = $query->getResultArray();

        $sql1 = "SELECT count(book_id) as e_books FROM book_tbl where author_name=$author_id and type_of_book =1";
        $query = $this->db->query($sql1);
        $data['ebook'] = $query->getResultArray();

        $sql2 = "SELECT count(book_id) as audio_books FROM book_tbl where author_name=$author_id and type_of_book =3";
        $query = $this->db->query($sql2);
        $data['audiobook'] = $query->getResultArray();

        $sql3 = "SELECT count(book_id) as paperbacks FROM book_tbl where author_name=$author_id  and paper_back_flag = 1";
        $query = $this->db->query($sql3);
        $data['paperback'] = $query->getResultArray();

        return $data;
    }

    public function authorStorytelDetails($author_id)
    {
        $sql = "SELECT 
                    storytel_books.title, 
                    storytel_books.publication_date,
                    author_tbl.storytel_link
                FROM 
                    storytel_books
                JOIN 
                    author_tbl ON author_tbl.author_id = storytel_books.author_id
                WHERE 
                    author_tbl.author_id= $author_id";

        $query = $this->db->query($sql);
        $data['storytel_books'] = $query->getResultArray();

        $sql1 = "SELECT count(book_id) as total_books FROM storytel_books where author_id= $author_id";
        $query = $this->db->query($sql1);
        $data['count'] = $query->getResultArray();

        return $data;
    }

    public function authorOverdriveDetails($author_id)
    {

        $sql = "SELECT 
                    title,
                    whs_usd,
                    sample_link,
                    onsale_date
                FROM 
                    overdrive_books 
                WHERE 
                    author_id=$author_id
                GROUP BY
                    title";

        $query = $this->db->query($sql);
        $data['overdrive_book'] = $query->getResultArray();

        $sql1 = "SELECT count(book_id) as total_books FROM overdrive_books where author_id=$author_id";
        $query = $this->db->query($sql1);
        $data['total_books'] = $query->getResultArray();

        $sql2 = "SELECT count(book_id) as ebook FROM overdrive_books where author_id=$author_id and type_of_book=1";
        $query = $this->db->query($sql2);
        $data['ebook'] = $query->getResultArray();

        $sql3 = "SELECT count(book_id) FROM overdrive_books where author_id=$author_id and type_of_book=3";
        $query = $this->db->query($sql3);
        $data['audiobook'] = $query->getResultArray();

        return $data;
    }

    public function authorPratilipiDetails($author_id)
    {
        $sql = "SELECT 
                    content_titles,
                    series_url,
                    number_of_parts,
                    uploaded_date
                FROM 
                    pratilipi_books
                WHERE 
                    author_id=$author_id";

        $query = $this->db->query($sql);
        $data['pratilipi_books'] = $query->getResultArray();

        $sql1 = "SELECT count(book_id) as total_books FROM pratilipi_books where author_id=$author_id";
        $query = $this->db->query($sql1);
        $data['total_books'] = $query->getResultArray();

        return $data;
    }

    public function authorScribdDetails($author_id)
    {
        $sql = "SELECT 
                    title,
                    updated_at,
                    author_id,
                    doc_id 
                FROM 
                    scribd_books
                WHERE 
                    author_id=$author_id
                GROUP BY 
                    title";

        $query = $this->db->query($sql);
        $data['scribd_books'] = $query->getResultArray();

        $sql1 = "SELECT count(book_id) as total_books FROM scribd_books where author_id=$author_id";
        $query = $this->db->query($sql1);
        $data['count'] = $query->getResultArray();

        return $data;
    }
    public function royaltySettlement($author_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT 
                        SUM(royalty_settlement.pustaka_ebooks) AS pustaka_ebooks,
                        SUM(royalty_settlement.pustaka_audiobooks) AS pustaka_audiobooks,
                        SUM(royalty_settlement.pustaka_consolidated_paperback) AS pustaka_consolidated,
                    SUM(
                        COALESCE(royalty_settlement.pustaka_online_paperback, 0) + 
                        COALESCE(royalty_settlement.pustaka_whatsapp_paperback, 0) + 
                        COALESCE(royalty_settlement.pustaka_bookfair_paperback, 0) + 
                        COALESCE(royalty_settlement.pustaka_amazon_paperback, 0) + 
                        COALESCE(royalty_settlement.pustaka_booksellers_paperback, 0) 
                    ) AS pustaka_paperback,
                    SUM(royalty_settlement.amazon) AS amazon,
                    SUM(royalty_settlement.kobo) AS kobo,
                    SUM(royalty_settlement.scribd) AS scribd,
                    SUM(royalty_settlement.google_ebooks) AS google_ebooks,
                    SUM(royalty_settlement.google_audiobooks) AS google_audiobooks,
                    SUM(royalty_settlement.overdrive_ebooks) AS overdrive_ebooks,
                    SUM(royalty_settlement.overdrive_audiobooks) AS overdrive_audiobooks,
                    SUM(royalty_settlement.storytel_ebooks) AS storytel_ebooks,
                    SUM(royalty_settlement.storytel_audiobooks) AS storytel_audiobooks,
                    SUM(royalty_settlement.pratilipi_ebooks) AS pratilipi_ebooks,
                    SUM(royalty_settlement.audible) AS audible,
                    SUM(royalty_settlement.kukufm_audiobooks) AS kukufm_audiobooks,
                    royalty_settlement.year,
                    royalty_settlement.month
                FROM 
                    royalty_settlement
                JOIN 
                    copyright_mapping 
                    ON royalty_settlement.copy_right_owner_id = copyright_mapping.copyright_owner 
                WHERE 
                    copyright_mapping.author_id = $author_id
                GROUP BY 
                    royalty_settlement.year, royalty_settlement.month
                ORDER BY
                    royalty_settlement.year DESC, 
                    royalty_settlement.month DESC";

        $query = $db->query($sql);
        $data['details'] = $query->getResultArray();

        $sql1 = "SELECT 
                    sum(settlement_amount) as total_settlement,
                    fy
                FROM 
                    royalty_settlement
                WHERE
                    author_id = $author_id
                GROUP BY
                    fy
                ORDER BY
                    fy";

        $query = $db->query($sql1);
        $data['chart'] = $query->getResultArray();

        $sql2 = "SELECT 
                        SUM(royalty_settlement.settlement_amount) AS total_settlement,
                        SUM(royalty_settlement.bonus_value) AS total_bonus
                    FROM 
                        royalty_settlement 
                    JOIN 
                        copyright_mapping 
                        ON royalty_settlement.copy_right_owner_id = copyright_mapping.copyright_owner  
                    WHERE 
                        copyright_mapping.author_id = $author_id";

        $query = $db->query($sql2);
        $data['total'] = $query->getResultArray();
        
        $sql3="SELECT 
                    bank_acc_name,
                    bank_acc_no,
                    ifsc_code,
                    bank_acc_type,
                    pan_number
                FROM publisher_tbl
                WHERE copyright_owner = ?";
        $query = $db->query($sql3, [$author_id]);
        $data['bank_details'] = $query->getResultArray();

        return $data;
    }

        public function authorRoyaltySettlementDetails($author_id)
        {
            $db = \Config\Database::connect();

            // 1. Get copyright owners for author
            $cp_sql = "
                SELECT 
                    GROUP_CONCAT(DISTINCT copyright_owner) AS copyright_owner
                FROM copyright_mapping
                WHERE author_id = ?
            ";

            $cp_query  = $db->query($cp_sql, [$author_id]);
            $cp_result = $cp_query->getRow();

            if (empty($cp_result->copyright_owner)) {
                return [];
            }

            // 2. Convert comma separated owners to array
            $copyrightOwners = explode(',', $cp_result->copyright_owner);

            // 3. Prepare IN clause placeholders
            $placeholders = implode(',', array_fill(0, count($copyrightOwners), '?'));

            // 4. Settlement query
            $sql = "
                SELECT 
                    fy,
                    settlement_date,
                    settlement_amount,
                    tds_amount,
                    payment_type,
                    bank_transaction_details,
                    month,
                    year
                FROM royalty_settlement
                WHERE copy_right_owner_id IN ($placeholders)
                ORDER BY settlement_date DESC
            ";

            $query  = $db->query($sql, $copyrightOwners);
            $result = $query->getResultArray();

            // 5. Group by financial year
            $settlement_list = [];
            foreach ($result as $row) {
                $settlement_list[$row['fy']][] = $row;
            }

            return $settlement_list;
        }


    function authorSettlementList($author_id)
    {
        $db = \Config\Database::connect();

        $sql_ebook="SELECT
                        SUM(CASE WHEN pay_status = 'P' THEN royalty ELSE 0 END) AS total_ebook_royalty_paid,
                        SUM(CASE WHEN pay_status = 'O' THEN royalty ELSE 0 END) AS total_ebook_royalty_pending
                    FROM royalty_consolidation
                    WHERE author_id = ?
                    AND copyright_owner = ?
                    AND (
                            year < YEAR(CURDATE())
                            OR (year = YEAR(CURDATE()) AND month <= MONTH(CURDATE()))
                        )
                    AND type = 'ebook'";
        $query = $db->query($sql_ebook, [$author_id, $author_id]);
        $data['ebook'] = $query->getResultArray();

        $sql_audiobook="SELECT
                            SUM(CASE WHEN pay_status = 'P' THEN royalty ELSE 0 END) AS total_audiobook_royalty_paid,
                            SUM(CASE WHEN pay_status = 'O' THEN royalty ELSE 0 END) AS total_audiobook_royalty_pending
                        FROM royalty_consolidation
                        WHERE author_id = ?
                        AND copyright_owner = ?
                        AND (
                                year < YEAR(CURDATE())
                                OR (year = YEAR(CURDATE()) AND month <= MONTH(CURDATE()))
                            )
                        AND type = 'audiobook'";
        $query = $db->query($sql_audiobook, [$author_id, $author_id]);
        $data['audiobook'] = $query->getResultArray();


        $sql_paperback="SELECT
                            SUM(CASE WHEN pay_status = 'P' THEN royalty ELSE 0 END) AS total_paperback_royalty_paid,
                            SUM(CASE WHEN pay_status = 'O' THEN royalty ELSE 0 END) AS total_paperback_royalty_pending
                        FROM royalty_consolidation
                        WHERE author_id = ?
                        AND copyright_owner = ?
                        AND (
                                year < YEAR(CURDATE())
                                OR (year = YEAR(CURDATE()) AND month <= MONTH(CURDATE()))
                            )
                        AND type = 'paperback'";
        $query = $db->query($sql_paperback, [$author_id, $author_id]);
        $data['paperback'] = $query->getResultArray();

        $sql_bonus="SELECT
                    (SELECT bonus_percentage
                    FROM publisher_tbl
                    WHERE copyright_owner = ?) AS bonus_percentage,
                    (SELECT SUM(bonus_value)
                    FROM royalty_settlement
                    WHERE copy_right_owner_id = ?) AS total_bonus";

        $query = $db->query($sql_bonus, [$author_id, $author_id]);
        $data['bonus'] = $query->getResultArray();

        return $data;
    }


    public function authorBookroyaltyDetails($author_id)
    {
        $db  = \Config\Database::connect();

        // --- Ebook Royalty ---
        $sql1 = "SELECT 
                    bookwise_royalty_consolidation.book_id,
                    book_tbl.book_title,
                    SUM(CASE WHEN bookwise_royalty_consolidation.channel = 'pustaka' THEN bookwise_royalty_consolidation.royalty ELSE 0 END) AS pustaka_royalty,
                    SUM(CASE WHEN bookwise_royalty_consolidation.channel = 'amazon' THEN bookwise_royalty_consolidation.royalty ELSE 0 END) AS amazon_royalty,
                    SUM(CASE WHEN bookwise_royalty_consolidation.channel = 'google' THEN bookwise_royalty_consolidation.royalty ELSE 0 END) AS google_royalty,
                    SUM(CASE WHEN bookwise_royalty_consolidation.channel = 'overdrive' THEN bookwise_royalty_consolidation.royalty ELSE 0 END) AS overdrive_royalty,
                    SUM(CASE WHEN bookwise_royalty_consolidation.channel = 'scribd' THEN bookwise_royalty_consolidation.royalty ELSE 0 END) AS scribd_royalty,
                    SUM(CASE WHEN bookwise_royalty_consolidation.channel = 'storytel' THEN bookwise_royalty_consolidation.royalty ELSE 0 END) AS storytel_royalty,
                    SUM(CASE WHEN bookwise_royalty_consolidation.channel = 'pratilipi' THEN bookwise_royalty_consolidation.royalty ELSE 0 END) AS pratilipi_royalty,
                    SUM(CASE WHEN bookwise_royalty_consolidation.channel = 'kobo' THEN bookwise_royalty_consolidation.royalty ELSE 0 END) AS kobo_royalty,
                    SUM(bookwise_royalty_consolidation.royalty) AS total 
                FROM 
                    bookwise_royalty_consolidation 
                JOIN
                    book_tbl ON book_tbl.book_id = bookwise_royalty_consolidation.book_id
                WHERE 
                    bookwise_royalty_consolidation.author_id = $author_id
                    AND bookwise_royalty_consolidation.type = 'ebook'
                GROUP BY 
                    bookwise_royalty_consolidation.book_id, book_tbl.book_title";

        $query1 = $db->query($sql1);
        $data['ebook'] = $query1->getResultArray();

        // --- Audiobook Royalty ---
        $sql2 = "SELECT 
                    bookwise_royalty_consolidation.book_id,
                    book_tbl.book_title,
                    SUM(CASE WHEN bookwise_royalty_consolidation.channel = 'pustaka' THEN bookwise_royalty_consolidation.royalty ELSE 0 END) AS pustaka_royalty,
                    SUM(CASE WHEN bookwise_royalty_consolidation.channel = 'google' THEN bookwise_royalty_consolidation.royalty ELSE 0 END) AS google_royalty,
                    SUM(CASE WHEN bookwise_royalty_consolidation.channel = 'overdrive' THEN bookwise_royalty_consolidation.royalty ELSE 0 END) AS overdrive_royalty,    
                    SUM(CASE WHEN bookwise_royalty_consolidation.channel = 'storytel' THEN bookwise_royalty_consolidation.royalty ELSE 0 END) AS storytel_royalty,
                    SUM(CASE WHEN bookwise_royalty_consolidation.channel = 'kukufm' THEN bookwise_royalty_consolidation.royalty ELSE 0 END) AS kukufm_royalty,
                    SUM(CASE WHEN bookwise_royalty_consolidation.channel = 'audible' THEN bookwise_royalty_consolidation.royalty ELSE 0 END) AS audible_royalty,
                    SUM(CASE WHEN bookwise_royalty_consolidation.channel = 'youtube' THEN bookwise_royalty_consolidation.royalty ELSE 0 END) AS youtube_royalty,
                    SUM(bookwise_royalty_consolidation.royalty) AS total_royalty
                FROM 
                    bookwise_royalty_consolidation 
                JOIN
                    book_tbl ON book_tbl.book_id = bookwise_royalty_consolidation.book_id
                WHERE 
                    bookwise_royalty_consolidation.author_id = $author_id 
                    AND bookwise_royalty_consolidation.type = 'audiobook'
                GROUP BY 
                    bookwise_royalty_consolidation.book_id, book_tbl.book_title";

        $query2 = $db->query($sql2);
        $data['audiobook'] = $query2->getResultArray();

        // --- Paperback Royalty ---
        $sql3 = "SELECT 
                    bookwise_royalty_consolidation.book_id,
                    book_tbl.book_title,
                    SUM(CASE WHEN bookwise_royalty_consolidation.channel = 'pustaka' THEN bookwise_royalty_consolidation.royalty ELSE 0 END) AS pustaka_royalty,
                    SUM(bookwise_royalty_consolidation.royalty) AS total
                FROM 
                    bookwise_royalty_consolidation 
                JOIN
                    book_tbl ON book_tbl.book_id = bookwise_royalty_consolidation.book_id
                WHERE 
                    bookwise_royalty_consolidation.author_id = $author_id 
                    AND bookwise_royalty_consolidation.type = 'paperback'
                GROUP BY 
                    bookwise_royalty_consolidation.book_id, book_tbl.book_title";

        $query3 = $db->query($sql3);
        $data['paperback'] = $query3->getResultArray();

        // --- Total Royalty and Revenue ---
        $sql4 = "SELECT 
                    SUM(CASE WHEN type = 'ebook' THEN royalty ELSE 0 END) AS ebook_royalty,
                    SUM(CASE WHEN type = 'audiobook' THEN royalty ELSE 0 END) AS audiobook_royalty,
                    SUM(CASE WHEN type = 'paperback' THEN royalty ELSE 0 END) AS paperback_royalty,
                    SUM(royalty) AS total_royalty
                FROM 
                    bookwise_royalty_consolidation 
                WHERE 
                    author_id = $author_id";

        $query4 = $db->query($sql4);
        $data['total'] = $query4->getResultArray();

        return $data;
    }
    public function authorPendings($author_id)
    {
        $db  = \Config\Database::connect();

       $getcopyrightowner = "
            SELECT copyright_owner 
            FROM author_tbl 
            WHERE author_id = ?
        ";

        $query  = $db->query($getcopyrightowner, [$author_id]);
        $author = $query->getRowArray();

        $copyright_owner = $author['copyright_owner'] ?? null;


        // --- Total Pending Royalty ---
        $sql = "SELECT 
                    SUM(revenue) AS total_revenue, 
                    SUM(royalty) AS total_royalty
                FROM 
                    royalty_consolidation 
                WHERE 
                    author_id = $author_id 
                    AND copyright_owner = $copyright_owner
                    AND pay_status = 'O'";

        $query = $db->query($sql);
        $data['author_pending'] = $query->getResultArray();

        // --- Channel Wise Pending Royalty ---
        $sql1 = "SELECT 
                    channels_list.channel, 
                    COALESCE(SUM(royalty_consolidation.revenue), 0) AS total_pending_revenue, 
                    COALESCE(SUM(royalty_consolidation.royalty), 0) AS total_pending_royalty
                FROM (
                    SELECT 'amazon' AS channel UNION ALL
                    SELECT 'audible' UNION ALL
                    SELECT 'google' UNION ALL
                    SELECT 'kobo' UNION ALL
                    SELECT 'kukufm' UNION ALL
                    SELECT 'overdrive' UNION ALL
                    SELECT 'pratilipi' UNION ALL
                    SELECT 'pustaka' UNION ALL
                    SELECT 'scribd' UNION ALL
                    SELECT 'storytel' UNION ALL
                    SELECT 'youtube'
                ) AS channels_list
                LEFT JOIN royalty_consolidation 
                    ON channels_list.channel = royalty_consolidation.channel 
                    AND royalty_consolidation.pay_status = 'O' 
                    AND royalty_consolidation.author_id = $author_id
                    AND copyright_owner = $copyright_owner
                GROUP BY channels_list.channel
                ORDER BY channels_list.channel";

        $query1 = $db->query($sql1);
        $data['channel_pending'] = $query1->getResultArray();

        return $data;
    }
    public function getActivateAuthorDetails($author_id)
    {
        // Author details
        $auth_details_query = $this->db->query("SELECT * FROM author_tbl WHERE author_id = ?", [$author_id]);
        $auth_details = $auth_details_query->getResultArray()[0];
        $result['author_details'] = $auth_details;

        // Author language
        $auth_language_query = $this->db->query("SELECT * FROM author_language WHERE author_id = ?", [$author_id]);
        $auth_language = $auth_language_query->getResultArray();
        $result['author_language'] = $auth_language;

        // Copyright mapping
        $copyright_mapping_query = $this->db->query("SELECT * FROM copyright_mapping WHERE author_id = ?", [$author_id]);
        $copyright_mapping_details = $copyright_mapping_query->getResultArray();
        $result['copyright_mapping'] = $copyright_mapping_details;

        // Publisher details
        $publisher_query = $this->db->query("SELECT * FROM publisher_tbl WHERE copyright_owner = ?", [$auth_details['copyright_owner']]);
        $publisher_details = $publisher_query->getResultArray()[0];
        $result['publisher_details'] = $publisher_details;

        // User details
        $user_query = $this->db->query("SELECT * FROM users_tbl WHERE user_id = ?", [$auth_details['copyright_owner']]);
        $user_details = $user_query->getResultArray()[0];
        $result['user_details'] = $user_details;

        // Book details
        $book_query = $this->db->query("SELECT * FROM book_tbl WHERE author_name = ?", [$author_id]);
        $book_details = $book_query->getResultArray();
        $result['book_details'] = $book_details;

        return $result;
    }

    public function activateAuthor($author_id, $send_mail_flag)
    {
        if ($send_mail_flag) {
            $this->sendActivateAuthorMail($author_id);
        }

        $current_date = date("Y-m-d H:i:s");
        $sql = "UPDATE author_tbl SET status = '1', activated_at = ? WHERE author_id = ?";
        $this->db->query($sql, [$current_date, $author_id]);

        return $this->db->affectedRows();
    }

    public function deactivateAuthor($author_id)
    {
        $author_id = $this->request->getPost('author_id');
        $sql = "UPDATE author_tbl SET status = '0' WHERE author_id = ?";
        $this->db->query($sql, [$author_id]);

        if ($this->db->affectedRows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function deleteAuthor($author_id)
    {
        $author_id = $this->request->getPost('author_id');
        $sql = "DELETE FROM author_tbl WHERE author_id = ?";
        $this->db->query($sql, [$author_id]);

        if ($this->db->affectedRows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    public function sendActivateAuthorMail($author_id)
    {
        $author_details = $this->db->table('author_tbl')
                                ->where('author_id', $author_id)
                                ->get()
                                ->getRowArray();

        if (!$author_details) {
            return false; 
        }
        $pustaka_url = config('App')->pustaka_url ?? '';
        $author_url = $pustaka_url . "/home/author/" . strtolower($author_details['url_name']);

        $subject = "Welcome to Pustaka!!";
        $message = "<html lang=\"en\">
        <head>
            <meta charset=\"utf-8\"/>
            <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />
            <meta name=\"x-apple-disable-message-reformatting\" />
            <!--[if !mso]><!-->
            <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />
            <!--<![endif]-->
            <title></title>
            <!--[if !mso]><!-->
            <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\" />
            <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin />
            <link href=\"https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&amp;display=swap\" rel=\"stylesheet\"/>
            <!--<![endif]-->
        </head>
        <body style=\"
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #000000;
            font-family: 'Quicksand', sans-serif;
            font-size: 16px;\"> 
        <table class=\"main-table\" style=\"
                max-width: 850px;
                min-width: 350px;
                margin: 0 auto;
                padding-left: 20px;
                padding-right: 20px;\" cellpadding=\"0\" cellspacing=\"0\">
            <tbody>
            <tr style=\"
                background: linear-gradient(
                    0deg,
                    rgba(0, 41, 107, 0.2),
                    rgba(0, 41, 107, 0.2)
                ),
                linear-gradient(135deg, #4685ec 0%, #00296b 100%);\">
                <td style=\"padding: 40px 0px; text-align: center\">
                <img src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/pustaka-logo-white-3x.png\"
                    alt=\"Logo\" title=\"Logo\" style=\"display: inline-block !important;width: 33%;max-width: 174.9px;\"/>
                </td>
            </tr>
            <tr>
                <td style=\"text-align: center\">
                <h1 style=\"text-align: center;word-wrap: break-word;font-weight: 600;font-size: 36px;margin-top: 30px;margin-bottom: 30px;\">Welcome to Pustaka</h1>
                </td>
            </tr>
            <tr>
                <td style=\"text-align: right\">
                <p style=\"font-size: 18px; line-height: 28px; margin: 0\">Date: " . date('d/M/Y') . "</p>
                </td>
            </tr>
            <tr>
                <td style=\"text-align: left; padding-top: 30px; padding-bottom: 20px\">
                <p style=\"font-size: 18px; line-height: 28px\">
                    <p>Dear " . esc($author_details['author_name']) . ",</p>
                    <p>Greetings!!!</p>
                    <p>We would like to extend our warm welcome to Pustaka and we have successfully on-boarded you as an author in our platform.</p>
                    <p>The link to the author page is <a href=\"" . esc($author_url) . "\">here</a>.</p>
                    <p>Kindly, let us know if you want us to change any information like the description, photo, etc.</p>
                    <p>You will get an access to the author dashboard to see all the transactions of your book. Here are the instructions on how to login to the dashboard:</p>
                    <ol>
                        <li>Go to <a target='_blank' href='https://dashboard.pustaka.co.in/'>https://dashboard.pustaka.co.in/</a></li>
                        <li>Provide your email id: " . esc($author_details['email']) . "</li>
                        <li>Default password: books123 (Note: You can change by using Forgot Password Option)</li>
                        <li>Click \"Login\"</li>
                    </ol>
                    Here are some of the features of Dashboard:
                    <ol>
                        <li>The main dashboard gives an overview about books, amount paid, amount pending, etc.</li>
                        <li>By clicking 'Royalty' in left side menu, you can view all the transactions financial year wise.</li>
                        <li>By clicking 'View' under the year you can view month-wise and by clicking month, you can view channel-wise details.</li>
                        <li>You can also 'Gift a book' through the dashboard.</li>
                    </ol>
                    <p>Kindly send an email to admin@pustaka.co.in for any questions or call/whatsapp us in the mobile 9980387852 if you have any issues.</p>
                    <p>Regards<br>Pustaka Team</p>
                </td>
            </tr>
            <tr style=\"display: table; margin: 0 auto\">
                <td style=\"text-align: center; padding-top: 20px; font-size: 20px\">
                Notice Something Wrong?
                <a href=\"https://www.pustaka.co.in/contact-us\" style=\"font-size: 20px;line-height: normal;font-weight: 500;color: #00296b;text-decoration: none;\">Contact us</a>
                </td>
            </tr>
            <tr style=\"display: table; margin: 0 auto; margin-bottom: 30px\">
                <td style=\"text-align: center; padding-top: 20px; font-size: 20px\">
                Want to Learn how it works?
                <a href=\"https://www.pustaka.co.in/how-it-works\" style=\"font-size: 20px;line-height: normal;font-weight: 500;color: #00296b;text-decoration: none;\">Click here</a>
                </td>
            </tr>
            <tr style=\"display: table; margin: 0 auto; margin-bottom: 50px\">
                <td style=\"text-align: center; padding-top: 20px\">
                <img src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/app-store-badge.png\" alt=\"App Store\" style=\"height: 50px; margin-right: 20px\"/>
                <img src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/play-store-badge.png\" alt=\"Play Store\" style=\"height: 50px\"/>
                </td>
            </tr>
            <tr style=\"background-color: #f9f9f9\">
                <td style=\"text-align: center\">
                <table style=\"text-align: center; padding: 20px; margin: 0 auto\">
                    <tbody>
                    <tr>
                        <td><a href=\"https://www.facebook.com/PustakaDigitalMedia\"><img src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/facebook.png\" style=\"width: 10px\"/></a></td>
                        <td style=\"padding-left: 30px; padding-right: 30px\"><a href=\"https://twitter.com/pustakabook\"><img src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/twitter.png\" style=\"width: 20px\"/></a></td>
                        <td style=\"padding-right: 30px\"><a href=\"https://www.instagram.com/pustaka_ebooks/\"><img src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/instagram.png\" style=\"width: 20px\"/></a></td>
                        <td><a href=\"https://in.pinterest.com/pustakadigital/_created/\"><img src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/pinterest.png\" style=\"width: 17px\"/></a></td>
                    </tr>
                    </tbody>
                </table>
                <table style=\"text-align: center; padding-bottom: 20px; margin: 0 auto\">
                    <tbody>
                    <tr>
                        <td style=\"padding-right: 30px\">
                        <a href=\"tel:9980387852\" style=\"font-size: 18px;color: #212121;text-decoration: none;\">
                            <img src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/call.png\" style=\"width: 20px;padding-right: 6px;vertical-align: sub;\"/>9980387852</a>
                        </td>
                        <td>
                        <a href=\"mailto:admin@pustaka.co.in\" style=\"font-size: 18px;color: #212121;text-decoration: none;\">
                            <img src=\"https://pustaka-assets.s3.ap-south-1.amazonaws.com/images/mail.png\" style=\"width: 20px;padding-right: 6px;vertical-align: sub;\"/>admin@pustaka.co.in</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                </td>
            </tr>
            </tbody>
        </table>
        </body></html>";
        $email = Services::email();
        $email->setMailType('html');
        $email->setFrom('admin@pustaka.co.in', 'Pustaka');
        $email->setTo($author_details['email']);
        $email->setCC('admin@pustaka.co.in');
        $email->setSubject($subject);
        $email->setMessage($message);

        return $email->send();
    }


}