<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LaporanLct;
use Illuminate\Support\Facades\Auth;

class ProgressPerbaikanTable extends Component
{
    use WithPagination;

    public function render()
    {
        $user = Auth::user();
        $picId = \App\Models\Pic::where('user_id', $user->id)->value('id');

        // Ambil laporan yang sedang dalam perbaikan
        $laporans = LaporanLct::where('status_lct', 'in_progress')
            ->where('pic_id', $picId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.progress-perbaikan-table', [
            'laporans' => $laporans
        ]);
    }
}
