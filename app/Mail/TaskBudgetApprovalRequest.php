<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\LaporanLct;

class TaskBudgetApprovalRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $laporan;
    public $tasks;

    public function __construct(LaporanLct $laporan, $tasks)
    {
        $this->laporan = $laporan;
        $this->tasks = $tasks;
    }

    public function build()
    {
        return $this->subject('📝 Budget Approval Request - LCT Report')
            ->markdown('emails.task_budget_approval');
    }
}
