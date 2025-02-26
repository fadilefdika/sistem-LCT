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
}
