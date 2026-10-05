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
        'perusahaan_id',
        'tipe_karyawan_id',
        'shift_id',
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

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function tipeKaryawan()
    {
        return $this->belongsTo(TipeKaryawan::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function jadwalShift()
    {
        return $this->hasMany(JadwalShift::class);
    }

    /**
     * Shift yang berlaku untuk karyawan ini DI TANGGAL TERTENTU.
     *
     * Dicari dulu di jadwal_shift (dipakai karyawan yang shift-nya bergilir,
     * diatur kepala bagian). Kalau tidak ada baris yang berlaku di tanggal
     * itu -- termasuk kalau karyawan ini memang tidak pernah punya jadwal
     * bergilir sama sekali -- jatuh ke shift default (kolom shift_id di
     * tabel karyawan, biasanya "Reguler"). Dengan begitu karyawan yang
     * kepala bagiannya belum pernah mengatur jadwal tetap punya shift yang
     * valid untuk dipakai rekap absensi.
     */
    public function shiftPadaTanggal(\Carbon\Carbon $tanggal): ?Shift
    {
        $jadwal = $this->jadwalShift()
            ->where('berlaku_mulai', '<=', $tanggal->toDateString())
            ->where(function ($q) use ($tanggal) {
                $q->whereNull('berlaku_sampai')
                  ->orWhere('berlaku_sampai', '>=', $tanggal->toDateString());
            })
            ->with('shift')
            ->orderByDesc('berlaku_mulai')
            ->first();

        return $jadwal?->shift ?? $this->shift;
    }

    /**
     * Apakah karyawan ini ditunjuk sebagai kepala di salah satu departemen
     * (lihat departemen.kepala_karyawan_id). Dipakai untuk mengecek wewenang
     * atur shift anak buah -- BUKAN lewat role, lihat catatan di migration
     * kepala_karyawan_id.
     */
    public function departemenYangDipimpin()
    {
        return Departemen::where('kepala_karyawan_id', $this->id)->get();
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