<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom ke tabel lembur:
     * - kelompok: reguler atau insentif (ditentukan saat acc berdasarkan jabatan)
     * - durasi_final_jam: durasi setelah pembulatan 15 menit
     * - memenuhi_syarat: false jika insentif < 4 jam (payroll bisa override saat acc)
     */
    public function up(): void
    {
        Schema::table('lembur', function (Blueprint $table) {
            $table->enum('kelompok', ['reguler', 'insentif'])->nullable()->after('status');
            $table->decimal('durasi_final_jam', 5, 2)->nullable()->after('durasi_jam')
                ->comment('Durasi setelah pembulatan 15 menit');
            $table->boolean('memenuhi_syarat')->nullable()->after('durasi_final_jam')
                ->comment('False jika insentif < 4 jam');
        });
    }

    public function down(): void
    {
        Schema::table('lembur', function (Blueprint $table) {
            $table->dropColumn(['kelompok', 'durasi_final_jam', 'memenuhi_syarat']);
        });
    }
};