<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_skor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skor_id')->constrained('skor')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('perubahan');
            $table->json('data_lama')->nullable();
            $table->json('data_baru')->nullable();
            $table->timestamp('waktu')->useCurrent();
            $table->index(['skor_id', 'user_id', 'waktu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_skor');
    }
};
