<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $table = 'divisi';

    protected $fillable = [
        'grup_id',
        'nama_divisi',
        'status',
    ];

    public function grup()
    {
        return $this->belongsTo(Grup::class);
    }

    public function departemen()
    {
        return $this->hasMany(Departemen::class);
    }
}
