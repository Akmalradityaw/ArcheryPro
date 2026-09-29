<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SesiLatihanRequest;
use App\Http\Requests\SetStatusSesiLatihanRequest;
use App\Models\SesiLatihan;
use App\Support\DataTable;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SesiLatihanController extends Controller
{
    public function index(): View
    {
        return view('admin.sesi-latihan.index', [
            'sesiLatihans' => DataTable::paginate(
                SesiLatihan::withCount('sesi'),
                ['nama_sesi', 'lokasi', 'fokus_latihan'],
                [
                    'nama' => 'nama_sesi',
                    'tanggal' => 'tanggal',
                    'status' => 'status',
                    'jenis' => 'jenis_latihan',
                ],
                ['tanggal', 'desc']
            ),
            'ringkasan' => [
                'total' => SesiLatihan::count(),
                'terjadwal' => SesiLatihan::where('status', 'terjadwal')->count(),
                'berlangsung' => SesiLatihan::where('status', 'berlangsung')->count(),
                'selesai' => SesiLatihan::where('status', 'selesai')->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.sesi-latihan.create');
    }

    public function store(SesiLatihanRequest $request): RedirectResponse
    {
        $data = $request->validated() + ['created_by' => auth()->id()];
        $data['is_publik'] = $request->boolean('is_publik');

        SesiLatihan::create($data);

        return redirect()->route('admin.sesi-latihan.index')->with('success', 'Jadwal latihan mingguan berhasil dibuat.');
    }

    public function edit(SesiLatihan $sesiLatihan): View
    {
        return view('admin.sesi-latihan.edit', compact('sesiLatihan'));
    }

    public function update(SesiLatihanRequest $request, SesiLatihan $sesiLatihan): RedirectResponse
    {
        $data = $request->validated();
        $data['is_publik'] = $request->boolean('is_publik');

        $sesiLatihan->update($data);

        return redirect()->route('admin.sesi-latihan.index')->with('success', 'Jadwal latihan berhasil diperbarui.');
    }

    public function destroy(SesiLatihan $sesiLatihan): RedirectResponse
    {
        $sesiLatihan->delete();

        return redirect()->route('admin.sesi-latihan.index')->with('success', 'Jadwal latihan berhasil dihapus.');
    }

    public function setStatus(SetStatusSesiLatihanRequest $request, SesiLatihan $sesiLatihan): RedirectResponse
    {
        $sesiLatihan->update($request->validated());

        return redirect()->route('admin.sesi-latihan.index')->with('success', "Status sesi latihan diubah menjadi: {$sesiLatihan->status}.");
    }

    /**
     * Fitur Duplikasi Jadwal (Copy Jadwal Pekan Lalu)
     * Menggandakan parameter sesi latihan untuk pekan berikutnya (+7 hari).
     */
    public function duplicate(SesiLatihan $sesiLatihan): RedirectResponse
    {
        $tanggalBaru = Carbon::parse($sesiLatihan->tanggal)->addWeek();

        $duplikat = SesiLatihan::create([
            'nama_sesi' => $sesiLatihan->nama_sesi . ' (Salinan)',
            'tanggal' => $tanggalBaru->toDateString(),
            'jam_mulai' => $sesiLatihan->jam_mulai,
            'jam_selesai' => $sesiLatihan->jam_selesai,
            'lokasi' => $sesiLatihan->lokasi,
            'jenis_latihan' => $sesiLatihan->jenis_latihan,
            'status' => 'terjadwal',
            'fokus_latihan' => $sesiLatihan->fokus_latihan,
            'is_publik' => $sesiLatihan->is_publik,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.sesi-latihan.edit', $duplikat)
            ->with('success', "Jadwal latihan berhasil diduplikasi untuk tanggal {$tanggalBaru->format('d M Y')}. Silakan sesuaikan nama dan detailnya.");
    }
}
