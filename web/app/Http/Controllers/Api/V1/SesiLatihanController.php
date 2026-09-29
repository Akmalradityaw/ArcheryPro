<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SesiLatihan;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SesiLatihanController extends Controller
{
    use ApiResponse;

    /**
     * Daftar sesi latihan mingguan dengan paginasi standar 10 data.
     */
    public function index(Request $request): JsonResponse
    {
        $today = date('Y-m-d');
        $status = $request->query('status');

        $query = SesiLatihan::withCount('sesi')
            ->orderByRaw('CASE WHEN tanggal = ? THEN 0 ELSE 1 END', [$today])
            ->latest('tanggal');

        if ($status && in_array($status, ['berlangsung', 'terjadwal', 'selesai', 'batal'])) {
            $query->where('status', $status);
        }

        // Paginasi wajib 10 data per halaman
        $sesiLatihans = $query->paginate(10)->through(function ($s) use ($today) {
            return [
                'id' => $s->id,
                'nama_sesi' => $s->nama_sesi,
                'tanggal' => $s->tanggal ? $s->tanggal->format('Y-m-d') : null,
                'tanggal_formatted' => $s->tanggal ? $s->tanggal->translatedFormat('d F Y') : '-',
                'jam_mulai' => $s->jam_mulai ? substr($s->jam_mulai, 0, 5) : null,
                'jam_selesai' => $s->jam_selesai ? substr($s->jam_selesai, 0, 5) : null,
                'lokasi' => $s->lokasi,
                'jenis_latihan' => $s->jenis_latihan,
                'fokus_latihan' => $s->fokus_latihan,
                'status' => $s->status,
                'is_today' => $s->tanggal && $s->tanggal->format('Y-m-d') === $today,
                'total_peserta' => $s->sesi_count,
            ];
        });

        return $this->ok($sesiLatihans, 'Daftar jadwal sesi latihan.');
    }

    /**
     * Detail satu sesi latihan beserta daftar atlet, skor, dan peringkat.
     */
    public function show(int $id): JsonResponse
    {
        $sesiLatihan = SesiLatihan::with([
            'sesi' => function ($q) {
                $q->with(['atlet.sekolah', 'atlet.kategori', 'skor' => fn($sq) => $sq->orderBy('end_ke')])
                  ->orderByDesc('total_skor');
            },
        ])->find($id);

        if (!$sesiLatihan) {
            return $this->fail('Sesi latihan tidak ditemukan.', 404);
        }

        $sesiList = $sesiLatihan->sesi;
        $totalPeserta = $sesiList->count();
        $skorTertinggi = $sesiList->max('total_skor') ?? 0;
        $rataSkor = $totalPeserta > 0 ? round($sesiList->avg('total_skor'), 1) : 0;

        $peringkat = 1;
        $hasilAtlet = $sesiList->map(function ($sesi) use (&$peringkat) {
            $atlet = $sesi->atlet;
            return [
                'peringkat' => $peringkat++,
                'sesi_id' => $sesi->id,
                'atlet_id' => $atlet?->id,
                'nama_lengkap' => $atlet?->nama_lengkap ?? 'Atlet #' . $sesi->atlet_id,
                'sekolah' => $atlet?->sekolah?->nama_sekolah,
                'kategori' => $atlet?->kategori?->nama_kategori,
                'total_skor' => $sesi->total_skor,
                'total_panah' => $sesi->skor->count() * 6,
                'total_end' => $sesi->skor->count(),
                'jarak_meter' => $sesi->jarak_meter,
                'catatan_pelatih' => $sesi->catatan_pelatih,
                'catatan_dibaca' => !is_null($sesi->catatan_dibaca_at),
                'ends' => $sesi->skor->map(function ($sk) {
                    return [
                        'end_ke' => $sk->end_ke,
                        'skor' => [$sk->skor1, $sk->skor2, $sk->skor3, $sk->skor4, $sk->skor5, $sk->skor6],
                        'total_end' => $sk->total_end,
                    ];
                }),
            ];
        });

        return $this->ok([
            'id' => $sesiLatihan->id,
            'nama_sesi' => $sesiLatihan->nama_sesi,
            'tanggal' => $sesiLatihan->tanggal ? $sesiLatihan->tanggal->format('Y-m-d') : null,
            'tanggal_formatted' => $sesiLatihan->tanggal ? $sesiLatihan->tanggal->translatedFormat('d F Y') : '-',
            'jam_mulai' => $sesiLatihan->jam_mulai ? substr($sesiLatihan->jam_mulai, 0, 5) : null,
            'jam_selesai' => $sesiLatihan->jam_selesai ? substr($sesiLatihan->jam_selesai, 0, 5) : null,
            'lokasi' => $sesiLatihan->lokasi,
            'jenis_latihan' => $sesiLatihan->jenis_latihan,
            'fokus_latihan' => $sesiLatihan->fokus_latihan,
            'status' => $sesiLatihan->status,
            'statistik' => [
                'total_peserta' => $totalPeserta,
                'skor_tertinggi' => $skorTertinggi,
                'rata_rata_skor' => $rataSkor,
            ],
            'peserta' => $hasilAtlet,
        ], 'Detail sesi latihan.');
    }
}
