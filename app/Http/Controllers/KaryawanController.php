<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Departemen;
use App\Models\Presensi;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        // Siapkan Query dasar (beserta relasi)
        $query = Karyawan::with(['jabatan', 'departemen']);

        // Filter Pencarian Teks (Nama atau NIK)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik_kerja', 'like', "%{$search}%");
            });
        }

        // Filter Departemen
        if ($request->has('departemen') && $request->departemen != '') {
            $query->where('departemen_id', $request->departemen);
        }

        // Filter Jabatan
        if ($request->has('jabatan') && $request->jabatan != '') {
            $query->where('jabatan_id', $request->jabatan);
        }

        // Filter Status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // ==========================================
        // FITUR EXPORT EXCEL (CSV Format)
        // ==========================================
        if ($request->has('export') && $request->export == 'excel') {
            $karyawans = $query->orderBy('created_at', 'desc')->get();
            
            $filename = "Data_Karyawan_" . date('Y-m-d_H-i-s') . ".csv";
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $columns = ['No', 'NIK KTP', 'NIK Kerja', 'Nama Lengkap', 'Departemen', 'Jabatan', 'Jenis Kelamin', 'No HP', 'Status', 'Tanggal Masuk'];

            $callback = function() use($karyawans, $columns) {
                $file = fopen('php://output', 'w');
                
                // Tambahkan titik koma (;) sebagai pemisah kolom
                fputcsv($file, $columns, ';');
                
                $no = 1;
                foreach ($karyawans as $kry) {
                    fputcsv($file, [
                        $no++,
                        "'" . $kry->nik_ktp, 
                        "'" . $kry->nik_kerja,
                        $kry->nama_lengkap,
                        $kry->departemen->nama_departemen ?? '-',
                        $kry->jabatan->nama_jabatan ?? '-',
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

        // Eksekusi query dengan urutan terbaru untuk tampilan HTML
        $karyawans = $query->orderBy('created_at', 'desc')->get();

        // Ambil data untuk opsi dropdown filter
        $departemens = Departemen::all();
        $jabatans = Jabatan::all();

        return view('karyawan.index', compact('karyawans', 'departemens', 'jabatans'));
    }

    public function create()
    {
        $jabatans = Jabatan::all();
        $departemens = Departemen::all();
        
        return view('karyawan.create', compact('jabatans', 'departemens'));
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
            'jabatan_id' => 'required',
            'departemen_id' => 'required',
            'tanggal_masuk' => 'required|date',
        ]);

        // Menggabungkan tingkat pendidikan (misal: S1) dengan nama sekolah/institusi (misal: Unila)
        $tingkat = $request->tingkat_pendidikan;
        $nama_sekolah = trim($request->nama_sekolah);
        $pendidikan_gabung = $nama_sekolah ? "{$tingkat} {$nama_sekolah}" : $tingkat;

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
            'pendidikan' => $pendidikan_gabung,
            'jabatan_id' => $request->jabatan_id,
            'departemen_id' => $request->departemen_id,
            'tanggal_masuk' => $request->tanggal_masuk,
            'status' => 'aktif'
        ]);

        // BUAT AKUN USER: Username dari nama, spasi dihilangkan, huruf kecil semua
        $username_baru = strtolower(str_replace(' ', '', $request->nama_lengkap));
        
        // Pengecekan jika ada nama yang persis sama agar tidak error
        $cek_username = User::where('username', $username_baru)->first();
        if ($cek_username) {
            $username_baru = $username_baru . rand(10, 99);
        }

        $password_mentah = 'passwor123'; // Password default sesuai permintaan

        User::create([
            'karyawan_id' => $karyawan->id,
            'username' => $username_baru,
            'password' => Hash::make($password_mentah),
            'role' => 'karyawan',
            'status' => 'aktif'
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Data Karyawan berhasil ditambahkan!');
    }

    // FUNGSI DETAIL KARYAWAN + RIWAYAT PRESENSI
    public function show($id)
    {
        $karyawan = Karyawan::with(['jabatan', 'departemen'])->findOrFail($id);
        $akun = User::where('karyawan_id', $id)->first(); // Ambil data akun terkait
        
        // Mengambil riwayat presensi karyawan ini (10 data terbaru)
        $riwayat_presensi = Presensi::where('karyawan_id', $id)
                                    ->orderBy('tanggal', 'desc')
                                    ->take(10)
                                    ->get();
        
        return view('karyawan.show', compact('karyawan', 'akun', 'riwayat_presensi'));
    }

    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $jabatans = Jabatan::all();
        $departemens = Departemen::all();
        
        return view('karyawan.edit', compact('karyawan', 'jabatans', 'departemens'));
    }

    public function cetakQr($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return view('karyawan.qr', compact('karyawan'));
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $karyawan->update([
            'nik_ktp' => $request->nik_ktp,
            'nik_kerja' => $request->nik_kerja,
            'nama_lengkap' => $request->nama_lengkap,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'status' => $request->status ?? $karyawan->status,
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Data Karyawan berhasil diperbarui!');
    }

    // Tambahkan method ini di dalam class Controller
    public function getJabatan($departemen_id)
    {
        // Mengambil data jabatan berdasarkan departemen_id, mengacu pada struktur database Tuan Muda
        $jabatans = Jabatan::where('departemen_id', $departemen_id)->get();
        
        return response()->json($jabatans);
    }
    
    public function qrIndex()
    {
        // Mengambil data karyawan yang aktif untuk ditampilkan di halaman QR
        // (Asumsi model relasinya bernama 'departemen' dan 'jabatan')
        $karyawans = \App\Models\Karyawan::with(['departemen', 'jabatan'])->get();
        
        // Arahkan ke file view manajemen_qr.blade.php
        return view('karyawan.manajemen_qr', compact('karyawans'));
    }

    public function cetakQrMassal(\Illuminate\Http\Request $request)
    {
        // Mengambil parameter ID yang dikirimkan melalui URL (contoh: ?ids=1,2,3)
        $ids = explode(',', $request->query('ids'));

        // Ambil data karyawan berdasarkan ID yang dipilih beserta relasinya
        $karyawans = \App\Models\Karyawan::with(['departemen', 'jabatan'])
                        ->whereIn('id', $ids)
                        ->get();

        // Tampilkan ke view khusus cetak massal
        return view('karyawan.qr_massal', compact('karyawans'));
    }
}