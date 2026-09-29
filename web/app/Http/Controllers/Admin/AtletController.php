<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AtletRequest;
use App\Models\Atlet;
use App\Models\Kategori;
use App\Models\Sekolah;
use App\Models\User;
use App\Support\DataTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AtletController extends Controller
{
    public function index(): View
    {
        return view('admin.atlet.index', [
            'atlets' => DataTable::paginate(
                Atlet::with(['user', 'sekolah', 'kategori']),
                ['nama_lengkap', 'nia'],
                ['nama' => 'nama_lengkap', 'nia' => 'nia', 'status' => 'status', 'dibuat' => 'created_at']
            ),
        ]);
    }

    public function create(): View
    {
        return view('admin.atlet.create', [
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

        return redirect('/admin/atlet')->with('success', 'Atlet dibuat.');
    }

    public function show(Atlet $atlet): View
    {
        $atlet->load(['user', 'sekolah', 'kategori']);

        return view('admin.atlet.show', [
            'atlet' => $atlet,
            'sesiTerbaru' => $atlet->sesi()->latest('tanggal_sesi')->take(5)->get(),
        ]);
    }

    public function edit(Atlet $atlet): View
    {
        return view('admin.atlet.edit', [
            'atlet' => $atlet->load('user'),
            'sekolah' => Sekolah::orderBy('nama_sekolah')->get(),
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function update(AtletRequest $request, Atlet $atlet): RedirectResponse
    {
        DB::transaction(function () use ($request, $atlet) {
            $userData = ['username' => $request->username];
            if ($request->filled('password')) {
                $userData['password'] = $request->password;
            }
            $atlet->user->update($userData);
            $atlet->update($request->except(['username', 'password']));
        });

        return redirect('/admin/atlet')->with('success', 'Atlet diperbarui.');
    }

    public function destroy(Atlet $atlet): RedirectResponse
    {
        $atlet->user->delete();

        return redirect('/admin/atlet')->with('success', 'Atlet dihapus.');
    }

    public function verifikasi(Atlet $atlet): RedirectResponse
    {
        $atlet->update(['status' => $atlet->status === 'aktif' ? 'tidak_aktif' : 'aktif']);

        return redirect('/admin/atlet')->with('success', "Status {$atlet->nama_lengkap}: {$atlet->status}.");
    }
}
