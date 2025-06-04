<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pic;
use App\Models\User;
use App\Models\AreaLct;
use App\Models\Kategori;
use App\Models\LctTasks;
use App\Models\LaporanLct;
use Illuminate\Http\Request;
use App\Models\RejectLaporan;
use App\Models\BudgetApproval;
use App\Models\LctDepartement;
use App\Exports\LaporanLctExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Mail\LaporanHasilPerbaikan;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TaskBudgetApprovalRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AssignToEhsRequest;
use App\Models\LctDepartemenPic;
use Illuminate\Support\Facades\Validator;

class ManajemenLctController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $picId = Pic::where('user_id', $user->id)->value('id');
        $areas = \App\Models\AreaLct::whereNull('deleted_at')->pluck('nama_area', 'id');
        $categories = \App\Models\Kategori::whereNull('deleted_at')->pluck('nama_kategori', 'id');

        $statusGroups = [
            'In Progress' => ['in_progress', 'progress_work', 'waiting_approval'],
            'Approved' => ['approved', 'approved_temporary', 'approved_taskbudget'],
            'Closed' => ['closed'],
            'Overdue' => ['overdue'],
        ];

        $now = Carbon::now();

        // Ambil semua laporan yang belum pernah dicatat overdue
        $laporanList = LaporanLct::whereNull('first_overdue_date')
            ->where('status_lct', '!=', 'closed')
            ->get();

        foreach ($laporanList as $laporan) {
            $overdue = false;

            if ($laporan->tingkat_bahaya === 'Low') {
                if (is_null($laporan->date_completion) && $laporan->due_date && Carbon::parse($laporan->due_date)->lt($now)) {
                    $overdue = true;
                }
            } elseif (in_array($laporan->tingkat_bahaya, ['Medium', 'High'])) {
                if (
                    is_null($laporan->due_date_temp) && $laporan->due_date && Carbon::parse($laporan->due_date)->lt($now)
                ) {
                    // Safety net jika due_date_temp tidak digunakan
                    $overdue = true;
                } elseif (
                    !is_null($laporan->due_date_temp) &&
                    is_null($laporan->due_date_perm) &&
                    Carbon::parse($laporan->due_date_temp)->lt($now)
                ) {
                    $overdue = true;
                } elseif (
                    !is_null($laporan->due_date_perm) &&
                    is_null($laporan->date_completion) &&
                    Carbon::parse($laporan->due_date_perm)->lt($now)
                ) {
                    $overdue = true;
                }
            }

            if ($overdue) {
                $laporan->first_overdue_date = $now;
                $laporan->save();
            }
        }
        $query = $this->buildLaporanQuery($request, $user, 'pic');
        $query->select('*', DB::raw("CASE WHEN status_lct = 'closed' THEN 1 ELSE 0 END as order_type"));

        $perPage = $request->input('perPage', 10);

        $laporans = $query
            ->orderBy('order_type')
            ->orderByDesc('updated_at')
            ->paginate($perPage)
            ->withQueryString();

       

        if ($request->ajax()) {
            // Ini penting! Return partial yang hanya bagian isi
            return view('partials.tabel-manajemen-lct-wrapper', compact('laporans'))->render();
        }

        return view('pages.admin.manajemen-lct.index', [
            'laporans' => $laporans, 
            'statusGroups' => $statusGroups,
            'areas'=>$areas,
            'categories' => $categories,
        
        ]);
    }

    public function show($id_laporan_lct)
    {
        // Cek apakah pengguna menggunakan guard 'ehs' atau 'web' untuk pengguna biasa
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $roleName = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            // Ambil dari session terlebih dahulu, fallback ke relasi jika tidak ada
            $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
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
    
        $picIds = LctDepartemenPic::whereNull('deleted_at')->pluck('pic_id')->toArray();

        // Ambil semua PIC beserta user-nya
        $picList = PIC::whereIn('id', $picIds)
            ->with('user:id,fullname') // load user, ambil field yang diperlukan
            ->get()
            ->map(function ($pic) {
                return [
                    'pic_id' => $pic->id,
                    'user_id' => $pic->user->id ?? null,
                    'fullname' => $pic->user->fullname ?? 'Unknown'
                ];
            });

    
        // Mengambil bukti temuan dan perbaikan
        $bukti_temuan = collect(json_decode($laporan->bukti_temuan, true))->map(fn($path) => asset('storage/' . $path));
        // Mengambil tindakan perbaikan yang disimpan dalam JSON dan mendekodekan
        $tindakan_perbaikan = collect(json_decode($laporan->tindakan_perbaikan, true))->map(function ($entry) {
            return [
                'tanggal' => $entry['tanggal'],
                'tindakan' => $entry['tindakan'],
                'bukti' => collect($entry['bukti'])->map(fn($path) => asset('storage/' . $path)),
            ];
        });

    
        // Kembalikan tampilan dengan data yang sudah diproses
        return view('pages.admin.manajemen-lct.show', compact('laporan', 'tasks', 'bukti_temuan', 'tindakan_perbaikan', 'picList'));
    }
    

    public function store(Request $request, $id_laporan_lct)
    {
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $roleName = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            // Ambil dari session terlebih dahulu, fallback ke relasi jika tidak ada
            $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }
        
    // Validasi dengan try-catch untuk tangkap error validasi dan log pesan
        try {
            $validated = $request->validate([
                'date_completion' => ['required', 'date'],
                'bukti_perbaikan' => ['required', 'array', 'max:5'],
                'bukti_perbaikan.*' => ['file', 'mimes:png,jpg,jpeg,gif,webp', 'max:1024'],
                'tindakan_perbaikan' => ['required', 'string', 'max:1000'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log error validasi agar tahu apa yang gagal
            Log::error('Validation failed:', $e->errors());

            // Jika ingin langsung kirim balik pesan error ke response (opsional)
            return back()->withErrors($e->errors())->withInput();
        }

        try {
            DB::beginTransaction();

            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->firstOrFail();
            $buktiPerbaikan = [];
            if ($request->hasFile('bukti_perbaikan')) {
                foreach ($request->file('bukti_perbaikan') as $file) {
                    $filename = 'bukti_' . $id_laporan_lct . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('public/bukti_perbaikan', $filename);
                    $buktiPerbaikan[] = 'bukti_perbaikan/' . $filename;
                }
            }

            // Ambil data tindakan_perbaikan sebelumnya (jika ada)
            $existing = json_decode($laporan->tindakan_perbaikan, true) ?? [];

            // Tambahkan data baru
            $newEntry = [
                'tanggal' => $request->date_completion,
                'tindakan' => $request->tindakan_perbaikan,
                'bukti' => $buktiPerbaikan,
            ];
            $existing[] = $newEntry;

            $statusLct = ($laporan->tingkat_bahaya === 'Medium' || $laporan->tingkat_bahaya === 'High') 
                ? 'waiting_approval_temporary' 
                : 'waiting_approval';
            $laporan->update([
                'date_completion' => $request->date_completion,
                'status_lct' => $statusLct,
                'tindakan_perbaikan' => json_encode($existing), // JSON lengkap
                'bukti_perbaikan' => null, // Optional: kosongkan karena sudah masuk dalam JSON
            ]);
            RejectLaporan::create([
                'id_laporan_lct' => $laporan->id_laporan_lct,
                'user_id' => $user->id,
                'role' => $roleName,
                'status_lct' => $statusLct,
                'alasan_reject' => null,
                'tipe_reject' => null,
            ]);

            // try {
            //     Mail::to('efdika1102@gmail.com')->send(new LaporanHasilPerbaikan($laporan));
            //     Log::info('Email berhasil dikirim.');
            // } catch (\Exception $mailException) {
            //     Log::error('Gagal mengirim email', ['error' => $mailException->getMessage()]);
            //     return redirect()->back()->with('error', 'Email gagal dikirim. Namun data sudah tersimpan.');
            // }
            

            DB::commit();

            return redirect()->back()->with('success', 'The repair results have been sent to EHS.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengirim hasil perbaikan ke EHS: ' . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred, please try again.');
        }
    }


    public function submitTaskBudget(Request $request, $id_laporan_lct)
    {
        // Cek apakah pengguna menggunakan guard 'ehs' atau 'web' untuk pengguna biasa
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $roleName = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            // Ambil dari session terlebih dahulu, fallback ke relasi jika tidak ada
            $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
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

            // Ambil ulang semua task terbaru yang terhubung dengan laporan ini
            $tasks = LctTasks::with('pic')
                ->where('id_laporan_lct', $id_laporan_lct)
                ->get();


            // try {
            //     Mail::to('efdika1102@gmail.com')->send(new TaskBudgetApprovalRequest($laporan, $tasks));
            //     Log::info('Email berhasil dikirim.');
            // } catch (\Exception $mailException) {
            //     Log::error('Gagal mengirim email', ['error' => $mailException->getMessage()]);
            //     return redirect()->back()->with('error', 'Email gagal dikirim. Namun data sudah tersimpan.');
            // }
    
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

        return view('pages.admin.history.index', compact('history','id_laporan_lct'));
    }
    
    public function exportExcel(Request $request)
    {
        // Ambil user dan role sesuai guard
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $role = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            // Ambil dari session terlebih dahulu, fallback ke relasi jika tidak ada
            $role = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }
        

        $query = $this->buildLaporanQuery($request, $user, $role);
        $laporans = $query->get();


        return Excel::download(new LaporanLctExport($laporans), 'laporan_lct_' . now()->format('Ymd_His') . '.xlsx');
    }

    private function buildLaporanQuery(Request $request, $user, $role)
    {
        $query = LaporanLct::query();

        // Filter berdasarkan role
        if ($role === 'user') {
            $query->where('user_id', $user->id);
        } elseif ($role === 'manajer') {
            $departemenId = \App\Models\LctDepartement::where('user_id', $user->id)->value('id');
            $query->where('departemen_id', $departemenId);
        } elseif (!in_array($role, ['ehs'])) {
            $picId = \App\Models\Pic::where('user_id', $user->id)->value('id');
            $query->where('pic_id', $picId);
        }

        // Filter tambahan dari request
        if ($request->filled('riskLevel')) {
            $query->where('tingkat_bahaya', $request->riskLevel);
        }

        if ($request->filled('statusLct')) {
            $statuses = explode(',', $request->statusLct);
            $today = now();

            $query->where(function ($q) use ($statuses, $today) {
                $q->whereIn('status_lct', $statuses);

                if (in_array('overdue', $statuses)) {
                    $q->orWhere(function ($sub) use ($today) {
                        $sub->where(function ($low) use ($today) {
                            $low->where('tingkat_bahaya', 'Low')
                                ->whereDate('due_date', '<', $today)
                                ->whereNull('date_completion');
                        })
                        ->orWhere(function ($mediumHighTemp) use ($today) {
                            $mediumHighTemp->whereIn('tingkat_bahaya', ['Medium', 'High'])
                                ->whereDate('due_date_temp', '<', $today)
                                ->whereNull('date_completion_temp');
                        })
                        ->orWhere(function ($mediumHighPerm) use ($today) {
                            $mediumHighPerm->whereIn('tingkat_bahaya', ['Medium', 'High'])
                                ->whereDate('due_date_perm', '<', $today)
                                ->whereNull('date_completion');
                        });
                    });
                }
            });
        }

        if ($request->filled('tanggalAwal') && $request->filled('tanggalAkhir')) {
            $startDate = \Carbon\Carbon::parse($request->tanggalAwal)->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->tanggalAkhir)->endOfDay();
            $query->whereBetween('tanggal_temuan', [$startDate, $endDate]);
        }

        if ($request->filled('categoryId')) {
            $query->where('kategori_id', $request->categoryId);
        }

        if ($request->filled('areaId')) {
            $query->where('area_id', $request->areaId);
        }

        return $query;
    }
}
