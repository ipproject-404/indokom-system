<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Departemen extends Model
{
    protected $table = 'departemen';

    protected $fillable = ['nama_departemen', 'status'];
    
    public function kepalaKaryawan()
    {
        return $this->belongsTo(Karyawan::class, 'kepala_karyawan_id');
    }
}
