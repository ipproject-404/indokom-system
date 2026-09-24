<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

// Memanggil Model Database berdasarkan ERD
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Lembur;

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
        $lemburMenunggu = Lembur::where('status', 'menunggu')->count();
        $qrTercetak = Karyawan::where('status', 'aktif')->whereNotNull('barcode_uid')->count();

        // ==========================================
        // 2. DATA TABEL 1: Daftar Karyawan (5 Terbaru)
        // ==========================================
        // Menggunakan "with" untuk memanggil relasi tabel jabatan dan departemen
        $karyawans = Karyawan::with(['jabatan', 'departemen'])
                             ->orderBy('created_at', 'desc')
                             ->take(5)
                             ->get();

        // ==========================================
        // 3. DATA TABEL 2: Pengajuan Lembur
        // ==========================================
        $lemburs = Lembur::with('karyawan.departemen')
                         ->where('status', 'menunggu')
                         ->orderBy('created_at', 'desc')
                         ->take(5)
                         ->get();

        // ==========================================
        // 4. DATA TABEL 3: Pantauan Kehadiran Hari Ini
        // ==========================================
        $presensis = Presensi::with('karyawan.departemen')
                             ->whereDate('tanggal', $hariIni)
                             ->orderBy('jam_masuk', 'desc')
                             ->take(5)
                             ->get();

        // Mengirim semua data di atas ke file blade HTML
        return view('dashboard_hrd', compact(
            'totalKaryawanAktif', 
            'hadirHariIni', 
            'lemburMenunggu', 
            'qrTercetak',
            'karyawans',
            'lemburs',
            'presensis'
        ));
    }
}