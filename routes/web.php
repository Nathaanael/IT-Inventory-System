<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ChangePasswordController;
// use App\Http\Controllers\DashboardController;
// use App\Http\Controllers\InventoryController;
// use App\Http\Controllers\ActivityLogController;

// ───────────────────────────────────────────────────
// HOME (Public / Redirect)
// ───────────────────────────────────────────────────
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// ───────────────────────────────────────────────────
// AUTHENTICATION
// ───────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login', ['title' => 'Log In']);
    })->name('login');
    
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


// ───────────────────────────────────────────────────
// GANTI PASSWORD (Harus login, tetapi belum tentu sudah isi password)
// ───────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/change-password', function () {
        return view('auth.change-password', ['title' => 'Change Password']);
    })->name('change_password');
    
    Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('auth.change-password.update');
});


// ───────────────────────────────────────────────────
// PROTECTED ROUTES (Membutuhkan Login & Password sudah di-set)
// ───────────────────────────────────────────────────
Route::middleware(['auth', 'first_login'])->group(function () {

    // ── Dashboard ──────────────────────────────────────
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // ── Inventory Management (User, IP, Password) ──────
    Route::middleware(['role:IT Support,Super Admin'])->prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ITSas\InventoryController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\ITSas\InventoryController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\ITSas\InventoryController::class, 'store'])->name('store');
        Route::get('/{inventory}/edit', [\App\Http\Controllers\ITSas\InventoryController::class, 'edit'])->name('edit');
        Route::put('/{inventory}', [\App\Http\Controllers\ITSas\InventoryController::class, 'update'])->name('update');
        Route::delete('/{inventory}', [\App\Http\Controllers\ITSas\InventoryController::class, 'destroy'])->name('destroy');
        
        // Vault Endpoints
        Route::post('/vault/set-pin', [\App\Http\Controllers\ITSas\InventoryController::class, 'setPin'])->name('vault.set-pin');
        Route::post('/vault/verify-pin', [\App\Http\Controllers\ITSas\InventoryController::class, 'verifyPin'])->name('vault.verify-pin');
        Route::post('/{inventory}/reveal', [\App\Http\Controllers\ITSas\InventoryController::class, 'revealPassword'])->name('vault.reveal');
    });

    // ── Activity Logs & Master Data (Hanya Super Admin) ──
    Route::middleware(['role:Super Admin'])->group(function () {
        
        // Logs
        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Sadmin\AuditController::class, 'index'])->name('index');
        });

        // Master Data Users & Departments
        Route::prefix('master')->name('master.')->group(function () {
            
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Sadmin\UserController::class, 'index'])->name('index');
                Route::post('/', [\App\Http\Controllers\Sadmin\UserController::class, 'store'])->name('store');
                Route::put('/{user}', [\App\Http\Controllers\Sadmin\UserController::class, 'update'])->name('update');
                Route::delete('/{user}', [\App\Http\Controllers\Sadmin\UserController::class, 'destroy'])->name('destroy');
            });
            
            Route::prefix('departments')->name('departments.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Sadmin\DepartmentController::class, 'index'])->name('index');
                Route::post('/', [\App\Http\Controllers\Sadmin\DepartmentController::class, 'store'])->name('store');
                Route::put('/{department}', [\App\Http\Controllers\Sadmin\DepartmentController::class, 'update'])->name('update');
                Route::delete('/{department}', [\App\Http\Controllers\Sadmin\DepartmentController::class, 'destroy'])->name('destroy');
            });
        });

    });

});














