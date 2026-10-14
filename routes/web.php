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

    // ⭐ INPUT MANUAL HRD (Sakit / Izin / Cuti / Alpa / Dinas Luar / Libur)
    Route::post('/hrd/absensi/status-manual', [App\Http\Controllers\PresensiController::class, 'simpanStatusManual'])->name('kehadiran.status-manual');
    Route::post('/hrd/absensi/status-manual/multi', [App\Http\Controllers\PresensiController::class, 'simpanIzinMultiHari'])->name('kehadiran.status-manual.multi');
    Route::post('/hrd/absensi/status-manual/hapus', [App\Http\Controllers\PresensiController::class, 'hapusStatusManual'])->name('kehadiran.status-manual.hapus');

    // LEMBUR HRD SUDAH DIHAPUS (dipindah ke role lain)
    Route::post('/hrd/karyawan/{id}/hak-akses', [App\Http\Controllers\HakAksesController::class, 'toggle'])->name('karyawan.hak-akses.toggle');

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

    //pelatihan & sertifikasi
    Route::get('/hrd/pelatihan', [App\Http\Controllers\PelatihanController::class, 'index'])->name('pelatihan.index');
    Route::post('/hrd/pelatihan', [App\Http\Controllers\PelatihanController::class, 'store'])->name('pelatihan.store');
    Route::put('/hrd/pelatihan/{pelatihan}', [App\Http\Controllers\PelatihanController::class, 'update'])->name('pelatihan.update');
    Route::delete('/hrd/pelatihan/{pelatihan}', [App\Http\Controllers\PelatihanController::class, 'destroy'])->name('pelatihan.destroy');
    Route::post('/hrd/pelatihan/{pelatihan}/peserta', [App\Http\Controllers\PelatihanController::class, 'tambahPeserta'])->name('pelatihan.peserta.tambah');
    Route::delete('/hrd/pelatihan/{pelatihan}/peserta/{karyawan}', [App\Http\Controllers\PelatihanController::class, 'hapusPeserta'])->name('pelatihan.peserta.hapus');
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
    Route::get('/pelatihan-saya', [App\Http\Controllers\PelatihanKaryawanController::class, 'index'])->name('pelatihan.karyawan');
});

Route::middleware(['auth', 'role:karyawan,hrd', 'hak:payroll'])->group(function () {
    Route::get('/lembur-payroll', [LemburPayrollController::class, 'index'])->name('lembur.payroll');
    Route::post('/lembur-payroll/{id}/setujui', [LemburPayrollController::class, 'setujui'])->name('lembur.payroll.setujui');
    Route::post('/lembur-payroll/{id}/tolak', [LemburPayrollController::class, 'tolak'])->name('lembur.payroll.tolak');

    Route::get('/payroll', [App\Http\Controllers\PayrollController::class, 'index'])->name('payroll.index');
    Route::post('/payroll/{id}/setujui', [App\Http\Controllers\PayrollController::class, 'setujui'])->name('payroll.setujui');
    Route::post('/payroll/{id}/tolak', [App\Http\Controllers\PayrollController::class, 'tolak'])->name('payroll.tolak');
    Route::get('/payroll/rekap', [App\Http\Controllers\PayrollController::class, 'rekap'])->name('payroll.rekap');
    Route::get('/payroll/slip/{karyawanId}', [App\Http\Controllers\PayrollController::class, 'slip'])->name('payroll.slip');
    Route::get('/payroll/tarif', [App\Http\Controllers\PayrollController::class, 'tarifIndex'])->name('payroll.tarif');
    Route::post('/payroll/tarif', [App\Http\Controllers\PayrollController::class, 'tarifStore'])->name('payroll.tarif.store');
    Route::delete('/payroll/tarif/{id}', [App\Http\Controllers\PayrollController::class, 'destroy'])->name('payroll.tarif.destroy');
});