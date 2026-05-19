<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\PendaftarController as AdminPendaftarController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\TampilanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PpdbController;
use Illuminate\Support\Facades\Route;

// Public PPDB
Route::get('/', [PpdbController::class, 'index'])->name('home');
Route::get('/jurusan', [PpdbController::class, 'jurusanIndex'])->name('ppdb.jurusan');
Route::get('/pengumuman', [PpdbController::class, 'pengumuman'])->name('ppdb.pengumuman');
Route::get('/daftar', [PpdbController::class, 'create'])->name('ppdb.daftar');
Route::post('/daftar', [PpdbController::class, 'store'])->name('ppdb.store');
Route::get('/daftar/sukses/{no_pendaftaran}', [PpdbController::class, 'sukses'])->name('ppdb.sukses');
Route::get('/daftar/bukti/{no_pendaftaran}', [PpdbController::class, 'bukti'])->name('ppdb.bukti');
Route::get('/cek-status', [PpdbController::class, 'cekStatus'])->name('ppdb.cek-status');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pendaftar', [AdminPendaftarController::class, 'index'])->name('pendaftar.index');
    Route::get('/pendaftar/export', [AdminPendaftarController::class, 'export'])->name('pendaftar.export');
    Route::post('/pendaftar/bulk', [AdminPendaftarController::class, 'bulk'])->name('pendaftar.bulk');
    Route::get('/pendaftar/{pendaftar}', [AdminPendaftarController::class, 'show'])->name('pendaftar.show');
    Route::put('/pendaftar/{pendaftar}', [AdminPendaftarController::class, 'update'])->name('pendaftar.update');
    Route::delete('/pendaftar/{pendaftar}', [AdminPendaftarController::class, 'destroy'])->name('pendaftar.destroy');

    Route::get('/jurusan', [JurusanController::class, 'index'])->name('jurusan.index');
    Route::post('/jurusan', [JurusanController::class, 'store'])->name('jurusan.store');
    Route::put('/jurusan/{jurusan}', [JurusanController::class, 'update'])->name('jurusan.update');
    Route::delete('/jurusan/{jurusan}', [JurusanController::class, 'destroy'])->name('jurusan.destroy');

    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');

    // Super Admin only: Tampilan & Branding
    Route::middleware('superadmin')->group(function () {
        Route::get('/tampilan', [TampilanController::class, 'index'])->name('tampilan.index');
        Route::put('/tampilan/branding', [TampilanController::class, 'updateBranding'])->name('tampilan.branding');
        Route::post('/tampilan/logo', [TampilanController::class, 'uploadLogo'])->name('tampilan.logo');
        Route::delete('/tampilan/logo', [TampilanController::class, 'deleteLogo'])->name('tampilan.logo.delete');
        Route::post('/tampilan/favicon', [TampilanController::class, 'uploadFavicon'])->name('tampilan.favicon');
        Route::post('/tampilan/hero-bg', [TampilanController::class, 'uploadHeroBg'])->name('tampilan.hero-bg');
        Route::delete('/tampilan/hero-bg', [TampilanController::class, 'deleteHeroBg'])->name('tampilan.hero-bg.delete');
    });
});
