<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    protected $table = 'lembur';
    protected $guarded = ['id'];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function presensi()
    {
        return $this->belongsTo(Presensi::class);
    }

    public function pemroses()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    /**
     * Apakah karyawan ini termasuk insentif (jabatan mengandung "Asisten" ke atas).
     * Karyawan insentif tidak bisa diajukan lembur.
     */
    public static function adalahInsentif(Karyawan $karyawan): bool
    {
        $namaJabatan = strtolower($karyawan->jabatan->nama_jabatan ?? '');
        return str_contains($namaJabatan, 'asisten');
    }

    /**
     * Bulatkan durasi: selisih scan vs ajuan ≤ 15 menit → pakai ajuan.
     * Selisih > 15 menit → hitung dari scan.
     */
    public static function bulatkanDurasi(float $durasiScanJam, float $durasiAjuanJam): float
    {
        $selisihMenit = abs($durasiScanJam - $durasiAjuanJam) * 60;
        return $selisihMenit <= 15 ? $durasiAjuanJam : round($durasiScanJam, 2);
    }

    /**
     * Hitung ulang durasi dan upah. Dipanggil saat acc oleh payroll.
     */
    public function hitungUlang(): void
    {
        if (! $this->jam_mulai || ! $this->jam_selesai) {
            return;
        }

        $tanggal = Carbon::parse($this->tanggal)->toDateString();
        $mulai   = Carbon::parse($tanggal . ' ' . $this->jam_mulai);
        $selesai = Carbon::parse($tanggal . ' ' . $this->jam_selesai);

        if ($selesai->lte($mulai)) {
            $selesai->addDay();
        }

        $durasiScanJam  = round(abs($mulai->diffInMinutes($selesai)) / 60, 4);
        $durasiAjuanJam = $this->durasi_jam ?? $durasiScanJam;
        $durasi         = static::bulatkanDurasi($durasiScanJam, (float) $durasiAjuanJam);

        $this->durasi_jam       = round($durasiScanJam, 2);
        $this->durasi_final_jam = $durasi;

        $karyawan = $this->karyawan;
        if ($karyawan) {
            $this->tarif_per_jam = 0;
            $this->total_upah    = TarifLembur::hitungUpah(
                $karyawan->departemen_id,
                'reguler', // semua lembur sekarang reguler
                $durasi
            );
        }
    }

    /**
     * Isi jam selesai dari scan pulang.
     */
    public static function isiDariPresensi(Presensi $presensi): void
    {
        $lembur = static::where('karyawan_id', $presensi->karyawan_id)
            ->whereDate('tanggal', $presensi->tanggal)
            ->where('status', 'menunggu')
            ->whereNull('jam_selesai_scan')
            ->first();

        $pulang = $presensi->waktuPulangLengkap();

        if (! $lembur || ! $pulang) {
            return;
        }

        $shift = $presensi->shift;

        if ($shift) {
            $mulai = $shift->waktuPulang(Carbon::parse($presensi->tanggal)->startOfDay());
            if ($pulang->lte($mulai)) {
                return;
            }
            $lembur->jam_mulai = $mulai->format('H:i:s');
        }

        $lembur->presensi_id      = $presensi->id;
        $lembur->jam_selesai_scan = $pulang->format('H:i:s');
        $lembur->jam_selesai      = $pulang->format('H:i:s');
        $lembur->hitungUlang();
        $lembur->save();
    }
}