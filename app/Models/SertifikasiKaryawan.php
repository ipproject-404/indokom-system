<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SertifikasiKaryawan extends Model
{
    protected $table = 'sertifikasi_karyawan';
    protected $guarded = [];

    protected $casts = [
        'tanggal_mulai'      => 'date',
        'tanggal_selesai'    => 'date',
        'tanggal_kadaluarsa' => 'date',
        'tanggal_surat'      => 'date',
        'nilai_pretest'      => 'float',
        'nilai_postest'      => 'float',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class);
    }

    public function penginput()
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }

    public function isKadaluarsa(): bool
    {
        return $this->tanggal_kadaluarsa && $this->tanggal_kadaluarsa->isPast();
    }

    public function sisaHari(): ?int
    {
        if (! $this->tanggal_kadaluarsa) return null;
        return now()->diffInDays($this->tanggal_kadaluarsa, false);
    }

    public function isLulus(): bool
    {
        return $this->nilai_postest !== null && $this->nilai_postest >= 8;
    }

    public function peningkatan(): ?float
    {
        if ($this->nilai_pretest === null || $this->nilai_postest === null) {
            return null;
        }
        return $this->nilai_postest - $this->nilai_pretest;
    }
}