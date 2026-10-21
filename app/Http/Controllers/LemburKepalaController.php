<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Lembur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LemburKepalaController extends Controller
{
    public function index()
    {
        Carbon::setLocale('id');

        $karyawan        = Auth::user()->karyawan;
        $idDepartemen    = $karyawan->departemenYangDipimpin()->pluck('id');

        $anakBuahReguler = Karyawan::with(['jabatan', 'departemen'])
            ->whereIn('departemen_id', $idDepartemen)
            ->where('status', 'aktif')
            ->get()
            ->filter(fn($k) => ! Lembur::adalahInsentif($k))
            ->values();

        $riwayat = Lembur::with(['karyawan.departemen', 'karyawan.jabatan'])
            ->whereIn('karyawan_id', $anakBuahReguler->pluck('id'))
            ->orderByDesc('tanggal')
            ->limit(50)
            ->get();

        return view('karyawan.lembur-kepala', compact('anakBuahReguler', 'riwayat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tanggal'     => 'required|date',
            'catatan'     => 'required|string|max:255',
        ], [
            'karyawan_id.required' => 'Pilih karyawan.',
            'catatan.required'     => 'Isi alasan lembur.',
        ]);

        $karyawan = $this->cekAnakBuahReguler((int) $request->karyawan_id);

        $tanggal = Carbon::parse($request->tanggal)->startOfDay();

        $sudahAda = Lembur::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $tanggal->toDateString())
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->exists();

        if ($sudahAda) {
            return back()->withInput()
                ->with('error', "{$karyawan->nama_lengkap} sudah punya pengajuan lembur di tanggal itu.");
        }

        $shift    = $karyawan->shiftPadaTanggal($tanggal);
        $jamMulai = $shift?->waktuPulang($tanggal)->format('H:i:s');

        Lembur::create([
            'karyawan_id' => $karyawan->id,
            'tanggal'     => $tanggal->toDateString(),
            'jam_mulai'   => $jamMulai,
            'status'      => 'menunggu',
            'catatan'     => $request->catatan,
        ]);

        return back()->with('success', "Lembur {$karyawan->nama_lengkap} berhasil diajukan.");
    }

    public function batal($id)
    {
        $lembur   = Lembur::findOrFail($id);
        $karyawan = $this->cekAnakBuahReguler($lembur->karyawan_id);

        if ($lembur->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan ini sudah tidak bisa dibatalkan.');
        }

        $lembur->delete();

        return back()->with('success', "Pengajuan lembur {$karyawan->nama_lengkap} dibatalkan.");
    }

    private function cekAnakBuahReguler(int $karyawanId): Karyawan
    {
        $idDepartemen = Auth::user()->karyawan->departemenYangDipimpin()->pluck('id');

        $karyawan = Karyawan::with('jabatan')
            ->whereIn('departemen_id', $idDepartemen)
            ->where('status', 'aktif')
            ->findOrFail($karyawanId);

        abort_if(Lembur::adalahInsentif($karyawan), 403, 'Karyawan insentif tidak bisa diajukan lembur.');

        return $karyawan;
    }
}