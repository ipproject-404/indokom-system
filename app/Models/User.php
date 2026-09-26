<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Route;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'karyawan_id',
        'username',
        'password',
        'role',
        'status',
        'last_login',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'last_login' => 'datetime',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    /**
     * Nama route dashboard yang sesuai untuk role user ini.
     * Peta role -> route diatur di satu tempat: config/dashboard_routes.php
     * -- supaya nambah role baru tidak perlu ubah kode controller.
     *
     * Sengaja abort(500) dengan pesan jelas (bukan biarkan Laravel
     * lempar RouteNotFoundException yang membingungkan) kalau role
     * belum punya pemetaan atau route-nya belum dibuat -- supaya
     * pas testing langsung ketahuan apa yang perlu dibenahi.
     */
    public function routeDashboard(): string
    {
        $peta = config('dashboard_routes', []);
        $namaRoute = $peta[$this->role] ?? null;

        if (! $namaRoute) {
            abort(500, "Role '{$this->role}' belum terdaftar di config/dashboard_routes.php. Tambahkan pemetaannya di sana.");
        }

        if (! Route::has($namaRoute)) {
            abort(500, "Route '{$namaRoute}' untuk role '{$this->role}' belum dibuat di routes/web.php.");
        }

        return $namaRoute;
    }
}