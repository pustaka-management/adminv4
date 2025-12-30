<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function getOverallSales()
    {
        // Year-wise sales query
        $sql_year = "
            SELECT 
                COALESCE(rc.fy, o.fy, p.fy, bf.fy) AS fy,
                COALESCE(rc.total_revenue, 0) AS total_revenue,
                COALESCE(rc.ebook_revenue, 0) + COALESCE(o.ebook_revenue, 0) AS ebook_revenue,
                COALESCE(rc.audiobook_revenue, 0) + COALESCE(o.audiobook_revenue, 0) AS audiobook_revenue,
                COALESCE(p.amazon_order, 0) + 
                COALESCE(p.author_order, 0) + 
                COALESCE(p.bookshop_order, 0) + 
                COALESCE(p.pod_order, 0) + 
                COALESCE(p.flipkart_order, 0) + 
                COALESCE(p.offline_order, 0) AS paperback_revenue,
                COALESCE(bf.bookfair_order, 0) AS book_fair_revenue
            FROM (
                SELECT 
                    fy,
                    SUM(revenue) AS total_revenue,
                    SUM(CASE WHEN type = 'ebook' THEN revenue ELSE 0 END) AS ebook_revenue,
                    SUM(CASE WHEN type = 'audiobook' THEN revenue ELSE 0 END) AS audiobook_revenue
                FROM royalty_consolidation
                GROUP BY fy
            ) rc
            LEFT JOIN (
                SELECT 
                    fy,
                    SUM(CASE WHEN cart_type = 1 THEN net_total ELSE 0 END) AS ebook_revenue,
                    SUM(CASE WHEN cart_type = 2 THEN net_total ELSE 0 END) AS audiobook_revenue
                FROM `order`
                GROUP BY fy
            ) o ON rc.fy = o.fy
            LEFT JOIN (
                SELECT 
                    fy.financial_year AS fy,
                    COALESCE(amazon_order, 0) AS amazon_order,
                    COALESCE(author_order, 0) AS author_order,
                    COALESCE(bookshop_order, 0) AS bookshop_order,
                    COALESCE(pod_order, 0) AS pod_order,
                    COALESCE(flipkart_order, 0) AS flipkart_order,
                    COALESCE(offline_order, 0) AS offline_order
                FROM (
                    SELECT DISTINCT
                        CASE 
                            WHEN EXTRACT(MONTH FROM order_date) >= 4 
                            THEN CONCAT(EXTRACT(YEAR FROM order_date), '-', EXTRACT(YEAR FROM order_date) + 1)
                            ELSE CONCAT(EXTRACT(YEAR FROM order_date) - 1, '-', EXTRACT(YEAR FROM order_date))
                        END AS financial_year
                    FROM (
                        SELECT order_date FROM pod_author_order
                        UNION ALL SELECT order_date FROM pod_bookshop_order_details
                        UNION ALL SELECT order_date FROM pod_order_details
                        UNION ALL SELECT order_date FROM pod_flipkart_order
                    ) AS all_orders
                ) fy
                LEFT JOIN (
                    SELECT 
                        CASE 
                            WHEN EXTRACT(MONTH FROM date) >= 4 
                            THEN CONCAT(EXTRACT(YEAR FROM date), '-', EXTRACT(YEAR FROM date) + 1)
                            ELSE CONCAT(EXTRACT(YEAR FROM date) - 1, '-', EXTRACT(YEAR FROM date))
                        END AS financial_year,
                        SUM(total_earnings) AS amazon_order
                    FROM amazon_paperback_transactions
                    GROUP BY financial_year
                ) amazon ON fy.financial_year = amazon.financial_year
                LEFT JOIN (
                    SELECT 
                        CASE 
                            WHEN EXTRACT(MONTH FROM order_date) >= 4 
                            THEN CONCAT(EXTRACT(YEAR FROM order_date), '-', EXTRACT(YEAR FROM order_date) + 1)
                            ELSE CONCAT(EXTRACT(YEAR FROM order_date) - 1, '-', EXTRACT(YEAR FROM order_date))
                        END AS financial_year,
                        SUM(net_total) AS author_order
                    FROM pod_author_order
                    GROUP BY financial_year
                ) author ON fy.financial_year = author.financial_year
                LEFT JOIN (
                    SELECT 
                        CASE 
                            WHEN EXTRACT(MONTH FROM order_date) >= 4 
                            THEN CONCAT(EXTRACT(YEAR FROM order_date), '-', EXTRACT(YEAR FROM order_date) + 1)
                            ELSE CONCAT(EXTRACT(YEAR FROM order_date) - 1, '-', EXTRACT(YEAR FROM order_date))
                        END AS financial_year,
                        SUM(total_amount) AS bookshop_order
                    FROM pod_bookshop_order_details
                    GROUP BY financial_year
                ) bookshop ON fy.financial_year = bookshop.financial_year
                LEFT JOIN (
                    SELECT 
                        CASE 
                            WHEN EXTRACT(MONTH FROM order_date) >= 4 
                            THEN CONCAT(EXTRACT(YEAR FROM order_date), '-', EXTRACT(YEAR FROM order_date) + 1)
                            ELSE CONCAT(EXTRACT(YEAR FROM order_date) - 1, '-', EXTRACT(YEAR FROM order_date))
                        END AS financial_year,
                        SUM(price) AS pod_order
                    FROM pod_order_details
                    GROUP BY financial_year
                ) pod ON fy.financial_year = pod.financial_year
                LEFT JOIN (
                    SELECT 
                        CASE 
                            WHEN EXTRACT(MONTH FROM order_date) >= 4 
                            THEN CONCAT(EXTRACT(YEAR FROM order_date), '-', EXTRACT(YEAR FROM order_date) + 1)
                            ELSE CONCAT(EXTRACT(YEAR FROM order_date) - 1, '-', EXTRACT(YEAR FROM order_date))
                        END AS financial_year,
                        SUM(grand_total) AS flipkart_order
                    FROM pod_flipkart_order
                    GROUP BY financial_year
                ) flipkart ON fy.financial_year = flipkart.financial_year
                LEFT JOIN (
                    SELECT 
                        CONCAT(
                            YEAR(DATE_ADD(ship_date, INTERVAL -3 MONTH)), '-', 
                            YEAR(DATE_ADD(ship_date, INTERVAL -3 MONTH)) + 1
                        ) AS financial_year,
                        SUM(pod.quantity * b.paper_back_inr) AS offline_order
                    FROM pustaka_offline_orders_details pod
                    JOIN book_tbl b ON pod.book_id = b.book_id
                    WHERE pod.ship_status = 1
                    GROUP BY financial_year
                ) offline ON fy.financial_year = offline.financial_year
            ) p ON COALESCE(rc.fy, o.fy) = p.fy
            LEFT JOIN (
                SELECT 
                    CASE 
                        WHEN EXTRACT(MONTH FROM book_fair_start_date) >= 4 
                            THEN CONCAT(EXTRACT(YEAR FROM book_fair_start_date), '-', EXTRACT(YEAR FROM book_fair_start_date) + 1)
                        ELSE CONCAT(EXTRACT(YEAR FROM book_fair_start_date) - 1, '-', EXTRACT(YEAR FROM book_fair_start_date))
                    END AS fy,
                    SUM(total_amount) AS bookfair_order
                FROM book_fair_item_wise_sale
                GROUP BY fy
            ) bf ON bf.fy = COALESCE(rc.fy, o.fy, p.fy)
            ORDER BY fy DESC
        ";

        $query = $this->db->query($sql_year);
        $data['overall_sales'] = $query->getResultArray();

        // Month-wise sales query
        $sql_month = "
            SELECT 
                o.year,
                o.month,
                o.ebook_revenue + r.ebook_revenue AS ebook_revenue,
                o.audiobook_revenue + r.audiobook_revenue AS audiobook_revenue,
                COALESCE(pb.paperback_revenue, 0) + COALESCE(bf.bookfair_order, 0) AS paperback_revenue
            FROM 
                (
                    SELECT 
                        YEAR(date_created) AS year,
                        MONTH(date_created) AS month,
                        SUM(CASE WHEN cart_type = 1 THEN net_total ELSE 0 END) AS ebook_revenue,
                        SUM(CASE WHEN cart_type = 2 THEN net_total ELSE 0 END) AS audiobook_revenue
                    FROM `order`
                    GROUP BY YEAR(date_created), MONTH(date_created)
                ) o
            LEFT JOIN (
                SELECT 
                    year,
                    month,
                    SUM(CASE WHEN type = 'ebook' THEN revenue ELSE 0 END) AS ebook_revenue,
                    SUM(CASE WHEN type = 'audiobook' THEN revenue ELSE 0 END) AS audiobook_revenue
                FROM royalty_consolidation
                GROUP BY year, month
            ) r ON o.year = r.year AND o.month = r.month
            LEFT JOIN (
                SELECT 
                    SUBSTRING(fy.month_year, 1, 4) AS year,
                    SUBSTRING(fy.month_year, 6, 2) AS month,
                    COALESCE(amazon.amazon_order_revenue, 0) +
                    COALESCE(bookshop.bookshop_order, 0) +
                    COALESCE(pod.pod_order, 0) +
                    COALESCE(flipkart.flipkart_order, 0) +
                    COALESCE(offline.offline_order, 0) AS paperback_revenue
                FROM (
                    SELECT DISTINCT DATE_FORMAT(order_date, '%Y-%m') AS month_year
                    FROM (
                        SELECT order_date FROM pod_bookshop_order_details
                        UNION ALL
                        SELECT order_date FROM pod_order_details
                        UNION ALL
                        SELECT order_date FROM pod_flipkart_order
                    ) AS all_dates
                ) fy
                LEFT JOIN (
                    SELECT 
                        DATE_FORMAT(date, '%Y-%m') AS month_year,
                        SUM(total_earnings) AS amazon_order_revenue
                    FROM amazon_paperback_transactions
                    GROUP BY month_year
                ) amazon ON fy.month_year = amazon.month_year
                LEFT JOIN (
                    SELECT 
                        DATE_FORMAT(order_date, '%Y-%m') AS month_year,
                        SUM(total_amount) AS bookshop_order
                    FROM pod_bookshop_order_details
                    GROUP BY month_year
                ) bookshop ON fy.month_year = bookshop.month_year
                LEFT JOIN (
                    SELECT 
                        DATE_FORMAT(order_date, '%Y-%m') AS month_year,
                        SUM(price) AS pod_order
                    FROM pod_order_details
                    GROUP BY month_year
                ) pod ON fy.month_year = pod.month_year
                LEFT JOIN (
                    SELECT 
                        DATE_FORMAT(order_date, '%Y-%m') AS month_year,
                        SUM(grand_total) AS flipkart_order
                    FROM pod_flipkart_order
                    GROUP BY month_year
                ) flipkart ON fy.month_year = flipkart.month_year
                LEFT JOIN (
                    SELECT 
                        DATE_FORMAT(DATE_ADD(ship_date, INTERVAL -3 MONTH), '%Y-%m') AS month_year,
                        SUM(pod.quantity * b.paper_back_inr) AS offline_order
                    FROM pustaka_offline_orders_details pod
                    JOIN book_tbl b ON pod.book_id = b.book_id
                    WHERE pod.ship_status = 1
                    GROUP BY month_year
                ) offline ON fy.month_year = offline.month_year
            ) pb ON o.year = pb.year AND o.month = pb.month
            LEFT JOIN (
                SELECT 
                    DATE_FORMAT(book_fair_start_date, '%Y') AS year,
                    DATE_FORMAT(book_fair_start_date, '%m') AS month,
                    SUM(total_amount) AS bookfair_order
                FROM book_fair_item_wise_sale
                GROUP BY year, month
            ) bf ON o.year = bf.year AND o.month = bf.month
            ORDER BY o.year DESC, o.month DESC
        ";

        $query_month = $this->db->query($sql_month);
        $data['month_wise_sales'] = $query_month->getResultArray();

        return $data;
    }

    public function channelwisesales()
    {
        $data = [];

        // pustaka ebook
        $sql_yr_pus = "SELECT fy, SUM(net_total) AS revenue FROM `order` WHERE cart_type = 1 AND fy IS NOT NULL 
                    GROUP BY fy ORDER BY fy DESC";
        $query = $this->db->query($sql_yr_pus);
        $data['pustaka_yearwise'] = $query->getResultArray();

        $sql_mon_pus = "SELECT YEAR(date_created) AS year, MONTH(date_created) AS month, SUM(net_total) AS revenue
                        FROM `order` WHERE cart_type = 1 AND fy IS NOT NULL GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $this->db->query($sql_mon_pus);
        $data['pustaka_monthwise'] = $query->getResultArray();

        // amazon ebook
        $sql_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation where channel='amazon' and type='ebook'";
        $query = $this->db->query($sql_tot);
        $data['amazon_total'] = $query->getRowArray();

        $sql_yr_amz = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation where channel='amazon' and type='ebook' 
                    GROUP BY fy ORDER BY fy DESC";
        $query = $this->db->query($sql_yr_amz);
        $data['amazon_yearwise'] = $query->getResultArray();

        $sql_mon_amz = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation where channel='amazon' and type='ebook' 
                        GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $this->db->query($sql_mon_amz);
        $data['amazon_monthwise'] = $query->getResultArray();

        // overdrive ebook
        $sql_ovr_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation where channel='overdrive' and type='ebook'";
        $query = $this->db->query($sql_ovr_tot);
        $data['overdrive_total'] = $query->getRowArray();

        $sql_yr_ovr = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation where channel='overdrive' and type='ebook' 
                    GROUP BY fy ORDER BY fy DESC";
        $query = $this->db->query($sql_yr_ovr);
        $data['overdrive_yearwise'] = $query->getResultArray();

        $sql_mon_ovr = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation where channel='overdrive' and type='ebook' 
                        GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $this->db->query($sql_mon_ovr);
        $data['overdrive_monthwise'] = $query->getResultArray();

        // google ebook
        $sql_gog_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation where channel='google' and type='ebook'";
        $query = $this->db->query($sql_gog_tot);
        $data['google_total'] = $query->getRowArray();

        $sql_yr_gog = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation where channel='google' and type='ebook' 
                    GROUP BY fy ORDER BY fy DESC";
        $query = $this->db->query($sql_yr_gog);
        $data['google_yearwise'] = $query->getResultArray();

        $sql_mon_gog = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation where channel='google' and type='ebook' 
                        GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $this->db->query($sql_mon_gog);
        $data['google_monthwise'] = $query->getResultArray();

        // scribd ebook
        $sql_sci_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation where channel='scribd' and type='ebook'";
        $query = $this->db->query($sql_sci_tot);
        $data['scribd_total'] = $query->getRowArray();

        $sql_yr_sci = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation where channel='scribd' and type='ebook' 
                    GROUP BY fy ORDER BY fy DESC";
        $query = $this->db->query($sql_yr_sci);
        $data['scribd_yearwise'] = $query->getResultArray();

        $sql_mon_sci = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation where channel='scribd' and type='ebook' 
                        GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $this->db->query($sql_mon_sci);
        $data['scribd_monthwise'] = $query->getResultArray();

        // storytel ebook
        $sql_sto_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation where channel='storytel' and type='ebook'";
        $query = $this->db->query($sql_sto_tot);
        $data['storytel_total'] = $query->getRowArray();

        $sql_yr_sto = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation where channel='storytel' and type='ebook' 
                    GROUP BY fy ORDER BY fy DESC";
        $query = $this->db->query($sql_yr_sto);
        $data['storytel_yearwise'] = $query->getResultArray();

        $sql_mon_sto = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation where channel='storytel' and type='ebook' 
                        GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $this->db->query($sql_mon_sto);
        $data['storytel_monthwise'] = $query->getResultArray();

        // pratilipi ebook
        $sql_pra_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation where channel='pratilipi' and type='ebook'";
        $query = $this->db->query($sql_pra_tot);
        $data['pratilipi_total'] = $query->getRowArray();

        $sql_yr_pra = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation where channel='pratilipi' and type='ebook' 
                    GROUP BY fy ORDER BY fy DESC";
        $query = $this->db->query($sql_yr_pra);
        $data['pratilipi_yearwise'] = $query->getResultArray();

        $sql_mon_pra = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation where channel='pratilipi' and type='ebook' 
                        GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $this->db->query($sql_mon_pra);
        $data['pratilipi_monthwise'] = $query->getResultArray();

        // kobo ebook
        $sql_kob_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation where channel='kobo' and type='ebook'";
        $query = $this->db->query($sql_kob_tot);
        $data['kobo_total'] = $query->getRowArray();

        $sql_yr_kob = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation where channel='kobo' and type='ebook' 
                    GROUP BY fy ORDER BY fy DESC";
        $query = $this->db->query($sql_yr_kob);
        $data['kobo_yearwise'] = $query->getResultArray();

        $sql_mon_kob = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation where channel='kobo' and type='ebook' 
                        GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $this->db->query($sql_mon_kob);
        $data['kobo_monthwise'] = $query->getResultArray();

        // pustaka audiobook
        $sql_yr_aud = "SELECT fy, SUM(net_total) AS revenue FROM `order` WHERE cart_type = 2 AND fy IS NOT NULL 
                    GROUP BY fy ORDER BY fy DESC";
        $query = $this->db->query($sql_yr_aud);
        $data['pustaka_aud_yearwise'] = $query->getResultArray();

        $sql_mon_aud = "SELECT YEAR(date_created) AS year, MONTH(date_created) AS month, SUM(net_total) AS revenue
                        FROM `order` WHERE cart_type = 2 AND fy IS NOT NULL GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $this->db->query($sql_mon_aud);
        $data['pustaka_aud_monthwise'] = $query->getResultArray();

        // audible audiobook
        $sql_aud_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation where channel='audible' and type='audiobook'";
        $query = $this->db->query($sql_aud_tot);
        $data['audible_total'] = $query->getRowArray();

        $sql_yr_aud = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation where channel='audible' and type='audiobook' 
                    GROUP BY fy ORDER BY fy DESC";
        $query = $this->db->query($sql_yr_aud);
        $data['audible_yearwise'] = $query->getResultArray();

        $sql_mon_aud = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation where channel='audible' and type='audiobook' 
                        GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $this->db->query($sql_mon_aud);
        $data['audible_monthwise'] = $query->getResultArray();

        // overdrive audiobook
        $sql_ovr_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation where channel='overdrive' and type='audiobook'";
        $query = $this->db->query($sql_ovr_tot);
        $data['overdrive_aud_total'] = $query->getRowArray();

        $sql_yr_ovr = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation where channel='overdrive' and type='audiobook' 
                    GROUP BY fy ORDER BY fy DESC";
        $query = $this->db->query($sql_yr_ovr);
        $data['overdrive_aud_yearwise'] = $query->getResultArray();

        $sql_mon_ovr = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation where channel='overdrive' and type='audiobook' 
                            GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $this->db->query($sql_mon_ovr);
        $data['overdrive_aud_monthwise'] = $query->getResultArray();

        //google audiobook
        $sql_goog_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation where channel='google' and type='audiobook'";
        $query = $this->db->query($sql_goog_tot);
        $data['google_aud_total'] = $query->getRowArray();

        $sql_yr_goog = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation where channel='google' and type='audiobook' 
                    GROUP BY fy ORDER BY fy DESC";
        $query = $this->db->query($sql_yr_goog);
        $data['google_aud_yearwise'] = $query->getResultArray();

        $sql_mon_goog = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation where channel='google' and type='audiobook' 
                            GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $this->db->query($sql_mon_goog);
        $data['google_aud_monthwise'] = $query->getResultArray();

        //storytel audiobook
        $sql_stot_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation where channel='storytel' and type='audiobook'";
        $query = $this->db->query($sql_stot_tot);
        $data['storytel_aud_total'] = $query->getRowArray();

        $sql_yr_stot = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation where channel='storytel' and type='audiobook' 
                    GROUP BY fy ORDER BY fy DESC";
        $query = $this->db->query($sql_yr_stot);
        $data['storytel_aud_yearwise'] = $query->getResultArray();

        $sql_mon_stot = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation where channel='storytel' and type='audiobook' 
                            GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $this->db->query($sql_mon_stot);
        $data['storytel_aud_monthwise'] = $query->getResultArray();

        // youtube audiobook
        $sql_yt_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation where channel='youtube' and type='audiobook'";
        $query = $this->db->query($sql_yt_tot);
        $data['youtube_aud_total'] = $query->getRowArray();

        $sql_yr_yt = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation where channel='youtube' and type='audiobook' 
                    GROUP BY fy ORDER BY fy DESC";
        $query = $this->db->query($sql_yr_yt);
        $data['youtube_aud_yearwise'] = $query->getResultArray();

        $sql_mon_yt = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation where channel='youtube' and type='audiobook' 
                        GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $this->db->query($sql_mon_yt);
        $data['youtube_aud_monthwise'] = $query->getResultArray();

        //kukufm audiobook
        $sql_kukufm_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation where channel='kukufm' and type='audiobook'";
        $query = $this->db->query($sql_kukufm_tot);
        $data['kukufm_aud_total'] = $query->getRowArray();

        $sql_yr_kukufm = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation where channel='kukufm' and type='audiobook' 
                    GROUP BY fy ORDER BY fy DESC";
        $query = $this->db->query($sql_yr_kukufm);
        $data['kukufm_aud_yearwise'] = $query->getResultArray();

        $sql_mon_kukufm = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation where channel='kukufm' and type='audiobook' 
                            GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $this->db->query($sql_mon_kukufm);
        $data['kukufm_aud_monthwise'] = $query->getResultArray();

        return $data;
    }
    public function salesDashboardDetails(): array
    {
        $sql = "WITH revenue_data AS (
                SELECT 
                    COALESCE(rc.fy, o.fy) AS fy,
                    rc.total_revenue,
                    COALESCE(rc.ebook_revenue, 0) + COALESCE(o.ebook_revenue, 0) AS ebook_revenue,
                    COALESCE(rc.audiobook_revenue, 0) + COALESCE(o.audiobook_revenue, 0) AS audiobook_revenue
                FROM (
                    SELECT 
                        fy,
                        SUM(revenue) AS total_revenue,
                        SUM(CASE WHEN type = 'ebook' THEN revenue ELSE 0 END) AS ebook_revenue,
                        SUM(CASE WHEN type = 'audiobook' THEN revenue ELSE 0 END) AS audiobook_revenue
                    FROM royalty_consolidation
                    GROUP BY fy
                ) rc
                LEFT JOIN (
                    SELECT 
                        fy,
                        SUM(CASE WHEN cart_type = 1 THEN net_total ELSE 0 END) AS ebook_revenue,
                        SUM(CASE WHEN cart_type = 2 THEN net_total ELSE 0 END) AS audiobook_revenue
                    FROM `order`
                    GROUP BY fy
                ) o ON rc.fy = o.fy
            ),
            paperback_data AS (
                SELECT 
                    fy.financial_year AS fy,
                    COALESCE(amazon.amazon_order, 0) +
                    COALESCE(bookshop.bookshop_order, 0) +
                    COALESCE(pod.pod_order, 0) +
                    COALESCE(flipkart.flipkart_order, 0) +
                    COALESCE(offline.offline_order, 0) +
                    COALESCE(bookfair.bookfair_order, 0) AS paperback_revenue
                FROM (
                    SELECT DISTINCT
                        CASE 
                            WHEN EXTRACT(MONTH FROM dt) >= 4 
                            THEN CONCAT(EXTRACT(YEAR FROM dt), '-', EXTRACT(YEAR FROM dt) + 1)
                            ELSE CONCAT(EXTRACT(YEAR FROM dt) - 1, '-', EXTRACT(YEAR FROM dt))
                        END AS financial_year
                    FROM (
                        SELECT date as dt FROM amazon_paperback_transactions
                        UNION ALL
                        SELECT order_date as dt FROM pod_bookshop_order_details
                        UNION ALL
                        SELECT order_date as dt FROM pod_order_details
                        UNION ALL
                        SELECT order_date as dt FROM pod_flipkart_order
                        UNION ALL
                        SELECT book_fair_start_date as dt FROM book_fair_item_wise_sale
                    ) AS all_dates
                ) fy
                LEFT JOIN (
                    SELECT 
                        CASE 
                            WHEN EXTRACT(MONTH FROM date) >= 4 
                            THEN CONCAT(EXTRACT(YEAR FROM date), '-', EXTRACT(YEAR FROM date) + 1)
                            ELSE CONCAT(EXTRACT(YEAR FROM date) - 1, '-', EXTRACT(YEAR FROM date))
                        END AS financial_year,
                        SUM(total_earnings) AS amazon_order
                    FROM amazon_paperback_transactions
                    GROUP BY financial_year
                ) amazon ON fy.financial_year = amazon.financial_year
                LEFT JOIN (
                    SELECT 
                        CASE 
                            WHEN EXTRACT(MONTH FROM order_date) >= 4 
                            THEN CONCAT(EXTRACT(YEAR FROM order_date), '-', EXTRACT(YEAR FROM order_date) + 1)
                            ELSE CONCAT(EXTRACT(YEAR FROM order_date) - 1, '-', EXTRACT(YEAR FROM order_date))
                        END AS financial_year,
                        SUM(total_amount) AS bookshop_order
                    FROM pod_bookshop_order_details
                    GROUP BY financial_year
                ) bookshop ON fy.financial_year = bookshop.financial_year
                LEFT JOIN (
                    SELECT 
                        CASE 
                            WHEN EXTRACT(MONTH FROM order_date) >= 4 
                            THEN CONCAT(EXTRACT(YEAR FROM order_date), '-', EXTRACT(YEAR FROM order_date) + 1)
                            ELSE CONCAT(EXTRACT(YEAR FROM order_date) - 1, '-', EXTRACT(YEAR FROM order_date))
                        END AS financial_year,
                        SUM(price) AS pod_order
                    FROM pod_order_details
                    GROUP BY financial_year
                ) pod ON fy.financial_year = pod.financial_year
                LEFT JOIN (
                    SELECT 
                        CASE 
                            WHEN EXTRACT(MONTH FROM order_date) >= 4 
                            THEN CONCAT(EXTRACT(YEAR FROM order_date), '-', EXTRACT(YEAR FROM order_date) + 1)
                            ELSE CONCAT(EXTRACT(YEAR FROM order_date) - 1, '-', EXTRACT(YEAR FROM order_date))
                        END AS financial_year,
                        SUM(grand_total) AS flipkart_order
                    FROM pod_flipkart_order
                    GROUP BY financial_year
                ) flipkart ON fy.financial_year = flipkart.financial_year
                LEFT JOIN (
                    SELECT 
                        CONCAT(
                            YEAR(DATE_ADD(pod.ship_date, INTERVAL -3 MONTH)), '-', 
                            YEAR(DATE_ADD(pod.ship_date, INTERVAL -3 MONTH)) + 1
                        ) AS financial_year,
                        SUM(pod.quantity * b.paper_back_inr) AS offline_order
                    FROM pustaka_offline_orders_details pod
                    JOIN book_tbl b ON pod.book_id = b.book_id
                    WHERE pod.ship_status = 1
                    GROUP BY financial_year
                ) offline ON fy.financial_year = offline.financial_year
                LEFT JOIN (
                    SELECT 
                        CASE
                            WHEN EXTRACT(MONTH FROM book_fair_start_date) >= 4 THEN 
                                CONCAT(EXTRACT(YEAR FROM book_fair_start_date), '-', EXTRACT(YEAR FROM book_fair_start_date) + 1)
                            ELSE 
                                CONCAT(EXTRACT(YEAR FROM book_fair_start_date) - 1, '-', EXTRACT(YEAR FROM book_fair_start_date))
                        END AS financial_year,
                        SUM(total_amount) AS bookfair_order
                    FROM book_fair_item_wise_sale
                    GROUP BY financial_year
                ) bookfair ON fy.financial_year = bookfair.financial_year
            ),
            full_revenue AS (
                SELECT 
                    COALESCE(r.fy, p.fy) AS fy,
                    r.total_revenue,
                    r.ebook_revenue,
                    r.audiobook_revenue,
                    COALESCE(p.paperback_revenue, 0) AS paperback_revenue
                FROM revenue_data r
                LEFT JOIN paperback_data p ON r.fy = p.fy
            )
            SELECT * FROM full_revenue
            UNION ALL
            SELECT 
                'Total' AS fy,
                NULL AS total_revenue,
                SUM(ebook_revenue) AS ebook_revenue,
                SUM(audiobook_revenue) AS audiobook_revenue,
                SUM(paperback_revenue) AS paperback_revenue
            FROM full_revenue
            ORDER BY 
                CASE WHEN fy = 'Total' THEN 1 ELSE 0 END,
                fy DESC";

        $db = \Config\Database::connect();
        $query = $db->query($sql);
        return ['total' => $query->getResultArray()];
    }
   public function podsalesDetails()
    {
        
        $sql_tot = "SELECT 
                        (SELECT SUM(invoice_value) FROM pod_publisher_books) AS invoice_value,
                        (SELECT SUM(price) FROM pod_author_order_details) AS author_order,
                        (SELECT SUM(invoice_value) FROM pod_publisher_books) + 
                        (SELECT SUM(price) FROM pod_author_order_details) AS pod_total";

        $query = $this->db->query($sql_tot);
        $data['pod_overall'] = $query->getRowArray();

        
        $sql = "SELECT SUM(invoice_value) AS invoice_value FROM pod_publisher_books";
        $query = $this->db->query($sql);
        $data['publisher_order'] = $query->getRowArray();

        
        $sql_author = "SELECT SUM(price) AS author_order FROM pod_author_order_details";
        $query = $this->db->query($sql_author);
        $data['author_order'] = $query->getRowArray();

        return $data;
    }
    public function paperbackDetails()
    {
        $db = \Config\Database::connect();

        // fy wise pustaka paperback
        $sql = "SELECT 
                    fy.financial_year,
                    COALESCE(amazon.amazon_order, 0) AS amazon_order,
                    COALESCE(bookshop.bookshop_order, 0) AS bookshop_order,
                    COALESCE(pod.pod_order, 0) AS pod_order,
                    COALESCE(flipkart.flipkart_order, 0) AS flipkart_order,
                    COALESCE(offline.offline_order, 0) AS offline_order,
                    COALESCE(bookfair.bookfair_revenue, 0) AS bookfair_revenue,
                    COALESCE(amazon.amazon_order, 0) +
                    COALESCE(bookshop.bookshop_order, 0) +
                    COALESCE(pod.pod_order, 0) +
                    COALESCE(flipkart.flipkart_order, 0) +
                    COALESCE(offline.offline_order, 0) +
                    COALESCE(bookfair.bookfair_revenue, 0) AS paperback_revenue
                FROM (
                    SELECT DISTINCT
                        CASE 
                            WHEN MONTH(order_date) >= 4 
                            THEN CONCAT(YEAR(order_date), '-', YEAR(order_date) + 1)
                            ELSE CONCAT(YEAR(order_date) - 1, '-', YEAR(order_date))
                        END AS financial_year
                    FROM (
                        SELECT date AS order_date FROM amazon_paperback_transactions
                        UNION ALL
                        SELECT order_date FROM pod_bookshop_order_details
                        UNION ALL
                        SELECT order_date FROM pod_order_details
                        UNION ALL
                        SELECT order_date FROM pod_flipkart_order
                    ) AS all_dates
                ) fy
                LEFT JOIN (
                    SELECT 
                        CASE 
                            WHEN MONTH(date) >= 4 
                            THEN CONCAT(YEAR(date), '-', YEAR(date) + 1)
                            ELSE CONCAT(YEAR(date) - 1, '-', YEAR(date))
                        END AS financial_year,
                        SUM(total_earnings) AS amazon_order
                    FROM amazon_paperback_transactions
                    GROUP BY financial_year
                ) amazon ON fy.financial_year = amazon.financial_year
                LEFT JOIN (
                    SELECT 
                        CASE 
                            WHEN MONTH(order_date) >= 4 
                            THEN CONCAT(YEAR(order_date), '-', YEAR(order_date) + 1)
                            ELSE CONCAT(YEAR(order_date) - 1, '-', YEAR(order_date))
                        END AS financial_year,
                        SUM(total_amount) AS bookshop_order
                    FROM pod_bookshop_order_details
                    GROUP BY financial_year
                ) bookshop ON fy.financial_year = bookshop.financial_year
                LEFT JOIN (
                    SELECT 
                        CASE 
                            WHEN MONTH(order_date) >= 4 
                            THEN CONCAT(YEAR(order_date), '-', YEAR(order_date) + 1)
                            ELSE CONCAT(YEAR(order_date) - 1, '-', YEAR(order_date))
                        END AS financial_year,
                        SUM(price) AS pod_order
                    FROM pod_order_details
                    GROUP BY financial_year
                ) pod ON fy.financial_year = pod.financial_year
                LEFT JOIN (
                    SELECT 
                        CASE 
                            WHEN MONTH(order_date) >= 4 
                            THEN CONCAT(YEAR(order_date), '-', YEAR(order_date) + 1)
                            ELSE CONCAT(YEAR(order_date) - 1, '-', YEAR(order_date))
                        END AS financial_year,
                        SUM(grand_total) AS flipkart_order
                    FROM pod_flipkart_order
                    GROUP BY financial_year
                ) flipkart ON fy.financial_year = flipkart.financial_year
                LEFT JOIN (
                    SELECT 
                        CASE 
                            WHEN MONTH(DATE_ADD(pod.ship_date, INTERVAL -3 MONTH)) >= 4 
                            THEN CONCAT(YEAR(DATE_ADD(pod.ship_date, INTERVAL -3 MONTH)), '-', YEAR(DATE_ADD(pod.ship_date, INTERVAL -3 MONTH)) + 1)
                            ELSE CONCAT(YEAR(DATE_ADD(pod.ship_date, INTERVAL -3 MONTH)) - 1, '-', YEAR(DATE_ADD(pod.ship_date, INTERVAL -3 MONTH)))
                        END AS financial_year,
                        SUM(pod.quantity * b.paper_back_inr) AS offline_order
                    FROM pustaka_offline_orders_details pod
                    JOIN book_tbl b ON pod.book_id = b.book_id
                    WHERE pod.ship_status = 1
                    GROUP BY financial_year
                ) offline ON fy.financial_year = offline.financial_year
                LEFT JOIN (
                    SELECT 
                        CASE
                            WHEN MONTH(book_fair_start_date) >= 4 THEN 
                                CONCAT(YEAR(book_fair_start_date), '-', YEAR(book_fair_start_date) + 1)
                            ELSE 
                                CONCAT(YEAR(book_fair_start_date) - 1, '-', YEAR(book_fair_start_date))
                        END AS financial_year,
                        SUM(total_amount) AS bookfair_revenue
                    FROM book_fair_item_wise_sale
                    GROUP BY financial_year
                ) bookfair ON fy.financial_year = bookfair.financial_year
                ORDER BY fy.financial_year DESC";

        $query = $db->query($sql);
        $data['pustaka_paperback'] = $query->getResultArray();

        // monthwise pustaka paperback revenue
        $sql_mon = "SELECT 
                        fy.month_year,
                        COALESCE(amazon.amazon_order, 0) +
                        COALESCE(bookshop.bookshop_order, 0) +
                        COALESCE(pod.pod_order, 0) +
                        COALESCE(flipkart.flipkart_order, 0) +
                        COALESCE(bookfair.bookfair_revenue, 0) +
                        COALESCE(offline.offline_order, 0) AS paperback_revenue
                    FROM (
                        SELECT DISTINCT DATE_FORMAT(dt, '%Y-%m') AS month_year
                        FROM (
                            SELECT `date` AS dt FROM amazon_paperback_transactions
                            UNION ALL
                            SELECT order_date AS dt FROM pod_bookshop_order_details
                            UNION ALL
                            SELECT order_date AS dt FROM pod_order_details
                            UNION ALL
                            SELECT order_date AS dt FROM pod_flipkart_order
                            UNION ALL
                            SELECT book_fair_start_date AS dt FROM book_fair_item_wise_sale
                        ) AS all_dates
                    ) fy

                    LEFT JOIN (
                        SELECT 
                            DATE_FORMAT(`date`, '%Y-%m') AS month_year,
                            SUM(total_earnings) AS amazon_order
                        FROM amazon_paperback_transactions
                        GROUP BY month_year
                    ) amazon ON fy.month_year = amazon.month_year

                    LEFT JOIN (
                        SELECT 
                            DATE_FORMAT(order_date, '%Y-%m') AS month_year,
                            SUM(total_amount) AS bookshop_order
                        FROM pod_bookshop_order_details
                        GROUP BY month_year
                    ) bookshop ON fy.month_year = bookshop.month_year

                    LEFT JOIN (
                        SELECT 
                            DATE_FORMAT(order_date, '%Y-%m') AS month_year,
                            SUM(price) AS pod_order
                        FROM pod_order_details
                        GROUP BY month_year
                    ) pod ON fy.month_year = pod.month_year

                    LEFT JOIN (
                        SELECT 
                            DATE_FORMAT(order_date, '%Y-%m') AS month_year,
                            SUM(grand_total) AS flipkart_order
                        FROM pod_flipkart_order
                        GROUP BY month_year
                    ) flipkart ON fy.month_year = flipkart.month_year

                    LEFT JOIN (
                        SELECT 
                            DATE_FORMAT(book_fair_start_date, '%Y-%m') AS month_year,
                            SUM(total_amount) AS bookfair_revenue
                        FROM book_fair_item_wise_sale
                        GROUP BY month_year
                    ) bookfair ON fy.month_year = bookfair.month_year

                    LEFT JOIN (
                        SELECT 
                            DATE_FORMAT(DATE_ADD(pod.ship_date, INTERVAL -3 MONTH), '%Y-%m') AS month_year,
                            SUM(pod.quantity * b.paper_back_inr) AS offline_order
                        FROM pustaka_offline_orders_details pod
                        JOIN book_tbl b ON pod.book_id = b.book_id
                        WHERE pod.ship_status = 1
                        GROUP BY month_year
                    ) offline ON fy.month_year = offline.month_year

                    ORDER BY fy.month_year DESC";

        $query = $db->query($sql_mon);
        $data['paperback_monthwise'] = $query->getResultArray();

        // paperback_summary 

        $sql_flipkart = "SELECT sum(item_total) as flipkart_order FROM pod_flipkart_order_details";
        $query = $db->query($sql_flipkart);
        $data['flipkart'] = $query->getRowArray();

        $sql_amazon = "SELECT SUM(total_earnings) as amazon_order FROM amazon_paperback_transactions";
        $query = $db->query($sql_amazon);
        $data['amazon'] = $query->getRowArray();

        $sql_bookshop = "SELECT sum(total_amount) as bookshop_order FROM pod_bookshop_order_details";
        $query = $db->query($sql_bookshop);
        $data['bookshop'] = $query->getRowArray();

        $sql_pustaka = "SELECT SUM(price) as pustaka_order FROM pod_order_details";
        $query = $db->query($sql_pustaka);
        $data['pustaka'] = $query->getRowArray();

        $sql_online = "SELECT SUM(pod.quantity * b.paper_back_inr) AS offline_order FROM pustaka_offline_orders_details pod
                    JOIN book_tbl b ON pod.book_id = b.book_id WHERE pod.ship_status = 1";
        $query = $db->query($sql_online);
        $data['online'] = $query->getRowArray();

        $sql_bookfair = "SELECT SUM(total_amount) as book_fair FROM book_fair_item_wise_sale";
        $query = $db->query($sql_bookfair);
        $data['bookfair'] = $query->getRowArray();

        return $data;
    }
    public function ebooksalesDetails()
    {
        $db = \Config\Database::connect();

        $sql = "SELECT fy, SUM(order_revenue + royalty_revenue) AS ebook_revenue
                FROM (SELECT fy, SUM(net_total) AS order_revenue, 0 AS royalty_revenue
                    FROM `order` WHERE fy IS NOT NULL AND cart_type = 1 GROUP BY fy
                    UNION ALL
                    SELECT fy, 0 AS order_revenue, SUM(revenue) AS royalty_revenue
                    FROM royalty_consolidation WHERE type = 'ebook' GROUP BY fy) revenue_data
                GROUP BY fy ORDER BY fy DESC";
        $query = $db->query($sql);
        $data['ebook_total'] = $query->getResultArray();

        //channel wise ebook sales
        //pustaka ebook
        $sql_yr_pus = "SELECT fy, SUM(net_total) AS revenue FROM `order` WHERE cart_type = 1 AND fy IS NOT NULL 
                    GROUP BY fy ORDER BY fy DESC";
        $query = $db->query($sql_yr_pus);
        $data['pustaka_yearwise'] = $query->getResultArray();

        $sql_mon_pus = "SELECT YEAR(date_created) AS year, MONTH(date_created) AS month, SUM(net_total) AS revenue
                        FROM `order` WHERE cart_type = 1 AND fy IS NOT NULL
                        GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $db->query($sql_mon_pus);
        $data['pustaka_monthwise'] = $query->getResultArray();

        $sql_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation WHERE channel='amazon' AND type='ebook'";
        $query = $db->query($sql_tot);
        $data['amazon_total'] = $query->getRowArray();

        $sql_yr_amz = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation 
                    WHERE channel='amazon' AND type='ebook' GROUP BY fy ORDER BY fy DESC";
        $query = $db->query($sql_yr_amz);
        $data['amazon_yearwise'] = $query->getResultArray();

        $sql_mon_amz = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation 
                        WHERE channel='amazon' AND type='ebook' GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $db->query($sql_mon_amz);
        $data['amazon_monthwise'] = $query->getResultArray();

        $sql_ovr_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation 
                        WHERE channel='overdrive' AND type='ebook'";
        $query = $db->query($sql_ovr_tot);
        $data['overdrive_total'] = $query->getRowArray();

        $sql_yr_ovr = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation 
                    WHERE channel='overdrive' AND type='ebook' GROUP BY fy ORDER BY fy DESC";
        $query = $db->query($sql_yr_ovr);
        $data['overdrive_yearwise'] = $query->getResultArray();

        $sql_mon_ovr = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation 
                        WHERE channel='overdrive' AND type='ebook' GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $db->query($sql_mon_ovr);
        $data['overdrive_monthwise'] = $query->getResultArray();

        $sql_gog_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation 
                        WHERE channel='google' AND type='ebook'";
        $query = $db->query($sql_gog_tot);
        $data['google_total'] = $query->getRowArray();

        $sql_yr_gog = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation 
                    WHERE channel='google' AND type='ebook' GROUP BY fy ORDER BY fy DESC";
        $query = $db->query($sql_yr_gog);
        $data['google_yearwise'] = $query->getResultArray();

        $sql_mon_gog = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation 
                        WHERE channel='google' AND type='ebook' GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $db->query($sql_mon_gog);
        $data['google_monthwise'] = $query->getResultArray();

        $sql_sci_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation 
                        WHERE channel='scribd' AND type='ebook'";
        $query = $db->query($sql_sci_tot);
        $data['scribd_total'] = $query->getRowArray();

        $sql_yr_sci = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation 
                    WHERE channel='scribd' AND type='ebook' GROUP BY fy ORDER BY fy DESC";
        $query = $db->query($sql_yr_sci);
        $data['scribd_yearwise'] = $query->getResultArray();

        $sql_mon_sci = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation 
                        WHERE channel='scribd' AND type='ebook' GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $db->query($sql_mon_sci);
        $data['scribd_monthwise'] = $query->getResultArray();

        $sql_sto_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation 
                        WHERE channel='storytel' AND type='ebook'";
        $query = $db->query($sql_sto_tot);
        $data['storytel_total'] = $query->getRowArray();

        $sql_yr_sto = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation 
                    WHERE channel='storytel' AND type='ebook' GROUP BY fy ORDER BY fy DESC";
        $query = $db->query($sql_yr_sto);
        $data['storytel_yearwise'] = $query->getResultArray();

        $sql_mon_sto = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation 
                        WHERE channel='storytel' AND type='ebook' GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $db->query($sql_mon_sto);
        $data['storytel_monthwise'] = $query->getResultArray();

        $sql_pra_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation 
                        WHERE channel='pratilipi' AND type='ebook'";
        $query = $db->query($sql_pra_tot);
        $data['pratilipi_total'] = $query->getRowArray();

        $sql_yr_pra = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation 
                    WHERE channel='pratilipi' AND type='ebook' GROUP BY fy ORDER BY fy DESC";
        $query = $db->query($sql_yr_pra);
        $data['pratilipi_yearwise'] = $query->getResultArray();

        $sql_mon_pra = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation 
                        WHERE channel='pratilipi' AND type='ebook' GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $db->query($sql_mon_pra);
        $data['pratilipi_monthwise'] = $query->getResultArray();

        $sql_kob_tot = "SELECT sum(revenue) as revenue FROM royalty_consolidation 
                        WHERE channel='kobo' AND type='ebook'";
        $query = $db->query($sql_kob_tot);
        $data['kobo_total'] = $query->getRowArray();

        $sql_yr_kob = "SELECT fy, sum(revenue) as revenue FROM royalty_consolidation 
                    WHERE channel='kobo' AND type='ebook' GROUP BY fy ORDER BY fy DESC";
        $query = $db->query($sql_yr_kob);
        $data['kobo_yearwise'] = $query->getResultArray();

        $sql_mon_kob = "SELECT year, month, sum(revenue) as revenue FROM royalty_consolidation 
                        WHERE channel='kobo' AND type='ebook' GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $db->query($sql_mon_kob);
        $data['kobo_monthwise'] = $query->getResultArray();

        return $data;
    }
    public function audiobookSalesDetails()
    {
        $db = \Config\Database::connect();

        $sql = "SELECT fy, SUM(audiobook_revenue) AS audiobook_revenue
                FROM (SELECT fy, SUM(net_total) AS audiobook_revenue FROM `order`
                    WHERE cart_type = 2 GROUP BY fy
                    UNION ALL
                    SELECT fy, SUM(revenue) AS audiobook_revenue FROM royalty_consolidation
                    WHERE type ='audiobook' GROUP BY fy) AS revenue_data
                GROUP BY fy ORDER BY fy DESC";

        $query = $db->query($sql);
        $data['audiobook_total'] = $query->getResultArray();

        // fy wise pustaka audiobook
        $sql_yr_aud = "SELECT fy, SUM(net_total) AS revenue FROM `order` WHERE cart_type = 2 AND fy IS NOT NULL 
                    GROUP BY fy ORDER BY fy DESC";
        $query = $db->query($sql_yr_aud);
        $data['pustaka_aud_yearwise'] = $query->getResultArray();

        // monthwise pustaka audiobook revenue
        $sql_mon_aud = "SELECT YEAR(date_created) AS year, MONTH(date_created) AS month, SUM(net_total) AS revenue
                        FROM `order` WHERE cart_type = 2 AND fy IS NOT NULL GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $db->query($sql_mon_aud);
        $data['pustaka_aud_monthwise'] = $query->getResultArray();

        // audible audiobook
        $sql_aud_tot = "SELECT SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='audible' AND type='audiobook'";
        $query = $db->query($sql_aud_tot);
        $data['audible_total'] = $query->getRowArray();

        $sql_yr_aud = "SELECT fy, SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='audible' AND type='audiobook' GROUP BY fy ORDER BY fy DESC";
        $query = $db->query($sql_yr_aud);
        $data['audible_yearwise'] = $query->getResultArray();

        $sql_mon_aud = "SELECT year, month, SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='audible' AND type='audiobook' GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $db->query($sql_mon_aud);
        $data['audible_aud_monthwise'] = $query->getResultArray();

        // overdrive audiobook
        $sql_ovr_aud_tot = "SELECT SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='overdrive' AND type='audiobook'";
        $query = $db->query($sql_ovr_aud_tot);
        $data['overdrive_aud_total'] = $query->getRowArray();

        $sql_yr_ovr_aud = "SELECT fy, SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='overdrive' AND type='audiobook' GROUP BY fy ORDER BY fy DESC";
        $query = $db->query($sql_yr_ovr_aud);
        $data['overdrive_aud_yearwise'] = $query->getResultArray();

        $sql_mon_ovr_aud = "SELECT year, month, SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='overdrive' AND type='audiobook' GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $db->query($sql_mon_ovr_aud);
        $data['overdrive_aud_monthwise'] = $query->getResultArray();

        // storytel audiobook
        $sql_sto_aud_tot = "SELECT SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='storytel' AND type='audiobook'";
        $query = $db->query($sql_sto_aud_tot);
        $data['storytel_aud_total'] = $query->getRowArray();

        $sql_yr_sto_aud = "SELECT fy, SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='storytel' AND type='audiobook' GROUP BY fy ORDER BY fy DESC";
        $query = $db->query($sql_yr_sto_aud);
        $data['storytel_aud_yearwise'] = $query->getResultArray();

        $sql_mon_sto_aud = "SELECT year, month, SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='storytel' AND type='audiobook' GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $db->query($sql_mon_sto_aud);
        $data['storytel_aud_monthwise'] = $query->getResultArray();

        // google audiobooks
        $sql_gog_aud_tot = "SELECT SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='google' AND type='audiobook'";
        $query = $db->query($sql_gog_aud_tot);
        $data['google_aud_total'] = $query->getRowArray();

        $sql_yr_gog_aud = "SELECT fy, SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='google' AND type='audiobook' GROUP BY fy ORDER BY fy DESC";
        $query = $db->query($sql_yr_gog_aud);
        $data['google_aud_yearwise'] = $query->getResultArray();

        $sql_mon_gog_aud = "SELECT year, month, SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='google' AND type='audiobook' GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $db->query($sql_mon_gog_aud);
        $data['google_aud_monthwise'] = $query->getResultArray();

        // youtube audiobook
        $sql_youtube_aud_tot = "SELECT SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='youtube' AND type='audiobook'";
        $query = $db->query($sql_youtube_aud_tot);
        $data['youtube_aud_total'] = $query->getRowArray();

        $sql_yr_youtube_aud = "SELECT fy, SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='youtube' AND type='audiobook' GROUP BY fy ORDER BY fy DESC";
        $query = $db->query($sql_yr_youtube_aud);
        $data['youtube_aud_yearwise'] = $query->getResultArray();

        $sql_mon_youtube_aud = "SELECT year, month, SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='youtube' AND type='audiobook' GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $db->query($sql_mon_youtube_aud);
        $data['youtube_aud_monthwise'] = $query->getResultArray();

        // kukufm audiobook
        $sql_kukufm_aud_tot = "SELECT SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='kukufm' AND type='audiobook'";
        $query = $db->query($sql_kukufm_aud_tot);
        $data['kukufm_aud_total'] = $query->getRowArray();

        $sql_yr_kukufm_aud = "SELECT fy, SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='kukufm' AND type='audiobook' GROUP BY fy ORDER BY fy DESC";
        $query = $db->query($sql_yr_kukufm_aud);
        $data['kukufm_aud_yearwise'] = $query->getResultArray();

        $sql_mon_kukufm_aud = "SELECT year, month, SUM(revenue) as revenue FROM royalty_consolidation WHERE channel='kukufm' AND type='audiobook' GROUP BY year, month ORDER BY year ASC, month ASC";
        $query = $db->query($sql_mon_kukufm_aud);
        $data['kukufm_aud_monthwise'] = $query->getResultArray();

        return $data;
    }

    function amazonpaperbackDetails()
    {

		$total_sql = "SELECT type, COUNT(*) as total_cnt, sum(product_sales) as total_sales, sum(shipping_credits) 
						    as total_credits, sum(total_earnings) as total_earnings, sum(tds) as total_tds,
							sum(selling_fees) as total_selling_fees, sum(other_transaction_fees) as total_trans_fees, 
							sum(shipping_fees) as total_shipping_fees, sum(final_royalty_value) as total_royalty_value 
							FROM amazon_paperback_transactions 
							where type like 'Order%' group by type";
		$total_query = $this->db->query($total_sql);
		$data['total_earnings'] = $total_query->getResultArray()[0];

		$other_pub_sql = "SELECT type, COUNT(*) as other_pub_cnt, sum(product_sales) as other_pub_sales, sum(shipping_credits) 
						  as other_pub_credits, sum(total_earnings) as other_pub_earnings, sum(tds) as other_pub_tds,
						  sum(selling_fees) as other_pub_selling_fees, sum(other_transaction_fees) as other_pub_trans_fees, 
						  sum(shipping_fees) as other_pub_shipping_fees, sum(final_royalty_value) as other_pub_royalty_value 
						  FROM amazon_paperback_transactions 
						  where type like 'Order%' and sku like 'OP%' ";
		$other_pub_query = $this->db->query($other_pub_sql);
		$data['other_pub_earnings'] = $other_pub_query->getResultArray()[0];

		$pustaka_bks_sql = "SELECT type, COUNT(*) as pustaka_bks_cnt, sum(product_sales) as pustaka_bks_sales, sum(shipping_credits) 
						    as pustaka_bks_credits, sum(total_earnings) as pustaka_bks_earnings, sum(tds) as pustaka_bks_tds,
							sum(selling_fees) as pustaka_bks_selling_fees, sum(other_transaction_fees) as pustaka_bks_trans_fees, 
							sum(shipping_fees) as pustaka_bks_shipping_fees, sum(final_royalty_value) as pustaka_bks_royalty_value 
							FROM amazon_paperback_transactions 
							where type like 'Order%' and sku not like 'OP%' group by type";
		$pustaka_bks_query = $this->db->query($pustaka_bks_sql);
		$data['pustaka_bks_earnings'] = $pustaka_bks_query->getResultArray()[0];

		$transfer_sql = "SELECT 
							DATE_FORMAT(date, '%M %Y') as month_name,
							DATE_FORMAT(date, '%m') as month,
							DATE_FORMAT(date, '%Y') as year, 
							type, 
							SUM(other) as transfer_amazon 
						FROM 
							amazon_paperback_transactions
						WHERE 
							type LIKE 'Transfer%'
						GROUP BY 
							month_name, type 
						ORDER BY 
							year Desc, month desc";
		$transfer_query = $this->db->query($transfer_sql);
		$data['transfers'] = $transfer_query->getResultArray();

		$top_selling_sql= "SELECT sku,description,author_id, COUNT(*) AS sales_count
							FROM amazon_paperback_transactions
							where type like 'Order%'
							GROUP BY sku order by sales_count desc
							limit 10";
		$top_selling_query = $this->db->query($top_selling_sql);
		$data['selling'] = $top_selling_query->getResultArray();

		$top_selling_sql= "SELECT sku,description,author_id, COUNT(*) AS return_count
							FROM amazon_paperback_transactions
							where type like 'Refund%'
							GROUP BY sku order by return_count desc
							limit 10";
		$top_selling_query = $this->db->query($top_selling_sql);
		$data['return'] = $top_selling_query->getResultArray();

		return $data;
	}
    public function amazonPaperbackRevenueDetails()
    {
        $sql ="SELECT 
                    amazon_paperback_books.*,
                    book_tbl.book_title,
                    author_tbl.author_name,
                    language_tbl.language_name
                FROM 
                    amazon_paperback_books
                JOIN book_tbl 
                    ON book_tbl.book_id = amazon_paperback_books.book_id
                JOIN author_tbl 
                    ON author_tbl.author_id = amazon_paperback_books.author_id
                JOIN language_tbl 
                    ON language_tbl.language_id = amazon_paperback_books.language";
        $query = $this->db->query($sql);
        return $query->getResultArray();  
    }
    public function getAmazonPaperbackBookDetails($sku)
    {
        $sql = "SELECT 
                    SUM(total_earnings) AS total_earnings,
                    (SUM(total_earnings) * 100.0 / (SELECT SUM(total_earnings) FROM amazon_paperback_transactions)) AS percentage_contribution
                FROM amazon_paperback_transactions
                WHERE type like 'Order%' and sku = ?";
        $query = $this->db->query($sql, [$sku]);
        $data['tot_count'] = $query->getRowArray();

        $sql_tot = "SELECT type, COUNT(*) as total_cnt, sum(product_sales) as total_sales, sum(shipping_credits) 
                        as total_credits, sum(total_earnings) as total_earnings, sum(tds) as total_tds,
                        sum(selling_fees) as total_selling_fees, sum(other_transaction_fees) as total_trans_fees, 
                        sum(shipping_fees) as total_shipping_fees, sum(final_royalty_value) as total_royalty_value 
                FROM amazon_paperback_transactions 
                WHERE type like 'Order%' AND sku = ?
                GROUP BY type";
        $query = $this->db->query($sql_tot, [$sku]);


        $data['total_earnings'] = $query->getRowArray() ?: [
            'total_cnt' => 0,
            'total_sales' => 0,
            'total_credits' => 0,
            'total_tds' => 0,
            'total_selling_fees' => 0,
            'total_trans_fees' => 0,
            'total_shipping_fees' => 0,
            'total_royalty_value' => 0,
            'total_earnings' => 0,
        ];

        $sql_mon ="SELECT 
                        COUNT(order_id) as total_orders,
                        SUM(quantity) as total_quantity,
                        DATE_FORMAT(`date`, '%Y-%m-01') AS month,
                        SUM(total_earnings) AS monthly_total_earnings
                    FROM amazon_paperback_transactions
                    WHERE sku = ? AND type LIKE 'Order%'
                    GROUP BY DATE_FORMAT(`date`, '%Y-%m')
                    ORDER BY month DESC";
        $query = $this->db->query($sql_mon, [$sku]);
        $data['monthly_earnings'] = $query->getResultArray();

        return $data;
    }
   public function offlinesalesDetails($filter = 'all')
    {
        $year = date("Y");

        if (date("m") >= 4) {
            $current_fy_start = "$year-04-01";
            $current_fy_end   = ($year + 1) . "-03-31";
        } else {
            $current_fy_start = ($year - 1) . "-04-01";
            $current_fy_end   = "$year-03-31";
        }

        $prev_fy_start = date("Y-m-d", strtotime("-1 year", strtotime($current_fy_start)));
        $prev_fy_end   = date("Y-m-d", strtotime("-1 year", strtotime($current_fy_end)));

        $where = "";

        switch ($filter) {
            case "month":
                $where = "AND MONTH(o.order_date)=MONTH(CURDATE()) 
                        AND YEAR(o.order_date)=YEAR(CURDATE())";
                break;

            case "this_year":
                $where = "AND o.order_date BETWEEN '$current_fy_start' AND '$current_fy_end'";
                break;

            case "prev_year":
                $where = "AND o.order_date BETWEEN '$prev_fy_start' AND '$prev_fy_end'";
                break;
        }

        /* ================= Chart ================= */
        $chartSql = "
            SELECT 
                DATE_FORMAT(o.order_date, '%Y-%m') AS order_month,
                COUNT(DISTINCT o.order_id) AS total_orders,
                COUNT(DISTINCT d.book_id) AS total_titles,
                SUM(d.total_amount) AS total_mrp
            FROM pustaka_offline_orders o
            JOIN pustaka_offline_orders_details d 
                ON o.order_id = d.offline_order_id
            WHERE 1=1 $where
            GROUP BY order_month
            ORDER BY order_month ASC
        ";

        $data['chart'] = $this->db->query($chartSql)->getResultArray();

        /* ================= Summary ================= */
        $summarySql = "
            SELECT
                COUNT(DISTINCT d.book_id) AS total_titles,
                COUNT(DISTINCT o.order_id) AS total_orders,
                SUM(o.payment_status='Pending') AS total_pending_orders,
                SUM(o.payment_status='Paid') AS total_paid_orders,
                SUM(d.total_amount) AS total_sales
            FROM pustaka_offline_orders o
            JOIN pustaka_offline_orders_details d 
                ON o.order_id = d.offline_order_id
            WHERE 1=1 $where
        ";

        $data['offline'] = $this->db->query($summarySql)->getRowArray();

        /* ================= Top Selling ================= */
        $topSql = "
            SELECT 
                d.book_id,
                b.book_title,
                COUNT(*) AS total_sold
            FROM pustaka_offline_orders_details d
            JOIN book_tbl b ON b.book_id = d.book_id
            GROUP BY d.book_id
            ORDER BY total_sold DESC
            LIMIT 10
        ";

        $data['top_selling'] = $this->db->query($topSql)->getResultArray();

        $genre_sql="SELECT 
                        genre_details_tbl.genre_id,
                        genre_details_tbl.genre_name,
                        COUNT(pustaka_offline_orders_details.book_id) AS total_sales
                    FROM pustaka_offline_orders_details
                    JOIN book_tbl
                        ON book_tbl.book_id = pustaka_offline_orders_details.book_id
                    JOIN genre_details_tbl
                        ON genre_details_tbl.genre_id = book_tbl.genre_id
                    WHERE pustaka_offline_orders_details.ship_status = 1
                    GROUP BY 
                        genre_details_tbl.genre_id,
                        genre_details_tbl.genre_name
                    ORDER BY total_sales DESC
                    LIMIT 10";
        $query = $this->db->query($genre_sql);
        $data['genre_sales'] = $query->getResultArray();

        $lan_sql="SELECT 
                        language_tbl.language_name,
                        COUNT(distinct(pustaka_offline_orders_details.offline_order_id)) AS offline_order_count
                    FROM 
                        pustaka_offline_orders_details
                    JOIN
                        book_tbl 
                            ON book_tbl.book_id = pustaka_offline_orders_details.book_id
                    JOIN
                        language_tbl 
                            ON language_tbl.language_id = book_tbl.language
                    GROUP BY 
                        language_tbl.language_name";
        $query = $this->db->query($lan_sql);
        $data['language_sales'] = $query->getResultArray();

        $auth_sql="SELECT
                        author_tbl.author_id,
                        author_tbl.author_name,
                        COUNT(distinct(pustaka_offline_orders_details.offline_order_id)) as total_orders,
                        SUM(pustaka_offline_orders_details.total_amount) AS total_revenue
                    FROM pustaka_offline_orders_details
                    JOIN book_tbl 
                        ON book_tbl.book_id = pustaka_offline_orders_details.book_id
                    JOIN author_tbl 
                        ON author_tbl.author_id = book_tbl.author_name
                    GROUP BY
                        author_tbl.author_id,
                        author_tbl.author_name
                    ORDER BY total_revenue DESC
                    LIMIT 10";
        $query = $this->db->query($auth_sql);
        $data['author_sales'] = $query->getResultArray();

        return $data;
    }
}
