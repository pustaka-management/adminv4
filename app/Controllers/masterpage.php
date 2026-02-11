<?php

namespace App\Controllers;

use App\Models\BookModel;

class MasterPage extends BaseController
{
    public function __construct()
    {
        helper(['form', 'url', 'file', 'email', 'html', 'cookie', 'text']);
    }

    public function dashboard($author_id = null)
    {
        $data['title'] = '';
        $data['subtitle'] = '';
        return view('author/masterPage', $data);
    }


}