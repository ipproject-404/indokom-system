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
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::with([
            'jabatan',
            'departemen.divisi',
            'perusahaan',
            'tipeKaryawan',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik_kerja', 'like', "%{$search}%");
            });
        }
        if ($request->filled('departemen')) {
            $query->where('departemen_id', $request->departemen);
        }
        if ($request->filled('jabatan')) {
            $query->where('jabatan_id', $request->jabatan);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // EXPORT EXCEL
        if ($request->has('export') && $request->export == 'excel') {
            $karyawans = $query->orderBy('created_at', 'desc')->get();

            $filename = "Data_Karyawan_" . date('Y-m-d_H-i-s') . ".csv";
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
            ];

            $columns = ['No', 'NIK KTP', 'NIK Kerja', 'Nama Lengkap', 'Perusahaan', 'Divisi', 'Departemen', 'Jabatan', 'Tipe Karyawan', 'Jenis Kelamin', 'No HP', 'Status', 'Tanggal Masuk'];

            $callback = function() use($karyawans, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns, ';');

                $no = 1;
                foreach ($karyawans as $kry) {
                    fputcsv($file, [
                        $no++,
                        "'" . $kry->nik_ktp,
                        "'" . $kry->nik_kerja,
                        $kry->nama_lengkap,
                        $kry->perusahaan->nama_perusahaan ?? '-',
                        $kry->departemen->divisi->nama_divisi ?? '-',
                        $kry->departemen->nama_departemen ?? '-',
                        $kry->jabatan->nama_jabatan ?? '-',
                        $kry->tipeKaryawan->nama ?? '-',
                        $kry->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                        "'" . $kry->no_hp,
                        strtoupper($kry->status),
                        $kry->tanggal_masuk
                    ], ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        $karyawans = $query->orderBy('created_at', 'desc')->get();

        $departemens = Departemen::all();
        $jabatans = Jabatan::all();

        return view('karyawan.index', compact('karyawans', 'departemens', 'jabatans'));
    }

    public function create()
    {
        $perusahaans = Perusahaan::where('status', 'aktif')->orderBy('nama_perusahaan')->get();
        $tipeKaryawans = TipeKaryawan::all();

        return view('karyawan.create', compact('perusahaans', 'tipeKaryawans'));
    }

    public function store(Request $request)
    {
        // ==========================================
        // VALIDASI
        // ==========================================
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

            'tanggal_masuk' => 'required|date',
        ]);

        // ==========================================
        // GABUNGKAN PENDIDIKAN
        // ==========================================
        $tingkat = $request->tingkat_pendidikan;
        $namaSekolah = trim($request->nama_sekolah ?? '');
        $pendidikanGabung = $namaSekolah ? "{$tingkat}-{$namaSekolah}" : $tingkat;

        // ==========================================
        // SIMPAN KARYAWAN
        // ==========================================
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

            'tanggal_masuk' => $request->tanggal_masuk,
            'status' => 'aktif'
        ]);

        // ==========================================
        // BUAT AKUN USER OTOMATIS
        // ==========================================
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

        $divisis = Divisi::where('status', 'aktif')->orderBy('nama_divisi')->get();
        $departemens = Departemen::where('status', 'aktif')->orderBy('nama_departemen')->get();
        $jabatans = Jabatan::where('departemen_id', $karyawan->departemen_id)->get();

        return view('karyawan.edit', compact(
            'karyawan',
            'perusahaans',
            'divisis',
            'departemens',
            'jabatans',
            'tipeKaryawans'
        ));
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

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
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'pendidikan' => $pendidikanGabung,
            'perusahaan_id' => $request->perusahaan_id ?? $karyawan->perusahaan_id,
            'tipe_karyawan_id' => $request->tipe_karyawan_id ?? $karyawan->tipe_karyawan_id,
            'departemen_id' => $request->departemen_id ?? $karyawan->departemen_id,
            'jabatan_id' => $request->jabatan_id ?? $karyawan->jabatan_id,
            'status' => $request->status ?? $karyawan->status,
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Data Karyawan berhasil diperbarui!');
    }

    // ==========================================
    // AJAX CASCADING DROPDOWN
    // ==========================================

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
}