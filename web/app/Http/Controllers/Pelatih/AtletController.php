<?php

namespace App\Http\Controllers\Pelatih;

use App\Http\Controllers\Controller;
use App\Http\Requests\AtletRequest;
use App\Models\Atlet;
use App\Models\Kategori;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

// ponytail: logika mirip Admin\AtletController by design (spec: "mirip admin") —
// bedanya pelatih tak menyentuh kredensial/role (form tanpa username/password, tanpa modul Users).
// Duplikasi ~60 baris diterima; abstraksi bersama baru dibuat jika keduanya berubah bersamaan 2x.
class AtletController extends Controller
{
    public function index(): View
    {
        return view('pelatih.atlet.index', [
            'atlets' => Atlet::with(['sekolah', 'kategori'])->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('pelatih.atlet.create', [
            'sekolah' => Sekolah::orderBy('nama_sekolah')->get(),
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function store(AtletRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'username' => $request->username,
                'password' => $request->password,
                'role' => 'atlet',
            ]);
            $user->assignRole('atlet');
            $user->atlet()->create($request->except(['username', 'password']));
        });

        return redirect()->route('pelatih.atlet.index')->with('success', 'Atlet dibuat.');
    }

    public function show(Atlet $atlet): View
    {
        $atlet->load(['sekolah', 'kategori']);

        return view('pelatih.atlet.show', [
            'atlet' => $atlet,
            'ringkasan' => [
                'sesi' => $atlet->sesi()->count(),
                'rata' => round($atlet->sesi()->avg('total_skor') ?? 0, 1),
                'terbaik' => $atlet->sesi()->max('total_skor') ?? 0,
            ],
        ]);
    }

    public function edit(Atlet $atlet): View
    {
        return view('pelatih.atlet.edit', [
            'atlet' => $atlet,
            'sekolah' => Sekolah::orderBy('nama_sekolah')->get(),
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function update(AtletRequest $request, Atlet $atlet): RedirectResponse
    {
        // pelatih hanya ubah data profil, bukan kredensial user
        $atlet->update($request->except(['username', 'password']));

        return redirect()->route('pelatih.atlet.index')->with('success', 'Atlet diperbarui.');
    }

    public function destroy(Atlet $atlet): RedirectResponse
    {
        $atlet->user->delete();

        return redirect()->route('pelatih.atlet.index')->with('success', 'Atlet dihapus.');
    }
}
