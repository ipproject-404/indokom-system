<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lembur sekarang diajukan SEBELUM selesai, jadi jam selesai, durasi,
     * dan upah belum ada saat baris dibuat -- semuanya jadi nullable.
     * Diisi saat scan pulang (jam_selesai_scan), lalu dikonfirmasi/dikoreksi
     * payroll setelah mencocokkan surat lembur.
     * jam_selesai = nilai FINAL yang dipakai hitung upah.
     */
    public function up(): void
    {
        Schema::table('lembur', function (Blueprint $table) {
            $table->time('jam_mulai')->nullable()->change();
            $table->time('jam_selesai')->nullable()->change();
            $table->decimal('durasi_jam', 4, 2)->nullable()->change();
            $table->decimal('tarif_per_jam', 12, 2)->nullable()->change();
            $table->decimal('total_upah', 12, 2)->nullable()->change();
        });

        Schema::table('lembur', function (Blueprint $table) {
            $table->foreignId('presensi_id')->nullable()->after('karyawan_id')
                ->constrained('presensi')->nullOnDelete();
            $table->time('jam_selesai_scan')->nullable()->after('jam_selesai')
                ->comment('Jam pulang asli dari scan, tidak berubah walau payroll koreksi');
            $table->string('catatan_payroll', 255)->nullable()->after('catatan');
            $table->dateTime('waktu_disetujui')->nullable()->after('disetujui_oleh')
                ->comment('Waktu payroll memproses (setuju/tolak)');
        });
    }

    /**
     * Kolom yang tadinya NOT NULL sengaja tidak dikembalikan, karena baris
     * pengajuan yang belum selesai pasti berisi NULL.
     */
    public function down(): void
    {
        Schema::table('lembur', function (Blueprint $table) {
            $table->dropConstrainedForeignId('presensi_id');
            $table->dropColumn(['jam_selesai_scan', 'catatan_payroll', 'waktu_disetujui']);
        });
    }
};