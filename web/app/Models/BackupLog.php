<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupLog extends Model
{
    protected $table = 'backup_log';

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'jenis_backup', 'format', 'ukuran_file', 'lokasi_file',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
