<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Master tipe hubungan kerja & skema gaji. Dibuat tabel (bukan enum)
     * supaya nambah tipe baru nanti tinggal tambah baris, tanpa migration.
     *
     * dasar_absensi menentukan bagaimana kehadiran tipe ini dihitung:
     * - 'jadwal'  : ikut jam kerja/shift standar (dipakai bulanan, bulanan_kontrak)
     *   -> bisa kena status "Terlambat", wajib absen tiap hari kerja
     * - 'jam'     : dihitung dari total jam kerja aktual, tidak ada jadwal
     *   tetap (borongan_jam, karena "masuk tergantung barang di pabrik")
     *   -> tidak dihitung "Terlambat"
     * - 'hasil'   : tidak dihitung dari jam sama sekali, gajinya dari
     *   transaksi_produksi (borongan_hasil)
     */
    public function up(): void
    {
        Schema::create('tipe_karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama', 50);
            $table->enum('dasar_absensi', ['jadwal', 'jam', 'hasil']);
            $table->enum('periode_gaji', ['bulanan', 'mingguan', 'harian'])->default('bulanan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        DB::table('tipe_karyawan')->insert([
            ['kode' => 'bulanan', 'nama' => 'Bulanan (Tetap)', 'dasar_absensi' => 'jadwal', 'periode_gaji' => 'bulanan', 'keterangan' => 'Gaji tetap per bulan, ikut jadwal/shift standar.', 'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'bulanan_kontrak', 'nama' => 'Bulanan Kontrak', 'dasar_absensi' => 'jadwal', 'periode_gaji' => 'bulanan', 'keterangan' => 'Sama seperti bulanan, tapi berstatus kontrak (ada tanggal berakhir).', 'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'borongan_jam', 'nama' => 'Borongan Jam', 'dasar_absensi' => 'jam', 'periode_gaji' => 'harian', 'keterangan' => 'Gaji dihitung dari total jam kerja aktual, jam masuk mengikuti ketersediaan barang.', 'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'borongan_hasil', 'nama' => 'Borongan Hasil', 'dasar_absensi' => 'hasil', 'periode_gaji' => 'harian', 'keterangan' => 'Gaji dari hasil kerja (kg dikali tarif), lihat tabel transaksi_produksi & master_tarif.', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipe_karyawan');
    }
};
