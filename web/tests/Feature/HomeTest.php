<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_menampilkan_event_publik(): void
    {
        Event::create(['nama_event' => 'PUBLIK', 'tanggal' => '2026-09-01', 'status' => 'selesai', 'is_publik' => true]);
        Event::create(['nama_event' => 'DRAF', 'tanggal' => '2026-09-01', 'status' => 'draft', 'is_publik' => false]);

        $res = $this->get('/');

        $res->assertOk()->assertSee('PUBLIK')->assertDontSee('DRAF');
    }

    public function test_detail_event_draft_404(): void
    {
        $event = Event::create(['nama_event' => 'DRAF', 'tanggal' => '2026-09-01', 'status' => 'draft', 'is_publik' => false]);

        $this->get("/events/{$event->id}")->assertNotFound();
    }
}
