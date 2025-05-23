<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\LaporanLct;
use Illuminate\Http\Request;
use App\Models\RejectLaporan;
use App\Mail\CloseNotification;
use App\Mail\LaporanRevisiToPic;
use App\Mail\ApprovalNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\AreaLct;
use App\Models\Kategori;
use App\Models\LctDepartement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpPresentation\Shape\Chart\Type\Area;

class ProgressPerbaikanController extends Controller
{
    
    public function index(Request $request)
    {
        // Ambil user dan role sesuai guard
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $role = optional($user->roles->first())->name;
        } else {
            $user = Auth::user();
            $role = optional($user->roleLct->first())->name;
        }

        // Data dropdown filter
        $departments = \App\Models\LctDepartement::pluck('nama_departemen', 'id');
        $areas = \App\Models\AreaLct::whereNull('deleted_at')->pluck('nama_area', 'id');

        $statusGroups = [
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
        // Query utama untuk data laporan
        $query = LaporanLct::query()
            ->select('*', DB::raw("CASE WHEN status_lct = 'closed' THEN 1 ELSE 0 END as order_type"));

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

        // Filter tambahan dari request
        if ($request->filled('riskLevel')) {
            $query->where('tingkat_bahaya', $request->riskLevel);
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

        if ($request->filled('tanggalAwal') && $request->filled('tanggalAkhir')) {
            $startDate = \Carbon\Carbon::parse($request->tanggalAwal)->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->tanggalAkhir)->endOfDay();
            $query->whereBetween('tanggal_temuan', [$startDate, $endDate]);
        }

        if ($request->filled('departemenId')) {
            $query->where('departemen_id', $request->departemenId);
        }

        if ($request->filled('areaId')) {
            $query->where('area_id', $request->areaId);  // koreksi dari where('id', $request->areaId) ke area_id
        }

        // Hanya yang status bukan open
        $laporans = $query->where('status_lct', '!=', 'open')
            ->orderBy('order_type')
            ->orderByDesc('updated_at')
            ->paginate(10);

        // ================== TAMBAHAN UNTUK DATA GRAFIK ==================

        // Query base untuk statistik, sama filter seperti query utama tapi tanpa paginate dan filter open
        $baseQuery = LaporanLct::query();

        // Filter role sama seperti di atas
        if ($role === 'user') {
            $baseQuery->where('user_id', $user->id);
        } elseif ($role === 'manajer') {
            $departemenId = \App\Models\LctDepartement::where('user_id', $user->id)->value('id');
            $baseQuery->where('departemen_id', $departemenId);
        } elseif (!in_array($role, ['ehs'])) {
            $picId = \App\Models\Pic::where('user_id', $user->id)->value('id');
            $baseQuery->where('pic_id', $picId);
        }

        // Filter tanggal dan lainnya
        if ($request->filled('riskLevel')) {
            $baseQuery->where('tingkat_bahaya', $request->riskLevel);
        }
        if ($request->filled('tanggalAwal') && $request->filled('tanggalAkhir')) {
            $baseQuery->whereBetween('tanggal_temuan', [$startDate, $endDate]);
        }
        if ($request->filled('departemenId')) {
            $baseQuery->where('departemen_id', $request->departemenId);
        }
        if ($request->filled('areaId')) {
            $baseQuery->where('area_id', $request->areaId);
        }

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
            // Jika pengguna adalah EHS, ambil role dari relasi 'roles' pada model EhsUser
            $user = Auth::guard('ehs')->user();
            $roleName = optional($user->roles->first())->name;
        } else {
            // Jika pengguna adalah User biasa, ambil role dari relasi 'roleLct' pada model User
            $user = Auth::user();
            $roleName = optional($user->roleLct->first())->name;
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
                    Log::info('Tingkat bahaya: Medium/High - pengecekan status saat ini');
        
                    if (in_array($laporan->status_lct, ['waiting_approval_temporary', 'temporary_revision'])) {
                        Log::info('Status sekarang: waiting_approval_temporary / temporary_revision');
                        $laporan->status_lct = 'approved_temporary';
                        $statusLog = 'approved_temporary';
                        $laporan->approved_temporary_by_ehs = true;
        
                    } elseif ($laporan->status_lct == 'waiting_approval_taskbudget') {
                        Log::info('Status sekarang: waiting_approval_taskbudget');
                        $laporan->status_lct = 'waiting_approval_taskbudget';
                        $statusLog = 'approved_temporary';
                        $laporan->approved_temporary_by_ehs = true;
        
                    } elseif ($laporan->status_lct == 'taskbudget_revision') {
                        Log::info('Status sekarang: taskbudget_revision');
                        $laporan->status_lct = 'taskbudget_revision';
                        $statusLog = 'approved_temporary';
                        $laporan->approved_temporary_by_ehs = true;
        
                    } elseif ($laporan->status_lct == 'approved_taskbudget') {
                        Log::info('Status sekarang: approved_taskbudget');
                        $laporan->status_lct = 'approved_taskbudget';
                        $statusLog = 'approved_temporary';
                        $laporan->approved_temporary_by_ehs = true;
        
                    } elseif ($laporan->status_lct == 'waiting_approval_permanent') {
                        Log::info('Status sekarang: waiting_approval_permanent - akan disetujui permanen');
                        $laporan->status_lct = 'approved_permanent';
                        $laporan->date_completion = Carbon::now();
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
        
            Log::info('Sebelum simpan status laporan', ['status_baru' => $laporan->status_lct]);
            $laporan->save();
            Log::info('Setelah simpan status laporan', ['status_baru_aktual' => $laporan->fresh()->status_lct]);
        
            // Simpan ke tabel log (RejectLaporan) sebagai histori status
            RejectLaporan::create([
                'id_laporan_lct' => $laporan->id_laporan_lct,
                'user_id' => $user->id,
                'role' => $roleName,
                'status_lct' => $statusLog,
                'alasan_reject' => null,
                'tipe_reject' => null,
            ]);
            Log::info('Log status tersimpan di tabel reject_laporan', ['status_log' => $statusLog]);
        
            DB::commit();
            Log::info('Proses approval selesai dan transaksi dikomit.');
        
            return redirect()->back()->with('approve', 'The repair report has been successfully approved.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving EHS report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while approving the report.');
        }
    }


