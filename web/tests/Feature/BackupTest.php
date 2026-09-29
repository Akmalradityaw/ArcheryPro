<?php

namespace Tests\Feature;

use App\Models\BackupLog;
use App\Models\Sekolah;
use App\Models\User;
use Database\Seeders\PengaturanSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, PengaturanSeeder::class]);
        $admin = User::create(['username' => 'admin', 'password' => 'admin123', 'role' => 'admin']);
        $admin->assignRole('admin');
        $this->actingAs($admin);
    }

    public function test_run_backup_json(): void
    {
        $res = $this->post('/admin/backup/run', ['format' => 'json']);

        $res->assertRedirect('/admin/backup');
        $log = BackupLog::latest()->first();
        $this->assertSame('json', $log->format);
        Storage::disk('local')->assertExists($log->lokasi_file);
    }

    public function test_restore_json_roundtrip(): void
    {
        $this->post('/admin/backup/run', ['format' => 'json']);
        Sekolah::create(['nama_sekolah' => 'SEMENTARA']);
        $this->post('/admin/backup/run', ['format' => 'json']);

        $path = BackupLog::latest()->first()->lokasi_file;
        Sekolah::where('nama_sekolah', 'SEMENTARA')->delete();
        $this->assertDatabaseMissing('sekolah', ['nama_sekolah' => 'SEMENTARA']);

        $res = $this->post('/admin/backup/restore', [
            'file' => new UploadedFile(Storage::disk('local')->path($path), 'b.json', 'application/json', null, true),
        ]);

        $res->assertRedirect('/admin/backup');
        $this->assertDatabaseHas('sekolah', ['nama_sekolah' => 'SEMENTARA']);
    }
}
