<?php
namespace App\Http\Controllers;

use App\Models\Pic;
use App\Models\User;
use App\Models\LctTasks;
use App\Models\LaporanLct;
use Illuminate\Http\Request;
use App\Models\RejectLaporan;
use App\Models\BudgetApproval;
use Illuminate\Support\Facades\DB;
use App\Mail\LaporanHasilPerbaikan;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TaskBudgetApprovalRequest;
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
        
        // Ambil laporan dengan relasi terkait
        $laporan = LaporanLct::with([
            'picUser', 
            'rejectLaporan', 
            'kategori',
            'tasks.pic',
        ])->where('id_laporan_lct', $id_laporan_lct)->first();
    
        // Cek jika laporan tidak ditemukan
        if (!$laporan) {
            abort(404, 'Laporan tidak ditemukan.');
        }
    
        // Logika ketika role adalah 'pic' dan belum pernah melihat laporan
        if ($roleName === 'pic' && !$laporan->first_viewed_by_pic_at) {
            // Catat waktu pertama kali PIC melihat laporan
            $laporan->update(['first_viewed_by_pic_at' => now()]);
    
            // Log pengiriman laporan ke EHS (sebagai contoh: pertama kali dilihat)
            RejectLaporan::create([
                'id_laporan_lct' => $laporan->id_laporan_lct,
                'user_id' => $user->id,
                'role' => $roleName,
                'status_lct' => 'progress_work',  // Status pengiriman laporan
                'alasan_reject' => null,  
                'tipe_reject' => null,  
            ]);
        }
    
        // Jika status laporan 'in_progress', perbarui menjadi 'progress_work'
        if ($laporan->status_lct === 'in_progress') {
            $laporan->update(['status_lct' => 'progress_work']);
        }
    
        // Ambil semua task terkait laporan tersebut
        $tasks = LctTasks::where('id_laporan_lct', $laporan->id_laporan_lct)
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'taskName' => $task->task_name,
                    'namePic' => $task->name_pic,
                    'picId' => $task->pic_id, // Pastikan pic_id diambil
                    'dueDate' => $task->due_date,
                    'status' => $task->status ?? 'pending',
                ];
            });
    
        // Menambahkan baris kosong untuk task baru
        $tasks[] = ['id' => '', 'taskName' => '', 'namePic' => '', 'dueDate' => '', 'attachment' => '', 'status' => ''];
    
        // Ambil daftar PIC terkait dengan laporan
        $picList = Pic::with('user:id,fullname')
            ->get()
            ->map(function ($pic) {
                return [
                    'id' => $pic->id,
                    'fullname' => $pic->user->fullname ?? 'Unknown'
                ];
            });
    
        // Mengambil bukti temuan dan perbaikan
        $bukti_temuan = collect(json_decode($laporan->bukti_temuan, true))->map(fn($path) => asset('storage/' . $path));
        $bukti_perbaikan = collect(json_decode($laporan->bukti_perbaikan, true))->map(fn($path) => asset('storage/' . $path));
    
        // Kembalikan tampilan dengan data yang sudah diproses
        return view('pages.admin.manajemen-lct.show', compact('laporan', 'tasks', 'bukti_temuan', 'bukti_perbaikan', 'picList'));
    }
    

    public function store(Request $request, $id_laporan_lct)
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

        // dd($request->all());
        $request->validate([
            'date_completion' => ['required', 'date'],
            'bukti_perbaikan' => ['required', 'array', 'max:5'], // Maksimal 5 file
            'bukti_perbaikan.*' => ['file', 'mimes:png,jpg,jpeg,gif', 'max:1024'], // Setiap file harus gambar dan max 1MB
            'tindakan_perbaikan' => ['required', 'string', 'max:1000'],
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
            
            
            // Update laporan dengan data terbaru
            $laporan->update([
                'date_completion' => $request->date_completion,
                'status_lct' => $statusLct,
                'bukti_perbaikan' => json_encode($buktiPerbaikan), 
                'tindakan_perbaikan' => $request->tindakan_perbaikan,
            ]);

            RejectLaporan::create([
                'id_laporan_lct' => $laporan->id_laporan_lct,
                'user_id' => $user->id,
                'role' => $roleName,
                'status_lct' => $statusLct,  // Status pengiriman laporan
                'alasan_reject' => null,  
                'tipe_reject' => null,  
            ]);

            Mail::to('efdika1102@gmail.com')->queue(new LaporanHasilPerbaikan($laporan));

            DB::commit();

            return redirect()->back()->with('success', 'The repair results have been sent to EHS.');
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e);
            Log::error('Gagal mengirim hasil perbaikan ke EHS: ' . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred, please try again.');
        }
    }

    public function submitTaskBudget(Request $request, $id_laporan_lct)
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
        
        $data = $request->all();
        $uploadedFiles = $request->file('attachments') ?? [];
    
        $validator = Validator::make($data, [
            'estimatedBudget' => 'required|numeric|min:0',
            'tasks' => 'nullable|array',
            'tasks.*.id' => 'nullable|integer|exists:lct_tasks,id',
            'tasks.*.taskName' => 'nullable|string|max:255',
            'tasks.*.picId' => 'nullable|integer|exists:lct_pic,id',
            'tasks.*.dueDate' => 'nullable|date',
        ]);
    
        if ($validator->fails()) {
            Log::error('Validation Failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed!',
                'errors' => $validator->errors()
            ], 422);
        }
    
        DB::beginTransaction();
        try {
            // Simpan semua file attachment
            $attachmentPaths = [];
            foreach ($uploadedFiles as $index => $uploadedFile) {
                if ($uploadedFile instanceof \Illuminate\Http\UploadedFile) {
                    // Mengambil nama asli file
                    $originalFileName = $uploadedFile->getClientOriginalName();
                    
                    // Menyimpan file dengan nama yang sudah diubah, tetapi bisa menyimpan nama asli di database
                    $fileName = "{$id_laporan_lct}_attachment_{$index}." . $uploadedFile->getClientOriginalExtension();
                    $filePath = $uploadedFile->storeAs('public/task_attachments', $fileName);
                    
                    // Menyimpan nama asli file dalam array attachment
                    $attachmentPaths[] = [
                        'path' => $filePath,
                        'original_name' => $originalFileName
                    ];
                }
            }
            
            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->firstOrFail();

            // Update laporan
            $laporan->update([
                'estimated_budget' => $data['estimatedBudget'],
                'status_lct' => 'waiting_approval_taskbudget',
                'attachments' => json_encode($attachmentPaths),
            ]);
            
    
            $existingTaskIds = [];
    
            if (!empty($data['tasks'])) {
                foreach ($data['tasks'] as $index => $task) {
                    if (empty($task['taskName']) || empty($task['picId']) || empty($task['dueDate'])) {
                        continue;
                    }
    
                    $taskData = [
                        'id_laporan_lct' => $id_laporan_lct,
                        'task_name' => $task['taskName'],
                        'pic_id' => $task['picId'],
                        'due_date' => $task['dueDate'],
                        // Tidak ada lagi attachment_path di task
                    ];
    
                    Log::info("Saving Task #{$index}:", $taskData);
    
                    if (!empty($task['id'])) {
                        $taskModel = LctTasks::find($task['id']);
                        if ($taskModel) {
                            $taskModel->update($taskData);
                            $existingTaskIds[] = $taskModel->id;
                        }
                    } else {
                        $taskModel = LctTasks::create($taskData);
                        $existingTaskIds[] = $taskModel->id;
                    }
                }
            }
    
            Log::info('Existing Task IDs:', $existingTaskIds);
    
            LctTasks::where('id_laporan_lct', $id_laporan_lct)
                ->whereNotIn('id', $existingTaskIds)
                ->delete();

                
            RejectLaporan::create([
                'id_laporan_lct' => $laporan->id_laporan_lct,
                'user_id' => $user->id,
                'role' => $roleName,
                'status_lct' => 'waiting_approval_taskbudget',  // Status pengiriman laporan
                'alasan_reject' => null,  
                'tipe_reject' => null,  
            ]);
    
            DB::commit();
    
            return redirect()->back()->with('success', 'Data saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exception caught during submitTaskBudget:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
    
            return redirect()->back()->with('error', 'An error occurred while saving data.');
        }
    }

    public function history($id_laporan_lct)
    {
        $history = RejectLaporan::with('laporanLct','user')->where('id_laporan_lct', $id_laporan_lct)->get();

        return view('pages.admin.history.index', compact('history'));
    }
    
    
}
