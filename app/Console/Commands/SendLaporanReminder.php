<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\LaporanLct;
use App\Mail\ReminderLaporan;
use Illuminate\Console\Command;
use App\Mail\OverdueLaporanKePic;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLaporanReminder extends Command
{
    protected $signature = 'laporan:reminder';
    protected $description = 'Send email reminders to PIC 2 days before the due date and when overdue';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::info('[laporan:reminder] Command started at ' . now());
    
        try {
            $today = Carbon::today();
    
            // Reminder sebelum due date
            $reminderDates = [
                'reminder_2' => $today->copy()->addDays(2),
                'reminder_1' => $today->copy()->addDay(),
                'due_today'  => $today->copy(),
            ];
    
            foreach ($reminderDates as $label => $targetDate) {
                $laporans = LaporanLct::where(function ($query) use ($targetDate) {
                    $query->where(function ($q) use ($targetDate) {
                        // Low → due_date
                        $q->whereIn('status_lct', ['in_progress', 'progress_work'])
                          ->where('tingkat_bahaya', 'Low')
                          ->whereDate('due_date', $targetDate)
                          ->whereNull('date_completion');
                    })->orWhere(function ($q) use ($targetDate) {
                        // Medium/High → due_date_temp
                        $q->whereIn('status_lct', ['in_progress', 'progress_work'])
                          ->whereIn('tingkat_bahaya', ['Medium', 'High'])
                          ->whereDate('due_date_temp', $targetDate)
                          ->whereNull('date_completion_temp');
                    })->orWhere(function ($q) use ($targetDate) {
                        // Medium/High permanent → due_date_perm
                        $q->where('status_lct', 'work_permanent')
                          ->whereIn('tingkat_bahaya', ['Medium', 'High'])
                          ->whereDate('due_date_perm', $targetDate)
                          ->whereNull('date_completion');
                    });
                })
                ->with('picUser', 'departemenPic.departemen')
                ->get();
            
                Log::info("[laporan:reminder] Found {$laporans->count()} reports for {$label} on {$targetDate}.");
            
                foreach ($laporans as $laporan) {
                    Mail::to('efdika1102@gmail.com')->send(new ReminderLaporan($laporan, $label));
                    Log::info("[laporan:reminder] Reminder email queued for Laporan ID {$laporan->id_laporan_lct} ({$label}).");
                }
            }
            
    
            // Overdue logic
            $overdueLaporans = LaporanLct::with('picUser', 'departemenPic.departemen')
                ->where(function ($query) use ($today) {
                    $query->where(function ($q) use ($today) {
                        $q->whereIn('status_lct', ['in_progress', 'progress_work'])
                            ->whereNull('date_completion')
                            ->where('tingkat_bahaya', 'Low')
                            ->whereDate('due_date', '<', $today);
                    })
                    ->orWhere(function ($q) use ($today) {
                        $q->whereIn('status_lct', ['in_progress', 'progress_work'])
                            ->whereIn('tingkat_bahaya', ['Medium', 'High'])
                            ->whereDate('due_date_temp', '<', $today)
                            ->whereNull('date_completion_temp');
                    })                    
                    ->orWhere(function ($q) use ($today) {
                        $q->where('status_lct', 'work_permanent')
                            ->whereIn('tingkat_bahaya', ['Medium', 'High'])
                            ->whereNull('date_completion')
                            ->whereDate('due_date_perm', '<', $today);
                    });
                })
                ->get();
    
            Log::info("[laporan:reminder] Found {$overdueLaporans->count()} overdue reports.");
    
            foreach ($overdueLaporans as $laporan) {
                // Kirim ke PIC
                Mail::to('efdika1102@gmail.com')->send(new ReminderLaporan($laporan, 'overdue'));
                Log::info("[laporan:reminder] Overdue email queued for PIC of Laporan ID {$laporan->id_laporan_lct}.");
            
                // Tentukan due date berdasarkan tingkat bahaya
                $dueDate = null;
                $tingkatBahaya = strtolower($laporan->tingkat_bahaya);
                if ($tingkatBahaya === 'low') {
                    $dueDate = $laporan->due_date;
                    Log::info("[laporan:reminder] Laporan ID {$laporan->id_laporan_lct} | Low | Using due_date: {$dueDate}");
                } elseif (in_array($laporan->tingkat_bahaya, ['Medium', 'High'])) {
                    if ($laporan->status_lct === 'work_permanent') {
                        $dueDate = $laporan->due_date_perm;
                        Log::info("[laporan:reminder] Laporan ID {$laporan->id_laporan_lct} | Medium/High + Permanent | Using due_date_perm: {$dueDate}");
                    } else {
                        $dueDate = $laporan->due_date_temp;
                        Log::info("[laporan:reminder] Laporan ID {$laporan->id_laporan_lct} | Medium/High + Temporary | Using due_date_temp: {$dueDate}");
                    }
                }
            
                // Hitung selisih hari antara dueDate dan hari ini
                if ($dueDate) {
                    $daysOverdue = Carbon::parse($dueDate)->diffInDays($today, false);
                    Log::info("[laporan:reminder] Laporan ID {$laporan->id_laporan_lct} | Days overdue: {$daysOverdue}");
            
                    if ($daysOverdue >= 2) {
                        // Cari manager
                        $managerId = optional($laporan->departemen)->user_id;
                        Log::info("[laporan:reminder] Laporan ID {$laporan->id_laporan_lct} | Manager ID: " . ($managerId ?? 'null'));
            
                        if ($managerId) {
                            $managerEmail = User::find($managerId)?->email ?? null;
                            Log::info("[laporan:reminder] Laporan ID {$laporan->id_laporan_lct} | Manager Email: " . ($managerEmail ?? 'null'));
            
                            // Ganti ke email testing
                            Mail::to('fadilefd1102@gmail.com')->send(new ReminderLaporan($laporan, 'overdue_manager'));
                            Log::info("[laporan:reminder] Manager email queued for overdue Laporan ID {$laporan->id_laporan_lct}.");
                        }
                    } else {
                        Log::info("[laporan:reminder] Laporan ID {$laporan->id_laporan_lct} | Tidak memenuhi syarat > 2 hari overdue.");
                    }
                } else {
                    Log::warning("[laporan:reminder] Laporan ID {$laporan->id_laporan_lct} | Due date tidak ditemukan, email manager tidak dikirim.");
                }
            }
            
    
            Log::info('[laporan:reminder] Command finished successfully.');
        } catch (\Throwable $e) {
            Log::error('[laporan:reminder] ERROR: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

}
