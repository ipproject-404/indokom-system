<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->string('alamat_masuk', 255)->nullable()->after('longitude_masuk');
            $table->decimal('jarak_masuk_meter', 8, 2)->nullable()->after('alamat_masuk');
            $table->enum('status_radius_masuk', ['dalam_radius', 'luar_radius'])->nullable()->after('jarak_masuk_meter');

            $table->string('alamat_pulang', 255)->nullable()->after('longitude_pulang');
            $table->decimal('jarak_pulang_meter', 8, 2)->nullable()->after('alamat_pulang');
            $table->enum('status_radius_pulang', ['dalam_radius', 'luar_radius'])->nullable()->after('jarak_pulang_meter');
        });
    }

    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropColumn([
                'alamat_masuk', 'jarak_masuk_meter', 'status_radius_masuk',
                'alamat_pulang', 'jarak_pulang_meter', 'status_radius_pulang',
            ]);
        });
    }
};
