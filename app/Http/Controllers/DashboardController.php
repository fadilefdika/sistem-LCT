<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pic;
use App\Models\AreaLct;
use App\Models\Kategori;
use App\Models\LaporanLct;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\LctDepartement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\PicTodoService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('ehs')->check()
            ? Auth::guard('ehs')->user()
            : Auth::guard('web')->user();

        $roleName = Auth::guard('ehs')->check()
            ? 'ehs'
            : session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';

        // Fungsi bantu untuk filter sesuai role
        $applyRoleFilter = function ($query) use ($roleName, $user) {
            if ($roleName === 'pic') {
                $picId = Pic::where('user_id', $user->id)->value('id');
                return $picId ? $query->where('pic_id', $picId) : $query->whereRaw('1 = 0');
            } elseif ($roleName === 'user') {
                return $query->where('user_id', $user->id);
            } elseif ($roleName === 'manajer') {
                $deptId = LctDepartement::where('user_id', $user->id)->value('id');
                return $deptId ? $query->where('departemen_id', $deptId) : $query->whereRaw('1 = 0');
            }
            return $query;
        };

        // Fungsi bantu ambil laporan overdue
        $ambilOverdue = function () use ($applyRoleFilter) {
            $baseQuery = LaporanLct::where('due_date', '<', now())
                ->where('status_lct', '!=', 'closed')
                ->where(fn ($q) => $q->whereNull('date_completion')->orWhereColumn('date_completion', '>', 'due_date'));

            $baseQuery = $applyRoleFilter($baseQuery);

            $priority = (clone $baseQuery)
                ->whereIn('tingkat_bahaya', ['high', 'medium'])
                ->orderByRaw("
                    CASE 
                        WHEN tingkat_bahaya = 'high' THEN 1
                        WHEN tingkat_bahaya = 'medium' THEN 2
                        WHEN tingkat_bahaya = 'low' THEN 3
                        ELSE 4
                    END
                ")
                ->orderBy('due_date')
                ->take(5)
                ->get();

            $remaining = 5 - $priority->count();

            if ($remaining > 0) {
                $excludedIds = $priority->pluck('id')->toArray();
                $low = (clone $baseQuery)
                    ->where('tingkat_bahaya', 'low')
                    ->whereNotIn('id', $excludedIds)
                    ->orderBy('due_date')
                    ->take($remaining)
                    ->get();
                return $priority->concat($low);
            }

            return $priority;
        };

        // ============================
        // Data & Query Dasar
        // ============================

        $findings = LaporanLct::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year');

        $categories = Kategori::all();
        $categoryAliases = $categories->mapWithKeys(fn ($item) => [
            $item->nama_kategori => $item->nama_kategori === '5S (Seiri, Seiton, Seiso, Seiketsu, dan Shitsuke)' ? '5S' : $item->nama_kategori
        ])->toArray();

        $categoryStatusCounts = DB::table('lct_laporan')
            ->join('lct_kategori', 'lct_laporan.kategori_id', '=', 'lct_kategori.id')
            ->select(
                'lct_kategori.nama_kategori',
                DB::raw("SUM(CASE WHEN status_lct = 'closed' THEN 1 ELSE 0 END) as closed_count"),
                DB::raw("SUM(CASE WHEN status_lct != 'closed' THEN 1 ELSE 0 END) as non_closed_count")
            )
            ->groupBy('lct_kategori.nama_kategori')
            ->get()->keyBy('nama_kategori');

        $areaStatusCounts = DB::table('lct_area')
            ->leftJoin('lct_laporan', 'lct_area.id', '=', 'lct_laporan.area_id')
            ->select(
                'lct_area.nama_area',
                DB::raw("SUM(CASE WHEN lct_laporan.status_lct = 'closed' THEN 1 ELSE 0 END) as closed_count"),
                DB::raw("SUM(CASE WHEN lct_laporan.status_lct IS NOT NULL AND lct_laporan.status_lct != 'closed' THEN 1 ELSE 0 END) as non_closed_count")
            )
            ->groupBy('lct_area.nama_area')
            ->get()->keyBy('nama_area');

        $statusCounts = [
            'open' => LaporanLct::where('status_lct', 'open')->count(),
            'close' => LaporanLct::where('status_lct', 'closed')->count(),
            'in_progress' => LaporanLct::whereNotIn('status_lct', ['open', 'closed'])->count(),
        ];

        // ============================
        // Query Berdasarkan Role
        // ============================

        $laporanMediumHighQuery = $applyRoleFilter(
            LaporanLct::whereIn('tingkat_bahaya', ['Medium', 'High'])->where('status_lct', '!=', 'closed')
        );

        $laporanLowQuery = $applyRoleFilter(
            LaporanLct::where('tingkat_bahaya', 'Low')->where('status_lct', '!=', 'closed')
        );

        $laporanNewQuery = $applyRoleFilter(
            LaporanLct::where('status_lct', 'open')
        );

        $laporanNeedApprovalQuery = $applyRoleFilter(
            LaporanLct::whereIn('status_lct', [
                'waiting_approval', 'waiting_approval_temporary', 'waiting_approval_permanent',
            ])->where('status_lct', '!=', 'closed')
            ->orderByRaw("
                CASE 
                    WHEN tingkat_bahaya = 'high' THEN 1
                    WHEN tingkat_bahaya = 'medium' THEN 2
                    WHEN tingkat_bahaya = 'low' THEN 3
                    ELSE 4
                END
            ")
        );

        $laporanNeedReviseQuery = $applyRoleFilter(
            LaporanLct::whereIn('status_lct', ['revision', 'taskbudget_revision', 'permanent_revision'])
                ->where('status_lct', '!=', 'closed')
        );

        $laporanNeedApprovalBudgetQuery = $applyRoleFilter(
            LaporanLct::where('status_lct', 'waiting_approval_taskbudget')->orderBy('updated_at', 'desc')
        );

        $laporanInProgressQuery = $applyRoleFilter(
            LaporanLct::where('status_lct', 'in_progress')
        );

        $laporanUserQuery = null;
        if ($roleName === 'user') {
            $laporanUserQuery = LaporanLct::where('user_id', $user->id)->where('status_lct', '!=', 'closed');
        }

        // ============================
        // Statistik Bulanan
        // ============================

        $thisMonth = [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
        $lastMonth = [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()];

        $monthlyQuery = fn ($range) => $applyRoleFilter(LaporanLct::whereBetween('created_at', $range));

        $totalFindings = $monthlyQuery($thisMonth)->count();
        $resolved = $monthlyQuery($thisMonth)->where('status_lct', 'closed')->count();
        $overdue = $monthlyQuery($thisMonth)->where('status_lct', '!=', 'closed')
            ->where(fn ($q) => $q
                ->where(fn ($qq) => $qq->where('tingkat_bahaya', 'Low')->whereNull('date_completion')->whereDate('due_date', '<', now()))
                ->orWhere(fn ($qq) => $qq->whereIn('tingkat_bahaya', ['Medium', 'High'])->whereNull('date_completion')->whereDate('due_date', '<', now()))
            )->count();
        $highRisk = $monthlyQuery($thisMonth)->where('tingkat_bahaya', 'High')->count();

        $lastMonthTotalFindings = $monthlyQuery($lastMonth)->count();
        $lastMonthResolved = $monthlyQuery($lastMonth)->where('status_lct', 'closed')->count();
        $lastMonthOverdue = $monthlyQuery($lastMonth)->where('status_lct', '!=', 'closed')
            ->where(fn ($q) => $q
                ->where(fn ($qq) => $qq->where('tingkat_bahaya', 'Low')->whereNull('date_completion')->whereDate('due_date', '<', now()))
                ->orWhere(fn ($qq) => $qq->whereIn('tingkat_bahaya', ['Medium', 'High'])->whereNull('date_completion')->whereDate('due_date', '<', now()))
            )->count();
        $lastMonthHighRisk = $monthlyQuery($lastMonth)->where('tingkat_bahaya', 'High')->count();

        $change = fn ($curr, $prev) => $prev == 0 ? ($curr == 0 ? 0 : 100) : round((($curr - $prev) / $prev) * 100);

        $correctiveLowCount = 0;
        $revisionLowCount = 0;
        $temporaryInProgressCount = 0;
        $revisionTemporaryCount = 0;
        $revisionBudgetCount = 0;
        $permanentWorkCount = 0;
        $todos = 0;
        

        if ($roleName === 'pic') {
            $picId = Pic::where('user_id', $user->id)->value('id');
            $taskOnlyCount = LaporanLct::whereHas('tasks', function ($q) use ($picId) {
                $q->where('pic_id', $picId);
            })
            ->where('pic_id', '!=', $picId)
            ->whereIn('status_lct', ['approved_taskbudget', 'closed'])
            ->count();
        
            if ($picId) {
                $todoCounts = PicTodoService::getTodosCountOnlyFor($picId);
        
                // Misalnya kalau mau kirim ke view
                $correctiveLowCount = $todoCounts['correctiveLow'];
                $revisionLowCount = $todoCounts['revisionLow'];
                $temporaryInProgressCount = $todoCounts['temporaryInProgress'];
                $revisionTemporaryCount = $todoCounts['revisionTemporary'];
                $revisionBudgetCount = $todoCounts['revisionBudget'];
                $permanentWorkCount = $todoCounts['permanentWork'];
                $todos = [
                    'taskOnly' => [
                        'total' => $taskOnlyCount
                    ]
                ];
            } else {
                $correctiveLowCount = $revisionLowCount = $temporaryInProgressCount = $revisionTemporaryCount = $revisionBudgetCount = $permanentWorkCount = $todos = 0;
            }
        }

        // dd("Dashboard",$overdue->toSql(), $overdue->getBindings());
        


        return view('pages.admin.dashboard', [
            'layout' => 'layouts.admin',
            'findings' => $findings,
            'categoryStatusCounts' => $categoryStatusCounts,
            'categoryAliases' => $categoryAliases,
            'areaStatusCounts' => $areaStatusCounts,
            'statusCounts' => $statusCounts,
            'laporanMediumHigh' => $laporanMediumHighQuery->take(5)->get(),
            'laporanLow' => $laporanLowQuery->take(5)->get(),
            'laporanOverdue' => $ambilOverdue(),
            'laporanNew' => $laporanNewQuery->take(5)->get(),
            'laporanNeedApproval' => $laporanNeedApprovalQuery->take(5)->get(),
            'laporanNeedRevise' => $laporanNeedReviseQuery->take(5)->get(),
            'laporanNeedApprovalBudget' => $laporanNeedApprovalBudgetQuery->take(5)->get(),
            'laporanInProgress' => $laporanInProgressQuery->take(5)->get(),
            'laporanUser' => $laporanUserQuery?->latest()->take(5)->get() ?? collect(),
            'roleName' => $roleName,
            'totalFindings' => $totalFindings,
            'resolved' => $resolved,
            'overdue' => $overdue,
            'highRisk' => $highRisk,
            'totalFindingsChange' => $change($totalFindings, $lastMonthTotalFindings),
            'resolvedChange' => $change($resolved, $lastMonthResolved),
            'overdueChange' => $change($overdue, $lastMonthOverdue),
            'highRiskChange' => $change($highRisk, $lastMonthHighRisk),
            // Todo Count
            'correctiveLowCount' => $correctiveLowCount,
            'revisionLowCount' => $revisionLowCount,
            'temporaryInProgressCount' => $temporaryInProgressCount,
            'revisionTemporaryCount' => $revisionTemporaryCount,
            'revisionBudgetCount' => $revisionBudgetCount,
            'permanentWorkCount' => $permanentWorkCount,
            'todos' => $todos
        ]);
    }

    public function getChartData(Request $request)
    {
        try {
            $year = $request->input('year');
            $month = $request->input('month');

            // Deteksi user dan role
            if (Auth::guard('ehs')->check()) {
                $user = Auth::guard('ehs')->user();
                $roleName = 'ehs';
            } else {
                $user = Auth::guard('web')->user();
                $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
            }

            // Awal query
            $query = LaporanLct::query();

            // Filter role untuk manajer: hanya departemennya sendiri
            if ($roleName === 'manajer') {
                $deptId = LctDepartement::where('user_id', $user->id)->value('id');
                if ($deptId) {
                    $query->where('departemen_id', $deptId);
                } else {
                    // Tidak punya dept -> kosongkan data
                    $query->whereRaw('1 = 0');
                }
            }
            // EHS tidak difilter, bisa melihat semua data

            // Filter by tahun & bulan
            if ($month) {
                $query->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->selectRaw('CAST(created_at AS DATE) as date, COUNT(*) as total')
                    ->groupByRaw('CAST(created_at AS DATE)')
                    ->orderByRaw('CAST(created_at AS DATE)');
                
                $data = $query->get();
                $labels = $data->pluck('date');
            } else {
                $query->whereYear('created_at', $year)
                    ->selectRaw('MONTH(created_at) as month_num, COUNT(*) as total')
                    ->groupByRaw('MONTH(created_at)')
                    ->orderByRaw('MONTH(created_at)');

                $data = $query->get();
                $labels = $data->pluck('month_num')->map(fn($m) =>
                    \Carbon\Carbon::create()->month((int)$m)->format('F')
                );
            }

            return response()->json([
                'labels' => $labels,
                'data' => $data->pluck('total'),
            ]);

        } catch (\Exception $e) {
            Log::error('ChartData Error: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }

    public function getAreaChartData(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');

        // Ambil semua nama area
        $allAreas = DB::table('lct_area')->pluck('nama_area');

        // Deteksi role & user
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $roleName = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }

        // Base query laporan
        $query = DB::table('lct_laporan')
            ->join('lct_area', 'lct_laporan.area_id', '=', 'lct_area.id')
            ->select(
                'lct_area.nama_area as area',
                DB::raw("SUM(CASE WHEN lct_laporan.status_lct = 'closed' THEN 1 ELSE 0 END) AS closed_count"),
                DB::raw("SUM(CASE WHEN lct_laporan.status_lct != 'closed' THEN 1 ELSE 0 END) AS non_closed_count")
            )
            ->whereYear('lct_laporan.created_at', $year);

        if ($month) {
            $query->whereMonth('lct_laporan.created_at', $month);
        }

        // Filter berdasarkan role
        if ($roleName === 'manajer') {
            $deptId = DB::table('lct_departement')->where('user_id', $user->id)->value('id');
            if ($deptId) {
                $query->where('lct_laporan.departemen_id', $deptId);
            } else {
                // Tidak punya departemen → tidak boleh lihat apa-apa
                $query->whereRaw('1 = 0');
            }
        }

        // Jalankan query dan kelola hasilnya
        $reportData = $query->groupBy('lct_area.nama_area')
            ->get()
            ->keyBy('area');

        // Gabungkan dengan semua area (biar semua area tetap muncul meskipun 0)
        $areaStatusCounts = $allAreas->mapWithKeys(function ($area) use ($reportData) {
            if ($reportData->has($area)) {
                return [$area => [
                    'closed_count' => $reportData[$area]->closed_count,
                    'non_closed_count' => $reportData[$area]->non_closed_count
                ]];
            } else {
                return [$area => [
                    'closed_count' => 0,
                    'non_closed_count' => 0
                ]];
            }
        });

        return response()->json(['areaStatusCounts' => $areaStatusCounts]);
    }


    public function getCategoryChartData(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');

        // Ambil semua kategori
        $allCategory = DB::table('lct_kategori')->pluck('nama_kategori');

        // Deteksi role & user
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $roleName = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }

        // Query laporan
        $query = DB::table('lct_laporan')
            ->join('lct_kategori', 'lct_laporan.kategori_id', '=', 'lct_kategori.id')
            ->select(
                'lct_kategori.nama_kategori as kategori',
                DB::raw("SUM(CASE WHEN lct_laporan.status_lct = 'closed' THEN 1 ELSE 0 END) AS closed_count"),
                DB::raw("SUM(CASE WHEN lct_laporan.status_lct != 'closed' THEN 1 ELSE 0 END) AS non_closed_count")
            )
            ->whereYear('lct_laporan.created_at', $year);

        if ($month) {
            $query->whereMonth('lct_laporan.created_at', $month);
        }

        // Filter berdasarkan role
        if ($roleName === 'manajer') {
            $deptId = DB::table('lct_departement')->where('user_id', $user->id)->value('id');
            if ($deptId) {
                $query->where('lct_laporan.departemen_id', $deptId);
            } else {
                // Tidak punya departemen → tidak dapat akses data
                $query->whereRaw('1 = 0');
            }
        }

        $reportData = $query->groupBy('lct_kategori.nama_kategori')
            ->get()
            ->keyBy('kategori');

        // Gabungkan semua kategori agar tetap tampil walau nilainya nol
        $categoryStatusCounts = $allCategory->mapWithKeys(function ($kategori) use ($reportData) {
            if ($reportData->has($kategori)) {
                return [$kategori => [
                    'closed_count' => $reportData[$kategori]->closed_count,
                    'non_closed_count' => $reportData[$kategori]->non_closed_count
                ]];
            } else {
                return [$kategori => [
                    'closed_count' => 0,
                    'non_closed_count' => 0
                ]];
            }
        });

        // Ganti alias jika nama kategori terlalu panjang
        $categoryAliases = $allCategory->mapWithKeys(function ($kategori) {
            return [$kategori => $kategori === '5S (Seiri, Seiton, Seiso, Seiketsu, dan Shitsuke)' ? '5S' : $kategori];
        });

        return response()->json([
            'categoryStatusCounts' => $categoryStatusCounts,
            'categoryAliases' => $categoryAliases,
        ]);
    }


    public function getStatusChartData(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');

        // Deteksi user dan role
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $roleName = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }

        // Query dasar
        $query = DB::table('lct_laporan');

        if ($year) {
            $query->whereYear('created_at', $year);
        }

        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        // Filter berdasarkan role
        if ($roleName === 'manajer') {
            $deptId = DB::table('lct_departement')->where('user_id', $user->id)->value('id');
            if ($deptId) {
                $query->where('departemen_id', $deptId);
            } else {
                // Jika manajer tidak punya departemen, tampilkan 0 semua
                return response()->json([
                    'statusCounts' => [
                        'open' => 0,
                        'closed' => 0,
                        'in_progress' => 0
                    ]
                ]);
            }
        }

        // Ambil data status
        $counts = $query
            ->select(
                DB::raw("SUM(CASE WHEN status_lct = 'open' THEN 1 ELSE 0 END) as [open]"),
                DB::raw("SUM(CASE WHEN status_lct = 'closed' THEN 1 ELSE 0 END) as [closed]"),
                DB::raw("SUM(CASE WHEN status_lct = 'in_progress' THEN 1 ELSE 0 END) as [in_progress]")
            )
            ->first();

        return response()->json([
            'statusCounts' => [
                'open' => $counts->open ?? 0,
                'closed' => $counts->closed ?? 0,
                'in_progress' => $counts->in_progress ?? 0
            ]
        ]);
    }


    public function getDepartmentChartData(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');

        // Deteksi user dan role
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $roleName = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }

        // Ambil semua departemen
        $departemenList = DB::table('lct_departement')->pluck('nama_departemen', 'id');

        // Query temuan per departemen
        $query = DB::table('lct_laporan')
            ->select('departemen_id', DB::raw('COUNT(*) as total'))
            ->groupBy('departemen_id');

        if ($year) {
            $query->whereYear('created_at', $year);
        }

        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        // Tambahkan filter untuk role manajer
        if ($roleName === 'manajer') {
            $deptId = DB::table('lct_departemen')->where('user_id', $user->id)->value('id');
            if ($deptId) {
                $query->where('departemen_id', $deptId);
            } else {
                // Jika tidak ada departemen, kembalikan semua 0
                return response()->json(['data' => collect([])]);
            }
        }

        $laporanData = $query->get()->keyBy('departemen_id');

        // Bangun data chart
        $chartData = $departemenList->map(function ($nama, $id) use ($laporanData, $roleName) {
            if ($roleName === 'manajer') {
                // Untuk manajer, hanya tampilkan departemennya sendiri
                return $laporanData->has($id) ? [
                    'label' => $nama,
                    'value' => $laporanData[$id]->total
                ] : null;
            } else {
                // Untuk EHS: tampilkan semua
                return [
                    'label' => $nama,
                    'value' => $laporanData[$id]->total ?? 0
                ];
            }
        })->filter()->values(); // Buang null dan reset index

        return response()->json([
            'data' => $chartData
        ]);
    }

    
    
}