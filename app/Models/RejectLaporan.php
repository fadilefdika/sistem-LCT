<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectLaporan extends Model
{
    use HasFactory;
    protected $table = 'lct_laporan_reject';
    protected $fillable = ['id_laporan_lct', 'alasan_reject', 'tipe_reject'];

    public function laporanLct()
    {
        return $this->belongsTo(LaporanLct::class, 'id_laporan_lct', 'id_laporan_lct');
    }

    public function budgetApproval()
    {
        return $this->belongsTo(BudgetApproval::class, 'id_laporan_lct', 'id_laporan_lct');
    }

}
