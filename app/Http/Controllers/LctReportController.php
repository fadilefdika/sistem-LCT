<?php

namespace App\Http\Controllers;

use App\Models\Pic;
use App\Models\User;

use App\Models\AreaLct;
use App\Models\Kategori;
use App\Models\LaporanLct;
use Illuminate\Http\Request;
use App\Models\RejectLaporan;
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
        // Cek apakah pengguna menggunakan guard 'ehs' atau 'web' untuk pengguna biasa
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $roleName = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            // Ambil dari session terlebih dahulu, fallback ke relasi jika tidak ada
            $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }
        
        
        $laporan = LaporanLct::with('user','kategori','area')->where('id_laporan_lct', $id_laporan_lct)->firstOrFail();
        $kategori = Kategori::all();
        $departemen = LctDepartement::all()->map(fn($d) => [
            'id' => $d->id,
            'nama' => $d->nama_departemen,
        ])->toArray();
        $picDepartemen=LctDepartemenPic::with(['departemen','pic.user'])->get();
        $bukti_temuan = collect(json_decode($laporan->bukti_temuan, true))->map(function ($path) {
            return asset('storage/' . $path);
        });

        // Cek apakah role adalah EHS dan apakah EHS pertama kali melihat laporan
        if ($roleName === 'ehs' && !$laporan->first_viewed_by_ehs_at) {
            // Catat waktu pertama kali EHS melihat laporan
            $laporan->update(['first_viewed_by_ehs_at' => now()]);

            // Log pengiriman laporan ke EHS (sebagai contoh: pertama kali dilihat)
            RejectLaporan::create([
                'id_laporan_lct' => $laporan->id_laporan_lct,
                'user_id' => $user->id,
                'role' => $roleName,
                'status_lct' => 'open_ehs',  
                'alasan_reject' => null,  
                'tipe_reject' => null,  
            ]);
        }


        return view('pages.admin.laporan-lct.show', compact('laporan', 'departemen', 'picDepartemen', 'bukti_temuan', 'kategori'));
    }

    //laporan dari user ke ehs 
    public function store(StoreLaporanRequest $request) 
    { 
        try {
            DB::beginTransaction(); // Mulai transaksi
    
           // Cek apakah pengguna menggunakan guard 'ehs' atau 'web' untuk pengguna biasa
           if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $roleName = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            // Ambil dari session terlebih dahulu, fallback ke relasi jika tidak ada
            $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }
        

            // Buat ID unik untuk laporan
            $idLCT = LaporanLct::generateLCTId();
    
            // Konversi kategori temuan ke kategori_id
            $kategori = Kategori::where('nama_kategori', $request->kategori_temuan)->first();
            if (!$kategori) {
                return redirect()->back()->with('error', 'Kategori is not valid.');
            }
    
            $area = AreaLct::where('nama_area',$request->area)->first();
            if(!$area){
                return redirect()->back()->with('error', 'Area is not valid.');
            }
    
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
            $laporan = LaporanLct::create([
                'id_laporan_lct' => $idLCT,
                'user_id' => $user->id,
                'tanggal_temuan' => $request->tanggal_temuan,
                'area_id' => $area->id,
                'detail_area' => $request->detail_area,
                'kategori_id' => $kategori->id,
                'temuan_ketidaksesuaian' => $request->temuan_ketidaksesuaian,
                'rekomendasi_safety' => $request->rekomendasi_safety,
                'bukti_temuan' => json_encode($buktiFotoPaths),
                'status_lct' => 'open',  // Status pertama adalah "open"
            ]);
            
            $laporan->load('user', 'kategori');
            
            // Kirim email ke EHS
            // try {
            //     Mail::to('efdika1102@gmail.com')->send(new LaporanKetidaksesuaian($laporan));
            //     Log::info('Email berhasil dikirim.');
            // } catch (\Exception $mailException) {
            //     Log::error('Gagal mengirim email', ['error' => $mailException->getMessage()]);
            //     return redirect()->back()->with('error', 'Email gagal dikirim. Namun data sudah tersimpan.');
            // }
            
    
            // Log pengiriman laporan ke EHS
            RejectLaporan::create([
                'id_laporan_lct' => $laporan->id_laporan_lct,
                'user_id' => $user->id,
                'role' => $roleName,  // Role user yang mengirimkan laporan
                'status_lct' => 'open',  // Status saat laporan baru dikirim
                'alasan_reject' => null,  // Tidak ada alasan reject pada tahap ini
                'tipe_reject' => null,  // Mengindikasikan ini adalah pengiriman ke EHS
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
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $roleName = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            // Ambil dari session terlebih dahulu, fallback ke relasi jika tidak ada
            $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }
        // Validasi request
        $validator = Validator::make($request->all(), [
            'temuan_ketidaksesuaian' => 'required|string|max:255',
            'kategori_temuan' => 'required|string|max:255',
            'departemen_id' => 'required|integer|exists:lct_departemen,id',
            'pic_id' => 'required|integer|exists:lct_pic,id',
            'tingkat_bahaya' => 'required|in:Low,Medium,High',
            'rekomendasi' => 'required|string|max:255',
            'due_date' => 'required|date|after_or_equal:today',
            'due_date_temp' => 'nullable|date|after_or_equal:today',
            'due_date_perm' => 'nullable|date|after_or_equal:today',
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
                'approved_temporary_by_ehs' => 'not yet',
                'due_date_temp' => $request->due_date_temp,
                'due_date_perm' => $request->due_date_perm,
                'status_lct' => 'in_progress',
            ]);
            
            
            RejectLaporan::create([
                'id_laporan_lct' => $laporan->id_laporan_lct,
                'user_id' => $user->id,
                'role' => $roleName,  // Role user yang mengirimkan laporan
                'status_lct' => 'in_progress',  // Status saat laporan baru dikirim
                'alasan_reject' => null,  // Tidak ada alasan reject pada tahap ini
                'tipe_reject' => null,  // Mengindikasikan ini adalah pengiriman ke EHS
            ]);
            DB::commit();
            
            $laporan->load('picUser');
            
            // try {
            //     Mail::to('efdika1102@gmail.com')->send(new LaporanDikirimKePic($laporan));
            //     Log::info('Email berhasil dikirim.');
            // } catch (\Exception $mailException) {
            //     Log::error('Gagal mengirim email', ['error' => $mailException->getMessage()]);
            //     return redirect()->back()->with('error', 'Email gagal dikirim. Namun data sudah tersimpan.');
            // }
            return redirect()->route('ehs.reporting.index')->with('success', 'The report has been successfully submitted to the PIC.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            
            return redirect()->back()->with('error', 'Report not found.', $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while submitting the report.', $e->getMessage());
        }
    }

    public function closed(Request $request, $id_laporan_lct)
    {
        $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->firstOrFail();
        $laporan->status_lct = 'closed';
        $laporan->date_closed = now();
        $laporan->catatan_ehs = "This report has been closed because another similar report has been processed first.";
        $laporan->update();
    
        return response()->json(['success' => true]);

    }
    
}
