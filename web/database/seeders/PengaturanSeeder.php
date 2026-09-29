<?php

namespace Database\Seeders;

use App\Models\PengaturanSistem;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['token_pelatih', 'PELATIH2025', 'Token registrasi pelatih'],
            ['token_scoring', 'SKORING2025', 'Token registrasi scoring'],
            ['jumlah_panah_per_end', '6', 'Jumlah anak panah per end'],
            ['backup_otomatis', 'harian', 'Jadwal backup: harian/mingguan/bulanan/off'],
            ['backup_path', 'storage/app/backups/', 'Lokasi penyimpanan backup'],
            ['leaderboard_publik', '1', 'Izinkan papan skor latihan diakses publik tanpa login'],
        ];

        foreach ($defaults as [$key, $value, $keterangan]) {
            PengaturanSistem::updateOrCreate(
                ['key_setting' => $key],
                ['value_setting' => $value, 'keterangan' => $keterangan]
            );
        }
    }
}
