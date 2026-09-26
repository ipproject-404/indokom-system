<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardHrdController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\JabatanDepartemenController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RekapAbsensiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// --- Login manual (username & password) ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Login lewat scan QR (tanpa catat presensi) ---
Route::get('/login/qr', [QrController::class, 'showScanLogin'])->name('login.qr');
Route::post('/login/qr', [QrController::class, 'scanLogin'])->name('login.qr.submit');

// --- Absensi lewat scan QR (login + catat jam masuk/pulang) ---
Route::get('/absensi/scan', [QrController::class, 'showScanAbsensi'])->name('absensi.scan');
Route::post('/absensi/scan', [QrController::class, 'scanAbsensi'])->name('absensi.scan.submit');

// --- Dashboard (wajib login DAN role harus sesuai -- setiap role hanya
//     bisa akses dashboard miliknya sendiri, tidak bisa saling intip
//     lewat ketik URL manual) ---
Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/dashboard-karyawan', [DashboardController::class, 'index'])->name('dashboard.karyawan');
    Route::get('/profil-karyawan', [ProfilController::class, 'index'])->name('profil.karyawan');
    Route::get('/absensi-karyawan', [RekapAbsensiController::class, 'index'])->name('absensi.karyawan');
});

Route::middleware(['auth', 'role:hrd'])->group(function () {
    Route::get('/dashboard-hrd', [DashboardHrdController::class, 'index'])->name('dashboard.hrd');
});

// ------------------------------------------------------------------
// Halaman khusus HRD (kelola karyawan, jabatan, departemen).
// SEBELUMNYA route-route ini tidak dijaga middleware apapun -- siapa
// saja (termasuk yang belum login) bisa akses langsung lewat URL,
// termasuk aksi create/update/delete. Sekarang wajib login DAN
// role-nya 'hrd' (lihat App\Http\Middleware\EnsureRoleIs).
// ------------------------------------------------------------------
Route::middleware(['auth', 'role:hrd'])->group(function () {
    Route::get('/hrd/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
    Route::get('/hrd/karyawan/create', [KaryawanController::class, 'create'])->name('karyawan.create');
    Route::post('/hrd/karyawan', [KaryawanController::class, 'store'])->name('karyawan.store');
    Route::get('/hrd/karyawan/{id}/edit', [KaryawanController::class, 'edit'])->name('karyawan.edit');
    Route::get('/hrd/karyawan/{id}/qr', [KaryawanController::class, 'cetakQr'])->name('karyawan.qr');
    Route::put('/hrd/karyawan/{id}', [KaryawanController::class, 'update'])->name('karyawan.update');
    Route::get('/hrd/karyawan/{id}/detail', [KaryawanController::class, 'show'])->name('karyawan.show');

    Route::get('/get-jabatan/{departemen_id}', [KaryawanController::class, 'getJabatan']);
    
    Route::get('/hrd/manajemen-qr', [App\Http\Controllers\KaryawanController::class, 'qrIndex'])->name('karyawan.qr.index');
    
    Route::get('/hrd/karyawan/cetak-qr-massal', [KaryawanController::class, 'cetakQrMassal'])->name('karyawan.qr.massal');
    Route::get('/hrd/jabatan-departemen', [JabatanDepartemenController::class, 'index'])->name('jabatan.departemen.index');
    Route::post('/hrd/departemen/store', [JabatanDepartemenController::class, 'storeDepartemen'])->name('departemen.store');
    Route::delete('/hrd/departemen/{id}', [JabatanDepartemenController::class, 'destroyDepartemen'])->name('departemen.destroy');
    Route::post('/hrd/jabatan/store', [JabatanDepartemenController::class, 'storeJabatan'])->name('jabatan.store');
    Route::delete('/hrd/jabatan/{id}', [JabatanDepartemenController::class, 'destroyJabatan'])->name('jabatan.destroy');
    Route::put('/hrd/departemen/{id}', [JabatanDepartemenController::class, 'updateDepartemen'])->name('departemen.update');
    Route::put('/hrd/jabatan/{id}', [JabatanDepartemenController::class, 'updateJabatan'])->name('jabatan.update');
});
