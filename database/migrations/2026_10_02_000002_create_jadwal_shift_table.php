<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Riwayat penjadwalan shift per karyawan -- dipakai untuk karyawan yang
     * shift-nya bergilir (ditukar-tukar kepala bagian per periode), BUKAN
     * untuk karyawan yang jadwalnya tetap (itu cukup pakai karyawan.shift_id).
     *
     * Satu baris = "karyawan ini pakai shift ini, dari tanggal sekian".
     * berlaku_sampai NULL artinya masih berlaku sampai ada baris baru yang
     * menggantikannya -- saat kepala bagian menukar jadwal, baris lama
     * ditutup (diisi berlaku_sampai = H-1) dan baris baru dibuat, bukan
     * di-update menimpa baris lama, supaya riwayat pergantian shift tetap
     * tersimpan (berguna untuk audit & rekap).
     *
     * Dibuat oleh kepala bagian (dicek lewat departemen.kepala_karyawan_id,
     * bukan role khusus) -- lihat migration sebelumnya. Tidak ada alur
     * persetujuan HRD; HRD hanya baca dari tabel ini.
     */
    public function up(): void
    {
        Schema::create('jadwal_shift', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan');
            $table->foreignId('shift_id')->constrained('shift');
            $table->date('berlaku_mulai');
            $table->date('berlaku_sampai')->nullable();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            $table->index(['karyawan_id', 'berlaku_mulai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_shift');
    }
};
