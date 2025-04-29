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

        $monthlyReports = LaporanLct::selectRaw('MONTH(tanggal_temuan) as month_num, COUNT(*) as count')
            ->whereYear('tanggal_temuan', $currentYear)
            ->groupByRaw('MONTH(tanggal_temuan)')
            ->orderByRaw('MONTH(tanggal_temuan)')
            ->get()
            ->mapWithKeys(fn($item) => [$monthNames[$item->month_num] => $item->count]);

        $categoryCounts = DB::table('lct_laporan')
            ->join('lct_kategori', 'lct_laporan.kategori_id', '=', 'lct_kategori.id')
            ->selectRaw('lct_kategori.nama_kategori, COUNT(lct_laporan.id) as laporan_count')
            ->groupBy('lct_kategori.nama_kategori')
            ->pluck('laporan_count', 'nama_kategori') ?? collect();

        $categories = Kategori::all();
        $categoryAliases = $categories->mapWithKeys(function ($item) {
            return [$item->nama_kategori => $item->nama_kategori === '5S (Seiri, Seiton, Seiso, Seiketsu, dan Shitsuke)' ? '5S' : $item->nama_kategori];
        })->toArray();

        $areaCounts = AreaLct::withCount('laporan')->pluck('laporan_count', 'nama_area') ?? collect();

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
        ])->where('status_lct', '!=', 'closed');

        $laporanNeedReviseQuery = LaporanLct::whereIn('status_lct', [
            'revision', 'taskbudget_revision', 'permanent_revision',
        ])->where('status_lct', '!=', 'closed');

        $laporanNeedApprovalBudgetQuery = LaporanLct::where('status_lct', 'waiting_approval_taskbudget');

        $laporanInProgressQuery = LaporanLct::where('status_lct', 'in_progress');

        $now = Carbon::now()->toDateString();
        $laporanOverdueQuery = LaporanLct::where('due_date', '<', $now)
            ->where('status_lct', '!=', 'closed')
            ->where(function ($query) {
                $query->whereNull('date_completion')
                    ->orWhereColumn('date_completion', '>', 'due_date');
            });

        // Role-based filter
        if ($roleName === 'pic') {
            $picId = Pic::where('user_id', $user->id)->value('id');
            if ($picId) {
                foreach ([$laporanMediumHighQuery, $laporanOverdueQuery, $laporanInProgressQuery, $laporanNeedReviseQuery] as $query) {
                    $query->where('pic_id', $picId);
                }
            } else {
                foreach ([$laporanMediumHighQuery, $laporanOverdueQuery, $laporanInProgressQuery, $laporanNeedReviseQuery] as $query) {
                    $query->whereRaw('1 = 0');
                }
            }
        } elseif ($roleName === 'user') {
            $laporanMediumHighQuery->where('user_id', $user->id);
            $laporanOverdueQuery->where('user_id', $user->id);
            $laporanNeedApprovalQuery->where('user_id', $user->id);

            // Tambahan: Semua laporan milik user
            $laporanUserQuery = LaporanLct::where('user_id', $user->id)
            ->where('status_lct', '!=', 'closed');

        } elseif ($roleName === 'manajer') {
            $departemenId = LctDepartement::where('user_id', $user->id)->value('id');
            if ($departemenId) {
                foreach ([$laporanMediumHighQuery, $laporanOverdueQuery, $laporanNeedApprovalBudgetQuery, $laporanNeedReviseQuery] as $query) {
                    $query->where('departemen_id', $departemenId);
                }
            } else {
                foreach ([$laporanMediumHighQuery, $laporanOverdueQuery, $laporanNeedApprovalBudgetQuery, $laporanNeedReviseQuery] as $query) {
                    $query->whereRaw('1 = 0');
                }
            }
        }

        return view('pages.admin.dashboard', [
            'layout' => 'layouts.admin',
            'monthlyReports' => $monthlyReports,
            'categoryCounts' => $categoryCounts,
            'categoryAliases' => $categoryAliases,
            'areaCounts' => $areaCounts,
            'statusCounts' => $statusCounts,
            'laporanMediumHigh' => $laporanMediumHighQuery->take(5)->get(),
            'laporanOverdue' => $laporanOverdueQuery->take(5)->get(),
            'laporanNew' => $laporanNewQuery->take(5)->get(),
            'laporanNeedApproval' => $laporanNeedApprovalQuery->take(5)->get(),
            'laporanNeedRevise' => $laporanNeedReviseQuery->take(5)->get(),
            'laporanNeedApprovalBudget' => $laporanNeedApprovalBudgetQuery->take(5)->get(),
            'laporanInProgress' => $laporanInProgressQuery->take(5)->get(),
            'laporanUser' => $laporanUserQuery?->latest()->take(5)->get() ?? collect(), 
            'roleName' => $roleName,
        ]);
    }
}