<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lembur', function (Blueprint $table) {
            $table->decimal('estimasi_jam', 4, 1)->nullable()->after('catatan')
                ->comment('Estimasi durasi lembur dari karyawan, hanya info untuk payroll');
        });
    }

    public function down(): void
    {
        Schema::table('lembur', function (Blueprint $table) {
            $table->dropColumn('estimasi_jam');
        });
    }
};