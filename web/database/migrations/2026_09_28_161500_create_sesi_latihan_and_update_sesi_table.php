<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel sesi_latihan baru jika belum ada
        if (!Schema::hasTable('sesi_latihan')) {
            Schema::create('sesi_latihan', function (Blueprint $table) {
                $table->id();
                $table->string('nama_sesi', 100);
                $table->date('tanggal');
                $table->time('jam_mulai')->nullable();
                $table->time('jam_selesai')->nullable();
                $table->string('lokasi', 100)->nullable();
                $table->enum('jenis_latihan', ['rutin_mingguan', 'mandiri', 'evaluasi_skor', 'simulasi'])->default('rutin_mingguan');
                $table->enum('status', ['terjadwal', 'berlangsung', 'selesai', 'batal'])->default('terjadwal');
                $table->text('fokus_latihan')->nullable();
                $table->tinyInteger('minggu_ke')->nullable();
                $table->year('tahun')->nullable();
                $table->boolean('is_publik')->default(false);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->softDeletes();
                $table->timestamps();

                $table->index(['tanggal', 'jenis_latihan', 'status']);
                $table->index(['tahun', 'minggu_ke']);
            });
        }

        // 2. Bridging Data: Salin data dari tabel 'event' lama ke 'sesi_latihan'
        if (Schema::hasTable('event')) {
            $events = DB::table('event')->get();
            foreach ($events as $event) {
                $carbon = $event->tanggal ? \Carbon\Carbon::parse($event->tanggal) : now();
                DB::table('sesi_latihan')->updateOrInsert(
                    ['id' => $event->id],
                    [
                        'nama_sesi' => $event->nama_event,
                        'tanggal' => $event->tanggal,
                        'lokasi' => $event->lokasi,
                        'status' => $event->status === 'draft' ? 'terjadwal' : $event->status,
                        'is_publik' => $event->is_publik,
                        'minggu_ke' => (int) $carbon->isoWeek(),
                        'tahun' => (int) $carbon->year,
                        'created_by' => $event->created_by,
                        'created_at' => $event->created_at,
                        'updated_at' => $event->updated_at,
                    ]
                );
            }
        }

        // 3. Update tabel 'sesi'
        if (Schema::hasTable('sesi')) {
            Schema::table('sesi', function (Blueprint $table) {
                if (!Schema::hasColumn('sesi', 'sesi_latihan_id')) {
                    $table->foreignId('sesi_latihan_id')->nullable()->after('event_id')->constrained('sesi_latihan')->cascadeOnDelete();
                }
                if (!Schema::hasColumn('sesi', 'jarak_meter')) {
                    $table->smallInteger('jarak_meter')->nullable()->after('total_skor');
                }
                if (!Schema::hasColumn('sesi', 'catatan_pelatih')) {
                    $table->text('catatan_pelatih')->nullable()->after('jarak_meter');
                }
                if (!Schema::hasColumn('sesi', 'catatan_dibaca_at')) {
                    $table->timestamp('catatan_dibaca_at')->nullable()->after('catatan_pelatih');
                }
                if (!Schema::hasColumn('sesi', 'deleted_at')) {
                    $table->softDeletes()->after('updated_at');
                }
            });

            // Hubungkan sesi lama ke sesi_latihan_id
            DB::table('sesi')
                ->whereNull('sesi_latihan_id')
                ->whereNotNull('event_id')
                ->update(['sesi_latihan_id' => DB::raw('event_id')]);

            // Pasang unique constraint jika belum ada
            Schema::table('sesi', function (Blueprint $table) {
                $table->unique(['sesi_latihan_id', 'atlet_id'], 'uniq_sesi_atlet');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sesi')) {
            Schema::table('sesi', function (Blueprint $table) {
                $table->dropUnique('uniq_sesi_atlet');
                $table->dropSoftDeletes();
                $table->dropColumn(['catatan_dibaca_at', 'catatan_pelatih', 'jarak_meter']);
                $table->dropConstrainedForeignId('sesi_latihan_id');
            });
        }

        Schema::dropIfExists('sesi_latihan');
    }
};
