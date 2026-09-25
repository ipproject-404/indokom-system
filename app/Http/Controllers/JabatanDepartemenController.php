<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departemen;
use App\Models\Jabatan;

class JabatanDepartemenController extends Controller
{
    public function index()
    {
        $departemens = Departemen::orderBy('created_at', 'desc')->get();
        $jabatans = Jabatan::with('departemen')->orderBy('created_at', 'desc')->get();
        
        return view('jabatan_departemen_index', compact('departemens', 'jabatans'));
    }

    public function storeDepartemen(Request $request)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255|unique:departemen,nama_departemen',
        ]);

        Departemen::create([
            'nama_departemen' => $request->nama_departemen
        ]);

        return redirect()->route('jabatan.departemen.index')->with('success', 'Departemen baru berhasil ditambahkan!');
    }

    public function updateDepartemen(Request $request, $id)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255|unique:departemen,nama_departemen,' . $id,
        ]);

        $departemen = Departemen::findOrFail($id);
        $departemen->update([
            'nama_departemen' => $request->nama_departemen
        ]);

        return redirect()->route('jabatan.departemen.index')->with('success', 'Nama departemen berhasil diperbarui!');
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