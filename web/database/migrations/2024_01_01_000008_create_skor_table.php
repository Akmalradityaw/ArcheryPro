<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained('sesi')->cascadeOnDelete();
            $table->integer('end_ke');
            $table->tinyInteger('skor1');
            $table->tinyInteger('skor2');
            $table->tinyInteger('skor3');
            $table->tinyInteger('skor4');
            $table->tinyInteger('skor5');
            $table->tinyInteger('skor6');
            $table->smallInteger('total_end');
            $table->timestamps();
            $table->index(['sesi_id', 'end_ke']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skor');
    }
};
