<?php

namespace App\Http\Controllers\Atlet;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfilRequest;
use App\Models\Atlet;
use App\Models\Kategori;
use App\Models\Sekolah;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function edit(): View
    {
        return view('atlet.profil', [
            'atlet' => auth()->user()->atlet ?? new Atlet,
            'sekolah' => Sekolah::orderBy('nama_sekolah')->get(),
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function update(ProfilRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['izinkan_tampil_publik'] = $request->boolean('izinkan_tampil_publik');
        $data['status'] = 'aktif';

        Atlet::updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        return redirect()->route('atlet.profil.edit')->with('success', 'Profil dan pengaturan privasi berhasil disimpan.');
    }
}
