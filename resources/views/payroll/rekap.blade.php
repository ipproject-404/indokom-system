@extends('layouts.halaman-karyawan')

@section('title', 'Rekap Lembur')
@section('judul', 'Rekap Lembur')
@section('subjudul', 'Rekap pembayaran mingguan (dibayar setiap Sabtu)')

@section('konten')
@php
    $labelMinggu = \Carbon\Carbon::parse($awal)->translatedFormat('d M') . ' – ' . \Carbon\Carbon::parse($akhir)->translatedFormat('d M Y');
    $sabtu = \Carbon\Carbon::parse($akhir)->translatedFormat('d F Y');
@endphp

<style>
    .kartu-rekap { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:1rem; margin-bottom:.75rem; }
    .nav-minggu { display:flex; align-items:center; gap:8px; margin-bottom:1rem; }
    .nav-minggu a { width:32px; height:32px; border-radius:10px; background:#f1f5f9; color:#475569; display:flex; align-items:center; justify-content:center; text-decoration:none; }
</style>

<div class="nav-minggu">
    <a href="{{ route('payroll.rekap', ['minggu' => $mingguSebelumnya]) }}"><i class="bi bi-chevron-left"></i></a>
    <div class="fw-bold" style="font-size:.88rem; color:#1e293b;">{{ $labelMinggu }}</div>
    <a href="{{ route('payroll.rekap', ['minggu' => $mingguBerikutnya]) }}"><i class="bi bi-chevron-right"></i></a>
</div>

<div class="alert alert-info py-2 px-3 mb-3" style="font-size:.8rem; border-radius:12px;">
    <i class="bi bi-calendar-check me-1"></i> Jadwal bayar: <strong>Sabtu, {{ $sabtu }}</strong>
</div>

{{-- Total --}}
<div class="kartu-rekap mb-3" style="background:#eff6ff; border-color:#bfdbfe;">
    <div class="fw-bold" style="color:#1e293b;">Total Pembayaran Minggu Ini</div>
    <div class="fw-bold" style="font-size:1.4rem; color:#2563eb;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
    <div class="text-muted" style="font-size:.74rem;">{{ $rekap->count() }} karyawan</div>
</div>

@forelse($rekap as $item)
<div class="kartu-rekap">
    <div class="d-flex justify-content-between align-items-start mb-2">
        <div>
            <div class="fw-bold" style="font-size:.88rem;">{{ $item['karyawan']->nama_lengkap }}</div>
            <div class="text-muted" style="font-size:.74rem;">
                {{ $item['karyawan']->jabatan->nama_jabatan ?? '-' }} &middot; {{ $item['karyawan']->departemen->nama_departemen ?? '-' }}
            </div>
        </div>
        <a href="{{ route('payroll.slip', [$item['karyawan']->id, 'minggu' => $minggu->toDateString()]) }}"
            target="_blank"
            class="btn btn-sm" style="background:#eff6ff; color:#2563eb; border-radius:10px; font-size:.72rem; font-weight:700;">
            <i class="bi bi-printer me-1"></i> Slip
        </a>
    </div>
    <div class="d-flex justify-content-between">
        <span class="text-muted" style="font-size:.8rem;">Total jam: <strong>{{ $item['total_jam'] }} jam</strong></span>
        <span class="fw-bold" style="color:#16a34a; font-size:.88rem;">Rp {{ number_format($item['total_upah'], 0, ',', '.') }}</span>
    </div>
    {{-- Detail per hari --}}
    <div class="mt-2 pt-2 border-top">
        @foreach($item['detail'] as $l)
            <div class="d-flex justify-content-between" style="font-size:.75rem; color:#475569;">
                <span>{{ \Carbon\Carbon::parse($l->tanggal)->translatedFormat('d M') }} &middot; {{ $l->durasi_final_jam }}j</span>
                <span>Rp {{ number_format($l->total_upah, 0, ',', '.') }}</span>
            </div>
        @endforeach
    </div>
</div>
@empty
    <div class="kartu-rekap text-center text-muted" style="font-size:.85rem;">Tidak ada lembur disetujui minggu ini.</div>
@endforelse
@endsection