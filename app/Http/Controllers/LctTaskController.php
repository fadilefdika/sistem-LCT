<?php
namespace App\Http\Controllers;

use App\Models\LctTask;
use App\Models\LaporanLct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Console\View\Components\Task;

class LctTaskController extends Controller
{

    public function store(Request $request, $id_laporan_lct)
    {
        try {
            DB::beginTransaction(); // Mulai transaksi

            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->firstOrFail();

            // Ambil pic_id langsung dari laporan
            $pic_id = $laporan->pic_id;

            // Periksa apakah laporan memiliki PIC
            if (!$pic_id) {
                return back()->with('error', 'Anda tidak memiliki akses sebagai PIC.');
            }

            // Validasi data request
            $validatedData = $request->validate([
                'task_name' => 'required|string|max:255',
                'due_date' => 'required|date|after_or_equal:today', // Bisa pilih hari ini
                'status_task' => 'nullable|string|in:pending,in_progress,completed', // Tidak wajib
            ]);

            // Set default status jika tidak ada dalam request
            $statusTask = $validatedData['status_task'] ?? 'pending';
            
            // Buat task baru dengan `pic_id`
            LctTask::create([
                'id_laporan_lct' => $id_laporan_lct,
                'task_name' => $validatedData['task_name'],
                'due_date' => $validatedData['due_date'],
                'status_task' => $statusTask,
                'pic_id' => $pic_id, // Pakai `pic_id` langsung
                'validate_by_ehs' => 0, // Default belum divalidasi oleh EHS
            ]);
            
            DB::commit(); // Simpan transaksi jika tidak ada error

            return back()->with('success', 'Task berhasil ditambahkan');
        } catch (\Exception $e) {
            dd("Error: " . $e->getMessage()); // Tampilkan pesan error
            DB::rollBack(); // Batalkan transaksi jika ada error
            return back()->with('error', 'Gagal menambahkan task: ' . $e->getMessage());
        }
    }

    

    public function updateStatus(Request $request, $id_task)
    {
    
        $task = LctTask::findOrFail($id_task);

        if (!$task) {
            return response()->json(['success' => false, 'message' => 'Task tidak ditemukan'], 404);
        }
    
        $task->status_task = $request->status;
        $task->save();
    
        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui']);
    }
    

    public function __construct()
    {
        $this->middleware('auth')->except(['updateStatus']);
    }




    public function validateTask(Task $task)
    {
        if (auth()->user()->role !== 'ehs') {
            return abort(403, 'Unauthorized');
        }

        $task->update(['validated_by_ehs' => true]);

        return redirect()->back()->with('success', 'Task berhasil divalidasi!');
    }

}
