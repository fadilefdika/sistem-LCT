<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    EhsController,
    UserController,
    AreaLctController,
    LctTaskController,
    DataFeedController,
    RoleDataController,
    DashboardController,
    LctReportController,
    RiwayatLctController,
    CategoryDataController,
    ManajemenLctController,
    BudgetApprovalController,
    DepartmentDataController,
    EhsDahboardController,
    ProgressPerbaikanController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Pilihan Login
Route::get('/', fn () => view('auth.choose-login'))->name('choose-login');
Route::get('/login', fn () => view('auth.login'))->name('login');

// Login EHS
Route::get('/login-ehs', [EhsController::class, 'showLoginForm'])->name('login-ehs');
Route::post('/login-ehs', [EhsController::class, 'login']);

// Setelah login memilih tujuan
Route::middleware(['auth', 'verified','role:user,pic,manajer'])->group(function () {
    Route::get('/choose-destination-user', fn () => view('pages.choose-destination'))->name('choose-destination-user');
});

Route::middleware(['auth:ehs', 'verified','role:ehs'])->group(function () {
    Route::get('/choose-destination-ehs', fn () => view('pages.choose-destination'))->name('choose-destination-ehs');
});

// =================== ROUTE UNTUK USER (role: pic, manajer, user) ===================
Route::middleware(['auth', 'verified', 'role:pic,manajer,user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Form Laporan LCT
    Route::get('/report-form', [UserController::class, 'index'])->name('report-form');  
    Route::post('/laporan-lct/store', [LctReportController::class, 'store'])->name('laporan-lct.store');

    // Progress Perbaikan
    Route::prefix('progress-perbaikan')->name('admin.progress-perbaikan.')->group(function () {
        Route::get('/', [ProgressPerbaikanController::class, 'index'])->name('index');
        Route::get('/{id_laporan_lct}', [ProgressPerbaikanController::class, 'show'])->name('show');
        Route::get('/{id_laporan_lct}/history', [ProgressPerbaikanController::class, 'history'])->name('history');
    });

    
});

// =================== ROUTE UNTUK PIC ===================
Route::middleware(['auth', 'verified', 'role:pic'])->prefix('manajemen-lct')->name('admin.manajemen-lct.')->group(function () {
    Route::get('/', [ManajemenLctController::class, 'index'])->name('index');
    Route::get('/{id_laporan_lct}', [ManajemenLctController::class, 'show'])->name('show');
    Route::post('/{id_laporan_lct}/store', [ManajemenLctController::class, 'store'])->name('store');
    Route::post('/{id_laporan_lct}/submitTaskBudget', [ManajemenLctController::class, 'submitTaskBudget'])->name('submitTaskBudget');
    // Route::delete('/{id_laporan_lct}/attachment/{index}', [ManajemenLctController::class, 'deleteAttachment'])->name('deleteAttachment');

    Route::post('/{id_laporan_lct}/storeTask', [LctTaskController::class, 'store'])->name('storeTask');
    Route::post('/{id_task}/updateStatus', [LctTaskController::class, 'updateStatus'])->name('updateStatus');
});

