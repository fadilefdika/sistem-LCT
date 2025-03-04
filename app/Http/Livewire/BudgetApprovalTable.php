<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BudgetApproval; // Sesuaikan dengan model yang digunakan

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
        $budgets = BudgetApproval::with('laporanLct')
        ->whereIn('status_budget', ['pending', 'rejected'])
        ->where(function ($query) {
            $query->whereHas('pic.user', function ($subQuery) {
                $subQuery->where('fullname', 'like', '%' . $this->search . '%');
            })
            ->orWhere('deskripsi', 'like', '%' . $this->search . '%');
        })
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate($this->perPage);

        return view('livewire.budget-approval-table', compact('budgets'));
    }

}
