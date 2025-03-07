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
use Illuminate\Contracts\Cache\Store;

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
        $bukti_temuan = collect(json_decode($laporan->bukti_temuan, true))->map(function ($path) {
            return asset('storage/' . $path);
        });

        return view('pages.admin.laporan-lct.show', compact('laporan', 'departemen', 'picDepartemen', 'bukti_temuan'));
    }


    //laporan dari user ke ehs 
    public function store(StoreLaporanRequest $request) 
    { 
        // dd($request->all(), $request->file());
        
        try {
            DB::beginTransaction(); // Mulai transaksi
            
            // Ambil user yang sedang login
            $user = Auth::user();

            // Buat ID unik untuk laporan
            $idLCT = LaporanLct::generateLCTId();
            
            // Simpan gambar ke storage public
            $buktiFotoPaths = [];
            if ($request->hasFile('bukti_temuan')) {
                foreach ($request->file('bukti_temuan') as $file) {
                    // Nama file unik
                    $filename = 'bukti_' . $idLCT . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                    // Simpan file ke storage/public/bukti_temuan/
                    $path = $file->storeAs('public/bukti_temuan', $filename);

                    // Simpan path gambar ke array
                    $buktiFotoPaths[] = str_replace('public/', '', $path); // Simpan tanpa 'public/'
                }
            }

            // Simpan data ke database
            $laporan = LaporanLct::create([
                'id_laporan_lct' => $idLCT,
                'user_id' => $user->id,
                'tanggal_temuan' => $request->tanggal_temuan,
                'area' => $request->area,
                'detail_area' => $request->detail_area,
                'kategori_temuan' => $request->kategori_temuan,
                'temuan_ketidaksesuaian' => $request->temuan_ketidaksesuaian,
                'rekomendasi_safety' => $request->rekomendasi_safety,
                'bukti_temuan' => json_encode($buktiFotoPaths), // Simpan sebagai JSON
                'status_lct' => 'open',
            ]);

            DB::commit(); // Simpan perubahan
            return redirect()->back()->with('success', 'Laporan berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan jika ada error
            dd($e->getMessage());
            Log::error('Gagal menyimpan laporan LCT: ' . $e->getMessage()); // Logging error
            
            return redirect()->back()->with('error', 'Terjadi kesalahan, silakan coba lagi.');
        }
    }

    // dari ehs ke pic
    public function assignToPic(AssignToPicRequest $request, $id_laporan_lct)
    {
        dd("masuk sini");
        try {

            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->first();

            if (!$laporan) {
                abort(404, 'Data laporan tidak ditemukan');
            }


            $laporan->update([
                'pic_id' => $request->pic_id,
                'departemen_id' => $request->departemen_id,
                'tingkat_bahaya' => $request->tingkat_bahaya,
                'rekomendasi' => $request->rekomendasi,
                'due_date' => $request->due_date,
                'status_lct' => 'in_progress',
            ]);
            
            return redirect()->route('admin.progress-perbaikan')->with('success', 'Laporan berhasil dikirim ke PIC.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // dd($e);
            return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
        } catch (\Exception $e) {
            // dd($e);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengirim laporan.');
        }
    }


}
