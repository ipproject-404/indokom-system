<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Divisi;
use App\Models\Karyawan;

class JabatanDepartemenController extends Controller
{
    public function index()
    {
        $departemens = Departemen::with('divisi', 'kepalaKaryawan')->orderBy('created_at', 'desc')->get();
        $jabatans = Jabatan::with('departemen')->orderBy('created_at', 'desc')->get();
        $divisis = Divisi::orderBy('nama_divisi')->get();

        // Daftar karyawan per departemen, dipakai buat opsi dropdown "Kepala
        // Bagian" di modal edit -- sengaja dikelompokkan di sini (bukan di
        // Blade) supaya tiap departemen hanya menampilkan karyawan miliknya
        // sendiri sebagai calon kepala, dikirim ke JS sebagai JSON.
        $karyawanPerDepartemen = Karyawan::select('id', 'nama_lengkap', 'departemen_id')
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get()
            ->groupBy('departemen_id');

        return view('jabatan-departemen.index', compact('departemens', 'jabatans', 'divisis', 'karyawanPerDepartemen'));
    }

    public function storeDepartemen(Request $request)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255|unique:departemen,nama_departemen',
            'divisi_id' => 'required|exists:divisi,id',
        ]);

        Departemen::create([
            'nama_departemen' => $request->nama_departemen,
            'divisi_id' => $request->divisi_id,
        ]);

        return redirect()->route('jabatan.departemen.index')->with('success', 'Departemen baru berhasil ditambahkan!');
    }

    public function updateDepartemen(Request $request, $id)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255|unique:departemen,nama_departemen,' . $id,
            'divisi_id' => 'required|exists:divisi,id',
            // Kepala bagian boleh kosong (belum ditunjuk), tapi kalau diisi
            // harus karyawan yang memang berada di departemen ini -- supaya
            // tidak ada yang kepencet pilih orang dari departemen lain.
            'kepala_karyawan_id' => 'nullable|exists:karyawan,id,departemen_id,' . $id,
        ]);

        $departemen = Departemen::findOrFail($id);
        $departemen->update([
            'nama_departemen' => $request->nama_departemen,
            'divisi_id' => $request->divisi_id,
            'kepala_karyawan_id' => $request->kepala_karyawan_id ?: null,
        ]);

        return redirect()->route('jabatan.departemen.index')->with('success', 'Data departemen berhasil diperbarui!');
    }

    public function destroyDepartemen($id)
    {
        try {
            $departemen = Departemen::findOrFail($id);
            $departemen->delete();

            return redirect()->route('jabatan.departemen.index')->with('success', 'Departemen berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('jabatan.departemen.index')->with('error', 'Departemen tidak dapat dihapus karena masih terikat dengan data Jabatan atau Karyawan!');
        }
    }

    public function storeJabatan(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatan,nama_jabatan',
            'departemen_id' => 'required|exists:departemen,id',
        ]);

        Jabatan::create([
            'nama_jabatan' => $request->nama_jabatan,
            'departemen_id' => $request->departemen_id,
        ]);

        return redirect()->route('jabatan.departemen.index')->with('success', 'Jabatan baru berhasil ditambahkan!');
    }

    public function updateJabatan(Request $request, $id)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatan,nama_jabatan,' . $id,
            'departemen_id' => 'required|exists:departemen,id',
        ]);

        $jabatan = Jabatan::findOrFail($id);
        $jabatan->update([
            'nama_jabatan' => $request->nama_jabatan,
            'departemen_id' => $request->departemen_id,
        ]);

        return redirect()->route('jabatan.departemen.index')->with('success', 'Data jabatan berhasil diperbarui!');
    }

    public function destroyJabatan($id)
    {
        try {
            $jabatan = Jabatan::findOrFail($id);
            $jabatan->delete();

            return redirect()->route('jabatan.departemen.index')->with('success', 'Jabatan berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('jabatan.departemen.index')->with('error', 'Jabatan tidak dapat dihapus karena masih terikat dengan data Karyawan!');
        }
    }
}