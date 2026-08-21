<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ChangePasswordController;

// ITSAS
use App\Http\Controllers\ITSas\DataSwitchController;
use App\Http\Controllers\ITSas\InventoryController;
use App\Http\Controllers\ITSas\SwitchMonitoringController;
use App\Http\Controllers\ITSas\TopologyDesignController;
use App\Http\Controllers\ITSas\EnvMonitoringController;
use App\Http\Controllers\ITSas\DeviceManagerController;

// Sadmin
use App\Http\Controllers\Sadmin\UserController;
use App\Http\Controllers\Sadmin\DepartmentController;
use App\Http\Controllers\Sadmin\AuditController;

// Dashboard
use App\Http\Controllers\DashboardController;

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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Inventory Management (User, IP, Password) ──────
    Route::middleware(['role:IT Support,Super Admin'])->prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{inventory}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{inventory}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{inventory}', [InventoryController::class, 'destroy'])->name('destroy');
        
        // Vault Endpoints
        Route::post('/vault/set-pin', [InventoryController::class, 'setPin'])->name('vault.set-pin');
        Route::post('/vault/verify-pin', [InventoryController::class, 'verifyPin'])->name('vault.verify-pin')->middleware('throttle:5,1');
        Route::post('/{inventory}/reveal', [InventoryController::class, 'revealPassword'])->name('vault.reveal')->middleware('throttle:5,1');
        
        Route::get('/{inventory}/ping', [InventoryController::class, 'ping'])->name('ping')->middleware('throttle:10,1');
        
        // RDP Endpoint
        Route::get('/{inventory}/rdp', [InventoryController::class, 'downloadRdp'])->name('rdp');
    });

    // ── Data Switch ──────────────────────────────────────
    Route::middleware(['role:IT Support,Super Admin'])->prefix('dataswitch')->name('dataswitch.')->group(function () {
        Route::get('/', [DataSwitchController::class, 'index'])->name('index');
        Route::post('/panel', [DataSwitchController::class, 'storePanel'])->name('storePanel');
        Route::post('/switch', [DataSwitchController::class, 'storeSwitch'])->name('storeSwitch');
        Route::put('/switch/{dataSwitch}', [DataSwitchController::class, 'updateSwitch'])->name('updateSwitch');
        Route::delete('/switch/{dataSwitch}', [DataSwitchController::class, 'destroySwitch'])->name('destroySwitch');
        Route::put('/panel/{panel}', [DataSwitchController::class, 'updatePanel'])->name('updatePanel');
        Route::delete('/panel/{panel}', [DataSwitchController::class, 'destroyPanel'])->name('destroyPanel');
        
        Route::get('/switch/{id}/ping', [DataSwitchController::class, 'ping'])->name('ping')->middleware('throttle:10,1');
    });

    // ── Switch Monitoring ────────────────────────────────
    Route::middleware(['role:IT Support,Super Admin'])->prefix('switchmonitoring')->name('switchmonitoring.')->group(function () {
        Route::get('/', [SwitchMonitoringController::class, 'index'])->name('index');
        Route::get('/status', [SwitchMonitoringController::class, 'status'])->name('status');
        Route::get('/{id}/ping', [SwitchMonitoringController::class, 'ping'])->name('ping')->middleware('throttle:10,1');
    });

    // ── Topology Design ─────────────────────────────────
    Route::middleware(['role:IT Support,Super Admin'])->prefix('topologydesign')->name('topologydesign.')->group(function () {
        Route::get('/', [TopologyDesignController::class, 'index'])->name('index');
        Route::post('/save', [TopologyDesignController::class, 'save'])->name('save');
        Route::post('/live-ping', [TopologyDesignController::class, 'livePing'])->name('livePing');
    });

    // ── Environment Monitoring ──────────────────────────
    Route::middleware(['role:IT Support,Super Admin'])->prefix('envmonitoring')->name('envmonitoring.')->group(function () {
        Route::get('/', [EnvMonitoringController::class, 'index'])->name('index');
        Route::get('/data', [EnvMonitoringController::class, 'getData'])->name('data');
    });

    // ── Device Manager ──────────────────────────────────
    Route::middleware(['role:IT Support,Super Admin'])->prefix('devicemanager')->name('devicemanager.')->group(function () {
        Route::get('/', [DeviceManagerController::class, 'index'])->name('index');
        Route::get('/create', [DeviceManagerController::class, 'create'])->name('create');
        Route::post('/store', [DeviceManagerController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [DeviceManagerController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [DeviceManagerController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [DeviceManagerController::class, 'destroy'])->name('destroy');
    });

    // ── Activity Logs & Master Data (Hanya Super Admin) ──
    Route::middleware(['role:Super Admin'])->group(function () {
        
        // Logs
        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', [AuditController::class, 'index'])->name('index');
        });

        // Master Data Users & Departments
        Route::prefix('master')->name('master.')->group(function () {
            
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::post('/', [UserController::class, 'store'])->name('store');
                Route::put('/{user}', [UserController::class, 'update'])->name('update');
                Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
                Route::post('/{user}/reset-pin', [UserController::class, 'resetPin'])->name('reset-pin');
                Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            });
            
            Route::prefix('departments')->name('departments.')->group(function () {
                Route::get('/', [DepartmentController::class, 'index'])->name('index');
                Route::post('/', [DepartmentController::class, 'store'])->name('store');
                Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
                Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
            });
        });

    });

});














