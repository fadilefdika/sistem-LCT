<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BudgetApproval; // Sesuaikan dengan model yang digunakan
use App\Models\LaporanLct;
use Illuminate\Support\Facades\Auth;

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
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $role = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            // Ambil dari session terlebih dahulu, fallback ke relasi jika tidak ada
            $role = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }
        

        $query = LaporanLct::with([
            'picUser',
            'tasks:id,id_laporan_lct,updated_at', // lebih ringkas
        ])
        ->whereIn('status_lct', ['waiting_approval_taskbudget', 'taskbudget_revision','work_permanent','approved_taskbudget','waiting_approval_permanent','permanent_revision','approved_permanent']);

        // Filter berdasarkan role 'manajer'
        if ($role === 'manajer') {
            $departemenId = \App\Models\LctDepartement::where('user_id', $user->id)->value('id');

            if ($departemenId) {
                $query->where('departemen_id', $departemenId);
            } else {
                $query->whereRaw('1=0'); // tidak mengembalikan data
            }
        }

        // Ambil data dengan sorting dan pagination
        $taskBudget = $query
            ->orderByRaw("
                CASE 
                    WHEN status_lct = 'waiting_approval_taskbudget' THEN 1
                    WHEN status_lct = 'taskbudget_revision' THEN 2
                    WHEN status_lct = 'work_permanent' THEN 3
                    WHEN status_lct = 'approved_taskbudget' THEN 4
                    WHEN status_lct = 'waiting_approval_permanent' THEN 5
                    WHEN status_lct = 'permanent_revision' THEN 6
                    WHEN status_lct = 'approved_permanent' THEN 7
                    ELSE 99
                END
            ")
            ->orderBy($this->sortField ?? 'created_at', $this->sortDirection ?? 'desc')
            ->paginate($this->perPage ?? 10);



        return view('livewire.budget-approval-table', compact('taskBudget'));
    }



}
