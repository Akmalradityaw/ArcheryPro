<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sesi extends Model
{
    use SoftDeletes;

    protected $table = 'sesi';

    protected $fillable = [
        'event_id',
        'sesi_latihan_id',
        'atlet_id',
        'tanggal_sesi',
        'total_skor',
        'jarak_meter',
        'catatan_pelatih',
        'catatan_dibaca_at',
        'created_by',
    ];

    protected $casts = [
        'tanggal_sesi' => 'date',
        'catatan_dibaca_at' => 'datetime',
        'jarak_meter' => 'integer',
        'total_skor' => 'integer',
    ];

    public function sesiLatihan(): BelongsTo
    {
        return $this->belongsTo(SesiLatihan::class, 'sesi_latihan_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function atlet(): BelongsTo
    {
        return $this->belongsTo(Atlet::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function skor(): HasMany
    {
        return $this->hasMany(Skor::class);
    }

    public function isCatatanDibaca(): bool
    {
        return !is_null($this->catatan_dibaca_at);
    }

    public function tandaiCatatanDibaca(): void
    {
        $this->update(['catatan_dibaca_at' => now()]);
    }
}
