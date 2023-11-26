<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\BooksController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AccessLogsController;
use App\Http\Controllers\Admin\BookAuthorsController;
use App\Http\Controllers\Admin\KonfigurasiController;
use App\Http\Controllers\Admin\BookPublishersController;

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

    // ========================== Book Management ===========================================
    Route::prefix('book_authors')->group(function () {
        Route::get('/', [BookAuthorsController::class, 'index'])->middleware(['role_or_permission:SUPERADMIN|book-authors-list']);
        Route::get('ajax_list', [BookAuthorsController::class, 'ajax_list'])->middleware(['role_or_permission:SUPERADMIN|book-authors-list']);
        Route::get('create', [BookAuthorsController::class, 'create'])->middleware(['role_or_permission:SUPERADMIN|book-authors-add']);
        Route::post('ajax_save', [BookAuthorsController::class, 'ajax_save'])->middleware(['role_or_permission:SUPERADMIN|book-authors-add']);
        Route::post('ajax_update', [BookAuthorsController::class, 'ajax_update'])->middleware(['role_or_permission:SUPERADMIN|book-authors-edit']);
        Route::get('edit/{id}', [BookAuthorsController::class, 'edit'])->middleware(['role_or_permission:SUPERADMIN|book-authors-edit']);
        Route::post('ajax_get_one', [BookAuthorsController::class, 'ajaxGetOne'])->middleware(['role_or_permission:SUPERADMIN|book-authors-edit']);
        Route::post('ajax_delete', [BookAuthorsController::class, 'ajax_delete'])->middleware(['role_or_permission:SUPERADMIN|book-authors-delete']);
    });

    Route::prefix('book_publishers')->group(function () {
        Route::get('/', [BookPublishersController::class, 'index'])->middleware(['role_or_permission:SUPERADMIN|book-publishers-list']);
        Route::get('ajax_list', [BookPublishersController::class, 'ajax_list'])->middleware(['role_or_permission:SUPERADMIN|book-publishers-list']);
        Route::get('create', [BookPublishersController::class, 'create'])->middleware(['role_or_permission:SUPERADMIN|book-publishers-add']);
        Route::post('ajax_save', [BookPublishersController::class, 'ajax_save'])->middleware(['role_or_permission:SUPERADMIN|book-publishers-add']);
        Route::post('ajax_update', [BookPublishersController::class, 'ajax_update'])->middleware(['role_or_permission:SUPERADMIN|book-publishers-edit']);
        Route::get('edit/{id}', [BookPublishersController::class, 'edit'])->middleware(['role_or_permission:SUPERADMIN|book-publishers-edit']);
        Route::post('ajax_get_one', [BookPublishersController::class, 'ajaxGetOne'])->middleware(['role_or_permission:SUPERADMIN|book-publishers-edit']);
        Route::post('ajax_delete', [BookPublishersController::class, 'ajax_delete'])->middleware(['role_or_permission:SUPERADMIN|book-publishers-delete']);
    });

    Route::prefix('books')->group(function () {
        Route::get('/', [BooksController::class, 'index'])->middleware(['role_or_permission:SUPERADMIN|books-list']);
        Route::get('ajax_list', [BooksController::class, 'ajax_list'])->middleware(['role_or_permission:SUPERADMIN|books-list']);
        Route::get('create', [BooksController::class, 'create'])->middleware(['role_or_permission:SUPERADMIN|books-add']);
        Route::post('ajax_save', [BooksController::class, 'ajax_save'])->middleware(['role_or_permission:SUPERADMIN|books-add']);
        Route::post('ajax_update', [BooksController::class, 'ajax_update'])->middleware(['role_or_permission:SUPERADMIN|books-edit']);
        Route::get('edit/{id}', [BooksController::class, 'edit'])->middleware(['role_or_permission:SUPERADMIN|books-edit']);
        Route::post('ajax_get_one', [BooksController::class, 'ajaxGetOne'])->middleware(['role_or_permission:SUPERADMIN|books-edit']);
        Route::post('ajax_delete', [BooksController::class, 'ajax_delete'])->middleware(['role_or_permission:SUPERADMIN|books-delete']);
    });

    // ========================== Book Management ===========================================

    Route::prefix('users')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->middleware(['role_or_permission:SUPERADMIN|users-list']);
        Route::get('ajax_list', [UsersController::class, 'ajax_list'])->middleware(['role_or_permission:SUPERADMIN|users-list']);
        Route::get('create', [UsersController::class, 'create'])->middleware(['role_or_permission:SUPERADMIN|users-add']);
        Route::post('ajax_save', [UsersController::class, 'ajax_save'])->middleware(['role_or_permission:SUPERADMIN|users-add']);
        Route::post('ajax_update', [UsersController::class, 'ajax_update'])->middleware(['role_or_permission:SUPERADMIN|users-edit']);
        Route::get('edit/{id}', [UsersController::class, 'edit'])->middleware(['role_or_permission:SUPERADMIN|users-edit']);
        Route::post('ajax_get_one', [UsersController::class, 'ajaxGetOne'])->middleware(['role_or_permission:SUPERADMIN|users-edit']);
        Route::post('ajax_delete', [UsersController::class, 'ajax_delete'])->middleware(['role_or_permission:SUPERADMIN|users-delete']);
    });

    Route::prefix('access_logs')->group(function () {
        Route::get('/', [AccessLogsController::class, 'index'])->middleware(['role_or_permission:SUPERADMIN|access-logs-list']);
        Route::get('ajax_list', [AccessLogsController::class, 'ajaxList'])->middleware(['role_or_permission:SUPERADMIN|access-logs-list']);
        Route::get('detail/{id}', [AccessLogsController::class, 'detil'])->middleware(['role_or_permission:SUPERADMIN|access-logs-list']);
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
