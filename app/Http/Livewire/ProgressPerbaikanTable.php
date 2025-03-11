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
        // Query laporan yang sedang dalam perbaikan
        $laporans = LaporanLct::whereIn('status_lct', ['in_progress', 'progress_work', 'waiting_approval', 'approved', 'revision','progress_work', 'waiting_approval_temporary','approved_temporary','temporary_revision','work_permanent','waiting_approval_permanent','approved_permanent','permanent_revision'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // dd($laporans);
        return view('livewire.progress-perbaikan-table', compact('laporans'));
    }

}
