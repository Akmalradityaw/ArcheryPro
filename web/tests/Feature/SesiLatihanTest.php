<?php

namespace Tests\Feature;

use App\Models\SesiLatihan;
use App\Models\User;
use Database\Seeders\PengaturanSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SesiLatihanTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, PengaturanSeeder::class]);

        $this->admin = User::create([
            'username' => 'admin_test',
            'password' => 'admin123',
            'role' => 'admin',
        ]);
        $this->admin->assignRole('admin');
        $this->actingAs($this->admin);
    }

    public function test_admin_bisa_membuat_jadwal_latihan(): void
    {
        $res = $this->post('/admin/sesi-latihan', [
            'nama_sesi' => 'Latihan Rutin Pekan 39',
            'tanggal' => '2026-09-28',
            'jam_mulai' => '08:00',
            'jam_selesai' => '11:00',
            'lokasi' => 'Lapangan Utama',
            'jenis_latihan' => 'rutin_mingguan',
            'status' => 'terjadwal',
            'fokus_latihan' => 'Uji akurasi 30m',
            'is_publik' => 1,
        ]);

        $res->assertRedirect('/admin/sesi-latihan');
        $this->assertDatabaseHas('sesi_latihan', [
            'nama_sesi' => 'Latihan Rutin Pekan 39',
            'status' => 'terjadwal',
        ]);
    }

    public function test_fitur_duplikasi_jadwal_ke_pekan_depan(): void
    {
        $sesi = SesiLatihan::create([
            'nama_sesi' => 'Latihan Sabtu Pagi',
            'tanggal' => '2026-09-26',
            'jam_mulai' => '07:30',
            'jam_selesai' => '10:30',
            'lokasi' => 'Lapangan Tembak',
            'jenis_latihan' => 'rutin_mingguan',
            'status' => 'selesai',
            'fokus_latihan' => 'Teknik rilis',
            'created_by' => $this->admin->id,
        ]);

        $res = $this->post("/admin/sesi-latihan/{$sesi->id}/duplicate");

        $res->assertRedirect();
        $this->assertDatabaseHas('sesi_latihan', [
            'nama_sesi' => 'Latihan Sabtu Pagi (Salinan)',
            'status' => 'terjadwal',
        ]);
        $salinan = SesiLatihan::where('nama_sesi', 'Latihan Sabtu Pagi (Salinan)')->first();
        $this->assertEquals('2026-10-03', $salinan->tanggal->format('Y-m-d'));
    }

    public function test_simpan_skor_dengan_sesi_latihan_id(): void
    {
        $skorUser = User::create(['username' => 'petugas_skor', 'password' => 'secret', 'role' => 'scoring']);
        $skorUser->assignRole('scoring');
        $this->actingAs($skorUser);

        $sesiLatihan = SesiLatihan::create([
            'nama_sesi' => 'Latihan Scoring Pekanan',
            'tanggal' => '2026-09-28',
            'status' => 'berlangsung',
            'created_by' => $this->admin->id,
        ]);

        $u = User::create(['username' => 'atlet_1', 'password' => 'secret', 'role' => 'atlet']);
        $u->assignRole('atlet');
        $atlet = $u->atlet()->create(['nia' => 'NIA001', 'nama_lengkap' => 'Budi Pratama', 'status' => 'aktif']);

        $res = $this->post('/scoring/input/selesai', [
            'sesi_latihan_id' => $sesiLatihan->id,
            'tanggal_sesi' => '2026-09-28',
            'jarak_meter' => 30,
            'catatan_pelatih' => 'Konsistensi bagus',
            'atlet_ids' => [$atlet->id],
            'skor' => [
                $atlet->id => [
                    [9, 9, 8, 8, 7, 7], // 48
                ],
            ],
        ]);

        $res->assertRedirect('/scoring/riwayat');
        $this->assertDatabaseHas('sesi', [
            'sesi_latihan_id' => $sesiLatihan->id,
            'atlet_id' => $atlet->id,
            'jarak_meter' => 30,
            'catatan_pelatih' => 'Konsistensi bagus',
            'total_skor' => 48,
        ]);
    }
}
