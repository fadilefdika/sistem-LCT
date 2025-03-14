<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\LaporanLct;
use Livewire\WithPagination;

class BudgetApprovalHistory extends Component
{
    use WithPagination;
    public $search = ''; // Untuk pencarian
    public $perPage = 10; // Jumlah data per halaman
    public $sortField = 'created_at'; // Kolom default untuk sorting
    public $sortDirection = 'desc'; // Arah sorting

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortField === $field
            ? ($this->sortDirection === 'asc' ? 'desc' : 'asc')
            : 'asc';

        $this->sortField = $field;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function render()
    {
        $taskBudget = LaporanLct::with([
            'picUser',
            'tasks' => function ($query) {
                $query->select('id', 'id_laporan_lct', 'created_at');
            }
        ])
        ->where('status_lct', 'approved')
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate($this->perPage);

        return view('livewire.budget-approval-history', compact('taskBudget'));
    }
}
