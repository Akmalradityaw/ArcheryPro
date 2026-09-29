<?php

use App\Http\Controllers\Api\V1\AtletController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PublicController;
use App\Http\Controllers\Api\V1\SesiLatihanController;
use App\Http\Controllers\Api\V1\SkorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - ArcheryPro REST API v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Autentikasi & Data Publik
    Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::get('/public/leaderboard', [PublicController::class, 'leaderboard'])->name('api.public.leaderboard');
    Route::get('/public/sesi-latihan', [PublicController::class, 'sesiPublik'])->name('api.public.sesi-latihan');

    // Endpoint Terproteksi Bearer Token Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        // User & Auth
        Route::get('/auth/me', [AuthController::class, 'me'])->name('api.auth.me');
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');

        // Sesi Latihan Mingguan
        Route::get('/sesi-latihan', [SesiLatihanController::class, 'index'])->name('api.sesi-latihan.index');
        Route::get('/sesi-latihan/{id}', [SesiLatihanController::class, 'show'])->name('api.sesi-latihan.show');

        // Input Skor
        Route::get('/atlet', [SkorController::class, 'listAtlet'])->name('api.atlet.list');
        Route::post('/skor', [SkorController::class, 'store'])->name('api.skor.store');

        // Atlet: Performa, Riwayat, Catatan & Privasi
        Route::get('/atlet/{id}/performa', [AtletController::class, 'performa'])->name('api.atlet.performa');
        Route::get('/atlet/{id}/riwayat', [AtletController::class, 'riwayat'])->name('api.atlet.riwayat');
        Route::post('/sesi/{sesi}/baca-catatan', [AtletController::class, 'bacaCatatan'])->name('api.sesi.baca-catatan');
        Route::post('/sesi/{sesi}/catatan', [AtletController::class, 'updateCatatanPelatih'])->name('api.sesi.catatan');
        Route::put('/atlet/privasi', [AtletController::class, 'updatePrivasi'])->name('api.atlet.privasi');
    });
});
