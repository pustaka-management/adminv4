<?php

namespace App\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function getDashboardDetails()
    {
        $sql1 = "SELECT 
                        COUNT(book_id) AS total_titles, 
                        SUM(quantity) AS total_books, 
                        SUM(stock_in_hand) AS total_stock
                    FROM 
                        paperback_stock
                    WHERE 
                        book_id IN (
                            SELECT book_id 
                            FROM book_tbl 
                            WHERE paper_back_readiness_flag = 1 
                        )";
        $stockInData = $this->db->query($sql1)->getRow();

        $sql2 = "SELECT COUNT(*) AS out_of_stock, COUNT(book_id) AS out_of_stocks_titles, SUM(quantity) AS out_of_stocks_tot FROM paperback_stock WHERE stock_in_hand = 0";
        $outOfStockData = $this->db->query($sql2)->getRow();

        $sql3="SELECT COUNT(DISTINCT book_id) AS total_lost_titles,SUM(lost_qty) AS total_lost_books FROM paperback_stock WHERE lost_qty != 0";
        $lostBooks = $this->db->query($sql3)->getRow();
        
        $sql4="SELECT COUNT(book_id) AS total_title,SUM(bookfair + bookfair2 + bookfair3 + bookfair4 + bookfair5) AS total_books FROM paperback_stock WHERE (bookfair + bookfair2 + bookfair3 + bookfair4 + bookfair5) > 0";
        $outsideStocks = $this->db->query($sql4)->getRow();

        $sql5="SELECT COUNT(DISTINCT book_id) AS total_excess_titles,SUM(excess_qty) AS total_excess_books FROM paperback_stock WHERE excess_qty != 0";
        $excessBooks = $this->db->query($sql5)->getRow();


        return [
            'stock_in_hand' => $stockInData,
            'out_of_stock'  => $outOfStockData,
            'lost_books'    => $lostBooks,
            'excess_books'  => $excessBooks,
            'outside_stocks' => $outsideStocks
        ];
    }
    public function getStockDetails()
    {
         $sql="SELECT 
                author_tbl.author_id,
                author_tbl.author_name,
                paperback_stock.book_id,
                book_tbl.book_title,
                paperback_stock.quantity,
                paperback_stock.stock_in_hand,
                paperback_stock.lost_qty,
                paperback_stock.excess_qty,
                paperback_stock.validated_flag,
                paperback_stock.last_validated_date,
                paperback_stock.mismatch_flag,
                book_tbl.paper_back_readiness_flag
            FROM
                paperback_stock
            JOIN
                book_tbl ON book_tbl.book_id = paperback_stock.book_id
            JOIN
                author_tbl ON author_tbl.author_id = book_tbl.author_name
            ORDER BY 
                book_tbl.book_id asc";

        $query = $this->db->query($sql);
        $data['stock'] = $query->getResultArray();
        return $data;
    }
    public function getOutofstockdetails()
    {
        $sql="SELECT 
                author_tbl.author_id,
                author_tbl.author_name,
                paperback_stock.book_id,
                book_tbl.book_title,
                paperback_stock.stock_in_hand,
                paperback_stock.lost_qty,
                paperback_stock.excess_qty
            FROM
                paperback_stock
            JOIN
                book_tbl ON book_tbl.book_id = paperback_stock.book_id
            JOIN
                author_tbl ON author_tbl.author_id = book_tbl.author_name
            WHERE 
                paperback_stock.stock_in_hand=0";

        $query = $this->db->query($sql);
        $data['stockout'] = $query->getResultArray();
        return $data;
    }
    public function getLostStockDetails()
    {
        // LOST STOCK
        $sql_lost = "SELECT 
                        author_tbl.author_id,
                        author_tbl.author_name,
                        ps.book_id,
                        book_tbl.book_title,
                        ps.stock_in_hand,
                        ps.lost_qty,
                        ps.excess_qty
                    FROM
                        paperback_stock ps
                    JOIN
                        book_tbl ON book_tbl.book_id = ps.book_id
                    JOIN
                        author_tbl ON author_tbl.author_id = book_tbl.author_name
                    WHERE 
                        ps.lost_qty != 0";

        $queryLost = $this->db->query($sql_lost);
        $data['loststock'] = $queryLost->getResultArray();


        // EXCESS STOCK
        $sql_excess = "SELECT 
                            author_tbl.author_id,
                            author_tbl.author_name,
                            ps.book_id,
                            book_tbl.book_title,
                            ps.stock_in_hand,
                            ps.lost_qty,
                            ps.excess_qty
                        FROM
                            paperback_stock ps
                        JOIN
                            book_tbl ON book_tbl.book_id = ps.book_id
                        JOIN
                            author_tbl ON author_tbl.author_id = book_tbl.author_name
                        WHERE 
                            ps.excess_qty != 0";

        $queryExcess = $this->db->query($sql_excess);
        $data['excessstock'] = $queryExcess->getResultArray();

        return $data;
    }
    public function getOutsideStockDetails()
    {
        $sql = "SELECT 
                    author_tbl.author_id,
                    author_tbl.author_name,
                    paperback_stock.book_id,
                    book_tbl.book_title,
                    paperback_stock.stock_in_hand,
                    paperback_stock.lost_qty
                FROM 
                    paperback_stock
                JOIN 
                    book_tbl ON book_tbl.book_id = paperback_stock.book_id
                JOIN 
                    author_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE 
                    (paperback_stock.bookfair + paperback_stock.bookfair2 + paperback_stock.bookfair3 + paperback_stock.bookfair4 + paperback_stock.bookfair5) > 0";

        $query = $this->db->query($sql);
        $data['outside_stock'] = $query->getResultArray();
        return $data;
    }
    // protected $table = 'paperback_stock';     
    // protected $primaryKey = 'book_id';
    // protected $allowedFields = [
    //     'quantity', 'bookfair', 'bookfair2', 'bookfair3',
    //     'bookfair4', 'bookfair5', 'lost_qty', 'stock_in_hand'
    // ];
    public function getPaperbackBooks()
{
    $sql = "SELECT 
                book_tbl.book_id, 
                book_tbl.book_title, 
                book_tbl.regional_book_title,
                book_tbl.copyright_owner,
                book_tbl.author_name,
                book_tbl.paper_back_pages AS number_of_page, 
                book_tbl.paper_back_inr, 
                author_tbl.author_name AS author,
                COALESCE(paperback_stock.stock_in_hand, 0) AS stock_in_hand
            FROM 
                book_tbl
            JOIN 
                author_tbl ON author_tbl.author_id = book_tbl.author_name
            LEFT JOIN
                paperback_stock ON paperback_stock.book_id = book_tbl.book_id
            WHERE 
                book_tbl.paper_back_readiness_flag = 1";

    $query = $this->db->query($sql);
    $data['details'] = $query->getResultArray();
    return $data;
}
    public function getPaperbackSelectedBooksList($selectedBookList)
    {
        if (empty($selectedBookList)) {
            return [];
        }

        $bookIds = explode(',', $selectedBookList);
        $bookIds = array_map('trim', $bookIds);

        $builder = $this->db->table('book_tbl');
        $builder->select('
            book_tbl.book_id,
            book_tbl.book_title,
            book_tbl.copyright_owner,
            book_tbl.regional_book_title,
            book_tbl.paper_back_pages AS number_of_page,
            book_tbl.paper_back_inr,
            author_tbl.author_name,
            pustaka_paperback_books.id,
            (SELECT SUM(quantity) 
            FROM pustaka_paperback_books 
            WHERE book_id = book_tbl.book_id AND completed_flag = 0
            ) AS Qty
        ');
        $builder->join('author_tbl', 'author_tbl.author_id = book_tbl.author_name');
        $builder->join('pustaka_paperback_books', 'pustaka_paperback_books.book_id = book_tbl.book_id', 'left');
        $builder->where('book_tbl.paper_back_flag', 1);
        $builder->whereIn('book_tbl.book_id', $bookIds);
        $builder->groupBy('book_tbl.book_id');

        $query = $builder->get();

        return $query->getResultArray();
    }

    public function submitDetails($book_id, $qty)
    {

         if ( empty($book_id) || empty($qty)) {
            log_message('error', 'Missing data in submitDetails');
            return 0;
        }

        $order_id = time();

        $select_query = "SELECT * FROM paperback_stock WHERE book_id = " . (int)$book_id;
        $result = $this->db->query($select_query);

        if ($result->getNumRows() == 1) {
            $sql = "UPDATE paperback_stock SET quantity = quantity + $qty WHERE book_id = $book_id";
            $sql1 = "UPDATE paperback_stock SET stock_in_hand = stock_in_hand + $qty WHERE book_id = $book_id";
            $sql2 = "UPDATE paperback_stock SET last_update_date = NOW() WHERE book_id = $book_id";
            $sql3 = "UPDATE paperback_stock SET updated_user_id = " . session()->get('user_id') . " WHERE book_id = $book_id";
            $sql4 = "UPDATE paperback_stock SET validated_flag =0 WHERE book_id = $book_id";
            $this->db->query($sql);
            $this->db->query($sql1);
            $this->db->query($sql2);
            $this->db->query($sql3);
            $this->db->query($sql4);
        } else {
            $insert_data = [
                'book_id' => $book_id,
                'quantity' => $qty,
                'stock_in_hand' => $qty,
                'last_update_date' => date('Y-m-d H:i:s'),
                'updated_user_id'=>session()->get('user_id'),
                'validated_flag'=>0,
            ];
            $this->db->table('paperback_stock')->insert($insert_data);
        }

        // Get data for author_transaction
        $transaction_sql = "SELECT  * FROM  book_tbl WHERE book_id = $book_id";
        $tmp1 = $this->db->query($transaction_sql);
        $transaction = $tmp1->getRowArray();

        $royalty_value_inr = $transaction['paper_back_inr'] * $qty * 0.2;
        $comments = "Paperback royalty @ 20%, Per book cost: {$transaction['paper_back_inr']}; Qty: {$qty}";

        $transaction_data = [
            'book_id' => $transaction['book_id'],
            'order_id' => $order_id,
            'order_date' => date('Y-m-d H:i:s'),
            'author_id' => $transaction['author_name'],
            'order_type' => 15,
            'copyright_owner' => $transaction['paper_back_copyright_owner'],
            'currency' => 'INR',
            'book_final_royalty_value_inr' => $royalty_value_inr,
            'pay_status' => 'O',
            'comments' => $comments
        ];
        $this->db->table('author_transaction')->insert($transaction_data);

        // Insert into stock ledger
        $stock_sql = "SELECT  book_tbl.*, paperback_stock.quantity as current_stock
                    FROM  book_tbl 
                    JOIN paperback_stock ON paperback_stock.book_id = book_tbl.book_id
                    WHERE book_tbl.book_id = $book_id";
        $temp = $this->db->query($stock_sql);
        $stock = $temp->getRowArray();

        $stock_data = [
            'book_id' => $stock['book_id'],
            'order_id' => $order_id,
            'author_id' => $stock['author_name'],
            'copyright_owner' => $stock['paper_back_copyright_owner'],
            'description' => "Stock added to Inventory",
            'channel_type' => "STK",
            'stock_in' => $qty,
            'transaction_date' => date('Y-m-d H:i:s')
        ];
        $this->db->table('pustaka_paperback_stock_ledger')->insert($stock_data);

        if ($this->db->affectedRows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    public function getBookDetails($book_id)
    {
        $data = $this->db->table('paperback_stock')
            ->select('book_tbl.book_title, paperback_stock.book_id, paperback_stock.last_update_date, paperback_stock.quantity, paperback_stock.stock_in_hand')
            ->join('book_tbl', 'paperback_stock.book_id = book_tbl.book_id')
            ->where('paperback_stock.book_id', $book_id)
            ->get()
            ->getRowArray();

        return $data;
    }

    public function getAuthorTransaction($book_id)
    {
        return $this->db->table('author_transaction')
            ->select('
                author_transaction.id,
                author_transaction.book_id,
                book_tbl.book_title,
                author_transaction.order_date, 
                author_transaction.comments,
                paperback_stock.quantity,
                paperback_stock.stock_in_hand
            ')
            ->join('book_tbl', 'book_tbl.book_id = author_transaction.book_id')
            ->join('paperback_stock', 'paperback_stock.book_id = author_transaction.book_id')
            ->where('author_transaction.book_id', $book_id)
            ->where('DATE(author_transaction.order_date) = CURDATE()')  
            ->orderBy('author_transaction.id', 'DESC')
            ->get()
            ->getRowArray(); 
    }


    public function getStockLedger($book_id)
    {
        return $this->db->table('pustaka_paperback_stock_ledger')
            ->select('
                pustaka_paperback_stock_ledger.id,
                pustaka_paperback_stock_ledger.transaction_date,
                pustaka_paperback_stock_ledger.book_id,
                pustaka_paperback_stock_ledger.description,
                pustaka_paperback_stock_ledger.stock_in,
                pustaka_paperback_stock_ledger.current_stock AS quantity,
                pustaka_paperback_stock_ledger.channel_type,
                book_tbl.book_title
            ')
            ->join('book_tbl', 'book_tbl.book_id = pustaka_paperback_stock_ledger.book_id')
            ->where('pustaka_paperback_stock_ledger.book_id', $book_id)
            ->orderBy('pustaka_paperback_stock_ledger.id', 'DESC')
            ->get()
            ->getRowArray(); 

    }
    public function updateValidationInfo($book_id, $user_id, $validated_date)
    {
        if (empty($book_id) || empty($user_id)) {
            return false;
        }

        $builder = $this->db->table('paperback_stock');
        $builder->where('book_id', $book_id);

        return $builder->update([
            'validated_flag' => 1,
            'validated_user_id' => $user_id,
            'last_validated_date' => $validated_date
        ]);
    }

    public function getstockuserdetails($book_id)
    {
        $sql="SELECT 
                u1.username AS updated_by,
                u2.username AS validated_by,
                paperback_stock.updated_user_id,
                DATE_FORMAT(paperback_stock.last_update_date, '%d-%m-%Y %H:%i:%s') AS last_update_date,
                paperback_stock.validated_user_id,
                DATE_FORMAT(paperback_stock.last_validated_date, '%d-%m-%Y %H:%i:%s') AS last_validated_date
            FROM 
                paperback_stock
            JOIN 
                users_tbl u1 ON u1.user_id = paperback_stock.updated_user_id
            LEFT JOIN 
                users_tbl u2 ON u2.user_id = paperback_stock.validated_user_id
            WHERE 
                paperback_stock.book_id = $book_id";

        return $this->db->query($sql)->getResultArray();

    }
    public function getOtherDistributionDetails()
    {
        $sql = "SELECT 
                    book_tbl.book_id, 
                    book_tbl.book_title, 
                    book_tbl.regional_book_title,
                    book_tbl.copyright_owner,
                    book_tbl.author_name,
                    book_tbl.paper_back_pages AS number_of_page, 
                    book_tbl.paper_back_inr, 
                    author_tbl.author_name
                FROM 
                    book_tbl
                JOIN 
                    author_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE 
                    book_tbl.paper_back_flag = 1";

        $query = $this->db->query($sql);
        $data['free'] = $query->getResultArray();
        return $data;
    }

    public function getBookFairDetails()
    {
        $db = \Config\Database::connect();

        $query = $db->query("SELECT GROUP_CONCAT(CONCAT('s.', bookfair_name, ' AS `', retailer_name, '`')) AS dynamic_columns FROM bookfair_retailer_list");
        $row = $query->getRow();
        $dynamicColumns = $row->dynamic_columns ?? '';

        $selectFields = "s.id, s.book_id, s.quantity";
        if (!empty($dynamicColumns)) {
            $selectFields .= ", " . $dynamicColumns;
        }
        $selectFields .= ", s.lost_qty, s.stock_in_hand, s.last_update_date";

        $fullSQL = "SELECT $selectFields FROM paperback_stock s";

        $result = $db->query($fullSQL);

        return $result->getResultArray();  
    }

    public function insertOtherDistribution($data)
    {
        return $this->db->table('paperback_other_distribution')->insert($data);
    }

    public function getMismatchStockDetails()
    {
        $db = \Config\Database::connect();

        // mismatch stock details
        $sql = "SELECT 
                    author_tbl.author_id,
                    author_tbl.author_name,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.quantity,
                    paperback_stock.stock_in_hand
                FROM 
                    paperback_stock
                JOIN
                    book_tbl ON book_tbl.book_id = paperback_stock.book_id
                JOIN
                    author_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE
                    quantity != stock_in_hand";

        $query = $this->db->query($sql);
        $data['details'] = $query->getResultArray();
        $sql2 = "SELECT 
                    COUNT(CASE WHEN quantity != stock_in_hand THEN book_id END) AS mismatched_count,
                    SUM(CASE WHEN quantity != stock_in_hand THEN quantity END) AS total_quantity,
                    SUM(stock_in_hand) AS total_stock
                FROM paperback_stock";
        $query2 = $this->db->query($sql2);
        $data['mismatch_count'] = $query2->getRow()->mismatched_count;
        $data['total_quantity'] = $query2->getRow()->total_quantity;
        $data['total_stock'] = $query2->getRow()->total_stock;

         $mismatch_validate_sql = "SELECT 
                    author_tbl.author_id,
                    author_tbl.author_name,
                    book_tbl.book_id,
                    book_tbl.book_title,
                    paperback_stock.quantity,
                    paperback_stock.stock_in_hand
                FROM 
                    paperback_stock
                JOIN
                    book_tbl ON book_tbl.book_id = paperback_stock.book_id
                JOIN
                    author_tbl ON author_tbl.author_id = book_tbl.author_name
                WHERE
                   mismatch_flag = 1";

        $mismatch_validate_query = $this->db->query($mismatch_validate_sql);
        $data['validate'] = $mismatch_validate_query->getResultArray();
        return $data;
    }

    public function getBookfairNames($book_id)
    {
        $db = \Config\Database::connect();

        // Get valid mappings
       $retailers = $db->table('bookfair_retailer_list')
                ->select('bookfair_name, retailer_name')
                ->where('bookfair_name IS NOT NULL')
                ->where('bookfair_name !=', '')
                ->where('retailer_name IS NOT NULL')
                ->where('retailer_name !=', '')
                ->get()
                ->getResultArray();

            // Base fields
            $fields = "ps.book_id, ps.quantity, ps.lost_qty, ps.excess_qty, ps.stock_in_hand";

            // Track aliases to avoid duplicates
            $aliases = [];

            foreach ($retailers as $r) {
                $alias = $r['retailer_name'];

                // Make alias unique if needed
                $i = 1;
                while (in_array($alias, $aliases)) {
                    $alias = $r['retailer_name'] . '_' . $i;
                    $i++;
                }
                $aliases[] = $alias;

                // Add dynamic bookfair column with unique alias
                $fields .= ", ps.`{$r['bookfair_name']}` AS `{$alias}`";
            }

            // Build SQL with query binding for safety
            $sql = "SELECT {$fields} FROM paperback_stock ps WHERE ps.book_id = ?";

            // Execute query
            $query = $db->query($sql, [(int)$book_id]);

            // Return result
            return $query->getResultArray();
    }

        public function getMismatchLog($book_id)
    {
        $db = \Config\Database::connect();

        $retailers = $db->table('bookfair_retailer_list')
            ->select('bookfair_name, retailer_name')
            ->where('bookfair_name IS NOT NULL')
            ->where('bookfair_name !=', '')
            ->where('retailer_name IS NOT NULL')
            ->where('retailer_name !=', '')
            ->get()
            ->getResultArray();

        $fields = "ml.book_id, ml.quantity, ml.lost_qty, ml.excess_qty, ml.stock_in_hand, ml.comments";

         // Track aliases to avoid duplicates

        $aliases = [];
        foreach ($retailers as $r) {
            $alias = $r['retailer_name'];
            $i = 1;
            while (in_array($alias, $aliases)) {
                $alias = $r['retailer_name'] . '_' . $i;
                $i++;
            }
            $aliases[] = $alias;

            $fields .= ", ml.`{$r['bookfair_name']}` AS `{$alias}`";
        }

        $sql = "SELECT {$fields} 
                FROM mismatch_stock_log ml 
                WHERE ml.book_id = ? AND ml.approved_flag = 0";
        $query = $db->query($sql, [(int)$book_id]);

        return $query->getResultArray();
    }

    public function mismatchSubmit($book_id, $updates,$comments)
    {
        $db = \Config\Database::connect();

        $created_by = session()->get('user_id');
        $created_date = date('Y-m-d H:i:s');

        // Check if a row exists for this book_id with approved_flag = 0
        $existing = $db->table('mismatch_stock_log')
                    ->where('book_id', $book_id)
                    ->where('approved_flag', 0)
                    ->get()
                    ->getRow();

        $data = [
            'book_id' => $book_id,
            'approved_flag' => 0,
            'created_by' => $created_by,
            'created_date' => $created_date,
            'comments' => $comments
        ];

        foreach ($updates as $key => $value) {
            // Extract number if value is an array
            $val = is_array($value) ? $value[0] : $value;

            // Handle base fields directly
            if (in_array($key, ['quantity','lost_qty', 'excess_qty', 'stock_in_hand'])) {
                $data[$key] = $val;
            } else {
                // Map retailer name to column
                $map = $db->table('bookfair_retailer_list')
                        ->select('bookfair_name')
                        ->where('retailer_name', $key)
                        ->get()
                        ->getRow();

                if ($map && !empty($map->bookfair_name)) {
                    $data[$map->bookfair_name] = $val;
                }
            }
        }

        if ($existing) {
            // Update existing mismatch log row
            $db->table('mismatch_stock_log')
            ->where('book_id', $book_id)
            ->where('approved_flag', 0)
            ->set($data)
            ->update();
        } else {
            // Insert new mismatch log row
            $db->table('mismatch_stock_log')->insert($data);
        }

        //  Always update mismatch_flag in paperback_stock
        $db->table('paperback_stock')
        ->where('book_id', $book_id)
        ->set('mismatch_flag', 1)
        ->update();
    }

     public function mismatchvalidate($book_id, $updates, $comments)
    {
        $db = \Config\Database::connect();
        $builderLog = $db->table('mismatch_stock_log');
        $builderStock = $db->table('paperback_stock');

        // Step 1: Fetch the mismatch log row
        $mismatch = $builderLog
            ->where('book_id', $book_id)
            ->where('approved_flag', 0)
            ->get()
            ->getRowArray();

        if (!$mismatch) {
            return false; // no pending mismatch
        }

        // Step 2: Prepare data to update paperback_stock
        $updateData = [];

        // Basic stock fields
        $stockFields = ['quantity', 'lost_qty', 'excess_qty', 'stock_in_hand'];
        foreach ($stockFields as $field) {
            if (isset($updates[$field])) {
                $updateData[$field] = is_array($updates[$field]) ? $updates[$field][0] : $updates[$field];
            }
        }

        // Step 3: Map retailer fields dynamically
        $stockColumns = $db->getFieldNames('paperback_stock');
        foreach ($updates as $key => $val) {
            if (!in_array($key, $stockFields)) {
                $mapped = $db->table('bookfair_retailer_list')
                    ->select('bookfair_name')
                    ->where('retailer_name', $key)
                    ->get()
                    ->getRow();

                if ($mapped && in_array($mapped->bookfair_name, $stockColumns)) {
                    $updateData[$mapped->bookfair_name] = is_array($val) ? $val[0] : $val;
                }
            }
        }

        // Step 4: Update paperback_stock
        if (!empty($updateData)) {
            $builderStock
                ->where('book_id', $book_id)
                ->set($updateData)
                ->set('mismatch_flag', 0)
                ->set('validated_user_id', session()->get('user_id'))
                ->set('last_validated_date', date('Y-m-d H:i:s'))
                ->update();
        }

        // Step 5: Update mismatch_stock_log as approved
        $builderLog
            ->where('book_id', $book_id)
            ->where('approved_flag', 0)
            ->set([
                'approved_flag' => 1,
                'approved_date' => date('Y-m-d H:i:s'),
                // 'comments' => $comments,
                'approved_user_id' => session()->get('user_id')
            ])
            ->update();

        return true;
    }
    
    function mismatchstockcount()
    {
        $sql = "SELECT COUNT(*) AS count FROM paperback_stock WHERE mismatch_flag = 1";
        $query = $this->db->query($sql);
        $result = $query->getRow();
        return $result ? $result->count : 0;
    }
    public function paperbackLedgerBooks()
    {
        $db = \Config\Database::connect();
        $sql = "SELECT book_tbl.book_id, book_tbl.book_title,
                    book_tbl.regional_book_title, book_tbl.paper_back_readiness_flag,
                    author_tbl.author_name
                FROM book_tbl, author_tbl 
                WHERE book_tbl.author_name = author_tbl.author_id 
                AND book_tbl.paper_back_flag = 1";
        $query = $db->query($sql);
        return $query->getResultArray();
    }
    public function paperbackLedgerDetails($book_id)
    {
        $db = \Config\Database::connect();

        $sql = "SELECT 
                    book_tbl.book_id,
                    book_tbl.book_title,
                    book_tbl.regional_book_title,
                    book_tbl.paper_back_inr,
                    book_tbl.paper_back_copyright_owner,
                    author_tbl.author_name,
                    author_tbl.author_id,
                    paperback_stock.*
                FROM 
                    book_tbl
                JOIN 
                    author_tbl ON book_tbl.author_name = author_tbl.author_id
                LEFT JOIN 
                    paperback_stock ON book_tbl.book_id = paperback_stock.book_id
                WHERE 
                    book_tbl.book_id = $book_id";
        $query = $db->query($sql);
        $data['books'] = $query->getResultArray()[0] ?? [];

        $sql = "SELECT 
                    pustaka_paperback_stock_ledger.transaction_date,
                    pustaka_paperback_stock_ledger.*
                FROM 
                    pustaka_paperback_stock_ledger
                WHERE 
                    pustaka_paperback_stock_ledger.book_id = $book_id
                ORDER BY 
                    pustaka_paperback_stock_ledger.transaction_date ASC";
        $query = $db->query($sql);
        $data['list'] = $query->getResultArray();

        $sql = "SELECT 
                    author_transaction.order_date,
                    author_transaction.book_final_royalty_value_inr,
                    author_transaction.comments
                FROM 
                    author_transaction
                WHERE 
                    author_transaction.book_id = $book_id 
                    AND author_transaction.order_type = 15
                ORDER BY 
                    author_transaction.order_date ASC";
        $query = $db->query($sql);
        $data['author'] = $query->getResultArray();

        $sql = "SELECT 
                    author_transaction.order_date,
                    author_transaction.order_id,
                    author_transaction.book_final_royalty_value_inr,
                    author_transaction.comments,
                    author_transaction.order_type,
                    CASE 
                        WHEN author_transaction.order_type = 7 THEN 'Online'
                        WHEN author_transaction.order_type = 9 THEN 'Book Fair'
                        WHEN author_transaction.order_type = 10 THEN 'Offline'
                        WHEN author_transaction.order_type = 11 THEN 'Amazon'
                        WHEN author_transaction.order_type = 12 THEN 'Flipkart'
                        WHEN author_transaction.order_type = 14 THEN 'Book Seller'
                    END AS channel
                FROM 
                    author_transaction
                WHERE 
                    author_transaction.book_id = $book_id
                    AND author_transaction.order_type IN (7, 9, 10, 11, 12, 14)
                ORDER BY 
                    author_transaction.order_date ASC";
        $query = $db->query($sql);
        $data['old_details'] = $query->getResultArray();

        $sql = "SELECT 
                    author_transaction.order_date,
                    author_transaction.order_id,
                    author_transaction.comments,
                    author_transaction.order_type,
                    pod_bookfair.*
                FROM 
                    author_transaction
                JOIN 
                    pod_bookfair ON pod_bookfair.order_id = author_transaction.order_id
                WHERE 
                    pod_bookfair.book_id = $book_id";
        $query = $db->query($sql);
        $data['book_fair'] = $query->getResultArray();

        return $data;
    }
    public function otherdistributionbooksstatus()
    {
        $db = \Config\Database::connect();
        $data = [];

        // Not Started
        $sql = "SELECT paperback_other_distribution.*,book_tbl.book_title,author_tbl.author_name
                FROM paperback_other_distribution,book_tbl,author_tbl
                WHERE author_tbl.author_id = book_tbl.author_name
                AND paperback_other_distribution.book_id = book_tbl.book_id
                AND paperback_other_distribution.status = 0
                ORDER BY paperback_other_distribution.order_date ASC";
        $query = $db->query($sql);
        $data['book_not_start'] = $query->getResultArray();


        // Completed (Last 30 days)
        $sql = "SELECT
                    paperback_other_distribution.*,
                    book_tbl.book_title,
                    author_tbl.author_name
                FROM
                    paperback_other_distribution,
                    book_tbl,
                    author_tbl
                WHERE
                    author_tbl.author_id = book_tbl.author_name
                    AND paperback_other_distribution.book_id = book_tbl.book_id
                    AND paperback_other_distribution.status = 1
                    AND paperback_other_distribution.ship_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ORDER BY
                    paperback_other_distribution.ship_date DESC";
        $query = $db->query($sql);
        $data['completed'] = $query->getResultArray();

        // Completed All
        $sql = "SELECT
                    paperback_other_distribution.*,
                    book_tbl.book_title,
                    author_tbl.author_name
                FROM
                    paperback_other_distribution,
                    book_tbl,
                    author_tbl
                WHERE
                    author_tbl.author_id = book_tbl.author_name
                    AND paperback_other_distribution.book_id = book_tbl.book_id
                    AND paperback_other_distribution.status = 1
                ORDER BY
                    paperback_other_distribution.ship_date DESC";
        $query = $db->query($sql);
        $data['completed_all'] = $query->getResultArray();

        return $data;
    }
    //free books status update functions
    public function markStart($id, $type)
    {

        if ($type == 'Initiate_print') {
            $update_data = ["start_flag" => 1];
            $this->db->query("UPDATE pustaka_paperback_books SET start_flag = 1 WHERE id = ?", [$id]);
        } elseif ($type == 'Free_books') {
            $update_data = ["start_flag" => 1];
            $this->db->query("UPDATE paperback_other_distribution SET start_flag = 1 WHERE id = ?", [$id]);
        }

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markCoverComplete($id, $type)
    {

        if ($type == 'Initiate_print') {
            $this->db->query("UPDATE pustaka_paperback_books SET cover_flag = 1 WHERE id = ?", [$id]);
        } elseif ($type == 'Free_books') {
            $this->db->query("UPDATE paperback_other_distribution SET cover_flag = 1 WHERE id = ?", [$id]);
        }

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markContentComplete($id, $type)
    {

        if ($type == 'Initiate_print') {
            $this->db->query("UPDATE pustaka_paperback_books SET content_flag = 1 WHERE id = ?", [$id]);
        } elseif ($type == 'Free_books') {
            $this->db->query("UPDATE paperback_other_distribution SET content_flag = 1 WHERE id = ?", [$id]);
        }

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markLaminationComplete($id, $type)
    {
        if ($type == 'Initiate_print') {
            $this->db->query("UPDATE pustaka_paperback_books SET lamination_flag = 1 WHERE id = ?", [$id]);
        } elseif ($type == 'Free_books') {
            $this->db->query("UPDATE paperback_other_distribution SET lamination_flag = 1 WHERE id = ?", [$id]);
        }

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markBindingComplete($id, $type)
    {
        if ($type == 'Initiate_print') {
            $this->db->query("UPDATE pustaka_paperback_books SET binding_flag = 1 WHERE id = ?", [$id]);
        } elseif ($type == 'Free_books') {
            $this->db->query("UPDATE paperback_other_distribution SET binding_flag = 1 WHERE id = ?", [$id]);
        }

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markFinalCutComplete($id, $type)
    {

        if ($type == 'Initiate_print') {
            $this->db->query("UPDATE pustaka_paperback_books SET finalcut_flag = 1 WHERE id = ?", [$id]);
        } elseif ($type == 'Free_books') {
            $this->db->query("UPDATE paperback_other_distribution SET finalcut_flag = 1 WHERE id = ?", [$id]);
        }

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markQcComplete($id, $type)
    {
        
        if ($type == 'Initiate_print') {
            $this->db->query("UPDATE pustaka_paperback_books SET qc_flag = 1 WHERE id = ?", [$id]);
        } elseif ($type == 'Free_books') {
            $this->db->query("UPDATE paperback_other_distribution SET qc_flag = 1 WHERE id = ?", [$id]);
        }

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }

    public function markCompleted($id, $type)
    {
        $query = $this->db->query("SELECT quantity, order_id, book_id FROM pustaka_paperback_books WHERE id = ?", [$id]);
        $record = $query->getRowArray();

        if (!$record) {
            return 0;
        }

        $update_data = [
            "cover_flag" => 1,
            "content_flag" => 1,
            "lamination_flag" => 1,
            "binding_flag" => 1,
            "finalcut_flag" => 1,
            "qc_flag" => 1,
            "completed_flag" => 1,
            "completed_date" => date('Y-m-d H:i:s')
        ];

        $this->db->query("
            UPDATE pustaka_paperback_books
            SET cover_flag = 1,
                content_flag = 1,
                lamination_flag = 1,
                binding_flag = 1,
                finalcut_flag = 1,
                qc_flag = 1,
                completed_flag = 1,
                completed_date = ?
            WHERE id = ?
        ", [$update_data['completed_date'], $id]);

        return ($this->db->affectedRows() > 0) ? 1 : 0;
    }
    public function freeMarkCompleted($id, $type)
    {
        $completed_date = date('Y-m-d H:i:s');

        $this->db->query("
            UPDATE paperback_other_distribution 
            SET 
                status = 1,
                ship_date = ?
            WHERE id = ?
        ", [$completed_date, $id]);

        if ($this->db->affectedRows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function savequantity($data)
        {
            $db = \Config\Database::connect();

            $book_id       = $data['book_id'];
            $quantity      = $data['quantity'];
            $bookfair      = $data['bookfair'];
            $bookfair2     = $data['bookfair2'];
            $bookfair3     = $data['bookfair3'];
            $bookfair4     = $data['bookfair4'];
            $bookfair5     = $data['bookfair5'];
            $lost_qty      = $data['lost_qty'];
            $excess_qty    = $data['excess_qty'];
            $stock_in_hand = $data['stock_in_hand'];

            $sql = "
                UPDATE paperback_stock SET 
                    quantity      = ?,
                    bookfair      = ?,
                    bookfair2     = ?,
                    bookfair3     = ?,
                    bookfair4     = ?,
                    bookfair5     = ?,
                    lost_qty      = ?,
                    excess_qty    = ?,
                    stock_in_hand = ?
                WHERE book_id = ?
            ";

            $db->query($sql, [
                $quantity,
                $bookfair,
                $bookfair2,
                $bookfair3,
                $bookfair4,
                $bookfair5,
                $lost_qty,
                $excess_qty,
                $stock_in_hand,
                $book_id
            ]);

            return ($db->affectedRows() > 0) ? 1 : 0;
        }


    public function getlistPaperbackBooks()
    {
        $db = \Config\Database::connect();
        $sql = "SELECT book_tbl.book_id, book_tbl.book_title, book_tbl.regional_book_title,
                    book_tbl.paper_back_pages AS number_of_page, book_tbl.paper_back_inr, author_tbl.author_name
                FROM book_tbl, author_tbl
                WHERE author_tbl.author_id = book_tbl.author_name
                AND book_tbl.paper_back_flag = 1";
        
        $query = $db->query($sql);
        $data['paperback_book'] = $query->getResultArray();
        return $data;
    }
    public function getPaperbackStockDetails()
    {
        $db = \Config\Database::connect();
        $data = [];

        // Total books count
        $query = $db->query("SELECT COUNT(*) AS books_cnt FROM paperback_stock");
        $row = $query->getRowArray();
        $data['books_cnt'] = $row['books_cnt'] ?? 0;

        // Total quantity
        $query = $db->query("SELECT SUM(quantity) AS quantity_cnt FROM paperback_stock");
        $row = $query->getRowArray();
        $data['quantity_cnt'] = $row['quantity_cnt'] ?? 0;

        // Detailed stock data
        $sql = "SELECT 
                    ps.book_id AS id,
                    bt.book_title AS title,
                    ps.quantity AS qty,
                    ps.bookfair, ps.bookfair2, ps.bookfair3, ps.bookfair4, ps.bookfair5,
                    ps.lost_qty, ps.stock_in_hand, ps.excess_qty,
                    at.author_name
                FROM paperback_stock ps
                JOIN book_tbl bt ON ps.book_id = bt.book_id
                JOIN author_tbl at ON bt.author_name = at.author_id";
        $query = $db->query($sql);
        $data['stock_data'] = $query->getResultArray();

         $sql = "SELECT * FROM bookfair_retailer_list";
        $query = $db->query($sql);
        $data['bookfair_retailer'] = $query->getResultArray();


        return $data;
    }
    public function getLostExcessBookStatus()
{
    $sql = "SELECT 
                author_tbl.author_name as author_name,
                book_tbl.book_id,
                book_tbl.book_title,
                book_tbl.url_name, 
                paperback_stock.quantity as qty,
                paperback_stock.stock_in_hand,
                paperback_stock.bookfair,
                paperback_stock.bookfair2,
                paperback_stock.bookfair3,
                paperback_stock.bookfair4,
                paperback_stock.bookfair5,
                paperback_stock.lost_qty,
                paperback_stock.excess_qty
            FROM 
                book_tbl
            JOIN 
                author_tbl ON book_tbl.author_name = author_tbl.author_id
            LEFT JOIN 
                paperback_stock ON paperback_stock.book_id = book_tbl.book_id
            WHERE 
                (paperback_stock.lost_qty != 0 OR paperback_stock.excess_qty > 0)
            ORDER BY
                author_tbl.author_name ASC";

    $query = $this->db->query($sql);
    $data['in_progress'] = $query->getResultArray();

    return $data;
}
    public function printExcessLostOneItem($book_id)
    {
        $sql = "SELECT 
                    book_tbl.author_name, paperback_stock.lost_qty,
                    book_tbl.paper_back_copyright_owner, book_tbl.paper_back_inr, book_tbl.paper_back_royalty 
                FROM 
                    paperback_stock, book_tbl
                WHERE 
                    paperback_stock.book_id = $book_id AND 
                    paperback_stock.book_id = book_tbl.book_id";

        log_message('debug', 'Query:::::  ' . $sql);

        $query = $this->db->query($sql);
        $record = $query->getResultArray()[0];

        $author_id = $record['author_name'];
        $copyright_owner = $record['paper_back_copyright_owner'];
        $description = "Stock added to Inventory";
        $channel_type = "STK";
        $qty = $record['lost_qty'];
        $royalty_value_inr = 0;
        $comments = ""; 
        $order_id = time();
        $order_date = date('Y-m-d H:i:s');

        log_message('debug', 'Book ID: ' . $book_id);
        log_message('debug', 'author_id: ' . $author_id);
        log_message('debug', 'copyright_owner: ' . $copyright_owner);
        log_message('debug', 'description: ' . $description);
        log_message('debug', 'channel_type: ' . $channel_type);
        log_message('debug', 'qty: ' . $qty);
        log_message('debug', 'order_id: ' . $order_id);
        log_message('debug', 'order_date: ' . $order_date);

        if ($qty < 0) {
            // Excess quantity
            $royalty_value_inr = ($record['paper_back_inr'] * $record['paper_back_royalty']) / 100;
            $comments = "Paperback royalty @ 20%, Per book cost: {$record['paper_back_inr']}; Qty: 1";

            log_message('debug', 'royalty_value_inr: ' . $royalty_value_inr);
            log_message('debug', 'comments: ' . $comments);

            // Insert into Stock ledger
            $stock_ledger_data = [
                'book_id' => $book_id,
                "order_id" => $order_id,
                "author_id" => $author_id,
                "copyright_owner" => $copyright_owner,
                "description" => "Stock added to Inventory",
                "channel_type" => "STK",
                "stock_in" => 1,
                "stock_out" => 0,
                "current_stock" => 0,
                'transaction_date' => date('Y-m-d H:i:s'),
            ];
            $this->db->table('pustaka_paperback_stock_ledger')->insert($stock_ledger_data);
            log_message('debug', 'Stock Ledger - inserted');

            // Insert into Author Transaction
            $auth_transaction_data = [
                'book_id' => $book_id,
                "order_id" => $order_id,
                "order_date" => $order_date,
                "author_id" => $author_id,
                "order_type" => 15,
                "copyright_owner" => $copyright_owner,
                "currency" => 'INR',
                "book_final_royalty_value_inr" => $royalty_value_inr,
                "pay_status" => 'O',
                "comments" => $comments
            ];
            $this->db->table('author_transaction')->insert($auth_transaction_data);
            log_message('debug', 'Author Transaction - inserted');

            // Update stock
            $update_stock_sql = "UPDATE paperback_stock 
                                SET quantity = quantity + 1, 
                                    lost_qty = lost_qty + 1 
                                WHERE book_id = $book_id";
            $this->db->query($update_stock_sql);

            log_message('debug', 'Excess qty - reduced by 1');

            $return_data = "Excess Quantity for Book ID: " . $book_id . " is reduced by 1 !!!";

        } else {

            // lost_qty + stock update
            $update_stock_sql = "UPDATE paperback_stock 
                                SET lost_qty = lost_qty - 1, 
                                    stock_in_hand = stock_in_hand + 1 
                                WHERE book_id = $book_id";
            $this->db->query($update_stock_sql);
            log_message('debug', 'Lost qty - updated by 1');

            $return_data = "Lost Quantity for Book ID: " . $book_id . " is reduced by 1 !!!";
        }

        return $return_data;
    }
    public function printExcessLostAllItem($book_id)
    {
        $db = \Config\Database::connect();

        $sql = "SELECT 
                    book_tbl.author_name, paperback_stock.lost_qty,
                    book_tbl.paper_back_copyright_owner, 
                    book_tbl.paper_back_inr, book_tbl.paper_back_royalty 
                FROM 
                    paperback_stock, book_tbl
                WHERE 
                    paperback_stock.book_id = $book_id 
                    AND paperback_stock.book_id = book_tbl.book_id";

        log_message('debug', 'Query:::::  ' . $sql);

        $query = $db->query($sql);
        $record = $query->getResultArray()[0];

        $author_id = $record['author_name'];
        $copyright_owner = $record['paper_back_copyright_owner'];
        $description = "Stock added to Inventory";
        $channel_type = "STK";
        $qty = $record['lost_qty'];
        $order_id = time();
        $royalty_value_inr = 0;
        $comments = ""; 
        $order_date = date('Y-m-d H:i:s');

        log_message('debug', 'Book ID: ' . $book_id);
        log_message('debug', 'author_id: ' . $author_id);
        log_message('debug', 'copyright_owner: ' . $copyright_owner);
        log_message('debug', 'description: ' . $description);
        log_message('debug', 'channel_type: ' . $channel_type);
        log_message('debug', 'qty: ' . $qty);
        log_message('debug', 'order_id: ' . $order_id);
        log_message('debug', 'order_date: ' . $order_date);

        if ($qty < 0) {

            $qty = abs($record['lost_qty']);
            $royalty_value_inr = (($record['paper_back_inr'] * $record['paper_back_royalty']) / 100) * $qty;

            $comments = "Paperback royalty @ 20%, Per book cost: {$record['paper_back_inr']}; Qty: {$qty}";

            log_message('debug', 'royalty_value_inr: ' . $royalty_value_inr);
            log_message('debug', 'comments: ' . $comments);

            // Insert Stock Ledger
            $stock_ledger_data = array(
                'book_id' => $book_id,
                "order_id" => $order_id,  
                "author_id" => $author_id,
                "copyright_owner" => $copyright_owner,
                "description" => "Stock added to Inventory",
                "channel_type" => "STK",
                "stock_in" => $qty,
                "stock_out" => 0,
                "current_stock" => 0,
                'transaction_date' => date('Y-m-d H:i:s'),
            );
            $db->table('pustaka_paperback_stock_ledger')->insert($stock_ledger_data);

            log_message('debug', 'Stock Ledger - inserted');

            // Insert Author Transaction
            $auth_transaction_data = array(
                'book_id' => $book_id,
                "order_id" => $order_id,
                "order_date" => $order_date,
                "author_id" => $author_id,
                "order_type" => 15,
                "copyright_owner" => $copyright_owner,
                "currency" =>'INR',
                "book_final_royalty_value_inr" => $royalty_value_inr,
                "pay_status" => 'O',
                "comments" => $comments
            );
            $db->table('author_transaction')->insert($auth_transaction_data);

            log_message('debug', 'Author Transaction - inserted');

            // Update stock
            $update_stock_sql ="UPDATE paperback_stock 
                                SET quantity = quantity + $qty, 
                                    lost_qty = lost_qty + $qty 
                                WHERE book_id = $book_id";

            $db->query($update_stock_sql);

            log_message('debug', 'Excess qty - reduced by ' . $qty);

            $return_data = "Excess Quantity for Book ID: " . $book_id . " is reduced by " . $qty . " now !!!";

        } else {

            // Update lost qty & stock in hand
            $update_stock_sql ="UPDATE paperback_stock 
                                SET lost_qty = lost_qty - $qty, 
                                    stock_in_hand = stock_in_hand + $qty 
                                WHERE book_id = $book_id";

            $db->query($update_stock_sql);

            log_message('debug', 'Lost qty - updated by ' . $qty);

            $return_data = "Lost Quantity for Book ID: " . $book_id . " is reduced by " . $qty . " now !!!";
        }

        return $return_data;
    }

   function saveBulkStock($books)
{
    foreach ($books as $b) {

        $book_id = $b['book_id'];
        $qty     = $b['quantity'];

        if (empty($book_id) || empty($qty)) {
            log_message('error', 'Missing data in saveBulkStock');
            continue; // continue to next book instead of stopping everything
        }

        $order_id = time();

        // Check existing stock
        $stockRow = $this->db->query(
            "SELECT * FROM paperback_stock WHERE book_id = " . (int)$book_id
        );

        if ($stockRow->getNumRows() == 1) {

            // Use one UPDATE query instead of 5 separate queries
            $updateData = [
                'quantity'         => "quantity + $qty",
                'stock_in_hand'    => "stock_in_hand + $qty",
                'last_update_date' => date('Y-m-d H:i:s'),
                'updated_user_id'  => session()->get('user_id'),
                'validated_flag'   => 0
            ];

            // Manual SQL because CI4 will not evaluate arithmetic in set()
            $sql = "
                UPDATE paperback_stock SET
                    quantity = quantity + $qty,
                    stock_in_hand = stock_in_hand + $qty,
                    last_update_date = NOW(),
                    updated_user_id = " . session()->get('user_id') . ",
                    validated_flag = 0
                WHERE book_id = $book_id
            ";
            $this->db->query($sql);

        } else {

            // Insert new stock
            $insert_data = [
                'book_id'         => $book_id,
                'quantity'        => $qty,
                'stock_in_hand'   => $qty,
                'last_update_date'=> date('Y-m-d H:i:s'),
                'updated_user_id' => session()->get('user_id'),
                'validated_flag'  => 0
            ];

            $this->db->table('paperback_stock')->insert($insert_data);
        }

        // Fetch transaction details
        $transaction = $this->db->query(
            "SELECT * FROM book_tbl WHERE book_id = $book_id"
        )->getRowArray();

        if (!$transaction) {
            log_message('error', "Book not found for ID: $book_id");
            continue;
        }

        // Royalty calculation
        $royalty_value_inr = $transaction['paper_back_inr'] * $qty * 0.2;
        $comments = "Paperback royalty @ 20%, Per book cost: {$transaction['paper_back_inr']}; Qty: {$qty}";

        // Insert into author_transaction
        $transaction_data = [
            'book_id'                          => $transaction['book_id'],
            'order_id'                         => $order_id,
            'order_date'                       => date('Y-m-d H:i:s'),
            'author_id'                        => $transaction['author_name'],
            'order_type'                       => 15,
            'copyright_owner'                  => $transaction['paper_back_copyright_owner'],
            'currency'                         => 'INR',
            'book_final_royalty_value_inr'     => $royalty_value_inr,
            'pay_status'                       => 'O',
            'comments'                         => $comments
        ];
        $this->db->table('author_transaction')->insert($transaction_data);

        // Get updated stock for ledger
        $stock = $this->db->query("
            SELECT book_tbl.*, paperback_stock.quantity as current_stock
            FROM book_tbl
            JOIN paperback_stock ON paperback_stock.book_id = book_tbl.book_id
            WHERE book_tbl.book_id = $book_id
        ")->getRowArray();

        // Insert into stock ledger
        $stock_data = [
            'book_id'         => $stock['book_id'],
            'order_id'        => $order_id,
            'author_id'       => $stock['author_name'],
            'copyright_owner' => $stock['paper_back_copyright_owner'],
            'description'     => "Stock added to Inventory",
            'channel_type'    => "STK",
            'stock_in'        => $qty,
            'transaction_date'=> date('Y-m-d H:i:s')
        ];
        $this->db->table('pustaka_paperback_stock_ledger')->insert($stock_data);
    }

   return [
                'status' => 1,
                'order_id' => $order_id,
                'message' => 'Bulk Stock Added successfully'
            ];
}

    public function returnBookshopBulkOrder($books)
    {
       foreach ($books as $b) {

            $book_id = $b['book_id'];
            $qty     = $b['quantity'];

            if (empty($book_id) || empty($qty)) {
                log_message('error', 'Missing data in saveBulkStock');
                continue; // skip this row
            }

            // Update stock
            $update_sql = "
                UPDATE paperback_stock 
                SET quantity = quantity + $qty,
                    stock_in_hand = stock_in_hand + $qty 
                WHERE book_id = $book_id
            ";
            $this->db->query($update_sql);

            // Fetch book details
            $stock_sql = "SELECT * FROM book_tbl WHERE book_id = $book_id";
            $temp = $this->db->query($stock_sql);
            $stock = $temp->getResultArray()[0];

            $bookshop_order_id = '1751609871';
            $description = "Bookshop Return - S T Book Traders";
            $channel_type = "BKS";

            $stock_data = array(
                'book_id'          => $stock['book_id'],
                'order_id'         => $bookshop_order_id,
                'author_id'        => $stock['author_name'],
                'copyright_owner'  => $stock['paper_back_copyright_owner'],
                'description'      => $description,
                'stock_in'         => $qty,
                'channel_type'     => $channel_type,
                'transaction_date' => date('Y-m-d H:i:s'),
            );

            $this->db->table('pustaka_paperback_stock_ledger')->insert($stock_data);
        }

        // always return after loop completes
        return 1;

    }

}