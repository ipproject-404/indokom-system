@extends('layouts.halaman-karyawan')

@section('title', 'Pelatihan Saya')
@section('judul', 'Pelatihan & Sertifikasi')
@section('subjudul', 'Jadwal dan riwayat pelatihan kamu')

@section('konten')
@php
    $kategoriBadge = [
        'sertifikasi'         => ['bg' => '#f5f3ff', 'c' => '#7c3aed', 'label' => 'Sertifikasi'],
        'pelatihan_internal'  => ['bg' => '#f0f9ff', 'c' => '#0284c7', 'label' => 'Internal'],
        'pelatihan_eksternal' => ['bg' => '#fffbeb', 'c' => '#d97706', 'label' => 'Eksternal'],
    ];
@endphp

<style>
    .kartu-pelatihan { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:1rem; margin-bottom:.75rem; }
    .badge-kat { display:inline-flex; padding:2px 10px; border-radius:20px; font-size:.7rem; font-weight:700; }
    .badge-status-p { display:inline-flex; padding:2px 10px; border-radius:20px; font-size:.7rem; font-weight:700; }
</style>

@forelse($peserta as $ps)
@php
    $p = $ps->pelatihan;
    $kat = $kategoriBadge[$p->kategori] ?? $kategoriBadge['pelatihan_internal'];
    $berjalan = $p->sedangBerjalan();
    $akan = $p->akanDatang();
    $stLabel = $p->status === 'selesai' ? 'Selesai'
        : ($p->status === 'dibatalkan' ? 'Dibatalkan'
        : ($berjalan ? 'Sedang Berjalan' : ($akan ? 'Akan Datang' : 'Aktif')));
    $stBg = $p->status === 'selesai' ? '#f1f5f9'
        : ($p->status === 'dibatalkan' ? '#fef2f2'
        : ($berjalan ? '#f0fdf4' : '#eff6ff'));
    $stC = $p->status === 'selesai' ? '#64748b'
        : ($p->status === 'dibatalkan' ? '#ef4444'
        : ($berjalan ? '#16a34a' : '#2563eb'));
@endphp
<div class="kartu-pelatihan">
    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
        <div>
            <span class="badge-kat" style="background:{{ $kat['bg'] }}; color:{{ $kat['c'] }};">{{ $kat['label'] }}</span>
        </div>
        <span class="badge-status-p" style="background:{{ $stBg }}; color:{{ $stC }};">{{ $stLabel }}</span>
    </div>
    <div class="fw-bold" style="font-size:.92rem; color:#1e293b;">{{ $p->nama_pelatihan }}</div>
    @if($p->penyelenggara)
        <div class="text-muted mt-1" style="font-size:.78rem;">
            <i class="bi bi-building me-1"></i>{{ $p->penyelenggara }}
        </div>
    @endif
    <div class="text-muted mt-1" style="font-size:.78rem;">
        <i class="bi bi-calendar3 me-1"></i>
        {{ $p->tanggal_mulai->translatedFormat('d M Y') }}
        @if($p->tanggal_selesai && !$p->tanggal_mulai->isSameDay($p->tanggal_selesai))
            – {{ $p->tanggal_selesai->translatedFormat('d M Y') }}
        @endif
    </div>
    @if($p->deskripsi)
        <div class="mt-2 text-muted" style="font-size:.76rem;">{{ $p->deskripsi }}</div>
    @endif
    @if($ps->catatan)
        <div class="mt-1" style="font-size:.74rem; color:#94a3b8;">
            <i class="bi bi-chat-left-text me-1"></i>{{ $ps->catatan }}
        </div>
    @endif
</div>
@empty
    <div class="kartu-pelatihan text-center text-muted" style="font-size:.85rem;">
        <i class="bi bi-mortarboard d-block mb-2" style="font-size:2rem; color:#cbd5e1;"></i>
        Belum ada pelatihan yang dijadwalkan untukmu.
    </div>
@endforelse
@endsection