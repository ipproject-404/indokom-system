<?php

// ============================================================
// Konfigurasi lokasi kantor -- SATU-SATUNYA tempat nilai ini
// diatur. Dipakai baik oleh JavaScript (halaman scan absensi,
// buat ditampilkan ke karyawan) maupun PHP (controller, buat
// validasi jarak & radius yang bisa diandalkan).
//
// Cara ganti nilainya: JANGAN edit file ini langsung -- edit
// value KANTOR_NAMA, KANTOR_LATITUDE, dst di file .env kamu.
// (Kalau belum ada barisnya di .env, tambahkan sendiri.)
// ============================================================

return [
    'nama' => env('KANTOR_NAMA', 'PT Nama Perusahaan Anda'),
    'latitude' => (float) env('KANTOR_LATITUDE', -6.123456),
    'longitude' => (float) env('KANTOR_LONGITUDE', 106.123456),
    'radius_meter' => (int) env('KANTOR_RADIUS_METER', 100),
];
