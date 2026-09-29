<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atlet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nia', 20)->unique();
            $table->string('nama_lengkap', 100);
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->foreignId('sekolah_id')->nullable()->constrained('sekolah')->nullOnDelete();
            $table->string('kelas', 20)->nullable();
            $table->string('no_telepon', 15)->nullable();
            $table->string('nama_orangtua', 100)->nullable();
            $table->string('no_telepon_orangtua', 15)->nullable();
            $table->text('alamat')->nullable();
            $table->foreignId('kategori_id')->nullable()->constrained('kategori')->nullOnDelete();
            $table->string('golongan_darah', 5)->nullable();
            $table->text('riwayat_cedera')->nullable();
            $table->text('alergi')->nullable();
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->timestamps();
            $table->index(['sekolah_id', 'kategori_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atlet');
    }
};
