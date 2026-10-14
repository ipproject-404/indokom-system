<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master jenis pelatihan/sertifikasi yang pernah/akan diadakan
     * perusahaan. Satu baris di sini bisa diikuti banyak karyawan lewat
     * sertifikasi_karyawan.
     */
    public function up(): void
    {
        Schema::create('pelatihan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelatihan', 150);
            $table->string('penyelenggara', 150)->nullable();
            $table->enum('kategori', ['sertifikasi', 'pelatihan_internal', 'pelatihan_eksternal'])->default('pelatihan_internal');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'dibatalkan'])->default('aktif');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelatihan');
    }
};