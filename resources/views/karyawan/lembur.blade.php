@extends('layouts.halaman-karyawan')

@section('title', 'Lembur Saya')
@section('judul', 'Lembur Saya')
@section('subjudul', 'Riwayat dan status lembur kamu')

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

<div class="kartu mb-3" style="background:#eff6ff; border-color:#bfdbfe;">
    <div class="d-flex align-items-center gap-2" style="font-size:.82rem; color:#1d4ed8;">
        <i class="bi bi-info-circle-fill fs-5"></i>
        <span>Pengajuan lembur dilakukan oleh kepala bagianmu. Halaman ini hanya untuk memantau status.</span>
    </div>
</div>

@forelse($riwayat as $l)
@php $st = $statusLembur[$l->status] ?? $statusLembur['menunggu']; @endphp
<div class="kartu">
    <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
        <div class="fw-bold" style="font-size:.88rem;">
            {{ \Carbon\Carbon::parse($l->tanggal)->translatedFormat('l, d M Y') }}
        </div>
        <span class="badge-st" style="background:{{ $st['warna'] }}1a; color:{{ $st['warna'] }};">
            {{ $st['label'] }}
        </span>
    </div>
    <div class="text-muted" style="font-size:.78rem;">{{ $l->catatan }}</div>

    @if($l->jam_mulai)
        <div class="mt-1" style="font-size:.78rem; color:#475569;">
            <i class="bi bi-clock me-1"></i>
            {{ substr($l->jam_mulai, 0, 5) }}
            @if($l->jam_selesai) – {{ substr($l->jam_selesai, 0, 5) }} @endif
            @if($l->durasi_final_jam) &middot; {{ $l->durasi_final_jam }} jam @endif
        </div>
    @endif

    @if($l->status === 'disetujui' && $l->total_upah)
        <div class="mt-1 fw-bold" style="font-size:.78rem; color:#16a34a;">
            Rp {{ number_format($l->total_upah, 0, ',', '.') }}
        </div>
    @endif

    @if($l->catatan_payroll)
        <div class="mt-1 text-muted" style="font-size:.76rem;">
            <i class="bi bi-chat-left-text me-1"></i>{{ $l->catatan_payroll }}
        </div>
    @endif
</div>
@empty
    <div class="kartu text-center text-muted" style="font-size:.85rem;">
        <i class="bi bi-clock-history d-block mb-2" style="font-size:2rem; color:#cbd5e1;"></i>
        Belum ada riwayat lembur.
    </div>
@endforelse
@endsection