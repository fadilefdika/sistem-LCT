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
        $laporan = LaporanLct::with([
            'picUser', 
            'rejectLaporan', 
            'kategori',
            'tasks.pic' // Langsung ambil tasks dan relasinya
        ])->where('id_laporan_lct', $id_laporan_lct)->first();

        if (!$laporan) {
            abort(404, 'Laporan tidak ditemukan');
        }

        // Perbarui status jika masih 'in_progress'
        if ($laporan->status_lct === 'in_progress') {
            $laporan->update(['status_lct' => 'progress_work']);
        }

        // Ambil tasks langsung dari relasi tanpa query tambahan
        $tasks = LctTasks::where('id_laporan_lct', $laporan->id_laporan_lct)
            ->orderBy('due_date', 'asc') // Urutkan berdasarkan due_date terdekat
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'taskName' => $task->task_name,
                    'namePic' => $task->name_pic,
                    'dueDate' => $task->due_date,
                    'notes' => $task->notes,
                    'status' => $task->status ?? 'pending',
                ];
            });

        $tasks[] = ['id'=> '','taskName' => '', 'namePic' => '', 'dueDate' => '', 'notes' => '', 'status' => '']; // Tambah 1 baris kosong

            
        // dd($tasks, $laporan->estimated_budget);
        // Konversi bukti_temuan dan bukti_perbaikan
        $bukti_temuan = collect(json_decode($laporan->bukti_temuan, true))->map(fn ($path) => asset('storage/' . $path));
        $bukti_perbaikan = collect(json_decode($laporan->bukti_perbaikan, true))->map(fn ($path) => asset('storage/' . $path));

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
        // dd($request->all());
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

            // Ambil semua ID task yang ada di database untuk laporan ini
            $existingTaskIds = LctTasks::where('id_laporan_lct', $id_laporan_lct)
                ->pluck('id')
                ->toArray();

            // Ambil ID task yang dikirim dalam request (jika ada)
            $submittedTaskIds = array_filter(array_column($filteredTasks, 'id'));

            // Cari task yang ada di database tetapi tidak ada di request → ini yang harus dihapus
            $tasksToDelete = array_diff($existingTaskIds, $submittedTaskIds);

            // Hapus task yang tidak lagi ada di request
            if (!empty($tasksToDelete)) {
                LctTasks::whereIn('id', $tasksToDelete)->delete();
            }

            // Simpan atau update task yang dikirim
            foreach ($filteredTasks as $task) {
                LctTasks::updateOrCreate(
                    [
                        'id' => $task['id'] ?? null, // Gunakan ID jika ada
                    ],
                    [
                        'id_laporan_lct' => $id_laporan_lct,
                        'task_name' => $task['taskName'],
                        'name_pic' => $task['namePic'],
                        'due_date' => $task['dueDate'],
                        'notes' => $task['notes'] ?? null,
                        'pic_id' => $pic,
                        'status' => 'pending',
                    ]
                );
            }

            // Update estimated budget
            LaporanLct::where('id_laporan_lct', $id_laporan_lct)->update([
                'estimated_budget' => $validatedData['estimatedBudget'],
                'status_lct' => 'waiting_approval_taskbudget',
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Tasks successfully submitted!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    

}
