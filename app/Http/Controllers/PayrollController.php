<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\Lembur;
use App\Models\TarifLembur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    // ==========================================
    // DASHBOARD PAYROLL
    // ==========================================
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        // Default: minggu berjalan (Senin s/d Sabtu)
        $minggu = $request->query('minggu')
            ? Carbon::parse($request->query('minggu'))->startOfWeek()
            : Carbon::now()->startOfWeek();

        $awal  = $minggu->toDateString();
        $akhir = $minggu->copy()->addDays(5)->toDateString(); // Senin-Sabtu

        $lemburs = Lembur::with(['karyawan.departemen', 'karyawan.jabatan'])
            ->whereBetween('tanggal', [$awal, $akhir])
            ->orderByDesc('tanggal')
            ->paginate(20)
            ->withQueryString();

        // Rekap per status
        $rekap = Lembur::whereBetween('tanggal', [$awal, $akhir])
    ->selectRaw('status, COUNT(*) as jumlah, SUM(total_upah) as total')
    ->groupBy('status')
    ->get()
    ->mapWithKeys(fn($r) => [$r->status => $r->jumlah])
    ->put('total', Lembur::whereBetween('tanggal', [$awal, $akhir])
        ->where('status', 'disetujui')
        ->sum('total_upah'));

        return view('payroll.index', [
            'lemburs'         => $lemburs,
            'rekap'           => $rekap,
            'minggu'          => $minggu,
            'awal'            => $awal,
            'akhir'           => $akhir,
            'mingguSebelumnya' => $minggu->copy()->subWeek()->toDateString(),
            'mingguBerikutnya' => $minggu->copy()->addWeek()->toDateString(),
        ]);
    }

    // ==========================================
    // ACC LEMBUR
    // ==========================================
    public function setujui(Request $request, $id)
    {
        $request->validate([
            'jam_mulai'       => 'required|date_format:H:i',
            'jam_selesai'     => 'required|date_format:H:i|different:jam_mulai',
            'catatan_payroll' => 'nullable|string|max:255',
        ], [
            'jam_selesai.different' => 'Jam selesai tidak boleh sama dengan jam mulai.',
        ]);

        $lembur = Lembur::with('karyawan.jabatan')->findOrFail($id);

        if ($lembur->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        if ($lembur->karyawan_id === Auth::user()->karyawan->id) {
            return back()->with('error', 'Kamu tidak bisa memproses lembur milikmu sendiri.');
        }

        $lembur->jam_mulai  = $request->jam_mulai . ':00';
        $lembur->jam_selesai = $request->jam_selesai . ':00';
        $lembur->hitungUlang();
        $lembur->status          = 'disetujui';
        $lembur->disetujui_oleh  = Auth::id();
        $lembur->waktu_disetujui = now();
        $lembur->catatan_payroll = $request->catatan_payroll;
        $lembur->save();

        $ket = $lembur->memenuhi_syarat ? '' : ' (insentif < 4 jam, upah = 0)';
        return back()->with('success', "Lembur {$lembur->karyawan->nama_lengkap} disetujui — {$lembur->durasi_final_jam} jam{$ket}.");
    }

    public function tolak(Request $request, $id)
    {
        $request->validate([
            'catatan_payroll' => 'required|string|max:255',
        ], [
            'catatan_payroll.required' => 'Isi alasan penolakan.',
        ]);

        $lembur = Lembur::with('karyawan')->findOrFail($id);

        if ($lembur->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $lembur->status          = 'ditolak';
        $lembur->disetujui_oleh  = Auth::id();
        $lembur->waktu_disetujui = now();
        $lembur->catatan_payroll = $request->catatan_payroll;
        $lembur->save();

        return back()->with('success', "Lembur {$lembur->karyawan->nama_lengkap} ditolak.");
    }

    // ==========================================
    // REKAP MINGGUAN (untuk pembayaran Sabtu)
    // ==========================================
    public function rekap(Request $request)
    {
        Carbon::setLocale('id');

        $minggu = $request->query('minggu')
            ? Carbon::parse($request->query('minggu'))->startOfWeek()
            : Carbon::now()->startOfWeek();

        $awal  = $minggu->toDateString();
        $akhir = $minggu->copy()->addDays(5)->toDateString();

        $rekap = Lembur::with(['karyawan.departemen', 'karyawan.jabatan'])
            ->whereBetween('tanggal', [$awal, $akhir])
            ->where('status', 'disetujui')
            ->get()
            ->groupBy('karyawan_id')
            ->map(function ($items) {
                $karyawan    = $items->first()->karyawan;
                $totalJam    = $items->sum('durasi_final_jam');
                $totalUpah   = $items->sum('total_upah');
                return [
                    'karyawan'   => $karyawan,
                    'total_jam'  => $totalJam,
                    'total_upah' => $totalUpah,
                    'detail'     => $items,
                ];
            })
            ->values();

        $grandTotal = $rekap->sum('total_upah');

        return view('payroll.rekap', [
            'rekap'           => $rekap,
            'grandTotal'      => $grandTotal,
            'minggu'          => $minggu,
            'awal'            => $awal,
            'akhir'           => $akhir,
            'mingguSebelumnya' => $minggu->copy()->subWeek()->toDateString(),
            'mingguBerikutnya' => $minggu->copy()->addWeek()->toDateString(),
        ]);
    }

    // ==========================================
    // SLIP LEMBUR (cetak per karyawan per minggu)
    // ==========================================
    public function slip($karyawanId, Request $request)
    {
        Carbon::setLocale('id');

        $minggu = $request->query('minggu')
            ? Carbon::parse($request->query('minggu'))->startOfWeek()
            : Carbon::now()->startOfWeek();

        $awal  = $minggu->toDateString();
        $akhir = $minggu->copy()->addDays(5)->toDateString();

        $karyawan = Karyawan::with(['jabatan', 'departemen'])->findOrFail($karyawanId);

        $lemburs = Lembur::with('karyawan')
            ->where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$awal, $akhir])
            ->where('status', 'disetujui')
            ->orderBy('tanggal')
            ->get();

        $totalJam  = $lemburs->sum('durasi_final_jam');
        $totalUpah = $lemburs->sum('total_upah');

        return view('payroll.slip', compact(
            'karyawan', 'lemburs', 'totalJam', 'totalUpah', 'minggu', 'awal', 'akhir'
        ));
    }

    // ==========================================
    // KELOLA TARIF
    // ==========================================
    public function tarifIndex()
    {
        $departemens = Departemen::orderBy('nama_departemen')->get();
        $tarifs = TarifLembur::with('departemen')
            ->orderBy('departemen_id')
            ->orderBy('kelompok')
            ->orderBy('urutan_jam')
            ->get()
            ->groupBy(fn ($t) => ($t->departemen_id ?? 'default') . '_' . $t->kelompok);

        return view('payroll.tarif', compact('departemens', 'tarifs'));
    }

    public function tarifStore(Request $request)
    {
        $request->validate([
            'departemen_id' => 'nullable|exists:departemen,id',
            'kelompok'      => 'required|in:reguler,insentif',
            'urutan_jam'    => 'required|integer|min:1|max:24',
            'upah_per_jam'  => 'required|numeric|min:0',
        ]);

        TarifLembur::updateOrCreate(
            [
                'departemen_id' => $request->departemen_id ?: null,
                'kelompok'      => $request->kelompok,
                'urutan_jam'    => $request->urutan_jam,
            ],
            ['upah_per_jam' => $request->upah_per_jam]
        );

        return back()->with('success', 'Tarif berhasil disimpan.');
    }

    public function tarifDestroy($id)
    {
        TarifLembur::findOrFail($id)->delete();
        return back()->with('success', 'Tarif dihapus.');
    }
}