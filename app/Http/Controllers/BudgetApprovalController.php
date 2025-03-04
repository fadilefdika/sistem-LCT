<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BudgetApproval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\RejectLaporan;

class BudgetApprovalController extends Controller
{
    public function index()
    {
        return view('pages.admin.budget-approval.index');
    }

    public function show($id_laporan_lct)
    {
        $budget = BudgetApproval::with(['laporanLct', 'pic.user', 'rejects' => function ($query) {
            $query->where('tipe_reject', 'budget_approval')->orderBy('created_at', 'desc');;
        }])
        ->where('id_laporan_lct', $id_laporan_lct)
        ->firstOrFail();  

        return view('pages.admin.budget-approval.show', compact('budget'));
    }

    public function approve($id)
    {
        try {
            DB::beginTransaction(); // Mulai transaksi
            
            $budget = BudgetApproval::findOrFail($id);
            $budget->status_budget = 'approved';
            $budget->save();
    
            DB::commit(); 
            
            return redirect()->route('admin.budget-approval')->with('success', 'Budget request telah disetujui.');
        } catch (\Exception $e) {
            DB::rollBack(); // Kembalikan perubahan jika ada error
    
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyetujui budget request.');
        }
    }

    public function reject(Request $request, $id_laporan_lct)
    {
        try {

            DB::beginTransaction(); // Mulai transaksi
            $budget = BudgetApproval::where('id_laporan_lct', $id_laporan_lct)->first();
            
            if (!$budget) {
                return redirect()->back()->with('error', 'Budget tidak ditemukan.');
            }

            // 2. Update status_budget di tabel lct_budget_approval
            $budget->update([
                'status_budget' => 'rejected',
            ]);

            // 3. Simpan alasan reject di tabel lct_laporan_reject
            RejectLaporan::create([
                'id_laporan_lct' => $id_laporan_lct,
                'alasan_reject' => $request->alasan_reject,
                'tipe_reject' => 'budget_approval',
            ]);

            DB::commit(); // Simpan perubahan

            return redirect()->back()->with('success', 'Budget request telah ditolak.');
        } catch (\Exception $e) {
            dd("masuk catch", $e->getMessage());
            DB::rollBack(); // Kembalikan perubahan jika ada error

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menolak budget request: ' . $e->getMessage());
        }
    }

    public function history(){
        return view('pages.admin.budget-approval-history.index');
    }


}
