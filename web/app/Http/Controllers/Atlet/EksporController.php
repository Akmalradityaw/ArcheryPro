<?php

namespace App\Http\Controllers\Atlet;

use App\Http\Controllers\Controller;
use App\Services\EksporService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EksporController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (! auth()->user()->atlet) {
            return redirect()->route('atlet.profil.edit')->with('warning', 'Lengkapi profil dulu sebelum mengekspor.');
        }

        return view('atlet.ekspor');
    }

    public function pdf(EksporService $ekspor)
    {
        return $ekspor->riwayatAtletPdf(auth()->user()->atlet);
    }

    public function excel(EksporService $ekspor)
    {
        return $ekspor->riwayatAtletExcel(auth()->user()->atlet);
    }

    public function word(EksporService $ekspor)
    {
        return $ekspor->riwayatAtletWord(auth()->user()->atlet);
    }
}
