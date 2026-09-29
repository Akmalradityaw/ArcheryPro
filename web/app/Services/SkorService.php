<?php

namespace App\Services;

use App\Models\LogSkor;
use App\Models\Sesi;
use Illuminate\Support\Facades\DB;

class SkorService
{
    /**
     * Menyimpan data skor latihan multi-atlet beserta end dan log perubahannya.
     *
     * @param  array  $data Data yang telah divalidasi dari StoreSkorRequest
     * @param  int    $oleh ID user yang menginput (pelatih atau petugas scoring)
     * @return array  Array ID sesi yang diperbarui/dibuat
     */
    public function simpanMultiAtlet(array $data, int $oleh): array
    {
        return DB::transaction(function () use ($data, $oleh) {
            $sesiLatihanId = $data['sesi_latihan_id'] ?? null;
            $eventId = $data['event_id'] ?? null;
            $savedSesiIds = [];

            foreach ($data['atlet_ids'] as $atletId) {
                if (empty($data['skor'][$atletId])) {
                    continue;
                }

                // Cari sesi yang sudah ada atau buat sesi baru untuk atlet di sesi latihan ini
                if ($sesiLatihanId) {
                    $sesi = Sesi::withTrashed()
                        ->where('sesi_latihan_id', $sesiLatihanId)
                        ->where('atlet_id', $atletId)
                        ->first();

                    if ($sesi) {
                        if ($sesi->trashed()) {
                            $sesi->restore();
                        }
                    } else {
                        $sesi = Sesi::create([
                            'sesi_latihan_id' => $sesiLatihanId,
                            'atlet_id' => $atletId,
                            'event_id' => $eventId,
                            'tanggal_sesi' => $data['tanggal_sesi'],
                            'jarak_meter' => $data['jarak_meter'] ?? null,
                            'catatan_pelatih' => $data['catatan_pelatih'] ?? null,
                            'created_by' => $oleh,
                        ]);
                    }
                } else {
                    $sesi = Sesi::withTrashed()
                        ->where('event_id', $eventId)
                        ->where('atlet_id', $atletId)
                        ->whereDate('tanggal_sesi', $data['tanggal_sesi'])
                        ->first();

                    if ($sesi) {
                        if ($sesi->trashed()) {
                            $sesi->restore();
                        }
                    } else {
                        $sesi = Sesi::create([
                            'event_id' => $eventId,
                            'atlet_id' => $atletId,
                            'tanggal_sesi' => $data['tanggal_sesi'],
                            'jarak_meter' => $data['jarak_meter'] ?? null,
                            'catatan_pelatih' => $data['catatan_pelatih'] ?? null,
                            'created_by' => $oleh,
                        ]);
                    }
                }

                // Perbarui catatan pelatih jika diisi pada formulir ini
                if (!empty($data['catatan_pelatih'])) {
                    $sesi->update(['catatan_pelatih' => $data['catatan_pelatih']]);
                }

                // Simpan end dan nilai panah
                $endKe = (int) $sesi->skor()->max('end_ke');
                foreach ($data['skor'][$atletId] as $panah) {
                    $endKe++;
                    $skor = $sesi->skor()->create([
                        'end_ke' => $endKe,
                        'skor1' => $panah[0], 'skor2' => $panah[1], 'skor3' => $panah[2],
                        'skor4' => $panah[3], 'skor5' => $panah[4], 'skor6' => $panah[5],
                        'total_end' => array_sum($panah),
                    ]);

                    LogSkor::create([
                        'skor_id' => $skor->id,
                        'user_id' => $oleh,
                        'perubahan' => 'Skor latihan diinput',
                        'data_baru' => $skor->only(['end_ke', 'skor1', 'skor2', 'skor3', 'skor4', 'skor5', 'skor6', 'total_end']),
                    ]);
                }

                $sesi->update(['total_skor' => $sesi->skor()->sum('total_end')]);
                $savedSesiIds[] = $sesi->id;
            }

            return $savedSesiIds;
        });
    }
}
