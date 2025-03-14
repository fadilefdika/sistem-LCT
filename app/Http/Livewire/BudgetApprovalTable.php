<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BudgetApproval; // Sesuaikan dengan model yang digunakan
use App\Models\LaporanLct;

class BudgetApprovalTable extends Component
{
    use WithPagination;

    public $search = ''; // Untuk pencarian
    public $perPage = 10; // Jumlah data per halaman
    public $sortField = 'created_at'; // Kolom default untuk sorting
    public $sortDirection = 'desc'; // Arah sorting

    protected $queryString = ['search', 'sortField', 'sortDirection'];

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
        ->whereIn('status_lct', ['waiting_approval_taskbudget', 'taskbudget_revision'])
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate($this->perPage);
    
    

        return view('livewire.budget-approval-table', compact('taskBudget'));
    }

}
