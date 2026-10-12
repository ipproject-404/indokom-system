<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

// Memanggil Model Database berdasarkan ERD
use App\Models\Karyawan;
use App\Models\Presensi;

class DashboardHrdController extends Controller
{
    public function index()
    {
        // Ambil tanggal hari ini (format YYYY-MM-DD)
        $hariIni = Carbon::today()->format('Y-m-d');

        // ==========================================
        // 1. DATA STATISTIK (Untuk Card di atas)
        // ==========================================
        $totalKaryawanAktif = Karyawan::where('status', 'aktif')->count();
        $hadirHariIni = Presensi::whereDate('tanggal', $hariIni)->count();
        $qrTercetak = Karyawan::where('status', 'aktif')->whereNotNull('barcode_uid')->count();

        // ==========================================
        // 2. DATA TABEL 1: Daftar Karyawan (5 Terbaru)
        // ==========================================
        $karyawans = Karyawan::with(['jabatan', 'departemen'])
                             ->orderBy('created_at', 'desc')
                             ->take(5)
                             ->get();

        // ==========================================
        // 3. DATA TABEL 2: Pantauan Kehadiran Hari Ini
        // ==========================================
        $presensis = Presensi::with('karyawan.departemen')
                             ->whereDate('tanggal', $hariIni)
                             ->orderBy('jam_masuk', 'desc')
                             ->take(5)
                             ->get();

        // Mengirim semua data di atas ke file blade HTML
        return view('dashboard.hrd', compact(
            'totalKaryawanAktif',
            'hadirHariIni',
            'qrTercetak',
            'karyawans',
            'presensis'
        ));
    }
}