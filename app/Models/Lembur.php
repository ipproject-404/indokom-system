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
     * Hitung durasi & upah dari jam_mulai/jam_selesai. Kalau jam selesai
     * <= jam mulai berarti lewat tengah malam (+1 hari). Tidak melakukan
     * apa-apa kalau salah satu jam belum ada. Tarif flat dari config.
     */
    public function hitungUlang(): void
    {
        if (! $this->jam_mulai || ! $this->jam_selesai) {
            return;
        }

        $tanggal = Carbon::parse($this->tanggal)->toDateString();
        $mulai = Carbon::parse($tanggal . ' ' . $this->jam_mulai);
        $selesai = Carbon::parse($tanggal . ' ' . $this->jam_selesai);

        if ($selesai->lte($mulai)) {
            $selesai->addDay();
        }

        $this->durasi_jam = round(abs($mulai->diffInMinutes($selesai)) / 60, 2);
        $this->tarif_per_jam = $this->tarif_per_jam ?? config('lembur.tarif_per_jam');
        $this->total_upah = round($this->durasi_jam * $this->tarif_per_jam);
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