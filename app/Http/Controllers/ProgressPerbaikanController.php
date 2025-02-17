<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProgressPerbaikanController extends Controller
{
    
    public function index()
    {
        return view('pages.admin.progress-perbaikan.index');
    }

    public function detail()
    {
        return view('pages.admin.progress-perbaikan.detail');
    }
}
