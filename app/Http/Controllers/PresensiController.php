<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function logHarian(Request $request)
    {
        // Default: 7 Hari Terakhir (termasuk hari ini)
        $defaultStartDate = Carbon::now()->subDays(6)->toDateString();
        $defaultEndDate = Carbon::today()->toDateString();

        $startDate = $request->input('start_date', $defaultStartDate);
        $endDate = $request->input('end_date', $defaultEndDate);
        $search = $request->input('search');

        // Query Utama Presensi
        $query = Presensi::with(['karyawan.departemen', 'karyawan.jabatan'])
            ->whereBetween('tanggal', [$startDate, $endDate]);

        // Jika ada pencarian nama/NIK
        if ($search) {
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik_kerja', 'like', "%{$search}%");
            });
        }

        // Urutkan dari tanggal terbaru (hari ini) lalu berdasarkan jam masuk tercepat
        $presensis = $query->orderBy('tanggal', 'desc')
                           ->orderBy('jam_masuk', 'asc')
                           ->paginate(15);

        // --- MENGHITUNG STATISTIK RINGKAS UNTUK KARTU DASHBOARD ---
        // 1. Total data presensi dalam rentang waktu tersebut
        $totalKehadiran = Presensi::whereBetween('tanggal', [$startDate, $endDate])->count();
        
        // 2. Karyawan yang sudah absen masuk tapi belum absen pulang
        $belumPulang = Presensi::whereBetween('tanggal', [$startDate, $endDate])
                               ->whereNull('jam_pulang')->count();

        // 3. Menghitung rata-rata kehadiran harian
        $selisihHari = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $rataHarian = $selisihHari > 0 ? round($totalKehadiran / $selisihHari) : $totalKehadiran;

        return view('absensi.log_kehadiran', compact(
            'presensis', 'startDate', 'endDate', 'search', 
            'totalKehadiran', 'belumPulang', 'rataHarian'
        ));
    }
}