<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaLct extends Model
{
    use SoftDeletes;

    protected $table = 'lct_area'; // nama tabel di SQL Server

    protected $fillable = [
        'nama_area',
    ];

    protected $dates = ['deleted_at']; // untuk soft deletes

    public function laporan()
    {
        return $this->hasMany(LaporanLct::class, 'area_id');
    }

}
