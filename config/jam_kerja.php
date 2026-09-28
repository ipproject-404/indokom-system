<?php

/*
|--------------------------------------------------------------------------
| Jam Kerja
|--------------------------------------------------------------------------
| Dipakai untuk menentukan keterlambatan & pulang lebih awal di rekap absensi.
| Semua jam dalam WIB -- pastikan APP_TIMEZONE=Asia/Jakarta di file .env,
| kalau tidak, jam yang tercatat saat scan QR akan bergeser dan
| keterlambatan salah hitung.
|
| Kunci 'jam_pulang' memakai nomor hari ISO: 1=Senin ... 6=Sabtu, 7=Minggu.
| Hari yang tidak ada di daftar ini (Minggu) tidak punya jadwal kerja.
*/

return [

    'jam_masuk' => '08:00',

    // Masuk sampai jam_masuk + toleransi (08:05) belum dianggap terlambat.
    // Mulai 08:06 dianggap terlambat, dihitung dari 08:00.
    'toleransi_menit' => 5,

    'jam_pulang' => [
        1 => '16:00', // Senin
        2 => '16:00', // Selasa
        3 => '16:00', // Rabu
        4 => '16:00', // Kamis
        5 => '16:30', // Jumat
        6 => '14:00', // Sabtu
    ],

];