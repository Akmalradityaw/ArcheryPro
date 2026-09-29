<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ponytail: dikembalikan karena fitur "Ingat saya" dipakai form login —
            // dibuang di TAHAP 2 tanpa sadar flag remember masih dikirim
            $table->rememberToken();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('remember_token');
        });
    }
};
