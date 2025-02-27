<?php

namespace App\Http\Livewire;

use App\Models\Pic;
use Livewire\Component;
use App\Models\LaporanLct;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user(); // Ambil user yang login

        // Ambil ID PIC yang terkait dengan user ini (subquery)
        $picId = Pic::where('user_id', $user->id)->value('id');

        // Query untuk mengambil laporan yang hanya sesuai dengan PIC dan Departemennya
        $laporans = LaporanLct::where('status_lct', 'in_progress') // Hanya yang dikirim EHS
            ->where('pic_id', $picId) // Hanya laporan yang ditugaskan ke PIC ini
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Jika tidak ada laporan, kirimkan pesan ke view
        $message = $laporans->isEmpty() ? 'Data belum ada' : null;

        return view('livewire.manajemen-lct-table', [
            'laporans' => $laporans,
            'message' => $message
        ]);
    }


}
