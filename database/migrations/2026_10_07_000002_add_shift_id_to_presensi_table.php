<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Snapshot shift yang berlaku saat scan masuk. presensi.tanggal berarti
     * TANGGAL KERJA (tanggal shift mulai), bukan tanggal kalender scan,
     * supaya shift malam yang pulang lewat 00:00 tetap satu baris.
     * Nullable: borongan dan data lama bisa tanpa shift.
     */
    public function up(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable()->after('karyawan_id')
                ->constrained('shift')->nullOnDelete();
        });

        // Data lama: bekukan ke shift default karyawan saat ini, supaya rekap
        // lama tidak ikut berubah kalau shift_id karyawan diganti nanti.
        DB::statement('UPDATE presensi SET shift_id = (SELECT shift_id FROM karyawan WHERE karyawan.id = presensi.karyawan_id)');
    }

    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shift_id');
        });
    }
};