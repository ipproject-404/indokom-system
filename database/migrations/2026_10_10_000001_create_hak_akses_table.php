<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hak khusus per karyawan (payroll, nanti penggajian, dll) sebagai DATA,
     * bukan role baru di users -- polanya sama dengan kepala_karyawan_id.
     * Orang yang punya hak tetap role 'karyawan' dan tetap bisa absen biasa.
     */
    public function up(): void
    {
        Schema::create('hak_akses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->string('hak', 30)->comment('Contoh: payroll');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['karyawan_id', 'hak'], 'unik_karyawan_hak');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hak_akses');
    }
};