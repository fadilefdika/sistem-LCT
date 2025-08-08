<?php

namespace App\Mail;

use App\Models\LaporanLct;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        return $this->subject('📝 Budget Approval Request – Finding Reports [EHSight]')
            ->markdown('emails.task_budget_approval');
    }
}
