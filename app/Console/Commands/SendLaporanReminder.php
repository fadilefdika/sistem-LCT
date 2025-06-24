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

            $reminderDates = [
                'reminder_2' => $today->copy()->addDays(2),
                'reminder_1' => $today->copy()->addDay(),
                'due_today' => $today->copy(),
            ];

            foreach ($reminderDates as $label => $targetDate) {
                $laporans = LaporanLct::whereDate('due_date', $targetDate)
                    ->whereIn('status_lct', ['in_progress', 'review', 'progress_work'])
                    ->whereNull('date_completion')
                    ->with('picUser', 'departemenPic.departemen')
                    ->get();

                Log::info("[laporan:reminder] Found {$laporans->count()} reports for {$label} on {$targetDate}.");

                foreach ($laporans as $laporan) {
                    Mail::to('efdika1102@gmail.com')->send(new ReminderLaporan($laporan, $label));
                    Log::info("[laporan:reminder] Reminder email queued for Laporan ID {$laporan->id} ({$label}).");
                }
            }

            $overdueLaporans = LaporanLct::whereDate('due_date', '<', $today)
                ->whereIn('status_lct', ['in_progress', 'review', 'progress_work'])
                ->whereNull('date_completion')
                ->with('picUser', 'departemenPic.departemen')
                ->get();

            Log::info("[laporan:reminder] Found {$overdueLaporans->count()} overdue reports.");

            foreach ($overdueLaporans as $laporan) {
                if ($today->diffInDays($laporan->due_date) >= 2 && $laporan->tingkat_bahaya == 'low') {
                    $manager = optional(optional($laporan->departemenPic)->departemen)->user_id;

                    if ($manager) {
                        $managerEmail = User::find($manager)?->email ?? null;
                        Mail::to('efdika1102@gmail.com')->send(new ReminderLaporan($laporan, 'overdue_manager'));
                        Log::info("[laporan:reminder] Manager email queued for overdue Laporan ID {$laporan->id}.");
                    }
                }

                Mail::to('efdika1102@gmail.com')->send(new ReminderLaporan($laporan, 'overdue'));
                Log::info("[laporan:reminder] Overdue email queued for PIC of Laporan ID {$laporan->id_laporan_lct}.");
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
