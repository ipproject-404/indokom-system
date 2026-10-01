<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grup extends Model
{
    protected $table = 'grup';

    protected $fillable = [
        'nama_grup',
    ];

    public function perusahaan()
    {
        return $this->hasMany(Perusahaan::class);
    }

    public function divisi()
    {
        return $this->hasMany(Divisi::class);
    }
}
