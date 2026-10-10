<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pintu masuk untuk hak khusus per karyawan (tabel hak_akses).
 * Pemakaian: Route::middleware(['auth', 'hak:payroll'])
 */
class EnsureHakAkses
{
    public function handle(Request $request, Closure $next, string $hak): Response
    {
        $karyawan = Auth::user()?->karyawan;

        if (! $karyawan || ! $karyawan->punyaHak($hak)) {
            abort(403, 'Kamu tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}