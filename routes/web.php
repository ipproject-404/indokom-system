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
use App\Http\Controllers\PelatihanController;
use App\Http\Controllers\lemburKepalaController;

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
    Route::get('/hrd/absensi/export-massal', [App\Http\Controllers\PresensiController::class, 'exportExcelMassal'])->name('kehadiran.export-massal');

    // ⭐ INPUT MANUAL HRD (Sakit / Izin / Cuti / Alpa / Dinas Luar / Libur)
    Route::post('/hrd/absensi/status-manual', [App\Http\Controllers\PresensiController::class, 'simpanStatusManual'])->name('kehadiran.status-manual');
    Route::post('/hrd/absensi/status-manual/multi', [App\Http\Controllers\PresensiController::class, 'simpanIzinMultiHari'])->name('kehadiran.status-manual.multi');
    Route::post('/hrd/absensi/status-manual/hapus', [App\Http\Controllers\PresensiController::class, 'hapusStatusManual'])->name('kehadiran.status-manual.hapus');

    // Hak Akses
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

    // Shift karyawan
    Route::get('/hrd/shift', [ShiftHrdController::class, 'index'])->name('shift.hrd.index');

    // ================================================================
    // PELATIHAN & SERTIFIKASI (Khusus HRD)
    // ================================================================
    Route::get('/hrd/pelatihan', [PelatihanController::class, 'index'])->name('pelatihan.index');
    Route::post('/hrd/pelatihan', [PelatihanController::class, 'store'])->name('pelatihan.store');
    Route::put('/hrd/pelatihan/{pelatihan}', [PelatihanController::class, 'update'])->name('pelatihan.update');
    Route::delete('/hrd/pelatihan/{pelatihan}', [PelatihanController::class, 'destroy'])->name('pelatihan.destroy');

    // Peserta
    Route::post('/hrd/pelatihan/{pelatihan}/peserta', [PelatihanController::class, 'tambahPeserta'])->name('pelatihan.peserta.tambah');
    Route::delete('/hrd/pelatihan/{pelatihan}/peserta/{karyawan}', [PelatihanController::class, 'hapusPeserta'])->name('pelatihan.peserta.hapus');

    // ⬇️ ROUTE ABSEN HADIR OLEH HRD ⬇️
    Route::post('/hrd/pelatihan/peserta/{peserta}/hadir', [PelatihanController::class, 'tandaiHadir'])->name('pelatihan.peserta.hadir');

    // Nilai
    Route::post('/hrd/pelatihan/{pelatihan}/nilai', [PelatihanController::class, 'simpanNilaiBulk'])->name('pelatihan.nilai.simpan');

    // Cetak semua sertifikat (Multi-page)
    Route::get('/hrd/pelatihan/{pelatihan}/cetak-semua', [PelatihanController::class, 'cetakSemuaSertifikat'])->name('pelatihan.cetak-semua');

    // Export
    Route::get('/hrd/pelatihan/{pelatihan}/export-excel', [PelatihanController::class, 'exportPesertaExcel'])->name('pelatihan.export-peserta-excel');
    Route::get('/hrd/pelatihan/{pelatihan}/export-pdf',   [PelatihanController::class, 'exportPesertaPdf'])->name('pelatihan.export-peserta-pdf');

    // Hapus nilai
    Route::delete('/hrd/pelatihan/{pelatihan}/sertifikasi/{sertifikasi}', [PelatihanController::class, 'hapusSertifikasi'])->name('pelatihan.sertifikasi.hapus');
});

// --- Shift Tim (Kepala Bagian) ---
Route::middleware(['auth', 'role:karyawan,hrd', 'kepala.bagian'])->group(function () {
    Route::get('/shift-tim', [ShiftTimController::class, 'index'])->name('shift-tim.index');
    Route::post('/shift-tim', [ShiftTimController::class, 'store'])->name('shift-tim.store');
    Route::post('/shift-tim/tukar', [ShiftTimController::class, 'tukar'])->name('shift-tim.tukar');
    Route::get('/lembur-kepala', [App\Http\Controllers\LemburKepalaController::class, 'index'])->name('lembur.kepala');
    Route::post('/lembur-kepala', [App\Http\Controllers\LemburKepalaController::class, 'store'])->name('lembur.kepala.store');
    Route::delete('/lembur-kepala/{id}', [App\Http\Controllers\LemburKepalaController::class, 'batal'])->name('lembur.kepala.batal');
});

// --- Lembur & Pelatihan Karyawan ---
Route::middleware(['auth', 'role:karyawan,hrd'])->group(function () {
    // ⬇️ LEMBUR: HANYA UNTUK MELIHAT (READ-ONLY) ⬇️
    Route::get('/lembur-karyawan', [LemburKaryawanController::class, 'index'])->name('lembur.karyawan');
    
    // ⬇️ ROUTE PELATIHAN KARYAWAN ⬇️
    Route::get('/pelatihan-saya', [App\Http\Controllers\PelatihanKaryawanController::class, 'index'])->name('pelatihan.karyawan');
    
    // ⬇️ ROUTE ABSEN HADIR OLEH KARYAWAN ⬇️
    Route::post('/pelatihan-saya/peserta/{peserta}/hadir', [App\Http\Controllers\PelatihanKaryawanController::class, 'tandaiHadir'])->name('pelatihan.karyawan.hadir');

    // ⬇️ ROUTE CETAK SERTIFIKAT PER ORANG ⬇️
    Route::get('/sertifikasi/{sertifikasi}/cetak', [PelatihanController::class, 'cetakSertifikat'])->name('sertifikasi.cetak');
});

// --- Payroll ---
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
    Route::get('/payroll/rekap-departemen', [App\Http\Controllers\PayrollController::class, 'rekapDepartemen'])->name('payroll.rekap.departemen');
});