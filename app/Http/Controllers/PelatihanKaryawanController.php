<?php

namespace App\Http\Controllers;

use App\Models\PesertaPelatihan;
use App\Models\SertifikasiKaryawan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PelatihanKaryawanController extends Controller
{
    public function index()
    {
        Carbon::setLocale('id');

        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            return back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        // 1. Ambil data pelatihan yang diikuti
        $peserta = PesertaPelatihan::with(['pelatihan'])
            ->where('karyawan_id', $karyawan->id)
            ->orderByDesc('created_at')
            ->get();

        // 2. Ambil data sertifikasi milik karyawan ini
        $sertifikasiData = SertifikasiKaryawan::where('karyawan_id', $karyawan->id)
            ->get()
            ->keyBy('pelatihan_id'); // Kelompokkan berdasarkan pelatihan_id

        // 3. Gabungkan data sertifikasi ke dalam objek peserta
        foreach ($peserta as $p) {
            $p->sertifikat = $sertifikasiData->get($p->pelatihan_id);
        }

        return view('karyawan.pelatihan', compact('karyawan', 'peserta'));
    }
}