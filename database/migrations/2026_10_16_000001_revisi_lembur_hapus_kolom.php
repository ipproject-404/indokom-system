<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus kolom yang tidak dipakai lagi
        Schema::table('lembur', function (Blueprint $table) {
            $table->dropColumn(['kelompok', 'memenuhi_syarat', 'estimasi_jam']);
        });

        // Hapus boleh_lembur dari karyawan
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn('boleh_lembur');
        });

        // Hapus boleh_lembur dari departemen
        Schema::table('departemen', function (Blueprint $table) {
            $table->dropColumn('boleh_lembur');
        });
    }

    public function down(): void
    {
        Schema::table('lembur', function (Blueprint $table) {
            $table->enum('kelompok', ['reguler', 'insentif'])->nullable()->after('status');
            $table->decimal('memenuhi_syarat', 1)->nullable()->after('durasi_final_jam');
            $table->decimal('estimasi_jam', 4, 1)->nullable()->after('catatan');
        });

        Schema::table('karyawan', function (Blueprint $table) {
            $table->boolean('boleh_lembur')->default(false)->after('pakai_jadwal_shift');
        });

        Schema::table('departemen', function (Blueprint $table) {
            $table->boolean('boleh_lembur')->default(false)->after('kepala_karyawan_id');
        });
    }
};