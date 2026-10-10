<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LemburPayrollController extends Controller
{
    private const STATUS_VALID = ['menunggu', 'disetujui', 'ditolak', 'semua'];

    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $status = in_array($request->query('status'), self::STATUS_VALID, true)
            ? $request->query('status')
            : 'menunggu';

        $lemburs = Lembur::with('karyawan.departemen')
            ->when($status !== 'semua', fn ($q) => $q->where('status', $status))
            ->orderByDesc('tanggal')
            ->paginate(15)
            ->withQueryString();

        return view('karyawan.lembur-payroll', [
            'lemburs' => $lemburs,
            'status' => $status,
            'idKaryawanLogin' => Auth::user()->karyawan->id,
        ]);
    }

    /**
     * Setujui. Jam mulai/selesai dikirim dari form (sudah terisi dari
     * sistem); kalau payroll mengubahnya sesuai surat, itu yang disimpan
     * sebagai koreksi. jam_selesai_scan tetap asli.
     */
    public function setujui(Request $request, $id)
    {
        $request->validate([
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|different:jam_mulai',
            'catatan_payroll' => 'nullable|string|max:255',
        ], [
            'jam_selesai.different' => 'Jam selesai tidak boleh sama dengan jam mulai.',
        ]);

        $lembur = Lembur::with('karyawan')->findOrFail($id);

        if ($pesan = $this->cekBolehProses($lembur)) {
            return back()->with('error', $pesan);
        }

        $lembur->jam_mulai = $request->jam_mulai . ':00';
        $lembur->jam_selesai = $request->jam_selesai . ':00';
        $lembur->hitungUlang();
        $lembur->status = 'disetujui';
        $lembur->disetujui_oleh = Auth::id();
        $lembur->waktu_disetujui = now();
        $lembur->catatan_payroll = $request->catatan_payroll;
        $lembur->save();

        $dikoreksi = $lembur->jam_selesai_scan
            && substr($lembur->jam_selesai, 0, 5) !== substr($lembur->jam_selesai_scan, 0, 5);

        return back()->with('success', "Lembur {$lembur->karyawan->nama_lengkap} disetujui ({$lembur->durasi_jam} jam)"
            . ($dikoreksi ? ', dengan koreksi jam.' : '.'));
    }

    public function tolak(Request $request, $id)
    {
        $request->validate([
            'catatan_payroll' => 'required|string|max:255',
        ], [
            'catatan_payroll.required' => 'Isi alasan penolakan di kolom catatan payroll.',
        ]);

        $lembur = Lembur::with('karyawan')->findOrFail($id);

        if ($pesan = $this->cekBolehProses($lembur)) {
            return back()->with('error', $pesan);
        }

        $lembur->status = 'ditolak';
        $lembur->disetujui_oleh = Auth::id();
        $lembur->waktu_disetujui = now();
        $lembur->catatan_payroll = $request->catatan_payroll;
        $lembur->save();

        return back()->with('success', "Lembur {$lembur->karyawan->nama_lengkap} ditolak.");
    }

    /** Return pesan error, atau null kalau boleh diproses. */
    private function cekBolehProses(Lembur $lembur): ?string
    {
        if ($lembur->status !== 'menunggu') {
            return 'Pengajuan ini sudah diproses.';
        }

        if ($lembur->karyawan_id === Auth::user()->karyawan->id) {
            return 'Kamu tidak bisa memproses lembur milikmu sendiri.';
        }

        return null;
    }
}