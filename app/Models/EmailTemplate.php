<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $table = 'lct_emailtemplates'; // Nama tabel di SQL Server

    protected $primaryKey = 'id';

    public $timestamps = false; // Jika tidak pakai created_at dan updated_at otomatis

    protected $fillable = [
        'name',
        'slug',
        'content',
        'created_at',
        'updated_at',
    ];

    // Jika kamu pakai route binding dengan slug
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
