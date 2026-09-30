<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Divisi milik grup (bukan milik satu PT), karena departemen di
     * bawahnya menaungi lebih dari satu PT sekaligus (contoh: Divisi
     * Holding -> Departemen Personalia berisi HRD dari PT A & PT B).
     */
    public function up(): void
    {
        Schema::create('divisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grup_id')->constrained('grup');
            $table->string('nama_divisi', 100);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        // 2 baris awal sesuai contoh yang kamu sebutkan. Tambah/ubah sesuai
        // kebutuhan lewat halaman HRD nanti, atau seeder terpisah.
        DB::table('divisi')->insert([
            ['grup_id' => 1, 'nama_divisi' => 'Holding', 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['grup_id' => 1, 'nama_divisi' => 'Tambak', 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('divisi');
    }
};
