<?php

namespace App\Http\Controllers\Scoring;

use App\Http\Controllers\Controller;
use App\Models\Sesi;
use App\Models\Skor;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $hariIni = Sesi::where('created_by', auth()->id())->whereDate('tanggal_sesi', today());

        return view('scoring.dashboard', [
            'sesiHariIni' => (clone $hariIni)->count(),
            'totalSkorHariIni' => (clone $hariIni)->sum('total_skor'),
            'totalEndHariIni' => Skor::whereIn('sesi_id', (clone $hariIni)->pluck('id'))->count(),
            'riwayatTerbaru' => Sesi::with('atlet')->where('created_by', auth()->id())->latest()->take(5)->get(),
        ]);
    }
}
