<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel master tarif lembur per kelompok (reguler/insentif) per departemen.
     * Satu departemen bisa punya beberapa baris tarif (bertingkat per jam).
     * urutan_jam = jam ke berapa (1, 2, 3, dst), upah_per_jam = nominal.
     * Jika departemen_id NULL = tarif default berlaku untuk semua departemen
     * yang belum punya tarif sendiri.
     */
    public function up(): void
    {
        Schema::create('tarif_lembur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departemen_id')->nullable()->constrained('departemen')->nullOnDelete();
            $table->enum('kelompok', ['reguler', 'insentif'])->comment('reguler=bawah asisten, insentif=asisten ke atas');
            $table->unsignedTinyInteger('urutan_jam')->comment('Jam ke-1, ke-2, dst');
            $table->decimal('upah_per_jam', 12, 2);
            $table->timestamps();

            $table->unique(['departemen_id', 'kelompok', 'urutan_jam'], 'unik_tarif_lembur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif_lembur');
    }
};