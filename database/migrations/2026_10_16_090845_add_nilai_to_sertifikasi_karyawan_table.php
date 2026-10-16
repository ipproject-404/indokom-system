<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sertifikasi_karyawan', function (Blueprint $table) {
            $table->decimal('nilai_pretest', 5, 2)
                  ->nullable()
                  ->after('nomor_sertifikat')
                  ->comment('Nilai pretest (0-10)');

            $table->decimal('nilai_postest', 5, 2)
                  ->nullable()
                  ->after('nilai_pretest')
                  ->comment('Nilai postest (0-10). >= 8 = lulus');
        });
    }

    public function down(): void
    {
        Schema::table('sertifikasi_karyawan', function (Blueprint $table) {
            $table->dropColumn(['nilai_pretest', 'nilai_postest']);
        });
    }
};