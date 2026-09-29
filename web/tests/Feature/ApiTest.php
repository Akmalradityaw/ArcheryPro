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

class ApiTest extends TestCase
{
    use RefreshDatabase;

    private User $pelatih;
    private User $userAtlet;
    private Atlet $atlet;
    private SesiLatihan $sesiLatihan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, PengaturanSeeder::class]);

        $this->pelatih = User::create([
            'username' => 'coach_api',
            'email' => 'coach@example.com',
            'password' => 'secret123',
            'role' => 'pelatih',
        ]);
        $this->pelatih->assignRole('pelatih');

        $this->userAtlet = User::create([
            'username' => 'atlet_api',
            'email' => 'atlet@example.com',
            'password' => 'secret123',
            'role' => 'atlet',
        ]);
        $this->userAtlet->assignRole('atlet');

        $sekolah = Sekolah::create(['nama_sekolah' => 'SMA Prestasi Panahan']);
        $kategori = Kategori::create([
            'nama_kategori' => 'Recurve Men 70m',
            'jarak_tempuh' => 70,
            'jumlah_panah_per_end' => 6,
        ]);

        $this->atlet = Atlet::create([
            'user_id' => $this->userAtlet->id,
            'nia' => 'NIA-API-001',
            'nama_lengkap' => 'Ahmad Mobile Archer',
            'sekolah_id' => $sekolah->id,
            'kategori_id' => $kategori->id,
            'nomor_target' => 'B2',
            'status' => 'aktif',
            'izinkan_tampil_publik' => true,
        ]);

        $this->sesiLatihan = SesiLatihan::create([
            'nama_sesi' => 'Latihan Rutin API',
            'tanggal' => date('Y-m-d'),
            'jam_mulai' => '08:00',
            'jam_selesai' => '11:00',
            'lokasi' => 'Lapangan Panahan A',
            'jenis_latihan' => 'rutin_mingguan',
            'status' => 'berlangsung',
            'is_publik' => true,
        ]);
    }

    /** @test */
    public function login_berhasil_mengembalikan_token_sanctum_dan_profil(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'atlet_api',
            'password' => 'secret123',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'token_type',
                    'user' => [
                        'id',
                        'username',
                        'role',
                        'atlet' => [
                            'id',
                            'nia',
                            'nama_lengkap',
                        ],
                    ],
                ],
            ]);
    }

    /** @test */
    public function login_gagal_jika_kredensial_salah(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'atlet_api',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    /** @test */
    public function endpoint_me_mengembalikan_data_profil_dengan_token(): void
    {
        $token = $this->userAtlet->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.username', 'atlet_api')
            ->assertJsonPath('data.atlet.nama_lengkap', 'Ahmad Mobile Archer');
    }

    /** @test */
    public function daftar_sesi_latihan_mengembalikan_paginasi_10_data(): void
    {
        $token = $this->pelatih->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/sesi-latihan');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.per_page', 10);
    }

    /** @test */
    public function input_skor_via_api_berhasil_menyimpan_data_skor(): void
    {
        $token = $this->pelatih->createToken('test')->plainTextToken;

        $payload = [
            'sesi_latihan_id' => $this->sesiLatihan->id,
            'tanggal_sesi' => date('Y-m-d'),
            'jarak_meter' => 70,
            'catatan_pelatih' => 'Catatan kepelatihan via API REST mobile.',
            'atlet_ids' => [$this->atlet->id],
            'skor' => [
                $this->atlet->id => [
                    [10, 9, 9, 8, 8, 8], // 52
                    [10, 10, 9, 9, 9, 8], // 55
                ],
            ],
        ];

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/skor', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('sesi', [
            'sesi_latihan_id' => $this->sesiLatihan->id,
            'atlet_id' => $this->atlet->id,
            'total_skor' => 107,
            'catatan_pelatih' => 'Catatan kepelatihan via API REST mobile.',
        ]);
    }

    /** @test */
    public function performa_atlet_mengembalikan_statistik_dan_badges(): void
    {
        $sesi = Sesi::create([
            'sesi_latihan_id' => $this->sesiLatihan->id,
            'atlet_id' => $this->atlet->id,
            'tanggal_sesi' => date('Y-m-d'),
            'total_skor' => 285,
            'created_by' => $this->pelatih->id,
        ]);

        Skor::create([
            'sesi_id' => $sesi->id,
            'end_ke' => 1,
            'skor1' => 10, 'skor2' => 10, 'skor3' => 9, 'skor4' => 9, 'skor5' => 8, 'skor6' => 8,
            'total_end' => 54,
        ]);

        $token = $this->userAtlet->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/atlet/{$this->atlet->id}/performa");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.statistik.total_sesi', 1)
            ->assertJsonPath('data.statistik.skor_tertinggi', 285);
    }

    /** @test */
    public function atlet_bisa_mengubah_opsi_privasi_publik(): void
    {
        $token = $this->userAtlet->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/v1/atlet/privasi', [
                'izinkan_tampil_publik' => false,
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.izinkan_tampil_publik', false);

        $this->assertDatabaseHas('atlet', [
            'id' => $this->atlet->id,
            'izinkan_tampil_publik' => false,
        ]);
    }
}
