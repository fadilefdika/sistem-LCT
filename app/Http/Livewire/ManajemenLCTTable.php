<?php

namespace App\Http\Livewire;

use App\Models\Pic;
use Livewire\Component;
use App\Models\LaporanLct;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
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

        // Ambil ID PIC yang terkait dengan user ini
        $picId = Pic::where('user_id', $user->id)->value('id');

        // Daftar status yang dianggap progress
        $progressStatuses = [
            'in_progress', 'progress_work', 'waiting_approval', 'revision', 'approved',
            'waiting_approval_temporary', 'approved_temporary', 'temporary_revision',
            'waiting_approval_taskbudget', 'approved_taskbudget', 'taskbudget_revision',
            'work_permanent', 'waiting_approval_permanent', 'approved_permanent', 'permanent_revision',
            'closed'
        ];

        // Query utama
        $laporans = LaporanLct::select('*', DB::raw("
                CASE 
                    WHEN status_lct = 'closed' THEN 1 
                    ELSE 0 
                END as order_type
            "))
            ->whereIn('status_lct', $progressStatuses)
            ->where('pic_id', $picId)
            ->orderBy('order_type') // Urutkan progress dulu, lalu closed
            ->orderByDesc('updated_at') // Urutkan berdasarkan tanggal
            ->paginate(10);

        // Pesan jika kosong
        $message = $laporans->isEmpty() ? 'Data belum ada' : null;

        return view('livewire.manajemen-lct-table', [
            'laporans' => $laporans,
            'message' => $message
        ]);
    }
}
