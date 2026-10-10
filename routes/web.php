<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardHrdController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\JabatanDepartemenController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RekapAbsensiController;
use App\Http\Controllers\ShiftTimController;
use App\Http\Controllers\ShiftHrdController;
use App\Http\Controllers\LemburKaryawanController;
use App\Http\Controllers\LemburPayrollController;


use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

// --- Login manual ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Login lewat scan QR ---
Route::get('/login/qr', [QrController::class, 'showScanLogin'])->name('login.qr');
Route::post('/login/qr', [QrController::class, 'scanLogin'])->name('login.qr.submit');

// --- Absensi scan QR ---
Route::get('/absensi/scan', [QrController::class, 'showScanAbsensi'])->name('absensi.scan');
Route::post('/absensi/scan', [QrController::class, 'scanAbsensi'])->name('absensi.scan.submit');

// --- Dashboard Karyawan ---
Route::middleware(['auth', 'role:karyawan,hrd'])->group(function () {
    Route::get('/dashboard-karyawan', [DashboardController::class, 'index'])->name('dashboard.karyawan');
    Route::get('/profil-karyawan', [ProfilController::class, 'index'])->name('profil.karyawan');
    Route::get('/absensi-karyawan', [RekapAbsensiController::class, 'index'])->name('absensi.karyawan');
});

// --- Dashboard HRD ---
Route::middleware(['auth', 'role:hrd'])->group(function () {
    Route::get('/dashboard-hrd', [DashboardHrdController::class, 'index'])->name('dashboard.hrd');
});

// --- Halaman Khusus HRD ---
Route::middleware(['auth', 'role:hrd'])->group(function () {

    // Karyawan
    Route::get('/hrd/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
    Route::get('/hrd/karyawan/create', [KaryawanController::class, 'create'])->name('karyawan.create');
    Route::get('/hrd/karyawan/{id}/edit', [KaryawanController::class, 'edit'])->name('karyawan.edit');
    Route::get('/hrd/karyawan/{id}/qr', [KaryawanController::class, 'cetakQr'])->name('karyawan.qr');
    Route::get('/hrd/karyawan/{id}/detail', [KaryawanController::class, 'show'])->name('karyawan.show');
    Route::post('/hrd/karyawan', [KaryawanController::class, 'store'])->name('karyawan.store');
    Route::put('/hrd/karyawan/{id}', [KaryawanController::class, 'update'])->name('karyawan.update');
    Route::post('/hrd/karyawan/{id}/reset-password', [KaryawanController::class, 'resetPassword'])->name('karyawan.reset-password');

    // ⭐ Route AJAX Cascading Dropdown ⭐
    Route::get('/get-divisi/{perusahaan_id}', [KaryawanController::class, 'getDivisi'])->name('get.divisi');
    Route::get('/get-departemen/{divisi_id}', [KaryawanController::class, 'getDepartemen'])->name('get.departemen');
    Route::get('/get-jabatan/{departemen_id}', [KaryawanController::class, 'getJabatan'])->name('get.jabatan');

    // Manajemen QR
    Route::get('/hrd/manajemen-qr', [KaryawanController::class, 'qrIndex'])->name('karyawan.qr.index');
    Route::get('/hrd/karyawan/cetak-qr-massal', [KaryawanController::class, 'cetakQrMassal'])->name('karyawan.qr.massal');

    // Kehadiran
    Route::get('/hrd/absensi/log-kehadiran', [App\Http\Controllers\PresensiController::class, 'logHarian'])->name('kehadiran.log');
    Route::get('/hrd/absensi/export-excel', [App\Http\Controllers\PresensiController::class, 'exportExcel'])->name('kehadiran.export');

    // Lembur
    Route::get('/hrd/lembur/pengajuan', [App\Http\Controllers\LemburController::class, 'index'])->name('lembur.pengajuan');
    Route::get('/hrd/lembur/export-excel', [App\Http\Controllers\LemburController::class, 'exportExcel'])->name('lembur.export');
    Route::post('/hrd/lembur/{id}/approve', [App\Http\Controllers\LemburController::class, 'approve'])->name('lembur.approve');
    Route::post('/hrd/lembur/{id}/reject', [App\Http\Controllers\LemburController::class, 'reject'])->name('lembur.reject');

    // Jabatan & Departemen
    Route::get('/hrd/jabatan-departemen', [JabatanDepartemenController::class, 'index'])->name('jabatan.departemen.index');
    Route::post('/hrd/departemen/store', [JabatanDepartemenController::class, 'storeDepartemen'])->name('departemen.store');
    Route::delete('/hrd/departemen/{id}', [JabatanDepartemenController::class, 'destroyDepartemen'])->name('departemen.destroy');
    Route::put('/hrd/departemen/{id}', [JabatanDepartemenController::class, 'updateDepartemen'])->name('departemen.update');
    Route::post('/hrd/jabatan/store', [JabatanDepartemenController::class, 'storeJabatan'])->name('jabatan.store');
    Route::delete('/hrd/jabatan/{id}', [JabatanDepartemenController::class, 'destroyJabatan'])->name('jabatan.destroy');
    Route::put('/hrd/jabatan/{id}', [JabatanDepartemenController::class, 'updateJabatan'])->name('jabatan.update');

    // Profil
    Route::get('/hrd/profil', [ProfilController::class, 'index'])->name('profile.index');
    Route::post('/hrd/profil/update-password', [ProfilController::class, 'updatePassword'])->name('profile.update-password');

        // Shift karyawan (hanya lihat; shift diatur kepala bagian)
    Route::get('/hrd/shift', [ShiftHrdController::class, 'index'])->name('shift.hrd.index');
});

Route::middleware(['auth', 'role:karyawan,hrd', 'kepala.bagian'])->group(function () {
    Route::get('/shift-tim', [ShiftTimController::class, 'index'])->name('shift-tim.index');
    Route::post('/shift-tim', [ShiftTimController::class, 'store'])->name('shift-tim.store');
    Route::post('/shift-tim/tukar', [ShiftTimController::class, 'tukar'])->name('shift-tim.tukar');
});

// --- Lembur sisi karyawan & payroll ---
Route::middleware(['auth', 'role:karyawan,hrd'])->group(function () {
    Route::get('/lembur-karyawan', [LemburKaryawanController::class, 'index'])->name('lembur.karyawan');
    Route::post('/lembur-karyawan', [LemburKaryawanController::class, 'store'])->name('lembur.karyawan.store');
    Route::delete('/lembur-karyawan/{id}', [LemburKaryawanController::class, 'batal'])->name('lembur.karyawan.batal');
});

Route::middleware(['auth', 'role:karyawan,hrd', 'hak:payroll'])->group(function () {
    Route::get('/lembur-payroll', [LemburPayrollController::class, 'index'])->name('lembur.payroll');
    Route::post('/lembur-payroll/{id}/setujui', [LemburPayrollController::class, 'setujui'])->name('lembur.payroll.setujui');
    Route::post('/lembur-payroll/{id}/tolak', [LemburPayrollController::class, 'tolak'])->name('lembur.payroll.tolak');
});

