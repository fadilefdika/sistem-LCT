<?php

namespace App\Http\Livewire;

use App\Models\Pic;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LctDepartemenPic;

class RoleDataTable extends Component
{
    use WithPagination;

    public $search = '';
    protected $listeners = ['refreshTable' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function render()
    {
        // Ambil data LctDepartemenPic dengan relasi pic dan departemen
        $pics = LctDepartemenPic::with(['pic.user', 'departemen'])->paginate(10); // Pastikan relasi 'user' ikut di-load

        // Menyiapkan data untuk ditampilkan di view
        $data = $pics->map(function ($pic) {
            return [
                'id' => $pic->id, // ID relasi di LctDepartemenPic
                'pic_id' => $pic->pic->id ?? null, // ID dari PIC, beri default null untuk menghindari error
                'user_name' => $pic->pic->user->fullname ?? 'N/A', // Nama PIC
                'user_email' => $pic->pic->user->email ?? 'N/A', // Email PIC
                'departments' => $pic->departemen->nama_departemen ?? 'N/A', // Nama Departemen
                'department_id' => $pic->departemen->id ?? null,
                'user_id' => $pic->pic->user_id ?? null, // ID pengguna yang terkait dengan PIC
            ];
        });

        return view('livewire.role-data-table', compact('data', 'pics'));
    }


}
