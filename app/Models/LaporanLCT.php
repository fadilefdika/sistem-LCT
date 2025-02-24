<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LaporanLCT extends Model
{
    protected $table = 'lct_laporan'; // Pastikan sesuai dengan tabel di database
    protected $primaryKey = 'id_laporan_lct';
     protected $fillable = [
        'id_laporan_lct',
        'user_id', 
        'tanggal_temuan', 
        'area', 
        'detail_area', 
        'kategori_temuan', 
        'temuan_ketidaksesuaian', 
        'rekomendasi_safety', 
        'bukti_temuan'
    ];
    public $timestamps = true;

    public static function generateLCTId()
    {
        $bulan = Carbon::now()->format('m');
        $tahun = Carbon::now()->format('y');

        // Ambil ID terbesar dalam bulan dan tahun ini, pastikan ID yang diambil sudah dalam format angka
        $lastId = self::whereMonth('created_at', $bulan)
            ->whereYear('created_at', Carbon::now()->year)
            ->max(DB::raw('CAST(SUBSTRING(id_laporan_lct, 1, 4) AS INT)')); // Ganti UNSIGNED dengan INT

        // Increment ID, jika tidak ada ID maka mulai dari 1
        $nextId = $lastId ? ($lastId + 1) : 1;

        // Format ID agar menjadi 4 digit, dengan padding 0 di depan
        $kodeUrut = str_pad($nextId, 4, '0', STR_PAD_LEFT);

        // Kembalikan format ID LCT
        return "{$kodeUrut}{$bulan}{$tahun}";
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
