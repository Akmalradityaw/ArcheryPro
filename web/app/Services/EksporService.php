<?php

namespace App\Services;

use App\Exports\ArrayExport;
use App\Models\Atlet;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EksporService
{
    public function rowsRiwayat(Atlet $atlet): array
    {
        return $atlet->sesi()->with(['sesiLatihan', 'event'])->latest('tanggal_sesi')->get()
            ->map(fn ($s) => [
                $s->tanggal_sesi ? $s->tanggal_sesi->format('d/m/Y') : '-',
                $s->sesiLatihan->nama_sesi ?? $s->event->nama_event ?? 'Latihan Rutin',
                $s->jarak_meter ?? 18,
                $s->total_skor,
                $s->catatan_pelatih ?? '-',
            ])->all();
    }

    public function headingsRiwayat(): array
    {
        return ['Tanggal', 'Sesi Latihan', 'Jarak (m)', 'Total Skor', 'Catatan Pelatih'];
    }

    public function riwayatAtletPdf(Atlet $atlet)
    {
        return Pdf::loadView('exports.riwayat-pdf', [
            'judul' => "Riwayat Skor — {$atlet->nama_lengkap}",
            'headings' => $this->headingsRiwayat(),
            'rows' => $this->rowsRiwayat($atlet),
        ])->download("riwayat-{$atlet->nia}.pdf");
    }

    public function riwayatAtletExcel(Atlet $atlet)
    {
        return Excel::download(
            new ArrayExport($this->headingsRiwayat(), $this->rowsRiwayat($atlet)),
            "riwayat-{$atlet->nia}.xlsx"
        );
    }

    public function riwayatAtletWord(Atlet $atlet): BinaryFileResponse
    {
        $doc = new PhpWord;
        $section = $doc->addSection();
        $section->addText("Riwayat Skor — {$atlet->nama_lengkap}", ['bold' => true, 'size' => 14]);

        $table = $section->addTable(['borderSize' => 6]);
        $table->addRow();
        foreach ($this->headingsRiwayat() as $h) {
            $table->addCell()->addText($h, ['bold' => true]);
        }
        foreach ($this->rowsRiwayat($atlet) as $row) {
            $table->addRow();
            foreach ($row as $cell) {
                $table->addCell()->addText((string) $cell);
            }
        }

        $path = storage_path("app/riwayat-{$atlet->nia}.docx");
        $doc->save($path, 'Word2007');

        return response()->download($path)->deleteFileAfterSend();
    }

    public function headingsLaporan(): array
    {
        return ['NIA', 'Nama Lengkap', 'Sekolah', 'Kategori', 'Jumlah Sesi', 'Rata-rata Skor', 'Skor Terbaik', 'Total Skor'];
    }

    /**
     * Menggunakan chunking database untuk mencegah memory exhaustion (Kendala 4).
     */
    public function rowsLaporan(?int $sesiLatihanId = null, ?int $eventId = null): array
    {
        $rows = [];

        Atlet::with([
            'sekolah',
            'kategori',
            'sesi' => function ($q) use ($sesiLatihanId, $eventId) {
                if ($sesiLatihanId) {
                    $q->where('sesi_latihan_id', $sesiLatihanId);
                }
                if ($eventId) {
                    $q->where('event_id', $eventId);
                }
            },
        ])
        ->orderBy('nama_lengkap')
        ->chunk(50, function ($atlets) use (&$rows) {
            foreach ($atlets as $a) {
                $sesiCount = $a->sesi->count();
                $avgScore = $sesiCount > 0 ? round($a->sesi->avg('total_skor'), 1) : 0;
                $maxScore = $sesiCount > 0 ? $a->sesi->max('total_skor') : 0;
                $sumScore = $sesiCount > 0 ? $a->sesi->sum('total_skor') : 0;

                $rows[] = [
                    $a->nia,
                    $a->nama_lengkap,
                    $a->sekolah->nama_sekolah ?? '-',
                    $a->kategori->nama_kategori ?? '-',
                    $sesiCount,
                    $avgScore,
                    $maxScore,
                    $sumScore,
                ];
            }
        });

        return $rows;
    }

    public function laporanExcel(?int $sesiLatihanId = null, ?int $eventId = null)
    {
        return Excel::download(
            new ArrayExport($this->headingsLaporan(), $this->rowsLaporan($sesiLatihanId, $eventId)),
            'laporan-performa.xlsx'
        );
    }

    public function laporanPdf(?int $sesiLatihanId = null, ?int $eventId = null)
    {
        return Pdf::loadView('exports.riwayat-pdf', [
            'judul' => 'Laporan Performa Latihan Atlet',
            'headings' => $this->headingsLaporan(),
            'rows' => $this->rowsLaporan($sesiLatihanId, $eventId),
        ])->download('laporan-performa.pdf');
    }
}
