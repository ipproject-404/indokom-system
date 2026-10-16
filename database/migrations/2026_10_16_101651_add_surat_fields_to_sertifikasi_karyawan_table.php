<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sertifikasi_karyawan', function (Blueprint $table) {
            $table->date('tanggal_surat')->nullable()->after('nomor_sertifikat');
            $table->string('ditandatangani_oleh', 150)->nullable()->after('tanggal_surat');
            $table->string('jabatan_penandatangan', 150)->nullable()->after('ditandatangani_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('sertifikasi_karyawan', function (Blueprint $table) {
            $table->dropColumn(['tanggal_surat', 'ditandatangani_oleh', 'jabatan_penandatangan']);
        });
    }
};