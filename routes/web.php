<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\DashboardController;
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
});
