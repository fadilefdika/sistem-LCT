<?php

namespace App\Providers;

use App\Models\Pic;
use App\Models\LaporanLct;
use App\Models\LctDepartement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

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
                $roleName = 'ehs';
            } else {
                $user = Auth::guard('web')->user();
                // Ambil dari session terlebih dahulu, fallback ke relasi jika tidak ada
                $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
            }
            

            // Cek jika rute adalah admin.reporting.show.new dan memiliki parameter id
            if ($route && $route->getName() === 'admin.reporting.show.new') {
                $id_laporan_lct = $route->parameter('id_laporan_lct');
                $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
            }else if ($route && $route->getName() === 'ehs.reporting.show.new') {
                $id_laporan_lct = $route->parameter('id_laporan_lct');
                $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
            }else if ($route && $route->getName() === 'admin.manajemen-lct.show') {
                $id_laporan_lct = $route->parameter('id_laporan_lct');
                $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
            }else if ($route && $route->getName() === 'admin.reporting.show') {
                $id_laporan_lct = $route->parameter('id_laporan_lct');
                $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
            }else if ($route && $route->getName() === 'ehs.reporting.show') {
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

            $query = LaporanLct::query();

            if ($roleName === 'ehs') {
                $query->where(function ($subQuery) {
                    $subQuery->where(function ($q) {
                        // Filter status dengan pengecualian 'open' yang sudah dilihat
                        $q->whereIn('status_lct', [
                            'in_progress',
                            'progress_work',
                            'waiting_approval',
                            'waiting_approval_permanent'
                        ]);
                    })->orWhere(function ($q) {
                        // 'open' tapi belum pernah dilihat
                        $q->where('status_lct', 'open')
                          ->whereNull('first_viewed_by_ehs_at');
                    })->orWhere(function ($q) {
                        $q->where('status_lct', 'waiting_approval_temporary')
                          ->where('approved_temporary_by_ehs', 'not yet');
                    })->orWhere(function ($subSubQuery) {
                        $subSubQuery->whereIn('status_lct', [
                            'waiting_approval_taskbudget',
                            'taskbudget_revision',
                            'approved_taskbudget'
                        ])
                        ->where('approved_temporary_by_ehs', 'not yet');
                    });
                });
            }            
             elseif ($roleName === 'pic') {
                $query->whereIn('status_lct', ['in_progress','progress_work','revision', 'temporary_revision', 'permanent_revision', 'taskbudget_revision']);
                $picId = Pic::where('user_id', $user->id)->value('id');
                if ($picId) {
                    $query->where('pic_id', $picId);
                }
            } elseif ($roleName === 'manajer') {
                $query->whereIn('status_lct', ['waiting_approval_taskbudget']);
                $departemenId = LctDepartement::where('user_id', $user->id)->value('id');
                if ($departemenId) {
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
