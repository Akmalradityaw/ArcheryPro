<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_sistem', function (Blueprint $table) {
            $table->id();
            $table->string('key_setting', 50)->unique();
            $table->text('value_setting');
            $table->text('keterangan')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_sistem');
    }
};
