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

        // Ambil ID terbesar dalam bulan dan tahun ini
        $lastId = self::whereMonth('created_at', $bulan)
            ->whereYear('created_at', Carbon::now()->year)
            ->max(DB::raw("TRY_CAST(id_laporan_lct AS INT)")); // Ganti UNSIGNED dengan TRY_CAST

        $nextId = $lastId ? ($lastId + 1) : 1;
        $kodeUrut = str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return "{$kodeUrut}{$bulan}{$tahun}";
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
