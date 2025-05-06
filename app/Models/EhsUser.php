<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class EhsUser extends Authenticatable
{
    use HasFactory,HasRoles;

    protected $table = 'lct_ehs';

    protected $fillable = [
        'user_id',
        'username',
        'password_hash',
    ];

    protected $hidden = [
        'password',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany(RoleLct::class, 'lct_user_roles', 'model_id', 'role_id')
                    ->withTimestamps();
    }
}
