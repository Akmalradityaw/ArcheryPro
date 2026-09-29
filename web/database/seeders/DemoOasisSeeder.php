<?php

namespace Database\Seeders;

use App\Models\Atlet;
use App\Models\Kategori;
use App\Models\Sekolah;
use App\Models\SesiLatihan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoOasisSeeder extends Seeder
{
    /**
     * Run the database seeds for Demo Sore: Latihan Oasis.
     */
    public function run(): void
    {
        $today = Carbon::today();

        // 1. Akun Pelatih: Irsyadi Bagas
        $pelatihUser = User::updateOrCreate(
            ['username' => 'irsyadibagas'],
            [
                'email' => 'irsyadi.bagas@archerypro.test',
                'password' => 'password123',
                'role' => 'pelatih',
            ]
        );
        $pelatihUser->syncRoles(['pelatih']);

        // 2. Tiga Atlet (Fadly + 2 Atlet Dummy, seluruh data skor KOSONG)
        $sekolahBwi = Sekolah::firstOrCreate(
            ['nama_sekolah' => 'SMAN 1 Banyuwangi'],
            ['kecamatan' => 'Banyuwangi', 'alamat' => 'Jl. Wijaya Kusuma No. 10']
        );
        $sekolahGenteng = Sekolah::firstOrCreate(
            ['nama_sekolah' => 'SMAN 1 Genteng'],
            ['kecamatan' => 'Genteng', 'alamat' => 'Jl. Raya Genteng No. 45']
        );
        $sekolahRogo = Sekolah::firstOrCreate(
            ['nama_sekolah' => 'SMAN 1 Rogojampi'],
            ['kecamatan' => 'Rogojampi', 'alamat' => 'Jl. Raya Rogojampi No. 1']
        );

        $kategoriRecurve = Kategori::firstOrCreate(['nama_kategori' => 'Recurve']);
        $kategoriCompound = Kategori::firstOrCreate(['nama_kategori' => 'Compound']);
        $kategoriBarebow = Kategori::firstOrCreate(['nama_kategori' => 'Barebow']);

        $atletData = [
            [
                'username' => 'fadly',
                'nama_lengkap' => 'Fadly',
                'nia' => 'ARC-OASIS-01',
                'jenis_kelamin' => 'L',
                'sekolah_id' => $sekolahBwi->id,
                'kategori_id' => $kategoriRecurve->id,
                'kelas' => 'XI MIPA 2',
                'no_telepon' => '081234567801',
            ],
            [
                'username' => 'aditya',
                'nama_lengkap' => 'Aditya Pratama',
                'nia' => 'ARC-OASIS-02',
                'jenis_kelamin' => 'L',
                'sekolah_id' => $sekolahGenteng->id,
                'kategori_id' => $kategoriCompound->id,
                'kelas' => 'XI IPS 1',
                'no_telepon' => '081234567802',
            ],
            [
                'username' => 'nabila',
                'nama_lengkap' => 'Nabila Syahrani',
                'nia' => 'ARC-OASIS-03',
                'jenis_kelamin' => 'P',
                'sekolah_id' => $sekolahRogo->id,
                'kategori_id' => $kategoriBarebow->id,
                'kelas' => 'X-4',
                'no_telepon' => '081234567803',
            ],
        ];

        foreach ($atletData as $d) {
            $atletUser = User::updateOrCreate(
                ['username' => $d['username']],
                [
                    'email' => "{$d['username']}@archerypro.test",
                    'password' => 'password123',
                    'role' => 'atlet',
                ]
            );
            $atletUser->syncRoles(['atlet']);

            Atlet::updateOrCreate(
                ['user_id' => $atletUser->id],
                [
                    'nia' => $d['nia'],
                    'nama_lengkap' => $d['nama_lengkap'],
                    'tempat_lahir' => 'Banyuwangi',
                    'tanggal_lahir' => '2008-05-14',
                    'jenis_kelamin' => $d['jenis_kelamin'],
                    'sekolah_id' => $d['sekolah_id'],
                    'kategori_id' => $d['kategori_id'],
                    'kelas' => $d['kelas'],
                    'no_telepon' => $d['no_telepon'],
                    'status' => 'aktif',
                    'izinkan_tampil_publik' => true,
                ]
            );
        }

        // 3. Sesi Latihan: "Latihan Oasis" (Sore Hari, Skor KOSONG untuk demo live)
        $sesiOasis = SesiLatihan::updateOrCreate(
            [
                'nama_sesi' => 'Latihan Oasis',
                'tanggal' => $today->format('Y-m-d'),
            ],
            [
                'jam_mulai' => '15:30:00',
                'jam_selesai' => '17:30:00',
                'lokasi' => 'Lapangan Panahan Oasis',
                'jenis_latihan' => 'rutin_mingguan',
                'status' => 'terjadwal',
                'fokus_latihan' => 'Evaluasi Form, Konsistensi Anchor & Akurasi Rilis Sore Hari',
                'minggu_ke' => (int) $today->isoWeek(),
                'tahun' => (int) $today->year,
                'is_publik' => true,
                'created_by' => $pelatihUser->id,
            ]
        );

        // Pastikan bersih dari sesi dan skor tes apapun secara permanen (force delete)
        $sesiOasis->sesi()->withTrashed()->each(function ($s) {
            $s->skor()->delete();
            $s->forceDelete();
        });

        $this->command->info("Seeder 'Latihan Oasis' berhasil dibuat!");
        $this->command->info("- Pelatih: Irsyadi Bagas (username: irsyadibagas | pass: password123)");
        $this->command->info("- Atlet 1: Fadly (username: fadly | pass: password123)");
        $this->command->info("- Atlet 2: Aditya Pratama (username: aditya | pass: password123)");
        $this->command->info("- Atlet 3: Nabila Syahrani (username: nabila | pass: password123)");
        $this->command->info("- Sesi: {$sesiOasis->nama_sesi} ({$today->format('d M Y')}, 15:30 - 17:30) [Skor Kosong Siap Demo]");
    }
}
