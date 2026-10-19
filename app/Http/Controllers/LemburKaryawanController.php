<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LemburKaryawanController extends Controller
{
    /** 
     * Menampilkan riwayat / jadwal lembur milik karyawan yang login.
     * (Read-Only: Karyawan tidak bisa mengajukan dari sini)
     */
    public function index()
    {
        Carbon::setLocale('id');

        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            return back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        // Ambil semua data lembur milik karyawan ini
        $riwayat = Lembur::where('karyawan_id', $karyawan->id)
            ->orderByDesc('tanggal')
            ->limit(30)
            ->get();

        return view('karyawan.lembur', compact('karyawan', 'riwayat'));
    }
}