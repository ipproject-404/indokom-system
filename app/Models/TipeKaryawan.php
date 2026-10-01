<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipeKaryawan extends Model
{
    protected $table = 'tipe_karyawan';

    protected $fillable = [
        'kode',
        'nama',
        'dasar_absensi',
        'periode_gaji',
        'keterangan',
    ];

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class);
    }
}
