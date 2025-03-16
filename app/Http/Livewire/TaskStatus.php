<?php
namespace App\Livewire;

use Livewire\Component;

class TaskStatus extends Component
{
    public $taskId;
    public $status;

    public function mount($taskId, $status)
    {
        $this->taskId = $taskId;
        $this->status = $status;
    }

    public function render()
    {
        return view('livewire.task-status');
    }
}
