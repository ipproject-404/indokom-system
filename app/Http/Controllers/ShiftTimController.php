<?php

namespace App\Http\Controllers;

use App\Models\JadwalShift;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Shift;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShiftTimController extends Controller
{
    /** Batas jumlah hari sekali atur, supaya satu request tidak membuat ribuan baris. */
    private const MAKS_HARI_SEKALI_ATUR = 62;

    /**
     * Papan jadwal mingguan anak buah yang pakai_jadwal_shift = true di
     * SEMUA departemen yang dipimpin. ?minggu=YYYY-MM-DD menggeser minggu.
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $request->validate(['minggu' => 'nullable|date_format:Y-m-d']);

        $karyawan = Auth::user()->karyawan;
        $departemenDipimpin = $karyawan->departemenYangDipimpin();
        $idDepartemen = $departemenDipimpin->pluck('id');

        $semua = Karyawan::with(['jabatan', 'departemen'])
            ->whereIn('departemen_id', $idDepartemen)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        $anakBuah = $semua->where('pakai_jadwal_shift', true)->values();
        $tanpaJadwal = $semua->where('pakai_jadwal_shift', false)->count();

        $mulaiMinggu = $request->filled('minggu')
            ? Carbon::parse($request->query('minggu'))->startOfWeek()
            : Carbon::today()->startOfWeek();

        $hari = collect(range(0, 6))->map(fn ($i) => $mulaiMinggu->copy()->addDays($i));
        $awal = $hari->first()->toDateString();
        $akhir = $hari->last()->toDateString();
        $idAnakBuah = $anakBuah->pluck('id');

        $jadwal = JadwalShift::with('shift')
            ->whereIn('karyawan_id', $idAnakBuah)
            ->whereBetween('tanggal', [$awal, $akhir])
            ->get()
            ->groupBy('karyawan_id')
            ->map(fn ($g) => $g->keyBy(fn ($j) => $j->tanggal->toDateString()));

        // Sel yang sudah punya absensi dikunci (tidak boleh diubah lagi).
        $adaPresensi = Presensi::whereIn('karyawan_id', $idAnakBuah)
            ->whereBetween('tanggal', [$awal, $akhir])
            ->get(['karyawan_id', 'tanggal'])
            ->map(fn ($p) => $p->karyawan_id . '|' . Carbon::parse($p->tanggal)->toDateString())
            ->flip();

        // Hanya shift milik semua PT atau PT anak buah yang ditampilkan.
        $idPerusahaan = $anakBuah->pluck('perusahaan_id')->unique();
        $shifts = Shift::where('status', 'aktif')
            ->where(fn ($q) => $q->whereNull('perusahaan_id')->orWhereIn('perusahaan_id', $idPerusahaan))
            ->orderBy('nama_shift')
            ->get();

        return view('karyawan.shift-tim', [
            'anakBuah' => $anakBuah,
            'tanpaJadwal' => $tanpaJadwal,
            'shifts' => $shifts,
            'departemenDipimpin' => $departemenDipimpin,
            'hari' => $hari,
            'jadwal' => $jadwal,
            'adaPresensi' => $adaPresensi,
            'mingguSebelumnya' => $mulaiMinggu->copy()->subWeek()->toDateString(),
            'mingguBerikutnya' => $mulaiMinggu->copy()->addWeek()->toDateString(),
        ]);
    }

    /**
     * Mengatur shift (atau libur kalau shift_id kosong) untuk satu atau
     * beberapa anak buah, dari tanggal_mulai sampai tanggal_selesai.
     * Tanggal lampau ditolak; tanggal yang sudah punya absensi dilewati.
     */
    public function store(Request $request)
    {
        $request->validate([
            'karyawan_ids' => 'required|array|min:1',
            'karyawan_ids.*' => 'integer|exists:karyawan,id',
            'shift_id' => 'nullable|exists:shift,id',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $mulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
        $selesai = Carbon::parse($request->tanggal_selesai)->startOfDay();

        if ($mulai->diffInDays($selesai) >= self::MAKS_HARI_SEKALI_ATUR) {
            return back()->withInput()->with('error', 'Maksimal ' . self::MAKS_HARI_SEKALI_ATUR . ' hari sekali atur.');
        }

        $ids = array_values(array_unique(array_map('intval', $request->karyawan_ids)));
        $anak = $this->anakBuahYangBolehDiatur($ids);

        // Jaga supaya tidak bisa mengatur karyawan di luar departemen yang
        // dipimpin lewat request yang dimanipulasi.
        if ($anak->count() !== count($ids)) {
            abort(403, 'Ada karyawan yang bukan anak buah berjadwal shift di departemen yang kamu pimpin.');
        }

        $shift = $request->shift_id
            ? Shift::where('status', 'aktif')->findOrFail($request->shift_id)
            : null;

        if ($shift) {
            foreach ($anak as $k) {
                if (! $this->shiftBolehDipakai($shift, $k)) {
                    return back()->withInput()->with('error', "Shift {$shift->nama_shift} bukan milik perusahaan {$k->nama_lengkap}.");
                }
            }
        }

        $tersimpan = 0;
        $dilewati = 0;

        DB::transaction(function () use ($anak, $mulai, $selesai, $shift, $request, &$tersimpan, &$dilewati) {
            foreach ($anak as $k) {
                $sudahAbsen = Presensi::where('karyawan_id', $k->id)
                    ->whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
                    ->pluck('tanggal')
                    ->map(fn ($t) => Carbon::parse($t)->toDateString())
                    ->all();

                foreach (CarbonPeriod::create($mulai, $selesai) as $tgl) {
                    $tanggal = $tgl->toDateString();

                    if (in_array($tanggal, $sudahAbsen, true)) {
                        $dilewati++;
                        continue;
                    }

                    JadwalShift::updateOrCreate(
                        ['karyawan_id' => $k->id, 'tanggal' => $tanggal],
                        [
                            'shift_id' => $shift?->id,
                            'dibuat_oleh' => Auth::id(),
                            'keterangan' => $request->keterangan,
                        ]
                    );
                    $tersimpan++;
                }
            }
        });

        $pesan = "{$tersimpan} jadwal tersimpan.";
        if ($dilewati > 0) {
            $pesan .= " {$dilewati} dilewati karena sudah ada absensi.";
        }

        return back()->with('success', $pesan);
    }

    /**
     * Tukar shift dua anak buah di SATU tanggal. Shift (atau libur) A
     * pindah ke B dan sebaliknya; jadwal hari lain tidak tersentuh.
     */
    public function tukar(Request $request)
    {
        $request->validate([
            'karyawan_a_id' => 'required|integer|exists:karyawan,id',
            'karyawan_b_id' => 'required|integer|exists:karyawan,id|different:karyawan_a_id',
            'tanggal' => 'required|date|after_or_equal:today',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'karyawan_b_id.different' => 'Pilih dua karyawan yang berbeda.',
        ]);

        $ids = [(int) $request->karyawan_a_id, (int) $request->karyawan_b_id];
        $anak = $this->anakBuahYangBolehDiatur($ids)->keyBy('id');

        if ($anak->count() !== 2) {
            abort(403, 'Ada karyawan yang bukan anak buah berjadwal shift di departemen yang kamu pimpin.');
        }

        $a = $anak[$ids[0]];
        $b = $anak[$ids[1]];
        $tanggal = Carbon::parse($request->tanggal)->toDateString();

        if (Presensi::whereIn('karyawan_id', $ids)->whereDate('tanggal', $tanggal)->exists()) {
            return back()->withInput()->with('error', 'Salah satu karyawan sudah punya absensi di tanggal itu, jadwalnya tidak bisa ditukar.');
        }

        $shiftA = JadwalShift::with('shift')->where('karyawan_id', $a->id)->where('tanggal', $tanggal)->first()?->shift;
        $shiftB = JadwalShift::with('shift')->where('karyawan_id', $b->id)->where('tanggal', $tanggal)->first()?->shift;

        if ($shiftA?->id === $shiftB?->id) {
            return back()->withInput()->with('error', 'Keduanya sudah di shift yang sama (atau sama-sama libur) di tanggal itu, tidak ada yang perlu ditukar.');
        }

        if (($shiftB && ! $this->shiftBolehDipakai($shiftB, $a)) || ($shiftA && ! $this->shiftBolehDipakai($shiftA, $b))) {
            return back()->withInput()->with('error', 'Shift salah satu karyawan bukan milik perusahaan karyawan yang satunya, tidak bisa ditukar.');
        }

        DB::transaction(function () use ($a, $b, $shiftA, $shiftB, $tanggal, $request) {
            JadwalShift::updateOrCreate(
                ['karyawan_id' => $a->id, 'tanggal' => $tanggal],
                [
                    'shift_id' => $shiftB?->id,
                    'dibuat_oleh' => Auth::id(),
                    'keterangan' => $request->keterangan ?: 'Tukar shift dengan ' . $b->nama_lengkap,
                ]
            );
            JadwalShift::updateOrCreate(
                ['karyawan_id' => $b->id, 'tanggal' => $tanggal],
                [
                    'shift_id' => $shiftA?->id,
                    'dibuat_oleh' => Auth::id(),
                    'keterangan' => $request->keterangan ?: 'Tukar shift dengan ' . $a->nama_lengkap,
                ]
            );
        });

        return back()->with('success', "Shift {$a->nama_lengkap} dan {$b->nama_lengkap} pada "
            . Carbon::parse($tanggal)->translatedFormat('d F Y') . ' berhasil ditukar.');
    }

    /** Anak buah aktif, berjadwal shift, di departemen yang dipimpin user login. */
    private function anakBuahYangBolehDiatur(array $ids)
    {
        $idDepartemen = Auth::user()->karyawan->departemenYangDipimpin()->pluck('id');

        return Karyawan::whereIn('id', $ids)
            ->whereIn('departemen_id', $idDepartemen)
            ->where('status', 'aktif')
            ->where('pakai_jadwal_shift', true)
            ->get();
    }

    /** Shift tanpa perusahaan_id berlaku untuk semua PT; selain itu harus sama dengan PT karyawan. */
    private function shiftBolehDipakai(Shift $shift, Karyawan $karyawan): bool
    {
        return $shift->perusahaan_id === null
            || (int) $shift->perusahaan_id === (int) $karyawan->perusahaan_id;
    }
}