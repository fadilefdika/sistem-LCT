<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaporanPerbaikanLCTController extends Controller
{
    public function index()
    {
        return view('pages.admin.laporan-perbaikan-lct.index');
    }

    public function detail()
    {
        return view('pages.admin.laporan-perbaikan-lct.detail');
    }
}
