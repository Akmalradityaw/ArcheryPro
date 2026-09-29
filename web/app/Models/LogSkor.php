<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogSkor extends Model
{
    protected $table = 'log_skor';

    public $timestamps = false;

    protected $fillable = [
        'skor_id', 'user_id', 'perubahan', 'data_lama', 'data_baru',
    ];

    protected $casts = ['data_lama' => 'array', 'data_baru' => 'array'];

    public function skor(): BelongsTo
    {
        return $this->belongsTo(Skor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
