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
use Illuminate\Support\Facades\Mail;

class BudgetApprovalController extends Controller
{
    public function index()
    {
        return view('pages.admin.budget-approval.index');
    }

    public function show($id_laporan_lct)
    {
        $taskBudget = LaporanLct::with([
            'tasks' => function ($query) {
                $query->orderBy('due_date', 'asc'); // Urutkan dari due_date terdekat
            },
            'tasks.pic.user',  // Memuat user melalui pic yang terkait dengan task
            'rejectLaporan'
        ])
        ->where('id_laporan_lct', $id_laporan_lct)
        ->whereIn('status_lct', ['waiting_approval_taskbudget', 'approved_taskbudget', 'taskbudget_revision'])
        ->first();

        // dd($taskBudget);

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
        try {
            DB::beginTransaction(); // Mulai transaksi

            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->first();
            $laporan->update(['status_lct' => 'approved_taskbudget']);

            // Kirim email ke user yang bersangkutan
            Mail::to('efdika1102@gmail.com')->queue(new TaskBudgetApproved($laporan));

            DB::commit(); 
            
            return redirect()->route('admin.budget-approval-history.index')->with('success', 'Budget request has been approved.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'An error occurred while approving the budget request.');
        }
    }

    public function reject(Request $request, $id_laporan_lct)
    {
        // Validasi input untuk memastikan alasan reject tidak kosong
        $request->validate([
            'alasan_reject' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction(); // Mulai transaksi

            // Cari laporan berdasarkan ID
            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->firstOrFail();

            // Simpan alasan reject di tabel lct_laporan_reject
            RejectLaporan::create([
                'id_laporan_lct' => $id_laporan_lct,
                'alasan_reject' => $request->alasan_reject,
                'tipe_reject' => $request->tipe_reject ?? 'budget_approval',
            ]);

            // Update status laporan
            $laporan->update([
                'status_lct' => 'taskbudget_revision',
            ]);
            
            DB::commit(); // Simpan perubahan
            // Ambil alasan terbaru
            $alasanReject = RejectLaporan::where('id_laporan_lct', $id_laporan_lct)
            ->where('tipe_reject', 'budget_approval')
            ->latest()
            ->first();

            // Kirim email ke PIC
            Mail::to('efdika1102@gmail.com')->queue(new TaskBudgetRevisionMail($laporan, $alasanReject));


            return redirect()->back()->with('success', 'Budget request needs revision.');
        } catch (\Exception $e) {
            DB::rollBack(); // Kembalikan perubahan jika ada error

            // Log error untuk debugging (gantilah `Log` dengan use `Illuminate\Support\Facades\Log;`)
            Log::error('Error rejecting budget request: ' . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while rejecting the budget request: ' . $e->getMessage());
        }
    }


    public function history(){
        return view('pages.admin.budget-approval-history.index');
    }


}
