<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    use HasFactory;

    // Sesuaikan dengan nama tabel di ERD Anda (bukan 'lemburs')
    protected $table = 'lembur';

    // Mengizinkan semua kolom untuk diisi (Mass Assignment)
    protected $guarded = ['id'];

    // Relasi (BelongsTo) ke tabel karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}