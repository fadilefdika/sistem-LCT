<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PIC extends Model
{
    use HasFactory;
    protected $table = 'lct_pic';
    protected $fillable = ['user_id'];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Departemen (Melalui Pivot Table)
    public function departemen()
    {
        return $this->belongsToMany(LctDepartement::class, 'lct_departement_pic', 'pic_id', 'departemen_id');
    }

}
