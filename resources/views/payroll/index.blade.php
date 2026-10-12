@extends('layouts.halaman-karyawan')

@section('title', 'Dashboard Payroll')
@section('judul', 'Dashboard Payroll')
@section('subjudul', 'Kelola lembur, tarif, dan rekap pembayaran')

@section('konten')
@php
    $labelMinggu = \Carbon\Carbon::parse($awal)->translatedFormat('d M') . ' – ' . \Carbon\Carbon::parse($akhir)->translatedFormat('d M Y');
@endphp

<style>
    .kartu-stat { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:1rem 1.25rem; }
    .kartu-lembur { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:1rem; margin-bottom:.75rem; }
    .badge-status { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:700; }
    .nav-minggu { display:flex; align-items:center; gap:8px; }
    .nav-minggu a { width:32px; height:32px; border-radius:10px; background:#f1f5f9; color:#475569; display:flex; align-items:center; justify-content:center; text-decoration:none; }
    .nav-minggu .label { font-weight:700; color:#1e293b; font-size:.88rem; }
    .menu-payroll { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; margin-bottom:1.25rem; }
    .menu-payroll a { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:1rem; text-decoration:none; display:flex; flex-direction:column; align-items:center; gap:6px; color:#1e293b; font-size:.78rem; font-weight:700; }
    .menu-payroll a i { font-size:1.5rem; }
</style>

{{-- Menu Navigasi Payroll --}}
<div class="menu-payroll">
    <a href="{{ route('payroll.index') }}" style="border-color:#2563eb; color:#2563eb;">
        <i class="bi bi-house-fill"></i> Dashboard
    </a>
    <a href="{{ route('payroll.rekap', ['minggu' => $minggu->toDateString()]) }}">
        <i class="bi bi-table"></i> Rekap Minggu Ini
    </a>
    <a href="{{ route('payroll.tarif') }}">
        <i class="bi bi-gear-fill"></i> Kelola Tarif
    </a>
    <a href="{{ route('lembur.payroll') }}">
        <i class="bi bi-clock-history"></i> Pengajuan Lama
    </a>
</div>

{{-- Navigasi Minggu --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="nav-minggu">
        <a href="{{ route('payroll.index', ['minggu' => $mingguSebelumnya]) }}"><i class="bi bi-chevron-left"></i></a>
        <div class="label">{{ $labelMinggu }}</div>
        <a href="{{ route('payroll.index', ['minggu' => $mingguBerikutnya]) }}"><i class="bi bi-chevron-right"></i></a>
    </div>
</div>

{{-- Stat --}}
<div class="row g-2 mb-3">
    <div class="col-4">
        <div class="kartu-stat text-center">
            <div class="fw-bold fs-5" style="color:#f59e0b;">{{ $lemburs->total() }}</div>
            <div style="font-size:.7rem; color:#94a3b8;">Total</div>
        </div>
    </div>
    <div class="col-4">
        <div class="kartu-stat text-center">
            <div class="fw-bold fs-5" style="color:#16a34a;">{{ $rekap['disetujui'] ?? 0 }}</div>
            <div style="font-size:.7rem; color:#94a3b8;">Disetujui</div>
        </div>
    </div>
    <div class="col-4">
        <div class="kartu-stat text-center">
            <div class="fw-bold fs-5" style="color:#2563eb;">Rp {{ number_format($rekap['disetujui_upah'] ?? ($rekap->get('disetujui') ?? 0), 0, ',', '.') }}</div>
            <div style="font-size:.7rem; color:#94a3b8;">Total Upah</div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success py-2 px-3 mb-3" style="font-size:.82rem; border-radius:12px;">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.82rem; border-radius:12px;">{{ session('error') }}</div>
@endif

{{-- Daftar Lembur --}}
@forelse($lemburs as $l)
@php
    $st = match($l->status) {
        'disetujui' => ['warna' => '#16a34a', 'label' => 'Disetujui'],
        'ditolak'   => ['warna' => '#ef4444', 'label' => 'Ditolak'],
        default     => ['warna' => '#f59e0b', 'label' => 'Menunggu'],
    };
    $milikSendiri = $l->karyawan_id === Auth::user()->karyawan?->id;
@endphp
<div class="kartu-lembur">
    <div class="d-flex justify-content-between align-items-start mb-1">
        <div>
            <div class="fw-bold" style="font-size:.9rem;">{{ $l->karyawan->nama_lengkap ?? '-' }}</div>
            <div class="text-muted" style="font-size:.74rem;">
                {{ $l->karyawan->jabatan->nama_jabatan ?? '-' }} &middot; {{ $l->karyawan->departemen->nama_departemen ?? '-' }}
            </div>
        </div>
        <span class="badge-status" style="background:{{ $st['warna'] }}1a; color:{{ $st['warna'] }};">{{ $st['label'] }}</span>
    </div>
    <div style="font-size:.82rem; font-weight:600; color:#1e293b;">
        {{ \Carbon\Carbon::parse($l->tanggal)->translatedFormat('l, d M Y') }}
    </div>
    <div class="text-muted mb-2" style="font-size:.78rem;">{{ $l->catatan }}</div>

    {{-- Info durasi & upah kalau sudah disetujui --}}
    @if($l->status === 'disetujui')
        <div class="d-flex gap-2 flex-wrap mb-2">
            <span class="badge-status" style="background:#eff6ff; color:#2563eb;">
                {{ $l->durasi_final_jam }} jam &middot; {{ ucfirst($l->kelompok ?? '-') }}
            </span>
            <span class="badge-status" style="background:#f0fdf4; color:#16a34a;">
                Rp {{ number_format($l->total_upah ?? 0, 0, ',', '.') }}
            </span>
            @if(!$l->memenuhi_syarat)
                <span class="badge-status" style="background:#fef2f2; color:#ef4444;">Insentif &lt; 4 jam</span>
            @endif
        </div>
    @endif

    @if($l->status === 'menunggu' && !$milikSendiri)
        <form method="POST" action="{{ route('payroll.setujui', $l->id) }}" class="mt-2">
            @csrf
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <label class="form-label mb-1" style="font-size:.72rem; font-weight:700;">Jam Mulai</label>
                    <input type="time" name="jam_mulai" class="form-control form-control-sm"
                        value="{{ $l->jam_mulai ? substr($l->jam_mulai, 0, 5) : '' }}" required>
                </div>
                <div class="col-6">
                    <label class="form-label mb-1" style="font-size:.72rem; font-weight:700;">Jam Selesai</label>
                    <input type="time" name="jam_selesai" class="form-control form-control-sm"
                        value="{{ $l->jam_selesai ? substr($l->jam_selesai, 0, 5) : '' }}" required>
                </div>
            </div>
            <input type="text" name="catatan_payroll" maxlength="255" class="form-control form-control-sm mb-2"
                placeholder="Catatan (wajib kalau menolak)">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-biru flex-fill" style="border-radius:10px;"
                    onclick="return confirm('ACC lembur ini?')">ACC</button>
                <button type="submit" formaction="{{ route('payroll.tolak', $l->id) }}" formnovalidate
                    class="btn btn-sm btn-light flex-fill" style="border-radius:10px; color:#ef4444;"
                    onclick="return confirm('Tolak lembur ini?')">Tolak</button>
            </div>
        </form>
    @elseif($milikSendiri && $l->status === 'menunggu')
        <div class="text-muted" style="font-size:.76rem;">Ini lembur milikmu, diproses payroll lain.</div>
    @endif
</div>
@empty
    <div class="kartu-lembur text-center text-muted" style="font-size:.85rem;">Tidak ada pengajuan lembur minggu ini.</div>
@endforelse

@if($lemburs->hasPages())
    <div class="mt-2">{{ $lemburs->links() }}</div>
@endif
@endsection