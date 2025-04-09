<?php

namespace App\Http\Livewire;

use App\Models\Kategori;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryDataTable extends Component
{
    use WithPagination;

    public $search = '';
    protected $listeners = ['refreshTable' => '$refresh'];

    public function render()
    {
        $categories = Kategori::where('nama_kategori', 'like', '%' . $this->search . '%')->paginate(10);
        return view('livewire.category-data-table', [
            'categories' => $categories,
        ]);
    }

}
