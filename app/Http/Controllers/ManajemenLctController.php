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
        
        $bukti_temuan = collect(json_decode($laporan->bukti_temuan, true))->map(function ($path) {
            return asset('storage/' . $path);
        });

        return view('pages.admin.manajemen-lct.show', compact('laporan', 'tasks', 'budget', 'bukti_temuan'));
    }


    public function store(Request $request, $id_laporan_lct)
    {
        $request->validate([
            'date_completion' => ['required', 'date'],
            'bukti_perbaikan' => ['required', 'array', 'max:5'], // Maksimal 5 file
            'bukti_perbaikan.*' => ['file', 'mimes:png,jpg,jpeg,gif', 'max:1024'], // Setiap file harus gambar dan max 1MB
        ]);

        try {
            DB::beginTransaction();

            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->firstOrFail();

            $buktiPerbaikan = [];

            // Loop untuk menyimpan semua gambar
            if ($request->hasFile('bukti_perbaikan')) {
                foreach ($request->file('bukti_perbaikan') as $file) {
                    // Buat nama file unik
                    $filename = 'bukti_' . $id_laporan_lct . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                    // Simpan file ke storage/public/bukti_perbaikan
                    $path = $file->storeAs('public/bukti_perbaikan', $filename);

                    // Simpan hanya nama file atau path relatif
                    $buktiPerbaikan[] = 'bukti_perbaikan/' . $filename;
                }
            }

            // Update laporan dengan data terbaru
            $laporan->update([
                'date_completion' => $request->date_completion,
                'status_lct' => 'waiting_approval',
                'bukti_perbaikan' => json_encode($buktiPerbaikan), // Simpan dalam format JSON
            ]);

            DB::commit();

            return redirect()->route('admin.manajemen-lct')->with('success', 'Hasil perbaikan telah dikirim ke EHS.');
        } catch (\Exception $e) {
            DB::rollBack();

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
