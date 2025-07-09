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
            'Closed' => ['closed'],
            'Overdue' => ['overdue'],
        ];

        $laporanList = LaporanLct::whereNull('first_overdue_date')
            ->where('status_lct', '!=', 'closed')
            ->get();

        $query = $this->buildLaporanQuery($request, $user, 'pic');
        $query->select('*', DB::raw("CASE WHEN status_lct = 'closed' THEN 1 ELSE 0 END as order_type"));

        $perPage = $request->input('perPage', 10);

        $allowedSorts = [
            'Type' => 'type',
            'due_date' => 'due_date',
            'completion_date' => 'date_completion',
            'area' => DB::raw('(SELECT nama_area FROM lct_area WHERE lct_area.id = lct_laporan.area_id)'),
            'tingkat_bahaya' => DB::raw("CASE 
                                            WHEN tingkat_bahaya IS NULL THEN 0
                                            WHEN tingkat_bahaya = 'Low' THEN 1
                                            WHEN tingkat_bahaya = 'Medium' THEN 2
                                            WHEN tingkat_bahaya = 'High' THEN 3
                                            ELSE 4
                                        END"),
            'progress_status' => 'status_lct',
        ];

        $sortBy = request('sort_by');
        $sortColumn = $allowedSorts[$sortBy] ?? 'created_at';
        $sortOrder = request('sort_order') === 'desc' ? 'desc' : 'asc';

        // ✅ Final Query tanpa duplikat
        $laporans = $query
            ->orderBy('order_type')
            ->orderBy($sortColumn, $sortOrder)
            ->orderByDesc('updated_at')
            ->paginate($perPage)
            ->withQueryString();

        if ($request->ajax()) {
            return view('partials.tabel-manajemen-lct-wrapper', compact('laporans'))->render();
        }

        return view('pages.admin.manajemen-lct.index', [
            'laporans' => $laporans,
            'statusGroups' => $statusGroups,
            'areas' => $areas,
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

        $lowOrTemporaryRejects = $laporan->rejectLaporan->filter(function($item) {
            return in_array($item->tipe_reject, ['lct_perbaikan_low', 'lct_perbaikan_temporary']);
        });
            
        $budgetApprovalRejects = $laporan->rejectLaporan->filter(function($item) {
            return $item->tipe_reject === 'budget_approval';
        }); 

        $budgetApprovals = RejectLaporan::where('id_laporan_lct', $id_laporan_lct)
            ->where('tipe_reject', 'budget_approval')
            ->orderBy('created_at')
            ->get();
        
        $picResponses = RejectLaporan::where('id_laporan_lct', $id_laporan_lct)
            ->where('tipe_reject', 'pic_response')
            ->orderBy('created_at')
            ->get();
        
        // Combine approval + response
        $combined = $budgetApprovals->map(function ($approval) use ($picResponses) {
            $response = $picResponses->firstWhere('created_at', '>', $approval->created_at);
            return [
                'rev' => $approval,
                'pic_message' => $response?->alasan_reject
            ];
        })->reverse()->values();
        // Kembalikan tampilan dengan data yang sudah diproses
        return view('pages.admin.manajemen-lct.show', compact('laporan', 'tasks', 'bukti_temuan', 'tindakan_perbaikan', 'picList', 'lowOrTemporaryRejects', 'budgetApprovalRejects', 'combined','budgetApprovals','picResponses'));
    }
    

    public function store(Request $request, $id_laporan_lct)
    {
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $roleName = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }

        try {
            $validated = $request->validate([
                'date_completion' => ['required', 'date'],
                'bukti_perbaikan' => ['required', 'array', 'max:5'],
                'bukti_perbaikan.*' => ['file', 'mimes:png,jpg,jpeg,gif,webp', 'max:1024'],
                'tindakan_perbaikan' => ['required', 'string', 'max:1000'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        }

        try {
            DB::beginTransaction();

            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->firstOrFail();

            // Upload bukti perbaikan
            $buktiPerbaikan = [];
            foreach ($request->file('bukti_perbaikan', []) as $file) {
                $filename = 'bukti_' . $id_laporan_lct . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/bukti_perbaikan', $filename);
                $buktiPerbaikan[] = 'bukti_perbaikan/' . $filename;
            }

            // Update tindakan perbaikan
            $existing = json_decode($laporan->tindakan_perbaikan, true) ?? [];
            $existing[] = [
                'tanggal' => $request->date_completion,
                'tindakan' => $request->tindakan_perbaikan,
                'bukti' => $buktiPerbaikan,
            ];

            // Tentukan status_lct dan statusLog
            $statusLog = null;

            if ($laporan->tingkat_bahaya === 'Low') {
                $laporan->status_lct = 'waiting_approval';
                $statusLog = 'waiting_approval';

            } elseif (in_array($laporan->tingkat_bahaya, ['Medium', 'High'])) {
                $laporan->approved_temporary_by_ehs = 'pending';

                switch ($laporan->status_lct) {
                    case 'waiting_approval_temporary':
                    case 'temporary_revision':
                    case 'progress_work':
                        $laporan->status_lct = 'waiting_approval_temporary';
                        $statusLog = 'waiting_approval_temporary';
                        break;

                    case 'waiting_approval_taskbudget':
                    case 'taskbudget_revision':
                    case 'approved_taskbudget':
                    case 'waiting_approval_permanent':
                        // Jangan ubah status_lct, hanya log approval sementara
                        $statusLog = 'waiting_approval_temporary';
                        break;

                    default:
                        Log::warning('Status tidak valid untuk tingkat bahaya ini', [
                            'status' => $laporan->status_lct
                        ]);
                        return redirect()->back()->with('error', 'Status tidak valid untuk tingkat bahaya tersebut.');
                }

            } else {
                Log::error('Tingkat bahaya tidak valid', [
                    'tingkat_bahaya' => $laporan->tingkat_bahaya
                ]);
                return redirect()->back()->with('error', 'Tingkat bahaya tidak valid.');
            }

            // Update laporan
            $laporan->update([
                'date_completion' => $request->date_completion,
                'tindakan_perbaikan' => json_encode($existing),
                'bukti_perbaikan' => null, // reset jika memang perlu
                'status_lct' => $laporan->status_lct,
                'approved_temporary_by_ehs' => $laporan->approved_temporary_by_ehs ?? null
            ]);

            // Catat riwayat
            RejectLaporan::create([
                'id_laporan_lct' => $laporan->id_laporan_lct,
                'user_id' => $user->id,
                'role' => $roleName,
                'status_lct' => $statusLog,
                'alasan_reject' => null,
                'tipe_reject' => null,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'The repair results have been sent to EHS.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan hasil perbaikan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred, please try again.');
        }
    }


    public function submitTaskBudget(Request $request, $id_laporan_lct)
    {
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $roleName = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }

        $data = $request->all();

        // Format budget
        $estimatedBudgetRaw = $request->estimatedBudget;
        $estimatedBudgetClean = preg_replace('/[^\d]/', '', $estimatedBudgetRaw);
        $data['estimatedBudget'] = (int) $estimatedBudgetClean;

        $uploadedFiles = $request->file('attachments') ?? [];

        // Ambil laporan untuk validasi existing attachment
        $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->firstOrFail();
        $existingAttachments = json_decode($laporan->attachments ?? '[]', true) ?? [];

        // Cek minimal file
        if (count($uploadedFiles) === 0 && count($existingAttachments) === 0) {
            return back()->withErrors(['attachments' => 'Please upload at least one file.'])->withInput();
        }

        // Validasi
        $validator = Validator::make($data, [
            'permanentAction' => 'required|string|max:1000',
            'estimatedBudget' => 'required|numeric|min:0',
            'tindakan_perbaikan' => 'nullable|string|max:1000',
            'tasks' => 'nullable|array',
            'tasks.*.id' => 'nullable|integer|exists:lct_tasks,id',
            'tasks.*.taskName' => 'nullable|string|max:255',
            'tasks.*.picId' => 'nullable|integer|exists:lct_pic,id',
            'tasks.*.dueDate' => 'nullable|date',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Update laporan
            $laporan->action_permanent = $data['permanentAction'];
            $laporan->estimated_budget = $data['estimatedBudget'];

            $totalFiles = count($existingAttachments) + count($uploadedFiles);
            if ($totalFiles > 5) {
                return back()->with('error', 'Maximum 5 files allowed.')->withInput();
            }

            foreach ($uploadedFiles as $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $path = $file->store("lct/attachments/{$id_laporan_lct}", 'public');
                    $existingAttachments[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'path' => $path
                    ];
                }
            }
            $laporan->attachments = json_encode($existingAttachments);

            $isRevision = $laporan->status_lct === 'taskbudget_revision' && !empty($data['tindakan_perbaikan']);
            $alasan = $isRevision ? $data['tindakan_perbaikan'] : null;
            $tipeReject = $isRevision ? 'pic_response' : null;

            $laporan->status_lct = 'waiting_approval_taskbudget';
            $laporan->save();

            // Handle tasks
            $submittedTasks = $data['tasks'] ?? [];
            $submittedIds = [];
            $existingTaskIds = LctTasks::where('id_laporan_lct', $id_laporan_lct)->pluck('id')->toArray();

            foreach ($submittedTasks as $taskData) {
                if (!empty($taskData['taskName'])) {
                    $task = !empty($taskData['id']) ? LctTasks::find($taskData['id']) : new LctTasks();
                    $task->id_laporan_lct = $id_laporan_lct;
                    $task->task_name = $taskData['taskName'];
                    $task->pic_id = $taskData['picId'] ?? null;
                    $task->due_date = $taskData['dueDate'] ?? null;
                    $task->save();
                    $submittedIds[] = $task->id;
                }
            }

            $deletedTasks = array_diff($existingTaskIds, $submittedIds);
            if (!empty($deletedTasks)) {
                LctTasks::whereIn('id', $deletedTasks)->delete();
                Log::info('Deleted Task IDs', $deletedTasks);
            }

            RejectLaporan::create([
                'id_laporan_lct' => $laporan->id_laporan_lct,
                'user_id' => $user->id,
                'role' => $roleName,
                'status_lct' => 'waiting_approval_taskbudget',
                'alasan_reject' => $alasan,
                'tipe_reject' => $tipeReject,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Action permanent updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in submitTaskBudget', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred while saving the data.');
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
        $picId = \App\Models\Pic::where('user_id', $user->id)->value('id');
        $laporanTaskIds = \App\Models\LctTasks::where('pic_id', $picId)->pluck('id_laporan_lct');

        $query = LaporanLct::query()
            ->where(function ($q) use ($picId, $laporanTaskIds) {
                $q->where('pic_id', $picId); // Main PIC
                
                // Task-only dengan status khusus
                $q->orWhere(function ($subQ) use ($picId, $laporanTaskIds) {
                    $subQ->whereIn('id_laporan_lct', $laporanTaskIds)
                        ->where('pic_id', '!=', $picId)
                        ->whereIn('status_lct', ['approved_taskbudget', 'closed']);
                });
            })
            ->select('*', DB::raw("
                CASE 
                    WHEN pic_id = $picId THEN 0 
                    ELSE 1 
                END as is_task_only
            "))
            ->with('picUser', 'kategori', 'area');

            if ($request->boolean('is_task_only')) {
                
                return LaporanLct::query()
                    ->whereIn('id_laporan_lct', $laporanTaskIds)
                    ->where('pic_id', '!=', $picId)
                    ->whereIn('status_lct', ['approved_taskbudget', 'closed'])
                    ->with('picUser', 'kategori', 'area')
                    ->select('*', DB::raw("
                        CASE 
                            WHEN pic_id = $picId THEN 0 
                            ELSE 1 
                        END as is_task_only
                    "));
            }
            

        // === FILTER TAMBAHAN ===
        if ($request->filled('tanggalAwal') && $request->filled('tanggalAkhir')) {
            $query->whereBetween('tanggal_temuan', [
                \Carbon\Carbon::parse($request->tanggalAwal)->startOfDay(),
                \Carbon\Carbon::parse($request->tanggalAkhir)->endOfDay()
            ]);
        }

        if ($request->filled('areaId')) {
            $query->where('area_id', $request->areaId);
        }

        if ($request->filled('categoryId')) {
            $query->where('kategori_id', $request->categoryId);
        }

        if ($request->filled('riskLevel')) {
            $riskLevels = explode(',', $request->riskLevel);
            $query->whereIn('tingkat_bahaya', $riskLevels);
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
                        ->orWhere(function ($midTemp) use ($today) {
                            $midTemp->whereIn('tingkat_bahaya', ['Medium', 'High'])
                                ->whereDate('due_date_temp', '<', $today)
                                ->whereNull('date_completion_temp');
                        })
                        ->orWhere(function ($midPerm) use ($today) {
                            $midPerm->whereIn('tingkat_bahaya', ['Medium', 'High'])
                                ->whereDate('due_date_perm', '<', $today)
                                ->whereNull('date_completion');
                        });
                    });
                }
            });
        }

        return $query;
    }

}
