<?php

namespace App\Models;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class PermissionLct extends Permission
{
    protected $table = 'lct_permissions';

    protected $fillable = ['name', 'guard_name'];

    // Override atribut default Spatie agar tetap kompatibel
    public function getNameAttribute()
    {
        return $this->attributes['name'];
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
    }
}
