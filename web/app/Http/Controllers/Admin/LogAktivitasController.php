<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogAktivitasController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.log-aktivitas.index', [
            'logs' => $this->filtered($request)->with('user')->latest('waktu')->paginate(10),
            'q' => $request->q,
        ]);
    }

    // ponytail: CSV stdlib sekarang; ekspor Excel/PDF ikut EksporService TAHAP 10
    public function export(Request $request): StreamedResponse
    {
        $rows = $this->filtered($request)->with('user')->latest('waktu')->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Waktu', 'User', 'Aktivitas', 'IP']);
            foreach ($rows as $log) {
                fputcsv($out, [$log->waktu, $log->user->username ?? '-', $log->aktivitas, $log->ip_address]);
            }
            fclose($out);
        }, 'log-aktivitas-'.now()->format('Ymd-His').'.csv');
    }

    private function filtered(Request $request)
    {
        return LogAktivitas::when($request->filled('q'), function ($query) use ($request) {
            $query->where('aktivitas', 'like', "%{$request->q}%")
                ->orWhereHas('user', fn ($q) => $q->where('username', 'like', "%{$request->q}%"));
        });
    }
}
