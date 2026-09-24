<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->decimal('latitude_masuk', 10, 7)->nullable()->after('jam_masuk');
            $table->decimal('longitude_masuk', 10, 7)->nullable()->after('latitude_masuk');
            $table->decimal('latitude_pulang', 10, 7)->nullable()->after('jam_pulang');
            $table->decimal('longitude_pulang', 10, 7)->nullable()->after('latitude_pulang');
        });
    }

    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropColumn(['latitude_masuk', 'longitude_masuk', 'latitude_pulang', 'longitude_pulang']);
        });
    }
};
