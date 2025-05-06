<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\LaporanLct;
use Illuminate\Http\Request;
use App\Models\RejectLaporan;
use App\Mail\CloseNotification;
use App\Mail\LaporanRevisiToPic;
use App\Mail\ApprovalNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
        $tindakan_perbaikan = collect(json_decode($laporan->tindakan_perbaikan, true))->map(function ($entry) {
            return [
                'tanggal' => $entry['tanggal'],
                'tindakan' => $entry['tindakan'],
                'bukti' => collect($entry['bukti'])->map(fn($path) => asset('storage/' . $path)),
            ];
        });
        // Cek apakah semua task sudah selesai
        $tasks = $laporan->tasks;

        $allTasksCompleted = $tasks->isNotEmpty() && $tasks->every(fn($task) => $task->status === 'completed');

        if (
            $allTasksCompleted &&
            $laporan->status_lct !== 'approved_permanent'
        ) {
            $laporan->status_lct = 'waiting_approval_permanent';
            $laporan->save();
        }


        
        return view('pages.admin.progress-perbaikan.show', compact('laporan', 'bukti_temuan', 'tindakan_perbaikan', 'allTasksCompleted'));
    }


    public function approveLaporan($id_laporan_lct)
    {
        // Cek apakah pengguna menggunakan guard 'ehs' atau 'web' untuk pengguna biasa
        if (Auth::guard('ehs')->check()) {
            // Jika pengguna adalah EHS, ambil role dari relasi 'roles' pada model EhsUser
            $user = Auth::guard('ehs')->user();
            $roleName = optional($user->roles->first())->name;
        } else {
            // Jika pengguna adalah User biasa, ambil role dari relasi 'roleLct' pada model User
            $user = Auth::user();
            $roleName = optional($user->roleLct->first())->name;
        }

        $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->first();

        if (!$laporan) {
            return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
        }

        try {
            DB::beginTransaction();


            // Tentukan status berdasarkan tingkat bahaya
            switch ($laporan->tingkat_bahaya) {
                case 'Low':
                    $laporan->status_lct = 'approved';
                    $statusLog = 'approved';
                    break;

                case 'Medium':
                case 'High':
                    if (in_array($laporan->status_lct, ['waiting_approval_temporary', 'temporary_revision'])) {
                        $laporan->status_lct = 'approved_temporary';
                        $statusLog = 'approved_temporary';
                    } elseif (in_array($laporan->status_lct, ['waiting_approval_permanent'])) {
                        $laporan->status_lct = 'approved_permanent';
                        $laporan->date_completion = Carbon::now();
                        $statusLog = 'approved_permanent';
                        // dd('masuk sini');
                    } else {
                        return redirect()->back()->with('error', 'Status tidak valid untuk tingkat bahaya tersebut.');
                    }
                    break;

                default:
                    return redirect()->back()->with('error', 'Tingkat bahaya tidak valid.');
            }

            Log::info('Sebelum simpan', ['status' => $laporan->status_lct]);
            $laporan->save();
            Log::info('Sesudah simpan', ['status' => $laporan->fresh()->status_lct]);


            // Log history ke tabel reject_laporan (sebagai histori status, bukan hanya penolakan)
            RejectLaporan::create([
                'id_laporan_lct' => $laporan->id_laporan_lct,
                'user_id' => $user->id,
                'role' => $roleName,
                'status_lct' => $statusLog,
                'alasan_reject' => null,
                'tipe_reject' => null,
            ]);

            Mail::to('efdika1102@gmail.com')->queue(new ApprovalNotification($laporan));

            DB::commit();

            return redirect()->back()->with('approve', 'The repair report has been successfully approved.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving EHS report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while approving the report.');
        }
    }


    public function rejectLaporan(Request $request, $id_laporan_lct)
    {
        // Cek apakah pengguna menggunakan guard 'ehs' atau 'web' untuk pengguna biasa
        if (Auth::guard('ehs')->check()) {
            // Jika pengguna adalah EHS, ambil role dari relasi 'roles' pada model EhsUser
            $user = Auth::guard('ehs')->user();
            $roleName = optional($user->roles->first())->name;
        } else {
            // Jika pengguna adalah User biasa, ambil role dari relasi 'roleLct' pada model User
            $user = Auth::user();
            $roleName = optional($user->roleLct->first())->name;
        }

        // Validasi input untuk alasan reject
        $request->validate([
            'alasan_reject' => 'required|string|max:255',
        ]);

        DB::beginTransaction(); // Mulai transaksi database
        try {
            // Cari laporan berdasarkan ID
            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->first();
            if (!$laporan) {
                return response()->json(['error' => 'Laporan tidak ditemukan'], 404);
            }

            // Tentukan status reject berdasarkan tingkat bahaya
            switch ($laporan->tingkat_bahaya) {
                case 'Low':
                    $laporan->status_lct = 'revision';
                    break;

                case 'Medium':
                case 'High':
                    if (in_array($laporan->status_lct, ['waiting_approval_temporary', 'temporary_revision'])) {
                        $laporan->status_lct = 'temporary_revision';
                    } elseif (in_array($laporan->status_lct, ['waiting_approval_permanent', 'permanent_revision'])) {
                        $laporan->status_lct = 'permanent_revision';
                    }
                    break;

                default:
                    return response()->json(['error' => 'Tingkat bahaya tidak valid'], 400);
            }

            $laporan->save();

            // Tentukan tipe reject berdasarkan status_lct SEKARANG
            $tipeReject = match ($laporan->status_lct) {
                'revision' => 'lct_perbaikan_low',
                'temporary_revision' => 'lct_perbaikan_temporary',
                default => 'lct_perbaikan_unknown',
            };

            // Simpan alasan ke tabel lct_laporan_reject (log reject)
            RejectLaporan::create([
                'id_laporan_lct' => $id_laporan_lct,
                'user_id' => $user->id,
                'role' => $roleName,
                'status_lct' => $laporan->status_lct,
                'alasan_reject' => $request->alasan_reject,
                'tipe_reject' => $tipeReject, // Menambahkan tipe reject sesuai status
            ]);

            // Ambil alasan revisi terbaru berdasarkan tipe reject
            $alasanRevisi = RejectLaporan::where('id_laporan_lct', $id_laporan_lct)
                ->where('tipe_reject', $tipeReject)
                ->orderBy('created_at', 'desc')
                ->get();

            // Kirim email ke PIC atau user terkait
            Mail::to('efdika1102@gmail.com')->queue(new LaporanRevisiToPic($laporan, $alasanRevisi));

            DB::commit(); // Commit transaksi

            return redirect()->back()->with('reject', 'The revision report has been successfully sent to the PIC.');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback jika ada error
            return response()->json(['error' => 'Terjadi kesalahan saat menolak laporan.', 'message' => $e->getMessage()], 500);
        }
    }

    public function closeLaporan($id_laporan_lct)
    {
        // Cek apakah pengguna menggunakan guard 'ehs' atau 'web' untuk pengguna biasa
        if (Auth::guard('ehs')->check()) {
            // Jika pengguna adalah EHS, ambil role dari relasi 'roles' pada model EhsUser
            $user = Auth::guard('ehs')->user();
            $roleName = optional($user->roles->first())->name;
        } else {
            // Jika pengguna adalah User biasa, ambil role dari relasi 'roleLct' pada model User
            $user = Auth::user();
            $roleName = optional($user->roleLct->first())->name;
        }
        // dd("masuk close");
        $laporan = LaporanLct::where('id_laporan_lct',$id_laporan_lct)->first();

        if (!$laporan) {
            return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
        }
        $laporan->status_lct = 'closed';
        $laporan->date_closed = Carbon::now();
        $laporan->save();

        RejectLaporan::create([
            'id_laporan_lct' => $laporan->id_laporan_lct,
            'user_id' => $user->id,
            'role' => $roleName,
            'status_lct' => 'closed',
            'alasan_reject' => null,
            'tipe_reject' => null,
        ]);

        Mail::to('efdika1102@gmail.com')->queue(new CloseNotification($laporan));

        return redirect()->back()->with('closed', 'The repair report has been successfully approved.');
    }

    public function history($id_laporan_lct)
    {
        $history = RejectLaporan::with('laporanLct','user')->where('id_laporan_lct', $id_laporan_lct)->get();

        return view('pages.admin.history.index', compact('history','id_laporan_lct'));
    }
    

}
