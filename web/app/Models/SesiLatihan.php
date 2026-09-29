<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SesiLatihan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sesi_latihan';

    protected $fillable = [
        'nama_sesi',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'lokasi',
        'jenis_latihan',
        'status',
        'fokus_latihan',
        'minggu_ke',
        'tahun',
        'is_publik',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_publik' => 'boolean',
        'minggu_ke' => 'integer',
        'tahun' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if ($model->tanggal) {
                $carbon = Carbon::parse($model->tanggal);
                $model->minggu_ke = $model->minggu_ke ?? (int) $carbon->isoWeek();
                $model->tahun = $model->tahun ?? (int) $carbon->year;
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sesi(): HasMany
    {
        return $this->hasMany(Sesi::class, 'sesi_latihan_id');
    }

    // Scopes untuk query praktis
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', Carbon::today());
    }

    public function scopeBerlangsung($query)
    {
        return $query->where('status', 'berlangsung');
    }

    public function scopeAktifAtauHariIni($query)
    {
        return $query->where('status', 'berlangsung')
            ->orWhere(function ($q) {
                $q->whereDate('tanggal', Carbon::today())
                    ->whereIn('status', ['terjadwal', 'berlangsung']);
            });
    }

    public function scopePekanIni($query)
    {
        $now = Carbon::now();
        return $query->where('tahun', $now->year)->where('minggu_ke', $now->isoWeek());
    }
}
