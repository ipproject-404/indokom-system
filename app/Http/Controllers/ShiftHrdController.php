<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftHrdController extends Controller
{
    /**
     * HRD hanya BISA LIHAT shift siapa saja ada di mana -- tidak ada
     * tombol ubah di sini. Yang mengatur/menukar shift adalah kepala
     * bagian masing-masing lewat halaman Shift Tim (ShiftTimController).
     */
    public function index(Request $request)
    {
        $query = Karyawan::with(['jabatan', 'departemen.kepalaKaryawan', 'shift'])
            ->where('status', 'aktif');

        if ($request->filled('departemen')) {
            $query->where('departemen_id', $request->departemen);
        }

        $karyawans = $query->orderBy('nama_lengkap')->get();

        $hariIni = Carbon::now();
        $karyawans->each(function ($k) use ($hariIni) {
            $k->shift_sekarang = $k->shiftPadaTanggal($hariIni);
        });

        $departemens = Departemen::orderBy('nama_departemen')->get();

                return view('karyawan.shift-karyawan', compact('karyawans', 'departemens'));
    }
}