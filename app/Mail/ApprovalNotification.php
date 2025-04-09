<?php
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $laporan;

    public function __construct($laporan)
    {
        $this->laporan = $laporan;
    }

    public function build()
    {
        return $this->subject('Approval Notification - Laporan Perbaikan')
                    ->view('emails.approval_notification');
    }
}
