<?php

use App\Http\Controllers\Pelatih\AnalisisController;
use App\Http\Controllers\Pelatih\AtletController;
use App\Http\Controllers\Pelatih\DashboardController;
use App\Http\Controllers\Pelatih\EksporController;
use App\Http\Controllers\Pelatih\EventController;
use App\Http\Controllers\Pelatih\InputSkorController;
use App\Http\Controllers\Pelatih\SesiLatihanController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:pelatih'])->prefix('pelatih')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('pelatih.dashboard');

    Route::resource('/atlet', AtletController::class)->names('pelatih.atlet');

    // Jadwal Sesi Latihan Mingguan
    Route::resource('/sesi-latihan', SesiLatihanController::class)->names('pelatih.sesi-latihan')->only(['index', 'show']);

    // Input Skor Latihan oleh Pelatih
    Route::get('/skor/input', [InputSkorController::class, 'create'])->name('pelatih.skor.create');
    Route::post('/skor/input', [InputSkorController::class, 'store'])->name('pelatih.skor.store');
    Route::post('/skor/konfirmasi', [InputSkorController::class, 'konfirmasi'])->name('pelatih.skor.konfirmasi');
    Route::post('/skor/selesai', [InputSkorController::class, 'selesai'])->name('pelatih.skor.selesai');

    Route::get('/analisis', [AnalisisController::class, 'index'])->name('pelatih.analisis.index');
    Route::get('/analisis/{atlet}', [AnalisisController::class, 'show'])->name('pelatih.analisis.show');
    Route::post('/sesi/{sesi}/catatan', [AnalisisController::class, 'updateCatatan'])->name('pelatih.sesi.catatan');

    Route::get('/ekspor', [EksporController::class, 'index'])->name('pelatih.ekspor.index');
    Route::post('/ekspor/performa', [EksporController::class, 'performa'])->name('pelatih.ekspor.performa');

    // Backward-compatibility: route event lama tetap terbaca jika diakses
    Route::resource('/event', EventController::class)->names('pelatih.event')->only(['index', 'show', 'edit', 'update']);
});

