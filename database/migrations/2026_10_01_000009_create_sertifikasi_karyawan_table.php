<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catatan karyawan yang mengikuti satu pelaksanaan pelatihan/sertifikasi
     * tertentu. Biasanya cuma karyawan terpilih (kepala regu, dsb), jadi ini
     * TIDAK didaftar otomatis untuk semua karyawan -- diinput manual oleh
     * HRD tiap ada pelatihan berjalan.
     *
     * tanggal_kadaluarsa dihitung dari tanggal_selesai + pelatihan.masa_berlaku_bulan
     * saat data diinput (bukan kolom generated), supaya tetap bisa diubah manual
     * kalau ada kasus khusus (misal sertifikat diperpanjang tanpa ikut pelatihan ulang).
     */
    public function up(): void
    {
        Schema::create('sertifikasi_karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan');
            $table->foreignId('pelatihan_id')->constrained('pelatihan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->string('nomor_sertifikat', 100)->nullable();
            $table->string('file_sertifikat', 255)->nullable()->comment('Path file di storage, hasil scan/foto sertifikat');
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->enum('status', ['lulus', 'tidak_lulus', 'sedang_berjalan'])->default('sedang_berjalan');
            $table->foreignId('diinput_oleh')->nullable()->constrained('users');
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Cegah data ganda: karyawan yang sama tidak tercatat dua kali
            // untuk pelaksanaan pelatihan yang sama di tanggal mulai yang sama.
            $table->unique(['karyawan_id', 'pelatihan_id', 'tanggal_mulai'], 'unik_karyawan_pelatihan_tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikasi_karyawan');
    }
};
