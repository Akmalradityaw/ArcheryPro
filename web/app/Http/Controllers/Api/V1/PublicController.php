<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PengaturanSistem;
use App\Models\Sesi;
use App\Models\SesiLatihan;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    use ApiResponse;

    /**
     * Papan skor publik / leaderboard latihan mingguan (menghormati izin privasi atlet).
     */
    public function leaderboard(Request $request): JsonResponse
    {
        $settingLeaderboard = PengaturanSistem::getValue('leaderboard_publik', '1');
        if ($settingLeaderboard === '0' || $settingLeaderboard === 'false') {
            return $this->fail('Papan skor publik saat ini dinonaktifkan oleh administrator.', 403);
        }

        $query = Sesi::with(['atlet.sekolah', 'atlet.kategori', 'sesiLatihan'])
            ->whereHas('atlet', function ($q) {
                $q->where('izinkan_tampil_publik', true);
            })
            ->orderByDesc('total_skor')
            ->latest('tanggal_sesi');

        // Paginasi wajib 10 data per halaman
        $leaderboard = $query->paginate(10)->through(function ($sesi, $index) use ($query) {
            $atlet = $sesi->atlet;
            return [
                'sesi_id' => $sesi->id,
                'atlet_id' => $atlet->id,
                'nama_lengkap' => $atlet->nama_lengkap,
                'sekolah' => $atlet->sekolah?->nama_sekolah ?? '-',
                'kategori' => $atlet->kategori?->nama_kategori ?? '-',
                'sesi_latihan' => $sesi->sesiLatihan?->nama_sesi ?? 'Latihan Rutin',
                'tanggal' => $sesi->tanggal_sesi ? $sesi->tanggal_sesi->format('Y-m-d') : null,
                'jarak_meter' => $sesi->jarak_meter,
                'total_skor' => $sesi->total_skor,
            ];
        });

        return $this->ok($leaderboard, 'Papan skor publik latihan mingguan.');
    }

    /**
     * Jadwal latihan mingguan terbuka untuk publik.
     */
    public function sesiPublik(Request $request): JsonResponse
    {
        $sesiPublik = SesiLatihan::where('is_publik', true)
            ->whereIn('status', ['berlangsung', 'terjadwal'])
            ->latest('tanggal')
            ->paginate(10)
            ->through(function ($s) {
                return [
                    'id' => $s->id,
                    'nama_sesi' => $s->nama_sesi,
                    'tanggal' => $s->tanggal ? $s->tanggal->format('Y-m-d') : null,
                    'jam_mulai' => $s->jam_mulai ? substr($s->jam_mulai, 0, 5) : null,
                    'lokasi' => $s->lokasi,
                    'jenis_latihan' => $s->jenis_latihan,
                    'fokus_latihan' => $s->fokus_latihan,
                    'status' => $s->status,
                ];
            });

        return $this->ok($sesiPublik, 'Daftar sesi latihan publik.');
    }
}
