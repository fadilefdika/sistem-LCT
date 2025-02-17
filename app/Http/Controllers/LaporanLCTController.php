<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaporanLCTController extends Controller
{
    public function index()
    {
        return view('pages.admin.laporan-lct.index');
    }

    public function detail()
    {
        return view('pages.admin.laporan-lct.detail');
    }
}
