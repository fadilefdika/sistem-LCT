<?php

namespace App\Models;

use App\Models\LCTDepartement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LCTDepartemenPIC extends Model
{
    use HasFactory;

    protected $table = 'lct_departement_pic'; // Sesuaikan dengan nama tabel di database

    public $timestamps = false; // Jika tabel tidak punya created_at & updated_at

    // Relasi ke Departemen
    public function departemen()
    {
        return $this->belongsTo(LCTDepartement::class, 'departemen_id');
    }

    // Relasi ke PIC (User)
    public function pic()
    {
        return $this->belongsTo(PIC::class, 'pic_id');
    }
}
