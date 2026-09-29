<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event', function (Blueprint $table) {
            $table->id();
            $table->string('nama_event', 100);
            $table->date('tanggal');
            $table->string('lokasi', 100)->nullable();
            $table->enum('status', ['draft', 'berlangsung', 'selesai'])->default('draft');
            $table->boolean('is_publik')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['status', 'is_publik', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};
