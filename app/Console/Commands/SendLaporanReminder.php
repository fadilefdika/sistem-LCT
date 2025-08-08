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

            // ------------------------
            // 1. REMINDER SEBELUM DUE DATE
            // ------------------------
            $reminderDates = [
                'reminder_2' => $today->copy()->addDays(2),
                'reminder_1' => $today->copy()->addDay(),
                'due_today'  => $today->copy(),
            ];

            $groupedForPic = [];

            foreach ($reminderDates as $label => $targetDate) {
                Log::info("[laporan:reminder] Fetching laporans for {$label} with targetDate {$targetDate->toDateString()}");

                $laporans = LaporanLct::where(function ($query) use ($targetDate) {
                    $query->where(function ($q) use ($targetDate) {
                        $q->whereIn('status_lct', ['in_progress', 'progress_work'])
                        ->where('tingkat_bahaya', 'Low')
                        ->whereDate('due_date', $targetDate)
                        ->whereNull('date_completion');
                    })->orWhere(function ($q) use ($targetDate) {
                        $q->whereIn('status_lct', ['in_progress', 'progress_work'])
                        ->whereIn('tingkat_bahaya', ['Medium', 'High'])
                        ->whereDate('due_date_temp', $targetDate)
                        ->whereNull('date_completion_temp');
                    })->orWhere(function ($q) use ($targetDate) {
                        $q->where('status_lct', 'work_permanent')
                        ->whereIn('tingkat_bahaya', ['Medium', 'High'])
                        ->whereDate('due_date_perm', $targetDate)
                        ->whereNull('date_completion');
                    });
                })
                ->with('picUser', 'departemenPic.departemen','kategori')
                ->get();

                Log::info("[laporan:reminder] Found {$laporans->count()} laporans for {$label}");

                foreach ($laporans as $laporan) {
                    // Ganti dengan $laporan->picUser?->email kalau sudah fix
                    $picEmail = 'efdika1102@gmail.com';
                    if ($picEmail) {
                        $groupedForPic[$picEmail][$label][] = $laporan;
                    }
                }
            }

            // Kirim email PIC per kategori reminder
            foreach ($groupedForPic as $email => $byLabel) {
                foreach ($byLabel as $label => $laporanList) {
                    Log::info("[laporan:reminder] Sending reminder email to PIC {$email} ({$label}) with ".count($laporanList)." reports.");
                    Mail::to('efdika1102@gmail.com')->send(new ReminderLaporan($laporanList, $label, 'PIC'));
                }
            }

            // ------------------------
            // 2. OVERDUE
            // ------------------------
            Log::info("[laporan:reminder] Fetching overdue laporans before {$today->toDateString()}");

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

            Log::info("[laporan:reminder] Found {$overdueLaporans->count()} overdue laporans");

            $groupedOverdueForPic = [];
            $groupedOverdueForManager = [];

            foreach ($overdueLaporans as $laporan) {
                $dueDate = match (true) {
                    strtolower($laporan->tingkat_bahaya) === 'low' => $laporan->due_date,
                    in_array($laporan->tingkat_bahaya, ['Medium', 'High']) && $laporan->status_lct === 'work_permanent' => $laporan->due_date_perm,
                    default => $laporan->due_date_temp
                };
                $daysOverdue = Carbon::parse($dueDate)->diffInDays($today, false);

                // Ganti dengan $laporan->picUser?->email kalau sudah fix
                $picEmail = 'efdika1102@gmail.com';
                if ($picEmail) {
                    $groupedOverdueForPic[$picEmail]['overdue'][] = $laporan;
                }

                if ($daysOverdue >= 2) {
                    // $managerEmail = $laporan->departemen?->manager?->email ?? null;
                    $managerEmail = 'fadilefd1102@gmail.com';
                    if ($managerEmail) {
                        $groupedOverdueForManager[$managerEmail]['overdue_manager'][] = $laporan;
                    }
                }
            }

            foreach ($groupedOverdueForPic as $email => $byLabel) {
                foreach ($byLabel as $label => $laporanList) {
                    Log::info("[laporan:reminder] Sending overdue email to PIC {$email} ({$label}) with ".count($laporanList)." reports.");
                    Mail::to('efdika1102@gmail.com')->send(new ReminderLaporan($laporanList, $label, 'PIC'));
                }
            }

            foreach ($groupedOverdueForManager as $email => $byLabel) {
                foreach ($byLabel as $label => $laporanList) {
                    Log::info("[laporan:reminder] Sending overdue manager email to {$email} ({$label}) with ".count($laporanList)." reports.");
                    Mail::to('fadilefd1102@gmail.com')->send(new ReminderLaporan($laporanList, $label, 'Manager'));
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
