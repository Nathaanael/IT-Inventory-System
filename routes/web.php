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
    Route::get('/dashboard', function () {
        return view('dashboard.itsas', ['title' => 'Dashboard']);
    })->name('dashboard');

    // ── Inventory Management (User, IP, Password) ──────
    Route::middleware(['role:IT SAS Supervisor,Super Admin'])->prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', function () {
            return view('inventory.inventory', ['title' => 'Data Inventory']);
        })->name('index');
    });

    // ── Activity Logs & Master Data (Hanya Super Admin) ──
    Route::middleware(['role:Super Admin'])->group(function () {
        
        // Logs
        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', function () {
                return "Halaman Audit & Activity Logs";
            })->name('index');
        });

        // Master Data Users & Departments
        Route::prefix('master')->name('master.')->group(function () {
            Route::get('/users', function () {
                return "Halaman Manajemen User";
            })->name('users');
            
            Route::get('/departments', function () {
                return "Halaman Master Departemen";
            })->name('departments');
        });

    });

});














