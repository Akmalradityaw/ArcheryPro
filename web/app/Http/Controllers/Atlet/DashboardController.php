<?php

namespace App\Http\Controllers\Atlet;

use App\Http\Controllers\Controller;
use App\Models\Sesi;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $atlet = auth()->user()->atlet;
        if (! $atlet) {
            return redirect()->route('atlet.profil.edit')->with('warning', 'Lengkapi profil dulu sebelum melihat skor.');
        }

        $sesiQuery = $atlet->sesi()->with(['sesiLatihan', 'event']);

        $totalLatihan = (clone $sesiQuery)->count();
        $rataRata = round((clone $sesiQuery)->avg('total_skor') ?? 0, 1);
        $skorTerbaik = (clone $sesiQuery)->max('total_skor') ?? 0;
        $skorTerbaru = (clone $sesiQuery)->latest('tanggal_sesi')->latest('id')->take(5)->get();

        // Ambil catatan pelatih terbaru yang belum dibaca
        $catatanBelumDibaca = (clone $sesiQuery)
            ->whereNotNull('catatan_pelatih')
            ->whereNull('catatan_dibaca_at')
            ->latest('tanggal_sesi')
            ->first();

        // Hitung total panah
        $totalPanah = $atlet->sesi()->withCount('skor')->get()->sum(fn ($s) => $s->skor_count * 6);

        // Sistem Badge Motivasi
        $badges = $this->hitungBadges($totalLatihan, $skorTerbaik, $totalPanah);

        return view('atlet.dashboard', [
            'atlet' => $atlet,
            'totalLatihan' => $totalLatihan,
            'rataRata' => $rataRata,
            'skorTerbaik' => $skorTerbaik,
            'totalPanah' => $totalPanah,
            'skorTerbaru' => $skorTerbaru,
            'catatanBelumDibaca' => $catatanBelumDibaca,
            'badges' => $badges,
        ]);
    }

    public function bacaCatatan(Sesi $sesi): RedirectResponse
    {
        $atlet = auth()->user()->atlet;
        if (! $atlet || $sesi->atlet_id !== $atlet->id) {
            abort(403, 'Akses ditolak.');
        }

        $sesi->tandaiCatatanDibaca();

        return back()->with('success', 'Catatan pelatih telah ditandai sudah dibaca.');
    }

    private function hitungBadges(int $totalSesi, int $maxSkor, int $totalPanah): array
    {
        $badges = [];

        // 1. Badge Kehadiran Latihan
        if ($totalSesi >= 15) {
            $badges[] = [
                'nama' => 'Dedikasi Penuh',
                'deskripsi' => 'Menyelesaikan 15+ sesi latihan rutin tercatat.',
                'kategori' => 'Kehadiran',
                'unlocked' => true,
            ];
        } elseif ($totalSesi >= 8) {
            $badges[] = [
                'nama' => 'Disiplin Juara',
                'deskripsi' => 'Menyelesaikan minimal 8 sesi latihan rutin.',
                'kategori' => 'Kehadiran',
                'unlocked' => true,
            ];
        } elseif ($totalSesi >= 4) {
            $badges[] = [
                'nama' => 'Rajin Latihan',
                'deskripsi' => 'Menyelesaikan 4 sesi latihan awal.',
                'kategori' => 'Kehadiran',
                'unlocked' => true,
            ];
        } else {
            $badges[] = [
                'nama' => 'Langkah Awal',
                'deskripsi' => "Selesaikan 4 sesi latihan untuk membuka ({$totalSesi}/4).",
                'kategori' => 'Kehadiran',
                'unlocked' => false,
            ];
        }

        // 2. Badge Rekor Skor
        if ($maxSkor >= 300) {
            $badges[] = [
                'nama' => 'Master Archer (300+)',
                'deskripsi' => 'Meraih skor 300 poin dalam satu sesi latihan.',
                'kategori' => 'Skor',
                'unlocked' => true,
            ];
        } elseif ($maxSkor >= 280) {
            $badges[] = [
                'nama' => 'Akurasi Tinggi (280+)',
                'deskripsi' => 'Meraih skor 280 poin ke atas dalam sesi latihan.',
                'kategori' => 'Skor',
                'unlocked' => true,
            ];
        } elseif ($maxSkor >= 250) {
            $badges[] = [
                'nama' => 'Tembus 250 Poin',
                'deskripsi' => 'Meraih skor 250 poin dalam sesi latihan.',
                'kategori' => 'Skor',
                'unlocked' => true,
            ];
        } else {
            $badges[] = [
                'nama' => 'Target 250 Poin',
                'deskripsi' => 'Raih total skor 250+ dalam satu sesi latihan.',
                'kategori' => 'Skor',
                'unlocked' => false,
            ];
        }

        // 3. Badge Volume Panah
        if ($totalPanah >= 300) {
            $badges[] = [
                'nama' => '300+ Anak Panah',
                'deskripsi' => 'Telah menembakkan 300+ anak panah dalam latihan.',
                'kategori' => 'Volume',
                'unlocked' => true,
            ];
        } else {
            $badges[] = [
                'nama' => 'Target 300 Panah',
                'deskripsi' => "Capai total 300 anak panah ({$totalPanah}/300).",
                'kategori' => 'Volume',
                'unlocked' => false,
            ];
        }

        return $badges;
    }
}
