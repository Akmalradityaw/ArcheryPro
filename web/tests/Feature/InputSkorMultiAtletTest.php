<?php

namespace Tests\Feature;

use App\Models\Atlet;
use App\Models\Event;
use App\Models\User;
use Database\Seeders\PengaturanSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InputSkorMultiAtletTest extends TestCase
{
    use RefreshDatabase;

    private Event $event;
    private Atlet $a1;
    private Atlet $a2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, PengaturanSeeder::class]);

        $skor = User::create(['username' => 'skor', 'password' => 'password123', 'role' => 'scoring']);
        $skor->assignRole('scoring');
        $this->actingAs($skor);

        $this->event = Event::create(['nama_event' => 'EV', 'tanggal' => '2026-09-21', 'status' => 'berlangsung']);
        $this->a1 = $this->buatAtlet('u1', 'N1', 'Satu');
        $this->a2 = $this->buatAtlet('u2', 'N2', 'Dua');
    }

    private function buatAtlet(string $username, string $nia, string $nama): Atlet
    {
        $u = User::create(['username' => $username, 'password' => 'password123', 'role' => 'atlet']);
        $u->assignRole('atlet');

        return $u->atlet()->create(['nia' => $nia, 'nama_lengkap' => $nama, 'status' => 'aktif']);
    }

    private function payload(array $skor1, array $skor2): array
    {
        return [
            'event_id' => $this->event->id,
            'tanggal_sesi' => '2026-09-21',
            'atlet_ids' => [$this->a1->id, $this->a2->id],
            'skor' => [$this->a1->id => [$skor1], $this->a2->id => [$skor2]],
        ];
    }

    public function test_simpan_multi_atlet_dua_end(): void
    {
        $res = $this->post('/scoring/input/selesai', $this->payload(
            [8, 9, 7, 10, 6, 8], [5, 5, 5, 5, 5, 5]
        ));

        $res->assertRedirect('/scoring/riwayat');
        $this->assertDatabaseCount('sesi', 2);
        $this->assertDatabaseHas('sesi', ['atlet_id' => $this->a1->id, 'total_skor' => 48]);
        $this->assertDatabaseHas('sesi', ['atlet_id' => $this->a2->id, 'total_skor' => 30]);
        $this->assertDatabaseCount('log_skor', 2);
    }

    public function test_skor_11_ditolak(): void
    {
        $res = $this->post('/scoring/input/selesai', $this->payload(
            [11, 0, 0, 0, 0, 0], [5, 5, 5, 5, 5, 5]
        ));

        $res->assertSessionHasErrors();
        $this->assertDatabaseCount('sesi', 0);
    }

    public function test_event_draft_ditolak(): void
    {
        $this->event->update(['status' => 'draft']);
        $res = $this->post('/scoring/input/selesai', $this->payload(
            [5, 5, 5, 5, 5, 5], [5, 5, 5, 5, 5, 5]
        ));

        $res->assertSessionHasErrors('event_id');
        $this->assertDatabaseCount('sesi', 0);
    }
}
