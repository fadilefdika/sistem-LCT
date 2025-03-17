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
        $laporan = LaporanLct::with([
                'user', 'picUser', 'rejectLaporan', 'kategori',
                'tasks' => function ($query) {
                    $query->orderBy('due_date', 'asc'); // Urutkan dari due date terdekat
                }
            ])
            ->where('id_laporan_lct', $id_laporan_lct)
            ->first();

        if (!$laporan) {
            return abort(404, 'Laporan tidak ditemukan');
        }

        // Ambil bukti temuan & perbaikan
        $bukti_temuan = collect(json_decode($laporan->bukti_temuan, true))->map(fn($path) => asset('storage/' . $path));
        $bukti_perbaikan = collect(json_decode($laporan->bukti_perbaikan, true))->map(fn($path) => asset('storage/' . $path));

        // Cek apakah semua task sudah selesai
        $allTasksCompleted = $laporan->tasks->every(fn($task) => $task->status === 'completed');

        return view('pages.admin.progress-perbaikan.show', compact('laporan', 'bukti_temuan', 'bukti_perbaikan', 'allTasksCompleted'));
    }

    public function approveLaporan($id_laporan_lct)
    {
        $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->first();

        if (!$laporan) {
            return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
        }

        // Tentukan status berdasarkan tingkat bahaya
        switch ($laporan->tingkat_bahaya) {
            case 'Low':
                // dd("masuk low");
                $laporan->status_lct = 'approved';
                break;

            case 'Medium':
            case 'High':
                // dd("masuk medium");
                // Jika masih dalam tahap perbaikan sementara, set ke approved_temporary
                if (in_array($laporan->status_lct, ['waiting_approval_temporary', 'temporary_revision'])) {
                    $laporan->status_lct = 'approved_temporary';
                }
                // Jika sudah tahap perbaikan permanen, set ke approved_permanent
                elseif (in_array($laporan->status_lct, ['waiting_approval_permanent', 'permanent_revision', 'approved_taskbudget'])) {
                    $laporan->status_lct = 'approved_permanent';
                    $laporan->date_completion = Carbon::now();
                }
                break;

            default:
                return redirect()->back()->with('error', 'Tingkat bahaya tidak valid.');
        }

        $laporan->save();

        return redirect()->back()->with('approve', 'The repair report has been successfully approved.');
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

            // Tentukan status reject berdasarkan tingkat bahaya
            switch ($laporan->tingkat_bahaya) {
                case 'Low':
                    $laporan->status_lct = 'revision'; // Langsung ke revisi
                    break;

                case 'Medium':
                case 'High':
                    // Jika masih dalam tahap perbaikan sementara, set ke rejected_temporary
                    if (in_array($laporan->status_lct, ['waiting_approval_temporary', 'temporary_revision'])) {
                        $laporan->status_lct = 'temporary_revision';
                    }
                    // Jika sudah tahap perbaikan permanen, set ke rejected_permanent
                    elseif (in_array($laporan->status_lct, ['waiting_approval_permanent', 'permanent_revision'])) {
                        $laporan->status_lct = 'permanent_revision';
                    }
                    break;

                default:
                    return response()->json(['error' => 'Tingkat bahaya tidak valid'], 400);
            }

            $laporan->save();

            // Simpan alasan ke tabel lct_laporan_reject
            RejectLaporan::create([
                'id_laporan_lct' => $id_laporan_lct,
                'alasan_reject' => $request->alasan_reject,
            ]);

            DB::commit(); // ✅ Simpan perubahan ke database sebelum redirect

            return redirect()->back()->with('reject', 'The revision report has been successfully sent to the PIC.');
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

        return redirect()->route('admin.riwayat-lct')->with('closed', 'The repair report has been successfully approved.');
    }


}
