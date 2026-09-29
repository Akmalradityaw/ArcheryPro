<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skor extends Model
{
    protected $table = 'skor';

    protected $fillable = [
        'sesi_id', 'end_ke',
        'skor1', 'skor2', 'skor3', 'skor4', 'skor5', 'skor6',
        'total_end',
    ];

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(Sesi::class);
    }

    public function logSkor(): HasMany
    {
        return $this->hasMany(LogSkor::class);
    }
}
