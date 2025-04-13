<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Kategori;
use App\Models\LaporanLct;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\LctDepartement;
use App\Models\Pic;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $roleName = optional($user->roleLct->first())->name;
        
        $monthNames = [
            1 => "January", 2 => "February", 3 => "March", 4 => "April",
            5 => "May", 6 => "June", 7 => "July", 8 => "August",
            9 => "September", 10 => "October", 11 => "November", 12 => "December"
        ];

        // Query dengan SQL Server-friendly syntax
        $monthlyReports = LaporanLct::selectRaw('MONTH(tanggal_temuan) as month_num, COUNT(*) as count')
            ->whereRaw('YEAR(tanggal_temuan) = ?', [now()->year])
            ->groupByRaw('MONTH(tanggal_temuan)')
            ->orderByRaw('MONTH(tanggal_temuan)')
            ->get()
            ->mapWithKeys(fn($item) => [$monthNames[$item->month_num] => $item->count]);

        // Jumlah temuan berdasarkan kategori
        $categoryCounts = DB::table('lct_laporan')
            ->join('lct_kategori', 'lct_laporan.kategori_id', '=', 'lct_kategori.id')
            ->selectRaw('lct_kategori.nama_kategori, COUNT(lct_laporan.id) as laporan_count')
            ->groupBy('lct_kategori.nama_kategori')
            ->pluck('laporan_count', 'nama_kategori');

        $categories = Kategori::all();

        $categoryAliases = $categories->mapWithKeys(function ($item) {
            $name = $item->nama_kategori;
        
            if ($name === '5S (Seiri, Seiton, Seiso, Seiketsu, dan Shitsuke)') {
                return [$name => '5S']; // pakai huruf kapital sesuai preferensimu
            }
        
            return [$name => $name]; 
        })->toArray();
        
        // Jumlah temuan berdasarkan area
        $areaCounts = DB::table('lct_laporan')
            ->selectRaw('area, COUNT(id) as count')
            ->groupBy('area')
            ->pluck('count', 'area');

        // Jumlah open & close
        $statusCounts = [
            'open' => LaporanLct::where('status_lct', 'open')->count(),
            'close' => LaporanLct::where('status_lct', 'closed')->count(),
            'in_progress' => LaporanLct::whereNotIn('status_lct', ['open', 'closed'])->count(),
        ];

       // Base query untuk Medium dan High
        $laporanMediumHighQuery = LaporanLct::whereIn('tingkat_bahaya', ['Medium', 'High'])
        ->where('status_lct', '!=', 'closed');

        // Base query untuk Overdue
        $now = Carbon::now()->toDateString();
        $laporanOverdueQuery = LaporanLct::where('due_date', '<', $now)
        ->where('status_lct', '!=', 'closed')
        ->where(function ($query) {
            $query->whereNull('date_completion')
                ->orWhereColumn('date_completion', '>', 'due_date');
        });

        // Tambahkan filter berdasarkan role
        if ($roleName === 'pic') {
        $picId = Pic::where('user_id', $user->id)->value('id');

        if ($picId) {
            $laporanMediumHighQuery->where('pic_id', $picId);
            $laporanOverdueQuery->where('pic_id', $picId);
        } else {
            // Jika tidak ditemukan, kosongkan hasil
            $laporanMediumHighQuery->whereRaw('1 = 0');
            $laporanOverdueQuery->whereRaw('1 = 0');
        }
        } elseif ($roleName === 'user') {
        $laporanMediumHighQuery->where('user_id', $user->id);
        $laporanOverdueQuery->where('user_id', $user->id);
        } elseif ($roleName === 'manajer') {
        $departemenId = LctDepartement::where('user_id', $user->id)->value('id');

        if ($departemenId) {
            $laporanMediumHighQuery->where('departemen_id', $departemenId);
            $laporanOverdueQuery->where('departemen_id', $departemenId);
        } else {
            $laporanMediumHighQuery->whereRaw('1 = 0');
            $laporanOverdueQuery->whereRaw('1 = 0');
        }
        }

        // Ambil 5 data
        $laporanMediumHigh = $laporanMediumHighQuery->take(5)->get();
        $laporanOverdue = $laporanOverdueQuery->take(5)->get();



        return view('pages.admin.dashboard', [
            'layout' => 'layouts.admin',
            'monthlyReports' => $monthlyReports,
            'categoryCounts' => $categoryCounts,
            'categoryAliases' => $categoryAliases,
            'areaCounts' => $areaCounts,
            'statusCounts' => $statusCounts,
            'laporanMediumHigh' => $laporanMediumHigh,
            'laporanOverdue' => $laporanOverdue,
        ]);
    }
}
