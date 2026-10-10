<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HakAkses extends Model
{
    public const PAYROLL = 'payroll';

    protected $table = 'hak_akses';

    protected $fillable = ['karyawan_id', 'hak', 'dibuat_oleh'];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}