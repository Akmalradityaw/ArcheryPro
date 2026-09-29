<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\KategoriRequest;
use App\Models\Kategori;
use App\Support\DataTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(): View
    {
        return view('admin.kategori.index', [
            'kategori' => DataTable::paginate(
                Kategori::withCount('atlet'),
                ['nama_kategori'],
                ['nama' => 'nama_kategori', 'jarak' => 'jarak_tempuh', 'atlet' => 'atlet_count']
            ),
        ]);
    }

    public function create(): View
    {
        return view('admin.kategori.create');
    }

    public function store(KategoriRequest $request): RedirectResponse
    {
        Kategori::create($request->validated());

        return redirect('/admin/kategori')->with('success', 'Kategori dibuat.');
    }

    public function edit(Kategori $kategori): View
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(KategoriRequest $request, Kategori $kategori): RedirectResponse
    {
        $kategori->update($request->validated());

        return redirect('/admin/kategori')->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        $kategori->delete();

        return redirect('/admin/kategori')->with('success', 'Kategori dihapus.');
    }
}
