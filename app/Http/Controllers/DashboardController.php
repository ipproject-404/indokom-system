<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan; // null kalau akun ini tidak terhubung ke data karyawan (misal admin_master)

        $presensiHariIni = null;
        $shiftHariIni = null;

        if ($karyawan) {
            $sekarang = Carbon::now();

            // Utamakan presensi yang sedang berjalan (termasuk shift malam yang
            // mulai kemarin), baru presensi dengan tanggal kerja hari ini.
            $presensiHariIni = Presensi::terbukaUntuk($karyawan, $sekarang)
                ?? Presensi::where('karyawan_id', $karyawan->id)
                    ->where('tanggal', $sekarang->toDateString())
                    ->first();

            if ($karyawan->pakai_jadwal_shift) {
                $shiftHariIni = $presensiHariIni?->shift
                    ?? $karyawan->shiftPadaTanggal($sekarang->copy()->startOfDay());
            }
        }

        return view('dashboard.karyawan', [
            'user' => $user,
            'karyawan' => $karyawan,
            'presensiHariIni' => $presensiHariIni,
            'shiftHariIni' => $shiftHariIni,
            'pesanAbsensi' => $request->session()->get('pesan_absensi'),
        ]);
    }
}