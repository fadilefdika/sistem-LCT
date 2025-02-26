<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermissionLct extends Model
{
    protected $table = 'lct_role_permissions'; // Pastikan ini benar sesuai database
    protected $primaryKey = 'id';

    protected $fillable = ['role_lct_id', 'permission_lct_id'];
}
