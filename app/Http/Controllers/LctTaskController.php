<?php
namespace App\Http\Controllers;

use App\Models\LctTask;
use App\Models\LaporanLct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LctTaskController extends Controller
{

    public function store(Request $request, $id_laporan_lct)
    {
        // Ambil laporan dan sekalian ambil user yang menjadi PIC-nya
        $laporan = LaporanLct::with(['pic' => function ($query) {
            $query->where('user_id', auth()->id());
        }])->where('id_laporan_lct', $id_laporan_lct)->firstOrFail();

        // Periksa apakah user memiliki PIC
        $pic = $laporan->pic->first(); // Karena `with()` mengembalikan koleksi

        if (!$pic) {
            return back()->with('error', 'Anda tidak memiliki akses sebagai PIC.');
        }

        // Validasi data request
        $validatedData = $request->validate([
            'deskripsi' => 'required|string',
            'due_date' => 'required|date',
            'status_task' => 'required|string',
        ]);

        // Buat task baru dengan `pic_id` dari user yang login
        LctTask::create([
            'id_laporan_lct' => $id_laporan_lct,
            'deskripsi' => $validatedData['deskripsi'],
            'due_date' => $validatedData['due_date'],
            'status_task' => $validatedData['status_task'],
            'pic_id' => $pic->id,
        ]);

        return back()->with('success', 'Task berhasil ditambahkan');
    }




    public function updateStatus(Request $request, $id)
    {
        $task = LctTask::findOrFail($id);
        $task->update(['status' => $request->status]);
        return response()->json(['message' => 'Status berhasil diperbarui']);
    }

    public function approve($id)
    {
        $task = LctTask::findOrFail($id);
        $task->update(['status' => 'Selesai']);
        return response()->json(['message' => 'Task berhasil disetujui']);
    }
}
