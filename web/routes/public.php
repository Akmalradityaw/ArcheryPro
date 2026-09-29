<?php

use App\Http\Controllers\Public\EventResultController;
use App\Http\Controllers\Public\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events', [EventResultController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventResultController::class, 'show'])->name('events.show');
Route::get('/sesi-latihan/{sesiLatihan}', [EventResultController::class, 'showSesiLatihan'])->name('public.sesi-latihan.show');
