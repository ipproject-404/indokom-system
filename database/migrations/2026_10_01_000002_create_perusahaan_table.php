<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Perusahaan = PT. Satu grup punya beberapa PT (sekarang 2).
     * Karyawan nanti terhubung ke sini lewat karyawan.perusahaan_id.
     */
    public function up(): void
    {
        Schema::create('perusahaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grup_id')->constrained('grup');
            $table->string('kode', 20)->unique()->comment('Kode singkat, misal PT-A, PT-B');
            $table->string('nama_perusahaan', 150);
            $table->string('alamat', 255)->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        // 2 baris awal sesuai kondisi sekarang (masih baru pisah dari 1 PT).
        // Ganti nama/kode-nya sesuai nama resmi PT A dan PT B.
        DB::table('perusahaan')->insert([
            ['grup_id' => 1, 'kode' => 'PT-A', 'nama_perusahaan' => 'PT A (ganti sesuai nama resmi)', 'alamat' => null, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['grup_id' => 1, 'kode' => 'PT-B', 'nama_perusahaan' => 'PT B (ganti sesuai nama resmi)', 'alamat' => null, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('perusahaan');
    }
};
