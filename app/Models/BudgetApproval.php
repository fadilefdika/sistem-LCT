<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetApproval extends Model
{
    protected $table = 'lct_budget_approval';

    protected $fillable = [
        'pic_id',
        'budget',
        'deskripsi',
        'status_budget',
        'lampiran',
        'created_at',
        'updated_at',
        'id_laporan_lct',
    ];

    public function pic()
    {
        return $this->belongsTo(Pic::class, 'pic_id');
    }

}
