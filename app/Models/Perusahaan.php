<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    protected $table = 'perusahaan';

    protected $fillable = [
        'grup_id',
        'kode',
        'nama_perusahaan',
        'alamat',
        'status',
    ];

    public function grup()
    {
        return $this->belongsTo(Grup::class);
    }

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class);
    }
}
