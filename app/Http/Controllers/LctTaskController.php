<?php
namespace App\Http\Controllers;

use App\Models\LctTasks;
use App\Models\LaporanLct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Console\View\Components\Task;

class LctTaskController extends Controller
{

    public function store(Request $request, $id_laporan_lct)
    {
        // dd($request->all()); // Debugging, pastikan request sesuai
        
        try {
            DB::beginTransaction(); // Mulai transaksi

            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->firstOrFail();

            $pic_id = $laporan->pic_id;

            if (!$pic_id) {
                return back()->with('error', 'You do not have access as a PIC.');
            }

            $tasks = json_decode($request->tasks, true);

            if (!is_array($tasks)) {
                return back()->with('error', 'The task format is invalid.');
            }

            // dd($tasks);
            // Validasi setiap task dalam array
            foreach ($tasks as $task) {
                $validatedData = Validator::make($task, [
                    'taskName' => 'required|string|max:255',
                    'dueDate' => 'required|date|after_or_equal:today',
                    'status' => 'nullable|string|in:pending,in_progress,completed',
                ])->validate();

                // Set default status jika tidak ada dalam request
                $statusTask = $validatedData['status'] ?? 'pending';

                // Buat task baru dengan `pic_id`
                LctTasks::create([
                    'id_laporan_lct' => $id_laporan_lct,
                    'task_name' => $validatedData['taskName'],
                    'due_date' => $validatedData['dueDate'],
                    'status_task' => $statusTask,
                    'pic_id' => $pic_id, // Pakai `pic_id` langsung
                    'validate_by_ehs' => 0, // Default belum divalidasi oleh EHS
                ]);
            }

            DB::commit(); // Simpan transaksi jika tidak ada error

            return back()->with('success', 'All tasks have been successfully added.');
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika ada error
            return back()->with('error', 'Gagal menambahkan task: ' . $e->getMessage());
        }
    }


    
    public function updateTask(Request $request, $id) {
        $task = LctTasks::findOrFail($id);
        $task->update([
            'task_name' => $request->task_name,
            'due_date' => $request->due_date,
        ]);
    
        return redirect()->route('admin.manajemen-lct.index')->with('success', 'Task updated successfully.');
    }
    
    

    public function updateStatus(Request $request, $id_task)
    {
        Log::info('Request data:', $request->all());
        // Validasi input
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        try {
            $task = LctTasks::findOrFail($id_task);

            // Update status
            $task->status = $request->status;
            $task->save();

            // Return response JSON
            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    

    public function __construct()
    {
        $this->middleware('auth')->except(['updateStatus']);
    }

}
