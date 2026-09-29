<?php

namespace App\Http\Controllers\Atlet;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\SesiLatihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiwayatController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $atlet = auth()->user()->atlet;
        if (! $atlet) {
            return redirect()->route('atlet.profil.edit')->with('warning', 'Lengkapi profil dulu sebelum melihat skor.');
        }

        $query = $atlet->sesi()->with(['sesiLatihan', 'event', 'skor'])
            ->when($request->filled('dari'), fn ($q) => $q->whereDate('tanggal_sesi', '>=', $request->dari))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal_sesi', '<=', $request->sampai))
            ->when($request->filled('sesi_latihan_id'), fn ($q) => $q->where('sesi_latihan_id', $request->sesi_latihan_id))
            ->when($request->filled('event_id'), fn ($q) => $q->where('event_id', $request->event_id))
            ->latest('tanggal_sesi')
            ->latest('id');

        $sesi = $query->paginate(10)->withQueryString();

        return view('atlet.riwayat', [
            'sesi' => $sesi,
            'sesiLatihans' => SesiLatihan::orderByDesc('tanggal')->get(['id', 'nama_sesi', 'tanggal']),
            'events' => Event::orderBy('nama_event')->get(['id', 'nama_event']),
            'filter' => $request->only(['dari', 'sampai', 'sesi_latihan_id', 'event_id']),
        ]);
    }
}
