<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetApproval extends Model
{
    protected $table = 'lct_budget_approval';

    public function pic()
    {
        return $this->belongsTo(Pic::class, 'pic_id');
    }

}
