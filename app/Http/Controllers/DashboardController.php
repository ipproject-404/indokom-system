<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan; // null kalau akun ini tidak terhubung ke data karyawan (misal admin_master)

        $presensiHariIni = null;
        if ($karyawan) {
            $presensiHariIni = Presensi::where('karyawan_id', $karyawan->id)
                ->where('tanggal', Carbon::now()->toDateString())
                ->first();
        }

        return view('dashboard.karyawan', [
            'user' => $user,
            'karyawan' => $karyawan,
            'presensiHariIni' => $presensiHariIni,
            'pesanAbsensi' => $request->session()->get('pesan_absensi'),
        ]);
    }
}
