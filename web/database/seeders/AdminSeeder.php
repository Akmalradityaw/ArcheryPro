<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            ['password' => 'admin123', 'role' => 'admin']
        );
        $admin->assignRole('admin');
    }
}
