<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\LaporanLct;
use Illuminate\Http\Request;
use App\Models\RejectLaporan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\AreaLct;
use App\Models\Kategori;
use App\Models\LctDepartement;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanLctExport;

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
            // Ambil dari session terlebih dahulu, fallback ke relasi jika tidak ada
            $role = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }
        

        // Data dropdown filter
        $departments = \App\Models\LctDepartement::whereNull('deleted_at')->pluck('nama_departemen', 'id');
        $areas = \App\Models\AreaLct::whereNull('deleted_at')->pluck('nama_area', 'id');
        $categories = \App\Models\Kategori::whereNull('deleted_at')->pluck('nama_kategori', 'id');

        $statusGroups = [
            'Open' => ['open'],
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
        $query = $this->buildLaporanQuery($request, $user, $role);
        $query->select('*', DB::raw("CASE WHEN status_lct = 'closed' THEN 1 ELSE 0 END as order_type"));

        // dd(
        //     $query->toSql(),
        //     $query->getBindings(),
        //     $query->count()
        // );
        

        $perPage = $request->input('perPage', 10);

        $laporans = $query
            ->orderBy('order_type')
            ->orderByDesc('updated_at')
            ->paginate($perPage)
            ->withQueryString();


        // ================== TAMBAHAN UNTUK DATA GRAFIK ==================

        // Query base untuk statistik, sama filter seperti query utama tapi tanpa paginate dan filter open
        $baseQuery = $this->buildLaporanQuery($request, $user, $role);

        $availableYears = LaporanLct::selectRaw('YEAR(tanggal_temuan) as year')
        ->distinct()
        ->orderByDesc('year')
        ->pluck('year');
    
    

        if ($request->ajax()) {
            // Ini penting! Return partial yang hanya bagian isi
            return view('partials.tabel-reporting-wrapper', compact('laporans'))->render();
        }
        
        return view('pages.admin.progress-perbaikan.index', [
            'laporans' => $laporans,
            'statusGroups' => $statusGroups,
            'areas' => $areas,
            'departments' => $departments,
            'categories' => $categories,
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

        $allTasksCompleted = $tasks->isNotEmpty() && $tasks->every(fn($task) => $task->status === 'completed');

        if (
            $allTasksCompleted &&
            $laporan->status_lct !== 'approved_permanent' &&
            $laporan->status_lct !== 'closed'
        ) {
            $laporan->status_lct = 'waiting_approval_permanent';
            $laporan->save();
        }
        
        
        return view('pages.admin.progress-perbaikan.show', compact('laporan', 'bukti_temuan', 'tindakan_perbaikan', 'allTasksCompleted'));
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
                        $laporan->approved_temporary_by_ehs = true;
                        $laporan->date_completion_temp = Carbon::now();
        
                    } elseif ($laporan->status_lct == 'waiting_approval_taskbudget') {
                        $laporan->status_lct = 'waiting_approval_taskbudget';
                        $statusLog = 'approved_temporary';
                        $laporan->approved_temporary_by_ehs = true;
                        $laporan->date_completion_temp = Carbon::now();
        
                    } elseif ($laporan->status_lct == 'taskbudget_revision') {
                        $laporan->status_lct = 'taskbudget_revision';
                        $statusLog = 'approved_temporary';
                        $laporan->approved_temporary_by_ehs = true;
                        $laporan->date_completion_temp = Carbon::now();

                    } elseif ($laporan->status_lct == 'approved_taskbudget') {
                        $laporan->status_lct = 'approved_taskbudget';
                        $statusLog = 'approved_temporary';
                        $laporan->approved_temporary_by_ehs = true;
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
                    'waiting_approval_permanent' => ['approved_permanent', 'lct_perbaikan_permanent'],
                ];

                if (!array_key_exists($statusLama, $statusMap)) {
                    return response()->json(['error' => 'Status tidak valid untuk ditolak.'], 400);
                }

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
        
        // dd("masuk close");
        $laporan = LaporanLct::where('id_laporan_lct',$id_laporan_lct)->first();

        if (!$laporan) {
            return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
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
        $groupBy = $request->input('groupBy', 'daily');
        $format = 'Y-m-d'; // default

        switch ($groupBy) {
            case 'monthly':
                $format = 'Y-m';
                break;
            case 'yearly':
                $format = 'Y';
                break;
            case 'weekly':
                $format = 'o-\WW'; // ISO week format (e.g., 2024-W23)
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
    // Ambil semua departemen aktif
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

    // Group dan ambil hasil
    $result = $query->groupBy('l.departemen_id')
        ->pluck('total', 'l.departemen_id')
        ->toArray();

    // Susun hasil berdasarkan urutan nama departemen
    $labels = [];
    $data = [];

    foreach ($departments as $id => $name) {
        $labels[] = $name;
        $data[] = $result[$id] ?? 0;
    }

    return response()->json([
        'labels' => $labels,
        'data' => $data,
    ]);
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

        if ($request->filled('riskLevel')) {
            $query->where('tingkat_bahaya', $request->riskLevel);
        }

        if ($request->filled('statusLct')) {
            $statuses = explode(',', $request->statusLct);
            $today = now();
        
            $query->where(function ($q) use ($statuses, $today) {
                // Filter out 'overdue' from the normal status filter
                $statusFilters = array_filter($statuses, fn($s) => strtolower($s) !== 'overdue');
        
                // If there are other statuses, filter by them
                if (count($statusFilters) > 0) {
                    $q->whereIn('status_lct', $statusFilters);
                }
        
                // If 'overdue' is requested, filter by first_overdue_date (non-null means overdue)
                if (in_array('overdue', array_map('strtolower', $statuses))) {
                    $q->orWhereNotNull('first_overdue_date');
                }
            });
        }        
        
        
        if ($request->filled('tanggalAwal') && $request->filled('tanggalAkhir')) {
            $startDate = \Carbon\Carbon::parse($request->tanggalAwal)->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->tanggalAkhir)->endOfDay();
            $query->whereBetween('tanggal_temuan', [$startDate, $endDate]);
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
        $groupByFormat = null;
        if ($request->filled('groupBy')) {
            switch ($request->groupBy) {
                case 'daily':
                    $groupByFormat = '%Y-%m-%d';
                    break;
                case 'monthly':
                    $groupByFormat = '%Y-%m';
                    break;
                case 'yearly':
                    $groupByFormat = '%Y';
                    break;
            }
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

}
