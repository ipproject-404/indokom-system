<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom baru sesuai desain ERD
            $table->foreignId('karyawan_id')->nullable()->after('id')->constrained('karyawan');
            $table->string('username', 50)->unique()->after('karyawan_id');
            $table->enum('role', ['hrd', 'karyawan', 'verifikasi', 'admin_master'])->after('password');
            $table->enum('status', ['aktif', 'tidak'])->default('aktif')->after('role');
            $table->dateTime('last_login')->nullable()->after('status');

            // Kolom bawaan Laravel yang tidak dipakai di desain ini -- dihapus.
            // Kalau ada error saat drop kolom, jalankan dulu:
            //   composer require doctrine/dbal --dev
            $table->dropColumn(['name', 'email', 'email_verified_at', 'remember_token']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['karyawan_id']);
            $table->dropColumn(['karyawan_id', 'username', 'role', 'status', 'last_login']);

            $table->string('name')->after('id');
            $table->string('email')->unique()->after('name');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
        });
    }
};
