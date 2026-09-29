<?php

namespace App\Http\Controllers\Pelatih;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

// ponytail: index/show/edit/update saja (tanpa create/delete/status) — show ditambah atas permintaan pelatih
class EventController extends Controller
{
    public function index(): View
    {
        return view('pelatih.event.index', [
            'events' => Event::latest('tanggal')->paginate(10),
        ]);
    }

    public function show(Event $event): View
    {
        // ponytail: satu query + olah koleksi — cukup untuk satu event; paginasi hanya jika daftar sesi membesar
        $sesi = $event->sesi()->with('atlet')->latest('tanggal_sesi')->get();

        return view('pelatih.event.show', [
            'event' => $event,
            'ringkasan' => [
                'sesi' => $sesi->count(),
                'terbaik' => $sesi->max('total_skor') ?? 0,
                'rata' => round($sesi->avg('total_skor') ?? 0, 1),
            ],
            'sesiTerbaru' => $sesi->take(5),
        ]);
    }

    public function edit(Event $event): View
    {
        return view('pelatih.event.edit', compact('event'));
    }

    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $data = $request->validated();
        $data['is_publik'] = $request->boolean('is_publik');
        $event->update($data);

        return redirect()->route('pelatih.event.index')->with('success', 'Event diperbarui.');
    }
}
