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
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengecek guard yang digunakan dan mengambil pengguna yang sesuai
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $roleName = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            $roleName = optional($user->roleLct->first())->name ?? 'guest';
        }
        

        $monthNames = [
            1 => "January", 2 => "February", 3 => "March", 4 => "April",
            5 => "May", 6 => "June", 7 => "July", 8 => "August",
            9 => "September", 10 => "October", 11 => "November", 12 => "December"
        ];

        $currentYear = now()->year;

        $findings = LaporanLct::selectRaw('YEAR(created_at) as year')
                    ->distinct()
                    ->pluck('year');

        $categoryStatusCounts = DB::table('lct_laporan')
                    ->join('lct_kategori', 'lct_laporan.kategori_id', '=', 'lct_kategori.id')
                    ->select(
                        'lct_kategori.nama_kategori',
                        DB::raw("SUM(CASE WHEN status_lct = 'closed' THEN 1 ELSE 0 END) as closed_count"),
                        DB::raw("SUM(CASE WHEN status_lct != 'closed' THEN 1 ELSE 0 END) as non_closed_count")
                    )
                    ->groupBy('lct_kategori.nama_kategori')
                    ->get()
                    ->keyBy('nama_kategori');
                
        $categories = Kategori::all();
        $categoryAliases = $categories->mapWithKeys(function ($item) {
            return [$item->nama_kategori => $item->nama_kategori === '5S (Seiri, Seiton, Seiso, Seiketsu, dan Shitsuke)' ? '5S' : $item->nama_kategori];
        })->toArray();
                
                
        $areaStatusCounts = DB::table('lct_area')
            ->leftJoin('lct_laporan', 'lct_area.id', '=', 'lct_laporan.area_id')
            ->select(
                'lct_area.nama_area',
                DB::raw("SUM(CASE WHEN lct_laporan.status_lct = 'closed' THEN 1 ELSE 0 END) as closed_count"),
                DB::raw("SUM(CASE WHEN lct_laporan.status_lct IS NOT NULL AND lct_laporan.status_lct != 'closed' THEN 1 ELSE 0 END) as non_closed_count")
            )
            ->groupBy('lct_area.nama_area')
            ->get()
            ->keyBy('nama_area');



        $statusCounts = [
            'open' => LaporanLct::where('status_lct', 'open')->count(),
            'close' => LaporanLct::where('status_lct', 'closed')->count(),
            'in_progress' => LaporanLct::whereNotIn('status_lct', ['open', 'closed'])->count(),
        ];

        $laporanMediumHighQuery = LaporanLct::whereIn('tingkat_bahaya', ['Medium', 'High'])
            ->where('status_lct', '!=', 'closed');

        $laporanUserQuery = null;
        $laporanNewQuery = LaporanLct::where('status_lct', 'open');

        $laporanNeedApprovalQuery = LaporanLct::whereIn('status_lct', [
            'waiting_approval', 'waiting_approval_temporary', 'waiting_approval_permanent',
        ])->where('status_lct', '!=', 'closed')
          ->orderByRaw("
              CASE 
                  WHEN tingkat_bahaya = 'High' THEN 1
                  WHEN tingkat_bahaya = 'Medium' THEN 2
                  WHEN tingkat_bahaya = 'Low' THEN 3
                  ELSE 4
              END ASC
          ");
        
        

        $laporanNeedReviseQuery = LaporanLct::whereIn('status_lct', [
            'revision', 'taskbudget_revision', 'permanent_revision',
        ])->where('status_lct', '!=', 'closed');

        $laporanNeedApprovalBudgetQuery = LaporanLct::where('status_lct', 'waiting_approval_taskbudget');

        $laporanInProgressQuery = LaporanLct::where('status_lct', 'in_progress');

        $now = Carbon::now()->toDateString();

        // Query dasar overdue
        $baseQuery = LaporanLct::where('due_date', '<', $now)
            ->where('status_lct', '!=', 'closed')
            ->where(function ($query) {
                $query->whereNull('date_completion')
                    ->orWhereColumn('date_completion', '>', 'due_date');
            });

        // Ambil dulu yang tingkat_bahaya medium & high
        $priorityLaporans = (clone $baseQuery)
            ->whereIn('tingkat_bahaya', ['high', 'medium'])
            ->orderByRaw("
                CASE 
                    WHEN tingkat_bahaya = 'high' THEN 1
                    WHEN tingkat_bahaya = 'medium' THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('due_date', 'asc') // overdue paling lama dulu
            ->take(5)
            ->get();


        // Hitung sisa kuota yang masih kosong dari 5
        $remaining = 5 - $priorityLaporans->count();

        if ($remaining > 0) {
            // Ambil low yang belum ada di list sebelumnya
            $excludeIds = $priorityLaporans->pluck('id')->toArray();

            $lowLaporans = (clone $baseQuery)
                ->where('tingkat_bahaya', 'low')
                ->whereNotIn('id', $excludeIds)
                ->orderBy('due_date', 'asc')
                ->take($remaining)
                ->get();

            // Gabungkan kedua koleksi hasil
            $laporanOverdue = $priorityLaporans->concat($lowLaporans);
        } else {
            $laporanOverdue = $priorityLaporans;
        }

        // Role-based filter
        if ($roleName === 'pic') {
            $picId = Pic::where('user_id', $user->id)->value('id');
            if ($picId) {
                foreach ([$laporanMediumHighQuery, $laporanOverdue, $laporanInProgressQuery, $laporanNeedReviseQuery] as $query) {
                    $query->where('pic_id', $picId);
                }
            } else {
                foreach ([$laporanMediumHighQuery, $laporanOverdue, $laporanInProgressQuery, $laporanNeedReviseQuery] as $query) {
                    $query->whereRaw('1 = 0');
                }
            }
        } elseif ($roleName === 'user') {
            $laporanMediumHighQuery->where('user_id', $user->id);
            $laporanOverdue->where('user_id', $user->id);
            $laporanNeedApprovalQuery->where('user_id', $user->id);

            // Tambahan: Semua laporan milik user
            $laporanUserQuery = LaporanLct::where('user_id', $user->id)
            ->where('status_lct', '!=', 'closed');

        } elseif ($roleName === 'manajer') {
            $departemenId = LctDepartement::where('user_id', $user->id)->value('id');
            if ($departemenId) {
                foreach ([$laporanMediumHighQuery, $laporanOverdue, $laporanNeedApprovalBudgetQuery, $laporanNeedReviseQuery] as $query) {
                    $query->where('departemen_id', $departemenId);
                }
            } else {
                foreach ([$laporanMediumHighQuery, $laporanOverdue, $laporanNeedApprovalBudgetQuery, $laporanNeedReviseQuery] as $query) {
                    $query->whereRaw('1 = 0');
                }
            }
        }

        return view('pages.admin.dashboard', [
            'layout' => 'layouts.admin',
            'findings' => $findings,
            'categoryStatusCounts' => $categoryStatusCounts,
            'categoryAliases' => $categoryAliases,
            'areaStatusCounts' => $areaStatusCounts,
            'statusCounts' => $statusCounts,
            'laporanMediumHigh' => $laporanMediumHighQuery->take(5)->get(),
            'laporanOverdue' => $laporanOverdue,
            'laporanNew' => $laporanNewQuery->take(5)->get(),
            'laporanNeedApproval' => $laporanNeedApprovalQuery->take(5)->get(),
            'laporanNeedRevise' => $laporanNeedReviseQuery->take(5)->get(),
            'laporanNeedApprovalBudget' => $laporanNeedApprovalBudgetQuery->take(5)->get(),
            'laporanInProgress' => $laporanInProgressQuery->take(5)->get(),
            'laporanUser' => $laporanUserQuery?->latest()->take(5)->get() ?? collect(), 
            'roleName' => $roleName,
        ]);
    }

    public function getChartData(Request $request)
    {
        try {
            $year = $request->input('year');
            $month = $request->input('month');

            if ($month) {
                // Per hari, sumbu X tanggal
                $data = LaporanLct::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->selectRaw('CAST(created_at AS DATE) as date, COUNT(*) as total')
                    ->groupByRaw('CAST(created_at AS DATE)')
                    ->orderByRaw('CAST(created_at AS DATE)')
                    ->get();

                // Membuat labels sebagai tanggal
                $labels = $data->pluck('date');
            } else {
                // Per bulan, sumbu X bulan
                $data = LaporanLct::whereYear('created_at', $year)
                    ->selectRaw('MONTH(created_at) as month_num, COUNT(*) as total')
                    ->groupByRaw('MONTH(created_at)')
                    ->orderByRaw('MONTH(created_at)')
                    ->get();

                // Membuat labels sebagai nama bulan
                $labels = $data->pluck('month_num')->map(function ($m) {
                    return \Carbon\Carbon::create()->month((int) $m)->format('F');
                });
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

        // Query laporan
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

        $reportData = $query->groupBy('lct_area.nama_area')
            ->get()
            ->keyBy('area');

        // Gabungkan dengan semua area
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

        // Query laporan
        $query = DB::table('lct_laporan')
            ->join('lct_kategori', 'lct_laporan.kategori_id', '=', 'lct_kategori.id') // Pastikan join dengan kategori
            ->select(
                'lct_kategori.nama_kategori as kategori',
                DB::raw("SUM(CASE WHEN lct_laporan.status_lct = 'closed' THEN 1 ELSE 0 END) AS closed_count"),
                DB::raw("SUM(CASE WHEN lct_laporan.status_lct != 'closed' THEN 1 ELSE 0 END) AS non_closed_count")
            )
            ->whereYear('lct_laporan.created_at', $year);

        if ($month) {
            $query->whereMonth('lct_laporan.created_at', $month);
        }

        $reportData = $query->groupBy('lct_kategori.nama_kategori')
            ->get()
            ->keyBy('kategori');

        // Map data laporan dengan kategori yang ada
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

        // Siapkan categoryAliases untuk digunakan di chart
        $categoryAliases = $allCategory->mapWithKeys(function ($kategori) {
            return [$kategori => $kategori === '5S (Seiri, Seiton, Seiso, Seiketsu, dan Shitsuke)' ? '5S' : $kategori];
        });
        

        return response()->json([
            'categoryStatusCounts' => $categoryStatusCounts,
            'categoryAliases' => $categoryAliases // Kirimkan categoryAliases ke frontend
        ]);
    }

    public function getStatusChartData(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');

        $query = DB::table('lct_laporan');

        if ($year) {
            $query->whereYear('created_at', $year);
        }

        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        $counts = $query
            ->select(
                DB::raw("SUM(CASE WHEN status_lct = 'open' THEN 1 ELSE 0 END) as [open]"),
                DB::raw("SUM(CASE WHEN status_lct = 'closed' THEN 1 ELSE 0 END) as [closed]"), // <-- disini 'closed'
                DB::raw("SUM(CASE WHEN status_lct = 'in_progress' THEN 1 ELSE 0 END) as [in_progress]")
            )
            ->first();


        // Cek jika null
        if (!$counts) {
            $counts = (object)[
                'open' => 0,
                'closed' => 0,
                'in_progress' => 0
            ];
        }

        return response()->json([
            'statusCounts' => [
                'open' => $counts->open ?? 0,
                'closed' => $counts->closed ?? 0,  // ini sesuai alias
                'in_progress' => $counts->in_progress ?? 0
            ]
        ]);
    }

    public function getDepartmentChartData(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');
    
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
    
        $laporanData = $query->get()->keyBy('departemen_id');
    
        // Bangun data lengkap berdasarkan semua departemen
        $chartData = $departemenList->map(function ($nama, $id) use ($laporanData) {
            return [
                'label' => $nama,
                'value' => $laporanData[$id]->total ?? 0
            ];
        })->values(); // reset index agar array numerik
    
        return response()->json([
            'data' => $chartData
        ]);
    }
    



    
}