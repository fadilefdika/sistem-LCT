<?php

namespace App\Models;

use Spatie\Permission\Models\Permission;

class PermissionLCT extends Permission
{
    protected $table = 'permissions_lct';
    protected $primaryKey = 'id'; // Pastikan sesuai dengan struktur database

    protected $fillable = ['name', 'guard_name'];

    // Override atribut default Spatie
    public function getNameAttribute()
    {
        return $this->attributes['name'];
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
    }
}
