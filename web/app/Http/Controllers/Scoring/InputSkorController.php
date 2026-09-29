<?php

namespace App\Http\Controllers\Scoring;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSkorRequest;
use App\Models\Atlet;
use App\Models\Event;
use App\Models\SesiLatihan;
use App\Services\SkorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InputSkorController extends Controller
{
    public function __construct(
        protected SkorService $skorService
    ) {}

    public function create(): View
    {
        return view('scoring.input.index', [
            'sesiLatihans' => SesiLatihan::whereIn('status', ['berlangsung', 'terjadwal'])
                ->orderByRaw('CASE WHEN tanggal = ? THEN 0 ELSE 1 END', [date('Y-m-d')])
                ->latest('tanggal')
                ->get(),
            'events' => Event::where('status', 'berlangsung')->latest('tanggal')->get(),
            'atlets' => Atlet::with(['sekolah', 'kategori'])->where('status', 'aktif')->orderBy('nama_lengkap')->get(),
        ]);
    }

    // Jalur simpan langsung (tanpa review). Form utama memakai konfirmasi() + selesai().
    public function store(StoreSkorRequest $request): RedirectResponse
    {
        $this->skorService->simpanMultiAtlet($request->validated(), auth()->id());

        return redirect('/scoring/riwayat')->with('success', 'Skor latihan berhasil disimpan.');
    }

    public function konfirmasi(StoreSkorRequest $request): View
    {
        $data = $request->validated();
        $atlets = Atlet::whereIn('id', $data['atlet_ids'])->with(['sekolah', 'kategori'])->orderBy('nama_lengkap')->get()->keyBy('id');
        $sesiLatihan = !empty($data['sesi_latihan_id']) ? SesiLatihan::find($data['sesi_latihan_id']) : null;
        $event = !empty($data['event_id']) ? Event::find($data['event_id']) : null;

        return view('scoring.input.konfirmasi', compact('data', 'atlets', 'sesiLatihan', 'event'));
    }

    public function selesai(StoreSkorRequest $request): RedirectResponse
    {
        $this->skorService->simpanMultiAtlet($request->validated(), auth()->id());

        return redirect('/scoring/riwayat')->with('success', 'Skor latihan berhasil disimpan.');
    }
}

