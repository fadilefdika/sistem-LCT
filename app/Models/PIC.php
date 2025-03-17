<?php

namespace App\Models;

use App\Models\User;
use App\Models\LctDepartement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pic extends Model
{
    use HasFactory;
    protected $table = 'lct_pic';
    protected $fillable = ['user_id'];

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
