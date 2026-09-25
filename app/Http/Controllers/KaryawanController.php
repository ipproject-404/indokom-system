<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Departemen;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawans = Karyawan::with(['jabatan', 'departemen'])->orderBy('created_at', 'desc')->get();
        return view('karyawan_index', compact('karyawans'));
    }

    public function create()
    {
        $jabatans = Jabatan::all();
        $departemens = Departemen::all();
        
        return view('karyawan_create', compact('jabatans', 'departemens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik_ktp' => 'required|unique:karyawan,nik_ktp',
            'nik_kerja' => 'required|unique:karyawan,nik_kerja',
            'nama_lengkap' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'jabatan_id' => 'required',
            'departemen_id' => 'required',
            'tanggal_masuk' => 'required|date',
        ]);

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
            'pendidikan' => $request->pendidikan ?? '-',
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
            $username_baru = $username_baru . rand(10, 99); // Tambah angka jika nama pasaran
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

    // FUNGSI BARU UNTUK HALAMAN DETAIL KARYAWAN
    public function show($id)
    {
        $karyawan = Karyawan::with(['jabatan', 'departemen'])->findOrFail($id);
        $akun = User::where('karyawan_id', $id)->first(); // Ambil data akun terkait
        
        return view('karyawan_show', compact('karyawan', 'akun'));
    }

    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $jabatans = Jabatan::all();
        $departemens = Departemen::all();
        
        return view('karyawan_edit', compact('karyawan', 'jabatans', 'departemens'));
    }

    public function cetakQr($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return view('karyawan_qr', compact('karyawan'));
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
}