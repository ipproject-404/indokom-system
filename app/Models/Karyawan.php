<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';

    protected $fillable = [
        'nik_ktp',
        'nik_kerja',
        'barcode_uid',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'pendidikan',

        // Relasi
        'perusahaan_id',
        'shift_id',
        'tipe_karyawan_id',
        'jabatan_id',
        'departemen_id',

        // Tanggal & status
        'tanggal_masuk',
        'tanggal_keluar',
        'status',
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'karyawan_id');
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'karyawan_id');
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function tipeKaryawan()
    {
        return $this->belongsTo(TipeKaryawan::class, 'tipe_karyawan_id');
    }

    public function generateBarcodeToken(): string
    {
        $token = bin2hex(random_bytes(20));
        $this->barcode_uid = $token;
        $this->save();

        return $token;
    }
}