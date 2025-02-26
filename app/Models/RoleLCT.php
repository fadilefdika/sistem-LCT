<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleLct extends Model
{
    use HasFactory;

    protected $table = 'lct_roles'; 

    protected $fillable = [
        'nama_role',
        'deskripsi' 
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'lct_user_roles', 'user_id', 'role_lct_id');
    }

}
