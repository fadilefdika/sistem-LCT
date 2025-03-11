<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LaporanLCT;
use Illuminate\Support\Facades\Auth;

class TableLctReport extends Component
{
    use WithPagination;

    public $search = '';
    public $filterKategori = '';
    public $perPage = 10;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterKategori()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Ambil laporan LCT dengan kategori yang statusnya 'open'
        $query = LaporanLct::with('kategori')->where('status_lct', 'open');

        // Pastikan menggunakan paginate sebelum return
        $laporans = $query->orderBy('created_at', 'desc')->paginate($this->perPage);

        return view('livewire.table-lct-report', [
            'laporans' => $laporans, // Perbaiki koma berlebih
        ]);
    }

}
