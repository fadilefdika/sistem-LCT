<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LaporanDikirimKePic extends Mailable
{
    use Queueable, SerializesModels;

    public $laporan;

    /**
     * Create a new message instance.
     */
    public function __construct($laporan)
    {
        $this->laporan = $laporan;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('🚨 New Findings Reported – [EHSight]')
                ->view('emails.laporan_ke_pic');
    }
}
