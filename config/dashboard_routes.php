<?php

// ============================================================
// Peta "role -> nama route dashboard".
//
// SATU-SATUNYA tempat yang perlu diubah kalau ada role baru.
// AuthController & QrController sama-sama baca dari sini lewat
// User::routeDashboard(), jadi tidak perlu ubah kode di 2 tempat
// lagi tiap ada role baru -- cukup tambah 1 baris di sini.
//
// Kalau nambah role baru, jangan lupa juga:
// 1. Tambah value baru di enum kolom `role` tabel users (migration)
// 2. Buat route dashboard-nya sendiri di routes/web.php
// 3. Baru tambahkan pemetaannya di bawah ini
// ============================================================

return [
    'karyawan' => 'dashboard.karyawan',
    'hrd' => 'dashboard.hrd',

    // Contoh kalau nanti nambah role baru, tinggal buka komentar
    // dan sesuaikan nama route-nya:
    // 'verifikasi' => 'dashboard.verifikasi',
    // 'admin_master' => 'dashboard.admin-master',
];