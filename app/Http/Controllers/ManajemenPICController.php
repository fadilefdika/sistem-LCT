<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManajemenPicController extends Controller
{
    
    public function index()
    {
        return view('pages.admin.manajemen-pic.index');
    }
}
