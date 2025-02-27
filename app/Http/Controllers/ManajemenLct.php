<?php

namespace App\Http\Controllers;

use App\Models\LaporanLct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ManajemenLct extends Controller
{
    public function show($id)
    {
        $laporan = LaporanLct::findOrFail($id);

        // Pastikan user yang login adalah PIC yang bertanggung jawab
        if (auth()->user()->role === 'pic' && auth()->user()->id === $laporan->pic_id) {
            // Jika status masih 'in_progress', ubah ke 'progress_work'
            if ($laporan->status_lct === 'in_progress') {
                $laporan->update(['status_lct' => 'progress_work']);
            }
        }

        return view('admin.manajemen-lct.detail', compact('laporan'));
    }

}
