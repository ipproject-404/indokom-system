<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pelatihan extends Model
{
    protected $table = 'pelatihan';

    protected $fillable = [
        'nama_pelatihan',
        'penyelenggara',
        'kategori',
        'tanggal_mulai',
        'tanggal_selesai',
        'deskripsi',
        'status',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function peserta()
    {
        return $this->hasMany(PesertaPelatihan::class);
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /** Apakah pelatihan sedang berjalan sekarang. */
    public function sedangBerjalan(): bool
    {
        $hari = Carbon::today();
        return $hari->gte($this->tanggal_mulai)
            && ($this->tanggal_selesai === null || $hari->lte($this->tanggal_selesai));
    }

    /** Apakah pelatihan akan datang (belum mulai). */
    public function akanDatang(): bool
    {
        return Carbon::today()->lt($this->tanggal_mulai);
    }
}