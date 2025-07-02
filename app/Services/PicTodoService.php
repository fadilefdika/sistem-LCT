<?php 

namespace App\Services;

use App\Models\LaporanLct;

class PicTodoService
{
    public static function getTodosCountOnlyFor($picId)
    {
        return [
            'correctiveLow' => LaporanLct::where('tingkat_bahaya', 'low')
                ->whereIn('status_lct', ['in_progress', 'progress_work'])
                ->where('pic_id', $picId)
                ->count(),

            'revisionLow' => LaporanLct::where('tingkat_bahaya', 'low')
                ->where('status_lct', 'revision')
                ->where('pic_id', $picId)
                ->count(),

            'temporaryInProgress' => LaporanLct::whereIn('tingkat_bahaya', ['medium', 'high'])
                ->whereIn('status_lct', ['in_progress', 'progress_work'])
                ->where('pic_id', $picId)
                ->count(),

            'revisionTemporary' => LaporanLct::where('status_lct', 'temporary_revision')
                ->where('pic_id', $picId)
                ->count(),

            'revisionBudget' => LaporanLct::where('status_lct', 'taskbudget_revision')
                ->where('pic_id', $picId)
                ->count(),

            'permanentWork' => LaporanLct::where('status_lct', 'work_permanent')
                ->where('pic_id', $picId)
                ->count(),
        ];
    }

    
}
