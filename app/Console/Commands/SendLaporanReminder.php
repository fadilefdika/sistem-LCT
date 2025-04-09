<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\LaporanLct;
use Illuminate\Console\Command;
use App\Mail\OverdueLaporanKePic;
use App\Mail\ReminderLaporan;
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
        Log::info('Laporan Reminder Command Started.');

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
                ->with('picUser')
                ->get();

            Log::info("Found {$laporans->count()} reports for {$label}.");

            foreach ($laporans as $laporan) {
                Mail::to('efdika1102@gmail.com')
                    ->queue(new ReminderLaporan($laporan, $label));
                // Mail::to($laporan->picUser->email)
                //     ->queue(new ReminderLaporan($laporan, $label));
            }
        }

        // Overdue reports (before today)
        $overdueLaporans = LaporanLct::whereDate('due_date', '<', $today)
            ->whereIn('status_lct', ['in_progress', 'review', 'progress_work'])
            ->whereNull('date_completion')
            ->with('picUser')
            ->get();

        Log::info("Found {$overdueLaporans->count()} overdue reports.");

        foreach ($overdueLaporans as $laporan) {
            Mail::to('efdika1102@gmail.com')
                ->queue(new ReminderLaporan($laporan, 'overdue'));
            // Mail::to($laporan->picUser->email)
            //     ->queue(new ReminderLaporan($laporan, 'overdue'));
        }

        Log::info('Laporan Reminder Command Finished.');
    }

}
