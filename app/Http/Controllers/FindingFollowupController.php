<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanLct;
use Illuminate\Support\Facades\Auth;

class FindingFollowupController extends Controller
{
    public function index()
    {
        // Check which guard is authenticated and get user ID
        $userId = null;
        if (Auth::guard('web')->check()) {
            $userId = Auth::guard('web')->user()->id;
        } elseif (Auth::guard('ehs')->check()) {
            $userId = Auth::guard('ehs')->user()->id;
        }

        $perPage = request('perPage', 10); // default 10

        $laporans = LaporanLct::where('user_id', $userId)
            ->orderByRaw("CASE WHEN status_lct = 'closed' THEN 1 ELSE 0 END")
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        
        return view('pages.admin.finding-followup.index', compact('laporans'));
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
        
        $lowOrTemporaryRejects = $laporan->rejectLaporan->filter(function($item) {
            return in_array($item->tipe_reject, ['lct_perbaikan_low', 'lct_perbaikan_temporary']);
        });
        
        $budgetApprovalRejects = $laporan->rejectLaporan->filter(function($item) {
            return $item->tipe_reject === 'budget_approval';
        });        

        return view('pages.admin.finding-followup.show', compact('laporan', 'bukti_temuan', 'tindakan_perbaikan', 'allTasksCompleted', 'lowOrTemporaryRejects', 'budgetApprovalRejects'));
    }

    public function table()
    {
        $userId = Auth::guard('web')->id() ?? Auth::guard('ehs')->id();
        $perPage = request('perPage', 10);

        $laporans = LaporanLct::where('user_id', $userId)
            ->orderByRaw("CASE WHEN status_lct = 'closed' THEN 1 ELSE 0 END")
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('partials.tabel-finding-followup-wrapper', compact('laporans'))->render(); // khusus partial
    }

}

