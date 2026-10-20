<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peserta_pelatihan', function (Blueprint $table) {
            $table->enum('status_kehadiran', ['belum_hadir', 'hadir'])->default('belum_hadir')->after('catatan');
            $table->timestamp('waktu_hadir')->nullable()->after('status_kehadiran');
        });
    }

    public function down(): void
    {
        Schema::table('peserta_pelatihan', function (Blueprint $table) {
            $table->dropColumn(['status_kehadiran', 'waktu_hadir']);
        });
    }
};