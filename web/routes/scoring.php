<?php

use App\Http\Controllers\Scoring\DashboardController;
use App\Http\Controllers\Scoring\InputSkorController;
use App\Http\Controllers\Scoring\RiwayatController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:scoring'])->prefix('scoring')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('scoring.dashboard');

    Route::get('/input', [InputSkorController::class, 'create'])->name('scoring.input.index');
    Route::post('/input', [InputSkorController::class, 'store'])->name('scoring.input.store');
    Route::post('/input/konfirmasi', [InputSkorController::class, 'konfirmasi'])->name('scoring.input.konfirmasi');
    Route::post('/input/selesai', [InputSkorController::class, 'selesai'])->name('scoring.input.selesai');

    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('scoring.riwayat.index');
    Route::get('/riwayat/{sesi}', [RiwayatController::class, 'show'])->name('scoring.riwayat.show');
});
