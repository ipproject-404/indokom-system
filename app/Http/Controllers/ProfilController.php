<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProfilController extends Controller
{
    /**
     * Halaman profil karyawan yang sedang login.
     * Data diambil dari akun yang login sendiri (Auth::user()) --
     * karyawan tidak bisa lihat/akses profil orang lain lewat sini,
     * beda dengan halaman HRD yang memang perlu {id} di URL.
     */
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan; // null kalau akun ini tidak terhubung ke data karyawan

        $jumlahHadirBulanIni = null;
        if ($karyawan) {
            $jumlahHadirBulanIni = Presensi::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', Carbon::now()->month)
                ->whereYear('tanggal', Carbon::now()->year)
                ->whereNotNull('jam_masuk')
                ->count();
        }

        return view('karyawan.profil', [
            'user' => $user,
            'karyawan' => $karyawan,
            'jumlahHadirBulanIni' => $jumlahHadirBulanIni,
        ]);
    }
}