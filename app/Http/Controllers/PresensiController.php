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
        Carbon::setLocale('id');
    }

    /**
     * Menampilkan Halaman Matrix Log Kehadiran Harian
     */
    public function logHarian(Request $request)
    {
        Carbon::setLocale('id');

        $defaultStartDate = Carbon::now()->startOfMonth()->toDateString();
        $defaultEndDate = Carbon::today()->toDateString();

        $startDate = $request->input('start_date', $defaultStartDate);
        $endDate = $request->input('end_date', $defaultEndDate);
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
     * Export Laporan Rinci 1 Karyawan ke Excel (.xls HTML)
     */
    public function exportExcel(Request $request)
    {
        Carbon::setLocale('id');

        $karyawan_id = $request->input('karyawan_id');
        if (!$karyawan_id) {
            return back()->with('error', 'Pilih karyawan terlebih dahulu.');
        }

        $karyawan = Karyawan::with(['departemen.divisi', 'jabatan', 'perusahaan', 'shift', 'tipeKaryawan'])
            ->findOrFail($karyawan_id);

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', Carbon::today()->toDateString());

        $start = Carbon::parse($startDate);
        $end   = Carbon::parse($endDate);
        if ($start->diffInDays($end) > 31) {
            $end = $start->copy()->addDays(31);
            $endDate = $end->toDateString();
        }

        $presensi = Presensi::with('shift')
            ->where('karyawan_id', $karyawan_id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->keyBy('tanggal');

        $lembur = collect();
        if (class_exists('\App\Models\Lembur')) {
            try {
                $lembur = \App\Models\Lembur::where('karyawan_id', $karyawan_id)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->where('status', 'disetujui')
                    ->get()
                    ->keyBy('tanggal');
            } catch (\Exception $e) {
                $lembur = collect();
            }
        }

        $html = $this->buildLaporanRinciHtml($karyawan, $presensi, $lembur, $startDate, $endDate);

        $fileName = 'Laporan_Rinci_' . $karyawan->nik_kerja . '_' . $startDate . '_sd_' . $endDate . '.xls';

        return response("\xEF\xBB\xBF" . $html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Build HTML untuk Laporan Rinci 1 karyawan
     * + Info Lokasi (Dalam/Luar Radius + Alamat)
     */
    private function buildLaporanRinciHtml($karyawan, $presensi, $lembur, $startDate, $endDate)
    {
        $periode = Carbon::parse($startDate)->translatedFormat('d-M-Y')
                 . ' sd '
                 . Carbon::parse($endDate)->translatedFormat('d-M-Y');

        $thStyle = 'border:1px solid #1E3A8A; padding:6px 8px; background:#1E3A8A; color:#FFFFFF; font-weight:bold; text-align:center; font-family:Arial; font-size:10pt;';
        $thInfo  = 'border:1px solid #1E3A8A; padding:5px 8px; background:#DBEAFE; color:#1E3A8A; font-weight:bold; text-align:left; font-family:Arial; font-size:10pt;';
        $tdInfo  = 'border:1px solid #1E3A8A; padding:5px 8px; font-family:Arial; font-size:10pt; text-align:left;';
        $td      = 'border:1px solid #94A3B8; padding:5px 8px; font-family:Arial; font-size:10pt; vertical-align:top;';
        $tdC     = 'border:1px solid #94A3B8; padding:5px 8px; font-family:Arial; font-size:10pt; text-align:center; vertical-align:top;';
        $tdLibur = 'border:1px solid #94A3B8; padding:5px 8px; font-family:Arial; font-size:10pt; text-align:center; background:#FEE2E2; color:#B91C1C; font-weight:bold;';
        $tdTelat = 'border:1px solid #94A3B8; padding:5px 8px; font-family:Arial; font-size:10pt; text-align:center; color:#B91C1C; font-weight:bold;';
        $tdKet   = 'border:1px solid #94A3B8; padding:5px 8px; font-family:Arial; font-size:9pt; vertical-align:top; line-height:1.4;';

        $html  = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        $html .= '<head><meta charset="UTF-8">';
        $html .= '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
        $html .= '<x:Name>Laporan Rinci</x:Name>';
        $html .= '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>';
        $html .= '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
        $html .= '</head><body>';

        $html .= '<h2 style="font-family:Arial; margin:0 0 4px 0;">Laporan Rinci Kehadiran Karyawan</h2>';
        $html .= '<p style="font-family:Arial; margin:0 0 10px 0; font-size:11pt;">Periode : ' . e($periode) . '</p>';

        $html .= '<table cellspacing="0" cellpadding="0" style="border-collapse:collapse;">';

        // Info Karyawan
        $html .= '<tr>';
        $html .= '<td style="' . $thInfo . '" width="120">NAMA</td>';
        $html .= '<td style="' . $tdInfo . '" width="280">' . e($karyawan->nama_lengkap) . '</td>';
        $html .= '<td style="' . $thInfo . '" width="80">NIK</td>';
        $html .= '<td style="' . $tdInfo . '" width="200">' . e($karyawan->nik_kerja) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="' . $thInfo . '">DEPARTEMEN</td>';
        $html .= '<td style="' . $tdInfo . '">' . e($karyawan->departemen->nama_departemen ?? '-') . '</td>';
        $html .= '<td style="' . $thInfo . '">JABATAN</td>';
        $html .= '<td style="' . $tdInfo . '">' . e($karyawan->jabatan->nama_jabatan ?? '-') . '</td>';
        $html .= '</tr>';

        // Header tabel
        $html .= '<tr>';
        $html .= '<th style="' . $thStyle . '" rowspan="2">Tanggal</th>';
        $html .= '<th style="' . $thStyle . '" rowspan="2">Kerja/<br>Libur</th>';
        $html .= '<th style="' . $thStyle . '" colspan="2">Jadwal</th>';
        $html .= '<th style="' . $thStyle . '" colspan="2">Absensi</th>';
        $html .= '<th style="' . $thStyle . '" rowspan="2">Ter<br>Lambat</th>';
        $html .= '<th style="' . $thStyle . '" rowspan="2">Pulang<br>Awal</th>';
        $html .= '<th style="' . $thStyle . '" rowspan="2">Jam<br>Efektif</th>';
        $html .= '<th style="' . $thStyle . '" rowspan="2">Lembur</th>';
        $html .= '<th style="' . $thStyle . '" rowspan="2">Multi<br>plikasi</th>';
        $html .= '<th style="' . $thStyle . '" rowspan="2">Alasan</th>';
        $html .= '<th style="' . $thStyle . '" rowspan="2">Keterangan</th>';
        $html .= '<th style="' . $thStyle . '" rowspan="2" width="280">Lokasi</th>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<th style="' . $thStyle . '">Masuk</th>';
        $html .= '<th style="' . $thStyle . '">Pulang</th>';
        $html .= '<th style="' . $thStyle . '">Masuk</th>';
        $html .= '<th style="' . $thStyle . '">Pulang</th>';
        $html .= '</tr>';

        // Data harian
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $tgl) {
            $tglKey = $tgl->toDateString();
            $prs    = $presensi->get($tglKey);
            $lmb    = $lembur->get($tglKey);

            $isSunday = $tgl->isSunday();
            $shift    = $karyawan->shift;
            $statusKerja = $isSunday ? 'Libur' : 'Kerja';
            $keteranganLibur = $isSunday ? 'Hari Minggu' : '-';

            if ($karyawan->pakai_jadwal_shift) {
                $jadwalHariIni = \App\Models\JadwalShift::with('shift')
                    ->where('karyawan_id', $karyawan->id)
                    ->where('tanggal', $tglKey)
                    ->first();
                $shift = $jadwalHariIni?->shift;
                $statusKerja = $shift ? 'Kerja' : 'Libur';
                $keteranganLibur = $shift ? '-' : 'Libur sesuai jadwal';
            }

            $jadwalMasuk  = ($shift && $statusKerja === 'Kerja') ? substr($shift->jam_masuk, 0, 5) : '-';
            $jadwalPulang = ($shift && $statusKerja === 'Kerja') ? substr($shift->jam_pulang_default, 0, 5) : '-';

            $absenMasuk  = ($prs && $prs->jam_masuk)  ? Carbon::parse($prs->jam_masuk)->format('H:i')  : '-';
            $absenPulang = ($prs && $prs->jam_pulang) ? Carbon::parse($prs->jam_pulang)->format('H:i') : '-';

            // Hitung Terlambat
            $terlambat = '-';
            if ($prs && $shift && $prs->jam_masuk && $statusKerja === 'Kerja') {
                $masukStd = Carbon::parse($tglKey . ' ' . $shift->jam_masuk);
                $jamMasukStr = Carbon::parse($prs->jam_masuk)->format('H:i:s');
                $masukAkt    = Carbon::parse($tglKey . ' ' . $jamMasukStr);
                $batasStd = $masukStd->copy()->addMinutes($shift->toleransi_menit ?? 0);

                if ($masukAkt->gt($batasStd)) {
                    $terlambat = (int) $masukStd->diffInMinutes($masukAkt);
                }
            }

            // Hitung Pulang Awal
            $pulangAwal = '-';
            if ($prs && $shift && $prs->jam_pulang && $statusKerja === 'Kerja') {
                $pulangStd = Carbon::parse($tglKey . ' ' . $shift->jam_pulang_default);
                $jamPulangStr = Carbon::parse($prs->jam_pulang)->format('H:i:s');
                $pulangAkt    = Carbon::parse($tglKey . ' ' . $jamPulangStr);

                if ($pulangAkt->lt($pulangStd)) {
                    $pulangAwal = (int) $pulangStd->diffInMinutes($pulangAkt);
                }
            }

            // Hitung Jam Efektif
            $jamEfektif = '-';
            if ($prs && $prs->jam_masuk && $prs->jam_pulang) {
                $jamMasukStr  = Carbon::parse($prs->jam_masuk)->format('H:i:s');
                $jamPulangStr = Carbon::parse($prs->jam_pulang)->format('H:i:s');

                $m = Carbon::parse($tglKey . ' ' . $jamMasukStr);
                $p = Carbon::parse($tglKey . ' ' . $jamPulangStr);

                if ($p->lt($m)) {
                    $p->addDay();
                }

                $menit = (int) $m->diffInMinutes($p);
                $jamEfektif = floor($menit / 60) . '.' . str_pad($menit % 60, 2, '0', STR_PAD_LEFT);
            }

            $durasiLembur = $lmb ? ($lmb->durasi_jam ?? '-') : '-';
            $tglFormatted = $tgl->translatedFormat('d-M-Y');

            // ==========================================
            // INFO LOKASI (Dalam/Luar Radius + Alamat)
            // ==========================================
            $infoLokasi = '-';
            if ($prs) {
                $parts = [];

                // IN (Masuk)
                if ($prs->jam_masuk) {
                    $statusRadius = 'Tidak Terdeteksi';
                    $warnaIn = 'color:#64748B;';

                    if ($prs->status_radius_masuk == 'dalam_radius') {
                        $statusRadius = 'Dalam Radius';
                        $warnaIn = 'color:#15803D; font-weight:bold;';
                    } elseif ($prs->status_radius_masuk) {
                        $statusRadius = 'Luar Radius';
                        $warnaIn = 'color:#B91C1C; font-weight:bold;';
                    }

                    $alamat = $prs->alamat_masuk ?? $prs->nama_jalan_masuk ?? 'Lokasi tidak diketahui';
                    $jarak  = $prs->jarak_masuk_meter ? '(' . $prs->jarak_masuk_meter . 'm)' : '';

                    $parts[] = '<span style="color:#15803D; font-weight:bold;">IN:</span> '
                             . '<span style="' . $warnaIn . '">' . e($statusRadius) . '</span> '
                             . $jarak
                             . '<br><span style="color:#64748B; font-size:8.5pt;">' . e($alamat) . '</span>';
                }

                // OUT (Pulang)
                if ($prs->jam_pulang) {
                    $statusRadius = 'Tidak Terdeteksi';
                    $warnaOut = 'color:#64748B;';

                    if ($prs->status_radius_pulang == 'dalam_radius') {
                        $statusRadius = 'Dalam Radius';
                        $warnaOut = 'color:#15803D; font-weight:bold;';
                    } elseif ($prs->status_radius_pulang) {
                        $statusRadius = 'Luar Radius';
                        $warnaOut = 'color:#B91C1C; font-weight:bold;';
                    }

                    $alamat = $prs->alamat_pulang ?? $prs->nama_jalan_pulang ?? 'Lokasi tidak diketahui';
                    $jarak  = $prs->jarak_pulang_meter ? '(' . $prs->jarak_pulang_meter . 'm)' : '';

                    $parts[] = '<span style="color:#B91C1C; font-weight:bold;">OUT:</span> '
                             . '<span style="' . $warnaOut . '">' . e($statusRadius) . '</span> '
                             . $jarak
                             . '<br><span style="color:#64748B; font-size:8.5pt;">' . e($alamat) . '</span>';
                }

                if (!empty($parts)) {
                    $infoLokasi = implode('<br><br>', $parts);
                }
            }

            $html .= '<tr>';
            if ($statusKerja === 'Libur') {
                $html .= '<td style="' . $tdLibur . '">' . $tglFormatted . '</td>';
                $html .= '<td style="' . $tdLibur . '" colspan="10">LIBUR</td>';
                $html .= '<td style="' . $tdLibur . '">' . e($keteranganLibur) . '</td>';
                $html .= '<td style="' . $tdLibur . '">-</td>';
            } else {
                $html .= '<td style="' . $tdC . '">' . $tglFormatted . '</td>';
                $html .= '<td style="' . $tdC . '">Kerja</td>';
                $html .= '<td style="' . $tdC . '">' . $jadwalMasuk  . '</td>';
                $html .= '<td style="' . $tdC . '">' . $jadwalPulang . '</td>';
                $html .= '<td style="' . $tdC . '">' . $absenMasuk   . '</td>';
                $html .= '<td style="' . $tdC . '">' . $absenPulang  . '</td>';
                $html .= '<td style="' . ($terlambat !== '-' ? $tdTelat : $tdC) . '">' . $terlambat . '</td>';
                $html .= '<td style="' . ($pulangAwal !== '-' ? $tdTelat : $tdC) . '">' . $pulangAwal . '</td>';
                $html .= '<td style="' . $tdC . '">' . $jamEfektif   . '</td>';
                $html .= '<td style="' . $tdC . '">' . $durasiLembur . '</td>';
                $html .= '<td style="' . $tdC . '">-</td>';
                $html .= '<td style="' . $td . '">-</td>';
                $html .= '<td style="' . $td . '">-</td>';
                $html .= '<td style="' . $tdKet . '">' . $infoLokasi . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table></body></html>';
        return $html;
    }
}