<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LaporanLct;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RiwayatLctTable extends Component
{
    use WithPagination;

    public function render()
    {
        // Mengambil user dengan relasi roleLct
        $user = User::with('roleLct')->find(Auth::id());

        // Ambil satu role jika roleLct hasMany
        $role = optional($user->roleLct->first())->name;

        if ($role === 'ehs' || $role === 'manajer') {
            // Jika role-nya "ehs", ambil semua laporan dengan status "closed"
            $laporans = LaporanLct::where('status_lct', 'closed')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Jika bukan "ehs", ambil laporan berdasarkan pic_id masing-masing
            $picId = \App\Models\Pic::where('user_id', $user->id)->value('id');

            $laporans = LaporanLct::where('status_lct', 'closed')
                ->where('pic_id', $picId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('livewire.riwayat-lct-table', [
            'laporans' => $laporans,
            'role' => $role
        ]);
    }
}

