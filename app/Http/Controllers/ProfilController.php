<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProfilController extends Controller
{
    /**
     * Halaman profil pengguna yang sedang login.
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

        // Jika role HRD atau Admin, arahkan ke profil khusus HRD
        if ($user->role === 'hrd' || $user->role === 'admin') {
            return view('karyawan.profil_hrd', [
                'user' => $user,
                'karyawan' => $karyawan,
                'jumlahHadirBulanIni' => $jumlahHadirBulanIni,
            ]);
        }

        // Default untuk karyawan biasa
        return view('karyawan.profil', [
            'user' => $user,
            'karyawan' => $karyawan,
            'jumlahHadirBulanIni' => $jumlahHadirBulanIni,
        ]);
    }
}