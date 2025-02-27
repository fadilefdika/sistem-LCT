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

    public function show($id_laporan_lct)
    {
        $laporan = LaporanLct::with('user','picUser')->where('id_laporan_lct', $id_laporan_lct)->first();

        if (!$laporan) {
            return abort(404, 'Laporan tidak ditemukan');
        }

        return view('pages.admin.progress-perbaikan.show', compact('laporan'));
    }

}
