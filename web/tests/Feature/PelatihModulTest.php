<?php

namespace Tests\Feature;

use App\Models\Atlet;
use App\Models\Kategori;
use App\Models\Sekolah;
use App\Models\Sesi;
use App\Models\SesiLatihan;
use App\Models\Skor;
use App\Models\User;
use Database\Seeders\PengaturanSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PelatihModulTest extends TestCase
{
    use RefreshDatabase;

    private User $pelatih;
    private Atlet $atlet;
    private SesiLatihan $sesiLatihan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, PengaturanSeeder::class]);

        $this->pelatih = User::create([
            'username' => 'coach_budi',
            'password' => 'secret123',
            'role' => 'pelatih',
        ]);
        $this->pelatih->assignRole('pelatih');

        $userAtlet = User::create([
            'username' => 'atlet_dimas',
            'password' => 'secret123',
            'role' => 'atlet',
        ]);
        $userAtlet->assignRole('atlet');

        $sekolah = Sekolah::create(['nama_sekolah' => 'SMAN 1 Archery']);
        $kategori = Kategori::create([
            'nama_kategori' => 'Recurve Men',
            'jarak_tempuh' => 70,
            'jumlah_panah_per_end' => 6,
        ]);

        $this->atlet = Atlet::create([
            'user_id' => $userAtlet->id,
            'nia' => 'NIA-2026-001',
            'nama_lengkap' => 'Dimas Archery',
            'sekolah_id' => $sekolah->id,
            'kategori_id' => $kategori->id,
            'nomor_target' => 'A1',
            'status' => 'aktif',
        ]);

        $this->sesiLatihan = SesiLatihan::create([
            'nama_sesi' => 'Latihan Rutin Rabu Sore',
            'tanggal' => '2026-09-30',
            'jam_mulai' => '15:30',
            'jam_selesai' => '18:00',
            'lokasi' => 'Range A',
            'tipe_latihan' => 'rutin_mingguan',
            'status' => 'berlangsung',
            'jarak_meter' => 18,
            'is_publik' => 1,
        ]);
    }

    public function test_pelatih_bisa_mengakses_jadwal_latihan(): void
    {
        $this->actingAs($this->pelatih);

        $res = $this->get('/pelatih/sesi-latihan');
        $res->assertOk();
        $res->assertSee('Latihan Rutin Rabu Sore');
        $res->assertSee('Range A');
    }

    public function test_pelatih_bisa_melihat_detail_dan_evaluasi_sesi_latihan(): void
    {
        $this->actingAs($this->pelatih);

        $sesi = Sesi::create([
            'sesi_latihan_id' => $this->sesiLatihan->id,
            'atlet_id' => $this->atlet->id,
            'tanggal_sesi' => '2026-09-30',
            'total_skor' => 280,
            'jarak_meter' => 18,
            'created_by' => $this->pelatih->id,
        ]);

        Skor::create([
            'sesi_id' => $sesi->id,
            'end_ke' => 1,
            'skor1' => 10, 'skor2' => 9, 'skor3' => 9, 'skor4' => 8, 'skor5' => 8, 'skor6' => 8,
            'total_end' => 52,
        ]);

        Skor::create([
            'sesi_id' => $sesi->id,
            'end_ke' => 2,
            'skor1' => 10, 'skor2' => 10, 'skor3' => 9, 'skor4' => 9, 'skor5' => 8, 'skor6' => 8,
            'total_end' => 54,
        ]);

        $res = $this->get("/pelatih/sesi-latihan/{$this->sesiLatihan->id}");
        $res->assertOk();
        $res->assertSee('Dimas Archery');
        $res->assertSee('280');
    }

    public function test_pelatih_bisa_memberikan_catatan_evaluasi_pada_sesi_atlet(): void
    {
        $this->actingAs($this->pelatih);

        $sesi = Sesi::create([
            'sesi_latihan_id' => $this->sesiLatihan->id,
            'atlet_id' => $this->atlet->id,
            'tanggal_sesi' => '2026-09-30',
            'total_skor' => 280,
            'jarak_meter' => 18,
            'created_by' => $this->pelatih->id,
            'catatan_dibaca_at' => now(), // previously marked as read
        ]);

        $res = $this->post("/pelatih/sesi/{$sesi->id}/catatan", [
            'catatan_pelatih' => 'Posisi bahu kiri perlu diturunkan sedikit saat follow-through.',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('sesi', [
            'id' => $sesi->id,
            'catatan_pelatih' => 'Posisi bahu kiri perlu diturunkan sedikit saat follow-through.',
            'catatan_dibaca_at' => null, // reset agar atlet tahu ada catatan baru
        ]);
    }

    public function test_pelatih_bisa_melihat_analisis_atlet_dengan_stdev_dan_moving_average(): void
    {
        $this->actingAs($this->pelatih);

        // Buat 4 sesi latihan berbeda untuk menguji moving average & standard deviation
        for ($i = 1; $i <= 4; $i++) {
            $latihan = SesiLatihan::create([
                'nama_sesi' => "Latihan Pekan {$i}",
                'tanggal' => "2026-09-0{$i}",
                'jam_mulai' => '15:30',
                'jam_selesai' => '18:00',
                'lokasi' => 'Range A',
                'tipe_latihan' => 'rutin_mingguan',
                'status' => 'selesai',
                'jarak_meter' => 18,
                'is_publik' => 1,
            ]);

            $s = Sesi::create([
                'sesi_latihan_id' => $latihan->id,
                'atlet_id' => $this->atlet->id,
                'tanggal_sesi' => "2026-09-0{$i}",
                'total_skor' => 250 + ($i * 10),
                'jarak_meter' => 18,
                'created_by' => $this->pelatih->id,
            ]);

            Skor::create([
                'sesi_id' => $s->id,
                'end_ke' => 1,
                'skor1' => 9, 'skor2' => 9, 'skor3' => 8, 'skor4' => 8, 'skor5' => 8, 'skor6' => 8,
                'total_end' => 50,
            ]);

            Skor::create([
                'sesi_id' => $s->id,
                'end_ke' => 2,
                'skor1' => 10, 'skor2' => 9, 'skor3' => 9, 'skor4' => 9, 'skor5' => 8, 'skor6' => 7,
                'total_end' => 52,
            ]);
        }

        $res = $this->get("/pelatih/analisis/{$this->atlet->id}");
        $res->assertOk();
        $res->assertSee('Dimas Archery');
        $res->assertSee('Tren Performa');
        $res->assertSee('Moving Average');
        $res->assertSee('Stabilitas (Standar Deviasi)');
    }

    /** @test */
    public function pelatih_bisa_mengakses_halaman_input_skor(): void
    {
        $this->actingAs($this->pelatih);

        $res = $this->get(route('pelatih.skor.create', ['sesi_latihan_id' => $this->sesiLatihan->id]));
        $res->assertOk();
        $res->assertSee('Input Skor Latihan Mingguan');
        $res->assertSee($this->sesiLatihan->nama_sesi);
        $res->assertSee($this->atlet->nama_lengkap);
    }

    /** @test */
    public function pelatih_bisa_menyimpan_skor_latihan_dan_catatan_kepelatihan(): void
    {
        $this->actingAs($this->pelatih);

        $payload = [
            'sesi_latihan_id' => $this->sesiLatihan->id,
            'tanggal_sesi' => '2026-09-28',
            'jarak_meter' => 30,
            'catatan_pelatih' => 'Release sangat konsisten, perbaiki follow-through.',
            'atlet_ids' => [$this->atlet->id],
            'skor' => [
                $this->atlet->id => [
                    [10, 9, 9, 8, 8, 8], // Total 52
                    [10, 10, 9, 9, 8, 7], // Total 53
                ],
            ],
        ];

        $res = $this->post(route('pelatih.skor.selesai'), $payload);
        $res->assertRedirect(route('pelatih.sesi-latihan.show', $this->sesiLatihan->id));
        $res->assertSessionHas('success');

        $this->assertDatabaseHas('sesi', [
            'sesi_latihan_id' => $this->sesiLatihan->id,
            'atlet_id' => $this->atlet->id,
            'catatan_pelatih' => 'Release sangat konsisten, perbaiki follow-through.',
            'total_skor' => 105,
            'created_by' => $this->pelatih->id,
        ]);

        $this->assertDatabaseHas('skor', [
            'end_ke' => 1,
            'total_end' => 52,
        ]);
        $this->assertDatabaseHas('skor', [
            'end_ke' => 2,
            'total_end' => 53,
        ]);
    }
}

