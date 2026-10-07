<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RekapAbsensiController extends Controller
{
    /**
     * Rekap kehadiran bulanan milik karyawan yang sedang login (kalender).
     *
     * Cara hitung bergantung pada tipe_karyawan.dasar_absensi:
     * - 'jadwal': dicek terhadap shift -> bisa "Terlambat". Dua pola:
     *     * shift tetap (pakai_jadwal_shift = false): hari libur = Minggu,
     *       tanggal merah, dan hari di luar hari_kerja shift.
     *     * jadwal bergilir (pakai_jadwal_shift = true): libur HANYA kalau
     *       di jadwal_shift tidak ada shift di tanggal itu.
     * - 'jam': total jam kerja per hari dijumlah; tidak ada "terlambat".
     * - 'hasil': log kehadiran sederhana.
     *
     * Shift yang dipakai untuk menghitung terlambat/pulang awal adalah
     * snapshot di presensi.shift_id (kalau ada), bukan shift karyawan saat ini.
     * Presensi shift malam dihitung dari waktu masuk/pulang sebenarnya
     * (pulang bisa di tanggal kalender berikutnya).
     *
     * Bulan bisa digeser lewat ?bulan=YYYY-MM. Data karyawan lain tidak bisa
     * diintip karena selalu memakai Auth::user()->karyawan.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan?->load(['tipeKaryawan', 'shift']);

        $bulanDipilih = $request->query('bulan')
            ? Carbon::createFromFormat('Y-m', $request->query('bulan'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $bulanSebelumnya = $bulanDipilih->copy()->subMonth()->format('Y-m');
        $bulanBerikutnya = $bulanDipilih->copy()->addMonth()->format('Y-m');

        $tanggalMulai = $bulanDipilih->copy()->startOfMonth();
        $tanggalAkhir = $bulanDipilih->copy()->endOfMonth();

        $daftarPresensi = collect();
        if ($karyawan) {
            $daftarPresensi = Presensi::with('shift')
                ->where('karyawan_id', $karyawan->id)
                ->whereBetween('tanggal', [$tanggalMulai->toDateString(), $tanggalAkhir->toDateString()])
                ->get()
                ->keyBy(fn ($p) => Carbon::parse($p->tanggal)->toDateString());
        }

        $hariIni = Carbon::now()->toDateString();

        $tanggalMasuk = $karyawan?->tanggal_masuk
            ? Carbon::parse($karyawan->tanggal_masuk)->startOfDay()
            : null;

        $liburNasional = config('hari_libur.libur_nasional', []);
        $cutiBersama = config('hari_libur.cuti_bersama', []);
        $hitungCutiBersama = config('hari_libur.anggap_cuti_bersama_libur', false);

        // Tanpa tipe_karyawan (data lama) -> fallback 'jadwal'.
        $dasarAbsensi = $karyawan?->tipeKaryawan?->dasar_absensi ?? 'jadwal';
        $pakaiJadwal = $dasarAbsensi === 'jadwal' && (bool) $karyawan?->pakai_jadwal_shift;

        // Jadwal sebulan dimuat sekali (bukan query per tanggal).
        $jadwalBulan = collect();
        if ($karyawan && $pakaiJadwal) {
            $jadwalBulan = $karyawan->jadwalShift()
                ->with('shift')
                ->whereBetween('tanggal', [$tanggalMulai->toDateString(), $tanggalAkhir->toDateString()])
                ->get()
                ->keyBy(fn ($j) => $j->tanggal->toDateString());
        }

        $harian = [];
        $totalJamKerjaMenit = 0;

        for ($tgl = $tanggalMulai->copy(); $tgl->lte($tanggalAkhir); $tgl->addDay()) {
            $key = $tgl->toDateString();
            $presensi = $daftarPresensi->get($key);
            $isMasaDepan = $key > $hariIni;
            $belumBergabung = $tanggalMasuk && $tgl->lt($tanggalMasuk);

            // Shift menurut jadwal/shift tetap untuk tanggal ini.
            $shiftJadwal = null;
            if ($dasarAbsensi === 'jadwal') {
                $shiftJadwal = $pakaiJadwal
                    ? $jadwalBulan->get($key)?->shift
                    : $karyawan?->shift;
            }

            if ($pakaiJadwal) {
                // Jadwal bergilir adalah satu-satunya sumber: Minggu, tanggal
                // merah, dan hari_kerja shift tidak dipakai.
                $isLibur = $shiftJadwal === null;
                $keteranganLibur = $isLibur
                    ? ($jadwalBulan->has($key) ? 'Libur sesuai jadwal shift' : 'Belum ada jadwal shift')
                    : null;
            } else {
                $namaLibur = $liburNasional[$key]
                    ?? ($hitungCutiBersama ? ($cutiBersama[$key] ?? null) : null);

                $isLibur = $tgl->isSunday() || $namaLibur !== null;
                if (! $isLibur && $shiftJadwal && ! $shiftJadwal->apakahHariKerja($tgl->dayOfWeekIso)) {
                    $isLibur = true;
                    $namaLibur = 'Hari libur shift ' . $shiftJadwal->nama_shift;
                }
                $keteranganLibur = $namaLibur ?? ($tgl->isSunday() ? 'Hari Minggu' : null);
            }

            // Ada presensi di hari libur tetap dihitung hadir.
            if ($belumBergabung) {
                $status = 'belum_gabung';
            } elseif ($presensi) {
                $status = 'hadir';
            } elseif ($isLibur) {
                $status = 'libur';
            } elseif ($isMasaDepan) {
                $status = 'belum_diisi';
            } else {
                $status = 'tidak_hadir';
            }

            $terlambatMenit = null;
            $pulangAwalMenit = null;
            $jamStandar = null;
            $totalJamHariIni = null;
            $namaShiftHariIni = null;

            // Standar jam: snapshot shift di presensi diutamakan.
            $shiftPakai = $isLibur ? null : ($presensi?->shift ?? $shiftJadwal);

            if ($presensi && $dasarAbsensi === 'jadwal' && $shiftPakai) {
                $masukStandar = $shiftPakai->waktuMasuk($tgl);
                $masuk = $presensi->waktuMasukLengkap();

                if ($masuk->copy()->startOfMinute()->gt($masukStandar->copy()->addMinutes($shiftPakai->toleransi_menit))) {
                    $terlambatMenit = (int) floor(abs($masukStandar->diffInMinutes($masuk)));
                    $status = 'terlambat';
                }

                $pulangStandar = $shiftPakai->waktuPulang($tgl);
                $pulang = $presensi->waktuPulangLengkap();
                if ($pulang && $pulang->lt($pulangStandar)) {
                    $pulangAwalMenit = (int) floor(abs($pulang->diffInMinutes($pulangStandar)));
                }

                $jamStandar = substr($shiftPakai->jam_masuk, 0, 5) . ' - '
                    . substr($shiftPakai->jamPulangUntukHari($tgl->dayOfWeekIso), 0, 5);
                $namaShiftHariIni = $shiftPakai->nama_shift;
            }

            // Total jam kerja per hari (dari waktu sebenarnya, aman untuk shift malam).
            if ($presensi && $presensi->jam_pulang) {
                $menitKerja = (int) abs($presensi->waktuMasukLengkap()->diffInMinutes($presensi->waktuPulangLengkap()));
                $totalJamHariIni = $menitKerja;
                if ($dasarAbsensi === 'jam') {
                    $totalJamKerjaMenit += $menitKerja;
                }
            }

            $harian[] = [
                'tanggal' => $tgl->copy(),
                'status' => $status,
                'presensi' => $presensi,
                'hari_ini' => $key === $hariIni,
                'keterangan' => $status === 'libur' ? $keteranganLibur : null,
                'terlambat_menit' => $terlambatMenit,
                'pulang_awal_menit' => $pulangAwalMenit,
                'jam_standar' => $jamStandar,
                'nama_shift' => $namaShiftHariIni,
                'total_jam_menit' => $totalJamHariIni,
            ];
        }

        $minggu = [];
        $baris = array_fill(0, 7, null);
        foreach ($harian as $item) {
            $indexHari = $item['tanggal']->dayOfWeekIso - 1;
            $baris[$indexHari] = $item;

            if ($indexHari === 6) {
                $minggu[] = $baris;
                $baris = array_fill(0, 7, null);
            }
        }
        if (array_filter($baris)) {
            $minggu[] = $baris;
        }

        $ringkasan = [
            'hadir' => collect($harian)->whereIn('status', ['hadir', 'terlambat'])->count(),
            'tidak_hadir' => collect($harian)->where('status', 'tidak_hadir')->count(),
            'terlambat' => collect($harian)->where('status', 'terlambat')->count(),
            'total_jam_kerja' => $dasarAbsensi === 'jam'
                ? round($totalJamKerjaMenit / 60, 1)
                : null,
        ];

        return view('karyawan.rekap-absensi', [
            'user' => $user,
            'karyawan' => $karyawan,
            'dasarAbsensi' => $dasarAbsensi,
            'bulanDipilih' => $bulanDipilih,
            'bulanSebelumnya' => $bulanSebelumnya,
            'bulanBerikutnya' => $bulanBerikutnya,
            'minggu' => $minggu,
            'ringkasan' => $ringkasan,
        ]);
    }
}