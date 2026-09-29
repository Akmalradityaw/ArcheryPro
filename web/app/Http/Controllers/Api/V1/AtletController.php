<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Atlet;
use App\Models\Sesi;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AtletController extends Controller
{
    use ApiResponse;

    /**
     * Data performa atlet: ringkasan statistik, moving average, stabilitas, dan badges motivasi.
     */
    public function performa(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        // Jika user adalah atlet, hanya izinkan melihat data miliknya sendiri
        if ($user->role === 'atlet' && $user->atlet?->id !== $id) {
            return $this->fail('Akses ditolak. Anda hanya dapat melihat data performa Anda sendiri.', 403);
        }

        $atlet = Atlet::with(['sekolah', 'kategori'])->find($id);
        if (!$atlet) {
            return $this->fail('Data atlet tidak ditemukan.', 404);
        }

        $sesiList = $atlet->sesi()
            ->with(['skor' => fn($q) => $q->orderBy('end_ke'), 'sesiLatihan'])
            ->latest('tanggal_sesi')
            ->latest('id')
            ->get();

        $totalSesi = $sesiList->count();
        $totalSkorSemua = $sesiList->sum('total_skor');
        $rataRata = $totalSesi > 0 ? round($totalSkorSemua / $totalSesi, 1) : 0;
        $skorTertinggi = $sesiList->max('total_skor') ?? 0;
        $totalPanah = $sesiList->sum(fn($s) => $s->skor->count() * 6);

        // Catatan pelatih belum dibaca
        $catatanBelumDibaca = $sesiList->first(function ($s) {
            return !empty($s->catatan_pelatih) && is_null($s->catatan_dibaca_at);
        });

        // 1. Moving Average (Rata-rata 4 sesi bergerak)
        $movingAverage = [];
        $window = 4;
        $reversed = $sesiList->reverse()->values();
        for ($i = 0; $i < $reversed->count(); $i++) {
            $slice = $reversed->slice(max(0, $i - $window + 1), min($i + 1, $window));
            $movingAverage[] = [
                'sesi_nama' => $reversed[$i]->sesiLatihan?->nama_sesi ?? 'Sesi ' . ($i + 1),
                'tanggal' => $reversed[$i]->tanggal_sesi ? $reversed[$i]->tanggal_sesi->format('Y-m-d') : null,
                'skor_aktual' => $reversed[$i]->total_skor,
                'moving_average' => round($slice->avg('total_skor'), 1),
            ];
        }

        // 2. Stabilitas (Standar Deviasi per End) pada sesi terakhir
        $stabilitas = null;
        $sesiTerakhir = $sesiList->first();
        if ($sesiTerakhir && $sesiTerakhir->skor->count() > 1) {
            $endTotals = $sesiTerakhir->skor->pluck('total_end')->toArray();
            $n = count($endTotals);
            $mean = array_sum($endTotals) / $n;
            $variance = array_sum(array_map(fn($x) => pow($x - $mean, 2), $endTotals)) / ($n - 1);
            $sd = round(sqrt($variance), 2);

            $label = $sd <= 2.2 ? 'Sangat Stabil' : ($sd <= 4.0 ? 'Konsisten' : 'Perlu Perbaikan');
            $stabilitas = [
                'standar_deviasi' => $sd,
                'kategori_stabilitas' => $label,
                'rata_rata_per_end' => round($mean, 1),
                'jumlah_end' => $n,
            ];
        }

        // 3. Badges Motivasi
        $badges = $this->hitungBadges($totalSesi, $skorTertinggi, $totalPanah);

        return $this->ok([
            'atlet' => [
                'id' => $atlet->id,
                'nia' => $atlet->nia,
                'nama_lengkap' => $atlet->nama_lengkap,
                'sekolah' => $atlet->sekolah?->nama_sekolah ?? '-',
                'kategori' => $atlet->kategori?->nama_kategori ?? '-',
                'nomor_target' => $atlet->nomor_target,
                'izinkan_tampil_publik' => (bool) $atlet->izinkan_tampil_publik,
            ],
            'statistik' => [
                'total_sesi' => $totalSesi,
                'rata_rata_skor' => $rataRata,
                'skor_tertinggi' => $skorTertinggi,
                'total_panah' => $totalPanah,
            ],
            'catatan_pelatih_baru' => $catatanBelumDibaca ? [
                'sesi_id' => $catatanBelumDibaca->id,
                'sesi_nama' => $catatanBelumDibaca->sesiLatihan?->nama_sesi ?? 'Latihan Rutin',
                'tanggal' => $catatanBelumDibaca->tanggal_sesi ? $catatanBelumDibaca->tanggal_sesi->format('Y-m-d') : null,
                'catatan' => $catatanBelumDibaca->catatan_pelatih,
            ] : null,
            'stabilitas' => $stabilitas,
            'moving_average' => $movingAverage,
            'badges' => $badges,
        ], 'Data performa atlet.');
    }

    /**
     * Riwayat sesi latihan atlet dengan paginasi wajib 10 data per halaman.
     */
    public function riwayat(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        if ($user->role === 'atlet' && $user->atlet?->id !== $id) {
            return $this->fail('Akses ditolak.', 403);
        }

        $atlet = Atlet::find($id);
        if (!$atlet) {
            return $this->fail('Data atlet tidak ditemukan.', 404);
        }

        // Paginasi wajib 10 data per halaman
        $riwayat = $atlet->sesi()
            ->with(['sesiLatihan', 'skor' => fn($q) => $q->orderBy('end_ke')])
            ->latest('tanggal_sesi')
            ->latest('id')
            ->paginate(10)
            ->through(function ($s) {
                return [
                    'id' => $s->id,
                    'sesi_latihan_id' => $s->sesi_latihan_id,
                    'nama_sesi' => $s->sesiLatihan?->nama_sesi ?? 'Latihan Rutin',
                    'tanggal' => $s->tanggal_sesi ? $s->tanggal_sesi->format('Y-m-d') : null,
                    'tanggal_formatted' => $s->tanggal_sesi ? $s->tanggal_sesi->translatedFormat('d F Y') : '-',
                    'jarak_meter' => $s->jarak_meter,
                    'total_skor' => $s->total_skor,
                    'total_end' => $s->skor->count(),
                    'catatan_pelatih' => $s->catatan_pelatih,
                    'catatan_dibaca' => !is_null($s->catatan_dibaca_at),
                    'catatan_dibaca_at' => $s->catatan_dibaca_at ? $s->catatan_dibaca_at->format('Y-m-d H:i') : null,
                    'ends' => $s->skor->map(function ($sk) {
                        return [
                            'end_ke' => $sk->end_ke,
                            'skor' => [$sk->skor1, $sk->skor2, $sk->skor3, $sk->skor4, $sk->skor5, $sk->skor6],
                            'total_end' => $sk->total_end,
                        ];
                    }),
                ];
            });

        return $this->ok($riwayat, 'Riwayat latihan atlet.');
    }

    /**
     * Tandai catatan pelatih pada sesi sudah dibaca oleh atlet.
     */
    public function bacaCatatan(Request $request, int $sesiId): JsonResponse
    {
        $sesi = Sesi::find($sesiId);
        if (!$sesi) {
            return $this->fail('Sesi latihan tidak ditemukan.', 404);
        }

        $user = $request->user();
        if ($user->role === 'atlet' && $user->atlet?->id !== $sesi->atlet_id) {
            return $this->fail('Akses ditolak.', 403);
        }

        $sesi->tandaiCatatanDibaca();

        return $this->ok([
            'sesi_id' => $sesi->id,
            'catatan_dibaca_at' => $sesi->catatan_dibaca_at->format('Y-m-d H:i:s'),
        ], 'Catatan pelatih telah ditandai sudah dibaca.');
    }

    /**
     * Berikan atau perbarui catatan evaluasi pelatih pada sesi atlet.
     */
    public function updateCatatanPelatih(Request $request, int $sesiId): JsonResponse
    {
        $user = $request->user();
        if (!in_array($user->role, ['pelatih', 'admin'])) {
            return $this->fail('Hanya pelatih atau admin yang dapat memberikan catatan evaluasi.', 403);
        }

        $request->validate([
            'catatan_pelatih' => 'required|string|max:1000',
        ]);

        $sesi = Sesi::find($sesiId);
        if (!$sesi) {
            return $this->fail('Sesi latihan tidak ditemukan.', 404);
        }

        $sesi->update([
            'catatan_pelatih' => $request->catatan_pelatih,
            'catatan_dibaca_at' => null, // Reset status baca agar atlet mendapatkan notifikasi
        ]);

        return $this->ok([
            'sesi_id' => $sesi->id,
            'catatan_pelatih' => $sesi->catatan_pelatih,
        ], 'Catatan evaluasi pelatih berhasil diperbarui.');
    }

    /**
     * Atur opsi privasi atlet (izin tampil di papan publik).
     */
    public function updatePrivasi(Request $request): JsonResponse
    {
        $user = $request->user();
        $atlet = $user->atlet;

        if (!$atlet) {
            return $this->fail('Profil atlet tidak ditemukan untuk akun ini.', 404);
        }

        $request->validate([
            'izinkan_tampil_publik' => 'required|boolean',
        ]);

        $atlet->update([
            'izinkan_tampil_publik' => $request->boolean('izinkan_tampil_publik'),
        ]);

        return $this->ok([
            'atlet_id' => $atlet->id,
            'izinkan_tampil_publik' => (bool) $atlet->izinkan_tampil_publik,
        ], 'Preferensi privasi atlet berhasil diperbarui.');
    }

    /**
     * Hitung status badge motivasi atlet.
     */
    private function hitungBadges(int $totalSesi, int $maxSkor, int $totalPanah): array
    {
        $badges = [];

        // 1. Kehadiran
        if ($totalSesi >= 15) {
            $badges[] = ['nama' => 'Dedikasi Penuh', 'deskripsi' => 'Menyelesaikan 15+ sesi latihan tercatat.', 'kategori' => 'Kehadiran', 'unlocked' => true, 'icon' => 'award'];
        } elseif ($totalSesi >= 8) {
            $badges[] = ['nama' => 'Disiplin Juara', 'deskripsi' => 'Menyelesaikan minimal 8 sesi latihan rutin.', 'kategori' => 'Kehadiran', 'unlocked' => true, 'icon' => 'calendar'];
        } elseif ($totalSesi >= 4) {
            $badges[] = ['nama' => 'Rajin Latihan', 'deskripsi' => 'Menyelesaikan 4 sesi latihan awal.', 'kategori' => 'Kehadiran', 'unlocked' => true, 'icon' => 'check-circle'];
        } else {
            $badges[] = ['nama' => 'Langkah Awal', 'deskripsi' => "Selesaikan 4 sesi latihan untuk membuka ({$totalSesi}/4).", 'kategori' => 'Kehadiran', 'unlocked' => false, 'icon' => 'clock'];
        }

        // 2. Akurasi Skor
        if ($maxSkor >= 300) {
            $badges[] = ['nama' => 'Master Archer', 'deskripsi' => 'Mencapai skor luar biasa 300+ dalam satu sesi latihan.', 'kategori' => 'Akurasi', 'unlocked' => true, 'icon' => 'star'];
        } elseif ($maxSkor >= 280) {
            $badges[] = ['nama' => 'Akurasi Tinggi', 'deskripsi' => 'Mencapai skor 280+ dalam satu sesi latihan.', 'kategori' => 'Akurasi', 'unlocked' => true, 'icon' => 'target'];
        } elseif ($maxSkor >= 250) {
            $badges[] = ['nama' => 'Tembus 250 Poin', 'deskripsi' => 'Berhasil melampaui skor 250 dalam satu sesi.', 'kategori' => 'Akurasi', 'unlocked' => true, 'icon' => 'trophy'];
        } else {
            $badges[] = ['nama' => 'Target 250 Poin', 'deskripsi' => "Capai skor 250 dalam satu sesi latihan (Tertinggi: {$maxSkor}).", 'kategori' => 'Akurasi', 'unlocked' => false, 'icon' => 'target'];
        }

        // 3. Volume Panah
        if ($totalPanah >= 300) {
            $badges[] = ['nama' => '300+ Anak Panah', 'deskripsi' => "Akumulasi latihan tembakan telah melampaui 300 anak panah ({$totalPanah} panah).", 'kategori' => 'Volume', 'unlocked' => true, 'icon' => 'bolt'];
        } else {
            $badges[] = ['nama' => 'Menuju 300 Panah', 'deskripsi' => "Lepaskan 300 anak panah dalam latihan ({$totalPanah}/300).", 'kategori' => 'Volume', 'unlocked' => false, 'icon' => 'activity'];
        }

        return $badges;
    }
}
