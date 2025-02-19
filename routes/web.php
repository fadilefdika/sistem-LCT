<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\LaporanLCTController;
use App\Http\Controllers\RiwayatLCTController;
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

Route::get('/laporan-lct', [LaporanLCTController::class, 'index'])->name('admin.laporan-lct');
Route::get('/laporan-lct/detail', [LaporanLCTController::class, 'detail'])->name('admin.laporan-lct.detail');
Route::get('/progress-perbaikan', [ProgressPerbaikanController::class, 'index'])->name('admin.progress-perbaikan');
Route::get('/progress-perbaikan/detail', [ProgressPerbaikanController::class, 'detail'])->name('admin.progress-perbaikan.detail');
Route::get('/riwayat-lct', [RiwayatLCTController::class, 'index'])->name('admin.riwayat-lct');

Route::get('/users', [UserController::class, 'index'])->name('users');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    // Route for the getting the data feed
    Route::get('/json-data-feed', [DataFeedController::class, 'getDataFeed'])->name('json_data_feed');

   
});

Route::get('/test-db', function () {
    try {
        $users = DB::select("SELECT TOP 1 * FROM dbo.users");
        return response()->json($users);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});