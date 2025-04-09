<?php
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskBudgetApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $laporan;

    public function __construct($laporan)
    {
        $this->laporan = $laporan;
    }

    public function build()
    {
        return $this->subject('Your Task & Budget Has Been Approved')
                    ->view('emails.task_budget_approved');
    }
}
