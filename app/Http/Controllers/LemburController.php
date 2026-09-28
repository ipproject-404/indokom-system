<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lembur;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class LemburController extends Controller
{
    public function __construct()
    {
        Carbon::setLocale('id');
    }

    /**
     * Menampilkan daftar pengajuan lembur dengan filter lengkap
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        // Default Filter: Bulan ini
        $defaultStartDate = Carbon::now()->startOfMonth()->toDateString();
        $defaultEndDate = Carbon::today()->toDateString();

        $startDate = $request->input('start_date', $defaultStartDate);
        $endDate = $request->input('end_date', $defaultEndDate);
        $status = $request->input('status', 'menunggu');
        $search = $request->input('search');

        $query = Lembur::with(['karyawan.departemen', 'karyawan.jabatan'])
            ->whereBetween('tanggal', [$startDate, $endDate]);

        // Filter Status
        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        // Filter Pencarian Nama / NIK Karyawan
        if ($search) {
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik_kerja', 'like', "%{$search}%");
            });
        }

        $lemburs = $query->orderBy('tanggal', 'desc')->paginate(15);

        return view('absensi.pengajuan_lembur', compact('lemburs', 'status', 'startDate', 'endDate', 'search'));
    }

    /**
     * Export data lembur ke format Excel (.xls rapi)
     */
    public function exportExcel(Request $request)
    {
        Carbon::setLocale('id');

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $status = $request->input('status', 'semua');
        $search = $request->input('search');

        $query = Lembur::with(['karyawan.departemen'])
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik_kerja', 'like', "%{$search}%");
            });
        }

        $lemburs = $query->orderBy('tanggal', 'desc')->get();

        $fileName = 'Rekap_Lembur_' . $startDate . '_sd_' . $endDate . '.xls';

        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '<table border="1">';
        echo '<tr style="background-color: #334155; color: #ffffff; font-weight: bold; height: 30px;">';
        echo '<th style="width: 50px; text-align: center;">No</th>';
        echo '<th style="width: 120px;">NIK</th>';
        echo '<th style="width: 200px;">Nama Karyawan</th>';
        echo '<th style="width: 150px;">Departemen</th>';
        echo '<th style="width: 120px; text-align: center;">Tanggal</th>';
        echo '<th style="width: 120px; text-align: center;">Jam Mulai</th>';
        echo '<th style="width: 120px; text-align: center;">Jam Selesai</th>';
        echo '<th style="width: 100px; text-align: center;">Durasi</th>';
        echo '<th style="width: 120px; text-align: right;">Total Upah</th>';
        echo '<th style="width: 120px; text-align: center;">Status</th>';
        echo '<th style="width: 250px;">Catatan</th>';
        echo '</tr>';

        $no = 1;
        foreach ($lemburs as $lmb) {
            echo '<tr style="height: 25px;">';
            echo '<td align="center">' . $no++ . '</td>';
            echo '<td style="mso-number-format:\@;">&nbsp;' . ($lmb->karyawan->nik_kerja ?? '-') . '</td>';
            echo '<td>' . htmlspecialchars($lmb->karyawan->nama_lengkap ?? '-') . '</td>';
            echo '<td>' . htmlspecialchars($lmb->karyawan->departemen->nama_departemen ?? '-') . '</td>';
            echo '<td align="center">' . Carbon::parse($lmb->tanggal)->format('d/m/Y') . '</td>';
            echo '<td align="center">' . ($lmb->jam_mulai ? Carbon::parse($lmb->jam_mulai)->format('H:i') : '-') . '</td>';
            echo '<td align="center">' . ($lmb->jam_selesai ? Carbon::parse($lmb->jam_selesai)->format('H:i') : '-') . '</td>';
            echo '<td align="center">' . ($lmb->durasi_jam ?? 0) . ' Jam</td>';
            echo '<td align="right">Rp ' . number_format($lmb->total_upah ?? 0, 0, ',', '.') . '</td>';
            echo '<td align="center" style="font-weight: bold;">' . ucfirst($lmb->status) . '</td>';
            echo '<td>' . htmlspecialchars($lmb->catatan ?? '-') . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        exit;
    }

    public function approve($id)
    {
        $lembur = Lembur::findOrFail($id);
        $lembur->status = 'disetujui';
        
        if (Schema::hasColumn('lembur', 'disetujui_oleh')) {
            $lembur->disetujui_oleh = Auth::id() ?? 1;
        }
        
        $lembur->save();

        return redirect()->back()->with('success', 'Pengajuan lembur berhasil disetujui (ACC).');
    }

    public function reject($id)
    {
        $lembur = Lembur::findOrFail($id);
        $lembur->status = 'ditolak';

        if (Schema::hasColumn('lembur', 'disetujui_oleh')) {
            $lembur->disetujui_oleh = Auth::id() ?? 1;
        }

        $lembur->save();

        return redirect()->back()->with('success', 'Pengajuan lembur telah ditolak.');
    }
}