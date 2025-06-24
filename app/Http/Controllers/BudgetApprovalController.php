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
    public function index(Request $request)
    {
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $role = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            $role = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }
    
        $sortField = $request->get('sortField', 'created_at');
        $sortDirection = $request->get('sortDirection', 'desc');
        $perPage = $request->get('perPage', 10);
    
        $query = LaporanLct::select([
            'id', 
            'id_laporan_lct',
            'user_id',
            'pic_id',
            'status_lct',
            'tingkat_bahaya',
            'estimated_budget',
            'departemen_id',
            'created_at'
        ])
        ->with([
            'picUser',
            'tasks:id,id_laporan_lct,created_at',
        ])
        ->whereIn('status_lct', [
            'waiting_approval_taskbudget',
            'taskbudget_revision',
            'work_permanent',
            'approved_taskbudget',
            'waiting_approval_permanent',
            'permanent_revision',
            'approved_permanent'
        ]);
    
    
        if ($role === 'manajer') {
            $departemenId = \App\Models\LctDepartement::where('user_id', $user->id)->value('id');
            $query->where('departemen_id', $departemenId ?? 0); // 0 biar kosong
        }
        
        $taskBudget = $query
            ->orderByRaw("
                CASE 
                    WHEN status_lct = 'waiting_approval_taskbudget' THEN 1
                    WHEN status_lct = 'taskbudget_revision' THEN 2
                    WHEN status_lct = 'work_permanent' THEN 3
                    WHEN status_lct = 'approved_taskbudget' THEN 4
                    WHEN status_lct = 'waiting_approval_permanent' THEN 5
                    WHEN status_lct = 'permanent_revision' THEN 6
                    WHEN status_lct = 'approved_permanent' THEN 7
                    ELSE 99
                END
            ")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    
    
            if ($request->ajax()) {
                return view('partials.tabel-budget-approval-wrapper', compact('taskBudget'));
            }
            
    
        return view('pages.admin.budget-approval.index', compact('taskBudget'));
    }
    

    public function show($id_laporan_lct)
    {
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
    
        if (!$taskBudget) {
            // Redirect atau tampilkan error jika laporan tidak ditemukan
            return redirect()->back()->with('error', 'Data Laporan LCT tidak ditemukan atau status tidak sesuai.');
        }
    
        // Pastikan bukti_temuan dan bukti_perbaikan tidak null sebelum didecode
        $bukti_temuan = collect(json_decode($taskBudget->bukti_temuan ?? '[]', true))
            ->map(fn ($path) => asset('storage/' . $path));
    
        $bukti_perbaikan = collect(json_decode($taskBudget->bukti_perbaikan ?? '[]', true))
            ->map(fn ($path) => asset('storage/' . $path));

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

        $combined = $this->combineBudgetApprovalAndResponse($id_laporan_lct);
    
        $lowOrTemporaryRejects = $taskBudget->rejectLaporan->filter(function($item) {
            return in_array($item->tipe_reject, ['lct_perbaikan_low', 'lct_perbaikan_temporary']);
        });
        
        $budgetApprovalRejects = $taskBudget->rejectLaporan->filter(function($item) {
            return $item->tipe_reject === 'budget_approval';
        }); 

        return view('pages.admin.budget-approval.show', [
            'taskBudget' => $taskBudget,
            'bukti_temuan' => $bukti_temuan,
            'bukti_perbaikan' => $bukti_perbaikan,
            'combined' => $combined,
            'lowOrTemporaryRejects' => $lowOrTemporaryRejects,
            'budgetApprovalRejects' => $budgetApprovalRejects,
        ]);
    }

    private function combineBudgetApprovalAndResponse($id_laporan_lct)
    {
        $budget_approvals = RejectLaporan::where('id_laporan_lct', $id_laporan_lct)
            ->where('tipe_reject', 'budget_approval')
            ->orderBy('created_at')
            ->get();

        $pic_responses = RejectLaporan::where('id_laporan_lct', $id_laporan_lct)
            ->where('tipe_reject', 'pic_response')
            ->orderBy('created_at')
            ->get();

        return $budget_approvals->map(function ($approval) use ($pic_responses) {
            $response = $pic_responses
                ->firstWhere(fn($resp) => $resp->created_at->greaterThan($approval->created_at));
            return [
                'rev' => $approval,
                'pic_message' => $response?->alasan_reject
            ];
        })->reverse()->values();
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

            return redirect()->back()->with('success', 'Budget request has been approved.');
                            
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
                'tipe_reject'    => 'budget_approval',
                'status_lct'     => 'taskbudget_revision', // status saat ini untuk histori
            ]);

            // Update status laporan
            $laporan->update([
                'status_lct' => 'taskbudget_revision',
            ]);

            // dd($laporan);

            DB::commit();

            // // Ambil alasan reject terbaru
            // $alasanReject = RejectLaporan::where('id_laporan_lct', $id_laporan_lct)
            //     ->where('tipe_reject', 'budget_approval')
            //     ->latest()
            //     ->first();

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
