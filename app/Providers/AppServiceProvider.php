<?php

namespace App\Providers;

use App\Models\LaporanLct;
use App\Models\Pic;
use App\Models\LctDepartement;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
{
    View::composer('components.app.header', function ($view) {
        $route = Route::current();
        $laporan = null;

        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $roleName = optional($user->roles->first())->name;
        } else {
            $user = Auth::user();
            $roleName = optional($user->roleLct->first())->name;
        }

        // Cek jika rute adalah admin.laporan-lct.show dan memiliki parameter id
        if ($route && $route->getName() === 'admin.laporan-lct.show') {
            $id_laporan_lct = $route->parameter('id_laporan_lct');
            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
        }else if ($route && $route->getName() === 'ehs.laporan-lct.show') {
            $id_laporan_lct = $route->parameter('id_laporan_lct');
            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
        }else if ($route && $route->getName() === 'admin.manajemen-lct.show') {
            $id_laporan_lct = $route->parameter('id_laporan_lct');
            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
        }else if ($route && $route->getName() === 'admin.progress-perbaikan.show') {
            $id_laporan_lct = $route->parameter('id_laporan_lct');
            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
        }else if ($route && $route->getName() === 'ehs.progress-perbaikan.show') {
            $id_laporan_lct = $route->parameter('id_laporan_lct');
            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
        }else if ($route && $route->getName() === 'admin.riwayat-lct.show') {
                $id_laporan_lct = $route->parameter('id_laporan_lct');
                $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
        }else if ($route && $route->getName() === 'admin.budget-approval.show') {
            $id_laporan_lct = $route->parameter('id_laporan_lct');
            $laporan = LaporanLct::with('tasks')->where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
        }else if ($route && $route->getName() === 'admin.budget-approval-history.show') {
            $id_laporan_lct = $route->parameter('id_laporan_lct');
            $laporan = LaporanLct::with('tasks')->where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
        }

        // Define the relevant statuses for each role
        $relevantStatuses = match ($roleName) {
            'ehs' => ['open','in_progress','progress_work', 'waiting_approval', 'waiting_approval_temporary', 'waiting_approval_permanent'],
            'pic' => ['in_progress','progress_work','revision', 'temporary_revision', 'permanent_revision', 'taskbudget_revision'],
            'manajer' => ['waiting_approval_taskbudget'],
            default => [],
        };

        // Initialize query to fetch relevant LCT statuses
        $query = LaporanLct::query()->whereIn('status_lct', $relevantStatuses);

        // Filter based on the role
        if ($roleName === 'pic') {
            $picId = Pic::where('user_id', $user->id)->value('id');
            if ($picId) {
                // Apply filter for 'pic' role to check if they are assigned to this LCT
                $query->where('pic_id', $picId);
            }
        } elseif ($roleName === 'manajer') {
            $departemenId = LctDepartement::where('user_id', $user->id)->value('id');
            if ($departemenId) {
                // Filter LCT based on the department managed by the manager
                $query->where('departemen_id', $departemenId);
            }
        }

        $notifikasiLCT = $query->get();

        // Send the variables to the view
        $view->with([
            'notifikasiLCT' => $notifikasiLCT,
            'laporan' => $laporan,
            'roleName' => $roleName,
        ]);


    });
}
}
