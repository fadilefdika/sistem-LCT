<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RiwayatLCTController extends Controller
{
    public function index()
    {
        return view('pages.admin.riwayat-lct.index');
    }
}
