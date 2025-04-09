<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CloseNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $laporan;

    public function __construct($laporan)
    {
        $this->laporan = $laporan;
    }

    public function build()
    {
        return $this->subject('Laporan Ditutup - Notifikasi LCT')
                    ->view('emails.close_notification');
    }
}
