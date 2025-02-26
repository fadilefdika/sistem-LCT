<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\LaporanLct;
use Livewire\WithPagination;

class ManajemenLCTTable extends Component
{
    use WithPagination;

    public $search = '';
    protected $paginationTheme = 'tailwind';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $laporans = LaporanLct::orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.manajemen-lct-table',[
            'laporans' => $laporans,
        ]);
    }
}
