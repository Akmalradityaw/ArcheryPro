<?php

namespace Database\Seeders;

use App\Models\Atlet;
use App\Models\Event;
use App\Models\LogAktivitas;
use App\Models\LogSkor;
use App\Models\Sekolah;
use App\Models\Sesi;
use App\Models\User;
use Illuminate\Database\Seeder;

// ponytail: data demo realistis SEKALIGUS dokumentasi hidup (1 file, deterministik via mt_srand).
// SENGAJA tak didaftarkan di DatabaseSeeder — prod tetap bersih; jalan manual:
// php artisan db:seed --class=DemoDataSeeder. Guard di bawah membuatnya idempotent.
//
// Cakupan wilayah: Banyuwangi, Jawa Timur — Kec. Rogojampi, Genteng, Banyuwangi Kota.
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('username', 'pelatih1')->exists()) {
            $this->command->info('Data demo sudah ada — lewati.');

            return;
        }

        mt_srand(42);

        // ── Staff ────────────────────────────────────────────────
        $pelatih = $this->pengguna('pelatih1', 'pelatih', 'Bambang Sutrisno');
        $this->pengguna('pelatih2', 'pelatih', 'Sri Wahyuni');
        $skor1 = $this->pengguna('scoring1', 'scoring', 'Andi Kurnia');
        $this->pengguna('scoring2', 'scoring', 'Rudi Hartono');

        // ── Sekolah — Banyuwangi (Rogojampi, Genteng, Banyuwangi Kota) ──
        // Format: [nama_sekolah, kecamatan, alamat]
        $sekolahIds = collect([
            // Kecamatan Rogojampi
            ['SMAN 1 Rogojampi', 'Rogojampi', 'Jl. Raya Rogojampi No. 1'],
            ['SMKN 1 Rogojampi', 'Rogojampi', 'Jl. Diponegoro No. 12'],
            ['SMPN 1 Rogojampi', 'Rogojampi', 'Jl. Manggar No. 8'],
            // Kecamatan Genteng
            ['SMAN 1 Genteng', 'Genteng', 'Jl. Raya Genteng No. 45'],
            ['SMKN 1 Genteng', 'Genteng', 'Jl. Kalibaru No. 3'],
            ['SMPN 2 Genteng', 'Genteng', 'Jl. Gajah Mada No. 17'],
            // Kecamatan Banyuwangi Kota
            ['SMAN 1 Banyuwangi', 'Banyuwangi', 'Jl. Wijaya Kusuma No. 10'],
            ['SMKN 1 Banyuwangi', 'Banyuwangi', 'Jl. Brigjen Katamso No. 22'],
            ['SMPN 1 Banyuwangi', 'Banyuwangi', 'Jl. A. Yani No. 5'],
        ])->map(fn($s) => Sekolah::create([
            'nama_sekolah' => $s[0],
            'kota' => 'Banyuwangi',
            'alamat' => $s[2] . ', ' . $s[1] . ', Banyuwangi, Jawa Timur',
        ])->id)->all();

        // ── Atlet — nama khas Banyuwangi/Jawa Timur, skill dasar bervariasi ──
        // [nama, jk, skill dasar (rata-rata per panah), sekolah_index]
        $daftar = [
            // Rogojampi (idx 0–2)
            ['Rizky Pratama',    'L', 8, 0],
            ['Dewi Anggraini',   'P', 8, 1],
            ['Budi Santoso',     'L', 7, 2],
            ['Siti Rahma',       'P', 7, 0],
            // Genteng (idx 3–5)
            ['Agus Wijaya',      'L', 7, 3],
            ['Maya Putri',       'P', 6, 4],
            ['Dimas Prasetyo',   'L', 6, 5],
            ['Rina Marlina',     'P', 6, 3],
            // Banyuwangi Kota (idx 6–8)
            ['Fajar Nugroho',    'L', 5, 6],
            ['Intan Permata',    'P', 6, 7],
            ['Hendra Gunawan',   'L', 5, 8],
            ['Lestari Wulandari', 'P', 7, 6],
        ];

        // Tempat lahir representatif wilayah
        $tempatLahir = ['Banyuwangi', 'Rogojampi', 'Genteng', 'Kalibaru', 'Srono'];

        $atlets = collect($daftar)->map(function ($a, $i) use ($sekolahIds, $tempatLahir) {
            $slug = strtolower(explode(' ', $a[0])[0]) . ($i + 1);
            $u = $this->pengguna($slug, 'atlet', $a[0]);

            // ponytail: skill hanya atribut memori (bukan kolom) untuk variasi skor realistis
            $row = $u->atlet()->create([
                'nia' => sprintf('NIA-2026-%03d', $i + 1),
                'nama_lengkap' => $a[0],
                'tempat_lahir' => $tempatLahir[$i % count($tempatLahir)],
                'tanggal_lahir' => sprintf('%d-%02d-%02d', 2008 + ($i % 3), 1 + ($i % 12), 5 + ($i % 20)),
                'jenis_kelamin' => $a[1],
                'sekolah_id' => $sekolahIds[$a[3]],
                'kelas' => ['X', 'XI', 'XII'][$i % 3],
                'no_telepon' => '0812' . mt_rand(10000000, 99999999),
                'kategori_id' => 1 + ($i % 4),
                'status' => 'aktif',
            ]);
            $row->skill = $a[2];

            return $row;
        });

        // ── Event — nama khas Banyuwangi ──────────────────────────
        $events = [
            Event::create([
                'nama_event' => 'Kejuaraan Panahan Antar Sekolah Banyuwangi 2026',
                'tanggal' => '2026-08-10',
                'lokasi' => 'Lapangan GOR Tawang Alun, Banyuwangi',
                'status' => 'selesai',
                'is_publik' => true,
                'created_by' => $pelatih->id,
            ]),
            Event::create([
                'nama_event' => 'Latihan Bersama Wilayah Rogojampi–Genteng',
                'tanggal' => '2026-08-24',
                'lokasi' => 'Lapangan Kecamatan Rogojampi',
                'status' => 'selesai',
                'is_publik' => true,
                'created_by' => $pelatih->id,
            ]),
            Event::create([
                'nama_event' => 'Seleksi Tim Inti Porprov Jawa Timur',
                'tanggal' => '2026-09-15',
                'lokasi' => 'Stadion Blambangan, Banyuwangi',
                'status' => 'berlangsung',
                'is_publik' => true,
                'created_by' => $pelatih->id,
            ]),
            Event::create([
                'nama_event' => 'Persiapan Kejurda Panahan Jatim',
                'tanggal' => '2026-10-05',
                'lokasi' => 'Lapangan Kecamatan Genteng',
                'status' => 'draft',
                'is_publik' => false,
                'created_by' => $pelatih->id,
            ]),
        ];

        // event 0: semua ikut; event 1: 8 pertama; event 2: semua ikut
        $peserta = [range(0, 11), range(0, 7), range(0, 11)];
        foreach ([0, 1, 2] as $e) {
            foreach ($peserta[$e] as $i) {
                $this->sesi($events[$e], $atlets[$i], $skor1->id, 4);
            }
        }

        // 3 revisi skor → log_skor terisi (contoh jejak audit)
        foreach (Sesi::inRandomOrder()->take(3)->get() as $sesi) {
            $skor = $sesi->skor()->first();
            $lama = $skor->only(['skor1', 'skor2', 'skor3', 'skor4', 'skor5', 'skor6', 'total_end']);
            $skor->update(['skor6' => min(10, $skor->skor6 + 1), 'total_end' => $skor->total_end + 1]);
            $sesi->update(['total_skor' => $sesi->skor()->sum('total_end')]);
            LogSkor::create([
                'skor_id' => $skor->id,
                'user_id' => $skor1->id,
                'perubahan' => "Koreksi end {$skor->end_ke} ({$sesi->atlet->nama_lengkap})",
                'data_lama' => $lama,
                'data_baru' => $skor->only(['skor1', 'skor2', 'skor3', 'skor4', 'skor5', 'skor6', 'total_end']),
            ]);
        }

        // ── Log aktivitas — konteks Banyuwangi ────────────────────
        $aksi = [
            'membuat event Seleksi Tim Inti Porprov Jawa Timur',
            'menginput 32 sesi latihan wilayah Rogojampi',
            'mengekspor laporan performa atlet Genteng',
            'memperbarui data atlet Banyuwangi Kota',
            'mempublikasikan hasil Kejuaraan Antar Sekolah Banyuwangi',
        ];
        foreach ($aksi as $i => $a) {
            LogAktivitas::create([
                'user_id' => [$pelatih->id, $skor1->id][$i % 2],
                'aktivitas' => "POST /admin — {$a} → 302",
                'ip_address' => '127.0.0.1',
                'user_agent' => 'DemoDataSeeder',
                'waktu' => now()->subHours(5 - $i),
            ]);
        }

        $this->command->info('Data demo Banyuwangi selesai: 4 staff, 12 atlet, 4 event, sesi+skor realistis.');
    }

    private function pengguna(string $username, string $role, string $nama): User
    {
        $u = User::create(['username' => $username, 'password' => 'password123', 'role' => $role]);
        $u->assignRole($role);

        return $u;
    }

    private function sesi(Event $event, Atlet $atlet, int $oleh, int $ends): void
    {
        $sesi = Sesi::create([
            'event_id' => $event->id,
            'atlet_id' => $atlet->id,
            'tanggal_sesi' => $event->tanggal,
            'created_by' => $oleh,
        ]);
        $total = 0;
        for ($e = 1; $e <= $ends; $e++) {
            $panah = array_map(fn() => max(0, min(10, $atlet->skill + mt_rand(-2, 2))), range(1, 6));
            $sum = array_sum($panah);
            $skor = $sesi->skor()->create([
                'end_ke' => $e,
                'skor1' => $panah[0],
                'skor2' => $panah[1],
                'skor3' => $panah[2],
                'skor4' => $panah[3],
                'skor5' => $panah[4],
                'skor6' => $panah[5],
                'total_end' => $sum,
            ]);
            $total += $sum;
            LogSkor::create([
                'skor_id' => $skor->id,
                'user_id' => $oleh,
                'perubahan' => 'Skor dibuat (input awal)',
                'data_baru' => ['end_ke' => $e, 'total_end' => $sum],
            ]);
        }
        $sesi->update(['total_skor' => $total]);
    }
}
