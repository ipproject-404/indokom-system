@extends('layouts.halaman-karyawan')

@section('title', 'Rekap Lembur per Departemen')
@section('judul', 'Rekap per Departemen')
@section('subjudul', 'Total lembur disetujui per departemen')

@section('konten')
@php
    $labelBulan = $bulan->translatedFormat('F Y');
@endphp

<style>
    .kartu { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:1rem; margin-bottom:.75rem; }
    .nav-bulan { display:flex; align-items:center; gap:8px; margin-bottom:1rem; }
    .nav-bulan a { width:32px; height:32px; border-radius:10px; background:#f1f5f9; color:#475569; display:flex; align-items:center; justify-content:center; text-decoration:none; }
    .menu-payroll-grid { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; margin-bottom:1.25rem; }
    .menu-payroll-grid a { background:#fff; border:1.5px solid #e2e8f0; border-radius:14px; padding:1rem .75rem; text-decoration:none; display:flex; flex-direction:column; align-items:center; gap:6px; color:#475569; font-size:.75rem; font-weight:700; text-align:center; }
    .menu-payroll-grid a i { font-size:1.4rem; }
    .menu-payroll-grid a.aktif { border-color:#2563eb; color:#2563eb; background:#eff6ff; }
</style>

<div class="menu-payroll-grid">
    <a href="{{ route('payroll.index') }}">
        <i class="bi bi-house-fill"></i> Dashboard
    </a>
    <a href="{{ route('payroll.rekap') }}">
        <i class="bi bi-table"></i> Rekap Minggu
    </a>
    <a href="{{ route('payroll.rekap.departemen') }}" class="aktif">
        <i class="bi bi-building"></i> Rekap Dept
    </a>
    <a href="{{ route('payroll.tarif') }}">
        <i class="bi bi-gear-fill"></i> Kelola Tarif
    </a>
</div>

<div class="nav-bulan">
    <a href="{{ route('payroll.rekap.departemen', ['bulan' => $bulanSebelumnya]) }}">
        <i class="bi bi-chevron-left"></i>
    </a>
    <div class="fw-bold" style="font-size:.88rem; color:#1e293b;">{{ $labelBulan }}</div>
    <a href="{{ route('payroll.rekap.departemen', ['bulan' => $bulanBerikutnya]) }}">
        <i class="bi bi-chevron-right"></i>
    </a>
</div>

{{-- Total keseluruhan --}}
<div class="kartu mb-3" style="background:#eff6ff; border-color:#bfdbfe;">
    <div class="fw-bold" style="color:#1e293b;">Total Semua Departemen</div>
    <div class="fw-bold" style="font-size:1.4rem; color:#2563eb;">
        Rp {{ number_format($grandTotal, 0, ',', '.') }}
    </div>
    <div class="text-muted" style="font-size:.74rem;">
        {{ $totalJamSemua }} jam &middot; {{ $rekap->count() }} departemen
    </div>
</div>

@forelse($rekap as $dept)
<div class="kartu" x-data="{ buka: false }">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <div class="fw-bold" style="font-size:.9rem; color:#1e293b;">{{ $dept['nama_departemen'] }}</div>
            <div class="text-muted" style="font-size:.74rem;">
                {{ $dept['jumlah_karyawan'] }} karyawan &middot; {{ $dept['total_jam'] }} jam
            </div>
        </div>
        <div class="text-end">
            <div class="fw-bold" style="color:#16a34a; font-size:.88rem;">
                Rp {{ number_format($dept['total_upah'], 0, ',', '.') }}
            </div>
            <button @click="buka = !buka"
                class="btn btn-sm mt-1" style="background:#f1f5f9; color:#475569; border-radius:8px; font-size:.72rem; font-weight:700;">
                <span x-text="buka ? 'Tutup' : 'Detail'"></span>
            </button>
        </div>
    </div>

    <div x-show="buka" x-collapse class="mt-3 pt-3 border-top">
        @foreach($dept['detail'] as $item)
        <div class="d-flex justify-content-between align-items-start py-2 border-bottom">
            <div>
                <div style="font-size:.82rem; font-weight:600; color:#1e293b;">
                    {{ $item['karyawan']->nama_lengkap ?? '-' }}
                </div>
                <div class="text-muted" style="font-size:.74rem;">
                    {{ $item['karyawan']->jabatan->nama_jabatan ?? '-' }}
                </div>
                {{-- Detail per hari --}}
                @foreach($item['lemburs'] as $l)
                <div style="font-size:.72rem; color:#94a3b8;" class="mt-0.5">
                    {{ \Carbon\Carbon::parse($l->tanggal)->translatedFormat('d M') }}
                    &middot; {{ $l->durasi_final_jam }}j
                    &middot; Rp {{ number_format($l->total_upah, 0, ',', '.') }}
                </div>
                @endforeach
            </div>
            <div class="text-end">
                <div style="font-size:.82rem; font-weight:700; color:#16a34a;">
                    Rp {{ number_format($item['total_upah'], 0, ',', '.') }}
                </div>
                <div class="text-muted" style="font-size:.72rem;">{{ $item['total_jam'] }} jam</div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@empty
    <div class="kartu text-center text-muted" style="font-size:.85rem;">
        Tidak ada lembur disetujui bulan ini.
    </div>
@endforelse

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
@endsection