<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\JadwalShift;
use App\Models\Karyawan;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ShiftTimController extends Controller
{
    /**
     * Daftar anak buah di SEMUA departemen yang dipimpin karyawan yang
     * sedang login, lengkap dengan shift yang berlaku hari ini dan jadwal
     * yang sudah disiapkan untuk masa depan (kalau ada).
     */
    public function index()
    {
        $karyawan = Auth::user()->karyawan;
        $departemenDipimpin = $karyawan->departemenYangDipimpin();
        $idDepartemen = $departemenDipimpin->pluck('id');

        $anakBuah = Karyawan::with(['jabatan', 'departemen', 'shift'])
            ->whereIn('departemen_id', $idDepartemen)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        $hariIni = Carbon::now();

        $anakBuah->each(function ($k) use ($hariIni) {
            $k->shift_sekarang = $k->shiftPadaTanggal($hariIni);

            // Jadwal yang sudah disiapkan tapi belum berlaku (berlaku_mulai
            // di masa depan) -- ditampilkan supaya kepala bagian tahu apa
            // yang sudah dia atur sebelumnya untuk orang ini.
            $k->jadwal_akan_datang = $k->jadwalShift()
                ->with('shift')
                ->where('berlaku_mulai', '>', $hariIni->toDateString())
                ->orderBy('berlaku_mulai')
                ->get();
        });

        $shifts = Shift::where('status', 'aktif')->orderBy('nama_shift')->get();

        return view('karyawan.shift-tim', [
            'anakBuah' => $anakBuah,
            'shifts' => $shifts,
            'departemenDipimpin' => $departemenDipimpin,
        ]);
    }

    /**
     * Menukar/menjadwalkan shift seorang anak buah mulai tanggal tertentu
     * (boleh tanggal depan, supaya bisa disiapkan dari jauh hari).
     *
     * Aturan penyederhanaan yang dipakai: jadwal baru ini MENGGANTIKAN semua
     * jadwal yang sudah diatur mulai tanggal yang sama atau sesudahnya --
     * supaya kepala bagian tidak perlu menghapus manual dulu kalau berubah
     * pikiran soal jadwal yang sudah disiapkan. Jadwal yang sedang berjalan
     * (mulai sebelum tanggal ini) ditutup di H-1 dari tanggal baru, bukan
     * dihapus, supaya riwayatnya tetap tersimpan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'shift_id' => 'required|exists:shift,id',
            'berlaku_mulai' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $karyawan = Auth::user()->karyawan;
        $idDepartemenDipimpin = $karyawan->departemenYangDipimpin()->pluck('id');

        $anakBuah = Karyawan::findOrFail($request->karyawan_id);

        // Jaga supaya kepala bagian A tidak bisa mengatur shift karyawan di
        // departemen lain lewat request yang dimanipulasi -- bukan cuma
        // mengandalkan tombol yang disembunyikan di tampilan.
        if (! $idDepartemenDipimpin->contains($anakBuah->departemen_id)) {
            abort(403, 'Karyawan ini bukan anak buah di departemen yang kamu pimpin.');
        }

        $mulai = Carbon::parse($request->berlaku_mulai)->startOfDay();

        // Hapus jadwal yang sudah disiapkan mulai tanggal ini atau sesudahnya
        // -- digantikan oleh instruksi baru ini.
        JadwalShift::where('karyawan_id', $anakBuah->id)
            ->where('berlaku_mulai', '>=', $mulai->toDateString())
            ->delete();

        // Tutup jadwal yang sedang berjalan (kalau ada & masih terbuka),
        // bukan dihapus -- supaya riwayat shift sebelumnya tetap tercatat.
        JadwalShift::where('karyawan_id', $anakBuah->id)
            ->where('berlaku_mulai', '<', $mulai->toDateString())
            ->whereNull('berlaku_sampai')
            ->update(['berlaku_sampai' => $mulai->copy()->subDay()->toDateString()]);

        JadwalShift::create([
            'karyawan_id' => $anakBuah->id,
            'shift_id' => $request->shift_id,
            'berlaku_mulai' => $mulai->toDateString(),
            'berlaku_sampai' => null,
            'dibuat_oleh' => Auth::id(),
            'keterangan' => $request->keterangan,
        ]);

        $pesan = $mulai->isToday() || $mulai->isPast()
            ? 'Shift berhasil diubah, berlaku mulai hari ini.'
            : 'Shift berhasil dijadwalkan, mulai berlaku ' . $mulai->translatedFormat('d F Y') . '.';

        return back()->with('success', $pesan);
    }

    /**
     * Batalkan satu jadwal yang sudah disiapkan untuk masa depan (belum
     * berlaku). Jadwal yang SEDANG berjalan sengaja tidak bisa dihapus
     * lewat sini -- kalau mau diganti, buat jadwal baru lewat store().
     */
    public function destroy($id)
    {
        $karyawan = Auth::user()->karyawan;
        $idDepartemenDipimpin = $karyawan->departemenYangDipimpin()->pluck('id');

        $jadwal = JadwalShift::with('karyawan')->findOrFail($id);

        if (! $idDepartemenDipimpin->contains($jadwal->karyawan->departemen_id)) {
            abort(403);
        }

        if ($jadwal->berlaku_mulai->lte(Carbon::today())) {
            return back()->with('error', 'Jadwal yang sedang berjalan tidak bisa dibatalkan, buat jadwal baru untuk menggantikannya.');
        }

        $jadwal->delete();

        return back()->with('success', 'Jadwal yang akan datang berhasil dibatalkan.');
    }
}