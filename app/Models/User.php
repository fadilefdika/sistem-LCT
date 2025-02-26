<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\UserRoleLct;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    protected $table = 'users';

    protected $fillable = [
        'npk',
        'password',
    ];

    public function username()
    {
        return 'npk'; // Login menggunakan NPK
    }

    // Relasi ke UserRoleLct (Mengambil satu peran utama)
    public function userRoleLct()
    {
        return $this->hasOne(UserRoleLct::class, 'user_id');
    }

    // Relasi ke RoleLct melalui UserRoleLct
    public function roleLct()
    {
        return $this->belongsToMany(RoleLct::class, 'lct_user_roles', 'user_id', 'role_lct_id');
    }

    // Ambil nama role utama atau default sebagai "user"
    public function getRoleNameAttribute()
    {
        return $this->userRoleLct?->role->nama_role ?? 'user';
    }
}

