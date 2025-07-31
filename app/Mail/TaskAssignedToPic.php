<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskAssignedToPic extends Mailable
{
    use Queueable, SerializesModels;

    public $task;

    public function __construct($task)
    {
        $this->task = $task;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tugas LCT Telah Ditugaskan ke Anda',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task_assigned',
            with: [
                'task' => $this->task,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
