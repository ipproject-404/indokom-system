<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Departemen extends Model
{
    protected $table = 'departemen';

    protected $fillable = ['nama_departemen', 'status', 'divisi_id', 'kepala_karyawan_id'];

    public function kepalaKaryawan()
    {
        return $this->belongsTo(Karyawan::class, 'kepala_karyawan_id');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class);
    }
}