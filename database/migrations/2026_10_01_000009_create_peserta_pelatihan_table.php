<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Karyawan yang ditunjuk mengikuti satu pelatihan tertentu.
     * Diinput manual oleh HRD, tidak otomatis untuk semua karyawan.
     */
    public function up(): void
    {
        Schema::create('peserta_pelatihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelatihan_id')->constrained('pelatihan')->cascadeOnDelete();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->foreignId('ditunjuk_oleh')->nullable()->constrained('users');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['pelatihan_id', 'karyawan_id'], 'unik_peserta_pelatihan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_pelatihan');
    }
};