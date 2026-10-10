<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LemburKaryawanController extends Controller
{
    /** Riwayat + form pengajuan lembur milik karyawan yang login. */
    public function index()
    {
        Carbon::setLocale('id');

        $karyawan = $this->karyawanBolehLembur();

        $riwayat = Lembur::where('karyawan_id', $karyawan->id)
            ->orderByDesc('tanggal')
            ->limit(30)
            ->get();

        return view('karyawan.lembur', compact('karyawan', 'riwayat'));
    }

    /**
     * Ajukan lembur untuk satu tanggal kerja. Jam mulai otomatis = jam
     * pulang shift hari itu (kalau punya shift). Jam selesai belum ada,
     * nanti diisi dari scan pulang (lihat Lembur::isiDariPresensi).
     */
    public function store(Request $request)
    {
        $karyawan = $this->karyawanBolehLembur();

        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'catatan' => 'required|string|max:255',
        ], [
            'catatan.required' => 'Isi alasan / pekerjaan yang dilembur.',
        ]);

        $tanggal = Carbon::parse($request->tanggal)->startOfDay();

        $sudahAda = Lembur::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $tanggal->toDateString())
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->exists();

        if ($sudahAda) {
            return back()->withInput()->with('error', 'Kamu sudah punya pengajuan lembur di tanggal itu.');
        }

        $shift = $karyawan->shiftPadaTanggal($tanggal);

        Lembur::create([
            'karyawan_id' => $karyawan->id,
            'tanggal' => $tanggal->toDateString(),
            'jam_mulai' => $shift?->waktuPulang($tanggal)->format('H:i:s'),
            'status' => 'menunggu',
            'catatan' => $request->catatan,
        ]);

        return back()->with('success', 'Pengajuan lembur terkirim. Jangan lupa serahkan surat lembur ke payroll besok.');
    }

    /** Batalkan pengajuan yang belum diproses dan belum punya scan pulang lembur. */
    public function batal($id)
    {
        $karyawan = $this->karyawanBolehLembur();

        $lembur = Lembur::where('karyawan_id', $karyawan->id)->findOrFail($id);

        if ($lembur->status !== 'menunggu' || $lembur->presensi_id) {
            return back()->with('error', 'Pengajuan ini sudah tidak bisa dibatalkan.');
        }

        $lembur->delete();

        return back()->with('success', 'Pengajuan lembur dibatalkan.');
    }

    private function karyawanBolehLembur()
    {
        $karyawan = Auth::user()->karyawan;

        abort_unless($karyawan && $karyawan->boleh_lembur, 403, 'Kamu tidak punya izin mengajukan lembur.');

        return $karyawan;
    }
}