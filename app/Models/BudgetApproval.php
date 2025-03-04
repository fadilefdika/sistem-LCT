<?php

namespace App\Models;

use App\Models\Pic;
use App\Models\LaporanLct;
use Illuminate\Database\Eloquent\Model;

class BudgetApproval extends Model
{
    protected $table = 'lct_budget_approval';

    protected $fillable = [
        'pic_id',
        'budget',
        'deskripsi',
        'status_budget',
        'lampiran',
        'created_at',
        'updated_at',
        'id_laporan_lct',
    ];

    public function pic()
    {
        return $this->belongsTo(Pic::class, 'pic_id');
    }

    public function laporanLct()
    {
        return $this->belongsTo(LaporanLct::class, 'id_laporan_lct', 'id_laporan_lct');
    }

    public function rejects()
    {
        return $this->hasMany(RejectLaporan::class, 'id_budget_approval');
    }
}
