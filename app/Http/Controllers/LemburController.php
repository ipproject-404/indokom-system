<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lembur;

class LemburController extends Controller
{
    /**
     * Menampilkan daftar pengajuan lembur
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending'); // Default menampilkan yang pending/menunggu

        $lemburs = Lembur::with(['karyawan.departemen', 'karyawan.jabatan'])
            ->when($status, function ($query, $status) {
                if ($status !== 'semua') {
                    return $query->where('status', $status);
                }
            })
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        // PERUBAHAN DI SINI: Mengarahkan ke absensi.pengajuan_lembur
        return view('absensi.pengajuan_lembur', compact('lemburs', 'status'));
    }

    /**
     * Menyetujui (ACC) pengajuan lembur
     */
    public function approve($id)
    {
        $lembur = Lembur::findOrFail($id);
        $lembur->status = 'disetujui'; 
        $lembur->save();

        return redirect()->back()->with('success', 'Pengajuan lembur berhasil disetujui.');
    }

    /**
     * Menolak pengajuan lembur
     */
    public function reject($id)
    {
        $lembur = Lembur::findOrFail($id);
        $lembur->status = 'ditolak'; 
        $lembur->save();

        return redirect()->back()->with('success', 'Pengajuan lembur telah ditolak.');
    }
}