<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Http\Requests\SetStatusRequest;
use App\Models\Event;
use App\Support\DataTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        return view('admin.event.index', [
            'events' => DataTable::paginate(
                Event::query(),
                ['nama_event', 'lokasi'],
                ['nama' => 'nama_event', 'tanggal' => 'tanggal', 'status' => 'status'],
                ['tanggal', 'desc']
            ),
        ]);
    }

    public function create(): View
    {
        return view('admin.event.create');
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $data = $request->validated() + ['created_by' => auth()->id()];
        $data['is_publik'] = $request->boolean('is_publik');
        Event::create($data);

        return redirect('/admin/event')->with('success', 'Event dibuat.');
    }

    public function edit(Event $event): View
    {
        return view('admin.event.edit', compact('event'));
    }

    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $data = $request->validated();
        $data['is_publik'] = $request->boolean('is_publik');
        $event->update($data);

        return redirect('/admin/event')->with('success', 'Event diperbarui.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect('/admin/event')->with('success', 'Event dihapus.');
    }

    public function setStatus(SetStatusRequest $request, Event $event): RedirectResponse
    {
        $event->update($request->validated());

        return redirect('/admin/event')->with('success', "Status event: {$event->status}.");
    }
}
