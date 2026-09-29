<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Atlet;
use App\Models\Event;
use App\Models\Sesi;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // ponytail: 6 terbaru cukup untuk highlight; arsip lengkap di /events (paginate)
        $events = Event::where('is_publik', true)->latest('tanggal')->take(6)->get();

        return view('public.home', [
            'events' => $events,
            'totalEvent' => Event::where('is_publik', true)->count(),
            'totalAtlet' => Atlet::where('status', 'aktif')->count(),
            'totalSesi' => Sesi::count(),
        ]);
    }
}
