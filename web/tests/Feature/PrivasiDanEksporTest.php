<?php

namespace Tests\Feature;

use App\Models\Atlet;
use App\Models\Kategori;
use App\Models\PengaturanSistem;
use App\Models\Sekolah;
use App\Models\Sesi;
use App\Models\SesiLatihan;
use App\Models\User;
use Database\Seeders\PengaturanSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivasiDanEksporTest extends TestCase
{
    use RefreshDatabase;

    private User $userAtletPublik;
    private Atlet $atletPublik;
    private User $userAtletPrivat;
    private Atlet $atletPrivat;
    private SesiLatihan $sesiLatihan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, PengaturanSeeder::class]);

        $sekolah = Sekolah::create(['nama_sekolah' => 'Klub Panahan Garuda']);
        $kategori = Kategori::create([
            'nama_kategori' => 'Standar Bow',
            'jarak_tempuh' => 18,
            'jumlah_panah_per_end' => 6,
        ]);

        // Atlet 1: Izinkan tampil publik
        $this->userAtletPublik = User::create([
            'username' => 'atlet_publik',
            'password' => 'secret123',
            'role' => 'atlet',
        ]);
        $this->userAtletPublik->assignRole('atlet');
        $this->atletPublik = Atlet::create([
            'user_id' => $this->userAtletPublik->id,
            'nia' => 'NIA-PUB-01',
            'nama_lengkap' => 'Bambang Publik',
            'sekolah_id' => $sekolah->id,
            'kategori_id' => $kategori->id,
            'status' => 'aktif',
            'izinkan_tampil_publik' => true,
        ]);

        // Atlet 2: Tidak izinkan tampil publik (privat)
        $this->userAtletPrivat = User::create([
            'username' => 'atlet_privat',
            'password' => 'secret123',
            'role' => 'atlet',
        ]);
        $this->userAtletPrivat->assignRole('atlet');
        $this->atletPrivat = Atlet::create([
            'user_id' => $this->userAtletPrivat->id,
            'nia' => 'NIA-PRIV-02',
            'nama_lengkap' => 'Siti Privat',
            'sekolah_id' => $sekolah->id,
            'kategori_id' => $kategori->id,
            'status' => 'aktif',
            'izinkan_tampil_publik' => false,
        ]);

        $this->sesiLatihan = SesiLatihan::create([
            'nama_sesi' => 'Latihan Bersama Terbuka',
            'tanggal' => '2026-10-01',
            'jam_mulai' => '08:00',
            'jam_selesai' => '11:00',
            'lokasi' => 'Lapangan Utama',
            'tipe_latihan' => 'rutin_mingguan',
            'status' => 'selesai',
            'jarak_meter' => 18,
            'is_publik' => 1,
        ]);

        // Sesi nilai untuk kedua atlet
        Sesi::create([
            'sesi_latihan_id' => $this->sesiLatihan->id,
            'atlet_id' => $this->atletPublik->id,
            'tanggal_sesi' => '2026-10-01',
            'total_skor' => 295,
            'jarak_meter' => 18,
        ]);

        Sesi::create([
            'sesi_latihan_id' => $this->sesiLatihan->id,
            'atlet_id' => $this->atletPrivat->id,
            'tanggal_sesi' => '2026-10-01',
            'total_skor' => 290,
            'jarak_meter' => 18,
        ]);
    }

    public function test_atlet_bisa_mengatur_opsi_privasi_pada_profil(): void
    {
        $this->actingAs($this->userAtletPrivat);

        $res = $this->put('/atlet/profil', [
            'nia' => $this->atletPrivat->nia,
            'nama_lengkap' => $this->atletPrivat->nama_lengkap,
            'izinkan_tampil_publik' => 1,
        ]);

        $res->assertRedirect('/atlet/profil');
        $this->assertDatabaseHas('atlet', [
            'id' => $this->atletPrivat->id,
            'izinkan_tampil_publik' => 1,
        ]);
    }

    public function test_skor_atlet_privat_tidak_tampil_ke_tamu_publik(): void
    {
        // Akses sebagai tamu (tanpa login)
        $res = $this->get("/sesi-latihan/{$this->sesiLatihan->id}");
        $res->assertOk();

        // Atlet publik tampil
        $res->assertSee('Bambang Publik');
        $res->assertSee('295');

        // Atlet privat TIDAK tampil
        $res->assertDontSee('Siti Privat');
    }

    public function test_pelatih_bisa_melihat_semua_atlet_termasuk_yang_privat(): void
    {
        $pelatih = User::create([
            'username' => 'pelatih_sandi',
            'password' => 'secret123',
            'role' => 'pelatih',
        ]);
        $pelatih->assignRole('pelatih');
        $this->actingAs($pelatih);

        $res = $this->get("/pelatih/sesi-latihan/{$this->sesiLatihan->id}");
        $res->assertOk();
        $res->assertSee('Bambang Publik');
        $res->assertSee('Siti Privat');
    }

    public function test_toggle_global_leaderboard_publik_menonaktifkan_akses_tamu(): void
    {
        // Matikan leaderboard publik
        PengaturanSistem::where('key_setting', 'leaderboard_publik')->update(['value_setting' => '0']);

        // Tamu mencoba akses -> harus 403 Forbidden
        $res = $this->get("/sesi-latihan/{$this->sesiLatihan->id}");
        $res->assertForbidden();
    }

    public function test_pelatih_bisa_ekspor_laporan_performa_excel_dan_pdf(): void
    {
        $pelatih = User::create([
            'username' => 'coach_eko',
            'password' => 'secret123',
            'role' => 'pelatih',
        ]);
        $pelatih->assignRole('pelatih');
        $this->actingAs($pelatih);

        // Ekspor Excel
        $resExcel = $this->post('/pelatih/ekspor/performa', [
            'format' => 'excel',
            'sesi_latihan_id' => $this->sesiLatihan->id,
        ]);
        $resExcel->assertOk();

        // Ekspor PDF
        $resPdf = $this->post('/pelatih/ekspor/performa', [
            'format' => 'pdf',
            'sesi_latihan_id' => $this->sesiLatihan->id,
        ]);
        $resPdf->assertOk();
    }
}
