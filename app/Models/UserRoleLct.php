<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRoleLct extends Model
{
    use HasFactory;

    protected $table = 'user_role_lct'; // Nama tabel

    protected $fillable = [
        'user_id', 
        'role_id'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke RoleLct (Tabel roles_lct)
    public function role()
    {
        return $this->belongsTo(RoleLct::class, 'role_id');
    }
}
