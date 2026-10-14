<?php

namespace App\Http\Controllers;

use App\Models\PesertaPelatihan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PelatihanKaryawanController extends Controller
{
    public function index()
    {
        Carbon::setLocale('id');

        $karyawan = Auth::user()->karyawan;

        $peserta = PesertaPelatihan::with('pelatihan')
            ->where('karyawan_id', $karyawan->id)
            ->orderByDesc('created_at')
            ->get();

        return view('karyawan.pelatihan', compact('karyawan', 'peserta'));
    }
}