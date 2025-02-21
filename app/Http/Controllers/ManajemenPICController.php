<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManajemenPICController extends Controller
{
    
    public function index()
    {
        return view('pages.admin.manajemen-pic.index');
    }
}
