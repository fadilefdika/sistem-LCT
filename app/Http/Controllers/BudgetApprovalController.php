<?php

namespace App\Http\Controllers;

use App\Models\LaporanLct;
use Illuminate\Http\Request;
use App\Models\RejectLaporan;
use App\Mail\RevisiLaporanLCT;
use App\Models\BudgetApproval;
use App\Mail\TaskBudgetApproved;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Mail\TaskBudgetRevisionMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BudgetApprovalController extends Controller
{
    public function index()
    {
        return view('pages.admin.budget-approval.index');
    }

    public function show($id_laporan_lct)
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

        $taskBudget = LaporanLct::with([
            'tasks' => function ($query) {
                $query->orderBy('due_date', 'asc');
            },
            'tasks.pic.user',
            'rejectLaporan'
        ])
        ->where('id_laporan_lct', $id_laporan_lct)
        ->whereIn('status_lct', ['waiting_approval_taskbudget', 'approved_taskbudget', 'taskbudget_revision'])
        ->first();

        // Pastikan data ditemukan
        if (!$taskBudget) {
            abort(404, 'Laporan tidak ditemukan.');
        }

        // Jika role manager dan belum pernah melihat sebelumnya
        if ($roleName === 'manager' && !$taskBudget->first_viewed_by_manager_at) {
            $taskBudget->update(['first_viewed_by_manager_at' => now()]);

            RejectLaporan::create([
                'id_laporan_lct' => $taskBudget->id_laporan_lct,
                'user_id' => $user->id,
                'role' => $roleName,
                'status_lct' => 'open_manager',
                'alasan_reject' => null,
                'tipe_reject' => null,
            ]);
        }

        return view('pages.admin.budget-approval.show', compact('taskBudget'));
    }



    public function showHistory($id_laporan_lct)
    {
        $taskBudget = LaporanLct::with([
            'picUser',
            'tasks' => function ($query) {
                $query->orderBy('due_date', 'asc'); 
            },
            'rejectLaporan'
        ])
        ->where('id_laporan_lct', $id_laporan_lct)
        ->whereIn('status_lct', ['approved_taskbudget', 'closed'])
        ->first();

        return view('pages.admin.budget-approval-history.show', compact('taskBudget'));
    }

    public function approve($id_laporan_lct)
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

        try {
            DB::beginTransaction(); // Mulai transaksi

            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->first();
            $laporan->update(['status_lct' => 'approved_taskbudget']);

            // ✅ Tambahkan log ke history (meskipun tidak reject)
            RejectLaporan::create([
                'id_laporan_lct' => $laporan->id_laporan_lct,
                'user_id'        => $user->id,
                'role'           => $roleName,
                'status_lct'     => 'approved_taskbudget', // Catat status sekarang
                'alasan_reject'  => null,
                'tipe_reject'    => null,
            ]);

            // Kirim email ke user yang bersangkutan\
            // try {
            //     Mail::to('efdika1102@gmail.com')->send(new TaskBudgetApproved($laporan));
            //     Log::info('Email berhasil dikirim.');
            // } catch (\Exception $mailException) {
            //     Log::error('Gagal mengirim email', ['error' => $mailException->getMessage()]);
            //     return redirect()->back()->with('error', 'Email gagal dikirim. Namun data sudah tersimpan.');
            // }
            

            DB::commit();

            return redirect()->route('admin.budget-approval-history.index')
                            ->with('success', 'Budget request has been approved.');
                            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while approving the budget request.');
        }
    }


    public function reject(Request $request, $id_laporan_lct)
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

        // Validasi input untuk memastikan alasan reject tidak kosong
        $request->validate([
            'alasan_reject' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->firstOrFail();

            // Simpan log reject ke tabel reject
            RejectLaporan::create([
                'id_laporan_lct' => $id_laporan_lct,
                'user_id'        => $user->id,
                'role'           => $roleName,
                'alasan_reject'  => $request->alasan_reject,
                'tipe_reject'    => $request->tipe_reject ?? 'budget_approval',
                'status_lct'     => 'taskbudget_revision', // status saat ini untuk histori
            ]);

            // Update status laporan
            $laporan->update([
                'status_lct' => 'taskbudget_revision',
            ]);

            DB::commit();

            // Ambil alasan reject terbaru
            $alasanReject = RejectLaporan::where('id_laporan_lct', $id_laporan_lct)
                ->where('tipe_reject', 'budget_approval')
                ->latest()
                ->first();

            // Kirim email ke PIC
            // try {
            //     Mail::to('efdika1102@gmail.com')->send(new TaskBudgetRevisionMail($laporan, $alasanReject));
            //     Log::info('Email berhasil dikirim.');
            // } catch (\Exception $mailException) {
            //     Log::error('Gagal mengirim email', ['error' => $mailException->getMessage()]);
            //     return redirect()->back()->with('error', 'Email gagal dikirim. Namun data sudah tersimpan.');
            // }
            

            return redirect()->back()->with('success', 'Budget request needs revision.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting budget request: ' . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while rejecting the budget request: ' . $e->getMessage());
        }
    }


    public function history($id_laporan_lct)
    {
        $history = RejectLaporan::with('laporanLct','user')->where('id_laporan_lct', $id_laporan_lct)->get();

        return view('pages.admin.history.index', compact('history','id_laporan_lct'));
    }

}
