<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LaporanHasilPerbaikan extends Mailable
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
        $subject = ($this->laporan->tingkat_bahaya === 'Medium' || $this->laporan->tingkat_bahaya === 'High')
            ? 'Repair Report - Waiting for Temporary Approval'
            : 'Repair Report Submitted to EHS';

        return $this->subject($subject)
                    ->view('emails.laporan_perbaikan_lct');
    }

}
