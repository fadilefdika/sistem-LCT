<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderLaporan extends Mailable
{
    use Queueable, SerializesModels;

    public $laporans;
    public $status;
    public $recipientType; // 'PIC' atau 'Manager'

    public function __construct($laporans, $status, $recipientType)
    {
        $this->laporans = is_array($laporans) ? collect($laporans) : $laporans;
        $this->status = $status;
        $this->recipientType = $recipientType;
    }

    public function build()
    {
        return $this->subject('Reminder: LCT Report ' . ucfirst(str_replace('_', ' ', $this->status)))
                    ->view('emails.reminder_laporan');
    }
}
