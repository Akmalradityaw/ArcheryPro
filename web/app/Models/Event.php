<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $table = 'event';

    protected $fillable = [
        'nama_event', 'tanggal', 'lokasi', 'status', 'is_publik', 'created_by',
    ];

    protected $casts = ['tanggal' => 'date', 'is_publik' => 'boolean'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sesi(): HasMany
    {
        return $this->hasMany(Sesi::class);
    }
}
