<?php

namespace App\Http\Controllers;

use App\Models\LaporanLct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class LaporanPerbaikanLctController extends Controller
{
    public function index()
    {
        return view('pages.admin.manajemen-lct.index');
    }

    public function show($id_laporan_lct)
    {
        $laporan = LaporanLct::with('user')->where('id_laporan_lct', $id_laporan_lct)->first();

    // dd($laporan ? $laporan->toArray() : 'Laporan tidak ditemukan');
       
        if (auth()->user()->role === 'pic' && auth()->user()->id === $laporan->pic_id) {
            // Debugging: Cek status sebelum update
            if ($laporan->status_lct === 'in_progress') {
                // Update status ke 'progress_work'
                $laporan->update(['status_lct' => 'progress_work']);
                dd('masuk sini');
                // Debugging: Cek apakah status sudah berubah
                if ($laporan->wasChanged('status_lct')) {
                    Log::info("Status laporan {$laporan->id_laporan_lct} diubah menjadi progress_work oleh PIC ID: " . auth()->user()->id);
                } else {
                    Log::warning("Gagal mengubah status laporan {$laporan->id_laporan_lct} oleh PIC ID: " . auth()->user()->id);
                }
            }
        }

        return view('pages.admin.manajemen-lct.show', compact('laporan'));
    }


}
