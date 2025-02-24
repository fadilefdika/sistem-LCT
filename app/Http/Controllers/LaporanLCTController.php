<?php

namespace App\Http\Controllers;

use App\Models\PIC;
use App\Models\User;

use App\Models\LaporanLCT;
use Illuminate\Http\Request;
use App\Models\LCTDepartement;
use App\Http\Controllers\Controller;
use App\Models\LCTDepartemenPIC;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class LaporanLCTController extends Controller
{
    public function index()
    {
        return view('pages.admin.laporan-lct.index');
    }

    //untuk di show detail laporan lct tampilan ehs 
    public function show($id_laporan_lct)
    {
        $laporan = LaporanLCT::with('user')->where('id_laporan_lct', $id_laporan_lct)->firstOrFail();
        $departemen = LctDepartement::all();
        $picDepartemen=LCTDepartemenPIC::with(['departemen','pic.user'])->get();

        return view('pages.admin.laporan-lct.show', compact('laporan', 'departemen', 'picDepartemen'));
    }


    //laporan dari user ke ehs 
    public function store(Request $request) 
    { 
            try{
                // Ambil user yang sedang login
            $user = Auth::user();
    
            // Validasi input (tanpa nama & no_npk karena otomatis diambil)
            $validatedData = $request->validate([
                'tanggal_temuan' => 'required|date',
                'area' => 'required|string|max:255',
                'detail_area' => 'required|string|max:255',
                'kategori_temuan' => 'required|string|max:255',
                'temuan_ketidaksesuaian' => 'required|string',
                'rekomendasi_safety' => 'required|string',
            ]);

            // Buat ID unik untuk laporan
            $idLCT = LaporanLCT::generateLCTId();
    
            // Simpan gambar hanya jika ada
            $buktiFotoPath = null;
            if ($request->hasFile('bukti_temuan')) {
                $file = $request->file('bukti_temuan');
    
                // Buat nama file unik berdasarkan ID laporan
                $filename = 'bukti_' . $idLCT . '.' . $file->getClientOriginalExtension();
    
                // Simpan file dengan nama yang lebih bermakna
                $buktiFotoPath = $file->storeAs('bukti_temuan', $filename, 'public');
            }
    
            // Simpan data ke database
            LaporanLCT::create([
                'id_laporan_lct' => $idLCT,
                'user_id' => $user->id, // Simpan user_id saja
                'tanggal_temuan' => $validatedData['tanggal_temuan'],
                'area' => $validatedData['area'],
                'detail_area' => $validatedData['detail_area'],
                'kategori_temuan' => $validatedData['kategori_temuan'],
                'temuan_ketidaksesuaian' => $validatedData['temuan_ketidaksesuaian'],
                'rekomendasi_safety' => $validatedData['rekomendasi_safety'],
                'bukti_temuan' => $buktiFotoPath, // Menyimpan path bukti temuan jika ada
            ]);

            return redirect()->back()->with('success', 'Laporan berhasil disimpan!');
            }catch (\Exception $e){
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // dari ehs ke pic
    public function assignToPic(Request $request, $id) 
    {
        $request->validate([
            'departemen' => 'required|in:Manufacturing,AME,Purchasing,PPIC,Quality,Maintenance,Product Mechanical Engineering,Process Engineering,OPEX dan PDCA,Accounting,HR,GA EHS',
            'pic_id' => 'required|exists:users,id',
        ]);

        $laporan = LaporanLct::findOrFail($id);

        $laporan->update([
            'pic_id' => $request->pic_id,
            'departemen' => $request->departemen, 
            'status_lct' => 'in_progress',
            'visibility_role' => json_encode(['ehs', 'pic']),
            'role_last_updated' => 'ehs'
        ]);

        return redirect()->back()->with('success', 'Laporan dikirim ke PIC.');
    }



    //laporan dari user ke ehs 
    public function submitPerbaikan(Request $request, $id)
    {
        $laporan = LaporanLct::findOrFail($id);
        
        $laporan->update([
            'status_lct' => 'completed',
            'visibility_role' => json_encode(['ehs']), // Hanya EHS bisa lihat
            'role_last_updated' => 'pic'
        ]);

        return redirect()->back()->with('success', 'Hasil perbaikan dikirim ke EHS.');
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
