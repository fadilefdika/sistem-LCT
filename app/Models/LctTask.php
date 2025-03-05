<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LctTask extends Model
{
    protected $table = 'lct_task';
    protected $fillable = ['id_laporan_lct', 'pic_id', 'task_name', 'status_task', 'due_date', 'validate_by_ehs'];

    protected $attributes = [
        'status_task' => 'pending',
        'validate_by_ehs'=> false
    ];

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
