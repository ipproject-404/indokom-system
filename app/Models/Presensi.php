<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    /** Batas scan pulang setelah jam pulang shift malam yang lewat tengah malam. */
    public const TOLERANSI_PULANG_JAM = 4;

    protected $table = 'presensi';

    protected $fillable = [
        'karyawan_id',
        'shift_id',
        'tanggal',
        'jam_masuk',
        'latitude_masuk',
        'longitude_masuk',
        'alamat_masuk',
        'nama_jalan_masuk',
        'jarak_masuk_meter',
        'status_radius_masuk',
        'jam_pulang',
        'latitude_pulang',
        'longitude_pulang',
        'alamat_pulang',
        'nama_jalan_pulang',
        'jarak_pulang_meter',
        'status_radius_pulang',
        'metode_presensi',
        'status_verifikasi',
        'diverifikasi_oleh',
        'waktu_verifikasi',
        'catatan',

        // ⭐ TAMBAHAN BARU — untuk fitur Input Manual HRD
        'status_kehadiran',
        'keterangan_manual',
    ];

    // ==========================================
    // RELASI
    // ==========================================

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    // ==========================================
    // ACCESSOR — Label & Warna Status Manual
    // ==========================================

    /**
     * Label status kehadiran (untuk ditampilkan di view).
     */
    public function getLabelStatusAttribute(): ?string
    {
        return match ($this->status_kehadiran) {
            'hadir'          => 'Hadir',
            'terlambat'      => 'Terlambat',
            'sakit'          => 'Sakit',
            'izin'           => 'Izin',
            'cuti'           => 'Cuti',
            'alpa'           => 'Alpa',
            'dinas_luar'     => 'Dinas Luar',
            'libur_nasional' => 'Libur',
            default          => null,
        };
    }

    /**
     * Warna tailwind untuk status kehadiran.
     * Return array: ['ikon' => ..., 'warna' => ..., 'bg' => ...]
     */
    public function getStyleStatusAttribute(): array
    {
        $map = [
            'hadir'          => ['ikon' => 'bi-check-circle-fill',  'warna' => 'text-emerald-500', 'bg' => 'bg-emerald-50'],
            'terlambat'      => ['ikon' => 'bi-clock-fill',         'warna' => 'text-amber-500',   'bg' => 'bg-amber-50'],
            'sakit'          => ['ikon' => 'bi-bandaid-fill',       'warna' => 'text-blue-500',    'bg' => 'bg-blue-50'],
            'izin'           => ['ikon' => 'bi-envelope-fill',      'warna' => 'text-yellow-600',  'bg' => 'bg-yellow-50'],
            'cuti'           => ['ikon' => 'bi-airplane-fill',      'warna' => 'text-purple-500',  'bg' => 'bg-purple-50'],
            'alpa'           => ['ikon' => 'bi-x-octagon-fill',     'warna' => 'text-red-500',     'bg' => 'bg-red-50'],
            'dinas_luar'     => ['ikon' => 'bi-briefcase-fill',     'warna' => 'text-orange-500',  'bg' => 'bg-orange-50'],
            'libur_nasional' => ['ikon' => 'bi-calendar-x-fill',    'warna' => 'text-gray-500',    'bg' => 'bg-gray-100'],
        ];

        return $map[$this->status_kehadiran] ?? [
            'ikon' => 'bi-question-circle',
            'warna' => 'text-gray-400',
            'bg' => 'bg-gray-50',
        ];
    }

    /**
     * Cek apakah ini presensi input manual dari HRD (bukan scan QR).
     */
    public function getIsManualHrdAttribute(): bool
    {
        return $this->metode_presensi === 'MANUAL_HRD';
    }

    // ==========================================
    // HELPER — Logic jam masuk/pulang
    // ==========================================

    /**
     * Presensi yang masih terbuka (belum absen pulang) dan masih layak
     * dianggap "sedang berjalan" pada waktu $sekarang:
     * - tanggal kerjanya hari ini, atau
     * - kemarin, tapi shift-nya melewati tengah malam dan belum lewat
     *   toleransi setelah jam pulang.
     * Presensi kemarin yang lupa pulang untuk shift biasa tidak dihitung.
     */
    public static function terbukaUntuk(Karyawan $karyawan, Carbon $sekarang): ?self
    {
        $kandidat = static::with('shift')
            ->where('karyawan_id', $karyawan->id)
            ->whereNull('jam_pulang')
            ->whereDate('tanggal', '>=', $sekarang->copy()->subDay()->toDateString())
            ->orderByDesc('tanggal')
            ->get();

        foreach ($kandidat as $p) {
            $tanggal = Carbon::parse($p->tanggal)->startOfDay();

            if ($tanggal->isSameDay($sekarang)) {
                return $p;
            }

            $shift = $p->shift;
            if ($shift
                && $shift->melewatiTengahMalam($tanggal->dayOfWeekIso)
                && $sekarang->lte($shift->waktuPulang($tanggal)->addHours(self::TOLERANSI_PULANG_JAM))) {
                return $p;
            }
        }

        return null;
    }

    /**
     * Waktu masuk sebenarnya (tanggal + jam). Untuk shift malam yang scan
     * masuknya sudah lewat 00:00, tanggalnya mundur ke hari berikutnya
     * dari tanggal kerja.
     *
     * Return null kalau tidak ada jam masuk (input manual sakit/izin/cuti).
     */
    public function waktuMasukLengkap(): ?Carbon
    {
        if (! $this->jam_masuk) {
            return null;
        }

        $tanggal = Carbon::parse($this->tanggal)->startOfDay();
        $masuk = Carbon::parse($tanggal->toDateString() . ' ' . $this->jam_masuk);

        $shift = $this->shift;
        if ($shift && $shift->melewatiTengahMalam($tanggal->dayOfWeekIso)) {
            $jamPulang = substr($shift->jamPulangUntukHari($tanggal->dayOfWeekIso), 0, 5);
            if (substr($this->jam_masuk, 0, 5) <= $jamPulang) {
                $masuk->addDay();
            }
        }

        return $masuk;
    }

    /**
     * Waktu pulang sebenarnya; null kalau belum absen pulang.
     */
    public function waktuPulangLengkap(): ?Carbon
    {
        if (! $this->jam_pulang) {
            return null;
        }

        $masuk = $this->waktuMasukLengkap();

        // Kalau tidak ada jam masuk (manual), pakai tanggal langsung
        if (! $masuk) {
            return Carbon::parse($this->tanggal . ' ' . $this->jam_pulang);
        }

        $pulang = Carbon::parse($masuk->toDateString() . ' ' . $this->jam_pulang);

        if ($pulang->lt($masuk)) {
            $pulang->addDay();
        }

        return $pulang;
    }
}