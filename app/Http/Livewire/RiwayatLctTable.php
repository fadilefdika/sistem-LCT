<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LaporanLct;
use Illuminate\Support\Facades\Auth;

class RiwayatLctTable extends Component
{
    use WithPagination;

    public function render()
    {
        $user = Auth::user();
        $picId = \App\Models\Pic::where('user_id', $user->id)->value('id');

        // Ambil laporan yang sudah selesai atau ditolak
        $laporans = LaporanLct::whereIn('status_lct', ['closed', 'rejected'])
            ->where('pic_id', $picId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.riwayat-lct-table', [
            'laporans' => $laporans
        ]);
    }
}

