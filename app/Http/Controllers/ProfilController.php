<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ProfilController extends Controller
{
    /**
     * Halaman profil pengguna yang sedang login.
     * Data diambil dari akun yang login sendiri (Auth::user()) --
     * karyawan tidak bisa lihat/akses profil orang lain lewat sini,
     * beda dengan halaman HRD yang memang perlu {id} di URL.[cite: 15]
     */
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan; // null kalau akun ini tidak terhubung ke data karyawan[cite: 15]

        $jumlahHadirBulanIni = null;
        if ($karyawan) {
            $jumlahHadirBulanIni = Presensi::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', Carbon::now()->month)
                ->whereYear('tanggal', Carbon::now()->year)
                ->whereNotNull('jam_masuk')
                ->count();
        }

        // Jika role HRD atau Admin, arahkan ke profil khusus HRD[cite: 15]
        if ($user->role === 'hrd' || $user->role === 'admin') {
            return view('karyawan.profil_hrd', [
                'user' => $user,
                'karyawan' => $karyawan,
                'jumlahHadirBulanIni' => $jumlahHadirBulanIni,
            ]);
        }

        // Default untuk karyawan biasa[cite: 15]
        return view('karyawan.profil', [
            'user' => $user,
            'karyawan' => $karyawan,
            'jumlahHadirBulanIni' => $jumlahHadirBulanIni,
        ]);
    }

    /**
     * Memperbarui kata sandi pengguna
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Sandi lama wajib diisi.',
            'password.required' => 'Sandi baru wajib diisi.',
            'password.min' => 'Sandi baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi sandi baru tidak cocok.',
        ]);

        $user = Auth::user();

        // Cek apakah sandi lama sesuai
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Sandi lama yang Anda masukkan salah.']);
        }

        // Update sandi baru
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}