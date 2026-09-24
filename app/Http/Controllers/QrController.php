<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QrController extends Controller
{
    /**
     * Halaman scan QR khusus untuk LOGIN saja (tidak mencatat presensi).
     * Contoh pemakaian: karyawan mau buka dashboard dari rumah untuk
     * cek jadwal / ajukan cuti, tanpa maksud absen.
     */
    public function showScanLogin()
    {
        return view('auth.scan-login');
    }

    public function scanLogin(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $karyawan = Karyawan::where('barcode_uid', $request->token)
            ->where('status', 'aktif')
            ->first();

        if (! $karyawan) {
            return back()->withErrors(['token' => 'QR tidak dikenali atau karyawan tidak aktif.']);
        }

        $user = $karyawan->user;

        if (! $user) {
            return back()->withErrors(['token' => 'Karyawan ini belum punya akun login. Hubungi HR.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Halaman scan QR untuk ABSENSI (login sekaligus mencatat jam masuk/pulang).
     * Wajib menyertakan lokasi (latitude & longitude) dari browser.
     */
    public function showScanAbsensi()
    {
        return view('absensi.scan');
    }

    public function scanAbsensi(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        $karyawan = Karyawan::where('barcode_uid', $request->token)
            ->where('status', 'aktif')
            ->first();

        if (! $karyawan) {
            return back()->withErrors(['token' => 'QR tidak dikenali atau karyawan tidak aktif.']);
        }

        $user = $karyawan->user;

        if (! $user) {
            return back()->withErrors(['token' => 'Karyawan ini belum punya akun login. Hubungi HR.']);
        }

        $sekarang = Carbon::now();
        $hariIni = $sekarang->toDateString();

        // Cari record presensi hari ini untuk karyawan ini
        $presensi = Presensi::where('karyawan_id', $karyawan->id)
            ->where('tanggal', $hariIni)
            ->first();

        if (! $presensi) {
            // Scan pertama hari ini -> Clock In
            Presensi::create([
                'karyawan_id' => $karyawan->id,
                'tanggal' => $hariIni,
                'jam_masuk' => $sekarang->toTimeString(),
                'latitude_masuk' => $request->latitude,
                'longitude_masuk' => $request->longitude,
                'metode_presensi' => 'QR_KARYAWAN',
                'status_verifikasi' => 'menunggu',
            ]);

            $pesan = 'Absen masuk berhasil dicatat pukul ' . $sekarang->format('H:i:s') . '.';
        } elseif (! $presensi->jam_pulang) {
            // Sudah ada jam masuk, belum ada jam pulang -> Clock Out
            $presensi->update([
                'jam_pulang' => $sekarang->toTimeString(),
                'latitude_pulang' => $request->latitude,
                'longitude_pulang' => $request->longitude,
            ]);

            $pesan = 'Absen pulang berhasil dicatat pukul ' . $sekarang->format('H:i:s') . '.';
        } else {
            // Sudah absen masuk & pulang hari ini
            $pesan = 'Kamu sudah tercatat absen masuk dan pulang hari ini.';
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('pesan_absensi', $pesan);
    }
}
