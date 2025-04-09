<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskBudgetRevisionMail extends Mailable
{
    public $laporan;
    public $alasanReject;

    public function __construct($laporan, $alasanReject)
    {
        $this->laporan = $laporan;
        $this->alasanReject = $alasanReject;
    }

    public function build()
    {
        return $this->subject('Revision Required: Task & Budget Request')
                    ->view('emails.task_budget_revision_mail');
    }

}
