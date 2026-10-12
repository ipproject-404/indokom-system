<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    use HasFactory;

    // Sesuaikan dengan nama tabel di ERD Anda (bukan 'lemburs')
    protected $table = 'lembur';

    // Mengizinkan semua kolom untuk diisi (Mass Assignment)
    protected $guarded = ['id'];

    // Relasi (BelongsTo) ke tabel karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
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
 * Tentukan kelompok (reguler/insentif) berdasarkan nama jabatan.
 * Jabatan yang mengandung kata "asisten" (case-insensitive) ke atas = insentif.
 */
public static function tentukanKelompok(Karyawan $karyawan): string
{
    $namaJabatan = strtolower($karyawan->jabatan->nama_jabatan ?? '');
    return str_contains($namaJabatan, 'Asst') ? 'insentif' : 'reguler';
}

/**
 * Bulatkan durasi dengan aturan 15 menit:
 * - Selisih antara durasi scan dan durasi ajuan ≤ 15 menit → pakai durasi ajuan
 * - Selisih > 15 menit → hitung apa adanya dari scan, bulatkan ke 2 desimal
 */
public static function bulatkanDurasi(float $durasiScanJam, float $durasiAjuanJam): float
{
    $selisihMenit = abs($durasiScanJam - $durasiAjuanJam) * 60;

    if ($selisihMenit <= 15) {
        return $durasiAjuanJam;
    }

    return round($durasiScanJam, 2);
}

/**
 * Hitung ulang durasi, kelompok, syarat, dan upah.
 * Dipanggil saat acc oleh payroll.
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

    $durasiScanJam = round(abs($mulai->diffInMinutes($selesai)) / 60, 4);

    // Durasi ajuan dihitung dari jam_mulai dan jam_selesai_scan kalau ada
    $durasiAjuanJam = $this->durasi_jam ?? $durasiScanJam;

    $durasi = static::bulatkanDurasi($durasiScanJam, (float) $durasiAjuanJam);

    $this->durasi_jam       = $durasiScanJam;
    $this->durasi_final_jam = $durasi;

    // Kelompok & syarat
    $karyawan = $this->karyawan;
    if ($karyawan) {
        $kelompok              = static::tentukanKelompok($karyawan);
        $this->kelompok        = $kelompok;
        $this->memenuhi_syarat = $kelompok === 'reguler' || $durasi >= 4;

        $this->tarif_per_jam = 0; // tidak dipakai lagi, upah dihitung bertingkat
        $this->total_upah    = $this->memenuhi_syarat
            ? TarifLembur::hitungUpah($karyawan->departemen_id, $kelompok, $durasi)
            : 0;
    }

    $this->durasi_jam = round($durasiScanJam, 2);
}

    /**
     * Dipanggil saat scan PULANG. Kalau karyawan punya pengajuan lembur
     * (menunggu) di tanggal kerja itu dan pulangnya melewati jam pulang
     * shift, isi jam mulai/selesai/durasi otomatis. Pulang di jam normal
     * tidak mengubah apa pun. Karyawan tanpa shift: hanya jam selesai asli
     * yang dicatat, jam mulai diisi payroll dari surat.
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

        $lembur->presensi_id = $presensi->id;
        $lembur->jam_selesai_scan = $pulang->format('H:i:s');
        $lembur->jam_selesai = $pulang->format('H:i:s');
        $lembur->hitungUlang();
        $lembur->save();
    }
}