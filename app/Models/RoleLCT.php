<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleLct extends Model
{
    use HasFactory;

    protected $table = 'roles_lct'; 

    protected $fillable = [
        'nama_role',
        'deskripsi' 
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles_lct', 'role_lct_id', 'user_id');
    }

}
