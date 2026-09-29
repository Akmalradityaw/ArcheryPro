<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackupRunRequest;
use App\Http\Requests\RestoreRequest;
use App\Models\BackupLog;
use App\Services\BackupService;
use App\Support\DataTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function index(): View
    {
        return view('admin.backup.index', [
            'riwayat' => DataTable::paginate(
                BackupLog::with('user'),
                ['jenis_backup', 'format'],
                ['waktu' => 'created_at', 'format' => 'format']
            ),
        ]);
    }

    public function run(BackupRunRequest $request, BackupService $backup): RedirectResponse
    {
        $format = $request->validated()['format'];
        $log = $backup->run($format, auth()->id());

        return redirect('/admin/backup')->with('success', "Backup {$format} selesai: ".basename($log->lokasi_file));
    }

    public function download(BackupLog $backup): StreamedResponse
    {
        abort_if(! $backup->lokasi_file || ! Storage::exists($backup->lokasi_file), 404);

        return Storage::download($backup->lokasi_file);
    }

    public function restore(RestoreRequest $request): RedirectResponse
    {
        $data = json_decode($request->file('file')->getContent(), true);
        abort_if(! is_array($data), 422, 'File backup tidak valid.');

        DB::transaction(function () use ($data) {
            Schema::disableForeignKeyConstraints();
            foreach (array_reverse(BackupService::TABLES) as $table) {
                // ponytail: delete() bukan truncate() — TRUNCATE = DDL = implicit commit
                // di MySQL sehingga merusak transaksi Laravel ("no active transaction")
                DB::table($table)->delete();
            }
            foreach (BackupService::TABLES as $table) {
                if (! empty($data[$table])) {
                    DB::table($table)->insert($data[$table]);
                }
            }
            Schema::enableForeignKeyConstraints();
        });

        return redirect('/admin/backup')->with('success', 'Restore selesai. Silakan login ulang bila sesi berakhir.');
    }

    public function riwayat(): RedirectResponse
    {
        return redirect('/admin/backup');
    }
}
