<?php

namespace App\Models;

use App\Models\User;
use App\Models\LctDepartement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pic extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lct_pic'; // Menentukan nama tabel
    protected $fillable = ['user_id']; // Menentukan kolom yang bisa diisi secara massal

    // Menentukan kolom yang menggunakan SoftDeletes
    protected $dates = ['deleted_at']; // Pastikan Eloquent mengetahui kolom deleted_at

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Relasi ke Departemen (Melalui Pivot Table)
    public function departemen()
    {
        return $this->belongsToMany(LctDepartement::class, 'lct_departement_pic', 'pic_id', 'departemen_id');
    }
}
