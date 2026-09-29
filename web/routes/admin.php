<?php

use App\Http\Controllers\Admin\AtletController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\SesiLatihanController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\LogAktivitasController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\SekolahController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('/users', UserController::class)->names('admin.users');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset-password');
    Route::post('/users/{user}/reset-token', [UserController::class, 'resetToken'])->name('admin.users.reset-token');

    Route::resource('/atlet', AtletController::class)->names('admin.atlet')->parameters(['atlet' => 'atlet']);
    Route::post('/atlet/{atlet}/verifikasi', [AtletController::class, 'verifikasi'])->name('admin.atlet.verifikasi');

    // Manajemen Jadwal Latihan Mingguan
    Route::resource('/sesi-latihan', SesiLatihanController::class)->names('admin.sesi-latihan')->parameters(['sesi-latihan' => 'sesiLatihan'])->except(['show']);
    Route::post('/sesi-latihan/{sesiLatihan}/status', [SesiLatihanController::class, 'setStatus'])->name('admin.sesi-latihan.set-status');
    Route::post('/sesi-latihan/{sesiLatihan}/duplicate', [SesiLatihanController::class, 'duplicate'])->name('admin.sesi-latihan.duplicate');

    // Legacy Fallback Event
    Route::resource('/event', EventController::class)->names('admin.event')->parameters(['event' => 'event'])->except(['show']);
    Route::post('/event/{event}/status', [EventController::class, 'setStatus'])->name('admin.event.set-status');

    Route::resource('/sekolah', SekolahController::class)->names('admin.sekolah')->parameters(['sekolah' => 'sekolah'])->except(['show']);
    Route::resource('/kategori', KategoriController::class)->names('admin.kategori')->parameters(['kategori' => 'kategori'])->except(['show']);

    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('admin.pengaturan.index');
    Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('admin.pengaturan.update');

    Route::get('/backup', [BackupController::class, 'index'])->name('admin.backup.index');
    Route::post('/backup/run', [BackupController::class, 'run'])->name('admin.backup.run');
    Route::post('/backup/restore', [BackupController::class, 'restore'])->name('admin.backup.restore');
    Route::get('/backup/download/{backup}', [BackupController::class, 'download'])->name('admin.backup.download');
    Route::get('/backup/riwayat', [BackupController::class, 'riwayat'])->name('admin.backup.riwayat');

    Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])->name('admin.log-aktivitas.index');
    Route::get('/log-aktivitas/export', [LogAktivitasController::class, 'export'])->name('admin.log-aktivitas.export');
});
