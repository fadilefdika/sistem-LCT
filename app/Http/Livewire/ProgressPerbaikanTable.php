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
    public $statusLct = '';

    private function filterData()
    {
        return LaporanLct::whereIn('status_lct', [
            'in_progress', 'progress_work', 'waiting_approval', 'approved', 'revision', 
            'waiting_approval_temporary', 'approved_temporary', 'temporary_revision', 
            'work_permanent', 'waiting_approval_permanent', 'approved_permanent', 
            'permanent_revision'
        ])
        ->when($this->riskLevel, fn($query) => $query->where('tingkat_bahaya', $this->riskLevel))
        ->when($this->statusLct, function ($query) {
            $statuses = explode(',', $this->statusLct); 
            return $query->whereIn('status_lct', $statuses);
        })
        ->latest() 
        ->paginate(10);
    }

    public function updatedRiskLevel($value)
    {
        $this->resetPage(); 
    }

    public function applyFilter()
    {

    }

    public function resetFilters()
    {
        $this->riskLevel = '';
        $this->statusLct = '';
        $this->resetPage();
    }


    public function render()
    {
        return view('livewire.progress-perbaikan-table', [
            'laporans' => $this->filterData(),
        ]);
    }
}
