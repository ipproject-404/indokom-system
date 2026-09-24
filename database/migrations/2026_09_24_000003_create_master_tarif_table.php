<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_tarif', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pekerjaan', 100);
            $table->decimal('harga_per_satuan', 12, 2);
            $table->enum('tipe_tarif', ['per_kg', 'per_jam']);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_tarif');
    }
};
