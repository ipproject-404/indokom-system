<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master jenis pelatihan/sertifikasi yang pernah/akan diadakan
     * perusahaan (contoh: "Sertifikasi HACCP", "Pelatihan K3", "ISO 22000").
     * Satu baris di sini bisa diikuti banyak karyawan lewat
     * sertifikasi_karyawan -- bukan berarti tiap pelatihan hanya sekali jalan,
     * satu program yang sama bisa diadakan ulang di periode berikutnya,
     * dicatat sebagai baris sertifikasi_karyawan yang baru.
     */
    public function up(): void
    {
        Schema::create('pelatihan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelatihan', 150);
            $table->string('penyelenggara', 150)->nullable()->comment('Internal, atau lembaga luar seperti Dinas Tenaga Kerja, TUV, dll');
            $table->enum('kategori', ['sertifikasi', 'pelatihan_internal', 'pelatihan_eksternal'])->default('pelatihan_internal');
            $table->unsignedSmallInteger('masa_berlaku_bulan')->nullable()->comment('NULL = tidak ada masa berlaku/kedaluwarsa');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelatihan');
    }
};
