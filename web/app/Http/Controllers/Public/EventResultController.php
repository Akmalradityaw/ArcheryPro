<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\PengaturanSistem;
use App\Models\Sesi;
use App\Models\SesiLatihan;
use Illuminate\View\View;

class EventResultController extends Controller
{
    public function index(): View
    {
        $events = Event::where('is_publik', true)->latest('tanggal')->paginate(10);

        return view('public.event-result', compact('events'));
    }

    public function show(Event $event): View
    {
        abort_if(! $event->is_publik, 404);

        $isLeaderboardPublik = PengaturanSistem::getValue('leaderboard_publik', '1') === '1';
        abort_if(! $isLeaderboardPublik && ! auth()->check(), 403, 'Papan skor publik dinonaktifkan oleh administrator.');

        $query = Sesi::with('atlet.sekolah')
            ->where('event_id', $event->id);

        // Jika bukan user login (tamu umum), sembunyikan atlet yang tidak mengizinkan skor tampil publik
        if (! auth()->check()) {
            $query->whereHas('atlet', fn ($q) => $q->where('izinkan_tampil_publik', true));
        }

        $peringkat = $query->orderByDesc('total_skor')->get();

        return view('public.event-detail', compact('event', 'peringkat'));
    }

    public function showSesiLatihan(SesiLatihan $sesiLatihan): View
    {
        abort_if(! $sesiLatihan->is_publik, 404);

        $isLeaderboardPublik = PengaturanSistem::getValue('leaderboard_publik', '1') === '1';
        abort_if(! $isLeaderboardPublik && ! auth()->check(), 403, 'Papan skor publik dinonaktifkan oleh administrator.');

        $query = $sesiLatihan->sesi()->with(['atlet.sekolah', 'atlet.kategori']);

        if (! auth()->check()) {
            $query->whereHas('atlet', fn ($q) => $q->where('izinkan_tampil_publik', true));
        }

        $peringkat = $query->orderByDesc('total_skor')->get();

        return view('public.sesi-latihan-detail', compact('sesiLatihan', 'peringkat'));
    }
}
