<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LaporanLCT;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class LaporanLCTController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $users = User::select(['id', 'fullname', 'email', 'created_at']);

            // dd($users);

            return DataTables::of($users)
                ->addIndexColumn() // Tambahkan nomor urut otomatis
                ->make(true);
        }
        return view('pages.admin.laporan-lct.index');
    }

    public function detail()
    {
        return view('pages.admin.laporan-lct.detail');
    }

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
    
}
