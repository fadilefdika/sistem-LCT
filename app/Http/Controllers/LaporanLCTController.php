<?php

namespace App\Http\Controllers;

use App\Models\Pic;
use App\Models\User;

use App\Models\LaporanLct;
use Illuminate\Http\Request;
use App\Models\LctDepartement;
use App\Models\LctDepartemenPic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\AssignToPicRequest;
use App\Http\Requests\StoreLaporanRequest;

class LaporanLctController extends Controller
{
    public function index()
    {
        return view('pages.admin.laporan-lct.index');
    }

    //untuk di show detail laporan lct tampilan ehs 
    public function show($id_laporan_lct)
    {
        $laporan = LaporanLct::with('user')->where('id_laporan_lct', $id_laporan_lct)->firstOrFail();
        $departemen = LctDepartement::all();
        $picDepartemen=LctDepartemenPic::with(['departemen','pic.user'])->get();

        return view('pages.admin.laporan-lct.show', compact('laporan', 'departemen', 'picDepartemen'));
    }


    //laporan dari user ke ehs 
    public function store(StoreLaporanRequest $request) 
    { 
        try {
            DB::beginTransaction(); // Mulai transaksi

            // Ambil user yang sedang login
            $user = Auth::user();

            // Buat ID unik untuk laporan
            $idLCT = LaporanLct::generateLCTId();

            // Simpan gambar hanya jika diunggah
            $buktiFotoPath = null;
            if ($request->hasFile('bukti_temuan')) {
                $file = $request->file('bukti_temuan');

                // Nama file unik (pakai timestamp agar tidak menimpa file lain)
                $filename = 'bukti_' . $idLCT . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Simpan file di storage
                $buktiFotoPath = Storage::putFileAs('bukti_temuan', $file, $filename);
            }

            // Simpan data ke database
            LaporanLct::create([
                'id_laporan_lct' => $idLCT,
                'user_id' => $user->id,
                'tanggal_temuan' => $request->tanggal_temuan,
                'area' => $request->area,
                'detail_area' => $request->detail_area,
                'kategori_temuan' => $request->kategori_temuan,
                'temuan_ketidaksesuaian' => $request->temuan_ketidaksesuaian,
                'rekomendasi_safety' => $request->rekomendasi_safety,
                'bukti_temuan' => $buktiFotoPath, // Bisa null
                'status_lct' => 'open', // Status default ke open
                'role_last_updated' => 'user',
            ]);

            DB::commit(); // Simpan perubahan
            return redirect()->back()->with('success', 'Laporan berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan jika ada error

            Log::error('Gagal menyimpan laporan LCT: ' . $e->getMessage()); // Logging error

            return redirect()->back()->with('error', 'Terjadi kesalahan, silakan coba lagi.');
        }
    }

    // dari ehs ke pic
    public function assignToPic(AssignToPicRequest $request, $id_laporan_lct)
    {
        try {
            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->firstOrFail();
            
            $laporan->update([
                'pic_id' => $request->pic_id,
                'departemen_id' => $request->departemen_id,
                'tingkat_bahaya' => $request->tingkat_bahaya,
                'rekomendasi' => $request->rekomendasi,
                'due_date' => $request->due_date,
                'status_lct' => 'in_progress',
                'visibility_role' => json_encode(['ehs', 'pic']),
                'role_last_updated' => 'ehs',
            ]);
            
            // dd("siap dikirim");
            return redirect()->back()->with('success', 'Laporan berhasil dikirim ke PIC.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Gagal assign laporan ke PIC: Laporan tidak ditemukan', ['id_laporan_lct' => $id_laporan_lct]);
            return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Gagal assign laporan ke PIC: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengirim laporan.');
        }
    }

    public function approveOrReject(Request $request, $id)
    {
        $laporan = LaporanLct::findOrFail($id);
        
        if ($request->status == 'approved') {
            $laporan->update([
                'status_lct' => 'closed',
                'visibility_role' => json_encode(['user', 'ehs', 'pic']), // Semua bisa lihat
                'role_last_updated' => 'ehs'
            ]);
            return redirect()->back()->with('success', 'Laporan disetujui dan ditutup.');
        } else {
            $laporan->update([
                'status_lct' => 'rejected',
                'visibility_role' => json_encode(['ehs', 'pic']), // PIC masih bisa lihat untuk revisi
                'catatan_ehs' => $request->catatan_ehs,
                'role_last_updated' => 'ehs'
            ]);
            return redirect()->back()->with('error', 'Laporan dikembalikan ke PIC untuk revisi.');
        }
    }


}
