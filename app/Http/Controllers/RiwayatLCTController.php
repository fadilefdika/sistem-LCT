<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanLct;

class RiwayatLctController extends Controller
{
    public function index()
    {
        return view('pages.admin.riwayat-lct.index');
    }

    public function show($id_laporan_lct){
        $laporan = LaporanLct::with(['user', 'picUser', 'rejectLaporan', 'kategori'])
            ->where('id_laporan_lct', $id_laporan_lct)
            ->first();
        $bukti_temuan = collect(json_decode($laporan->bukti_temuan, true))->map(function ($path) {
            return asset('storage/' . $path);
        });    

        $bukti_perbaikan = collect(json_decode($laporan->bukti_perbaikan, true))->map(function ($path) {
            return asset('storage/' . $path);
        });   

        if (!$laporan) {
            return abort(404, 'Laporan tidak ditemukan');
        }

        return view('pages.admin.riwayat-lct.show', compact('laporan', 'bukti_temuan', 'bukti_perbaikan'));
    }
}

