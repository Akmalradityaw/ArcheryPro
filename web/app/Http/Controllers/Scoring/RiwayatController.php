<?php

namespace App\Http\Controllers\Scoring;

use App\Http\Controllers\Controller;
use App\Models\Sesi;
use Illuminate\View\View;

class RiwayatController extends Controller
{
    public function index(): View
    {
        return view('scoring.riwayat.index', [
            'sesi' => Sesi::with(['atlet', 'sesiLatihan', 'event'])->where('created_by', auth()->id())->latest()->paginate(10),
        ]);
    }

    public function show(Sesi $sesi): View
    {
        abort_if($sesi->created_by !== auth()->id(), 403);

        return view('scoring.riwayat.show', [
            'sesi' => $sesi->load(['atlet', 'sesiLatihan', 'event']),
            'ends' => $sesi->skor()->orderBy('end_ke')->get(),
        ]);
    }
}
