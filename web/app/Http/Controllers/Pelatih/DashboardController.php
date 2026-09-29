<?php

namespace App\Http\Controllers\Pelatih;

use App\Http\Controllers\Controller;
use App\Models\Atlet;
use App\Models\Event;
use App\Models\Sesi;
use App\Models\SesiLatihan;
use App\Models\Skor;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $sesiBulanIniCount = Sesi::whereMonth('tanggal_sesi', now()->month)
            ->whereYear('tanggal_sesi', now()->year)
            ->count();

        $panahBulanIni = Skor::whereHas('sesi', function ($q) {
            $q->whereMonth('tanggal_sesi', now()->month)
                ->whereYear('tanggal_sesi', now()->year);
        })->count() * 6;

        return view('pelatih.dashboard', [
            'totalAtlet' => Atlet::where('status', 'aktif')->count(),
            'eventBerlangsung' => SesiLatihan::where('status', 'berlangsung')->count(), // alias for compatibility
            'sesiLatihanAktif' => SesiLatihan::where('status', 'berlangsung')->count(),
            'sesiLatihanMendatang' => SesiLatihan::where('status', 'mendatang')->orderBy('tanggal')->take(3)->get(),
            'sesiBulanIni' => $sesiBulanIniCount,
            'panahBulanIni' => $panahBulanIni,
            'sesiTerbaru' => Sesi::with(['atlet', 'sesiLatihan'])
                ->latest('tanggal_sesi')
                ->latest('id')
                ->take(5)
                ->get(),
        ]);
    }
}
