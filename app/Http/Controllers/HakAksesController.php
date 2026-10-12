<?php

namespace App\Http\Controllers;

use App\Models\HakAkses;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HakAksesController extends Controller
{
    public function toggle(Request $request, $karyawanId)
    {
        $request->validate([
            'hak' => 'required|string|in:payroll',
        ]);

        $karyawan = Karyawan::findOrFail($karyawanId);

        $hak = HakAkses::where('karyawan_id', $karyawanId)
            ->where('hak', $request->hak)
            ->first();

        if ($hak) {
            $hak->delete();
            $pesan = "Hak '{$request->hak}' dicabut dari {$karyawan->nama_lengkap}.";
        } else {
            HakAkses::create([
                'karyawan_id' => $karyawanId,
                'hak'         => $request->hak,
                'dibuat_oleh' => Auth::id(),
            ]);
            $pesan = "Hak '{$request->hak}' diberikan ke {$karyawan->nama_lengkap}.";
        }

        return back()->with('success', $pesan);
    }
}