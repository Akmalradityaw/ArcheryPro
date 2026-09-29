<?php

namespace App\Services;

use App\Exports\ArrayExport;
use App\Models\BackupLog;
use App\Models\PengaturanSistem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\PhpWord;

// ponytail: service sendiri (bukan spatie/laravel-backup) — 5 format butuh satu tempat,
// tanpa mysqldump binary, restore JSON tanpa dump tool. Package spatie tetap terinstall
// tapi tak di-wire; lepas via composer remove bila yakin tak butuh backup file-level.
class BackupService
{
    public const TABLES = [
        'users', 'sekolah', 'kategori', 'atlet', 'event', 'sesi',
        'skor', 'log_skor', 'backup_log', 'log_aktivitas', 'pengaturan_sistem',
    ];

    public function __construct(private EksporService $ekspor) {}

    public function run(string $format, ?int $oleh = null): BackupLog
    {
        Storage::makeDirectory($this->dir($format));
        $path = match ($format) {
            'pdf' => $this->toPdf(),
            'docx' => $this->toDocx(),
            'xlsx' => $this->toXlsx(),
            'txt' => $this->toTxt(),
            default => $this->toJson(),
        };

        return BackupLog::create([
            'user_id' => $oleh,
            'jenis_backup' => 'full',
            'format' => $format,
            'ukuran_file' => round(Storage::size($path) / 1024, 1).' KB',
            'lokasi_file' => $path,
        ]);
    }

    public function dumpAll(): array
    {
        $data = [];
        foreach (self::TABLES as $table) {
            $data[$table] = DB::table($table)->get();
        }

        return $data;
    }

    public function toJson(): string
    {
        return $this->put('json', 'backup-'.$this->stamp().'.json', json_encode($this->dumpAll(), JSON_PRETTY_PRINT));
    }

    public function toTxt(): string
    {
        $lines = ['BACKUP ARCHERYPRO '.$this->stamp(), str_repeat('=', 40)];
        foreach ($this->dumpAll() as $table => $rows) {
            $lines[] = sprintf('%-20s %d baris', $table, count($rows));
        }

        return $this->put('txt', 'backup-'.$this->stamp().'.txt', implode(PHP_EOL, $lines).PHP_EOL);
    }

    public function toXlsx(): string
    {
        $nama = 'laporan-'.$this->stamp().'.xlsx';
        Excel::store(new ArrayExport($this->ekspor->headingsLaporan(), $this->ekspor->rowsLaporan()), $this->dir('xlsx').'/'.$nama);

        return $this->dir('xlsx').'/'.$nama;
    }

    public function toPdf(): string
    {
        $rel = $this->dir('pdf').'/laporan-'.$this->stamp().'.pdf';
        Pdf::loadView('exports.riwayat-pdf', [
            'judul' => 'Backup Laporan Performa — '.$this->stamp(),
            'headings' => $this->ekspor->headingsLaporan(),
            'rows' => $this->ekspor->rowsLaporan(),
        ])->save(storage_path('app/'.$rel));

        return $rel;
    }

    public function toDocx(): string
    {
        $rel = $this->dir('docx').'/laporan-'.$this->stamp().'.docx';
        $doc = new PhpWord;
        $section = $doc->addSection();
        $section->addText('Backup Laporan Performa — '.$this->stamp(), ['bold' => true, 'size' => 14]);
        $table = $section->addTable(['borderSize' => 6]);
        $table->addRow();
        foreach ($this->ekspor->headingsLaporan() as $h) {
            $table->addCell()->addText($h, ['bold' => true]);
        }
        foreach ($this->ekspor->rowsLaporan() as $row) {
            $table->addRow();
            foreach ($row as $cell) {
                $table->addCell()->addText((string) $cell);
            }
        }
        $doc->save(storage_path('app/'.$rel), 'Word2007');

        return $rel;
    }

    private function stamp(): string
    {
        return now()->format('Ymd-His');
    }

    private function base(): string
    {
        $p = PengaturanSistem::where('key_setting', 'backup_path')->value('value_setting') ?? 'storage/app/backups/';

        return trim(str_replace('storage/app/', '', $p), '/');
    }

    private function dir(string $format): string
    {
        return $this->base().'/'.$format;
    }

    private function put(string $format, string $nama, string $isi): string
    {
        $rel = $this->dir($format).'/'.$nama;
        Storage::put($rel, $isi);

        return $rel;
    }
}
