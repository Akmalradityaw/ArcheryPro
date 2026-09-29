<?php

namespace App\Http\Controllers\Pelatih;

use App\Http\Controllers\Controller;
use App\Models\SesiLatihan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SesiLatihanController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('cari');

        $query = SesiLatihan::withCount('sesi')
            ->latest('tanggal')
            ->latest('jam_mulai');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_sesi', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        $sesiLatihans = $query->paginate(10)->withQueryString();

        return view('pelatih.sesi-latihan.index', compact('sesiLatihans', 'status', 'search'));
    }

    public function show(SesiLatihan $sesiLatihan): View
    {
        $sesiLatihan->load([
            'sesi' => fn ($q) => $q->with(['atlet.sekolah', 'atlet.kategori', 'skor'])
                ->orderByDesc('total_skor'),
        ]);

        $peserta = $sesiLatihan->sesi->map(function ($s) {
            $ends = $s->skor->pluck('total_end')->filter(fn ($v) => !is_null($v))->values();
            $n = $ends->count();

            $sd = null;
            if ($n >= 2) {
                $mean = $ends->avg();
                $variance = $ends->map(fn ($x) => pow($x - $mean, 2))->sum() / ($n - 1);
                $sd = round(sqrt($variance), 2);
            }

            return [
                'sesi_id' => $s->id,
                'atlet' => $s->atlet,
                'total_skor' => $s->total_skor,
                'jarak_meter' => $s->jarak_meter ?? 18,
                'jumlah_end' => $n,
                'rata_end' => $n > 0 ? round($ends->avg(), 1) : 0,
                'standar_deviasi' => $sd,
                'catatan_pelatih' => $s->catatan_pelatih,
                'catatan_dibaca_at' => $s->catatan_dibaca_at,
                'is_dibaca' => $s->isCatatanDibaca(),
            ];
        });

        $totalPeserta = $peserta->count();
        $skorTertinggi = $peserta->max('total_skor') ?? 0;
        $rataSkor = $totalPeserta > 0 ? round($peserta->avg('total_skor'), 1) : 0;

        return view('pelatih.sesi-latihan.show', [
            'sesiLatihan' => $sesiLatihan,
            'peserta' => $peserta,
            'totalPeserta' => $totalPeserta,
            'skorTertinggi' => $skorTertinggi,
            'rataSkor' => $rataSkor,
        ]);
    }
}
