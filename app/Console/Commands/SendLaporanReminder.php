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
        $today = Carbon::today();
    
        // Mendefinisikan tanggal pengingat
        $reminderDates = [
            'reminder_2' => $today->copy()->addDays(2), // 2 hari sebelum due date
            'reminder_1' => $today->copy()->addDay(),   // 1 hari sebelum due date
            'due_today' => $today->copy(),               // Due date hari ini
        ];
    
        foreach ($reminderDates as $label => $targetDate) {
            $laporans = LaporanLct::whereDate('due_date', $targetDate)
                ->whereIn('status_lct', ['in_progress', 'review', 'progress_work'])
                ->whereNull('date_completion')
                ->with('picUser', 'departemenPic.departemen')
                ->get();
    
            Log::info("Found {$laporans->count()} reports for {$label}.");
    
            foreach ($laporans as $laporan) {
                // Kirim email ke PIC jika laporan sesuai dengan label pengingat
                if ($label == 'reminder_2' || $label == 'reminder_1' || $label == 'due_today') {
                    // Mail::to($laporan->picUser->email)
                    //     ->send(new ReminderLaporan($laporan, $label));
                    Mail::to('efdika1102@gmail.com')
                    ->queue(new ReminderLaporan($laporan, $label));
                }
            }
        }
    
        // Laporan yang sudah overdue (sebelum hari ini)
        $overdueLaporans = LaporanLct::whereDate('due_date', '<', $today)
            ->whereIn('status_lct', ['in_progress', 'review', 'progress_work'])
            ->whereNull('date_completion')
            ->with('picUser', 'departemenPic.departemen')
            ->get();
    
        foreach ($overdueLaporans as $laporan) {
            // Cek laporan yang sudah overdue dan terlambat lebih dari 2 hari serta tingkat bahaya 'low'
            if ($today->diffInDays($laporan->due_date) >= 2 && $laporan->tingkat_bahaya == 'low') {
                // Ambil data atasan PIC dari tabel lct_departement
                $departemenPic = $laporan->departemenPic;
                $departemen = $departemenPic ? $departemenPic->departemen : null;
                $manager = $departemen ? $departemen->user_id : null;
    
                if ($manager) {
                    // Ambil email manajer dari tabel users
                    $managerEmail = User::find($manager)->email;
    
                    // Kirim email kepada manajer (atasan PIC) untuk laporan overdue
                    Mail::to('efdika1102@gmail.com')
                    ->queue(new ReminderLaporan($laporan, $label));
                    // Mail::to($managerEmail)
                    //     ->send(new ReminderLaporan($laporan, 'overdue_manager'));
                }
            }
    
            // Kirim email pengingat kepada PIC untuk laporan overdue
            // Mail::to($laporan->picUser->email)
            //     ->send(new ReminderLaporan($laporan, 'overdue'));
            Mail::to('efdika1102@gmail.com')
                    ->queue(new ReminderLaporan($laporan, $label));
        }
    }
    

}
