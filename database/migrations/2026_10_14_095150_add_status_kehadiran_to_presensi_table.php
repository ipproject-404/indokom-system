<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambah kolom untuk fitur Input Manual HRD:
     * - status_kehadiran (sakit/izin/cuti/alpa/dinas_luar/libur_nasional)
     * - keterangan_manual
     * 
     * Plus modifikasi:
     * - jam_masuk boleh NULL (untuk input manual, bukan QR)
     * - metode_presensi + opsi MANUAL_HRD
     */
    public function up(): void
    {
        // ==========================================
        // STEP 1: Tambah kolom baru (aman, tidak hapus data)
        // ==========================================
        Schema::table('presensi', function (Blueprint $table) {
            // Cek dulu biar tidak error kalau sudah ada
            if (!Schema::hasColumn('presensi', 'status_kehadiran')) {
                $table->enum('status_kehadiran', [
                    'hadir',
                    'terlambat',
                    'sakit',
                    'izin',
                    'cuti',
                    'alpa',
                    'dinas_luar',
                    'libur_nasional',
                ])
                ->nullable()
                ->after('jam_pulang')
                ->comment('Status kehadiran manual dari HRD');
            }

            if (!Schema::hasColumn('presensi', 'keterangan_manual')) {
                $table->text('keterangan_manual')
                    ->nullable()
                    ->after('catatan')
                    ->comment('Catatan HRD saat input manual');
            }
        });

        // ==========================================
        // STEP 2: Ubah jam_masuk jadi nullable
        // (untuk input manual sakit/izin/cuti yang tidak punya jam masuk)
        // ==========================================
        DB::statement("ALTER TABLE `presensi` MODIFY `jam_masuk` TIME NULL");

        // ==========================================
        // STEP 3: Tambah opsi MANUAL_HRD ke metode_presensi
        // ==========================================
        DB::statement("
            ALTER TABLE `presensi` 
            MODIFY `metode_presensi` 
            ENUM('QR_KARYAWAN','QR_ADMIN','MANUAL_HRD') 
            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci 
            NOT NULL DEFAULT 'QR_KARYAWAN'
        ");
    }

    /**
     * Rollback: kembalikan seperti semula
     */
    public function down(): void
    {
        // Hapus kolom baru
        Schema::table('presensi', function (Blueprint $table) {
            if (Schema::hasColumn('presensi', 'status_kehadiran')) {
                $table->dropColumn('status_kehadiran');
            }
            if (Schema::hasColumn('presensi', 'keterangan_manual')) {
                $table->dropColumn('keterangan_manual');
            }
        });

        // Kembalikan enum metode_presensi
        DB::statement("
            ALTER TABLE `presensi` 
            MODIFY `metode_presensi` 
            ENUM('QR_KARYAWAN','QR_ADMIN') 
            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci 
            NOT NULL
        ");

        // Kembalikan jam_masuk jadi NOT NULL
        DB::statement("ALTER TABLE `presensi` MODIFY `jam_masuk` TIME NOT NULL");
    }
};