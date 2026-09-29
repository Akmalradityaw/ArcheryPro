<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterAtletController;
use App\Http\Controllers\Auth\RegisterPelatihController;
use App\Http\Controllers\Auth\RegisterScoringController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::get('/register/atlet', [RegisterAtletController::class, 'showForm'])->name('register.atlet');
    Route::post('/register/atlet', [RegisterAtletController::class, 'store'])->middleware('throttle:10,1');

    Route::get('/register/pelatih', [RegisterPelatihController::class, 'showForm'])->name('register.pelatih');
    Route::post('/register/pelatih', [RegisterPelatihController::class, 'store'])->middleware('throttle:10,1');

    Route::get('/register/scoring', [RegisterScoringController::class, 'showForm'])->name('register.scoring');
    Route::post('/register/scoring', [RegisterScoringController::class, 'store'])->middleware('throttle:10,1');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
