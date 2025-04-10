<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\LaporanLct;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProgressPerbaikanTable extends Component
{
    use WithPagination;

    public $riskLevel = '';
    public $statusLct = '';

    private function filterData()
    {
        $user = Auth::user();
        $roleName = optional($user->roleLct->first())->name;

        $query = LaporanLct::whereIn('status_lct', [
            'in_progress', 
            'progress_work', 
            'waiting_approval', 
            'approved', 
            'revision',
            'waiting_approval_temporary', 
            'approved_temporary', 
            'temporary_revision',
            'approved_taskbudget',
            'waiting_approval_taskbudget', 
            'taskbudget_revision',
            'work_permanent', 
            'waiting_approval_permanent', 
            'approved_permanent', 
            'permanent_revision'
        ]);

        // Filter berdasarkan peran
        if ($roleName === 'user') {
            $query->where('user_id', $user->id);
        }elseif ($roleName === 'manajer') {
            $departemenId = \App\Models\LctDepartement::where('user_id', $user->id)->value('id');
        
            if ($departemenId) {
                $query->where('departemen_id', $departemenId);
            } else {
                $query->whereRaw('1 = 0'); // biar kosong kalau gak ketemu
            }
        }
        
        // EHS tidak dibatasi apa pun

        // Filter tambahan jika ada
        $query->when($this->riskLevel, fn($q) => $q->where('tingkat_bahaya', $this->riskLevel));
        $query->when($this->statusLct, function ($q) {
            $statuses = explode(',', $this->statusLct);
            return $q->whereIn('status_lct', $statuses);
        });

        return $query->latest()->paginate(10);
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
