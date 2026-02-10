<?php

namespace App\Models;

use CodeIgniter\Model;

class ComboBookfairModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }


function combopackupload($books, $comboName)
{
    if (empty($books)) {
        return [
            'status' => 0,
            'message' => 'No books provided'
        ];
    }

    $total_quantity = 0;
    $no_of_title    = count($books);

    foreach ($books as $b) {
        if (empty($b['book_id']) || empty($b['quantity'])) {
            log_message('error', 'Missing data in combopackupload');
            continue;
        }

        $total_quantity += (int) $b['quantity'];
    }

    // 🔹 Insert into bookfair_combo_pack (ONLY ONCE)
    $comboPackData = [
        'pack_name'       =>  $comboName, // change if dynamic
        'no_of_title'     => $no_of_title,
        'total_quantity'  => $total_quantity,
        'created_date'    => date('Y-m-d H:i:s')
    ];

    $this->db->table('bookfair_combo_pack')->insert($comboPackData);

    // ✅ Get last inserted combo ID
    $combo_id = $this->db->insertID();

    // 🔹 Insert combo pack details
    foreach ($books as $b) {

        if (empty($b['book_id']) || empty($b['quantity'])) {
            continue;
        }

        $detailData = [
            'combo_id'      => $combo_id,
            'book_id'       => $b['book_id'],
            'default_value' => $b['quantity'],
            'created_date'  => date('Y-m-d H:i:s')
        ];

        $this->db->table('bookfair_combo_pack_details')->insert($detailData);
    }

    return [
        'status'   => 1,
        'combo_id' => $combo_id,
        'message'  => 'Combo Pack created successfully'
    ];
}

}