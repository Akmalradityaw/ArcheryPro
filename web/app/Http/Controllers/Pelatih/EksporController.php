<?php

namespace App\Http\Controllers\Pelatih;

use App\Http\Controllers\Controller;
use App\Http\Requests\EksporPerformaRequest;
use App\Models\Event;
use App\Models\SesiLatihan;
use App\Services\EksporService;
use Illuminate\View\View;

class EksporController extends Controller
{
    public function index(): View
    {
        return view('pelatih.ekspor.index', [
            'sesiLatihans' => SesiLatihan::orderByDesc('tanggal')->get(['id', 'nama_sesi', 'tanggal']),
            'events' => Event::orderBy('nama_event')->get(['id', 'nama_event']),
        ]);
    }

    public function performa(EksporPerformaRequest $request, EksporService $ekspor)
    {
        $data = $request->validated();
        $sesiLatihanId = $data['sesi_latihan_id'] ?? null;
        $eventId = $data['event_id'] ?? null;

        return $data['format'] === 'excel'
            ? $ekspor->laporanExcel($sesiLatihanId, $eventId)
            : $ekspor->laporanPdf($sesiLatihanId, $eventId);
    }
}
