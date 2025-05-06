<?php

namespace App\Http\Livewire;

use App\Models\EhsUser;
use Livewire\Component;
use Livewire\WithPagination;

class EhsDataTable extends Component
{
    use WithPagination;

    public $search = '';
    protected $listeners = ['refreshTable' => '$refresh'];

    public function render()
    {
        $ehs = EhsUser::with('user')
            ->where('username', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.ehs-data-table', compact('ehs'));
    }
}


