<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * departemen & jabatan sengaja TIDAK diduplikasi per PT (lihat catatan
     * di migration divisi) -- PT karyawan cukup ditentukan di sini, di
     * baris karyawan itu sendiri.
     *
     * perusahaan_id & tipe_karyawan_id dibackfill ke nilai default supaya
     * data karyawan yang sudah ada tetap valid, lalu dikunci NOT NULL.
     * shift_id sengaja dibiarkan nullable seterusnya, karena borongan_jam
     * dan borongan_hasil biasanya tidak punya jadwal tetap.
     */
    public function up(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->foreignId('perusahaan_id')->nullable()->after('id')->constrained('perusahaan');
            $table->foreignId('tipe_karyawan_id')->nullable()->after('departemen_id')->constrained('tipe_karyawan');
            $table->foreignId('shift_id')->nullable()->after('tipe_karyawan_id')->constrained('shift');
        });

        // Backfill sementara: semua karyawan lama dianggap PT-A / tipe
        // bulanan / shift Reguler. WAJIB dicek & dikoreksi manual lewat
        // halaman HRD setelah migration ini jalan -- ini cuma nilai aman
        // supaya tidak ada baris NULL yang lolos ke NOT NULL di bawah.
        DB::table('karyawan')->whereNull('perusahaan_id')->update(['perusahaan_id' => 1]);
        DB::table('karyawan')->whereNull('tipe_karyawan_id')->update(['tipe_karyawan_id' => 1]);
        DB::table('karyawan')->whereNull('shift_id')->update(['shift_id' => 1]);

        Schema::table('karyawan', function (Blueprint $table) {
            $table->foreignId('perusahaan_id')->nullable(false)->change();
            $table->foreignId('tipe_karyawan_id')->nullable(false)->change();
            // shift_id TETAP nullable, lihat catatan di atas.
        });
    }

    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('perusahaan_id');
            $table->dropConstrainedForeignId('tipe_karyawan_id');
            $table->dropConstrainedForeignId('shift_id');
        });
    }
};
