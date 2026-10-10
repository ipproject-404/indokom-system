<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Penanda karyawan yang boleh mengajukan lembur (engineering, tambak, mesin, soking, dll). */
    public function up(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->boolean('boleh_lembur')->default(false)->after('pakai_jadwal_shift');
        });
    }

    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn('boleh_lembur');
        });
    }
};