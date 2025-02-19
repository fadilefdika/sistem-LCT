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
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    use HasRoles;

    protected $table = 'users';

    protected $guard_name = 'web';

    public function username()
    {
        return 'npk'; // Laravel akan pakai npk untuk login
    }


    protected $fillable = [
        'npk',
        'password',
    ];

     // Relasi ke tabel user_roles_lct
    public function useRoleLct()
    {
        return $this->hasOne(UserRoleLct::class, 'user_id');
    }

    public function roleLct()
    {
        return $this->belongsToMany(RoleLct::class, 'user_roles_lct', 'user_id', 'role_lct_id');
    }

      // Ambil nama role langsung dari user_role_lct
    public function getRoleNameAttribute()
    {
        return $this->userRoleLct?->role->nama_role ?? 'user';
    }
}
