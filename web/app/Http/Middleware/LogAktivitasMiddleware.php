<?php

namespace App\Http\Middleware;

use App\Services\LogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// ponytail: hanya request mutasi (POST/PUT/PATCH/DELETE) yang dicatat — GET read-only
// hanya jadi noise. Didaftar global di grup web (1 baris Kernel) sehingga route
// TAHAP berikut otomatis tercakup tanpa ingat pasang manual.
class LogAktivitasMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('get')) {
            return $response;
        }

        LogService::catat(
            $request->method().' '.$request->path().' → '.$response->getStatusCode(),
            auth()->id(),
            $request
        );

        return $response;
    }
}
