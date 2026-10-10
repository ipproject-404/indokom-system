<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Lembur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QrController extends Controller
{
    /** Absen masuk shift dibuka sekian jam sebelum jam mulai. */
    private const MARGIN_MASUK_AWAL_JAM = 2;

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

        // Sama seperti di scanAbsensi -- jangan biarkan QR orang lain
        // diam-diam mengambil alih sesi yang sedang login sebagai user lain.
        if (Auth::check() && Auth::id() !== $user->id) {
            return back()->withErrors([
                'token' => 'QR ini bukan milik akun yang sedang login (' . Auth::user()->username . '). Logout dulu jika memang ingin login sebagai pemilik QR ini.',
            ]);
        }

        // remember=true -> cookie "remember_token" bertahan lama, jadi user
        // tidak perlu login ulang tiap kali menutup & membuka browser/app.
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route($user->routeDashboard());
    }

    /**
     * Halaman scan QR untuk ABSENSI (login sekaligus mencatat jam masuk/pulang).
     */
    public function showScanAbsensi()
    {
        $user = Auth::user();

        // Tombol "kembali" harus tahu mau kemana -- kalau sudah login, ke
        // dashboard sesuai role-nya; kalau belum login, ke halaman login.
        $urlKembali = $user
            ? route($user->routeDashboard())
            : route('login');

        return view('absensi.scan', ['urlKembali' => $urlKembali]);
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

        // Cegah QR milik orang lain dipakai absen sambil masih login sebagai
        // user lain. Ganti akun harus lewat logout secara sadar.
        if (Auth::check() && Auth::id() !== $user->id) {
            return back()->withErrors([
                'token' => 'QR ini bukan milik akun yang sedang login (' . Auth::user()->username . '). Logout dulu jika memang ingin absen sebagai pemilik QR ini.',
            ]);
        }

        // Jarak & status radius dihitung ulang DI SERVER (nilai dari JS bisa dimanipulasi).
        $jarakMeter = $this->hitungJarakMeter(
            (float) $request->latitude,
            (float) $request->longitude,
            (float) config('kantor.latitude'),
            (float) config('kantor.longitude')
        );
        $dalamRadius = $jarakMeter <= config('kantor.radius_meter');
        $statusRadius = $dalamRadius ? 'dalam_radius' : 'luar_radius';

        // Dalam radius -> nama kantor; di luar -> alamat hasil reverse-geocoding browser.
        $alamat = $dalamRadius ? config('kantor.nama') : ($request->alamat ?: 'Alamat tidak diketahui');

        // Nama jalan SELALU dari reverse-geocoding browser, terlepas dari status radius.
        $namaJalan = $request->nama_jalan ?: null;

        $sekarang = Carbon::now();

        // 1) Ada presensi yang sedang berjalan? Kalau ya, scan ini = absen PULANG.
        //    Termasuk shift malam yang mulai kemarin dan belum pulang.
        $presensi = Presensi::terbukaUntuk($karyawan, $sekarang);

        if ($presensi) {
            // Jeda minimal 1 jam sejak absen masuk (dihitung dari waktu masuk
            // sebenarnya, bukan dari tanggal kerja).
            $batasAbsenPulang = $presensi->waktuMasukLengkap()->addHour();

            if ($sekarang->lessThan($batasAbsenPulang)) {
                $pesan = 'Absen pulang baru bisa dilakukan mulai pukul ' . $batasAbsenPulang->format('H:i') . ' (minimal 1 jam setelah absen masuk).';
            } else {
                $presensi->update([
                    'jam_pulang' => $sekarang->toTimeString(),
                    'latitude_pulang' => $request->latitude,
                    'longitude_pulang' => $request->longitude,
                    'alamat_pulang' => $alamat,
                    'nama_jalan_pulang' => $namaJalan,
                    'jarak_pulang_meter' => round($jarakMeter, 2),
                    'status_radius_pulang' => $statusRadius,
                ]);
                Lembur::isiDariPresensi($presensi);

                $pesan = 'Absen pulang berhasil dicatat pukul ' . $sekarang->format('H:i:s') . '.';
            }
        } else {
            // 2) Absen MASUK: tentukan tanggal kerja & shift-nya.
            [$tanggalKerja, $shift, $pesanTolak] = $this->tentukanKonteksMasuk($karyawan, $sekarang);

            if ($pesanTolak) {
                $pesan = $pesanTolak;
            } elseif (Presensi::where('karyawan_id', $karyawan->id)
                ->whereDate('tanggal', $tanggalKerja->toDateString())
                ->exists()) {
                $pesan = 'Kamu sudah tercatat absen masuk dan pulang hari ini.';
            } else {
                Presensi::create([
                    'karyawan_id' => $karyawan->id,
                    'shift_id' => $shift?->id,
                    'tanggal' => $tanggalKerja->toDateString(),
                    'jam_masuk' => $sekarang->toTimeString(),
                    'latitude_masuk' => $request->latitude,
                    'longitude_masuk' => $request->longitude,
                    'alamat_masuk' => $alamat,
                    'nama_jalan_masuk' => $namaJalan,
                    'jarak_masuk_meter' => round($jarakMeter, 2),
                    'status_radius_masuk' => $statusRadius,
                    'metode_presensi' => 'QR_KARYAWAN',
                    'status_verifikasi' => 'disetujui',
                ]);

                $pesan = 'Absen masuk berhasil dicatat pukul ' . $sekarang->format('H:i:s') . '.';
            }
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route($user->routeDashboard())->with('pesan_absensi', $pesan);
    }

    /**
     * Menentukan TANGGAL KERJA dan SHIFT untuk absen masuk.
     * Return [tanggalKerja|null, shift|null, pesanTolak|null].
     *
     * Urutan: (1) shift hari ini yang jendelanya sedang terbuka,
     * (2) shift malam kemarin yang masih berjalan lewat tengah malam,
     * (3) karyawan berjadwal: ditolak dengan alasan jelas;
     *     karyawan non-shift: tetap boleh, tanggal = hari ini.
     */
    private function tentukanKonteksMasuk(Karyawan $karyawan, Carbon $sekarang): array
    {
        $hariIni = $sekarang->copy()->startOfDay();
        $kemarin = $hariIni->copy()->subDay();

        $shiftHariIni = $karyawan->shiftPadaTanggal($hariIni);
        $buka = null;
        $tutup = null;

        if ($shiftHariIni) {
            $buka = $shiftHariIni->waktuMasuk($hariIni)->subHours(self::MARGIN_MASUK_AWAL_JAM);
            $tutup = $shiftHariIni->waktuPulang($hariIni);

            if ($sekarang->between($buka, $tutup)) {
                return [$hariIni, $shiftHariIni, null];
            }
        }

        $shiftKemarin = $karyawan->shiftPadaTanggal($kemarin);
        if ($shiftKemarin
            && $shiftKemarin->melewatiTengahMalam($kemarin->dayOfWeekIso)
            && $sekarang->lte($shiftKemarin->waktuPulang($kemarin))) {

            $sudahAda = Presensi::where('karyawan_id', $karyawan->id)
                ->whereDate('tanggal', $kemarin->toDateString())
                ->exists();

            return $sudahAda
                ? [null, null, 'Kamu sudah tercatat absen masuk dan pulang untuk shift ini.']
                : [$kemarin, $shiftKemarin, null];
        }

        if ($karyawan->pakai_jadwal_shift) {
            if (! $shiftHariIni) {
                return [null, null, 'Hari ini kamu tidak punya jadwal shift. Hubungi kepala bagianmu.'];
            }

            return [null, null, $sekarang->lt($buka)
                ? 'Absen masuk shift ' . $shiftHariIni->nama_shift . ' baru dibuka pukul ' . $buka->format('H:i') . '.'
                : 'Shift ' . $shiftHariIni->nama_shift . ' hari ini sudah berakhir pukul ' . $tutup->format('H:i') . '.'];
        }

        // Karyawan non-shift: perilaku lama, boleh scan kapan saja.
        return [$hariIni, $shiftHariIni, null];
    }

    /**
     * Jarak dua koordinat pakai rumus Haversine, hasil dalam meter.
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