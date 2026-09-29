<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained('event')->nullOnDelete();
            $table->foreignId('atlet_id')->constrained('atlet')->cascadeOnDelete();
            $table->date('tanggal_sesi');
            $table->integer('total_skor')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['event_id', 'atlet_id', 'tanggal_sesi', 'created_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi');
    }
};
