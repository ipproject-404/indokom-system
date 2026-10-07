<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jadwal shift jadi satu baris per karyawan per tanggal. Tukar shift
     * cukup menukar shift_id dua baris di tanggal itu, tanpa memotong
     * jadwal lain. shift_id NULL = libur.
     */
    public function up(): void
    {
        Schema::dropIfExists('jadwal_shift');

        Schema::create('jadwal_shift', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan');
            $table->date('tanggal');
            $table->foreignId('shift_id')->nullable()->constrained('shift');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            $table->unique(['karyawan_id', 'tanggal'], 'unik_karyawan_tanggal_shift');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_shift');

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
};