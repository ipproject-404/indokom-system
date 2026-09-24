<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table = 'presensi';

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'jam_masuk',
        'latitude_masuk',
        'longitude_masuk',
        'jam_pulang',
        'latitude_pulang',
        'longitude_pulang',
        'metode_presensi',
        'status_verifikasi',
        'diverifikasi_oleh',
        'waktu_verifikasi',
        'catatan',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}
