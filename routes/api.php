<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\JurusanController;
use App\Http\Controllers\Api\PendaftarController;
use App\Http\Controllers\Api\PengaturanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Semua route di bawah ini menggunakan prefix /api/v1.
| Public routes tidak memerlukan autentikasi.
| Admin routes memerlukan Bearer token dari Sanctum.
|
*/

Route::prefix('v1')->group(function () {

    // ---------------------------------------------------------------
    // AUTH
    // ---------------------------------------------------------------
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/user', [AuthController::class, 'user']);
    });

    // ---------------------------------------------------------------
    // PUBLIC ENDPOINTS (tanpa autentikasi)
    // ---------------------------------------------------------------
    Route::get('/info', [PengaturanController::class, 'publicInfo']);
    Route::get('/jurusan', [JurusanController::class, 'publicIndex']);
    Route::get('/jurusan/{jurusan}', [JurusanController::class, 'publicShow']);
    Route::post('/pendaftaran', [PendaftarController::class, 'store']);
    Route::get('/pendaftaran/status/{no_pendaftaran}', [PendaftarController::class, 'cekStatus']);
    Route::get('/pengumuman', [PengaturanController::class, 'pengumuman']);
    Route::post('/chat', [ChatController::class, 'chat'])->middleware('throttle:30,1');
    Route::post('/chat/form-assist', [ChatController::class, 'formAssist'])->middleware('throttle:60,1');

    // ---------------------------------------------------------------
    // ADMIN ENDPOINTS (memerlukan autentikasi Sanctum + role admin)
    // ---------------------------------------------------------------
    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Pendaftar
        Route::get('/pendaftar', [PendaftarController::class, 'index']);
        Route::post('/pendaftar/bulk', [PendaftarController::class, 'bulk']);
        Route::get('/pendaftar/{pendaftar}', [PendaftarController::class, 'show']);
        Route::put('/pendaftar/{pendaftar}', [PendaftarController::class, 'update']);
        Route::delete('/pendaftar/{pendaftar}', [PendaftarController::class, 'destroy']);

        // Jurusan
        Route::get('/jurusan', [JurusanController::class, 'index']);
        Route::post('/jurusan', [JurusanController::class, 'store']);
        Route::put('/jurusan/{jurusan}', [JurusanController::class, 'update']);
        Route::delete('/jurusan/{jurusan}', [JurusanController::class, 'destroy']);

        // Pengaturan
        Route::get('/pengaturan', [PengaturanController::class, 'index']);
        Route::put('/pengaturan', [PengaturanController::class, 'update']);
    });
});
