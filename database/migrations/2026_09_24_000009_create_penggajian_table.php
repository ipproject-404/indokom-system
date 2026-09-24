<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penggajian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan');
            $table->tinyInteger('periode_bulan')->comment('1-12');
            $table->smallInteger('periode_tahun');
            $table->decimal('gaji_pokok', 12, 2)->default(0);
            $table->decimal('total_lembur', 12, 2)->default(0);
            $table->decimal('tunjangan', 12, 2)->default(0);
            $table->decimal('potongan', 12, 2)->default(0);
            $table->decimal('total_gaji', 12, 2)->default(0);
            $table->enum('status', ['belum_bayar', 'sudah_bayar'])->default('belum_bayar');
            $table->date('tanggal_bayar')->nullable();
            $table->timestamps();

            $table->unique(['karyawan_id', 'periode_bulan', 'periode_tahun'], 'unik_karyawan_periode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penggajian');
    }
};
