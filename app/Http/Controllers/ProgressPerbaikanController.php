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
            $laporan->status_lct !== 'approved_permanent' &&
            $laporan->status_lct !== 'closed'
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
            Log::info('Mulai proses approval', [
                'laporan_id' => $laporan->id_laporan_lct,
                'tingkat_bahaya' => $laporan->tingkat_bahaya,
                'status_awal' => $laporan->status_lct
            ]);
        
            // Tentukan status berdasarkan tingkat bahaya
            switch ($laporan->tingkat_bahaya) {
                case 'Low':
                    Log::info('Tingkat bahaya: Low - langsung approved');
                    $laporan->status_lct = 'approved';
                    $statusLog = 'approved';
                    break;
        
                case 'Medium':
                case 'High':
                    Log::info('Tingkat bahaya: Medium/High - pengecekan status saat ini');
        
                    if (in_array($laporan->status_lct, ['waiting_approval_temporary', 'temporary_revision'])) {
                        Log::info('Status sekarang: waiting_approval_temporary / temporary_revision');
                        $laporan->status_lct = 'approved_temporary';
                        $statusLog = 'approved_temporary';
                        $laporan->approved_temporary_by_ehs = true;
        
                    } elseif ($laporan->status_lct == 'waiting_approval_taskbudget') {
                        Log::info('Status sekarang: waiting_approval_taskbudget');
                        $laporan->status_lct = 'waiting_approval_taskbudget';
                        $statusLog = 'approved_temporary';
                        $laporan->approved_temporary_by_ehs = true;
        
                    } elseif ($laporan->status_lct == 'taskbudget_revision') {
                        Log::info('Status sekarang: taskbudget_revision');
                        $laporan->status_lct = 'taskbudget_revision';
                        $statusLog = 'approved_temporary';
                        $laporan->approved_temporary_by_ehs = true;
        
                    } elseif ($laporan->status_lct == 'approved_taskbudget') {
                        Log::info('Status sekarang: approved_taskbudget');
                        $laporan->status_lct = 'approved_taskbudget';
                        $statusLog = 'approved_temporary';
                        $laporan->approved_temporary_by_ehs = true;
        
                    } elseif ($laporan->status_lct == 'waiting_approval_permanent') {
                        Log::info('Status sekarang: waiting_approval_permanent - akan disetujui permanen');
                        $laporan->status_lct = 'approved_permanent';
                        $laporan->date_completion = Carbon::now();
                        $statusLog = 'approved_permanent';
        
                    } else {
                        Log::warning('Status tidak valid untuk tingkat bahaya ini', [
                            'status' => $laporan->status_lct
                        ]);
                        return redirect()->back()->with('error', 'Status tidak valid untuk tingkat bahaya tersebut.');
                    }
                    break;
        
                default:
                    Log::error('Tingkat bahaya tidak valid', [
                        'tingkat_bahaya' => $laporan->tingkat_bahaya
                    ]);
                    return redirect()->back()->with('error', 'Tingkat bahaya tidak valid.');
            }
        
            Log::info('Sebelum simpan status laporan', ['status_baru' => $laporan->status_lct]);
            $laporan->save();
            Log::info('Setelah simpan status laporan', ['status_baru_aktual' => $laporan->fresh()->status_lct]);
        
            // Simpan ke tabel log (RejectLaporan) sebagai histori status
            RejectLaporan::create([
                'id_laporan_lct' => $laporan->id_laporan_lct,
                'user_id' => $user->id,
                'role' => $roleName,
                'status_lct' => $statusLog,
                'alasan_reject' => null,
                'tipe_reject' => null,
            ]);
            Log::info('Log status tersimpan di tabel reject_laporan', ['status_log' => $statusLog]);
        
            DB::commit();
            Log::info('Proses approval selesai dan transaksi dikomit.');
        
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
            // try {
            //     Mail::to('efdika1102@gmail.com')->send(new LaporanRevisiToPic($laporan,$alasanRevisi));
            //     Log::info('Email berhasil dikirim.');
            // } catch (\Exception $mailException) {
            //     Log::error('Gagal mengirim email', ['error' => $mailException->getMessage()]);
            //     return redirect()->back()->with('error', 'Email gagal dikirim. Namun data sudah tersimpan.');
            // }

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
        
        Log::info('Sebelum simpan', ['status' => $laporan->status_lct]);
        $laporan->save();
        Log::info('sesudah simpan', ['status' => $laporan->status_lct]);

        RejectLaporan::create([
            'id_laporan_lct' => $laporan->id_laporan_lct,
            'user_id' => $user->id,
            'role' => $roleName,
            'status_lct' => 'closed',
            'alasan_reject' => null,
            'tipe_reject' => null,
        ]);

        // try {
        //     Mail::to('efdika1102@gmail.com')->send(new CloseNotification($laporan));
        //     Log::info('Email berhasil dikirim.');
        // } catch (\Exception $mailException) {
        //     Log::error('Gagal mengirim email', ['error' => $mailException->getMessage()]);
        //     return redirect()->back()->with('error', 'Email gagal dikirim. Namun data sudah tersimpan.');
        // }

        return redirect()->back()->with('closed', 'The repair report has been successfully approved.');
    }

    public function history($id_laporan_lct)
    {
        $history = RejectLaporan::with('laporanLct','user')->where('id_laporan_lct', $id_laporan_lct)->get();

        return view('pages.admin.history.index', compact('history','id_laporan_lct'));
    }
    

}
