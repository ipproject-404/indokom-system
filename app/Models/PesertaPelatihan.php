<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaPelatihan extends Model
{
    protected $table = 'peserta_pelatihan';

    protected $fillable = [
        'pelatihan_id',
        'karyawan_id',
        'ditunjuk_oleh',
        'catatan',
        'status_kehadiran', // ⬅️ TAMBAHKAN INI
        'waktu_hadir',      // ⬅️ TAMBAHKAN INI
    ];

    // Opsional: Agar 'waktu_hadir' otomatis dibaca sebagai objek Carbon/Tanggal
    protected $casts = [
        'waktu_hadir' => 'datetime',
    ];

    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function penunjuk()
    {
        return $this->belongsTo(User::class, 'ditunjuk_oleh');
    }
}