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
     * Bulan yang ditampilkan bisa digeser lewat query string
     * ?bulan=YYYY-MM dari tombol navigasi "<" / ">" di halaman.
     * Data lain karyawan TIDAK bisa diintip lewat sini karena yang
     * dipakai selalu Auth::user()->karyawan, bukan id dari URL.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

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

        // Tanggal masuk karyawan -- hari SEBELUM ini tidak dihitung apa-apa
        // (bukan "tidak hadir", karena karyawannya memang belum jadi
        // pegawai di tanggal tersebut). Kalau tanggal_masuk kosong,
        // anggap tidak ada batas (semua hari di bulan ini dihitung normal).
        $tanggalMasuk = $karyawan?->tanggal_masuk
            ? Carbon::parse($karyawan->tanggal_masuk)->startOfDay()
            : null;

        $harian = [];

        for ($tgl = $tanggalMulai->copy(); $tgl->lte($tanggalAkhir); $tgl->addDay()) {
            $key = $tgl->toDateString();
            $presensi = $daftarPresensi->get($key);
            $isMasaDepan = $key > $hariIni;
            $belumBergabung = $tanggalMasuk && $tgl->lt($tanggalMasuk);

            // ------------------------------------------------------------
            // Status per hari, urutan pengecekan penting:
            // 1) Sebelum tanggal_masuk karyawan -> belum bergabung, tidak
            //    dihitung sama sekali (bukan "tidak hadir").
            // 2) Sabtu/Minggu dianggap hari libur.
            // 3) Ada catatan presensi -> langsung "hadir". Absen masuk/pulang
            //    sudah tidak lagi lewat proses verifikasi HRD (begitu discan
            //    dan tercatat, otomatis sah) -- lihat QrController::scanAbsensi().
            //    Kolom status_verifikasi tetap ada di tabel tapi tidak lagi
            //    dipakai untuk menentukan status kehadiran di sini.
            // 4) Kalau belum ada catatan & tanggalnya belum lewat -> belum diisi.
            // 5) Kalau belum ada catatan & tanggalnya sudah lewat -> tidak hadir.
            // Sistem ini belum punya fitur pengajuan izin/cuti, jadi status
            // "Izin" sengaja tidak dimasukkan -- itu bagian terpisah (model
            // Lembur & fitur pengajuan lain) yang sedang dikerjakan tim HRD.
            // ------------------------------------------------------------
            if ($belumBergabung) {
                $status = 'belum_gabung';
            } elseif ($tgl->isWeekend()) {
                $status = 'libur';
            } elseif ($presensi) {
                $status = 'hadir';
            } elseif ($isMasaDepan) {
                $status = 'belum_diisi';
            } else {
                $status = 'tidak_hadir';
            }

            $harian[] = [
                'tanggal' => $tgl->copy(),
                'status' => $status,
                'presensi' => $presensi,
                'hari_ini' => $key === $hariIni,
            ];
        }

        // Kelompokkan per minggu (Senin=kolom pertama, Minggu=kolom terakhir)
        // supaya rapi dirender sebagai kalender Sen-Sel-Rab-Kam-Jum-Sab-Min.
        $minggu = [];
        $baris = array_fill(0, 7, null);
        foreach ($harian as $item) {
            $indexHari = $item['tanggal']->dayOfWeekIso - 1; // Senin=0 ... Minggu=6
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
            'hadir' => collect($harian)->where('status', 'hadir')->count(),
            'tidak_hadir' => collect($harian)->where('status', 'tidak_hadir')->count(),
        ];

        return view('karyawan.rekap-absensi', [
            'user' => $user,
            'karyawan' => $karyawan,
            'bulanDipilih' => $bulanDipilih,
            'bulanSebelumnya' => $bulanSebelumnya,
            'bulanBerikutnya' => $bulanBerikutnya,
            'minggu' => $minggu,
            'ringkasan' => $ringkasan,
        ]);
    }
}