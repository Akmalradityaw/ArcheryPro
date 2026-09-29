<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Atlet extends Model
{
    protected $table = 'atlet';

    protected $fillable = [
        'user_id', 'nia', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'sekolah_id', 'kelas', 'no_telepon', 'nama_orangtua',
        'no_telepon_orangtua', 'alamat', 'kategori_id', 'golongan_darah',
        'riwayat_cedera', 'alergi', 'status', 'izinkan_tampil_publik',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'izinkan_tampil_publik' => 'boolean',
    ];

    public function scopePublik(Builder $query): Builder
    {
        return $query->where('izinkan_tampil_publik', true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function sesi(): HasMany
    {
        return $this->hasMany(Sesi::class);
    }
}
