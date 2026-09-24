<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Departemen;

class KaryawanController extends Controller
{
    // Menampilkan halaman daftar karyawan
    public function index()
    {
        // Mengambil semua data karyawan beserta relasi jabatan & departemen
        $karyawans = Karyawan::with(['jabatan', 'departemen'])->orderBy('created_at', 'desc')->get();
        return view('karyawan_index', compact('karyawans'));
    }

    // Menampilkan form tambah karyawan
    public function create()
    {
        // Mengambil data untuk pilihan dropdown
        $jabatans = Jabatan::all();
        $departemens = Departemen::all();
        
        return view('karyawan_create', compact('jabatans', 'departemens'));
    }

    // Menyimpan data karyawan baru ke database
    public function store(Request $request)
    {
        // Validasi data yang diinput
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

        // Simpan ke database
        Karyawan::create([
            'nik_ktp' => $request->nik_ktp,
            'nik_kerja' => $request->nik_kerja,
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

        return redirect()->route('karyawan.index')->with('success', 'Data Karyawan berhasil ditambahkan!');
    }
}