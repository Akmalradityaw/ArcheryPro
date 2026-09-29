<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSkorRequest;
use App\Models\Atlet;
use App\Services\SkorService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkorController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected SkorService $skorService
    ) {}

    /**
     * Ambil daftar atlet aktif untuk form input skor.
     */
    public function listAtlet(Request $request): JsonResponse
    {
        $q = $request->query('q');

        $query = Atlet::with(['sekolah', 'kategori'])
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('nia', 'like', "%{$q}%");
            });
        }

        $atlets = $query->get()->map(function ($a) {
            return [
                'id' => $a->id,
                'nia' => $a->nia,
                'nama_lengkap' => $a->nama_lengkap,
                'nomor_target' => $a->nomor_target,
                'sekolah' => $a->sekolah?->nama_sekolah ?? '-',
                'kategori' => $a->kategori?->nama_kategori ?? '-',
                'jarak_meter' => $a->kategori?->jarak_tempuh ?? 30,
            ];
        });

        return $this->ok($atlets, 'Daftar atlet aktif.');
    }

    /**
     * Simpan data skor latihan multi-atlet via REST API.
     */
    public function store(StoreSkorRequest $request): JsonResponse
    {
        $data = $request->validated();
        $savedSesiIds = $this->skorService->simpanMultiAtlet($data, auth()->id());

        return $this->ok([
            'sesi_latihan_id' => $data['sesi_latihan_id'] ?? null,
            'total_atlet_tersimpan' => count($savedSesiIds),
            'sesi_ids' => $savedSesiIds,
        ], 'Skor latihan mingguan berhasil disimpan.', 201);
    }
}
