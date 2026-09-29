<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengaturanRequest;
use App\Models\PengaturanSistem;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function index(): View
    {
        return view('admin.pengaturan.index', [
            'settings' => PengaturanSistem::pluck('value_setting', 'key_setting')->all(),
        ]);
    }

    public function update(PengaturanRequest $request): RedirectResponse
    {
        foreach ($request->validated() as $key => $value) {
            PengaturanSistem::where('key_setting', $key)->update(['value_setting' => $value]);
        }

        return redirect('/admin/pengaturan')->with('success', 'Pengaturan disimpan.');
    }
}
