<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->string('nama_jalan_masuk', 255)->nullable()->after('status_radius_masuk');
            $table->string('nama_jalan_pulang', 255)->nullable()->after('status_radius_pulang');
        });
    }

    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropColumn(['nama_jalan_masuk', 'nama_jalan_pulang']);
        });
    }
};
