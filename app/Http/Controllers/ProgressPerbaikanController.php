<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\LaporanLct;
use Illuminate\Http\Request;
use App\Models\RejectLaporan;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProgressPerbaikanController extends Controller
{
    
    public function index()
    {
        return view('pages.admin.progress-perbaikan.index');
    }

    public function show($id_laporan_lct)
    {
        $laporan = LaporanLct::with(['user', 'picUser', 'rejectLaporan'])
            ->where('id_laporan_lct', $id_laporan_lct)
            ->first();

        if (!$laporan) {
            return abort(404, 'Laporan tidak ditemukan');
        }
        

        return view('pages.admin.progress-perbaikan.show', compact('laporan'));
    }


    public function approveLaporan($id_laporan_lct)
    {
        $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->first();

        if (!$laporan) {
            return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
        }

        $laporan->status_lct = 'approved';
        $laporan->save();

        return redirect()->back()->with('approve', 'Laporan perbaikan berhasil diapprove.');
    }




    public function rejectLaporan(Request $request, $id_laporan_lct)
    {
        $request->validate([
            'alasan_reject' => 'required|string|max:255',
        ]);
    
        DB::beginTransaction(); // Mulai transaksi database
        try {
            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->first();
            if (!$laporan) {
                return response()->json(['error' => 'Laporan tidak ditemukan'], 404);
            }
    
            $laporan->status_lct = 'rejected';
            $laporan->save();
    
            // Simpan alasan ke tabel lct_laporan_reject
            RejectLaporan::create([
                'id_laporan_lct' => $id_laporan_lct,
                'alasan_reject' => $request->alasan_reject,
            ]);
    
            DB::commit(); // ✅ Simpan perubahan ke database sebelum redirect
    
            // dd("sebelum redirect"); // ✅ Cek apakah kode sampai sini
    
            return redirect()->back()->with('reject', 'Laporan revisi berhasil dikirim ke PIC.');
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua perubahan jika ada error
            return response()->json(['error' => 'Terjadi kesalahan saat menolak laporan.', 'message' => $e->getMessage()], 500);
        }
    }
    

    public function closeLaporan($id_laporan_lct)
    {
        // dd("masuk close");
        $laporan = LaporanLct::where('id_laporan_lct',$id_laporan_lct)->first();

        if (!$laporan) {
            return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
        }
        $laporan->status_lct = 'closed';
        $laporan->save();

        return redirect()->route('admin.progress-perbaikan')->with('closed', 'Laporan perbaikan berhasil diapprove.');
    }


}
