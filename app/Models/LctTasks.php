<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LctTasks extends Model
{
    protected $table = 'lct_tasks';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'id_laporan_lct', 
        'task_name', 
        'name_pic', 
        'due_date', 
        'notes', 
        'pic_id', 
        'status'
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    // Relasi ke Laporan LCT
    public function laporanLct()
    {
        return $this->belongsTo(LaporanLct::class, 'id_laporan_lct', 'id');
    }

    // Relasi ke PIC
    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id', 'id');
    }
}
