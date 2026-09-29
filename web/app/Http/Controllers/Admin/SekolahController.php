<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SekolahRequest;
use App\Models\Sekolah;
use App\Support\DataTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SekolahController extends Controller
{
    public function index(): View
    {
        return view('admin.sekolah.index', [
            'sekolah' => DataTable::paginate(
                Sekolah::withCount('atlet'),
                ['nama_sekolah', 'kota'],
                ['nama' => 'nama_sekolah', 'kota' => 'kota', 'atlet' => 'atlet_count']
            ),
        ]);
    }

    public function create(): View
    {
        return view('admin.sekolah.create');
    }

    public function store(SekolahRequest $request): RedirectResponse
    {
        Sekolah::create($request->validated());

        return redirect('/admin/sekolah')->with('success', 'Sekolah dibuat.');
    }

    public function edit(Sekolah $sekolah): View
    {
        return view('admin.sekolah.edit', compact('sekolah'));
    }

    public function update(SekolahRequest $request, Sekolah $sekolah): RedirectResponse
    {
        $sekolah->update($request->validated());

        return redirect('/admin/sekolah')->with('success', 'Sekolah diperbarui.');
    }

    public function destroy(Sekolah $sekolah): RedirectResponse
    {
        $sekolah->delete();

        return redirect('/admin/sekolah')->with('success', 'Sekolah dihapus.');
    }
}
