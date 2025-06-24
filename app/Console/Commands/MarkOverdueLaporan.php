<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\LaporanLct;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class MarkOverdueLaporan extends Command
{
    protected $signature = 'laporan:mark-overdue';
    protected $description = 'Tandai laporan yang overdue berdasarkan due date dan tingkat bahaya';


    public function handle()
    {
        Log::info('[laporan:mark-overdue] Command started at ' . now());

        try {
            $now = Carbon::now('Asia/Jakarta');

            $laporanList = LaporanLct::whereNull('first_overdue_date')
                ->where('status_lct', '!=', 'closed')
                ->get();

            Log::info("[laporan:mark-overdue] Total laporan diperiksa: {$laporanList->count()}");

            $updatedCount = 0;
            $overdueIds = [];

            foreach ($laporanList as $laporan) {
                $overdue = false;

                if ($laporan->tingkat_bahaya === 'Low') {
                    if (is_null($laporan->date_completion) && $laporan->due_date && Carbon::parse($laporan->due_date)->lt($now)) {
                        $overdue = true;
                    }
                } elseif (in_array($laporan->tingkat_bahaya, ['Medium', 'High'])) {
                    if (
                        is_null($laporan->due_date_temp) && $laporan->due_date && Carbon::parse($laporan->due_date)->lt($now)
                    ) {
                        $overdue = true;
                    } elseif (
                        !is_null($laporan->due_date_temp) &&
                        is_null($laporan->due_date_perm) &&
                        Carbon::parse($laporan->due_date_temp)->lt($now)
                    ) {
                        $overdue = true;
                    } elseif (
                        !is_null($laporan->due_date_perm) &&
                        is_null($laporan->date_completion) &&
                        Carbon::parse($laporan->due_date_perm)->lt($now)
                    ) {
                        $overdue = true;
                    }
                }

                if ($overdue) {
                    $laporan->first_overdue_date = $now;
                    $laporan->save();

                    $updatedCount++;
                    $overdueIds[] = $laporan->id;

                    Log::info("[laporan:mark-overdue] ID {$laporan->id} ditandai overdue.");
                }
            }

            Log::info("[laporan:mark-overdue] Total laporan overdue yang ditandai: {$updatedCount}");
            if ($updatedCount > 0) {
                Log::info("[laporan:mark-overdue] ID yang ditandai overdue: " . implode(', ', $overdueIds));
            }

            Log::info('[laporan:mark-overdue] Command finished successfully.');
        } catch (\Throwable $e) {
            Log::error('[laporan:mark-overdue] ERROR: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

}
