<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('atlet', function (Blueprint $table) {
            $table->boolean('izinkan_tampil_publik')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('atlet', function (Blueprint $table) {
            $table->dropColumn('izinkan_tampil_publik');
        });
    }
};
