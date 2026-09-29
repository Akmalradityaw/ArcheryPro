<?php

namespace App\Http\Controllers\Pelatih;

use App\Http\Controllers\Controller;
use App\Models\Atlet;
use App\Models\Sesi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalisisController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('q') ?? $request->query('cari');

        $query = Atlet::with(['sekolah', 'kategori'])
            ->withCount('sesi')
            ->orderBy('nama_lengkap');

        if ($search) {
            $query->where('nama_lengkap', 'like', "%{$search}%");
        }

        $atlets = $query->paginate(10)->withQueryString();

        return view('pelatih.analisis.index', compact('atlets', 'search'));
    }

    public function show(Atlet $atlet): View
    {
        $sesi = $atlet->sesi()
            ->with(['sesiLatihan', 'event', 'skor'])
            ->orderBy('tanggal_sesi')
            ->orderBy('id')
            ->get();

        // 1. Hitung metrik per sesi (Standar Deviasi End & Volume Panah)
        $sesiList = $sesi->map(function ($s) {
            $ends = $s->skor->pluck('total_end')->filter(fn ($v) => !is_null($v))->values();
            $n = $ends->count();

            $sd = null;
            if ($n >= 2) {
                $mean = $ends->avg();
                $variance = $ends->map(fn ($x) => pow($x - $mean, 2))->sum() / ($n - 1);
                $sd = round(sqrt($variance), 2);
            }

            $stabilitasLabel = 'Belum Cukup Data';
            $stabilitasClass = 'badge-ghost';
            if ($sd !== null) {
                if ($sd <= 2.2) {
                    $stabilitasLabel = 'Sangat Stabil';
                    $stabilitasClass = 'badge-success text-white';
                } elseif ($sd <= 4.0) {
                    $stabilitasLabel = 'Konsisten';
                    $stabilitasClass = 'badge-info text-white';
                } else {
                    $stabilitasLabel = 'Perlu Perbaikan';
                    $stabilitasClass = 'badge-warning';
                }
            }

            // Total panah pada sesi ini (tiap end = 6 panah atau hitung input riil)
            $totalPanah = $s->skor->sum(function ($skor) {
                $panah = 0;
                for ($i = 1; $i <= 6; $i++) {
                    if (!is_null($skor->{"skor{$i}"})) {
                        $panah++;
                    }
                }
                return $panah > 0 ? $panah : 6;
            });

            return [
                'sesi_id' => $s->id,
                'nama' => $s->sesiLatihan->nama_sesi ?? $s->event->nama_event ?? 'Latihan Rutin',
                'tanggal' => $s->tanggal_sesi,
                'jarak' => $s->jarak_meter ?? 18,
                'total_skor' => $s->total_skor,
                'jumlah_end' => $n,
                'rata_end' => $n > 0 ? round($ends->avg(), 1) : 0,
                'standar_deviasi' => $sd,
                'stabilitas_label' => $stabilitasLabel,
                'stabilitas_class' => $stabilitasClass,
                'total_panah' => $totalPanah,
                'catatan_pelatih' => $s->catatan_pelatih,
                'catatan_dibaca_at' => $s->catatan_dibaca_at,
                'is_dibaca' => $s->isCatatanDibaca(),
            ];
        });

        // 2. Hitung 4-Period Moving Average (Tren 4 Sesi/Minggu Terakhir)
        $scores = $sesi->pluck('total_skor')->values();
        $movingAverages = [];
        for ($i = 0; $i < $scores->count(); $i++) {
            $windowStart = max(0, $i - 3);
            $window = $scores->slice($windowStart, $i - $windowStart + 1);
            $movingAverages[] = round($window->avg(), 1);
        }

        // 3. Hitung Keseluruhan Standar Deviasi Atlet
        $allEnds = $sesi->flatMap(fn ($s) => $s->skor->pluck('total_end'))->filter(fn ($v) => !is_null($v))->values();
        $overallSd = null;
        if ($allEnds->count() >= 2) {
            $meanAll = $allEnds->avg();
            $varianceAll = $allEnds->map(fn ($x) => pow($x - $meanAll, 2))->sum() / ($allEnds->count() - 1);
            $overallSd = round(sqrt($varianceAll), 2);
        }

        // 4. Hitung Akumulasi Panah Bulanan & Total
        $now = now();
        $panahBulanIni = $sesiList->filter(function ($item) use ($now) {
            return $item['tanggal'] && $item['tanggal']->isSameMonth($now);
        })->sum('total_panah');

        $panahTotal = $sesiList->sum('total_panah');

        // Ringkasan performa
        $ringkasan = [
            'total_sesi' => $sesi->count(),
            'rata_skor' => $sesi->count() > 0 ? round($sesi->avg('total_skor'), 1) : 0,
            'skor_terbaik' => $sesi->max('total_skor') ?? 0,
            'stabilitas_umum' => $overallSd,
            'panah_bulan_ini' => $panahBulanIni,
            'panah_total' => $panahTotal,
        ];

        return view('pelatih.analisis.detail', [
            'atlet' => $atlet,
            'sesiList' => $sesiList->reverse()->values(), // terbaru di atas untuk tabel
            'labels' => $sesi->map(fn ($s) => $s->tanggal_sesi ? $s->tanggal_sesi->format('d/m') : '-')->values(),
            'dataSkor' => $scores,
            'dataMovingAvg' => $movingAverages,
            'ringkasan' => $ringkasan,
        ]);
    }

    public function updateCatatan(Request $request, Sesi $sesi): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_pelatih' => 'nullable|string|max:1000',
        ]);

        $sesi->update([
            'catatan_pelatih' => $validated['catatan_pelatih'],
            'catatan_dibaca_at' => null, // reset agar atlet melihat indikasi ada catatan baru
        ]);

        return back()->with('success', 'Catatan evaluasi pelatih berhasil disimpan.');
    }
}
