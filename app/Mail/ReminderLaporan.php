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

    public $laporan;
    public $status;

    public function __construct($laporan, $status)
    {
        $this->laporan = $laporan;
        $this->status = $status;
    }

    public function build()
    {
        return $this->subject('Reminder: LCT Report ' . ucfirst(str_replace('_', ' ', $this->status)))
                    ->view('emails.reminder_laporan');
    }

}
