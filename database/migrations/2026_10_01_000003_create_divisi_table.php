<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Divisi milik PERUSAHAAN (bukan grup).
     * Contoh: PT-A ISP punya divisi Holding & Tambak.
     */
    public function up(): void
    {
        Schema::create('divisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')
                  ->constrained('perusahaan')
                  ->cascadeOnDelete();
            $table->string('nama_divisi', 100);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        // 2 divisi awal untuk PT-A (id=1)
        DB::table('divisi')->insert([
            ['perusahaan_id' => 1, 'nama_divisi' => 'Holding', 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['perusahaan_id' => 1, 'nama_divisi' => 'Tambak', 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('divisi');
    }
};