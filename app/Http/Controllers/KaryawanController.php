<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Departemen;
use App\Models\Presensi;
use App\Models\Perusahaan;
use App\Models\Divisi;
use App\Models\TipeKaryawan;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        // EAGER LOAD RELASI
        $query = Karyawan::with([
            'jabatan',
            'departemen.divisi',
            'perusahaan',
            'tipeKaryawan',
        ]);

        // SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik_kerja', 'like', "%{$search}%")
                  ->orWhere('nik_ktp', 'like', "%{$search}%");
            });
        }

        // FILTER PERUSAHAAN
        if ($request->filled('perusahaan')) {
            $query->where('perusahaan_id', $request->perusahaan);
        }

        // FILTER DIVISI
        if ($request->filled('divisi')) {
            $query->whereHas('departemen', function ($q) use ($request) {
                $q->where('divisi_id', $request->divisi);
            });
        }

        // FILTER DEPARTEMEN
        if ($request->filled('departemen')) {
            $query->where('departemen_id', $request->departemen);
        }

        // FILTER JABATAN
        if ($request->filled('jabatan')) {
            $query->where('jabatan_id', $request->jabatan);
        }

        // FILTER JENIS KELAMIN
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        // FILTER PENDIDIKAN
        if ($request->filled('pendidikan')) {
            $query->where('pendidikan', 'like', $request->pendidikan . '%');
        }

        // FILTER UMUR
        if ($request->filled('umur_min')) {
            $query->whereRaw(
                "TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= ?",
                [(int) $request->umur_min]
            );
        }
        if ($request->filled('umur_max')) {
            $query->whereRaw(
                "TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) <= ?",
                [(int) $request->umur_max]
            );
        }

        // FILTER STATUS
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ==========================================
        // EXPORT EXCEL (XLS - merge bertingkat 3 level)
        // ==========================================
        if ($request->has('export') && $request->export == 'excel') {

            // ⭐ Ambil data & sort pakai collection (agar bisa akses relasi divisi)
            $karyawans = $query->get();

            $karyawans = $karyawans->sortBy(function ($k) {
                return sprintf(
                    '%08d|%08d|%08d|%s',
                    $k->perusahaan_id ?? 0,
                    $k->departemen->divisi_id ?? 0,
                    $k->departemen_id ?? 0,
                    strtolower($k->nama_lengkap ?? '')
                );
            })->values();

            $total = $karyawans->count();

            // ============= PRECOMPUTE ROWSPAN =============
            $rowspanPT   = [];
            $rowspanDiv  = [];
            $rowspanDept = [];

            for ($i = 0; $i < $total; $i++) {
                $cur  = $karyawans[$i];
                $prev = $i > 0 ? $karyawans[$i - 1] : null;

                $curPT   = $cur->perusahaan_id;
                $curDiv  = $cur->departemen->divisi_id ?? 0;
                $curDept = $cur->departemen_id;

                // ---- Level 1: PT ----
                if ($prev === null || $prev->perusahaan_id != $curPT) {
                    $count = 0;
                    for ($j = $i; $j < $total; $j++) {
                        if ($karyawans[$j]->perusahaan_id == $curPT) $count++;
                        else break;
                    }
                    $rowspanPT[$i] = $count;
                } else {
                    $rowspanPT[$i] = 0;
                }

                // ---- Level 2: Divisi (dalam PT yang sama) ----
                if ($prev === null
                    || $prev->perusahaan_id != $curPT
                    || ($prev->departemen->divisi_id ?? 0) != $curDiv) {
                    $count = 0;
                    for ($j = $i; $j < $total; $j++) {
                        if ($karyawans[$j]->perusahaan_id == $curPT
                            && ($karyawans[$j]->departemen->divisi_id ?? 0) == $curDiv) {
                            $count++;
                        } else break;
                    }
                    $rowspanDiv[$i] = $count;
                } else {
                    $rowspanDiv[$i] = 0;
                }

                // ---- Level 3: Departemen (dalam PT + Divisi yang sama) ----
                if ($prev === null
                    || $prev->perusahaan_id != $curPT
                    || ($prev->departemen->divisi_id ?? 0) != $curDiv
                    || $prev->departemen_id != $curDept) {
                    $count = 0;
                    for ($j = $i; $j < $total; $j++) {
                        if ($karyawans[$j]->perusahaan_id == $curPT
                            && ($karyawans[$j]->departemen->divisi_id ?? 0) == $curDiv
                            && $karyawans[$j]->departemen_id == $curDept) {
                            $count++;
                        } else break;
                    }
                    $rowspanDept[$i] = $count;
                } else {
                    $rowspanDept[$i] = 0;
                }
            }

            // ============= STYLE =============
            $filename = "Data_Karyawan_" . date('Y-m-d_H-i-s') . ".xls";

            $styleThBase       = "border:1px solid #FFFFFF; padding:8px 6px; background-color:#1E3A8A; color:#FFFFFF; font-weight:bold; text-align:center; vertical-align:middle; font-family:Calibri; font-size:11pt;";
            $styleTdBase       = "border:1px solid #94A3B8; padding:6px 8px; vertical-align:middle; font-family:Calibri; font-size:11pt;";
            $styleTdMerged     = "border:1px solid #94A3B8; padding:6px 8px; vertical-align:middle; text-align:center; font-weight:bold; font-family:Calibri; font-size:11pt;";
            $styleTextFormat   = "mso-number-format:'\@';";
            $styleNumberFormat = "mso-number-format:'0';";

            $bgTempat    = "background-color:#FEF9C3;";
            $bgUrutan    = "background-color:#FFE4E6;";
            $bgJabatan   = "background-color:#FED7AA;";
            $bgIdentitas = "background-color:#DBEAFE;";
            $bgPeriode   = "background-color:#DCFCE7;";
            $bgKepeg     = "background-color:#E9D5FF;";
            $bgPribadi   = "background-color:#FCE7F3;";

            // ============= BUILD HTML =============
            $html  = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
            $html .= '<head><meta charset="UTF-8">';
            $html .= '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
            $html .= '<x:Name>Data Karyawan</x:Name>';
            $html .= '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>';
            $html .= '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            $html .= '</head><body>';
            $html .= '<table border="1" cellpadding="4" cellspacing="0">';

            // HEADER
            $html .= '<thead><tr>';
            $html .= '<th style="' . $styleThBase . '" width="180">Perusahaan</th>';
            $html .= '<th style="' . $styleThBase . '" width="100">Divisi</th>';
            $html .= '<th style="' . $styleThBase . '" width="160">Departemen</th>';
            $html .= '<th style="' . $styleThBase . '" width="60">No Urut</th>';
            $html .= '<th style="' . $styleThBase . '" width="180">Jabatan</th>';
            $html .= '<th style="' . $styleThBase . '" width="170">NIK KTP</th>';
            $html .= '<th style="' . $styleThBase . '" width="110">NIK Kerja</th>';
            $html .= '<th style="' . $styleThBase . '" width="100">Awal Masuk</th>';
            $html .= '<th style="' . $styleThBase . '" width="100">Lama Kerja</th>';
            $html .= '<th style="' . $styleThBase . '" width="190">Nama Lengkap</th>';
            $html .= '<th style="' . $styleThBase . '" width="140">Tipe Karyawan</th>';
            $html .= '<th style="' . $styleThBase . '" width="80">JK</th>';
            $html .= '<th style="' . $styleThBase . '" width="120">No HP</th>';
            $html .= '<th style="' . $styleThBase . '" width="200">Pendidikan</th>';
            $html .= '<th style="' . $styleThBase . '" width="100">Tgl Lahir</th>';
            $html .= '<th style="' . $styleThBase . '" width="60">Umur</th>';
            $html .= '<th style="' . $styleThBase . '" width="80">Status</th>';
            $html .= '</tr></thead><tbody>';

            $no = 1;
            foreach ($karyawans as $i => $kry) {

                $tglLahir = $kry->tanggal_lahir
                    ? \Carbon\Carbon::parse($kry->tanggal_lahir)->format('d/m/Y')
                    : '-';
                $umur = $kry->tanggal_lahir
                    ? \Carbon\Carbon::parse($kry->tanggal_lahir)->age
                    : '-';
                $tglMasuk = $kry->tanggal_masuk
                    ? \Carbon\Carbon::parse($kry->tanggal_masuk)->format('d/m/Y')
                    : '-';

                $lamaKerja = '-';
                if ($kry->tanggal_masuk) {
                    $diff  = \Carbon\Carbon::parse($kry->tanggal_masuk)->diff(now());
                    $parts = [];
                    if ($diff->y > 0) $parts[] = $diff->y . ' tahun';
                    if ($diff->m > 0) $parts[] = $diff->m . ' bulan';
                    if (empty($parts)) $parts[] = $diff->d . ' hari';
                    $lamaKerja = implode(' ', $parts);
                }

                $statusText  = strtoupper($kry->status ?? '-');
                $statusWarna = $kry->status === 'aktif'
                    ? 'color:#15803D; font-weight:bold;'
                    : 'color:#B91C1C; font-weight:bold;';
                $jkText = $kry->jenis_kelamin === 'L' ? 'L' : 'P';

                $html .= '<tr>';

                // ⭐ Kolom PT - merge lintas seluruh PT
                if ($rowspanPT[$i] > 0) {
                    $html .= '<td rowspan="' . $rowspanPT[$i] . '" style="' . $styleTdMerged . $bgTempat . '">'
                           . e($kry->perusahaan->nama_perusahaan ?? '-') . '</td>';
                }

                // ⭐ Kolom Divisi - merge dalam PT yang sama
                if ($rowspanDiv[$i] > 0) {
                    $html .= '<td rowspan="' . $rowspanDiv[$i] . '" style="' . $styleTdMerged . $bgTempat . '">'
                           . e($kry->departemen->divisi->nama_divisi ?? '-') . '</td>';
                }

                // ⭐ Kolom Departemen - merge dalam Divisi yang sama
                if ($rowspanDept[$i] > 0) {
                    $html .= '<td rowspan="' . $rowspanDept[$i] . '" style="' . $styleTdMerged . $bgTempat . '">'
                           . e($kry->departemen->nama_departemen ?? '-') . '</td>';
                }

                // Kolom per baris
                $html .= '<td style="' . $styleTdBase . $bgUrutan . $styleNumberFormat . ' text-align:center;">' . $no++ . '</td>';
                $html .= '<td style="' . $styleTdBase . $bgJabatan . '">'   . e($kry->jabatan->nama_jabatan ?? '-') . '</td>';
                $html .= '<td style="' . $styleTdBase . $bgIdentitas . $styleTextFormat . ' text-align:center;">' . e($kry->nik_ktp ?? '-') . '</td>';
                $html .= '<td style="' . $styleTdBase . $bgIdentitas . $styleTextFormat . ' text-align:center;">' . e($kry->nik_kerja ?? '-') . '</td>';
                $html .= '<td style="' . $styleTdBase . $bgPeriode . ' text-align:center;">' . $tglMasuk . '</td>';
                $html .= '<td style="' . $styleTdBase . $bgPeriode . ' text-align:center;">' . $lamaKerja . '</td>';
                $html .= '<td style="' . $styleTdBase . $bgIdentitas . '">' . e($kry->nama_lengkap ?? '-') . '</td>';
                $html .= '<td style="' . $styleTdBase . $bgKepeg . '">'     . e($kry->tipeKaryawan->nama ?? '-') . '</td>';
                $html .= '<td style="' . $styleTdBase . $bgKepeg . ' text-align:center;">' . $jkText . '</td>';
                $html .= '<td style="' . $styleTdBase . $bgKepeg . $styleTextFormat . ' text-align:center;">' . e($kry->no_hp ?? '-') . '</td>';
                $html .= '<td style="' . $styleTdBase . $bgKepeg . '">'     . e($kry->pendidikan ?? '-') . '</td>';
                $html .= '<td style="' . $styleTdBase . $bgPribadi . ' text-align:center;">' . $tglLahir . '</td>';
                $html .= '<td style="' . $styleTdBase . $bgPribadi . $styleNumberFormat . ' text-align:center;">' . $umur . '</td>';
                $html .= '<td style="' . $styleTdBase . $bgPribadi . $statusWarna . ' text-align:center;">' . $statusText . '</td>';

                $html .= '</tr>';
            }

            $html .= '</tbody></table></body></html>';

            return response("\xEF\xBB\xBF" . $html, 200, [
                'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control'       => 'max-age=0',
                'Pragma'              => 'public',
            ]);
        }

        // PAGINATION
        $karyawans = $query->orderBy('nama_lengkap', 'asc')
                           ->paginate(15)
                           ->withQueryString();

        // DATA DROPDOWN FILTER
        $divisis = Divisi::where('status', 'aktif')->orderBy('nama_divisi')->get();
        $departemens = Departemen::orderBy('nama_departemen')->get();
        $jabatans = Jabatan::orderBy('nama_jabatan')->get();
        $perusahaans = Perusahaan::where('status', 'aktif')->orderBy('nama_perusahaan')->get();
        $opsiPendidikan = ['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3'];

        return view('karyawan.index', compact(
            'karyawans',
            'divisis',
            'departemens',
            'jabatans',
            'perusahaans',
            'opsiPendidikan'
        ));
    }

    public function create()
    {
        $perusahaans = Perusahaan::where('status', 'aktif')->orderBy('nama_perusahaan')->get();
        $tipeKaryawans = TipeKaryawan::all();
        $shifts = Shift::where('status', 'aktif')->orderBy('nama_shift')->get();
        $shiftDefaultId = $shifts->firstWhere('nama_shift', 'Reguler')?->id;

        return view('karyawan.create', compact('perusahaans', 'tipeKaryawans', 'shifts', 'shiftDefaultId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik_ktp' => 'required|digits:16|unique:karyawan,nik_ktp',
            'nik_kerja' => 'required|unique:karyawan,nik_kerja',
            'nama_lengkap' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required|numeric',
            'tingkat_pendidikan' => 'required',
            'nama_sekolah' => 'nullable|string|max:255',

            'perusahaan_id' => 'required|exists:perusahaan,id',
            'tipe_karyawan_id' => 'required|exists:tipe_karyawan,id',
            'departemen_id' => 'required|exists:departemen,id',
            'jabatan_id' => 'required|exists:jabatan,id',
            'shift_id' => 'nullable|exists:shift,id',
            'pakai_jadwal_shift' => 'nullable|boolean',

            'tanggal_masuk' => 'required|date',
        ]);

        $tipe = TipeKaryawan::find($request->tipe_karyawan_id);
        $pakaiJadwalShift = $request->boolean('pakai_jadwal_shift') && $tipe?->dasar_absensi === 'jadwal';

        if ($pesanShift = $this->cekShift($request, $tipe, $pakaiJadwalShift)) {
            return back()->withInput()->withErrors(['shift_id' => $pesanShift]);
        }

        $tingkat = $request->tingkat_pendidikan;
        $namaSekolah = trim($request->nama_sekolah ?? '');
        $pendidikanGabung = $namaSekolah ? "{$tingkat}-{$namaSekolah}" : $tingkat;

        $karyawan = Karyawan::create([
            'nik_ktp' => $request->nik_ktp,
            'nik_kerja' => $request->nik_kerja,
            'barcode_uid' => Str::random(40),
            'nama_lengkap' => $request->nama_lengkap,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'pendidikan' => $pendidikanGabung,

            'perusahaan_id'    => $request->perusahaan_id,
            'tipe_karyawan_id' => $request->tipe_karyawan_id,
            'departemen_id'    => $request->departemen_id,
            'jabatan_id'       => $request->jabatan_id,
            'shift_id'         => $request->shift_id ?: null,
            'pakai_jadwal_shift' => $pakaiJadwalShift,

            'tanggal_masuk' => $request->tanggal_masuk,
            'status' => 'aktif'
        ]);

        $username_baru = strtolower(str_replace(' ', '', $request->nama_lengkap));
        if (User::where('username', $username_baru)->exists()) {
            $username_baru .= rand(10, 99);
        }

        User::create([
            'karyawan_id' => $karyawan->id,
            'username' => $username_baru,
            'password' => Hash::make('passwor123'),
            'role' => 'karyawan',
            'status' => 'aktif'
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Data Karyawan berhasil ditambahkan!');
    }

    public function show($id)
    {
        $karyawan = Karyawan::with([
            'jabatan',
            'departemen.divisi',
            'perusahaan',
            'tipeKaryawan',
            'shift',
        ])->findOrFail($id);

        $akun = User::where('karyawan_id', $id)->first();

        $riwayat_presensi = Presensi::where('karyawan_id', $id)
                                    ->orderBy('tanggal', 'desc')
                                    ->take(10)
                                    ->get();

        return view('karyawan.show', compact('karyawan', 'akun', 'riwayat_presensi'));
    }

    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $perusahaans = Perusahaan::where('status', 'aktif')->orderBy('nama_perusahaan')->get();
        $tipeKaryawans = TipeKaryawan::all();
        $shifts = Shift::where('status', 'aktif')->orderBy('nama_shift')->get();

        $divisis = Divisi::where('status', 'aktif')->orderBy('nama_divisi')->get();
        $departemens = Departemen::where('status', 'aktif')->orderBy('nama_departemen')->get();
        $jabatans = Jabatan::where('departemen_id', $karyawan->departemen_id)->get();

        return view('karyawan.edit', compact(
            'karyawan',
            'perusahaans',
            'divisis',
            'departemens',
            'jabatans',
            'tipeKaryawans',
            'shifts'
        ));
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $request->validate([
            'nik_ktp' => 'required|digits:16|unique:karyawan,nik_ktp,' . $id,
            'nik_kerja' => 'required|unique:karyawan,nik_kerja,' . $id,
            'nama_lengkap' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required|numeric',
            'tingkat_pendidikan' => 'nullable',
            'nama_sekolah' => 'nullable|string|max:255',
            'perusahaan_id' => 'required|exists:perusahaan,id',
            'tipe_karyawan_id' => 'required|exists:tipe_karyawan,id',
            'departemen_id' => 'required|exists:departemen,id',
            'jabatan_id' => 'required|exists:jabatan,id',
            'shift_id' => 'nullable|exists:shift,id',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'nullable|date',
            'status' => 'required|in:aktif,nonaktif',
            'pakai_jadwal_shift' => 'nullable|boolean',
        ]);

        $tipe = TipeKaryawan::find($request->tipe_karyawan_id);
        $pakaiJadwalShift = $request->boolean('pakai_jadwal_shift') && $tipe?->dasar_absensi === 'jadwal';

        if ($pesanShift = $this->cekShift($request, $tipe, $pakaiJadwalShift)) {
            return back()->withInput()->withErrors(['shift_id' => $pesanShift]);
        }

        $pendidikanGabung = $karyawan->pendidikan;
        if ($request->filled('tingkat_pendidikan')) {
            $namaSekolah = trim($request->nama_sekolah ?? '');
            $pendidikanGabung = $namaSekolah
                ? "{$request->tingkat_pendidikan}-{$namaSekolah}"
                : $request->tingkat_pendidikan;
        }

        $karyawan->update([
            'nik_ktp' => $request->nik_ktp,
            'nik_kerja' => $request->nik_kerja,
            'nama_lengkap' => $request->nama_lengkap,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'pendidikan' => $pendidikanGabung,
            'perusahaan_id' => $request->perusahaan_id,
            'tipe_karyawan_id' => $request->tipe_karyawan_id,
            'departemen_id' => $request->departemen_id,
            'jabatan_id' => $request->jabatan_id,
            'shift_id' => $request->shift_id ?: null,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_keluar' => $request->tanggal_keluar,
            'status' => $request->status,
            'pakai_jadwal_shift' => $pakaiJadwalShift,
        ]);

        if ($request->status === 'nonaktif') {
            User::where('karyawan_id', $id)->update(['status' => 'nonaktif']);
        } elseif ($request->status === 'aktif') {
            User::where('karyawan_id', $id)->update(['status' => 'aktif']);
        }

        return redirect()->route('karyawan.show', $karyawan->id)
                         ->with('success', 'Data Karyawan berhasil diperbarui!');
    }

    private function cekShift(Request $request, ?TipeKaryawan $tipe, bool $pakaiJadwalShift): ?string
    {
        $shiftId = $request->shift_id ?: null;

        if ($tipe?->dasar_absensi === 'jadwal' && ! $pakaiJadwalShift && ! $shiftId) {
            return 'Pilih shift tetap untuk karyawan bulanan ini, atau centang "Pakai jadwal shift bergilir".';
        }

        if ($shiftId) {
            $shift = Shift::find($shiftId);
            if ($shift && $shift->perusahaan_id !== null
                && (int) $shift->perusahaan_id !== (int) $request->perusahaan_id) {
                return "Shift {$shift->nama_shift} bukan milik perusahaan yang dipilih.";
            }
        }

        return null;
    }

    public function getDivisi($perusahaan_id)
    {
        $perusahaan = Perusahaan::find($perusahaan_id);
        if (!$perusahaan) {
            return response()->json([]);
        }

        $divisis = Divisi::where('grup_id', $perusahaan->grup_id)
                         ->where('status', 'aktif')
                         ->orderBy('nama_divisi')
                         ->get(['id', 'nama_divisi']);

        return response()->json($divisis);
    }

    public function getDepartemen($divisi_id)
    {
        $departemens = Departemen::where('divisi_id', $divisi_id)
                                 ->where('status', 'aktif')
                                 ->orderBy('nama_departemen')
                                 ->get(['id', 'nama_departemen']);

        return response()->json($departemens);
    }

    public function getJabatan($departemen_id)
    {
        $jabatans = Jabatan::where('departemen_id', $departemen_id)
                           ->orderBy('nama_jabatan')
                           ->get(['id', 'nama_jabatan']);

        return response()->json($jabatans);
    }

    public function cetakQr($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return view('karyawan.qr', compact('karyawan'));
    }

    public function qrIndex()
    {
        $karyawans = Karyawan::with(['departemen', 'jabatan'])->get();
        return view('karyawan.manajemen_qr', compact('karyawans'));
    }

    public function cetakQrMassal(Request $request)
    {
        $ids = explode(',', $request->query('ids'));

        $karyawans = Karyawan::with(['departemen', 'jabatan'])
                        ->whereIn('id', $ids)
                        ->get();

        return view('karyawan.qr_massal', compact('karyawans'));
    }

    public function resetPassword($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $akun = User::where('karyawan_id', $id)->first();

        if (!$akun) {
            return back()->with('error', 'Akun login tidak ditemukan untuk karyawan ini.');
        }

        $akun->update([
            'password' => Hash::make('passwor123'),
        ]);

        return back()->with('success', 'Password ' . $karyawan->nama_lengkap . ' berhasil direset ke "passwor123".');
    }
}