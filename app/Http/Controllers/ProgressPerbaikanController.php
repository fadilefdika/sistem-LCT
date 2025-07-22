<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\AreaLct;
use App\Models\Kategori;
use App\Models\LaporanLct;
use Illuminate\Http\Request;
use App\Models\RejectLaporan;
use App\Models\LctDepartement;
use App\Mail\CloseNotification;
use App\Exports\LaporanLctExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Shape\Table;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Shape\Drawing\File as DrawingFile;

class ProgressPerbaikanController extends Controller
{
    
    public function index(Request $request)
    {
        // Ambil user dan role sesuai guard
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $role = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            $role = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }

        // Data dropdown filter
        $departments = \App\Models\LctDepartement::whereNull('deleted_at')->pluck('nama_departemen', 'id');
        $areas = \App\Models\AreaLct::whereNull('deleted_at')->pluck('nama_area', 'id');
        $categories = \App\Models\Kategori::whereNull('deleted_at')->pluck('nama_kategori', 'id');

        $statusGroups = [
            'Open' => ['open'],
            'In Progress' => ['in_progress', 'progress_work', 'waiting_approval'],
            'Waiting Approval' => [
                'waiting_approval', 'waiting_approval_temporary', 'waiting_approval_permanent', 'waiting_approval_taskbudget'
            ],
            'Approved' => ['approved', 'approved_temporary', 'approved_taskbudget'],
            'Closed' => ['closed'],
            'Overdue' => ['overdue'],
        ];

        $query = $this->buildLaporanQuery($request, $user, $role);

        // JOIN agar bisa sort pakai fullname dari user
        $query->leftJoin('lct_pic', 'lct_laporan.pic_id', '=', 'lct_pic.id')
            ->leftJoin('users', 'users.id', '=', 'lct_pic.user_id');

        $query->select('lct_laporan.*', 'users.fullname', DB::raw("CASE WHEN status_lct = 'closed' THEN 1 ELSE 0 END as order_type"));

        $perPage = $request->input('perPage', 10);

        $allowedSorts = [
            'finding_date'     => 'tanggal_temuan',
            'due_date'         => 'due_date',
            'pic_name'         => 'users.fullname',
            'tingkat_bahaya'   => DB::raw("
                CASE 
                    WHEN tingkat_bahaya IS NULL THEN 0
                    WHEN tingkat_bahaya = 'Low' THEN 1
                    WHEN tingkat_bahaya = 'Medium' THEN 2
                    WHEN tingkat_bahaya = 'High' THEN 3
                    ELSE 4
                END
            "),
            'progress_status'  => 'status_lct',
            'created_at'       => 'lct_laporan.created_at',
        ];

        $sortBy = $request->input('sort_by');
        $sortOrder = $request->input('sort_order', 'asc');

        $query->orderBy('order_type');

        if ($sortBy && array_key_exists($sortBy, $allowedSorts)) {
            $query->orderBy($allowedSorts[$sortBy], $sortOrder);
        } else {
            $query->orderByDesc('lct_laporan.updated_at'); // Fallback default
        }

        $laporans = $query->paginate($perPage)->withQueryString();

        // Statistik grafik
        $availableYears = LaporanLct::selectRaw('YEAR(tanggal_temuan) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        if ($request->ajax()) {
            return view('partials.tabel-reporting-wrapper', compact('laporans'))->render();
        }

        return view('pages.admin.progress-perbaikan.index', [
            'laporans' => $laporans,
            'statusGroups' => $statusGroups,
            'areas' => $areas,
            'categories' => $categories,
            'departments' => $departments,
            'availableYears' => $availableYears,
        ]);
    }


    public function show($id_laporan_lct)
    {
        
        $laporan = LaporanLct::with([
                'user', 'picUser', 'rejectLaporan', 'kategori',
                'tasks' => function ($query) {
                    $query->orderBy('due_date', 'asc'); // Urutkan dari due date terdekat
                }
            ])
            ->where('id_laporan_lct', $id_laporan_lct)
            ->first();

        if (!$laporan) {
            return abort(404, 'Laporan tidak ditemukan');
        }

        // Ambil bukti temuan & perbaikan
        $bukti_temuan = collect(json_decode($laporan->bukti_temuan, true))->map(fn($path) => asset('storage/' . $path));
        $tindakan_perbaikan = collect(json_decode($laporan->tindakan_perbaikan, true))->map(function ($entry) {
            return [
                'tanggal' => $entry['tanggal'],
                'tindakan' => $entry['tindakan'],
                'bukti' => collect($entry['bukti'])->map(fn($path) => asset('storage/' . $path)),
            ];
        });
        // Cek apakah semua task sudah selesai
        $tasks = $laporan->tasks;

        $kategoriList = Kategori::all(); // Ambil semua kategori dari tabel

        $areaList = AreaLct::all();

        $allTasksCompleted = $tasks->isNotEmpty() && $tasks->every(fn($task) => $task->status === 'completed');

        if (
            $allTasksCompleted &&
            $laporan->status_lct !== 'approved_permanent' &&
            $laporan->status_lct !== 'closed'
        ) {
            $laporan->status_lct = 'waiting_approval_permanent';
            $laporan->save();
        }
        
        $lowOrTemporaryRejects = $laporan->rejectLaporan->filter(function($item) {
            return in_array($item->tipe_reject, ['lct_perbaikan_low', 'lct_perbaikan_temporary']);
        });
        
        $budgetApprovalRejects = $laporan->rejectLaporan->filter(function($item) {
            return $item->tipe_reject === 'budget_approval';
        });        

        return view('pages.admin.progress-perbaikan.show', compact('laporan', 'bukti_temuan', 'tindakan_perbaikan', 'allTasksCompleted', 'lowOrTemporaryRejects', 'budgetApprovalRejects','kategoriList','areaList'));
    }


