<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Shift = jadwal jam kerja, sekarang jadi data (bukan config/jam_kerja.php
     * lagi) supaya tiap departemen/tipe karyawan bisa punya jadwal sendiri,
     * dan mendukung karyawan shift-shiftan.
     *
     * jam_pulang_per_hari nullable: kalau NULL, semua hari kerja pulang di
     * jam_pulang_default. Kalau diisi, formatnya JSON per hari ISO
     * (1=Senin..7=Minggu), contoh: {"5":"16:30","6":"14:00"} -- ini persis
     * kasus Jumat/Sabtu yang beda dari hari lain, seperti config lama.
     * hari_kerja: daftar hari ISO yang termasuk hari kerja shift ini,
     * contoh [1,2,3,4,5,6] = Senin-Sabtu.
     */
    public function up(): void
    {
        Schema::create('shift', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->nullable()->constrained('perusahaan')
                ->comment('NULL = berlaku untuk semua PT dalam grup');
            $table->string('nama_shift', 50);
            $table->time('jam_masuk');
            $table->time('jam_pulang_default');
            $table->unsignedSmallInteger('toleransi_menit')->default(0);
            $table->json('jam_pulang_per_hari')->nullable();
            $table->json('hari_kerja')->comment('Array hari ISO, 1=Senin..7=Minggu, contoh [1,2,3,4,5,6]');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        // Shift "Reguler" -- pemindahan dari config/jam_kerja.php yang dibuat
        // sebelumnya, supaya karyawan lama tetap punya jadwal yang sama persis.
        DB::table('shift')->insert([
            'perusahaan_id' => null,
            'nama_shift' => 'Reguler',
            'jam_masuk' => '08:00:00',
            'jam_pulang_default' => '16:00:00',
            'toleransi_menit' => 5,
            'jam_pulang_per_hari' => json_encode(['5' => '16:30', '6' => '14:00']),
            'hari_kerja' => json_encode([1, 2, 3, 4, 5, 6]),
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('shift');
    }
};
