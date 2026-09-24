@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="app-header">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-white d-flex align-items-center justify-content-center"
                 style="width:40px;height:40px;overflow:hidden;">
                <i class="bi bi-person-fill text-primary fs-4"></i>
            </div>
            <div>
                <div class="fw-bold">{{ $karyawan->nama_lengkap ?? $user->username }}</div>
                <div class="small opacity-75">{{ $karyawan->jabatan->nama_jabatan ?? ucfirst($user->role) }}</div>
            </div>
        </div>
        <div class="d-flex gap-3">
            <i class="bi bi-search fs-5"></i>
            <i class="bi bi-bell fs-5"></i>
        </div>
    </div>
</div>

<div class="p-3 flex-grow-1">

    @if ($pesanAbsensi)
        <div class="alert alert-success small py-2">{{ $pesanAbsensi }}</div>
    @endif

    <div class="text-muted small mb-3">
        {{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }} &bull; {{ \Carbon\Carbon::now()->format('H:i:s') }} WIB
    </div>

    <div class="card card-absen p-3 mb-4">
        <div class="row text-center mb-3">
            <div class="col-6 border-end">
                <div class="text-muted small mb-1">Absen Masuk</div>
                <div class="fw-bold fs-5">
                    {{ $presensiHariIni?->jam_masuk ? \Carbon\Carbon::parse($presensiHariIni->jam_masuk)->format('H:i:s') : '--:--:--' }}
                </div>
            </div>
            <div class="col-6">
                <div class="text-muted small mb-1">Absen Keluar</div>
                <div class="fw-bold fs-5">
                    {{ $presensiHariIni?->jam_pulang ? \Carbon\Carbon::parse($presensiHariIni->jam_pulang)->format('H:i:s') : '--:--:--' }}
                </div>
            </div>
        </div>
        <a href="{{ route('absensi.scan') }}" class="btn btn-biru py-2 fw-semibold">
            <i class="bi bi-qr-code-scan me-1"></i>
            @if (!$presensiHariIni)
                Clock In
            @elseif (!$presensiHariIni->jam_pulang)
                Clock Out
            @else
                Sudah Absen Hari Ini
            @endif
        </a>
    </div>

    <div class="row row-cols-5 g-3 text-center mb-4">
        @php
            $menu = [
                ['icon' => 'bi-calendar-check', 'label' => 'Absensi', 'warna' => '#f59e0b', 'link' => route('absensi.scan')],
                ['icon' => 'bi-people', 'label' => 'Manajemen Kehadiran', 'warna' => '#16a34a', 'link' => '#'],
                ['icon' => 'bi-award', 'label' => 'Talent', 'warna' => '#ec4899', 'link' => '#'],
                ['icon' => 'bi-file-earmark-text', 'label' => 'Report', 'warna' => '#2563eb', 'link' => '#'],
                ['icon' => 'bi-grid-3x3-gap', 'label' => 'Spaces', 'warna' => '#f97316', 'link' => '#'],
                ['icon' => 'bi-clock-history', 'label' => 'Lembur', 'warna' => '#16a34a', 'link' => '#'],
                ['icon' => 'bi-cash-coin', 'label' => 'Reimburse', 'warna' => '#ec4899', 'link' => '#'],
                ['icon' => 'bi-car-front', 'label' => 'Fasilitas', 'warna' => '#2563eb', 'link' => '#'],
                ['icon' => 'bi-wallet2', 'label' => 'Pinjaman', 'warna' => '#f59e0b', 'link' => '#'],
                ['icon' => 'bi-piggy-bank', 'label' => 'Kasbon', 'warna' => '#16a34a', 'link' => '#'],
            ];
        @endphp

        @foreach ($menu as $item)
            <div class="col">
                <a href="{{ $item['link'] }}" class="menu-grid-item">
                    <div class="icon-circle" style="background: {{ $item['warna'] }};">
                        <i class="bi {{ $item['icon'] }}"></i>
                    </div>
                    {{ $item['label'] }}
                </a>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="fw-bold mb-0">Pengumuman</h6>
        <a href="#" class="small text-decoration-none">Lihat Semua</a>
    </div>
    <div class="card card-absen overflow-hidden">
        <div style="height:120px;background:linear-gradient(135deg,#2563eb,#1d4ed8);" class="d-flex align-items-center justify-content-center text-white small">
            Belum ada pengumuman
        </div>
    </div>

</div>

<nav class="bottom-nav">
    <div class="row text-center">
        <div class="col"><a href="{{ route('dashboard') }}" class="active"><i class="bi bi-house-fill"></i>Beranda</a></div>
        <div class="col"><a href="#"><i class="bi bi-check2-square"></i>Aktivitas</a></div>
        <div class="col"><a href="#"><i class="bi bi-journal-text"></i>Tugas</a></div>
        <div class="col"><a href="#"><i class="bi bi-mortarboard"></i>Pelatihan</a></div>
        <div class="col">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-link p-0 border-0" style="text-decoration:none;color:#9aa2b1;font-size:11px;">
                    <i class="bi bi-person-circle d-block fs-5"></i>Profil
                </button>
            </form>
        </div>
    </div>
</nav>

@endsection
