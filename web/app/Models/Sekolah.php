<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sekolah extends Model
{
    protected $table = 'sekolah';

    protected $fillable = ['nama_sekolah', 'alamat', 'kota'];

    public function atlet(): HasMany
    {
        return $this->hasMany(Atlet::class);
    }
}
