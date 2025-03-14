<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Kategori;
use App\Models\LaporanLct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

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

        // Tabel pertama: laporan dengan tingkat_bahaya Medium dan High dan status_lct bukan 'closed'
        $laporanMediumHigh = LaporanLct::whereIn('tingkat_bahaya', ['Medium', 'High'])
            ->where('status_lct', '!=', 'closed')
            ->take(5)
            ->get();

        $now = Carbon::now()->toDateString();

        $laporanOverdue = LaporanLct::where('due_date', '<', $now) // Pastikan due_date sudah lewat
            ->where('status_lct', '!=', 'closed') // Status tidak closed
            ->where(function ($query) {
                // Kondisi untuk date_completion: jika NULL, tetap ambil, jika tidak, pastikan melewati due_date
                $query->whereNull('date_completion') // Ambil data yang date_completion NULL
                    ->orWhere('date_completion', '>', DB::raw('due_date')); // Atau yang date_completion > due_date
            })
            ->take(5) // Ambil 5 data teratas
            ->get();


    
            // $laporanMediumHighh = collect([
            //     (object)[
            //         'id_laporan_lct' => 1,
            //         'temuan_ketidaksesuaian' => 'Machine malfunction due to overheating.',
            //         'picUser' => (object)['fullname' => 'John Doe'],
            //         'tingkat_bahaya' => 'High',
            //         'status_lct' => 'open',
            //         'due_date' => '2025-03-20',
            //         'date_completion' => null
            //     ],
            //     (object)[
            //         'id_laporan_lct' => 2,
            //         'temuan_ketidaksesuaian' => 'Chemical spill in the factory.',
            //         'picUser' => (object)['fullname' => 'Jane Smith'],
            //         'tingkat_bahaya' => 'Medium',
            //         'status_lct' => 'in_progress',
            //         'due_date' => '2025-02-15',
            //         'date_completion' => '2025-02-12'
            //     ],
            //     (object)[
            //         'id_laporan_lct' => 3,
            //         'temuan_ketidaksesuaian' => 'Electrical short circuit in the warehouse.',
            //         'picUser' => (object)['fullname' => 'Alice Johnson'],
            //         'tingkat_bahaya' => 'High',
            //         'status_lct' => 'waiting_approval',
            //         'due_date' => '2025-03-10',
            //         'date_completion' => null
            //     ],
            //     (object)[
            //         'id_laporan_lct' => 4,
            //         'temuan_ketidaksesuaian' => 'Lack of safety equipment in storage.',
            //         'picUser' => (object)['fullname' => 'Tom White'],
            //         'tingkat_bahaya' => 'Medium',
            //         'status_lct' => 'approved',
            //         'due_date' => '2025-03-15',
            //         'date_completion' => '2025-03-14'
            //     ],
            //     (object)[
            //         'id_laporan_lct' => 5,
            //         'temuan_ketidaksesuaian' => 'Hazardous materials improperly stored.',
            //         'picUser' => (object)['fullname' => 'Emily Brown'],
            //         'tingkat_bahaya' => 'High',
            //         'status_lct' => 'closed',
            //         'due_date' => '2025-03-01',
            //         'date_completion' => '2025-02-28'
            //     ]
            // ]);
        
            // // Dummy data for Overdue Reports
            // $laporanOverduee = collect([
            //     (object)[
            //         'id_laporan_lct' => 6,
            //         'temuan_ketidaksesuaian' => 'Broken emergency exit sign.',
            //         'picUser' => (object)['fullname' => 'Charlie Davis'],
            //         'tingkat_bahaya' => 'High',
            //         'status_lct' => 'in_progress',
            //         'due_date' => '2025-02-10',
            //         'date_completion' => null
            //     ],
            //     (object)[
            //         'id_laporan_lct' => 7,
            //         'temuan_ketidaksesuaian' => 'Uncalibrated equipment in the lab.',
            //         'picUser' => (object)['fullname' => 'Linda Williams'],
            //         'tingkat_bahaya' => 'Medium',
            //         'status_lct' => 'review',
            //         'due_date' => '2025-01-25',
            //         'date_completion' => '2025-02-01'
            //     ],
            //     (object)[
            //         'id_laporan_lct' => 8,
            //         'temuan_ketidaksesuaian' => 'Expired safety equipment.',
            //         'picUser' => (object)['fullname' => 'Greg Harris'],
            //         'tingkat_bahaya' => 'High',
            //         'status_lct' => 'waiting_approval',
            //         'due_date' => '2025-02-05',
            //         'date_completion' => null
            //     ],
            //     (object)[
            //         'id_laporan_lct' => 9,
            //         'temuan_ketidaksesuaian' => 'Improper chemical storage.',
            //         'picUser' => (object)['fullname' => 'Sophia Clark'],
            //         'tingkat_bahaya' => 'Medium',
            //         'status_lct' => 'approved',
            //         'due_date' => '2025-01-30',
            //         'date_completion' => '2025-02-10'
            //     ],
            //     (object)[
            //         'id_laporan_lct' => 10,
            //         'temuan_ketidaksesuaian' => 'Non-compliance with safety protocols.',
            //         'picUser' => (object)['fullname' => 'James Lee'],
            //         'tingkat_bahaya' => 'High',
            //         'status_lct' => 'closed',
            //         'due_date' => '2025-02-01',
            //         'date_completion' => '2025-02-02'
            //     ]
            // ]);

        return view('pages.admin.dashboard', [
            'layout' => 'layouts.admin',
            'monthlyReports' => $monthlyReports,
            'categoryCounts' => $categoryCounts,
            'areaCounts' => $areaCounts,
            'statusCounts' => $statusCounts,
            'laporanMediumHigh' => $laporanMediumHigh,
            'laporanOverdue' => $laporanOverdue,
        ]);
    }
}
