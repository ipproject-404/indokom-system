<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Shift extends Model
{
    protected $table = 'shift';

    protected $fillable = [
        'perusahaan_id',
        'nama_shift',
        'jam_masuk',
        'jam_pulang_default',
        'toleransi_menit',
        'jam_pulang_per_hari',
        'hari_kerja',
        'status',
    ];

    protected $casts = [
        'jam_pulang_per_hari' => 'array',
        'hari_kerja' => 'array',
    ];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class);
    }

    /**
     * Jam pulang standar untuk hari tertentu (ISO: 1=Senin..7=Minggu).
     * Pakai jam_pulang_per_hari kalau hari itu diatur khusus (contoh: Jumat
     * 16:30, Sabtu 14:00), kalau tidak diatur pakai jam_pulang_default.
     */
    public function jamPulangUntukHari(int $isoHari): string
    {
        return $this->jam_pulang_per_hari[(string) $isoHari]
            ?? $this->jam_pulang_per_hari[$isoHari]
            ?? $this->jam_pulang_default;
    }

    /**
     * Apakah ISO hari tertentu termasuk hari kerja shift ini.
     */
    public function apakahHariKerja(int $isoHari): bool
    {
        return in_array($isoHari, $this->hari_kerja ?? [], true);
    }
}
