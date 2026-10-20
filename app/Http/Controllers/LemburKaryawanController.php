<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LemburKaryawanController extends Controller
{
    /** Riwayat + form pengajuan lembur milik karyawan yang login. */
    public function index()
    {
        Carbon::setLocale('id');

        $karyawan = $this->karyawanBolehLembur();

        $riwayat = Lembur::where('karyawan_id', $karyawan->id)
            ->orderByDesc('tanggal')
            ->limit(30)
            ->get();

        return view('karyawan.lembur', compact('karyawan', 'riwayat'));
    }

    /**
     * Helper untuk memvalidasi dan mengambil data karyawan yang sedang login.
     * Mencegah error jika user yang login tidak punya relasi ke tabel karyawan.
     */
    protected function karyawanBolehLembur()
    {
        // Mengambil data karyawan berdasarkan karyawan_id dari user yang login
        $karyawan = Karyawan::find(Auth::user()->karyawan_id);

        // Jika akun tidak punya data karyawan, tolak aksesnya (403 Forbidden)
        if (!$karyawan) {
            abort(403, 'Akun Anda tidak terhubung dengan data karyawan.');
        }

        return $karyawan;
    }
}