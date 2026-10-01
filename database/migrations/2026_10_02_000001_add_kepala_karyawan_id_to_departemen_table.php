<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyimpan SIAPA kepala suatu departemen sebagai data, bukan sebagai
     * role baru di tabel users. Wewenang "boleh atur shift anak buah"
     * dicek lewat: apakah karyawan_id user yang login = kepala_karyawan_id
     * di salah satu baris departemen -- bukan lewat pengecekan role string.
     *
     * Ini sengaja dipilih supaya ke depan, kalau ada jenis wewenang lain
     * (kepala shift, kepala regu, dll), pola yang sama dipakai lagi tanpa
     * menambah nilai baru di enum role.
     *
     * Nullable selamanya -- banyak departemen mungkin belum punya kepala
     * yang ditunjuk di sistem ini (masih dipegang manual / belum diinput).
     */
    public function up(): void
    {
        Schema::table('departemen', function (Blueprint $table) {
            $table->foreignId('kepala_karyawan_id')->nullable()->after('divisi_id')
                ->constrained('karyawan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('departemen', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kepala_karyawan_id');
        });
    }
};
