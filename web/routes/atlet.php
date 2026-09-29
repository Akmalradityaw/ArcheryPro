<?php

use App\Http\Controllers\Atlet\DashboardController;
use App\Http\Controllers\Atlet\EksporController;
use App\Http\Controllers\Atlet\GrafikController;
use App\Http\Controllers\Atlet\ProfilController;
use App\Http\Controllers\Atlet\RiwayatController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:atlet'])->prefix('atlet')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('atlet.dashboard');
    Route::post('/sesi/{sesi}/baca-catatan', [DashboardController::class, 'bacaCatatan'])->name('atlet.catatan.baca');
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('atlet.riwayat.index');
    Route::get('/grafik', [GrafikController::class, 'index'])->name('atlet.grafik.index');
    Route::get('/profil', [ProfilController::class, 'edit'])->name('atlet.profil.edit');
    Route::put('/profil', [ProfilController::class, 'update'])->name('atlet.profil.update');

    Route::get('/ekspor', [EksporController::class, 'index'])->name('atlet.ekspor.index');
    Route::post('/ekspor/pdf', [EksporController::class, 'pdf'])->name('atlet.ekspor.pdf');
    Route::post('/ekspor/excel', [EksporController::class, 'excel'])->name('atlet.ekspor.excel');
    Route::post('/ekspor/word', [EksporController::class, 'word'])->name('atlet.ekspor.word');
});