    public function rejectLaporan(Request $request, $id_laporan_lct)
    {
        // Cek apakah pengguna menggunakan guard 'ehs' atau 'web' untuk pengguna biasa
        if (Auth::guard('ehs')->check()) {
            // Jika pengguna adalah EHS, ambil role dari relasi 'roles' pada model EhsUser
            $user = Auth::guard('ehs')->user();
            $roleName = optional($user->roles->first())->name;
        } else {
            // Jika pengguna adalah User biasa, ambil role dari relasi 'roleLct' pada model User
            $user = Auth::user();
            $roleName = optional($user->roleLct->first())->name;
        }

        // Validasi input untuk alasan reject
        $request->validate([
            'alasan_reject' => 'required|string|max:255',
        ]);

        DB::beginTransaction(); // Mulai transaksi database
        try {
            // Cari laporan berdasarkan ID
            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->first();
            if (!$laporan) {
                return response()->json(['error' => 'Laporan tidak ditemukan'], 404);
            }

            // Tentukan status reject berdasarkan tingkat bahaya
            switch ($laporan->tingkat_bahaya) {
                case 'Low':
                    $laporan->status_lct = 'revision';
                    break;

                case 'Medium':
                case 'High':
                    if (in_array($laporan->status_lct, ['waiting_approval_temporary', 'temporary_revision'])) {
                        $laporan->status_lct = 'temporary_revision';
                    } elseif (in_array($laporan->status_lct, ['waiting_approval_permanent', 'permanent_revision'])) {
                        $laporan->status_lct = 'permanent_revision';
                    }
                    break;

                default:
                    return response()->json(['error' => 'Tingkat bahaya tidak valid'], 400);
            }

            $laporan->save();

            // Tentukan tipe reject berdasarkan status_lct SEKARANG
            $tipeReject = match ($laporan->status_lct) {
                'revision' => 'lct_perbaikan_low',
                'temporary_revision' => 'lct_perbaikan_temporary',
                default => 'lct_perbaikan_unknown',
            };

            // Simpan alasan ke tabel lct_laporan_reject (log reject)
            RejectLaporan::create([
                'id_laporan_lct' => $id_laporan_lct,
                'user_id' => $user->id,
                'role' => $roleName,
                'status_lct' => $laporan->status_lct,
                'alasan_reject' => $request->alasan_reject,
                'tipe_reject' => $tipeReject, // Menambahkan tipe reject sesuai status
            ]);

            // Ambil alasan revisi terbaru berdasarkan tipe reject
            $alasanRevisi = RejectLaporan::where('id_laporan_lct', $id_laporan_lct)
                ->where('tipe_reject', $tipeReject)
                ->orderBy('created_at', 'desc')
                ->get();

            // Kirim email ke PIC atau user terkait
            // try {
            //     Mail::to('efdika1102@gmail.com')->send(new LaporanRevisiToPic($laporan,$alasanRevisi));
            //     Log::info('Email berhasil dikirim.');
            // } catch (\Exception $mailException) {
            //     Log::error('Gagal mengirim email', ['error' => $mailException->getMessage()]);
            //     return redirect()->back()->with('error', 'Email gagal dikirim. Namun data sudah tersimpan.');
            // }

            DB::commit(); // Commit transaksi

            return redirect()->back()->with('reject', 'The revision report has been successfully sent to the PIC.');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback jika ada error
            return response()->json(['error' => 'Terjadi kesalahan saat menolak laporan.', 'message' => $e->getMessage()], 500);
        }
    }

