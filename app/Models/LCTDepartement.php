<?php

namespace App\Models;

use App\Models\Pic;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LctDepartement extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'lct_departement';
    protected $fillable = ['nama_departemen','user_id'];
    protected $dates = ['deleted_at'];

    // Relasi ke PIC (Melalui Pivot Table)
    public function pic()
    {
        return $this->belongsToMany(Pic::class, 'lct_departement_pic', 'departemen_id', 'pic_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Kolom yang menghubungkan dengan User
    }
}
