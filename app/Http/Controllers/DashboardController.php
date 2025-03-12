<?php

namespace App\Http\Controllers;

use App\Models\LaporanLct;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
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

        // Alias untuk kategori panjang
        $categoryAliases = [
            "Kondisi Tidak Aman (Unsafe Condition)" => "Unsafe Condition",
            "Tindakan Tidak Aman (Unsafe Act)" => "Unsafe Act",
            "5S (Seiri, Seiton, Seiso, Seiketsu, dan Shitsuke)" => "5S",
            "Near miss" => "Near Miss"
        ];

        // Ubah nama kategori menggunakan alias
        $categoryCounts = collect($categoryCounts)->mapWithKeys(function ($count, $name) use ($categoryAliases) {
            return [$categoryAliases[$name] ?? $name => $count];
        });


        // Jumlah temuan berdasarkan area
        $areaCounts = DB::table('lct_laporan')
            ->selectRaw('area, COUNT(id) as count')
            ->groupBy('area')
            ->pluck('count', 'area');

        // Jumlah open & close
        $statusCounts = [
            'open' => LaporanLct::where('status_lct', 'open')->count(),
            'close' => LaporanLct::where('status_lct', 'closed')->count(),
        ];

        return view('pages.admin.dashboard', [
            'layout' => 'layouts.admin',
            'monthlyReports' => $monthlyReports,
            'categoryCounts' => $categoryCounts,
            'areaCounts' => $areaCounts,
            'statusCounts' => $statusCounts
        ]);
    }
}
