<?php

namespace App\Http\Controllers;

use App\Models\Pic;
use App\Models\User;

use App\Models\Kategori;
use App\Models\LaporanLct;
use Illuminate\Http\Request;
use App\Models\LctDepartement;
use PhpParser\Node\Expr\Assign;
use App\Models\LctDepartemenPic;
use App\Mail\LaporanDikirimKePic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Mail\LaporanKetidaksesuaian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\AssignToPicRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreLaporanRequest;

class LctReportController extends Controller
{
    public function index()
    {
        return view('pages.admin.laporan-lct.index');
    }

    //untuk di show detail laporan lct tampilan ehs 
    public function show($id_laporan_lct)
    {
        $laporan = LaporanLct::with('user','kategori')->where('id_laporan_lct', $id_laporan_lct)->firstOrFail();
        $kategori = Kategori::all();
        $departemen = LctDepartement::all()->map(fn($d) => [
            'id' => $d->id,
            'nama' => $d->nama_departemen,
        ])->toArray();
        $picDepartemen=LctDepartemenPic::with(['departemen','pic.user'])->get();
        $bukti_temuan = collect(json_decode($laporan->bukti_temuan, true))->map(function ($path) {
            return asset('storage/' . $path);
        });

        // dd($departemen);

        return view('pages.admin.laporan-lct.show', compact('laporan', 'departemen', 'picDepartemen', 'bukti_temuan', 'kategori'));
    }


    //laporan dari user ke ehs 
    public function store(StoreLaporanRequest $request) 
    { 
        // dd($request->all(), $request->files->all());
        
        try {
            DB::beginTransaction(); // Mulai transaksi
    
            // Ambil user yang sedang login
            $user = Auth::user();
    
            // Buat ID unik untuk laporan
            $idLCT = LaporanLct::generateLCTId();
    
            // Konversi kategori temuan ke kategori_id
            $kategori = Kategori::where('nama_kategori', $request->kategori_temuan)->first();
            if (!$kategori) {
                return redirect()->back()->with('error', 'Kategori tidak valid.');
            }

            // dd($kategori->id);
    
            // Simpan gambar ke storage public
            $buktiFotoPaths = [];
            if ($request->hasFile('bukti_temuan')) {
                foreach ($request->file('bukti_temuan') as $file) {
                    // Nama file unik
                    $filename = 'bukti_' . $idLCT . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
    
                    // Simpan file ke storage/app/public/bukti_temuan/
                    $path = $file->storeAs('bukti_temuan', $filename, 'public');
    
                    // Simpan path gambar ke array
                    $buktiFotoPaths[] = $path;
                }
            }
    
            // Simpan data ke database
            LaporanLct::create([
                'id_laporan_lct' => $idLCT,
                'user_id' => $user->id,
                'tanggal_temuan' => $request->tanggal_temuan,
                'area' => $request->area,
                'detail_area' => $request->detail_area,
                'kategori_id' => $kategori->id, // Simpan ID kategori, bukan nama
                'temuan_ketidaksesuaian' => $request->temuan_ketidaksesuaian,
                'rekomendasi_safety' => $request->rekomendasi_safety,
                'bukti_temuan' => json_encode($buktiFotoPaths), // Simpan sebagai JSON
                'status_lct' => 'open',
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
    public function assignToPic(Request $request, $id_laporan_lct)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'temuan_ketidaksesuaian' => 'required|string|max:255',
            'kategori_temuan' => 'required|string|max:255',
            'departemen_id' => 'required|integer|exists:lct_departemen,id',
            'pic_id' => 'required|integer|exists:lct_pic,id',
            'tingkat_bahaya' => 'required|in:Low,Medium,High',
            'rekomendasi' => 'required|string|max:255',
            'due_date' => 'required|date|after_or_equal:today',
        ]);
        
        try {
            DB::beginTransaction();

            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->first();
            if (!$laporan) {
                abort(404, 'Report data not found');
            }

            
            $laporan->update([
                'temuan_ketidaksesuaian' => $request->temuan_ketidaksesuaian,
                'kategori_temuan' => $request->kategori_temuan,
                'pic_id' => $request->pic_id,
                'departemen_id' => $request->departemen_id,
                'tingkat_bahaya' => $request->tingkat_bahaya,
                'rekomendasi' => $request->rekomendasi,
                'due_date' => $request->due_date,
                'status_lct' => 'in_progress',
            ]);
            // dd("masuk sini");
            
            DB::commit();
            
            Mail::to('efdika1102@gmail.com')->queue(new LaporanDikirimKePic($laporan));

            return redirect()->route('admin.progress-perbaikan')->with('success', 'The report has been successfully submitted to the PIC.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            // dd($e);
            return redirect()->back()->with('error', 'Report not found.', $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e);
            return redirect()->back()->with('error', 'An error occurred while submitting the report.', $e->getMessage());
        }
    }
}
