<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaporanPerbaikanLctController extends Controller
{
    public function index()
    {
        return view('pages.admin.manajemen-lct.index');
    }

    public function detail()
    {
        return view('pages.admin.manajemen-lct.detail');
    }
}
