<?php

namespace App\Http\Controllers;

use App\Models\Pic;
use App\Models\LaporanLct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignToEhsRequest;

class LaporanPerbaikanLctController extends Controller
{
    public function index()
    {
        return view('pages.admin.manajemen-lct.index');
    }

    public function show($id_laporan_lct)
    {
        $laporan = LaporanLct::with('picUser')->where('id_laporan_lct', $id_laporan_lct)->first();

        if($laporan->status_lct === 'in_progress') {
            $laporan->update(['status_lct' => 'progress_work']);
        }

        return view('pages.admin.manajemen-lct.show', compact('laporan'));
    }

    public function store(AssignToEhsRequest $request, $id_laporan_lct)
    {
        // dd("masuk");
        try {
            DB::beginTransaction(); 
            // Cari laporan berdasarkan ID
            $laporan = LaporanLct::where('id_laporan_lct',$id_laporan_lct)->first();

            // dd($laporan);
            // Simpan gambar hanya jika diunggah
            $buktiPerbaikan = $laporan->bukti_perbaikan; // Simpan nilai lama jika tidak ada file baru
            if ($request->hasFile('bukti_perbaikan')) {
                $file = $request->file('bukti_perbaikan');

                // Buat nama file unik
                $filename = 'bukti_' . $id_laporan_lct . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Simpan file di storage
                $buktiPerbaikan = Storage::putFileAs('bukti_perbaikan', $file, $filename);
            }

            // dd([
            //     'date_completion' => $request->date_completion,
            //     'status_lct' => 'waiting_approval',
            //     'bukti_perbaikan' => $buktiPerbaikan,
            // ]);

            // Update laporan dengan data terbaru
            $laporan->update([
                'date_completion' => $request->date_completion,
                'status_lct' => 'waiting_approval',
                'bukti_perbaikan' => $buktiPerbaikan,
            ]);

            DB::commit(); 

            return redirect()->route('admin.manajemen-lct')->with('success', 'Hasil perbaikan telah dikirim ke EHS.');
        } catch (\Exception $e) {
            // dd("gagal");
            DB::rollBack(); // Batalkan transaksi jika ada error

            Log::error('Gagal mengirim hasil perbaikan ke EHS: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan, silakan coba lagi.');
        }
    }


}
