<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class IndokomDummySeeder extends Seeder
{
    /**
     * Jalankan dengan: php artisan db:seed --class=IndokomDummySeeder
     * (atau panggil dari DatabaseSeeder supaya ikut ke-run pas `php artisan migrate:fresh --seed`)
     */
    public function run(): void
    {
        // ------------------------------------------------------------
        // departemen
        // ------------------------------------------------------------
        DB::table('departemen')->insert([
            ['id' => 1, 'nama_departemen' => 'Produksi', 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama_departemen' => 'HRD', 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama_departemen' => 'Gudang', 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nama_departemen' => 'Quality Control', 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nama_departemen' => 'Keuangan', 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ------------------------------------------------------------
        // jabatan
        // ------------------------------------------------------------
        DB::table('jabatan')->insert([
            ['id' => 1, 'nama_jabatan' => 'Operator Kupas Udang', 'departemen_id' => 1, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama_jabatan' => 'Kepala Regu Produksi', 'departemen_id' => 1, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama_jabatan' => 'Staff HRD', 'departemen_id' => 2, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nama_jabatan' => 'Admin Verifikasi Lapangan', 'departemen_id' => 2, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nama_jabatan' => 'Staff Gudang', 'departemen_id' => 3, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'nama_jabatan' => 'Staff Quality Control', 'departemen_id' => 4, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ------------------------------------------------------------
        // master_tarif
        // ------------------------------------------------------------
        DB::table('master_tarif')->insert([
            ['id' => 1, 'nama_pekerjaan' => 'Kupas Udang Size S', 'harga_per_satuan' => 2000.00, 'tipe_tarif' => 'per_kg', 'status_aktif' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama_pekerjaan' => 'Kupas Udang Size M', 'harga_per_satuan' => 1500.00, 'tipe_tarif' => 'per_kg', 'status_aktif' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama_pekerjaan' => 'Kupas Udang Size L', 'harga_per_satuan' => 1200.00, 'tipe_tarif' => 'per_kg', 'status_aktif' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nama_pekerjaan' => 'Sortir Kualitas', 'harga_per_satuan' => 15000.00, 'tipe_tarif' => 'per_jam', 'status_aktif' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nama_pekerjaan' => 'Kupas Udang Size XL', 'harga_per_satuan' => 1000.00, 'tipe_tarif' => 'per_kg', 'status_aktif' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ------------------------------------------------------------
        // karyawan
        // ------------------------------------------------------------
        DB::table('karyawan')->insert([
            ['id' => 1, 'nik_ktp' => '3201011990010001', 'nik_kerja' => 'KRY-0001', 'barcode_uid' => 'QR-KRY-0001', 'nama_lengkap' => 'Siti Rahayu', 'tempat_lahir' => 'Lampung', 'tanggal_lahir' => '1992-03-14', 'jenis_kelamin' => 'P', 'alamat' => 'Jl. Merdeka No. 12, Lampung', 'no_hp' => '081234560001', 'pendidikan' => 'SMA', 'jabatan_id' => 1, 'departemen_id' => 1, 'tanggal_masuk' => '2022-01-10', 'tanggal_keluar' => null, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nik_ktp' => '3201021988020002', 'nik_kerja' => 'KRY-0002', 'barcode_uid' => 'QR-KRY-0002', 'nama_lengkap' => 'Budi Santoso', 'tempat_lahir' => 'Lampung', 'tanggal_lahir' => '1988-07-22', 'jenis_kelamin' => 'L', 'alamat' => 'Jl. Sudirman No. 5, Lampung', 'no_hp' => '081234560002', 'pendidikan' => 'SMA', 'jabatan_id' => 2, 'departemen_id' => 1, 'tanggal_masuk' => '2020-05-03', 'tanggal_keluar' => null, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nik_ktp' => '3201031995030003', 'nik_kerja' => 'KRY-0003', 'barcode_uid' => 'QR-KRY-0003', 'nama_lengkap' => 'Dewi Kurniawati', 'tempat_lahir' => 'Metro', 'tanggal_lahir' => '1995-11-02', 'jenis_kelamin' => 'P', 'alamat' => 'Jl. Kartini No. 8, Metro', 'no_hp' => '081234560003', 'pendidikan' => 'SMP', 'jabatan_id' => 1, 'departemen_id' => 1, 'tanggal_masuk' => '2023-02-20', 'tanggal_keluar' => null, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nik_ktp' => '3201041993040004', 'nik_kerja' => 'KRY-0004', 'barcode_uid' => 'QR-KRY-0004', 'nama_lengkap' => 'Rina Wijaya', 'tempat_lahir' => 'Lampung', 'tanggal_lahir' => '1993-01-18', 'jenis_kelamin' => 'P', 'alamat' => 'Jl. Diponegoro No. 3, Lampung', 'no_hp' => '081234560004', 'pendidikan' => 'S1', 'jabatan_id' => 3, 'departemen_id' => 2, 'tanggal_masuk' => '2021-08-15', 'tanggal_keluar' => null, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nik_ktp' => '3201051991050005', 'nik_kerja' => 'KRY-0005', 'barcode_uid' => 'QR-KRY-0005', 'nama_lengkap' => 'Agus Prasetyo', 'tempat_lahir' => 'Lampung', 'tanggal_lahir' => '1991-09-09', 'jenis_kelamin' => 'L', 'alamat' => 'Jl. Gajah Mada No. 20, Lampung', 'no_hp' => '081234560005', 'pendidikan' => 'S1', 'jabatan_id' => 4, 'departemen_id' => 2, 'tanggal_masuk' => '2019-11-01', 'tanggal_keluar' => null, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'nik_ktp' => '3201061996060006', 'nik_kerja' => 'KRY-0006', 'barcode_uid' => 'QR-KRY-0006', 'nama_lengkap' => 'Fitriani', 'tempat_lahir' => 'Bandar Lampung', 'tanggal_lahir' => '1996-04-25', 'jenis_kelamin' => 'P', 'alamat' => 'Jl. Teuku Umar No. 15, Bandar Lampung', 'no_hp' => '081234560006', 'pendidikan' => 'SMA', 'jabatan_id' => 1, 'departemen_id' => 1, 'tanggal_masuk' => '2023-06-12', 'tanggal_keluar' => null, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'nik_ktp' => '3201071989070007', 'nik_kerja' => 'KRY-0007', 'barcode_uid' => 'QR-KRY-0007', 'nama_lengkap' => 'Hendra Gunawan', 'tempat_lahir' => 'Lampung', 'tanggal_lahir' => '1989-12-30', 'jenis_kelamin' => 'L', 'alamat' => 'Jl. Ahmad Yani No. 7, Lampung', 'no_hp' => '081234560007', 'pendidikan' => 'SMA', 'jabatan_id' => 5, 'departemen_id' => 3, 'tanggal_masuk' => '2020-02-17', 'tanggal_keluar' => null, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'nik_ktp' => '3201081994080008', 'nik_kerja' => 'KRY-0008', 'barcode_uid' => 'QR-KRY-0008', 'nama_lengkap' => 'Yuni Astuti', 'tempat_lahir' => 'Metro', 'tanggal_lahir' => '1994-06-05', 'jenis_kelamin' => 'P', 'alamat' => 'Jl. Cut Nyak Dien No. 9, Metro', 'no_hp' => '081234560008', 'pendidikan' => 'D3', 'jabatan_id' => 6, 'departemen_id' => 4, 'tanggal_masuk' => '2022-09-01', 'tanggal_keluar' => '2026-08-31', 'status' => 'nonaktif', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ------------------------------------------------------------
        // users
        // Password di-hash sungguhan lewat Hash::make() -- semua akun
        // dummy ini passwordnya "password123", cuma untuk testing lokal.
        // ------------------------------------------------------------
        DB::table('users')->insert([
            ['id' => 1, 'karyawan_id' => 1, 'username' => 'siti.rahayu', 'password' => Hash::make('password123'), 'role' => 'karyawan', 'status' => 'aktif', 'last_login' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'karyawan_id' => 2, 'username' => 'budi.santoso', 'password' => Hash::make('password123'), 'role' => 'karyawan', 'status' => 'aktif', 'last_login' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'karyawan_id' => 3, 'username' => 'dewi.kurniawati', 'password' => Hash::make('password123'), 'role' => 'karyawan', 'status' => 'aktif', 'last_login' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'karyawan_id' => 4, 'username' => 'rina.hrd', 'password' => Hash::make('password123'), 'role' => 'hrd', 'status' => 'aktif', 'last_login' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'karyawan_id' => 5, 'username' => 'agus.verifikasi', 'password' => Hash::make('password123'), 'role' => 'verifikasi', 'status' => 'aktif', 'last_login' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'karyawan_id' => null, 'username' => 'admin.master', 'password' => Hash::make('password123'), 'role' => 'admin_master', 'status' => 'aktif', 'last_login' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'karyawan_id' => 6, 'username' => 'fitriani', 'password' => Hash::make('password123'), 'role' => 'karyawan', 'status' => 'aktif', 'last_login' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'karyawan_id' => null, 'username' => 'wawan.verifikasi', 'password' => Hash::make('password123'), 'role' => 'verifikasi', 'status' => 'aktif', 'last_login' => now(), 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ------------------------------------------------------------
        // presensi
        // ------------------------------------------------------------
        DB::table('presensi')->insert([
            ['id' => 1, 'karyawan_id' => 1, 'tanggal' => '2026-09-22', 'jam_masuk' => '07:02:00', 'jam_pulang' => '16:05:00', 'metode_presensi' => 'QR_KARYAWAN', 'status_verifikasi' => 'disetujui', 'diverifikasi_oleh' => 5, 'waktu_verifikasi' => '2026-09-22 09:00:00', 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'karyawan_id' => 2, 'tanggal' => '2026-09-22', 'jam_masuk' => '06:55:00', 'jam_pulang' => '16:10:00', 'metode_presensi' => 'QR_KARYAWAN', 'status_verifikasi' => 'disetujui', 'diverifikasi_oleh' => 5, 'waktu_verifikasi' => '2026-09-22 09:05:00', 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'karyawan_id' => 3, 'tanggal' => '2026-09-22', 'jam_masuk' => '07:15:00', 'jam_pulang' => '16:00:00', 'metode_presensi' => 'QR_KARYAWAN', 'status_verifikasi' => 'disetujui', 'diverifikasi_oleh' => 8, 'waktu_verifikasi' => '2026-09-22 10:00:00', 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'karyawan_id' => 6, 'tanggal' => '2026-09-22', 'jam_masuk' => '07:20:00', 'jam_pulang' => null, 'metode_presensi' => 'QR_KARYAWAN', 'status_verifikasi' => 'menunggu', 'diverifikasi_oleh' => null, 'waktu_verifikasi' => null, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'karyawan_id' => 1, 'tanggal' => '2026-09-23', 'jam_masuk' => '07:00:00', 'jam_pulang' => '16:02:00', 'metode_presensi' => 'QR_KARYAWAN', 'status_verifikasi' => 'disetujui', 'diverifikasi_oleh' => 5, 'waktu_verifikasi' => '2026-09-23 09:00:00', 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'karyawan_id' => 2, 'tanggal' => '2026-09-23', 'jam_masuk' => '08:10:00', 'jam_pulang' => '16:08:00', 'metode_presensi' => 'QR_ADMIN', 'status_verifikasi' => 'ditolak', 'diverifikasi_oleh' => 5, 'waktu_verifikasi' => '2026-09-23 09:15:00', 'catatan' => 'QR karyawan rusak, discan manual oleh admin, jam masuk perlu dikonfirmasi ulang', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'karyawan_id' => 3, 'tanggal' => '2026-09-23', 'jam_masuk' => '07:05:00', 'jam_pulang' => null, 'metode_presensi' => 'QR_KARYAWAN', 'status_verifikasi' => 'menunggu', 'diverifikasi_oleh' => null, 'waktu_verifikasi' => null, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'karyawan_id' => 7, 'tanggal' => '2026-09-23', 'jam_masuk' => '07:00:00', 'jam_pulang' => '15:30:00', 'metode_presensi' => 'QR_KARYAWAN', 'status_verifikasi' => 'disetujui', 'diverifikasi_oleh' => 8, 'waktu_verifikasi' => '2026-09-23 09:20:00', 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ------------------------------------------------------------
        // lembur
        // ------------------------------------------------------------
        DB::table('lembur')->insert([
            ['id' => 1, 'karyawan_id' => 1, 'tanggal' => '2026-09-20', 'jam_mulai' => '16:00:00', 'jam_selesai' => '18:00:00', 'durasi_jam' => 2.00, 'tarif_per_jam' => 15000.00, 'total_upah' => 30000.00, 'status' => 'disetujui', 'disetujui_oleh' => 5, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'karyawan_id' => 2, 'tanggal' => '2026-09-20', 'jam_mulai' => '16:00:00', 'jam_selesai' => '19:00:00', 'durasi_jam' => 3.00, 'tarif_per_jam' => 15000.00, 'total_upah' => 45000.00, 'status' => 'disetujui', 'disetujui_oleh' => 5, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'karyawan_id' => 3, 'tanggal' => '2026-09-21', 'jam_mulai' => '16:00:00', 'jam_selesai' => '17:30:00', 'durasi_jam' => 1.50, 'tarif_per_jam' => 15000.00, 'total_upah' => 22500.00, 'status' => 'disetujui', 'disetujui_oleh' => 8, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'karyawan_id' => 6, 'tanggal' => '2026-09-21', 'jam_mulai' => '16:00:00', 'jam_selesai' => '18:00:00', 'durasi_jam' => 2.00, 'tarif_per_jam' => 15000.00, 'total_upah' => 30000.00, 'status' => 'menunggu', 'disetujui_oleh' => null, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'karyawan_id' => 7, 'tanggal' => '2026-09-22', 'jam_mulai' => '15:30:00', 'jam_selesai' => '17:30:00', 'durasi_jam' => 2.00, 'tarif_per_jam' => 15000.00, 'total_upah' => 30000.00, 'status' => 'ditolak', 'disetujui_oleh' => 8, 'catatan' => 'Tidak ada penugasan lembur resmi dari kepala regu', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ------------------------------------------------------------
        // transaksi_produksi
        // ------------------------------------------------------------
        DB::table('transaksi_produksi')->insert([
            ['id' => 1, 'karyawan_id' => 1, 'tarif_id' => 1, 'berat_kg' => 8.50, 'total_upah' => 17000.00, 'tanggal' => '2026-09-22', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'karyawan_id' => 2, 'tarif_id' => 2, 'berat_kg' => 12.00, 'total_upah' => 18000.00, 'tanggal' => '2026-09-22', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'karyawan_id' => 3, 'tarif_id' => 1, 'berat_kg' => 6.75, 'total_upah' => 13500.00, 'tanggal' => '2026-09-22', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'karyawan_id' => 1, 'tarif_id' => 3, 'berat_kg' => 5.00, 'total_upah' => 6000.00, 'tanggal' => '2026-09-23', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'karyawan_id' => 6, 'tarif_id' => 2, 'berat_kg' => 9.25, 'total_upah' => 13875.00, 'tanggal' => '2026-09-22', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'karyawan_id' => 3, 'tarif_id' => 5, 'berat_kg' => 10.00, 'total_upah' => 10000.00, 'tanggal' => '2026-09-23', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ------------------------------------------------------------
        // penggajian
        // ------------------------------------------------------------
        DB::table('penggajian')->insert([
            ['id' => 1, 'karyawan_id' => 1, 'periode_bulan' => 8, 'periode_tahun' => 2026, 'gaji_pokok' => 2500000.00, 'total_lembur' => 120000.00, 'tunjangan' => 100000.00, 'potongan' => 0.00, 'total_gaji' => 2720000.00, 'status' => 'sudah_bayar', 'tanggal_bayar' => '2026-09-05', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'karyawan_id' => 2, 'periode_bulan' => 8, 'periode_tahun' => 2026, 'gaji_pokok' => 2500000.00, 'total_lembur' => 90000.00, 'tunjangan' => 100000.00, 'potongan' => 50000.00, 'total_gaji' => 2640000.00, 'status' => 'sudah_bayar', 'tanggal_bayar' => '2026-09-05', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'karyawan_id' => 3, 'periode_bulan' => 8, 'periode_tahun' => 2026, 'gaji_pokok' => 2300000.00, 'total_lembur' => 45000.00, 'tunjangan' => 100000.00, 'potongan' => 0.00, 'total_gaji' => 2445000.00, 'status' => 'sudah_bayar', 'tanggal_bayar' => '2026-09-05', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'karyawan_id' => 6, 'periode_bulan' => 8, 'periode_tahun' => 2026, 'gaji_pokok' => 2300000.00, 'total_lembur' => 0.00, 'tunjangan' => 100000.00, 'potongan' => 0.00, 'total_gaji' => 2400000.00, 'status' => 'sudah_bayar', 'tanggal_bayar' => '2026-09-05', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'karyawan_id' => 1, 'periode_bulan' => 9, 'periode_tahun' => 2026, 'gaji_pokok' => 2500000.00, 'total_lembur' => 30000.00, 'tunjangan' => 100000.00, 'potongan' => 0.00, 'total_gaji' => 2630000.00, 'status' => 'belum_bayar', 'tanggal_bayar' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
