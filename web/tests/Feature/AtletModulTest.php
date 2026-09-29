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

class AtletModulTest extends TestCase
{
    use RefreshDatabase;

    private User $userAtlet;
    private Atlet $atlet;
    private SesiLatihan $sesiLatihan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, PengaturanSeeder::class]);

        $this->userAtlet = User::create([
            'username' => 'atlet_rina',
            'password' => 'secret123',
            'role' => 'atlet',
        ]);
        $this->userAtlet->assignRole('atlet');

        $sekolah = Sekolah::create(['nama_sekolah' => 'SMAN 2 Archery']);
        $kategori = Kategori::create([
            'nama_kategori' => 'Recurve Women',
            'jarak_tempuh' => 18,
            'jumlah_panah_per_end' => 6,
        ]);

        $this->atlet = Atlet::create([
            'user_id' => $this->userAtlet->id,
            'nia' => 'NIA-2026-002',
            'nama_lengkap' => 'Rina Panahan',
            'sekolah_id' => $sekolah->id,
            'kategori_id' => $kategori->id,
            'nomor_target' => 'B1',
            'status' => 'aktif',
        ]);

        $this->sesiLatihan = SesiLatihan::create([
            'nama_sesi' => 'Latihan Rutin Kamis',
            'tanggal' => '2026-10-01',
            'jam_mulai' => '16:00',
            'jam_selesai' => '18:00',
            'lokasi' => 'Range B',
            'tipe_latihan' => 'rutin_mingguan',
            'status' => 'berlangsung',
            'jarak_meter' => 18,
            'is_publik' => 1,
        ]);
    }

    public function test_atlet_bisa_mengakses_dashboard_dengan_badge_dan_stat(): void
    {
        $this->actingAs($this->userAtlet);

        $sesi = Sesi::create([
            'sesi_latihan_id' => $this->sesiLatihan->id,
            'atlet_id' => $this->atlet->id,
            'tanggal_sesi' => '2026-10-01',
            'total_skor' => 285,
            'jarak_meter' => 18,
            'created_by' => $this->userAtlet->id,
        ]);

        Skor::create([
            'sesi_id' => $sesi->id,
            'end_ke' => 1,
            'skor1' => 10, 'skor2' => 10, 'skor3' => 9, 'skor4' => 9, 'skor5' => 9, 'skor6' => 8,
            'total_end' => 55,
        ]);

        $res = $this->get('/atlet/dashboard');
        $res->assertOk();
        $res->assertSee('Dashboard Atlet');
        $res->assertSee('Lencana Pencapaian Latihan');
        $res->assertSee('Akurasi Tinggi (280+)');
        $res->assertSee('285');
    }

    public function test_atlet_melihat_alert_catatan_pelatih_baru(): void
    {
        $this->actingAs($this->userAtlet);

        Sesi::create([
            'sesi_latihan_id' => $this->sesiLatihan->id,
            'atlet_id' => $this->atlet->id,
            'tanggal_sesi' => '2026-10-01',
            'total_skor' => 270,
            'jarak_meter' => 18,
            'catatan_pelatih' => 'Fokus pada nafas sebelum melepas anak panah.',
            'catatan_dibaca_at' => null,
            'created_by' => $this->userAtlet->id,
        ]);

        $res = $this->get('/atlet/dashboard');
        $res->assertOk();
        $res->assertSee('Catatan Pelatih Baru');
        $res->assertSee('Fokus pada nafas sebelum melepas anak panah.');
        $res->assertSee('Tandai Sudah Dibaca');
    }

    public function test_atlet_bisa_menandai_catatan_pelatih_sudah_dibaca(): void
    {
        $this->actingAs($this->userAtlet);

        $sesi = Sesi::create([
            'sesi_latihan_id' => $this->sesiLatihan->id,
            'atlet_id' => $this->atlet->id,
            'tanggal_sesi' => '2026-10-01',
            'total_skor' => 270,
            'jarak_meter' => 18,
            'catatan_pelatih' => 'Pertahankan ritme draw.',
            'catatan_dibaca_at' => null,
            'created_by' => $this->userAtlet->id,
        ]);

        $res = $this->post("/atlet/sesi/{$sesi->id}/baca-catatan");
        $res->assertRedirect();

        $sesi->refresh();
        $this->assertNotNull($sesi->catatan_dibaca_at);
        $this->assertTrue($sesi->isCatatanDibaca());
    }

    public function test_atlet_tidak_bisa_menandai_catatan_milik_atlet_lain(): void
    {
        $this->actingAs($this->userAtlet);

        $userLain = User::create([
            'username' => 'atlet_lain',
            'password' => 'secret123',
            'role' => 'atlet',
        ]);
        $userLain->assignRole('atlet');

        $atletLain = Atlet::create([
            'user_id' => $userLain->id,
            'nia' => 'NIA-2026-003',
            'nama_lengkap' => 'Atlet Lain',
            'sekolah_id' => $this->atlet->sekolah_id,
            'kategori_id' => $this->atlet->kategori_id,
            'nomor_target' => 'B2',
            'status' => 'aktif',
        ]);

        $sesiLain = Sesi::create([
            'sesi_latihan_id' => $this->sesiLatihan->id,
            'atlet_id' => $atletLain->id,
            'tanggal_sesi' => '2026-10-01',
            'total_skor' => 250,
            'jarak_meter' => 18,
            'catatan_pelatih' => 'Catatan pribadi untuk atlet lain.',
            'catatan_dibaca_at' => null,
            'created_by' => $userLain->id,
        ]);

        $res = $this->post("/atlet/sesi/{$sesiLain->id}/baca-catatan");
        $res->assertForbidden();
    }

    public function test_atlet_bisa_melihat_grafik_performa(): void
    {
        $this->actingAs($this->userAtlet);

        Sesi::create([
            'sesi_latihan_id' => $this->sesiLatihan->id,
            'atlet_id' => $this->atlet->id,
            'tanggal_sesi' => '2026-10-01',
            'total_skor' => 275,
            'jarak_meter' => 18,
            'created_by' => $this->userAtlet->id,
        ]);

        $res = $this->get('/atlet/grafik');
        $res->assertOk();
        $res->assertSee('Grafik Performa Latihan');
        $res->assertSee('Sesi Terakhir');
        $res->assertSee('275');
    }

    public function test_atlet_bisa_melihat_riwayat_latihan_dengan_filter(): void
    {
        $this->actingAs($this->userAtlet);

        Sesi::create([
            'sesi_latihan_id' => $this->sesiLatihan->id,
            'atlet_id' => $this->atlet->id,
            'tanggal_sesi' => '2026-10-01',
            'total_skor' => 280,
            'jarak_meter' => 18,
            'created_by' => $this->userAtlet->id,
        ]);

        $res = $this->get("/atlet/riwayat?sesi_latihan_id={$this->sesiLatihan->id}");
        $res->assertOk();
        $res->assertSee('Latihan Rutin Kamis');
        $res->assertSee('280');
    }
}
