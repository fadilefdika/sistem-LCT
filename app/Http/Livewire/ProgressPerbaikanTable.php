<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\LaporanLct;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class ProgressPerbaikanTable extends Component
{
    use WithPagination;

    public $riskLevel = '';

    private function filterData()
    {
        return LaporanLct::whereIn('status_lct', [
            'in_progress', 'progress_work', 'waiting_approval', 'approved', 'revision', 
            'waiting_approval_temporary', 'approved_temporary', 'temporary_revision', 
            'work_permanent', 'waiting_approval_permanent', 'approved_permanent', 
            'permanent_revision'
        ])
        ->when($this->riskLevel, fn($query) => $query->where('tingkat_bahaya', $this->riskLevel))
        ->latest() // Lebih singkat dari orderBy('created_at', 'desc')
        ->paginate(10);
    }

    public function updatedRiskLevel($value)
    {
        $this->resetPage(); 
    }

    public function applyFilter()
    {

    }


    public function render()
    {
        return view('livewire.progress-perbaikan-table', [
            'laporans' => $this->filterData(),
        ]);
    }
}
