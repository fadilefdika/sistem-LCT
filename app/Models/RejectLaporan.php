<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectLaporan extends Model
{
    use HasFactory;
    protected $table = 'lct_laporan_reject';
    protected $fillable = [
        'id_laporan_lct',
        'alasan_reject',
        'tipe_reject',
        'user_id',
        'role',
        'status_lct',
        'action',
        'note',
        'created_by',
    ];
    

    public function laporanLct()
    {
        return $this->belongsTo(LaporanLct::class, 'id_laporan_lct', 'id_laporan_lct');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','id');
    }
}
