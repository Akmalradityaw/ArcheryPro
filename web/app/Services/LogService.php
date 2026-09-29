<?php

namespace App\Services;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogService
{
    public static function catat(string $aktivitas, ?int $userId = null, ?Request $request = null): void
    {
        $request ??= request();

        LogAktivitas::create([
            'user_id' => $userId ?? auth()->id(),
            'aktivitas' => $aktivitas,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
