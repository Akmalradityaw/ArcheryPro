<?php

namespace App\Http\Controllers\Pelatih;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSkorRequest;
use App\Models\Atlet;
use App\Models\Event;
use App\Models\SesiLatihan;
use App\Services\SkorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InputSkorController extends Controller
{
    public function __construct(
        protected SkorService $skorService
    ) {}

    /**
     * Tampilkan formulir input skor latihan untuk pelatih.
     */
    public function create(Request $request): View
    {
        $selectedSesiId = $request->query('sesi_latihan_id');

        $sesiLatihans = SesiLatihan::whereIn('status', ['berlangsung', 'terjadwal'])
            ->orderByRaw('CASE WHEN tanggal = ? THEN 0 ELSE 1 END', [date('Y-m-d')])
            ->latest('tanggal')
            ->get();

        $events = Event::where('status', 'berlangsung')
            ->latest('tanggal')
            ->get();

        $atlets = Atlet::with(['sekolah', 'kategori'])
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        return view('pelatih.skor.index', compact('sesiLatihans', 'events', 'atlets', 'selectedSesiId'));
    }

    /**
     * Simpan langsung (shortcut).
     */
    public function store(StoreSkorRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->skorService->simpanMultiAtlet($data, auth()->id());

        return $this->getRedirectResponse($data);
    }

    /**
     * Tinjau skor sebelum disimpan.
     */
    public function konfirmasi(StoreSkorRequest $request): View
    {
        $data = $request->validated();
        $atlets = Atlet::whereIn('id', $data['atlet_ids'])
            ->with(['sekolah', 'kategori'])
            ->orderBy('nama_lengkap')
            ->get()
            ->keyBy('id');

        $sesiLatihan = !empty($data['sesi_latihan_id']) ? SesiLatihan::find($data['sesi_latihan_id']) : null;
        $event = !empty($data['event_id']) ? Event::find($data['event_id']) : null;

        return view('pelatih.skor.konfirmasi', compact('data', 'atlets', 'sesiLatihan', 'event'));
    }

    /**
     * Simpan final setelah konfirmasi.
     */
    public function selesai(StoreSkorRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->skorService->simpanMultiAtlet($data, auth()->id());

        return $this->getRedirectResponse($data);
    }

    /**
     * Arahkan pelatih ke halaman sesi latihan terkait atau ke indeks sesi latihan.
     */
    protected function getRedirectResponse(array $data): RedirectResponse
    {
        if (!empty($data['sesi_latihan_id'])) {
            return redirect()
                ->route('pelatih.sesi-latihan.show', $data['sesi_latihan_id'])
                ->with('success', 'Skor latihan mingguan berhasil dicatat oleh pelatih.');
        }

        return redirect()
            ->route('pelatih.sesi-latihan.index')
            ->with('success', 'Skor latihan mingguan berhasil dicatat oleh pelatih.');
    }
}
