<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BudgetApproval;

class BudgetApprovalHistory extends Component
{
    use WithPagination;

    // Set jumlah data per halaman
    protected $paginationTheme = 'bootstrap'; // Atur tema pagination sesuai dengan kebutuhan

    public function render()
    {
        $budgets = BudgetApproval::with(['laporanLct', 'pic.user'])
            ->orderBy('updated_at', 'desc') // Urutkan berdasarkan tanggal terbaru
            ->paginate(10); // Sesuaikan dengan jumlah data per halaman

        return view('livewire.budget-approval-history', compact('budgets'));
    }
}
