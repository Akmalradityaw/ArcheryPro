<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PengaturanSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrasiTokenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, PengaturanSeeder::class]);
    }

    public function test_register_pelatih_token_valid(): void
    {
        $res = $this->post('/register/pelatih', [
            'username' => 'plt1', 'email' => 'plt1@mail.com', 'password' => 'password123',
            'password_confirmation' => 'password123', 'token' => 'PELATIH2025',
        ]);

        $res->assertRedirect('/pelatih/dashboard');
        $this->assertDatabaseHas('users', ['username' => 'plt1', 'role' => 'pelatih', 'email' => 'plt1@mail.com']);
        $this->assertTrue(User::where('username', 'plt1')->first()->hasRole('pelatih'));
    }

    public function test_register_pelatih_token_salah(): void
    {
        $res = $this->post('/register/pelatih', [
            'username' => 'plt2', 'password' => 'password123',
            'password_confirmation' => 'password123', 'token' => 'SALAH',
        ]);

        $res->assertSessionHasErrors('token');
        $this->assertDatabaseMissing('users', ['username' => 'plt2']);
    }

    public function test_register_scoring_token_valid(): void
    {
        $res = $this->post('/register/scoring', [
            'username' => 'skr1', 'email' => 'skr1@mail.com', 'password' => 'password123',
            'password_confirmation' => 'password123', 'token' => 'SKORING2025',
        ]);

        $res->assertRedirect('/scoring/dashboard');
        $this->assertDatabaseHas('users', ['username' => 'skr1', 'role' => 'scoring', 'email' => 'skr1@mail.com']);
    }

    public function test_register_atlet_tanpa_token(): void
    {
        $res = $this->post('/register/atlet', [
            'username' => 'atl1', 'email' => 'atl1@mail.com', 'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $res->assertRedirect('/atlet/dashboard');
        $this->assertDatabaseHas('users', ['username' => 'atl1', 'role' => 'atlet', 'email' => 'atl1@mail.com']);
    }
}
