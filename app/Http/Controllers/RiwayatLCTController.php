<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RiwayatLctController extends Controller
{
    public function index()
    {
        return view('pages.admin.riwayat-lct.index');
    }
}
