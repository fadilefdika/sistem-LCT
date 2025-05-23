<?php
namespace App\Http\Livewire;

use App\Models\Kategori;
use App\Models\LctDepartement;
use Livewire\Component;
use App\Models\Pic;
use Livewire\WithPagination;

class MasterData extends Component
{
    use WithPagination;


    public function render()
    {
        $pics = Pic::with('user', 'departemen')->paginate(10);
        $kategories = Kategori::paginate(10); // Hapus all()
        $departments = LctDepartement::paginate(10);

        return view('livewire.master-data', compact('pics', 'kategories','departments'));
    }

}
