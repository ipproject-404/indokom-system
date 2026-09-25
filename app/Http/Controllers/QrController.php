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
            'alamat' => ['nullable', 'string', 'max:255'],
            'nama_jalan' => ['nullable', 'string', 'max:255'],
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

        // ------------------------------------------------------------
        // Hitung ulang jarak & status radius DI SERVER (bukan percaya
        // begitu saja dari JS) -- supaya datanya bisa diandalkan untuk
        // keperluan verifikasi nanti, karena nilai dari JS/browser bisa
        // dimanipulasi orang yang paham cara ubah request.
        // ------------------------------------------------------------
        $jarakMeter = $this->hitungJarakMeter(
            (float) $request->latitude,
            (float) $request->longitude,
            (float) config('kantor.latitude'),
            (float) config('kantor.longitude')
        );
        $dalamRadius = $jarakMeter <= config('kantor.radius_meter');
        $statusRadius = $dalamRadius ? 'dalam_radius' : 'luar_radius';

        // Kalau di dalam radius kantor, pakai nama kantor sebagai alamat;
        // kalau di luar, pakai teks alamat/nama jalan yang dikirim dari
        // hasil reverse-geocoding di browser (bisa kosong kalau gagal).
        $alamat = $dalamRadius ? config('kantor.nama') : ($request->alamat ?: 'Alamat tidak diketahui');

        // Nama jalan SELALU diambil dari hasil reverse-geocoding browser,
        // terlepas dari status radius -- ini kolom terpisah dari $alamat
        // di atas (yang isinya bisa jadi nama kantor, bukan nama jalan).
        $namaJalan = $request->nama_jalan ?: null;

        $sekarang = Carbon::now();
        $hariIni = $sekarang->toDateString();

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
                'alamat_masuk' => $alamat,
                'nama_jalan_masuk' => $namaJalan,
                'jarak_masuk_meter' => round($jarakMeter, 2),
                'status_radius_masuk' => $statusRadius,
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
                'alamat_pulang' => $alamat,
                'nama_jalan_pulang' => $namaJalan,
                'jarak_pulang_meter' => round($jarakMeter, 2),
                'status_radius_pulang' => $statusRadius,
            ]);

            $pesan = 'Absen pulang berhasil dicatat pukul ' . $sekarang->format('H:i:s') . '.';
        } else {
            $pesan = 'Kamu sudah tercatat absen masuk dan pulang hari ini.';
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('pesan_absensi', $pesan);
    }

    /**
     * Jarak dua koordinat pakai rumus Haversine, hasil dalam meter.
     * Sengaja dihitung ulang di server -- lihat catatan di scanAbsensi().
     */
    private function hitungJarakMeter(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371000; // radius bumi, meter
        $toRad = fn ($deg) => $deg * M_PI / 180;

        $dLat = $toRad($lat2 - $lat1);
        $dLng = $toRad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos($toRad($lat1)) * cos($toRad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $r * $c;
    }
}