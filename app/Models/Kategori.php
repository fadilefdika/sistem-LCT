<?php
namespace App\Models;

use App\Models\LaporanLct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'lct_kategori'; // Nama tabel di database
    protected $fillable = ['nama_kategori']; // Kolom yang bisa diisi

    public function laporan()
    {
        return $this->hasMany(LaporanLct::class, 'kategori_id');
    }
}
