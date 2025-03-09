<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\LaporanLctController;
use App\Http\Controllers\RiwayatLctController;
use App\Http\Controllers\ManajemenLctController;
use App\Http\Controllers\ManajemenPicController;
use App\Http\Controllers\BudgetApprovalController;
use App\Http\Controllers\LctTaskController;
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
Route::get('/kirim-email', [LaporanLctController::class, 'kirimEmail']);

Route::redirect('/', 'login');
Route::middleware(['auth','verified', 'role:ehs,pic,manajer'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::get('/riwayat-lct', [RiwayatLctController::class, 'index'])->name('admin.riwayat-lct');
});

// Middleware untuk Manajer & EHS 
Route::middleware(['auth', 'verified', 'role:manajer,ehs'])->group(function () {
    Route::get('/progress-perbaikan', [ProgressPerbaikanController::class, 'index'])->name('admin.progress-perbaikan');
    Route::get('/progress-perbaikan/{id_laporan_lct}', [ProgressPerbaikanController::class, 'show'])->name('admin.progress-perbaikan.show');

    Route::get('/manajemen-pic', [ManajemenPicController::class, 'index'])->name('admin.manajemen-pic');
});


// Middleware khusus Manajer
Route::middleware(['auth', 'verified', 'role:manajer'])->group(function () {
    Route::get('/budget-approval',[BudgetApprovalController::class, 'index'])->name('admin.budget-approval');
    Route::get('/budget-approval/{id_laporan_lct}',[BudgetApprovalController::class, 'show'])->name('admin.budget-approval.show');
    Route::post('/budget-approval/{id_laporan_lct}/approve',[BudgetApprovalController::class, 'approve'])->name('admin.budget-approval.approve');
    Route::post('/budget-approval/{id_laporan_lct}/reject',[BudgetApprovalController::class, 'reject'])->name('admin.budget-approval.reject'); 

    Route::get('/budget-approval-history',[BudgetApprovalController::class, 'history'])->name('admin.budget-approval-history');
    Route::get('/budget-approval-history/{id_laporan_lct}',[BudgetApprovalController::class, 'showHistory'])->name('admin.budget-approval-history.show');
});

// Middleware khusus EHS
Route::middleware(['auth', 'verified', 'role:ehs'])->group(function () {
    Route::get('/laporan-lct', [LaporanLctController::class, 'index'])->name('admin.laporan-lct');
    Route::get('/laporan-lct/{id_laporan_lct}', [LaporanLctController::class, 'show'])->name('admin.laporan-lct.show');
    Route::post('/laporan-lct/{id_laporan_lct}/assign', [LaporanLctController::class, 'assignToPic'])->name('admin.laporan-lct.assignToPic');

    Route::post('/progress-perbaikan/{id_laporan_lct}/approve', [ProgressPerbaikanController::class, 'approveLaporan'])->name('admin.progress-perbaikan.approve');
    Route::post('/progress-perbaikan/{id_laporan_lct}/reject', [ProgressPerbaikanController::class, 'rejectLaporan'])->name('admin.progress-perbaikan.reject');
    Route::post('/progress-perbaikan/{id_laporan_lct}/close', [ProgressPerbaikanController::class, 'closeLaporan'])->name('admin.progress-perbaikan.close');

});



// Middleware untuk PIC
Route::middleware(['auth', 'verified', 'role:pic'])->group(function () {
    Route::get('/manajemen-lct', [ManajemenLctController::class, 'index'])->name('admin.manajemen-lct');
    Route::get('/manajemen-lct/{id_laporan_lct}', [ManajemenLctController::class, 'show'])->name('admin.manajemen-lct.show');
    Route::post('/manajemen-lct/{id_laporan_lct}/store', [ManajemenLctController::class, 'store'])->name('admin.manajemen-lct.store');
    Route::post('/manajemen-lct/{id_laporan_lct}/submitBudget', [ManajemenLctController::class, 'submitBudget'])->name('admin.manajemen-lct.submitBudget');
    
    Route::post('/manajemen-lct/{id_laporan_lct}/storeTask', [LctTaskController::class, 'store'])->name('admin.manajemen-lct.storeTask');
    Route::post('/manajemen-lct/{id_task}/updateStatus', [LctTaskController::class, 'updateStatus'])->name('admin.manajemen-lct.updateStatus');


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
