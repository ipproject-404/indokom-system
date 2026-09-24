<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';

    protected $fillable = ['nama_jabatan', 'departemen_id', 'status'];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }
}
