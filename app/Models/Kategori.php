<?php
namespace App\Models;

use App\Models\LaporanLct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lct_kategori'; // Nama tabel di database
    protected $fillable = ['nama_kategori']; // Kolom yang bisa diisi
    public $timestamps = true;
    
    public function laporan()
    {
        return $this->hasMany(LaporanLct::class, 'kategori_id');
    }
}
