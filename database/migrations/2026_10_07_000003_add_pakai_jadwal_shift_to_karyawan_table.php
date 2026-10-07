<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Penanda karyawan yang shift-nya bergilir (diatur kepala bagian).
     * Terpisah dari tipe_karyawan karena tipe = skema gaji, sedangkan
     * ini = pola jadwal. false = pakai shift tetap di karyawan.shift_id.
     */
    public function up(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->boolean('pakai_jadwal_shift')->default(false)->after('shift_id');
        });
    }

    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn('pakai_jadwal_shift');
        });
    }
};