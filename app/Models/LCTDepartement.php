<?php

namespace App\Models;

use App\Models\Pic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LctDepartement extends Model
{
    protected $table = 'lct_departement';
    protected $fillable = ['nama_departemen'];

    // Relasi ke PIC (Melalui Pivot Table)
    public function pic()
    {
        return $this->belongsToMany(Pic::class, 'lct_departement_pic', 'departemen_id', 'pic_id');
    }
}
