<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LaporanLCT;
use Illuminate\Support\Facades\Auth;

class LaporanTable extends Component
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
        $user = Auth::user(); // Ambil user yang sedang login

        // Ambil laporan yang sesuai dengan departemen PIC
        $query = LaporanLCT::where('status_lct', 'in_progress')
            ->where('departemen_id', $user->departemen_id)
            ->with('user');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_pelapor', 'like', '%' . $this->search . '%')
                  ->orWhere('kategori_temuan', 'like', '%' . $this->search . '%')
                  ->orWhere('area', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterKategori) {
            $query->where('kategori_temuan', $this->filterKategori);
        }

        $laporans = $query->orderBy('created_at', 'desc')->paginate($this->perPage);

        return view('livewire.laporan-table', [
            'laporans' => $laporans,
            'kategoriOptions' => LaporanLCT::select('kategori_temuan')->distinct()->pluck('kategori_temuan'),
        ]);
    }
}