    public function closeLaporan($id_laporan_lct)
    {
        // Cek apakah pengguna menggunakan guard 'ehs' atau 'web' untuk pengguna biasa
        if (Auth::guard('ehs')->check()) {
            // Jika pengguna adalah EHS, ambil role dari relasi 'roles' pada model EhsUser
            $user = Auth::guard('ehs')->user();
            $roleName = optional($user->roles->first())->name;
        } else {
            // Jika pengguna adalah User biasa, ambil role dari relasi 'roleLct' pada model User
            $user = Auth::user();
            $roleName = optional($user->roleLct->first())->name;
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
        $query = LaporanLct::query();

        if ($request->filled('tanggalAwal') && $request->filled('tanggalAkhir')) {
            $startDate = \Carbon\Carbon::parse($request->tanggalAwal)->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->tanggalAkhir)->endOfDay();
        } else {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        }
        
        $query->whereBetween('tanggal_temuan', [$startDate, $endDate]);
        
        
    
        if ($request->filled('riskLevel')) {
            $query->where('tingkat_bahaya', $request->riskLevel);
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
    
        if ($request->filled('departemenId')) {
            $query->where('departemen_id', $request->departemenId);
        }
    
        if ($request->filled('areaId')) {
            $query->where('area_id', $request->areaId);  // koreksi dari where('id', $request->areaId) ke area_id
        }
    
        $results = $query->get();
    
        // Hitung data chart: jumlah temuan per tanggal, dll
        $labels = [];
        $data = [];
    
        $grouped = $results->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->tanggal_temuan)->format('Y-m-d');
        });
        
    
        foreach ($grouped as $date => $items) {
            $labels[] = $date;
            $data[] = count($items);
        }
    
        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    public function chartStatus(Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month');

        $query = LaporanLct::query();

        if ($year) {
            $query->whereYear('tanggal_temuan', $year);
        }
        if ($month) {
            $query->whereMonth('tanggal_temuan', $month);
        }

        $records = $query->get();
        $today = now();

        $chartCounts = [
            'Open' => 0,
            'Closed' => 0,
            'In Progress' => 0,
            'Overdue' => 0,
        ];

        foreach ($records as $item) {
            $status = $item->status_lct;
            $bahaya = $item->tingkat_bahaya;
            $completion = $item->date_completion;
            $due = $item->due_date;
            $dueTemp = $item->due_date_temp;
            $duePerm = $item->due_date_perm;

            // Logika Overdue
            $isOverdue = false;

            if ($bahaya === 'low' && is_null($completion) && $due && $today->gt($due)) {
                $isOverdue = true;
            } elseif (in_array($bahaya, ['medium', 'high'])) {
                if (is_null($dueTemp) && $due && $today->gt($due)) {
                    $isOverdue = true;
                } elseif (!is_null($dueTemp) && is_null($duePerm) && $dueTemp && $today->gt($dueTemp)) {
                    $isOverdue = true;
                } elseif (!is_null($duePerm) && $today->gt($duePerm) && is_null($completion)) {
                    $isOverdue = true;
                }
            }

            if ($isOverdue) {
                $chartCounts['Overdue']++;
            } elseif ($status === 'open') {
                $chartCounts['Open']++;
            } elseif ($status === 'closed') {
                $chartCounts['Closed']++;
            } else {
                $chartCounts['In Progress']++;
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
        $year = $request->query('year');
        $month = $request->query('month');

        $query = DB::table('lct_laporan as l')
            ->join('lct_kategori as k', 'l.kategori_id', '=', 'k.id')
            ->whereNull('k.deleted_at');

        if ($year) {
            $query->whereYear('l.tanggal_temuan', $year);
        }

        if ($month) {
            $query->whereMonth('l.tanggal_temuan', $month);
        }

        $categoryCounts = $query
            ->select('k.nama_kategori', DB::raw('COUNT(*) as total'))
            ->groupBy('k.nama_kategori')
            ->pluck('total', 'k.nama_kategori')
            ->toArray();

            $categories = Kategori::whereNull('deleted_at')
            ->pluck('nama_kategori')
            ->toArray();
        
        // Mapping manual nama panjang ke pendek (untuk chart)
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
        foreach ($categories as $i => $category) {
            $data[] = $categoryCounts[$category] ?? 0;
        }

        return response()->json([
            'labels' => $categoryDisplayNames,
            'data' => $data,
        ]);
    }

    public function chartArea(Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month');

        // Ambil semua area aktif (tidak dihapus)
        $areas = AreaLct::whereNull('deleted_at')
            ->orderBy('nama_area')
            ->pluck('nama_area', 'id');

        $query = DB::table('lct_laporan')
            ->select('area_id', DB::raw('COUNT(*) as total'))
            ->whereIn('area_id', $areas->keys()) // hanya area aktif
            ->when($year, fn($q) => $q->whereYear('tanggal_temuan', $year))
            ->when($month, fn($q) => $q->whereMonth('tanggal_temuan', $month))
            ->groupBy('area_id')
            ->pluck('total', 'area_id')
            ->toArray();

        $labels = [];
        $data = [];

        foreach ($areas as $id => $nama) {
            $labels[] = $nama;
            $data[] = $query[$id] ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    public function chartDepartment(Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month');

        // Ambil semua departemen aktif
        $departments = LctDepartement::whereNull('deleted_at')
            ->orderBy('nama_departemen')
            ->pluck('nama_departemen', 'id');

        // Query laporan_lct hitung temuan per departemen sesuai filter
        $query = DB::table('lct_laporan')
            ->select('departemen_id', DB::raw('COUNT(*) as total'))
            ->whereIn('departemen_id', $departments->keys())
            ->when($year, fn($q) => $q->whereYear('tanggal_temuan', $year))
            ->when($month, fn($q) => $q->whereMonth('tanggal_temuan', $month))
            ->groupBy('departemen_id')
            ->pluck('total', 'departemen_id')
            ->toArray();

        $labels = [];
        $data = [];

        foreach ($departments as $id => $name) {
            $labels[] = $name;
            $data[] = $query[$id] ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    // public function chartOverdue(Request $request)
    // {
    //     $year = $request->query('year');
    //     $month = $request->query('month');

    //     $query = DB::table('lct_laporan')
    //         ->whereNotNull('first_overdue_date');

    //     if ($year) {
    //         $query->whereYear('first_overdue_date', $year);
    //     }

    //     if ($month) {
    //         // Filter per tanggal dalam bulan tsb
    //         $query->whereMonth('first_overdue_date', $month);

    //         $results = $query
    //             ->select(DB::raw("DAY(first_overdue_date) as label"), DB::raw("COUNT(*) as total"))
    //             ->groupBy(DB::raw("DAY(first_overdue_date)"))
    //             ->orderBy(DB::raw("DAY(first_overdue_date)"))
    //             ->pluck('total', 'label')
    //             ->toArray();

    //         $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year ?? now()->year);
    //         $labels = range(1, $daysInMonth);
    //     } else {
    //         // Filter per bulan dalam tahun
    //         $results = $query
    //             ->select(DB::raw("MONTH(first_overdue_date) as label"), DB::raw("COUNT(*) as total"))
    //             ->groupBy(DB::raw("MONTH(first_overdue_date)"))
    //             ->orderBy(DB::raw("MONTH(first_overdue_date)"))
    //             ->pluck('total', 'label')
    //             ->toArray();

    //         $labels = [
    //             1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
    //             5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
    //             9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
    //         ];
    //     }

    //     $chartLabels = [];
    //     $chartData = [];

    //     foreach ($labels as $key => $label) {
    //         if ($month) {
    //             // label adalah angka 1-31
    //             $chartLabels[] = $label;
    //             $chartData[] = $results[$label] ?? 0;
    //         } else {
    //             // label adalah nama bulan, key adalah angka 1-12
    //             $chartLabels[] = $label;
    //             $chartData[] = $results[$key] ?? 0;
    //         }
    //     }
        

    //     return response()->json([
    //         'labelsOverdue' => $chartLabels,
    //         'dataOverdue' => $chartData,
    //     ]);
    // }

    public function getPaginatedData(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $laporans = LaporanLct::latest()->paginate($perPage);

        return response()->json([
            'data' => view('partials.tabel-reporting', compact('laporans'))->render(),
        ]);
    }


}
