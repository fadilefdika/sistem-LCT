<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Kategori;
use App\Models\LaporanLct;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class TableLctReport extends Component
{
    use WithPagination;

    public $filterKategori = '';


    public function filterKategori()
    {
        $this->resetPage();
    }

    public function applyFilter()
    {
        
    }

    public function filterData()
    {
        // Ambil laporan LCT dengan kategori yang statusnya 'open'
        $query = LaporanLct::with('kategori')->where('status_lct', 'open');

        // Pastikan menggunakan paginate sebelum return
        return $query->orderBy('created_at', 'desc')
        ->when($this->filterKategori, fn($query)=>$query->where('kategori_id',$this->filterKategori))
        ->paginate(10);
    }


    public function render()
    {
        $categories = Kategori::all();  

        return view('livewire.table-lct-report', [
            'laporans' => $this->filterData(), // Perbaiki koma berlebih
            'categories' => $categories,
        ]);
    }

}
