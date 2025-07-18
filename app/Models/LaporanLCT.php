<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanLct extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'lct_laporan'; // Pastikan sesuai dengan tabel di database
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_laporan_lct',
        'user_id', 
        'tanggal_temuan', 
        'area_id', 
        'detail_area', 
        'kategori_id', 
        'temuan_ketidaksesuaian', 
        'rekomendasi_safety', 
        'bukti_temuan',
        'pic_id',   
        'catatan_ehs',
        'departemen_id',   
        'tingkat_bahaya',  
        'rekomendasi',  
        'due_date',  
        'due_date_temp',  
        'due_date_perm',  
        'date_completion', 
        'date_closed', 
        'status_lct',  
        'budget_approval',
        'bukti_perbaikan',
        'estimated_budget',
        'tindakan_perbaikan',
        'first_viewed_by_ehs_at',
        'first_viewed_by_manager_at',
        'first_viewed_by_pic_at',
        'approved_temporary_by_ehs',
        'attachments'
    ];
    
    protected $casts = [
        'bukti_temuan' => 'array',
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
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id', 'id');
    }

    public function picc()
    {
        return $this->belongsTo(Pic::class, 'pic_id');
    }

    // Relasi ke users melalui lct_pic
    public function picUser()
    {
        return $this->hasOneThrough(User::class, Pic::class, 'id', 'id', 'pic_id', 'user_id');
    }

    public static function findByLCTId($idLCT)
    {
        return self::where('id_laporan_lct', $idLCT)->first();
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    // Model LaporanLct
    public function tasks()
    {
        return $this->hasMany(LctTasks::class, 'id_laporan_lct', 'id_laporan_lct');
    }


    public function rejectLaporan()
    {
        return $this->hasMany(RejectLaporan::class, 'id_laporan_lct', 'id_laporan_lct');
    }

    public function area()
    {
        return $this->belongsTo(AreaLct::class, 'area_id');
    }

    public function departemenPic()
    {
        return $this->belongsTo(LctDepartemenPic::class, 'pic_id');
    }

    public function departemen()
    {
        return $this->belongsTo(LctDepartement::class, 'departemen_id');
    }

    public function getIsTaskOnlyAttribute()
    {
        $userId = auth()->id();
        $picId = \App\Models\Pic::where('user_id', $userId)->value('id');

        // Jika user adalah PIC utama → bukan task-only
        if ($this->pic_id == $picId) {
            return false;
        }

        // Jika bukan PIC utama tapi ada di task → task-only
        return $this->tasks()->where('pic_id', $picId)->exists();
    }


}
