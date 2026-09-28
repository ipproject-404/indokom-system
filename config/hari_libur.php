<?php

/*
|--------------------------------------------------------------------------
| Hari Libur (tanggal merah)
|--------------------------------------------------------------------------
| Sumber: SKB 3 Menteri No. 1497/2025, 2/2025, 5/2025 tentang Hari Libur
| Nasional dan Cuti Bersama Tahun 2026.
|
| Format: 'YYYY-MM-DD' => 'Nama hari libur'
|
| PENTING: tiap tahun pemerintah menerbitkan SKB baru. Tambahkan tanggal
| tahun berikutnya di bawah ini sebelum tahun tersebut berjalan, kalau
| tidak, tanggal merahnya akan terhitung "Tidak Hadir" di rekap.
|
| Hari Minggu otomatis libur (dihitung di RekapAbsensiController),
| jadi tidak perlu ditulis di sini.
*/

return [

    'libur_nasional' => [
        '2026-01-01' => 'Tahun Baru 2026 Masehi',
        '2026-01-16' => 'Isra Mikraj Nabi Muhammad saw.',
        '2026-02-17' => 'Tahun Baru Imlek 2577 Kongzili',
        '2026-03-19' => 'Hari Suci Nyepi (Tahun Baru Saka 1948)',
        '2026-03-21' => 'Idulfitri 1447 H',
        '2026-03-22' => 'Idulfitri 1447 H',
        '2026-04-03' => 'Wafat Yesus Kristus',
        '2026-04-05' => 'Kebangkitan Yesus Kristus (Paskah)',
        '2026-05-01' => 'Hari Buruh Internasional',
        '2026-05-14' => 'Kenaikan Yesus Kristus',
        '2026-05-27' => 'Iduladha 1447 H',
        '2026-05-31' => 'Hari Raya Waisak 2570 BE',
        '2026-06-01' => 'Hari Lahir Pancasila',
        '2026-06-16' => 'Tahun Baru Islam 1 Muharam 1448 H',
        '2026-08-17' => 'Proklamasi Kemerdekaan RI',
        '2026-08-25' => 'Maulid Nabi Muhammad saw.',
        '2026-12-25' => 'Kelahiran Yesus Kristus',
    ],

    /*
    | Cuti bersama BUKAN tanggal merah dan bagi swasta pelaksanaannya diatur
    | perusahaan masing-masing. Default: tidak dianggap libur. Ubah jadi true
    | kalau perusahaan ikut meliburkan cuti bersama.
    */
    'anggap_cuti_bersama_libur' => false,

    'cuti_bersama' => [
        '2026-02-16' => 'Cuti Bersama Tahun Baru Imlek',
        '2026-03-18' => 'Cuti Bersama Hari Suci Nyepi',
        '2026-03-20' => 'Cuti Bersama Idulfitri',
        '2026-03-23' => 'Cuti Bersama Idulfitri',
        '2026-03-24' => 'Cuti Bersama Idulfitri',
        '2026-05-15' => 'Cuti Bersama Kenaikan Yesus Kristus',
        '2026-05-28' => 'Cuti Bersama Iduladha',
        '2026-12-24' => 'Cuti Bersama Kelahiran Yesus Kristus',
    ],

];