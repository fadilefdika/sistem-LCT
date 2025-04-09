<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Tambahkan ini
use App\Models\LctDepartement;
use App\Models\PIC;

class LctDepartemenPic extends Model
{
    use HasFactory, SoftDeletes; // Gunakan SoftDeletes di sini

    protected $table = 'lct_departement_pic'; // Sesuaikan dengan nama tabel di database

    protected $fillable = [
        'departemen_id',
        'pic_id',
    ];

    public $timestamps = false; // Jika tabel tidak punya created_at & updated_at
    protected $dates = ['deleted_at'];
    // Relasi ke Departemen
    public function departemen()
    {
        return $this->belongsTo(LctDepartement::class, 'departemen_id');
    }

    // Relasi ke PIC (User)
    public function pic()
    {
        return $this->belongsTo(PIC::class, 'pic_id');
    }
}
