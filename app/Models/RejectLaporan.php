<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectLaporan extends Model
{
    use HasFactory;
    protected $table = 'lct_laporan_reject';
    protected $fillable = ['id_laporan_lct', 'alasan_reject'];
}
