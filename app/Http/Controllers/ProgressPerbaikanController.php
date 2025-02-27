<?php

namespace App\Http\Controllers;

use App\Models\LaporanLct;
use Illuminate\Http\Request;

class ProgressPerbaikanController extends Controller
{
    
    public function index()
    {
        return view('pages.admin.progress-perbaikan.index');
    }

    public function detail()
    {
        // $laporan = LaporanLct::with('user','picUser')->where('id_laporan_lct', $id_laporan_lct)->first();
        // // dd($laporan);


        return view('pages.admin.progress-perbaikan.detail');
    }
}