    public function approveLaporan($id_laporan_lct)
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
        

        $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->first();
   
        if (!$laporan) {
            return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
        }

        try {
            DB::beginTransaction();
            Log::info('Mulai proses approval', [
                'laporan_id' => $laporan->id_laporan_lct,
                'tingkat_bahaya' => $laporan->tingkat_bahaya,
                'status_awal' => $laporan->status_lct
            ]);
        
            // Tentukan status berdasarkan tingkat bahaya
            switch ($laporan->tingkat_bahaya) {
                case 'Low':
                    Log::info('Tingkat bahaya: Low - langsung approved');
                    $laporan->status_lct = 'approved';
                    $statusLog = 'approved';
                    break;
        
                case 'Medium':
                case 'High':
        
                    if (in_array($laporan->status_lct, ['waiting_approval_temporary', 'temporary_revision'])) {
                        $laporan->status_lct = 'approved_temporary';
                        $statusLog = 'approved_temporary';
                        $laporan->approved_temporary_by_ehs = "approved";
                        $laporan->date_completion_temp = Carbon::now();
        
                    } elseif ($laporan->status_lct == 'waiting_approval_taskbudget') {
                        $laporan->status_lct = 'waiting_approval_taskbudget';
                        $statusLog = 'approved_temporary';
                        $laporan->approved_temporary_by_ehs = "approved";
                        $laporan->date_completion_temp = Carbon::now();
        
                    } elseif ($laporan->status_lct == 'taskbudget_revision') {
                        $laporan->status_lct = 'taskbudget_revision';
                        $statusLog = 'approved_temporary';
                        $laporan->approved_temporary_by_ehs = "approved";
                        $laporan->date_completion_temp = Carbon::now();

                    } elseif ($laporan->status_lct == 'approved_taskbudget') {
                        $laporan->status_lct = 'approved_taskbudget';
                        $statusLog = 'approved_temporary';
                        $laporan->approved_temporary_by_ehs = "approved";
                        $laporan->date_completion_temp = Carbon::now();
                    } elseif ($laporan->status_lct == 'waiting_approval_permanent') {
                        $laporan->status_lct = 'approved_permanent';
                        $laporan->date_completion = Carbon::now();
                        $laporan->date_completion_temp = Carbon::now();
                        $statusLog = 'approved_permanent';
        
                    } else {
                        Log::warning('Status tidak valid untuk tingkat bahaya ini', [
                            'status' => $laporan->status_lct
                        ]);
                        return redirect()->back()->with('error', 'Status tidak valid untuk tingkat bahaya tersebut.');
                    }
                    break;
        
                default:
                    Log::error('Tingkat bahaya tidak valid', [
                        'tingkat_bahaya' => $laporan->tingkat_bahaya
                    ]);
                    return redirect()->back()->with('error', 'Tingkat bahaya tidak valid.');
            }
        
            $laporan->save();
        
            // Simpan ke tabel log (RejectLaporan) sebagai histori status
            RejectLaporan::create([
                'id_laporan_lct' => $laporan->id_laporan_lct,
                'user_id' => $user->id,
                'role' => $roleName,
                'status_lct' => $statusLog,
                'alasan_reject' => null,
                'tipe_reject' => null,
            ]);
        
            DB::commit();
        
            return redirect()->back()->with('approve', 'The repair report has been successfully approved.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while approving the report.');
        }
    }


    public function rejectLaporan(Request $request, $id_laporan_lct)
    {
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $roleName = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }

        $request->validate([
            'alasan_reject' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->first();
            if (!$laporan) {
                return response()->json(['error' => 'Laporan tidak ditemukan'], 404);
            }

            $statusLama = $laporan->status_lct;
            $tipeReject = 'lct_perbaikan_unknown';

            if ($laporan->tingkat_bahaya === 'Low') {
                $laporan->status_lct = 'revision';
                $tipeReject = 'lct_perbaikan_low';
            } elseif (in_array($laporan->tingkat_bahaya, ['Medium', 'High'])) {
                // Mapping status lama ke status baru & tipe reject
                $statusMap = [
                    'waiting_approval_temporary' => ['temporary_revision', 'lct_perbaikan_temporary'],
                    'temporary_revision'         => ['temporary_revision', 'lct_perbaikan_temporary'],
                    'waiting_approval_taskbudget'=> ['waiting_approval_taskbudget', 'lct_perbaikan_temporary'],
                    'taskbudget_revision'        => ['taskbudget_revision', 'lct_perbaikan_temporary'],
                    'approved_taskbudget'        => ['approved_taskbudget', 'lct_perbaikan_temporary'],
                    'work_permanent'             => ['work_permanent', 'lct_perbaikan_temporary'],
                    'revision_permanent'         => ['revision_permanent', 'lct_perbaikan_temporary'],
                    'waiting_approval_permanent' => ['waiting_approval_permanent', 'lct_perbaikan_temporary'],
                ];

                if (!array_key_exists($statusLama, $statusMap)) {
                    return response()->json(['error' => 'Status tidak valid untuk ditolak.'], 400);
                }

                $laporan->approved_temporary_by_ehs = "revise";

                [$newStatus, $tipeReject] = $statusMap[$statusLama];
                $laporan->status_lct = $newStatus;
            } else {
                return response()->json(['error' => 'Tingkat bahaya tidak valid'], 400);
            }

            $laporan->save();

            RejectLaporan::create([
                'id_laporan_lct' => $id_laporan_lct,
                'user_id' => $user->id,
                'role' => $roleName,
                'status_lct' => $laporan->status_lct,
                'alasan_reject' => $request->alasan_reject,
                'tipe_reject' => $tipeReject,
            ]);

            $alasanRevisi = RejectLaporan::where('id_laporan_lct', $id_laporan_lct)
                ->where('tipe_reject', $tipeReject)
                ->orderBy('created_at', 'desc')
                ->get();

            DB::commit();
            return redirect()->back()->with('reject', 'The revision report has been successfully sent to the PIC.');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Terjadi kesalahan saat menolak laporan.',
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function closeLaporan($id_laporan_lct)
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
        
        $laporan = LaporanLct::where('id_laporan_lct',$id_laporan_lct)->first();

        if (!$laporan) {
            return redirect()->back()->with('error', 'Report not found.');
        }

        if(($laporan->tingkat_bahaya === 'Medium' || $laporan->tingkat_bahaya === 'High') && in_array($laporan->approved_temporary_by_ehs, ['revise', 'pending','not yet']) ){
            return redirect()->back()->with('error', 'Report has not been approved by EHS.');
        }

        $laporan->status_lct = 'closed';
        $laporan->date_closed = Carbon::now();
        
        Log::info('Sebelum simpan', ['status' => $laporan->status_lct]);
        $laporan->save();
        Log::info('sesudah simpan', ['status' => $laporan->status_lct]);

        RejectLaporan::create([
            'id_laporan_lct' => $laporan->id_laporan_lct,
            'user_id' => $user->id,
            'role' => $roleName,
            'status_lct' => 'closed',
            'alasan_reject' => null,
            'tipe_reject' => null,
        ]);

        // try {
        //     Mail::to('efdika1102@gmail.com')->send(new CloseNotification($laporan));
        //     Log::info('Email berhasil dikirim.');
        // } catch (\Exception $mailException) {
        //     Log::error('Gagal mengirim email', ['error' => $mailException->getMessage()]);
        //     return redirect()->back()->with('error', 'Email gagal dikirim. Namun data sudah tersimpan.');
        // }

        return redirect()->back()->with('closed', 'The repair report has been successfully approved.');
    }

    public function history($id_laporan_lct)
    {
        $history = RejectLaporan::with('laporanLct','user')->where('id_laporan_lct', $id_laporan_lct)->get();

        return view('pages.admin.history.index', compact('history','id_laporan_lct'));
    }
    

    public function chartFindings(Request $request)
    {
        // Ambil user dan role
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $role = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            // Ambil dari session terlebih dahulu, fallback ke relasi jika tidak ada
            $role = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }
        

        // Ambil query terfilter
        $query = $this->buildLaporanQuery($request, $user, $role);

        $results = $query->get();

        // Tentukan format groupBy
        $groupBy = $request->input('groupBy', 'date');
        $format = 'Y-m-d'; // default per tanggal

        switch ($groupBy) {
            case 'month':
                $format = 'Y-m';
                break;
            case 'year':
                $format = 'Y';
                break;
            case 'week':
                $format = 'o-\WW'; // ISO week format
                break;
            case 'date':
            default:
                $format = 'Y-m-d';
                break;
        }


        // Group & hitung jumlah
        $grouped = $results->groupBy(function ($item) use ($format) {
            return \Carbon\Carbon::parse($item->tanggal_temuan)->format($format);
        });

        $labels = [];
        $data = [];

        foreach ($grouped as $label => $items) {
            $labels[] = $label;
            $data[] = count($items);
        }

        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    
    public function chartStatus(Request $request)
    {
        $query = LaporanLct::query();
    
        if ($request->filled('statusLct')) {
            $statuses = explode(',', $request->statusLct);
            $today = now();
    
            $query->where(function ($q) use ($statuses, $today) {
                // Filter status yang bukan 'overdue'
                $statusFilters = array_filter($statuses, fn($s) => strtolower($s) !== 'overdue');
    
                if (count($statusFilters) > 0) {
                    $q->whereIn('status_lct', $statusFilters);
                }
    
                if (in_array('overdue', array_map('strtolower', $statuses))) {
                    // Filter overdue berdasarkan kondisi tanggal & completion
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
    
        // Filter lainnya tetap sama
        if ($request->filled('riskLevel')) {
            $query->where('tingkat_bahaya', $request->riskLevel);
        }
    
        if ($request->filled('departemenId')) {
            $query->where('departemen_id', $request->departemenId);
        }
    
        if ($request->filled('categoryId')) {
            $query->where('kategori_id', $request->categoryId);
        }
    
        if ($request->filled('areaId')) {
            $query->where('area_id', $request->areaId);
        }
    
        if ($request->filled('tanggalAwal') && $request->filled('tanggalAkhir')) {
            $query->whereBetween('created_at', [
                $request->tanggalAwal . ' 00:00:00',
                $request->tanggalAkhir . ' 23:59:59',
            ]);
        }
    
        $records = $query->get();
        $today = now();
    
        // Hitung chart seperti yang kamu sudah buat, dengan logika overdue di foreach
        $chartCounts = [
            'Open' => 0,
            'Closed' => 0,
            'In Progress' => 0,
            'Overdue' => 0,
        ];
    
        foreach ($records as $item) {
            $status = strtolower($item->status_lct);
            $bahaya = strtolower($item->tingkat_bahaya);
            $completion = $item->date_completion;
            $completionTemp = $item->date_completion_temp;
            $due = $item->due_date;
            $dueTemp = $item->due_date_temp;
            $duePerm = $item->due_date_perm;
    
            $isOverdue = false;
            if ($bahaya === 'low' && is_null($completion) && $due && $today->gt($due)) {
                $isOverdue = true;
            } elseif (in_array($bahaya, ['medium', 'high'])) {
                if (is_null($dueTemp) && $due && $today->gt($due)) {
                    $isOverdue = true;
                } elseif (!is_null($dueTemp) && is_null($duePerm) && $today->gt($dueTemp) && is_null($completionTemp)) {
                    $isOverdue = true;
                } elseif (!is_null($duePerm) && is_null($completion) && $today->gt($duePerm)) {
                    $isOverdue = true;
                }
            }
    
            if ($isOverdue) {
                $chartCounts['Overdue']++;
            } else {
                if ($status === 'open') {
                    $chartCounts['Open']++;
                } elseif ($status === 'closed') {
                    $chartCounts['Closed']++;
                } else {
                    $chartCounts['In Progress']++;
                }
            }
        }
    
        return response()->json([
            'labels' => ['Open', 'Closed', 'In Progress', 'Overdue'],
            'data' => [
                $chartCounts['Open'],
                $chartCounts['Closed'],
                $chartCounts['In Progress'],
                $chartCounts['Overdue'],
            ],
        ]);
    }
    


    public function chartCategory(Request $request)
    {
        $query = DB::table('lct_laporan as l')
            ->join('lct_kategori as k', 'l.kategori_id', '=', 'k.id')
            ->whereNull('l.deleted_at')
            ->whereNull('k.deleted_at');

        if ($request->filled('statusLct')) {
            $statuses = explode(',', $request->statusLct);
            $today = now();

            $query->where(function ($q) use ($statuses, $today) {
                $statusFilters = array_filter($statuses, fn($s) => strtolower($s) !== 'overdue');

                if (count($statusFilters) > 0) {
                    $q->whereIn('l.status_lct', $statusFilters);
                }

                if (in_array('overdue', array_map('strtolower', $statuses))) {
                    $q->orWhere(function ($sub) use ($today) {
                        $sub->where(function ($low) use ($today) {
                            $low->where('l.tingkat_bahaya', 'Low')
                                ->whereDate('l.due_date', '<', $today)
                                ->whereNull('l.date_completion');
                        })
                        ->orWhere(function ($mediumHighTemp) use ($today) {
                            $mediumHighTemp->whereIn('l.tingkat_bahaya', ['Medium', 'High'])
                                ->whereDate('l.due_date_temp', '<', $today)
                                ->whereNull('l.date_completion_temp');
                        })
                        ->orWhere(function ($mediumHighPerm) use ($today) {
                            $mediumHighPerm->whereIn('l.tingkat_bahaya', ['Medium', 'High'])
                                ->whereDate('l.due_date_perm', '<', $today)
                                ->whereNull('l.date_completion');
                        });
                    });
                }
            });
        }

        if ($request->filled('riskLevel')) {
            $query->where('l.tingkat_bahaya', $request->riskLevel);
        }

        if ($request->filled('departemenId')) {
            $query->where('l.departemen_id', $request->departemenId);
        }

        if ($request->filled('categoryId')) {
            $query->where('l.kategori_id', $request->categoryId);
        }

        if ($request->filled('areaId')) {
            $query->where('l.area_id', $request->areaId);
        }

        if ($request->filled('tanggalAwal') && $request->filled('tanggalAkhir')) {
            $query->whereBetween('l.created_at', [
                $request->tanggalAwal . ' 00:00:00',
                $request->tanggalAkhir . ' 23:59:59',
            ]);
        }

        $categoryCounts = $query
            ->select('k.nama_kategori', DB::raw('COUNT(*) as total'))
            ->groupBy('k.nama_kategori')
            ->pluck('total', 'k.nama_kategori')
            ->toArray();

        $categories = Kategori::whereNull('deleted_at')
            ->pluck('nama_kategori')
            ->toArray();

        $categoryDisplayNames = array_map(function ($name) {
            return match ($name) {
                '5S (Seiri, Seiton, Seiso, Seiketsu, dan Shitsuke)' => '5S',
                'Unsafe Condition' => 'Unsafe Condition',
                'Unsafe Act' => 'Unsafe Act',
                'Near miss' => 'Nearmiss',
                default => $name,
            };
        }, $categories);

        $data = [];
        foreach ($categories as $category) {
            $data[] = $categoryCounts[$category] ?? 0;
        }

        return response()->json([
            'labels' => $categoryDisplayNames,
            'data' => $data,
        ]);
    }


    public function chartArea(Request $request)
    {
        // Ambil semua area aktif (tidak dihapus)
        $areas = AreaLct::whereNull('deleted_at')
            ->orderBy('nama_area')
            ->pluck('nama_area', 'id');

        // Mulai query laporan
        $query = DB::table('lct_laporan as l')
            ->whereNull('l.deleted_at')
            ->select('l.area_id', DB::raw('COUNT(*) as total'))
            ->whereIn('l.area_id', $areas->keys());

        // Terapkan filter statusLct dengan overdue support
        if ($request->filled('statusLct')) {
            $statuses = explode(',', $request->statusLct);
            $today = now();

            $query->where(function ($q) use ($statuses, $today) {
                $statusFilters = array_filter($statuses, fn($s) => strtolower($s) !== 'overdue');

                if (count($statusFilters) > 0) {
                    $q->whereIn('l.status_lct', $statusFilters);
                }

                if (in_array('overdue', array_map('strtolower', $statuses))) {
                    $q->orWhere(function ($sub) use ($today) {
                        $sub->where(function ($low) use ($today) {
                            $low->where('l.tingkat_bahaya', 'Low')
                                ->whereDate('l.due_date', '<', $today)
                                ->whereNull('l.date_completion');
                        })
                        ->orWhere(function ($mediumHighTemp) use ($today) {
                            $mediumHighTemp->whereIn('l.tingkat_bahaya', ['Medium', 'High'])
                                ->whereDate('l.due_date_temp', '<', $today)
                                ->whereNull('l.date_completion_temp');
                        })
                        ->orWhere(function ($mediumHighPerm) use ($today) {
                            $mediumHighPerm->whereIn('l.tingkat_bahaya', ['Medium', 'High'])
                                ->whereDate('l.due_date_perm', '<', $today)
                                ->whereNull('l.date_completion');
                        });
                    });
                }
            });
        }

        if ($request->filled('riskLevel')) {
            $query->where('l.tingkat_bahaya', $request->riskLevel);
        }

        if ($request->filled('departemenId')) {
            $query->where('l.departemen_id', $request->departemenId);
        }

        if ($request->filled('categoryId')) {
            $query->where('l.kategori_id', $request->categoryId);
        }

        if ($request->filled('areaId')) {
            $query->where('l.area_id', $request->areaId);
        }

        if ($request->filled('tanggalAwal') && $request->filled('tanggalAkhir')) {
            $query->whereBetween('l.created_at', [
                $request->tanggalAwal . ' 00:00:00',
                $request->tanggalAkhir . ' 23:59:59',
            ]);
        }

        // Group dan ambil hasil
        $counts = $query->groupBy('l.area_id')
            ->pluck('total', 'l.area_id')
            ->toArray();

        // Susun hasil sesuai urutan area
        $labels = [];
        $data = [];

        foreach ($areas as $id => $nama) {
            $labels[] = $nama;
            $data[] = $counts[$id] ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }


    public function chartDepartment(Request $request)
    {
        $departments = LctDepartement::whereNull('deleted_at')
            ->orderBy('nama_departemen')
            ->pluck('nama_departemen', 'id');

        $query = DB::table('lct_laporan as l')
            ->whereNull('l.deleted_at')
            ->select('l.departemen_id', DB::raw('COUNT(*) as total'))
            ->whereIn('l.departemen_id', $departments->keys());

        // Terapkan filter statusLct dengan overdue
        if ($request->filled('statusLct')) {
            $statuses = explode(',', $request->statusLct);
            $today = now();

            $query->where(function ($q) use ($statuses, $today) {
                $statusFilters = array_filter($statuses, fn($s) => strtolower($s) !== 'overdue');

                if (count($statusFilters) > 0) {
                    $q->whereIn('l.status_lct', $statusFilters);
                }

                if (in_array('overdue', array_map('strtolower', $statuses))) {
                    $q->orWhere(function ($sub) use ($today) {
                        $sub->where(function ($low) use ($today) {
                            $low->where('l.tingkat_bahaya', 'Low')
                                ->whereDate('l.due_date', '<', $today)
                                ->whereNull('l.date_completion');
                        })
                        ->orWhere(function ($mediumHighTemp) use ($today) {
                            $mediumHighTemp->whereIn('l.tingkat_bahaya', ['Medium', 'High'])
                                ->whereDate('l.due_date_temp', '<', $today)
                                ->whereNull('l.date_completion_temp');
                        })
                        ->orWhere(function ($mediumHighPerm) use ($today) {
                            $mediumHighPerm->whereIn('l.tingkat_bahaya', ['Medium', 'High'])
                                ->whereDate('l.due_date_perm', '<', $today)
                                ->whereNull('l.date_completion');
                        });
                    });
                }
            });
        }

        if ($request->filled('riskLevel')) {
            $query->where('l.tingkat_bahaya', $request->riskLevel);
        }

        if ($request->filled('departemenId')) {
            $query->where('l.departemen_id', $request->departemenId);
        }

        if ($request->filled('categoryId')) {
            $query->where('l.kategori_id', $request->categoryId);
        }

        if ($request->filled('areaId')) {
            $query->where('l.area_id', $request->areaId);
        }

        if ($request->filled('tanggalAwal') && $request->filled('tanggalAkhir')) {
            $query->whereBetween('l.created_at', [
                $request->tanggalAwal . ' 00:00:00',
                $request->tanggalAkhir . ' 23:59:59',
            ]);
        }

        // Ambil hasil yang memiliki departemen_id
        $result = $query->groupBy('l.departemen_id')
            ->pluck('total', 'l.departemen_id')
            ->toArray();

        // Hitung jumlah laporan yang belum memiliki departemen_id (null)
        $unassignedQuery = DB::table('lct_laporan as l')
            ->whereNull('l.deleted_at')
            ->whereNull('l.departemen_id');

        // Terapkan filter yang sama ke $unassignedQuery
        if ($request->filled('statusLct')) {
            $statuses = explode(',', $request->statusLct);
            $today = now();

            $unassignedQuery->where(function ($q) use ($statuses, $today) {
                $statusFilters = array_filter($statuses, fn($s) => strtolower($s) !== 'overdue');

                if (count($statusFilters) > 0) {
                    $q->whereIn('l.status_lct', $statusFilters);
                }

                if (in_array('overdue', array_map('strtolower', $statuses))) {
                    $q->orWhere(function ($sub) use ($today) {
                        $sub->where(function ($low) use ($today) {
                            $low->where('l.tingkat_bahaya', 'Low')
                                ->whereDate('l.due_date', '<', $today)
                                ->whereNull('l.date_completion');
                        })
                        ->orWhere(function ($mediumHighTemp) use ($today) {
                            $mediumHighTemp->whereIn('l.tingkat_bahaya', ['Medium', 'High'])
                                ->whereDate('l.due_date_temp', '<', $today)
                                ->whereNull('l.date_completion_temp');
                        })
                        ->orWhere(function ($mediumHighPerm) use ($today) {
                            $mediumHighPerm->whereIn('l.tingkat_bahaya', ['Medium', 'High'])
                                ->whereDate('l.due_date_perm', '<', $today)
                                ->whereNull('l.date_completion');
                        });
                    });
                }
            });
        }

        if ($request->filled('riskLevel')) {
            $unassignedQuery->where('l.tingkat_bahaya', $request->riskLevel);
        }

        if ($request->filled('categoryId')) {
            $unassignedQuery->where('l.kategori_id', $request->categoryId);
        }

        if ($request->filled('areaId')) {
            $unassignedQuery->where('l.area_id', $request->areaId);
        }

        if ($request->filled('tanggalAwal') && $request->filled('tanggalAkhir')) {
            $unassignedQuery->whereBetween('l.created_at', [
                $request->tanggalAwal . ' 00:00:00',
                $request->tanggalAkhir . ' 23:59:59',
            ]);
        }

        $unassignedCount = $unassignedQuery->count();

        // Susun hasil berdasarkan urutan nama departemen
        $labels = [];
        $data = [];

        foreach ($departments as $id => $name) {
            $labels[] = $name;
            $data[] = $result[$id] ?? 0;
        }

        // Tambahkan batang untuk Unassigned
        if ($unassignedCount > 0) {
            $labels[] = 'Unassigned';
            $data[] = $unassignedCount;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }




    private function buildLaporanQuery(Request $request, $user, $role)
    {
        $query = LaporanLct::query();

        $today = now();

        // === Filter by Role ===
        switch ($role) {
            case 'user':
                $query->where('lct_laporan.user_id', $user->id);
                break;
            case 'manajer':
                $departemenId = \App\Models\LctDepartement::where('user_id', $user->id)->value('id');
                $query->where('departemen_id', $departemenId);
                break;
            default:
                if (!in_array($role, ['ehs'])) {
                    $picId = \App\Models\Pic::where('user_id', $user->id)->value('id');
                    $query->where('pic_id', $picId);
                }
        }

        // === Filter Risk Level ===
        if ($request->filled('riskLevel')) {
            $query->where('tingkat_bahaya', $request->riskLevel);
        }

        // === Filter Status LCT & Overdue ===
        if ($request->filled('statusLct')) {
            $statuses = explode(',', $request->statusLct);
            $statusFilters = array_filter($statuses, fn($s) => strtolower($s) !== 'overdue');
            $includeOverdue = in_array('overdue', array_map('strtolower', $statuses));

            $query->where(function ($q) use ($statusFilters, $includeOverdue, $today) {
                // Status selain overdue
                if (count($statusFilters) > 0) {
                    $q->whereIn('status_lct', $statusFilters);
                }

                // Status overdue
                if ($includeOverdue) {
                    $q->orWhere(function ($subQuery) use ($today) {
                        // Hilangkan syarat first_overdue_date sementara agar semua overdue masuk
                        // $subQuery->whereNotNull('first_overdue_date')

                        $subQuery->where('status_lct', '!=', 'closed')
                            ->where(function ($inner) use ($today) {
                                $inner->where(function ($low) use ($today) {
                                        $low->where('tingkat_bahaya', 'Low')
                                            ->whereDate('due_date', '<', $today)
                                            ->whereNull('date_completion');
                                    })
                                    ->orWhere(function ($temp) use ($today) {
                                        $temp->whereIn('tingkat_bahaya', ['Medium', 'High'])
                                            ->whereDate('due_date_temp', '<', $today)
                                            ->whereNull('date_completion_temp');
                                    })
                                    ->orWhere(function ($perm) use ($today) {
                                        $perm->whereIn('tingkat_bahaya', ['Medium', 'High'])
                                            ->whereDate('due_date_perm', '<', $today)
                                            ->whereNull('date_completion');
                                    });
                            });
                    });
                }
            });

            // Debug log
            Log::debug('Filtered status_lct values:', ['statusLct' => $request->statusLct]);
            Log::debug('Current SQL for status_lct filtering:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings(),
            ]);
        }


        // === Filter Tanggal Temuan ===
        if ($request->filled('tanggalAwal') && $request->filled('tanggalAkhir')) {
            $startDate = \Carbon\Carbon::parse($request->tanggalAwal)->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->tanggalAkhir)->endOfDay();
            $query->whereBetween('tanggal_temuan', [$startDate, $endDate]);
        }

        // === Filter Tambahan ===
        if ($request->filled('departemenId')) {
            $query->where('departemen_id', $request->departemenId);
        }

        if ($request->filled('categoryId')) {
            $query->where('kategori_id', $request->categoryId);
        }

        if ($request->filled('areaId')) {
            $query->where('area_id', $request->areaId);
        }

        // === Optional Group By (Hanya pengaturan format, belum digunakan) ===
        $groupByFormat = null;
        if ($request->filled('groupBy')) {
            $groupByFormat = match ($request->groupBy) {
                'daily' => '%Y-%m-%d',
                'monthly' => '%Y-%m',
                'yearly' => '%Y',
                default => null
            };
        }

        $query->with([
            'area',
            'kategori',
            'picc.user'
        ]);

        if ($request->overdue == 'true') {
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();
        
            $query->whereBetween('lct_laporan.created_at', [$startOfMonth, $endOfMonth])
                  ->where('status_lct', '!=', 'closed')
                  ->where(function ($q) {
                      $q->where(function ($qq) {
                          $qq->where('tingkat_bahaya', 'Low')
                             ->whereNull('date_completion')
                             ->whereDate('due_date', '<', now());
                      })->orWhere(function ($qq) {
                          $qq->whereIn('tingkat_bahaya', ['Medium', 'High'])
                             ->whereNull('date_completion')
                             ->whereDate('due_date', '<', now());
                      });
                  });
        }
        
        return $query;
    }



    public function getPaginatedData(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $laporans = LaporanLct::latest()->paginate($perPage);

        return response()->json([
            'data' => view('partials.tabel-reporting', compact('laporans'))->render(),
        ]);
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

    public function exportPpt(Request $request)
    {
        $statusMapping = [
            'open' => ['label' => 'Open (new)'],
            'review' => ['label' => 'Under Review'],
            'in_progress' => ['label' => 'In Progress'],
            'progress_work' => ['label' => 'In Progress'],
            'work_permanent' => ['label' => 'In Progress'],
            'waiting_approval' => ['label' => 'Waiting Approval'],
            'waiting_approval_temporary' => ['label' => 'Waiting Approval (temporary)'],
            'waiting_approval_permanent' => ['label' => 'Waiting Approval'],
            'waiting_approval_taskbudget' => ['label' => 'Waiting Approval (budget)'],
            'approved' => ['label' => 'Approved'],
            'approved_temporary' => ['label' => 'Approved (temporary)'],
            'approved_permanent' => ['label' => 'Approved'],
            'approved_taskbudget' => ['label' => 'Approved (budget)'],
            'revision' => ['label' => 'Revision'],
            'temporary_revision' => ['label' => 'Revision (temporary)'],
            'permanent_revision' => ['label' => 'Revision'],
            'taskbudget_revision' => ['label' => 'Revision (budget)'],
            'closed' => ['label' => 'Closed'],
        ];
        try {
           // Ambil user dan role
            if (Auth::guard('ehs')->check()) {
                $user = Auth::guard('ehs')->user();
                $role = 'ehs';
            } else {
                $user = Auth::guard('web')->user();
                $role = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
            }

            // Gunakan buildLaporanQuery agar query sama dengan Excel
            $query = $this->buildLaporanQuery($request, $user, $role);
            $laporans = $query->with('picUser', 'kategori', 'area')->get();

            $ppt = new PhpPresentation();
            
            foreach ($laporans as $laporan) {

                // BUAT SLIDE BARU
                $slide = $ppt->createSlide();

                // --- JUDUL ---
                $title = $slide->createRichTextShape()
                    ->setHeight(50)
                    ->setWidth(400)
                    ->setOffsetX(20)
                    ->setOffsetY(20);
                $title->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $run = $title->createTextRun('SAFETY PATROL REPORT');
                $run->getFont()->setBold(true)->setSize(24)->setColor(new Color(Color::COLOR_BLACK));

                // --- TABEL KIRI ---
                $tableLeft = new Table(2);
                $tableLeft->setWidth(460)->setOffsetX(20)->setOffsetY(80);
                $col1Width = 140; // Label
                $col2Width = 320; // Value

                $observationDateRaw = $laporan->tanggal_temuan ?? $laporan->created_at;
                $observationDate = Carbon::parse($observationDateRaw)->format('F d, Y');

                $leftData = [
                    "No LCT" => $laporan->id_laporan_lct,
                    "Observation No/Location" => $laporan->area->nama_area ?? '-' . ' ('.$laporan->detail_area .')' ?? '-',
                    "Observation date" => $observationDate,
                    "Responsibility (PIC)" => $laporan->picUser->fullname ?? '-',
                    "Risk Hazard" => $laporan->tingkat_bahaya ?? '-',
                    "Finding Item" => $laporan->temuan_ketidaksesuaian ?? '-',
                ];

                foreach ($leftData as $header => $value) {
                    $row = $tableLeft->createRow();
                    $row->setHeight(30);

                    $cell1 = $row->getCell(0);
                    $cell1->setWidth($col1Width);
                    $cell1->createTextRun('  ' . $header);

                    $cell2 = $row->getCell(1);
                    $cell2->setWidth($col2Width);
                    $cell2->createTextRun('  ' . $value);
                }

                $slide->addShape($tableLeft);

                // --- TABEL KANAN ---
                $tableRight = new Table(2);
                $tableRight->setWidth(460)->setOffsetX(490)->setOffsetY(30);

                // Tentukan label dan value untuk Due Date
                $dueDateLabel = 'Due Date';
                $dueDateLabelCompletion = 'Date of Completion';
                if (in_array($laporan->tingkat_bahaya, ['High', 'Medium'])) {
                    $dueDateLabel = 'Due Date (Temporary)';
                    $dueDateLabelCompletion = 'Date of Completion (Temporary)';
                }
                $dueDateValue = $laporan->due_date ? Carbon::parse($laporan->due_date)->format('d F Y') : '-';


                // Tentukan Date of Completion Permanent
                $dateCompletionPermanent = '-';
                if (in_array($laporan->tingkat_bahaya, ['High', 'Medium'])) {
                    $dateCompletionPermanent = $laporan->date_completion_perm ? Carbon::parse($laporan->date_completion_perm)->format('d F Y') : '-';
                }

                $dateCompletionPermanent = $laporan->date_completion ? Carbon::parse($laporan->date_completion)->format('d F Y') : '-';
                // Susun array rightData
                $rightData = [
                    "Category" => $laporan->kategori->nama_kategori ?? '-',
                    $dueDateLabel => $dueDateValue,
                    $dueDateLabelCompletion => $dateCompletionPermanent ?? '-',
                    "Status" => $statusMapping[$laporan->status_lct]['label'] ?? '-',
                    "Recommendation" => $laporan->rekomendasi ?? '-',
                    "Action Permanent (if medium & high risk)" => $laporan->action_permanent ?? '-',
                    "Date of Completion Permanent" => $dateCompletionPermanent,
                ];
                
                foreach ($rightData as $header => $value) {
                    $row = $tableRight->createRow();
                    $row->setHeight(30);

                    $cell1 = $row->getCell(0);
                    $cell1->setWidth($col1Width);
                    $cell1->createTextRun('  ' . $header);

                    $cell2 = $row->getCell(1);
                    $cell2->setWidth($col2Width);
                    $cell2->createTextRun('  ' . $value);
                }

                $slide->addShape($tableRight);

                // --- TABEL BAWAH (BEFORE / AFTER) ---
                $tableBottom = new Table(2);
                $tableBottom->setWidth(920)->setOffsetX(20)->setOffsetY(325);

                $headerRow = $tableBottom->createRow();
                $headerRow->setHeight(40);

                $beforeCell = $headerRow->getCell(0);
                $beforeCell->createTextRun(' BEFORE');

                $afterCell = $headerRow->getCell(1);
                $afterCell->createTextRun(' AFTER');

                $imageRow = $tableBottom->createRow();
                $imageRow->setHeight(320);

                // Pastikan path benar
                $bukti = is_array($laporan->bukti_temuan)
                    ? $laporan->bukti_temuan
                    : json_decode($laporan->bukti_temuan, true);
                $firstBuktiTemuan = $bukti[0] ?? null;
                $beforeCellImg = $imageRow->getCell(0);
                if ($firstBuktiTemuan) {
                    $beforePath = storage_path('app/public/' . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $firstBuktiTemuan));

                    if (file_exists($beforePath)) {
                        $img = new DrawingFile();
                        $img->setPath($beforePath)
                            ->setWidth(320)
                            ->setHeight(200)
                            ->setOffsetX(70)  
                            ->setOffsetY(420); 

                        $slide->addShape($img);  // Bukan ke cell, tapi ke slide
                    } else {
                        $beforeCellImg->createTextRun('Before Image Not Found');
                    }
                }

                // Decode JSON tindakan_perbaikan
                $tindakanPerbaikan = is_array($laporan->tindakan_perbaikan)
                    ? $laporan->tindakan_perbaikan
                    : json_decode($laporan->tindakan_perbaikan, true);

                // Default jika tidak ada
                $afterPath = null;

                // Cek jika array valid dan ada data
                if (is_array($tindakanPerbaikan) && !empty($tindakanPerbaikan)) {
                    // Ambil tindakan paling baru (terakhir)
                    $latestTindakan = end($tindakanPerbaikan);
                    
                    if (isset($latestTindakan['bukti'][0])) {
                        // Path file
                        $afterPath = storage_path('app/public/' . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $latestTindakan['bukti'][0]));
                    }
                }

                // Tambahkan ke cell
                $afterCellImg = $imageRow->getCell(1);
                if ($afterPath && file_exists($afterPath)) {
                    $img = new DrawingFile();
                    $img->setPath($afterPath)
                            ->setWidth(320)
                            ->setHeight(200)
                            ->setOffsetX(530)  
                            ->setOffsetY(420);
                    $slide->addShape($img); // Perlu ditambahkan ke slide, karena Cell tidak mendukung addShape
                } else {
                    $afterCellImg->createTextRun('After Image Not Found');
                }

                $slide->addShape($tableBottom);
            }

            // EXPORT
            $writer = IOFactory::createWriter($ppt, 'PowerPoint2007');
            $fileName = 'laporan_safety_patrol_' . date('Ymd_His') . '.pptx';
            $filePath = storage_path('app/public/' . $fileName);

            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (Exception $e) {
            \Log::error('PowerPoint Export Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PowerPoint file: ' . $e->getMessage()], 500);
        }
    }

    public function getRevisiData(Request $request)
    {
        $id = $request->id_laporan_lct;
        $perPage = $request->input('per_page', 5);
        $laporan = LaporanLct::with(['rejectLaporan'])->findOrFail($id);

        $revisions = $laporan->rejectLaporan
            ->filter(fn($item) => !empty($item->alasan_reject))
            ->values();

        $tindakan = $laporan->tindakan_perbaikan ?? [];
        $combined = $revisions->map(function ($rev, $index) use ($tindakan) {
            return [
                'rev' => $rev,
                'tindakan' => $tindakan[$index + 1] ?? null,
            ];
        })->reverse()->values();

        $page = $request->input('page', 1);
        $paginated = $combined->slice(($page - 1) * $perPage, $perPage)->values();
        $totalPages = ceil($combined->count() / $perPage);

        $result = $paginated->map(function ($item, $i) use ($page) {
            $index = ($page - 1) * request('per_page', 5) + $i;

            $html = view('partials.table-revise', [
                'item' => $item,
                'i' => $index
            ])->render();

            return [
                'html' => $html
            ];
        });

        return response()->json([
            'data' => $result,
            'total_pages' => $totalPages,
        ]);
    }



}
