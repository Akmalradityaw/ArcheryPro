<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ponytail: role saja, tanpa permission — spec tidak mendefinisikan
        // permission apa pun; RoleMiddleware cek role. Tambah permission saat ada kebutuhan nyata.
        foreach (['admin', 'pelatih', 'scoring', 'atlet'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }
}
