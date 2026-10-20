<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LemburKaryawanController extends Controller
{
    /** Riwayat + form pengajuan lembur milik karyawan yang login. */
    public function index()
    {
        Carbon::setLocale('id');

        $karyawan = $this->karyawanBolehLembur();

        $riwayat = Lembur::where('karyawan_id', $karyawan->id)
            ->orderByDesc('tanggal')
            ->limit(30)
            ->get();

        return view('karyawan.lembur', compact('karyawan', 'riwayat'));
    }
}