<?php

use Illuminate\Support\Facades\DB;
use App\Http\Livewire\RoleDataTable;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LctTaskController;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\RoleDataController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LctReportController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\RiwayatLctController;
use App\Http\Controllers\CategoryDataController;
use App\Http\Controllers\ManajemenLctController;
use App\Http\Controllers\BudgetApprovalController;
use App\Http\Controllers\DepartmentDataController;
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
// Route::get('/kirim-email', [LctReportController::class, 'kirimEmail']);

Route::redirect('/', 'login');

Route::middleware(['auth', 'verified'])->get('/choose-destination', function () {
    return view('pages.choose-destination');
})->name('choose-destination');


// Middleware untuk Semua Role yang Dapat Akses Dashboard
Route::middleware(['auth', 'verified', 'role:ehs,pic,manajer,user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Form Laporan LCT
    Route::get('/report-form', [UserController::class, 'index'])->name('report-form');  
    Route::post('/laporan-lct/store', [LctReportController::class, 'store'])->name('laporan-lct.store');

    // Progress Perbaikan
    Route::prefix('progress-perbaikan')->name('admin.progress-perbaikan.')->group(function () {
        Route::get('/', [ProgressPerbaikanController::class, 'index'])->name('index');
        Route::get('/{id_laporan_lct}', [ProgressPerbaikanController::class, 'show'])->name('show');
    });

    // Riwayat LCT
    Route::prefix('riwayat-lct')->name('admin.riwayat-lct.')->group(function () {
        Route::get('/', [RiwayatLctController::class, 'index'])->name('index');
        Route::get('/{id_laporan_lct}', [RiwayatLctController::class, 'show'])->name('show');
    });
});

// Middleware untuk Manajer & EHS
Route::middleware(['auth', 'verified', 'role:manajer,ehs'])->group(function () {
    Route::get('/laporan-lct', [LctReportController::class, 'index'])->name('admin.laporan-lct.index');
    Route::get('/laporan-lct/{id_laporan_lct}', [LctReportController::class, 'show'])->name('admin.laporan-lct.show');
    Route::delete('/laporan-lct/{id_laporan_lct}', [LctReportController::class, 'destroy'])->name('admin.laporan-lct.destroy');

    Route::get('/master-data/role-data', [RoleDataController::class,'index'])->name('admin.master-data.role-data.index');

    
    Route::prefix('master-data/department-data')->name('admin.master-data.department-data.')->group(function () {
        Route::get('/', [DepartmentDataController::class, 'index'])->name('index');
        Route::post('/', [DepartmentDataController::class, 'store'])->name('store');
        Route::put('/{id}', [DepartmentDataController::class, 'update'])->name('update');
        Route::delete('/{id}', [DepartmentDataController::class, 'destroy'])->name('destroy');
        Route::get('/search-users', [DepartmentDataController::class, 'searchUsers'])->name('search-users');
    });

    Route::prefix('master-data/role-data')->name('admin.master-data.role-data.')->group(function () {
        Route::get('/', [RoleDataController::class, 'index'])->name('index');
        Route::post('/', [RoleDataController::class, 'store'])->name('store');
        Route::put('/{id}', [RoleDataController::class, 'update'])->name('update');
        Route::delete('/{id}', [RoleDataController::class, 'destroy'])->name('destroy');
        Route::get('/search-users', [RoleDataController::class, 'searchUsers'])->name('search-users');
        Route::get('/search-department', [RoleDataController::class, 'searchDepartment'])->name('search-department');
    });

    Route::prefix('master-data/category-data')->name('admin.master-data.category-data.')->group(function () {
        Route::get('/', [CategoryDataController::class, 'index'])->name('index');
        Route::post('/', [CategoryDataController::class, 'store'])->name('store');
        Route::put('/{id}', [CategoryDataController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoryDataController::class, 'destroy'])->name('destroy');
    });
});

// Middleware khusus Manajer
Route::middleware(['auth', 'verified', 'role:manajer'])->group(function () {
    // Budget Approval
    Route::prefix('budget-approval')->name('admin.budget-approval.')->group(function () {
        Route::get('/', [BudgetApprovalController::class, 'index'])->name('index');
        Route::get('/{id_laporan_lct}', [BudgetApprovalController::class, 'show'])->name('show');
        Route::post('/{id_laporan_lct}/approve', [BudgetApprovalController::class, 'approve'])->name('approve');
        Route::post('/{id_laporan_lct}/reject', [BudgetApprovalController::class, 'reject'])->name('reject');
    });

    // Budget Approval History (Menggunakan nama yang diinginkan)
    Route::prefix('budget-approval-history')->name('admin.budget-approval-history.')->group(function () {
        Route::get('/', [BudgetApprovalController::class, 'history'])->name('index');
        Route::get('/{id_laporan_lct}', [BudgetApprovalController::class, 'showHistory'])->name('show');
    });
});


// Middleware khusus EHS
Route::middleware(['auth', 'verified', 'role:ehs'])->group(function () {
    Route::post('/laporan-lct/{id_laporan_lct}/assign', [LctReportController::class, 'assignToPic'])->name('admin.laporan-lct.assignToPic');

    Route::prefix('progress-perbaikan/{id_laporan_lct}')->name('admin.progress-perbaikan.')->group(function () {
        Route::post('/approve', [ProgressPerbaikanController::class, 'approveLaporan'])->name('approve');
        Route::post('/reject', [ProgressPerbaikanController::class, 'rejectLaporan'])->name('reject');
        Route::post('/close', [ProgressPerbaikanController::class, 'closeLaporan'])->name('close');
    });
});

// Middleware untuk PIC
Route::middleware(['auth', 'verified', 'role:pic'])->prefix('manajemen-lct')->name('admin.manajemen-lct.')->group(function () {
    Route::get('/', [ManajemenLctController::class, 'index'])->name('index');
    Route::get('/{id_laporan_lct}', [ManajemenLctController::class, 'show'])->name('show');
    Route::post('/{id_laporan_lct}/store', [ManajemenLctController::class, 'store'])->name('store');
    Route::post('/{id_laporan_lct}/submitTaskBudget', [ManajemenLctController::class, 'submitTaskBudget'])->name('submitTaskBudget');
    // Route::delete('/{id_laporan_lct}/attachment/{index}', [ManajemenLctController::class, 'deleteAttachment'])->name('deleteAttachment');

    Route::post('/{id_laporan_lct}/storeTask', [LctTaskController::class, 'store'])->name('storeTask');
    Route::post('/{id_task}/updateStatus', [LctTaskController::class, 'updateStatus'])->name('updateStatus');
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
