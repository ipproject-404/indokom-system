<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_produksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan');
            $table->foreignId('tarif_id')->constrained('master_tarif');
            $table->decimal('berat_kg', 10, 2)->nullable()->comment('Diisi kalau tarif bertipe per_kg');
            $table->decimal('total_upah', 12, 2);
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_produksi');
    }
};
