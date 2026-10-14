<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Pelatihan;
use App\Models\PesertaPelatihan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelatihanController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $query = Pelatihan::with(['peserta.karyawan', 'pembuat'])
            ->orderByDesc('tanggal_mulai');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pelatihans = $query->paginate(15)->withQueryString();

        return view('pelatihan.index', compact('pelatihans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelatihan'  => 'required|string|max:150',
            'penyelenggara'   => 'nullable|string|max:150',
            'kategori'        => 'required|in:sertifikasi,pelatihan_internal,pelatihan_eksternal',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'deskripsi'       => 'nullable|string',
        ]);

        Pelatihan::create([
            ...$request->only(['nama_pelatihan', 'penyelenggara', 'kategori', 'tanggal_mulai', 'tanggal_selesai', 'deskripsi']),
            'dibuat_oleh' => Auth::id(),
        ]);

        return back()->with('success', 'Pelatihan berhasil ditambahkan.');
    }

    public function update(Request $request, Pelatihan $pelatihan)
    {
        $request->validate([
            'nama_pelatihan'  => 'required|string|max:150',
            'penyelenggara'   => 'nullable|string|max:150',
            'kategori'        => 'required|in:sertifikasi,pelatihan_internal,pelatihan_eksternal',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'deskripsi'       => 'nullable|string',
            'status'          => 'required|in:aktif,selesai,dibatalkan',
        ]);

        $pelatihan->update($request->only([
            'nama_pelatihan', 'penyelenggara', 'kategori',
            'tanggal_mulai', 'tanggal_selesai', 'deskripsi', 'status',
        ]));

        return back()->with('success', 'Pelatihan berhasil diperbarui.');
    }

    public function destroy(Pelatihan $pelatihan)
    {
        $pelatihan->delete();
        return back()->with('success', 'Pelatihan dihapus.');
    }

    /** Tambah peserta ke pelatihan. */
    public function tambahPeserta(Request $request, Pelatihan $pelatihan)
    {
        $request->validate([
            'karyawan_ids'   => 'required|array|min:1',
            'karyawan_ids.*' => 'exists:karyawan,id',
            'catatan'        => 'nullable|string|max:255',
        ]);

        $ditambah  = 0;
        $sudahAda  = 0;

        foreach ($request->karyawan_ids as $id) {
            $exists = PesertaPelatihan::where('pelatihan_id', $pelatihan->id)
                ->where('karyawan_id', $id)
                ->exists();

            if ($exists) {
                $sudahAda++;
                continue;
            }

            PesertaPelatihan::create([
                'pelatihan_id' => $pelatihan->id,
                'karyawan_id'  => $id,
                'ditunjuk_oleh' => Auth::id(),
                'catatan'      => $request->catatan,
            ]);
            $ditambah++;
        }

        $pesan = "{$ditambah} peserta berhasil ditambahkan.";
        if ($sudahAda > 0) {
            $pesan .= " {$sudahAda} sudah terdaftar sebelumnya.";
        }

        return back()->with('success', $pesan);
    }

    /** Hapus satu peserta dari pelatihan. */
    public function hapusPeserta(Pelatihan $pelatihan, Karyawan $karyawan)
    {
        PesertaPelatihan::where('pelatihan_id', $pelatihan->id)
            ->where('karyawan_id', $karyawan->id)
            ->delete();

        return back()->with('success', "{$karyawan->nama_lengkap} dihapus dari daftar peserta.");
    }
}