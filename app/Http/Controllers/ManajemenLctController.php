<?php

namespace App\Http\Controllers;

use App\Models\Pic;
use App\Models\LctTask;
use App\Models\LaporanLct;
use Illuminate\Http\Request;
use App\Models\BudgetApproval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AssignToEhsRequest;

class ManajemenLctController extends Controller
{
    public function index()
    {
        return view('pages.admin.manajemen-lct.index');
    }

    public function show($id_laporan_lct)
    {
        $laporan = LaporanLct::with(['picUser', 'rejectLaporan'])
            ->where('id_laporan_lct', $id_laporan_lct)
            ->first();

            // dd($laporan);
        if (!$laporan) {
            abort(404, 'Laporan tidak ditemukan');
        }

        // Perbarui status jika masih 'in_progress'
        if ($laporan->status_lct === 'in_progress') {
            $laporan->update(['status_lct' => 'progress_work']);
        }

        // Ambil semua task terkait laporan ini
        $tasks = LctTask::with('pic')
            ->where('id_laporan_lct', $id_laporan_lct)
            ->orderBy('due_date', 'asc')
            ->get();

        $budget = BudgetApproval::where('id_laporan_lct', $id_laporan_lct)
            ->with(['rejects' => function ($query) {
                $query->where('tipe_reject', 'budget_approval'); // Filter hanya "budget_approval"
            }])
            ->first();

        return view('pages.admin.manajemen-lct.show', compact('laporan', 'tasks', 'budget'));
    }


    public function store(AssignToEhsRequest $request, $id_laporan_lct)
    {
        // dd("masuk");
        try {
            DB::beginTransaction(); 
            // Cari laporan berdasarkan ID
            $laporan = LaporanLct::where('id_laporan_lct',$id_laporan_lct)->first();

            // dd($laporan);
            // Simpan gambar hanya jika diunggah
            $buktiPerbaikan = $laporan->bukti_perbaikan; // Simpan nilai lama jika tidak ada file baru
            if ($request->hasFile('bukti_perbaikan')) {
                $file = $request->file('bukti_perbaikan');

                // Buat nama file unik
                $filename = 'bukti_' . $id_laporan_lct . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Simpan file di storage
                $buktiPerbaikan = Storage::putFileAs('bukti_perbaikan', $file, $filename);
            }

            // Update laporan dengan data terbaru
            $laporan->update([
                'date_completion' => $request->date_completion,
                'status_lct' => 'waiting_approval',
                'bukti_perbaikan' => $buktiPerbaikan,
            ]);

            DB::commit(); 

            return redirect()->route('admin.manajemen-lct')->with('success', 'Hasil perbaikan telah dikirim ke EHS.');
        } catch (\Exception $e) {
            // dd("gagal");
            DB::rollBack(); // Batalkan transaksi jika ada error

            Log::error('Gagal mengirim hasil perbaikan ke EHS: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan, silakan coba lagi.');
        }
    }

    public function submitBudget(Request $request, $id_laporan_lct)
    {
        $request->merge([
            'budget_amount' => str_replace('.', '', $request->budget_amount),
        ]);
        
        $request->validate([
            'budget_amount' => 'required|numeric',
            'budget_description' => 'required|string',
            'payment_proof' => 'sometimes|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        try {
            DB::beginTransaction(); // Mulai transaksi

            $user = auth()->user()->id;
            $pic = Pic::where('user_id', $user)->first()->id;

            // Cek apakah budget sudah ada sebelumnya
            $budget = BudgetApproval::where('id_laporan_lct', $id_laporan_lct)->first();

            // Cek apakah ada file yang diunggah
            $filePath = $budget->lampiran ?? null;
            if ($request->hasFile('payment_proof')) {
                // Hapus file lama jika ada
                if ($filePath) {
                    Storage::disk('public')->delete($filePath);
                }
                $filePath = $request->file('payment_proof')->store('budget_proofs', 'public');
            }

            // Jika budget sudah ada, perbarui, jika belum, buat baru
            BudgetApproval::updateOrCreate(
                ['id_laporan_lct' => $id_laporan_lct], // Kondisi pencarian
                [
                    'pic_id' => $pic,
                    'budget' => $request->budget_amount,
                    'deskripsi' => $request->budget_description,
                    'lampiran' => $filePath,
                    'status_budget' => 'pending', // Reset status setelah perbaikan
                ]
            );

            DB::commit(); // Jika semua berhasil, commit transaksi

            return redirect()->back()->with('success', 'Budget request submitted successfully');
        } catch (\Exception $e) {
            DB::rollBack(); // Jika ada error, rollback transaksi
            return redirect()->back()->with('error', 'Failed to submit budget request: ' . $e->getMessage());
        }
    }




}
