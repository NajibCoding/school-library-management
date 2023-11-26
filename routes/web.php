<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KonfigurasiController;

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

Route::get('/', function () {
    return redirect('auth/login');
});
Route::prefix('auth')->group(function () {
    Route::get('/', function () {
        return redirect('auth/login');
    });
    Route::get('login', [AuthController::class, 'index']);
    Route::get('forget_password', [AuthController::class, 'forget_password']);
    Route::post('request_reset_password', [AuthController::class, 'request_reset_password']);
    Route::get('reset_password/{reset_password_token}', [AuthController::class, 'reset_password']);
    Route::post('reset_password/{reset_password_token}', [AuthController::class, 'submit_reset_password']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::prefix('admin')->middleware('cek_login')->group(function () {
    //Change password
    Route::post('ajax_change_password', [AuthController::class, 'change_password']);
    Route::get('logout', [AuthController::class, 'logout']);
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
    });


    Route::prefix('konfigurasi')->group(function () {
        Route::get('/', [KonfigurasiController::class, 'index'])->middleware(['role_or_permission:SUPERADMIN|konfigurasi-list']);
        Route::get('ajax_list', [KonfigurasiController::class, 'ajax_list'])->middleware(['role_or_permission:SUPERADMIN|konfigurasi-list']);
        Route::post('ajax_update', [KonfigurasiController::class, 'ajax_update'])->middleware(['role_or_permission:SUPERADMIN|konfigurasi-edit']);
        Route::get('edit/{id}', [KonfigurasiController::class, 'edit'])->middleware(['role_or_permission:SUPERADMIN|konfigurasi-edit']);
        Route::post('ajax_get_one', [KonfigurasiController::class, 'ajaxGetOne'])->middleware(['role_or_permission:SUPERADMIN|konfigurasi-edit']);
        Route::post('ajax_reset', [KonfigurasiController::class, 'ajax_reset'])->middleware(['role_or_permission:SUPERADMIN|konfigurasi-reset']);
    });
});
