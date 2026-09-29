<?php

namespace Tests\Feature;

use App\Models\Atlet;
use App\Models\Event;
use App\Models\User;
use Database\Seeders\PengaturanSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, PengaturanSeeder::class]);
        $admin = User::create(['username' => 'admin', 'password' => 'admin123', 'role' => 'admin']);
        $admin->assignRole('admin');
    }

    public function test_login_valid_redirect_dashboard(): void
    {
        $res = $this->post('/login', ['username' => 'admin', 'password' => 'admin123']);

        $res->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_salah_kembali(): void
    {
        $res = $this->post('/login', ['username' => 'admin', 'password' => 'salah']);

        $res->assertSessionHasErrors('username');
        $this->assertGuest();
    }
}
