@extends('layouts.halaman-karyawan')

@section('title', 'Lembur Tim')
@section('judul', 'Lembur Tim')
@section('subjudul', 'Ajukan lembur untuk anak buah')

@section('konten')
@php
    $statusLembur = [
        'menunggu'  => ['warna' => '#f59e0b', 'label' => 'Menunggu'],
        'disetujui' => ['warna' => '#16a34a', 'label' => 'Disetujui'],
        'ditolak'   => ['warna' => '#ef4444', 'label' => 'Ditolak'],
    ];
@endphp

<style>
    .kartu { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:1rem; margin-bottom:.75rem; }
    .badge-st { display:inline-flex; padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:700; }
</style>

@if(session('success'))
    <div class="alert alert-success py-2 px-3 mb-3" style="font-size:.82rem; border-radius:12px;">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.82rem; border-radius:12px;">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.82rem; border-radius:12px;">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('lembur.kepala.store') }}" class="kartu mb-4">
    @csrf
    <div class="fw-bold mb-3" style="color:#1e293b;">Ajukan Lembur Anak Buah</div>

    <label class="form-label small fw-bold">Karyawan</label>
    <select name="karyawan_id" class="form-select mb-2" required>
        <option value="">Pilih karyawan...</option>
        @foreach($anakBuahReguler as $k)
            <option value="{{ $k->id }}" {{ old('karyawan_id') == $k->id ? 'selected' : '' }}>
                {{ $k->nama_lengkap }} — {{ $k->jabatan->nama_jabatan ?? '-' }}
            </option>
        @endforeach
    </select>
    @if($anakBuahReguler->isEmpty())
        <div class="text-muted mb-2" style="font-size:.76rem;">
            <i class="bi bi-info-circle me-1"></i>Tidak ada anak buah reguler di departemenmu.
        </div>
    @endif

    <label class="form-label small fw-bold">Tanggal</label>
    <input type="date" name="tanggal" class="form-control mb-2"
        value="{{ old('tanggal', now()->toDateString()) }}" required>

    <label class="form-label small fw-bold">Alasan Lembur</label>
    <textarea name="catatan" rows="2" maxlength="255" class="form-control mb-3"
        placeholder="Misal: perbaikan mesin line 2" required>{{ old('catatan') }}</textarea>

    <p class="text-muted mb-3" style="font-size:.74rem;">
        <i class="bi bi-info-circle me-1"></i>
        Jam mulai otomatis dari jam pulang shift karyawan.
        Jam selesai diisi payroll saat acc berdasarkan surat lembur.
    </p>

    <button type="submit" class="btn btn-biru w-100" style="border-radius:12px;">
        <i class="bi bi-send me-1"></i>Ajukan Lembur
    </button>
</form>

<div class="fw-bold mb-2" style="color:#1e293b;">Riwayat Pengajuan</div>

@forelse($riwayat as $l)
@php $st = $statusLembur[$l->status] ?? $statusLembur['menunggu']; @endphp
<div class="kartu">
    <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
        <div>
            <div class="fw-bold" style="font-size:.88rem;">{{ $l->karyawan->nama_lengkap ?? '-' }}</div>
            <div class="text-muted" style="font-size:.74rem;">
                {{ $l->karyawan->jabatan->nama_jabatan ?? '-' }} &middot;
                {{ $l->karyawan->departemen->nama_departemen ?? '-' }}
            </div>
        </div>
        <span class="badge-st" style="background:{{ $st['warna'] }}1a; color:{{ $st['warna'] }};">
            {{ $st['label'] }}
        </span>
    </div>
    <div style="font-size:.82rem; font-weight:600; color:#1e293b;">
        {{ \Carbon\Carbon::parse($l->tanggal)->translatedFormat('l, d M Y') }}
    </div>
    <div class="text-muted mt-1" style="font-size:.78rem;">{{ $l->catatan }}</div>

    @if($l->status === 'disetujui' && $l->total_upah)
        <div class="mt-1 fw-bold" style="font-size:.78rem; color:#16a34a;">
            {{ $l->durasi_final_jam }} jam &middot; Rp {{ number_format($l->total_upah, 0, ',', '.') }}
        </div>
    @endif
    @if($l->catatan_payroll)
        <div class="mt-1 text-muted" style="font-size:.76rem;">
            <i class="bi bi-chat-left-text me-1"></i>{{ $l->catatan_payroll }}
        </div>
    @endif

    @if($l->status === 'menunggu')
        <form method="POST" action="{{ route('lembur.kepala.batal', $l->id) }}" class="mt-2"
            onsubmit="return confirm('Batalkan pengajuan lembur ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-light" style="border-radius:10px; color:#ef4444;">
                Batalkan
            </button>
        </form>
    @endif
</div>
@empty
    <div class="kartu text-center text-muted" style="font-size:.85rem;">Belum ada pengajuan lembur.</div>
@endforelse
@endsection