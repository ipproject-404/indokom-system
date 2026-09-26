<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardHrdController; // <-- TAMBAHAN: Import Controller HRD
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

// --- Dashboard karyawan (wajib login) ---
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // --- Dashboard HRD ---
    Route::get('/dashboard-hrd', [DashboardHrdController::class, 'index'])->name('dashboard.hrd'); // <-- TAMBAHAN: Route untuk HRD
});

// <-- TAMBAHKAN KODE INI DI BAWAH ROUTE DASHBOARD HRD -->
Route::get('/hrd/karyawan', [App\Http\Controllers\KaryawanController::class, 'index'])->name('karyawan.index');
Route::get('/hrd/karyawan/create', [App\Http\Controllers\KaryawanController::class, 'create'])->name('karyawan.create');
Route::post('/hrd/karyawan', [App\Http\Controllers\KaryawanController::class, 'store'])->name('karyawan.store');

Route::get('/hrd/karyawan/{id}/edit', [App\Http\Controllers\KaryawanController::class, 'edit'])->name('karyawan.edit');
Route::get('/hrd/karyawan/{id}/qr', [App\Http\Controllers\KaryawanController::class, 'cetakQr'])->name('karyawan.qr');

Route::put('/hrd/karyawan/{id}', [App\Http\Controllers\KaryawanController::class, 'update'])->name('karyawan.update');

// Tambahkan baris ini di bawah route karyawan.index / karyawan.create Anda
Route::get('/hrd/karyawan/{id}/detail', [App\Http\Controllers\KaryawanController::class, 'show'])->name('karyawan.show');

use App\Http\Controllers\JabatanDepartemenController;

// Rute Kelola Jabatan & Departemen (HRD)
Route::get('/hrd/jabatan-departemen', [JabatanDepartemenController::class, 'index'])->name('jabatan.departemen.index');
Route::post('/hrd/departemen/store', [JabatanDepartemenController::class, 'storeDepartemen'])->name('departemen.store');
Route::delete('/hrd/departemen/{id}', [JabatanDepartemenController::class, 'destroyDepartemen'])->name('departemen.destroy');

Route::post('/hrd/jabatan/store', [JabatanDepartemenController::class, 'storeJabatan'])->name('jabatan.store');
Route::delete('/hrd/jabatan/{id}', [JabatanDepartemenController::class, 'destroyJabatan'])->name('jabatan.destroy');

Route::put('/hrd/departemen/{id}', [JabatanDepartemenController::class, 'updateDepartemen'])->name('departemen.update');
Route::put('/hrd/jabatan/{id}', [JabatanDepartemenController::class, 'updateJabatan'])->name('jabatan.update');