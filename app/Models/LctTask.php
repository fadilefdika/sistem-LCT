<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LctTask extends Model
{
    protected $table = 'lct_task';
    protected $fillable = ['id_laporan_lct', 'pic_id', 'deskripsi', 'status_task', 'due_date'];

    // Relasi ke Laporan LCT
    public function laporanLct()
    {
        return $this->belongsTo(LaporanLct::class, 'id_laporan_lct');
    }

    // Relasi ke PIC
    public function pic()
    {
        return $this->belongsTo(Pic::class, 'pic_id');
    }

}
