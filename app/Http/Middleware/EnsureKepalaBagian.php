<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Wewenang "kepala bagian" BUKAN role terpisah (lihat catatan di migration
 * kepala_karyawan_id) -- ini karyawan biasa yang kebetulan ditunjuk sebagai
 * kepala di satu atau lebih departemen (departemen.kepala_karyawan_id).
 *
 * Middleware ini hanya menjaga pintu masuk ke halaman "Shift Tim" (menolak
 * yang bukan kepala sama sekali). Pengecekan departemen MANA yang boleh dia
 * atur tetap dilakukan di controller, karena satu kepala bisa memimpin
 * lebih dari satu departemen, dan anak buah hanya boleh diambil dari
 * departemen yang benar-benar dia pimpin.
 */
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