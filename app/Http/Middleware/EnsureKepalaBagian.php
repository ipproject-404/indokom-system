<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class EnsureKepalaBagian
{
    public function handle(Request $request, Closure $next): Response
    {
        $karyawan = Auth::user()?->karyawan;

        if (! $karyawan || $karyawan->departemenYangDipimpin()->isEmpty()) {
            abort(403, 'Halaman ini hanya untuk kepala bagian.');
        }

        return $next($request);
    }
}