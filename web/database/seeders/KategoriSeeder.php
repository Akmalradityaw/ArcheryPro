<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['Recurve', 30, 6],
            ['Compound', 50, 6],
            ['Traditional', 20, 6],
            ['Barebow', 30, 6],
        ];

        foreach ($defaults as [$nama, $jarak, $panah]) {
            Kategori::firstOrCreate(
                ['nama_kategori' => $nama],
                ['jarak_tempuh' => $jarak, 'jumlah_panah_per_end' => $panah]
            );
        }
    }
}
