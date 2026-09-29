<?php

namespace App\Http\Controllers\Atlet;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GrafikController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $atlet = auth()->user()->atlet;
        if (! $atlet) {
            return redirect()->route('atlet.profil.edit')->with('warning', 'Lengkapi profil dulu sebelum melihat skor.');
        }

        $sesi = $atlet->sesi()
            ->with(['sesiLatihan', 'event'])
            ->orderBy('tanggal_sesi')
            ->orderBy('id')
            ->get();

        $scores = $sesi->pluck('total_skor')->values();

        // 4-session Moving Average
        $movingAverages = [];
        for ($i = 0; $i < $scores->count(); $i++) {
            $windowStart = max(0, $i - 3);
            $window = $scores->slice($windowStart, $i - $windowStart + 1);
            $movingAverages[] = round($window->avg(), 1);
        }

        // Hitung selisih dari sesi sebelumnya
        $selisihTerakhir = 0;
        if ($scores->count() >= 2) {
            $selisihTerakhir = $scores->last() - $scores[$scores->count() - 2];
        }

        $ringkasan = [
            'total_sesi' => $sesi->count(),
            'skor_terbaik' => $scores->max() ?? 0,
            'skor_rata' => $scores->count() > 0 ? round($scores->avg(), 1) : 0,
            'skor_terakhir' => $scores->last() ?? 0,
            'selisih_terakhir' => $selisihTerakhir,
        ];

        return view('atlet.grafik', [
            'labels' => $sesi->map(fn ($s) => $s->tanggal_sesi ? $s->tanggal_sesi->format('d/m') : '-')->values(),
            'data' => $scores,
            'movingAvg' => $movingAverages,
            'ringkasan' => $ringkasan,
        ]);
    }
}
