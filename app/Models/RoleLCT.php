<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RoleLct extends SpatieRole
{
    protected $table = 'lct_roles'; 

    protected $fillable = [
        'name',  // Sesuai dengan nama kolom di tabel
        'deskripsi',
        'guard_name',
    ];

    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
    }

    public function assignedUsers()
    {
        // Menghubungkan RoleLct dengan EhsUser melalui tabel pivot lct_user_roles
        return $this->belongsToMany(EhsUser::class, 'lct_user_roles', 'role_id', 'model_id')
                    ->withTimestamps();
    }
}
