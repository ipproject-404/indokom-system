<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RekapAbsensiController extends Controller
{
    /**
     * Rekap kehadiran bulanan milik karyawan yang sedang login,
     * ditampilkan sebagai kalender (Senin - Minggu) per minggu.
     *
     * Cara menghitung status per hari BERGANTUNG pada
     * tipe_karyawan.dasar_absensi milik karyawan ini:
     * - 'jadwal' (bulanan, bulanan_kontrak): dicek terhadap shift ->
     *   bisa berstatus "Terlambat", dan hari yang bukan hari_kerja shift-nya
     *   ikut dihitung libur.
     * - 'jam' (borongan_jam): tidak ada konsep terlambat/jadwal, karena jam
     *   masuknya mengikuti ketersediaan barang. Total jam kerja per hari
     *   dihitung dan dijumlah di ringkasan, karena itu dasar upahnya.
     * - 'hasil' (borongan_hasil): gaji dari transaksi_produksi, bukan dari
     *   jam kerja. Rekap di sini hanya log kehadiran sederhana (belum
     *   dikaitkan ke hasil produksi -- menunggu keputusan lebih lanjut).
     *
     * Bulan yang ditampilkan bisa digeser lewat query string ?bulan=YYYY-MM.
     * Data lain karyawan TIDAK bisa diintip lewat sini karena yang dipakai
     * selalu Auth::user()->karyawan, bukan id dari URL.
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
            $daftarPresensi = Presensi::where('karyawan_id', $karyawan->id)
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

        // dasar_absensi menentukan cara hitung di bawah. Kalau karyawan belum
        // punya tipe_karyawan (data lama yang belum sempat dilengkapi HRD),
        // fallback ke 'jadwal' -- perilaku paling aman & sama seperti sebelumnya.
        $dasarAbsensi = $karyawan?->tipeKaryawan?->dasar_absensi ?? 'jadwal';
        // Shift TIDAK diambil sekali untuk sebulan penuh -- sebagian karyawan
        // shift-nya bergilir (ditukar kepala bagian per periode lewat tabel
        // jadwal_shift), jadi shift dicari ULANG tiap tanggal di bawah lewat
        // $karyawan->shiftPadaTanggal($tgl).

        $harian = [];
        $totalJamKerjaMenit = 0;

        for ($tgl = $tanggalMulai->copy(); $tgl->lte($tanggalAkhir); $tgl->addDay()) {
            $key = $tgl->toDateString();
            $presensi = $daftarPresensi->get($key);
            $isMasaDepan = $key > $hariIni;
            $belumBergabung = $tanggalMasuk && $tgl->lt($tanggalMasuk);

            $namaLibur = $liburNasional[$key]
                ?? ($hitungCutiBersama ? ($cutiBersama[$key] ?? null) : null);

            // Hari Minggu & tanggal merah selalu libur untuk semua tipe.
            // Tambahan: kalau karyawan sudah punya shift dengan hari_kerja
            // sendiri (misal shift yang tidak masuk Sabtu), hari di luar itu
            // ikut dihitung libur juga -- ini hanya berlaku untuk dasar_absensi
            // 'jadwal', karena borongan jam/hasil biasanya tidak terikat shift.
            $isLibur = $tgl->isSunday() || $namaLibur !== null;
            $shiftHariIni = $dasarAbsensi === 'jadwal' ? $karyawan?->shiftPadaTanggal($tgl) : null;
            if (! $isLibur && $shiftHariIni && ! $shiftHariIni->apakahHariKerja($tgl->dayOfWeekIso)) {
                $isLibur = true;
                $namaLibur = $namaLibur ?? 'Hari libur shift ' . $shiftHariIni->nama_shift;
            }
            $keteranganLibur = $namaLibur ?? ($tgl->isSunday() ? 'Hari Minggu' : null);

            if ($belumBergabung) {
                $status = 'belum_gabung';
            } elseif ($isLibur) {
                $status = 'libur';
            } elseif ($presensi) {
                $status = 'hadir';
            } elseif ($isMasaDepan) {
                $status = 'belum_diisi';
            } else {
                $status = 'tidak_hadir';
            }

            // ------------------------------------------------------------
            // Terlambat & pulang lebih awal -- HANYA untuk dasar_absensi
            // 'jadwal' dan karyawan yang sudah punya shift. Borongan jam
            // tidak pernah dianggap terlambat, hanya dihitung total jamnya.
            // ------------------------------------------------------------
            $terlambatMenit = null;
            $pulangAwalMenit = null;
            $jamStandar = null;
            $totalJamHariIni = null;
            $namaShiftHariIni = null;

            if ($presensi && ! $isLibur && $dasarAbsensi === 'jadwal' && $shiftHariIni) {
                $masukStandar = Carbon::parse($key . ' ' . $shiftHariIni->jam_masuk);
                $masuk = Carbon::parse($key . ' ' . $presensi->jam_masuk);

                if ($masuk->copy()->startOfMinute()->gt($masukStandar->copy()->addMinutes($shiftHariIni->toleransi_menit))) {
                    $terlambatMenit = (int) floor(abs($masukStandar->diffInMinutes($masuk)));
                    if ($status === 'hadir') {
                        $status = 'terlambat';
                    }
                }

                $jamPulangStandarTeks = $shiftHariIni->jamPulangUntukHari($tgl->dayOfWeekIso);
                $pulangStandar = Carbon::parse($key . ' ' . $jamPulangStandarTeks);
                if ($presensi->jam_pulang) {
                    $pulang = Carbon::parse($key . ' ' . $presensi->jam_pulang);
                    if ($pulang->lt($pulangStandar)) {
                        $pulangAwalMenit = (int) floor(abs($pulang->diffInMinutes($pulangStandar)));
                    }
                }

                $jamStandar = substr($shiftHariIni->jam_masuk, 0, 5) . ' - ' . substr($jamPulangStandarTeks, 0, 5);
                $namaShiftHariIni = $shiftHariIni->nama_shift;
            }

            // Total jam kerja per hari -- dihitung untuk SEMUA dasar_absensi
            // selama jam pulang sudah tercatat (berguna terutama untuk
            // borongan_jam, tapi tidak ada salahnya ditampilkan untuk yang lain).
            if ($presensi && $presensi->jam_pulang) {
                $masukAktual = Carbon::parse($key . ' ' . $presensi->jam_masuk);
                $pulangAktual = Carbon::parse($key . ' ' . $presensi->jam_pulang);
                $menitKerja = (int) abs($masukAktual->diffInMinutes($pulangAktual));
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
                'nama_shift' => $namaShiftHariIni ?? null,
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