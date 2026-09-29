<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Atlet;
use App\Models\Sesi;
use App\Models\SesiLatihan;
use App\Models\Skor;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalAtlet' => Atlet::count(),
            'totalJadwal' => SesiLatihan::count(),
            'totalSesi' => Sesi::count(),
            'totalSkor' => Skor::count(),
            'sesiTerbaru' => Sesi::with(['atlet', 'sesiLatihan', 'event'])->latest()->take(5)->get(),
        ]);
    }
}
