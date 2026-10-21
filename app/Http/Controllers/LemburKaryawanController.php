<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LemburKaryawanController extends Controller
{
    public function index()
    {
        Carbon::setLocale('id');

        $karyawan = Karyawan::find(Auth::user()->karyawan_id);

        if (!$karyawan) {
            abort(403, 'Akun Anda tidak terhubung dengan data karyawan.');
        }

        $riwayat = Lembur::where('karyawan_id', $karyawan->id)
            ->orderByDesc('tanggal')
            ->limit(30)
            ->get();

        return view('karyawan.lembur', compact('karyawan', 'riwayat'));
    }
}