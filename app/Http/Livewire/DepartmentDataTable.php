<?php

namespace App\Http\Livewire;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LctDepartement;

class DepartmentDataTable extends Component
{
    use WithPagination;

    public $search = '';
    protected $listeners = ['refreshTable' => '$refresh'];

    public function render()
    {
        $departments = LctDepartement::with('user')
            ->where('nama_departemen', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.department-data-table', compact('departments'));
    }
}