// =================== ROUTE UNTUK EHS ===================
Route::prefix('ehs')->middleware(['auth:ehs', 'verified', 'role:ehs'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('ehs.dashboard');

    // Form Laporan LCT
    Route::get('/report-form', [UserController::class, 'index'])->name('ehs.report-form');  
    Route::post('/laporan-lct/store', [LctReportController::class, 'store'])->name('ehs.laporan-lct.store');

    // Progress Perbaikan
    Route::prefix('progress-perbaikan')->name('ehs.progress-perbaikan.')->group(function () {
        Route::get('/', [ProgressPerbaikanController::class, 'index'])->name('index');
        Route::get('/{id_laporan_lct}', [ProgressPerbaikanController::class, 'show'])->name('show');
        Route::get('/{id_laporan_lct}/history', [ProgressPerbaikanController::class, 'history'])->name('history');
    });

    Route::post('/laporan-lct/{id_laporan_lct}/assign', [LctReportController::class, 'assignToPic'])->name('ehs.laporan-lct.assignToPic');

    Route::prefix('progress-perbaikan/{id_laporan_lct}')->name('ehs.progress-perbaikan.')->group(function () {
        Route::post('/approve', [ProgressPerbaikanController::class, 'approveLaporan'])->name('approve');
        Route::post('/reject', [ProgressPerbaikanController::class, 'rejectLaporan'])->name('reject');
        Route::post('/close', [ProgressPerbaikanController::class, 'closeLaporan'])->name('close');
    });

    // Laporan LCT
    Route::prefix('laporan-lct')->name('ehs.laporan-lct.')->group(function () {
        Route::get('/', [LctReportController::class, 'index'])->name('index');
        Route::get('/{id_laporan_lct}', [LctReportController::class, 'show'])->name('show');
        Route::post('/{id_laporan_lct}/close', [LctReportController::class, 'closed'])->name('closed');
    });

    // Master Data
    Route::prefix('master-data')->group(function () {
        Route::prefix('department-data')->name('ehs.master-data.department-data.')->group(function () {
            Route::get('/', [DepartmentDataController::class, 'index'])->name('index');
            Route::post('/', [DepartmentDataController::class, 'store'])->name('store');
            Route::put('/{id}', [DepartmentDataController::class, 'update'])->name('update');
            Route::delete('/{id}', [DepartmentDataController::class, 'destroy'])->name('destroy');
            Route::get('/search-users', [DepartmentDataController::class, 'searchUsers'])->name('search-users');
        });

        Route::prefix('role-data')->name('ehs.master-data.role-data.')->group(function () {
            Route::get('/', [RoleDataController::class, 'index'])->name('index');
            Route::post('/', [RoleDataController::class, 'store'])->name('store');
            Route::put('/{id}', [RoleDataController::class, 'update'])->name('update');
            Route::delete('/{id}', [RoleDataController::class, 'destroy'])->name('destroy');
            Route::get('/search-users', [RoleDataController::class, 'searchUsers'])->name('search-users');
            Route::get('/search-department', [RoleDataController::class, 'searchDepartment'])->name('search-department');
        });

        Route::prefix('category-data')->name('ehs.master-data.category-data.')->group(function () {
            Route::get('/', [CategoryDataController::class, 'index'])->name('index');
            Route::post('/', [CategoryDataController::class, 'store'])->name('store');
            Route::put('/{id}', [CategoryDataController::class, 'update'])->name('update');
            Route::delete('/{id}', [CategoryDataController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('area-data')->name('ehs.master-data.area-data.')->group(function () {
            Route::get('/', [AreaLctController::class, 'index'])->name('index');
            Route::post('/', [AreaLctController::class, 'store'])->name('store');
            Route::put('/{id}', [AreaLctController::class, 'update'])->name('update');
            Route::delete('/{id}', [AreaLctController::class, 'destroy'])->name('destroy');
        });
    });
});

// =================== ROUTE UNTUK MANAJER ===================
Route::middleware(['auth', 'verified', 'role:manajer'])->group(function () {
    // Laporan LCT
    Route::prefix('laporan-lct')->name('admin.laporan-lct.')->group(function () {
        Route::get('/', [LctReportController::class, 'index'])->name('index');
        Route::get('/{id_laporan_lct}', [LctReportController::class, 'show'])->name('show');
        Route::post('/{id_laporan_lct}/close', [LctReportController::class, 'closed'])->name('closed');
    });

    // Master Data
    Route::prefix('master-data')->group(function () {
        Route::prefix('department-data')->name('admin.master-data.department-data.')->group(function () {
            Route::get('/', [DepartmentDataController::class, 'index'])->name('index');
            Route::post('/', [DepartmentDataController::class, 'store'])->name('store');
            Route::put('/{id}', [DepartmentDataController::class, 'update'])->name('update');
            Route::delete('/{id}', [DepartmentDataController::class, 'destroy'])->name('destroy');
            Route::get('/search-users', [DepartmentDataController::class, 'searchUsers'])->name('search-users');
        });

        Route::prefix('role-data')->name('admin.master-data.role-data.')->group(function () {
            Route::get('/', [RoleDataController::class, 'index'])->name('index');
            Route::post('/', [RoleDataController::class, 'store'])->name('store');
            Route::put('/{id}', [RoleDataController::class, 'update'])->name('update');
            Route::delete('/{id}', [RoleDataController::class, 'destroy'])->name('destroy');
            Route::get('/search-users', [RoleDataController::class, 'searchUsers'])->name('search-users');
            Route::get('/search-department', [RoleDataController::class, 'searchDepartment'])->name('search-department');
        });

        Route::prefix('category-data')->name('admin.master-data.category-data.')->group(function () {
            Route::get('/', [CategoryDataController::class, 'index'])->name('index');
            Route::post('/', [CategoryDataController::class, 'store'])->name('store');
            Route::put('/{id}', [CategoryDataController::class, 'update'])->name('update');
            Route::delete('/{id}', [CategoryDataController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('area-data')->name('admin.master-data.area-data.')->group(function () {
            Route::get('/', [AreaLctController::class, 'index'])->name('index');
            Route::post('/', [AreaLctController::class, 'store'])->name('store');
            Route::put('/{id}', [AreaLctController::class, 'update'])->name('update');
            Route::delete('/{id}', [AreaLctController::class, 'destroy'])->name('destroy');
        });
    });

    
 // Budget Approval
    Route::prefix('budget-approval')->name('admin.budget-approval.')->group(function () {
        Route::get('/', [BudgetApprovalController::class, 'index'])->name('index');
        Route::get('/{id_laporan_lct}', [BudgetApprovalController::class, 'show'])->name('show');
        Route::post('/{id_laporan_lct}/approve', [BudgetApprovalController::class, 'approve'])->name('approve');
        Route::post('/{id_laporan_lct}/reject', [BudgetApprovalController::class, 'reject'])->name('reject');
        Route::get('/{id_laporan_lct}/history', [BudgetApprovalController::class, 'history'])->name('history');
    });

    // Budget Approval History (Menggunakan nama yang diinginkan)
    Route::prefix('budget-approval-history')->name('admin.budget-approval-history.')->group(function () {
        Route::get('/', [BudgetApprovalController::class, 'history'])->name('index');
        Route::get('/{id_laporan_lct}', [BudgetApprovalController::class, 'showHistory'])->name('show');
    });
});

