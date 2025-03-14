<?php

namespace App\Http\Controllers;

use App\Models\Pic;
use App\Models\LctTasks;
use App\Models\LaporanLct;
use Illuminate\Http\Request;
use App\Models\BudgetApproval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AssignToEhsRequest;
use Illuminate\Support\Facades\Validator;

class ManajemenLctController extends Controller
{
    public function index()
    {
        return view('pages.admin.manajemen-lct.index');
    }

    public function show($id_laporan_lct)
    {
        $laporan = LaporanLct::with(['picUser', 'rejectLaporan', 'kategori'])
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
        $tasks = LctTasks::with('pic')
            ->where('id_laporan_lct', $id_laporan_lct)
            ->orderBy('due_date', 'asc')
            ->get();
        
        $bukti_temuan = collect(json_decode($laporan->bukti_temuan, true))->map(function ($path) {
            return asset('storage/' . $path);
        });
        $bukti_perbaikan = collect(json_decode($laporan->bukti_perbaikan, true))->map(function ($path) {
            return asset('storage/' . $path);
        });

        return view('pages.admin.manajemen-lct.show', compact('laporan', 'tasks', 'bukti_temuan', 'bukti_perbaikan'));
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

            // Tentukan status berdasarkan tingkat bahaya
            $statusLct = ($laporan->tingkat_bahaya === 'Medium' || $laporan->tingkat_bahaya === 'High') 
            ? 'waiting_approval_temporary' 
            : 'waiting_approval';
            
            // dd("masuk sin");
            // Update laporan dengan data terbaru
            $laporan->update([
                'date_completion' => $request->date_completion,
                'status_lct' => $statusLct,
                'bukti_perbaikan' => json_encode($buktiPerbaikan), // Simpan dalam format JSON
            ]);

            DB::commit();

            return redirect()->route('admin.manajemen-lct')->with('success', 'The repair results have been sent to EHS.');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            Log::error('Gagal mengirim hasil perbaikan ke EHS: ' . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred, please try again.');
        }
    }



    public function submitTaskBudget(Request $request, $id_laporan_lct)
    {
        // Ambil semua data
        $data = $request->all();

        // Filter hanya task yang memiliki nilai
        $filteredTasks = array_filter($data['tasks'], function ($task) {
            return !empty($task['taskName']) && !empty($task['namePic']) && !empty($task['dueDate']);
        });

        // Gantilah 'tasks' dengan data yang sudah difilter
        $data['tasks'] = array_values($filteredTasks);

        // Jika setelah filtering tidak ada task yang valid, hentikan proses
        if (empty($data['tasks'])) {
            return redirect()->back()->with('error', 'Tidak ada task yang valid untuk disimpan.');
        }

        // Validasi setelah filtering
        $validatedData = Validator::make($data, [
            'tasks' => 'array|required',
            'tasks.*.taskName' => 'required|string',
            'tasks.*.namePic' => 'required|string',
            'tasks.*.dueDate' => 'required|date',
            'tasks.*.notes' => 'nullable|string',
            'estimatedBudget' => 'required|numeric',
        ])->validate();
        
        $pic = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->value('pic_id');


        try {
            DB::beginTransaction(); 
            foreach ($filteredTasks as $task) {
                LctTasks::create([
                    'id_laporan_lct' => $id_laporan_lct,
                    'task_name' => $task['taskName'],
                    'name_pic' => $task['namePic'],
                    'due_date' => $task['dueDate'],
                    'notes' => $task['notes'] ?? null,
                    'pic_id' => $pic,
                    'status' => 'pending',
                ]);
            }
            // dd("masuk ini euy");
            // Simpan atau update estimasi budget di Laporan LCT
            LaporanLct::where('id_laporan_lct', $id_laporan_lct)->update([
                'estimated_budget' => $validatedData['estimatedBudget'],
                'status_lct' => 'waiting_approval_taskbudget',
            ]);

            DB::commit(); // Simpan perubahan jika semua berhasil

            return redirect()->back()->with('success', 'Tasks successfully submitted!');
        } catch (\Exception $e) {
            DB::rollBack(); 
            dd($e);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

}
