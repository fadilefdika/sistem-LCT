<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\LaporanLctController;
use App\Http\Controllers\RiwayatLctController;
use App\Http\Controllers\LaporanPerbaikanLctController;
use App\Http\Controllers\ManajemenPicController;
use App\Http\Controllers\ProgressPerbaikanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', 'login');
Route::middleware(['auth','verified', 'role:ehs,pic'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::get('/riwayat-lct', [RiwayatLctController::class, 'index'])->name('admin.riwayat-lct');
});

// Middleware untuk Admin (EHS)
Route::middleware(['auth', 'verified', 'role:ehs'])->group(function () {

    Route::get('/laporan-lct', [LaporanLctController::class, 'index'])->name('admin.laporan-lct');
    Route::get('/laporan-lct/{id_laporan_lct}', [LaporanLctController::class, 'show'])->name('admin.laporan-lct.show');
    Route::post('/laporan-lct/{id_laporan_lct}/assign', [LaporanLctController::class, 'assignToPic'])->name('admin.laporan-lct.assignToPic');

    Route::get('/progress-perbaikan', [ProgressPerbaikanController::class, 'index'])->name('admin.progress-perbaikan');
    Route::get('/progress-perbaikan/{id_laporan_lct}', [ProgressPerbaikanController::class, 'show'])->name('admin.progress-perbaikan.show');

    Route::get('/manajemen-pic', [ManajemenPicController::class, 'index'])->name('admin.manajemen-pic');
});

// Middleware untuk PIC
Route::middleware(['auth', 'verified', 'role:pic'])->group(function () {
    Route::get('/manajemen-lct', [LaporanPerbaikanLctController::class, 'index'])->name('admin.manajemen-lct');
    Route::get('/manajemen-lct/{id_laporan_lct}', [LaporanPerbaikanLctController::class, 'show'])->name('admin.manajemen-lct.show');
    Route::post('/manajemen-lct/{id_laporan_lct}/store', [LaporanPerbaikanLctController::class, 'store'])->name('admin.manajemen-lct.store');
});

// Middleware untuk User
Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/laporan-lct/store', [LaporanLctController::class, 'store'])->name('laporan-lct.store');
});  

// Cek Koneksi Database
Route::get('/test-db', function () {
    try {
        $users = DB::select("SELECT TOP 1 * FROM dbo.users");
        return response()->json($users);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});
