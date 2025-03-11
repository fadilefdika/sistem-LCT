<?php

namespace App\Providers;

use App\Models\LaporanLct;
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

        // Cek jika rute adalah admin.laporan-lct.show dan memiliki parameter id
        if ($route && $route->getName() === 'admin.laporan-lct.show') {
            $id_laporan_lct = $route->parameter('id_laporan_lct');
            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
        }else if ($route && $route->getName() === 'admin.manajemen-lct.show') {
            $id_laporan_lct = $route->parameter('id_laporan_lct');
            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
        }else if ($route && $route->getName() === 'admin.progress-perbaikan.show') {
            $id_laporan_lct = $route->parameter('id_laporan_lct');
            $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
        }else if ($route && $route->getName() === 'admin.riwayat-lct.show') {
                $id_laporan_lct = $route->parameter('id_laporan_lct');
                $laporan = LaporanLct::where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
        }else if ($route && $route->getName() === 'admin.budget-approval.show') {
            $id_laporan_lct = $route->parameter('id_laporan_lct');
            $laporan = LaporanLct::with('budget')->where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
        }else if ($route && $route->getName() === 'admin.budget-approval-history.show') {
            $id_laporan_lct = $route->parameter('id_laporan_lct');
            $laporan = LaporanLct::with('budget')->where('id_laporan_lct', $id_laporan_lct)->with('user')->first();
        }

        $view->with('laporan', $laporan);
    });
}
}
