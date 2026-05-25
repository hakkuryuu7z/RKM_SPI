<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AuthController;
use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\JalurController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. ROUTE REDIRECT (Halaman Utama)
// Langsung arahin ke dashboard, nanti satpam (middleware) yang ngecek sudah login atau belum
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// 2. KUBU TAMU (Cuma bisa diakses kalau BELUM login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// 3. KUBU MEMBER (Wajib login baru bisa masuk)
Route::middleware('auth')->group(function () {

    // Halaman Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Fitur Sinkronisasi Member API
    Route::get('/sync-member', [MemberController::class, 'syncApi'])->name('member.sync');

    Route::get('/members', [MemberController::class, 'index'])->name('members.index');

    // Fitur Kelola Pengguna (Full CRUD)
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Fitur Kelola Role (Full CRUD)
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/{id}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    Route::prefix('jalur')->group(function () {
        Route::get('/', [JalurController::class, 'index'])->name('jalur.index');
        Route::post('/store', [JalurController::class, 'store'])->name('jalur.store');
        Route::post('/import', [JalurController::class, 'import'])->name('jalur.import');
    });
    Route::post('/jalur/delete', [JalurController::class, 'destroy'])->name('jalur.destroy');
    Route::get('/jalur/template', [JalurController::class, 'downloadTemplate'])->name('jalur.template');
    Route::post('/jalur/import', [JalurController::class, 'import'])->name('jalur.import');
    // Fitur Keluar Sistem
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
