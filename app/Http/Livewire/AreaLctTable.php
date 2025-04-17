<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AreaLct; // Import model AreaLct

class AreaLctTable extends Component
{
    use WithPagination; // Mengaktifkan pagination

    public function render()
    {
        // Ambil data dari tabel area_lct dengan pagination
        $areas = AreaLct::paginate(10); // Mengambil 10 data per halaman

        return view('livewire.area-lct-table', compact('areas'));
    }
}
