<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Grup = holding. Untuk sekarang isinya cuma 1 baris (grup kalian),
     * tapi dibuat tabel (bukan hardcode) supaya kalau suatu saat ada
     * ekspansi tidak perlu ubah struktur.
     */
    public function up(): void
    {
        Schema::create('grup', function (Blueprint $table) {
            $table->id();
            $table->string('nama_grup', 100);
            $table->timestamps();
        });

        // Baris default supaya perusahaan/divisi punya induk sejak awal.
        // Ganti "Nama Grup Anda" lewat halaman pengaturan nanti, atau UPDATE manual.
        DB::table('grup')->insert([
            'nama_grup' => 'Nama Grup Anda',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('grup');
    }
};
