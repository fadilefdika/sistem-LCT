<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LaporanRevisiToPic extends Mailable
{
    // app/Mail/LaporanRevisiToPic.php

    public $laporan;
    public $alasanRevisi;

    public function __construct($laporan, $alasanRevisi)
    {
        $this->laporan = $laporan;
        $this->alasanRevisi = $alasanRevisi;
    }


    public function build()
    {
        return $this->subject('Revisi Laporan Ketidaksesuaian')
            ->markdown('emails.revisi.to-pic');
    }

}
