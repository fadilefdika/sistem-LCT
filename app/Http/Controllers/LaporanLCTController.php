<?php

namespace App\Http\Controllers;

use App\Models\Pic;
use App\Models\User;

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
        $departemen = LctDepartement::all()->map(fn($d) => [
            'id' => $d->id,
            'nama' => $d->nama_departemen,
        ])->toArray();
        $picDepartemen=LctDepartemenPic::with(['departemen','pic.user'])->get();
        $bukti_temuan = collect(json_decode($laporan->bukti_temuan, true))->map(function ($path) {
            return asset('storage/' . $path);
        });

        // dd($departemen);

        return view('pages.admin.laporan-lct.show', compact('laporan', 'departemen', 'picDepartemen', 'bukti_temuan'));
    }


    //laporan dari user ke ehs 
    public function store(StoreLaporanRequest $request) 
    { 
        // dd($request->all(), $request->files->all());
        
        try {
            DB::beginTransaction(); // Mulai transaksi
            // dd("masuk sini");
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
    public function assignToPic(Request $request, $id_laporan_lct)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
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
                abort(404, 'Data laporan tidak ditemukan');
            }

            // dd($request->all());
            // dd($laporan);
            $laporan->update([
                'pic_id' => $request->pic_id,
                'departemen_id' => $request->departemen_id,
                'tingkat_bahaya' => $request->tingkat_bahaya,
                'rekomendasi' => $request->rekomendasi,
                'due_date' => $request->due_date,
                'status_lct' => 'in_progress',
            ]);
            // dd("masuk sini");
            
            DB::commit();

            // $pic = Pic::find($request->pic_id); // Ambil data PIC berdasarkan ID
            // if ($pic && $pic->email) {
            //     Mail::to($pic->email)->send(new LaporanDikirimKePic($laporan));
            // }
            
            Mail::to('efdika1102@gmail.com')->queue(new LaporanDikirimKePic($laporan));

            return redirect()->route('admin.progress-perbaikan')->with('success', 'Laporan berhasil dikirim ke PIC.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            dd($e);
            return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengirim laporan.');
        }
    }


    // public function kirimEmail()
    // {
    //     $laporan = [
    //         'judul' => 'Ketidaksesuaian Mesin Produksi',
    //         'deskripsi' => 'Ada masalah pada mesin produksi yang perlu segera diperbaiki.',
    //         'tanggal' => now()->format('d-m-Y'),
    //         'url' => url('/laporan/123'), // Ganti dengan link laporan yang benar
    //     ];

    //     Mail::to('efdika1102@gmail.com')->send(new LaporanKetidaksesuaian($laporan));

    //     return "Email laporan ketidaksesuaian berhasil dikirim!";
    // }
}
