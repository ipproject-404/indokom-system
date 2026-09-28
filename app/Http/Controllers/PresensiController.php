<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\Karyawan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PresensiController extends Controller
{
    public function __construct()
    {
        // Set zona waktu dan bahasa Indonesia untuk Carbon
        Carbon::setLocale('id');
    }

    /**
     * Menampilkan Halaman Matrix Log Kehadiran Harian
     */
    public function logHarian(Request $request)
    {
        Carbon::setLocale('id');

        // Default: Awal bulan ini sampai hari ini
        $defaultStartDate = Carbon::now()->startOfMonth()->toDateString();
        $defaultEndDate = Carbon::today()->toDateString();

        $startDate = $request->input('start_date', $defaultStartDate);
        $endDate = $request->input('end_date', $defaultEndDate);
        $search = $request->input('search');

        // Batasi maksimal rentang waktu 31 hari agar tabel tidak rusak/terlalu panjang
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        if ($start->diffInDays($end) > 31) {
            $end = $start->copy()->addDays(31);
            $endDate = $end->toDateString();
        }

        // Buat Array berisi daftar tanggal
        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        // Query Karyawan
        $karyawansQuery = Karyawan::with(['departemen', 'jabatan']);
        
        if ($search) {
            $karyawansQuery->where('nama_lengkap', 'like', "%{$search}%")
                           ->orWhere('nik_kerja', 'like', "%{$search}%");
        }

        $karyawans = $karyawansQuery->orderBy('nama_lengkap', 'asc')->paginate(10);

        $karyawanIds = $karyawans->pluck('id');
        $presensiData = Presensi::whereIn('karyawan_id', $karyawanIds)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->groupBy('karyawan_id')
            ->map(function ($items) {
                return $items->keyBy('tanggal');
            });

        // Statistik
        $totalKehadiran = Presensi::whereBetween('tanggal', [$startDate, $endDate])->count();
        $belumPulang = Presensi::whereBetween('tanggal', [$startDate, $endDate])->whereNull('jam_pulang')->count();
        $selisihHari = count($dates);
        $rataHarian = $selisihHari > 0 ? round($totalKehadiran / $selisihHari) : $totalKehadiran;

        return view('absensi.log_kehadiran', compact(
            'karyawans', 'presensiData', 'dates', 
            'startDate', 'endDate', 'search',
            'totalKehadiran', 'belumPulang', 'rataHarian'
        ));
    }

    /**
     * Export data presensi ke format Excel (HTML Table Wrapper)
     */
    public function exportExcel(Request $request)
    {
        Carbon::setLocale('id');

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $search = $request->input('search');

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        if ($start->diffInDays($end) > 31) {
            $end = $start->copy()->addDays(31);
            $endDate = $end->toDateString();
        }

        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        $karyawansQuery = Karyawan::with('departemen');
        if ($search) {
            $karyawansQuery->where('nama_lengkap', 'like', "%{$search}%")
                           ->orWhere('nik_kerja', 'like', "%{$search}%");
        }
        $karyawans = $karyawansQuery->orderBy('nama_lengkap', 'asc')->get();

        $karyawanIds = $karyawans->pluck('id');
        $presensiData = Presensi::whereIn('karyawan_id', $karyawanIds)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->groupBy('karyawan_id')
            ->map(function ($items) {
                return $items->keyBy('tanggal');
            });

        $fileName = 'Rekap_Absensi_' . $startDate . '_sd_' . $endDate . '.xls';

        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '<table border="1">';
        
        echo '<tr style="background-color: #334155; color: #ffffff; font-weight: bold; height: 30px;">';
        echo '<th style="width: 50px; text-align: center;">No</th>';
        echo '<th style="width: 120px;">NIK</th>';
        echo '<th style="width: 220px;">Nama Karyawan</th>';
        echo '<th style="width: 160px;">Departemen</th>';
        foreach ($dates as $date) {
            echo '<th style="width: 140px; text-align: center;">' . Carbon::parse($date)->translatedFormat('d/m/Y') . '</th>';
        }
        echo '</tr>';

        $no = 1;
        foreach ($karyawans as $karyawan) {
            echo '<tr style="height: 25px;">';
            echo '<td align="center" style="width: 50px;">' . $no++ . '</td>';
            echo '<td style="width: 120px; mso-number-format:\@;">&nbsp;' . $karyawan->nik_kerja . '</td>'; 
            echo '<td style="width: 220px;">' . htmlspecialchars($karyawan->nama_lengkap) . '</td>';
            echo '<td style="width: 160px;">' . htmlspecialchars($karyawan->departemen->nama_departemen ?? '-') . '</td>';

            foreach ($dates as $date) {
                $prs = isset($presensiData[$karyawan->id][$date]) ? $presensiData[$karyawan->id][$date] : null;
                
                if ($prs) {
                    $in = $prs->jam_masuk ? Carbon::parse($prs->jam_masuk)->format('H:i') : '-';
                    $out = $prs->jam_pulang ? Carbon::parse($prs->jam_pulang)->format('H:i') : '-';
                    echo '<td align="center" style="width: 140px;">In: ' . $in . ' | Out: ' . $out . '</td>';
                } else {
                    echo '<td align="center" style="width: 140px; color: #94a3b8;">Tidak Hadir</td>';
                }
            }
            echo '</tr>';
        }

        echo '</table>';
        exit;
    }
}