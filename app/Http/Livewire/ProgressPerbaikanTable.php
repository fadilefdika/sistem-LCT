<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\LaporanLct;
use Livewire\WithPagination;
use App\Models\LctDepartement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProgressPerbaikanTable extends Component
{
    use WithPagination;

    public $riskLevel = '';
    public $statusLct = '';
    public $tanggalAwal = null;
    public $tanggalAkhir = null;
    public $departemenId = '';
    public $area = '';
    public $search = '';

    private function filterData()
    {
        $user = Auth::user();
        $role = optional($user->roleLct->first())->name;

        // === Status progress dan closed ===
        $progressStatuses = [
            'in_progress', 'progress_work', 'waiting_approval', 'approved', 'revision',
            'waiting_approval_temporary', 'approved_temporary', 'temporary_revision',
            'approved_taskbudget', 'waiting_approval_taskbudget', 'taskbudget_revision',
            'work_permanent', 'waiting_approval_permanent', 'approved_permanent', 'permanent_revision', 'closed'
        ];

        // === Base query untuk semua data (progress dan closed) ===
        $query = LaporanLct::query()
            ->select('*', DB::raw("
                CASE 
                    WHEN status_lct = 'closed' THEN 1
                    ELSE 0
                END as order_type
            "));

        // === Filter berdasarkan role ===
        if ($role === 'user') {
            $query->where('user_id', $user->id);
        } elseif ($role === 'manajer') {
            $departemenId = \App\Models\LctDepartement::where('user_id', $user->id)->value('id');
            if ($departemenId) {
                $query->where('departemen_id', $departemenId);
            } else {
                $query->whereRaw('1=0');
            }
        } elseif (!in_array($role, ['ehs'])) {
            $picId = \App\Models\Pic::where('user_id', $user->id)->value('id');
            $query->where('pic_id', $picId);
        }

        // === Filter status sesuai dengan array $progressStatuses ===
        $query->whereIn('status_lct', $progressStatuses);

        // === Filter tambahan ===
        if ($this->riskLevel) {
            $query->where('tingkat_bahaya', $this->riskLevel);
        }

        if ($this->statusLct) {
            $statuses = explode(',', $this->statusLct);
            $query->whereIn('status_lct', $statuses);
        }

        if ($this->tanggalAwal && $this->tanggalAkhir) {
            $startDate = \Carbon\Carbon::parse($this->tanggalAwal)->startOfDay(); // 00:00:00
            $endDate = \Carbon\Carbon::parse($this->tanggalAkhir)->endOfDay(); // 23:59:59
        
            $query->whereBetween('tanggal_temuan', [$startDate, $endDate]);
        }        
        
        if ($this->departemenId) {
            $query->where('departemen_id', $this->departemenId);
        }
        
        if ($this->area) {
            $query->where('area', 'like', '%' . $this->area . '%');
        }
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('judul', 'like', '%' . $this->search . '%')
                ->orWhere('lokasi', 'like', '%' . $this->search . '%');
            });
        }        

        // === Urutan: Progress (order_type 0) dulu, lalu Closed (order_type 1) ===
        return $query->orderBy('order_type')
                    ->orderByDesc('created_at')
                    ->paginate(10);
    }

    public function updatedRiskLevel($value)
    {
        $this->resetPage(); 
    }

    public function applyFilter()
    {
        $this->resetPage(); // Reset halaman pagination ketika filter diterapkan
    }

    public function resetFilters()
    {
        $this->riskLevel = '';
        $this->statusLct = '';
        $this->tanggalAwal = null;
        $this->tanggalAkhir = null;
        $this->departemenId = '';
        $this->area = '';
        $this->search = '';
        $this->resetPage(); // Reset halaman pagination
    }

    public function render()
    {
        $departments = \App\Models\LctDepartement::pluck('id', 'nama_departemen');

        // Defining $statusGroups in the render method
        $statusGroups = [
            'In Progress' => ['in_progress', 'progress_work', 'waiting_approval'],
            'Approved' => ['approved', 'approved_temporary', 'approved_taskbudget'],
            'Closed' => ['closed'],
            // Tambahkan kelompok status lainnya sesuai kebutuhan
        ];

        return view('livewire.progress-perbaikan-table', [
            'laporans' => $this->filterData(),
            'departments' => $departments,
            'statusGroups' => $statusGroups // Passing the statusGroups to the view
        ]);
    }
}


