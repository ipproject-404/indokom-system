@extends('layouts.halaman-karyawan')

@section('title', 'Kelola Tarif Lembur')
@section('judul', 'Kelola Tarif Lembur')
@section('subjudul', 'Atur upah per jam per departemen dan kelompok')

@section('konten')
<style>
    .kartu-tarif { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:1rem; margin-bottom:.75rem; }
    .badge-kelompok { display:inline-flex; padding:2px 10px; border-radius:20px; font-size:.7rem; font-weight:700; }
</style>

@if(session('success'))
    <div class="alert alert-success py-2 px-3 mb-3" style="font-size:.82rem; border-radius:12px;">{{ session('success') }}</div>
@endif

{{-- Form Tambah Tarif --}}
<div class="kartu-tarif mb-3">
    <div class="fw-bold mb-3" style="color:#1e293b;">Tambah / Update Tarif</div>
    <form method="POST" action="{{ route('payroll.tarif.store') }}">
        @csrf
        <div class="mb-2">
            <label class="form-label small fw-bold">Departemen</label>
            <select name="departemen_id" class="form-select form-select-sm">
                <option value="">Default (semua departemen)</option>
                @foreach(\App\Models\Departemen::orderBy('nama_departemen')->get() as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                @endforeach
            </select>
            <div class="form-text">Kosongkan = tarif default kalau departemen tidak punya tarif sendiri.</div>
        </div>
        <div class="row g-2 mb-2">
            <div class="col-4">
                <label class="form-label small fw-bold">Kelompok</label>
                <select name="kelompok" class="form-select form-select-sm" required>
                    <option value="reguler">Reguler (bawah asisten)</option>
                    <option value="insentif">Insentif (asisten ke atas)</option>
                </select>
            </div>
            <div class="col-4">
                <label class="form-label small fw-bold">Jam ke-</label>
                <input type="number" name="urutan_jam" class="form-control form-control-sm" min="1" max="24" placeholder="1, 2, 3..." required>
            </div>
            <div class="col-4">
                <label class="form-label small fw-bold">Upah/jam (Rp)</label>
                <input type="number" name="upah_per_jam" class="form-control form-control-sm" min="0" placeholder="15000" required>
            </div>
        </div>
        <button type="submit" class="btn btn-biru btn-sm w-100" style="border-radius:10px;">Simpan Tarif</button>
    </form>
</div>

{{-- Daftar Tarif --}}
@forelse($tarifs as $key => $group)
@php
    $first     = $group->first();
    $namaGrup  = $first->departemen ? $first->departemen->nama_departemen : 'Default (semua departemen)';
    $kelompok  = $first->kelompok;
    $warna     = $kelompok === 'insentif' ? ['bg' => '#eff6ff', 'c' => '#2563eb'] : ['bg' => '#f0fdf4', 'c' => '#16a34a'];
@endphp
<div class="kartu-tarif">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="fw-bold" style="font-size:.85rem; color:#1e293b;">{{ $namaGrup }}</div>
        <span class="badge-kelompok" style="background:{{ $warna['bg'] }}; color:{{ $warna['c'] }};">{{ ucfirst($kelompok) }}</span>
    </div>
    @foreach($group as $t)
    <div class="d-flex justify-content-between align-items-center py-1 border-top">
        <span style="font-size:.82rem; color:#475569;">Jam ke-{{ $t->urutan_jam }}</span>
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold" style="font-size:.82rem;">Rp {{ number_format($t->upah_per_jam, 0, ',', '.') }}</span>
            <form method="POST" action="{{ route('payroll.tarif.destroy', $t->id) }}" onsubmit="return confirm('Hapus tarif ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm p-0" style="color:#ef4444; font-size:.8rem;">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@empty
    <div class="kartu-tarif text-center text-muted" style="font-size:.85rem;">Belum ada tarif. Tambahkan tarif di atas.</div>
@endforelse
@endsection