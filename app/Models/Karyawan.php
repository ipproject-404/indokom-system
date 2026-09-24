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
        'jabatan_id',
        'departemen_id',
        'tanggal_masuk',
        'tanggal_keluar',
        'status',
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class);
    }

    /**
     * Generate token QR baru yang acak & sulit ditebak, lalu simpan.
     * Dipanggil dari halaman HR saat "Reset QR" atau saat karyawan baru dibuat.
     */
    public function generateBarcodeToken(): string
    {
        $token = bin2hex(random_bytes(20)); // 40 karakter hex
        $this->barcode_uid = $token;
        $this->save();

        return $token;
    }
}
