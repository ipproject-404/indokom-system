<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Divisi milik GRUP (bukan perusahaan): ada 2 PT tapi struktur
     * organisasinya masih satu. PT karyawan ditentukan di karyawan.perusahaan_id.
     * Migration create_divisi_table awalnya membuat perusahaan_id; ini
     * meluruskannya. Kalau grup_id sudah ada, tidak melakukan apa-apa.
     */
    public function up(): void
    {
        if (Schema::hasColumn('divisi', 'grup_id')) {
            return;
        }

        Schema::table('divisi', function (Blueprint $table) {
            $table->foreignId('grup_id')->nullable()->after('id')->constrained('grup');
        });

        DB::statement('UPDATE divisi SET grup_id = (SELECT grup_id FROM perusahaan WHERE perusahaan.id = divisi.perusahaan_id)');

        Schema::table('divisi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('perusahaan_id');
            $table->foreignId('grup_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('divisi', 'perusahaan_id')) {
            return;
        }

        Schema::table('divisi', function (Blueprint $table) {
            $table->foreignId('perusahaan_id')->nullable()->after('id')->constrained('perusahaan');
        });

        DB::statement('UPDATE divisi SET perusahaan_id = (SELECT MIN(id) FROM perusahaan WHERE perusahaan.grup_id = divisi.grup_id)');

        Schema::table('divisi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('grup_id');
            $table->foreignId('perusahaan_id')->nullable(false)->change();
        });
    }
};