@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')

@php
    $statusInfo = [
        'hadir' => ['icon' => 'bi-check-circle-fill', 'warna' => '#16a34a', 'label' => 'Hadir'],
        'tidak_hadir' => ['icon' => 'bi-record-circle', 'warna' => '#ef4444', 'label' => 'Tidak Hadir'],
        'belum_diisi' => ['icon' => 'bi-circle', 'warna' => '#cbd5e1', 'label' => 'Belum Diisi'],
        'libur' => ['icon' => 'bi-square-fill', 'warna' => '#94a3b8', 'label' => 'Hari Libur'],
        'belum_gabung' => ['icon' => 'bi-dash-circle', 'warna' => '#e2e8f0', 'label' => 'Belum Bergabung'],
    ];

    $namaBulanTampil = $bulanDipilih->translatedFormat('F Y');
    $namaTampil = $karyawan->nama_lengkap ?? $user->username;
    $jabatanTampil = $karyawan->jabatan->nama_jabatan ?? ucfirst($user->role);
    $inisial = collect(explode(' ', trim($namaTampil)))
        ->filter()
        ->map(fn ($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))
        ->take(2)
        ->implode('');

    $namaHari = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
@endphp

{{-- ================================================================
     VERSI HP (hanya muncul di layar < 992px)
================================================================ --}}
<style>
    html, body { margin: 0; padding: 0; width: 100%; overflow-x: hidden; }
    body { background-color: #f1f5f9; font-family: 'Inter', 'Segoe UI', sans-serif; }

    .app-shell { max-width: none; margin: 0; box-shadow: none; padding-bottom: 0; }

    .mobile-shell {
        width: 100%;
        max-width: 100%;
        margin: 0;
        background: #ffffff;
        min-height: 100vh;
        box-shadow: none;
        padding-bottom: 90px;
        position: relative;
    }
    .mobile-shell .app-header {
        background: #fff;
        padding: 1.1rem 1rem;
        border-bottom: 1px solid #e2e8f0;
        position: sticky;
        top: 0;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .mobile-shell .app-header .back-btn {
        width: 38px; height: 38px;
        border-radius: 12px;
        background: #f1f5f9;
        display: flex; align-items: center; justify-content: center;
        color: #475569;
        text-decoration: none;
        flex-shrink: 0;
    }
    .mobile-shell .app-header h6 { margin: 0; font-weight: 800; color: #1e293b; }

    .mobile-shell .modern-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.02);
        overflow: hidden;
    }

    .periode-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 8px;
    }
    .periode-bar .nav-btn {
        width: 34px; height: 34px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #475569;
        display: flex; align-items: center; justify-content: center;
        text-decoration: none;
        flex-shrink: 0;
    }
    .periode-bar .label { text-align: center; }
    .periode-bar .label .bulan { font-weight: 800; color: #1e293b; font-size: 0.95rem; }

    .ringkasan-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .ringkasan-grid .kotak {
        border-radius: 14px;
        padding: 10px 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ringkasan-grid .kotak .angka { font-weight: 800; font-size: 1.15rem; line-height: 1; }
    .ringkasan-grid .kotak .label { font-size: 0.68rem; color: #64748b; font-weight: 600; }

    .kalender-header {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
        font-size: 0.68rem;
        font-weight: 700;
        color: #94a3b8;
        padding: 0 2px 8px;
    }
    .kalender-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
    }
    .kalender-sel {
        aspect-ratio: 1 / 1.05;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 3px;
        background: #f8fafc;
    }
    .kalender-sel.kosong { background: transparent; }
    .kalender-sel .tanggal-angka {
        font-size: 0.78rem;
        font-weight: 700;
        color: #334155;
        width: 22px; height: 22px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%;
    }
    .kalender-sel.hari-ini .tanggal-angka {
        border: 2px solid #2563eb;
        color: #2563eb;
    }
    .kalender-sel i { font-size: 0.65rem; }

    .legenda-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.76rem;
        color: #475569;
        padding: 5px 0;
    }
    .legenda-item i { font-size: 0.85rem; width: 18px; text-align: center; }

    .mobile-shell .bottom-nav {
        position: fixed;
        bottom: 0; left: 50%;
        transform: translateX(-50%);
        width: 100%; max-width: 480px;
        background: white;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
        border-top: 1px solid #e2e8f0;
        padding: 0.5rem 0;
    }
    .mobile-shell .bottom-nav a, .mobile-shell .bottom-nav button {
        display: flex; flex-direction: column; align-items: center;
        font-size: 0.7rem; color: #94a3b8; text-decoration: none; border: none; background: none;
        width: 100%;
    }
    .mobile-shell .bottom-nav a.active { color: #0d6efd; }
    .mobile-shell .bottom-nav i { font-size: 1.3rem; margin-bottom: 2px; }
</style>

<div class="mobile-shell d-lg-none">

    <div class="app-header">
        <a href="{{ route('dashboard.karyawan') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <h6>Rekap Absensi</h6>
    </div>

    <div class="px-3 pt-3 pb-3">

        <div class="modern-card mb-3">
            <div class="periode-bar">
                <a href="{{ route('absensi.karyawan', ['bulan' => $bulanSebelumnya]) }}" class="nav-btn"><i class="bi bi-chevron-left"></i></a>
                <div class="label">
                    <div class="bulan">{{ $namaBulanTampil }}</div>
                </div>
                <a href="{{ route('absensi.karyawan', ['bulan' => $bulanBerikutnya]) }}" class="nav-btn"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>

        <div class="ringkasan-grid mb-3">
            <div class="kotak" style="background:#f0fdf4;">
                <div class="angka" style="color:#16a34a;">{{ $ringkasan['hadir'] }}</div>
                <div class="label">Hadir</div>
            </div>
            <div class="kotak" style="background:#fef2f2;">
                <div class="angka" style="color:#ef4444;">{{ $ringkasan['tidak_hadir'] }}</div>
                <div class="label">Tidak Hadir</div>
            </div>
        </div>

        <div class="modern-card p-3 mb-3">
            <div class="kalender-header">
                @foreach ($namaHari as $h)
                    <div>{{ $h }}</div>
                @endforeach
            </div>
            <div class="kalender-grid">
                @foreach ($minggu as $baris)
                    @foreach ($baris as $hari)
                        @if ($hari === null)
                            <div class="kalender-sel kosong"></div>
                        @else
                            <div class="kalender-sel {{ $hari['hari_ini'] ? 'hari-ini' : '' }}">
                                <div class="tanggal-angka">{{ $hari['tanggal']->day }}</div>
                                <i class="bi {{ $statusInfo[$hari['status']]['icon'] }}" style="color: {{ $statusInfo[$hari['status']]['warna'] }};"></i>
                            </div>
                        @endif
                    @endforeach
                @endforeach
            </div>
        </div>

        <div class="modern-card p-3">
            <div class="row">
                @foreach ($statusInfo as $info)
                    <div class="col-6">
                        <div class="legenda-item">
                            <i class="bi {{ $info['icon'] }}" style="color: {{ $info['warna'] }};"></i>
                            {{ $info['label'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <nav class="bottom-nav">
        <div class="row text-center gx-0">
            <div class="col"><a href="{{ route('dashboard.karyawan') }}"><i class="bi bi-house-fill"></i> Beranda</a></div>
            <div class="col"><a href="#"><i class="bi bi-check2-square"></i> Aktivitas</a></div>
            <div class="col"><a href="{{ route('absensi.karyawan') }}" class="active"><i class="bi bi-calendar-check"></i> Absensi</a></div>
            <div class="col"><a href="#"><i class="bi bi-mortarboard"></i> Pelatihan</a></div>
            <div class="col"><a href="{{ route('profil.karyawan') }}"><i class="bi bi-person-circle"></i> Profil</a></div>
        </div>
    </nav>
</div>

{{-- ================================================================
     VERSI DESKTOP (hanya muncul di layar >= 992px)
================================================================ --}}
<style>
    .desktop-shell { display: none; }

    @media (min-width: 992px) {
        .desktop-shell {
            display: flex;
            min-height: 100vh;
            background: #f8fafc;
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }
        .desktop-sidebar {
            width: 250px;
            flex-shrink: 0;
            background: #fff;
            border-right: 1px solid #e2e8f0;
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
        }
        .desktop-sidebar .brand { font-weight: 800; font-size: 1.05rem; color: #1e293b; padding: 0 0.5rem; margin-bottom: 0.1rem; }
        .desktop-sidebar .brand small { display: block; font-weight: 500; font-size: 0.72rem; color: #94a3b8; }
        .desktop-sidebar .brand i { color: #2563eb; }
        .desktop-sidebar .nav-section-title {
            font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.06em;
            color: #94a3b8; font-weight: 700; padding: 0 0.5rem; margin: 1.25rem 0 0.5rem;
        }
        .desktop-sidebar a, .desktop-sidebar button {
            display: flex; align-items: center; gap: 12px; padding: 9px 12px;
            border-radius: 10px; color: #64748b; text-decoration: none; font-weight: 500;
            font-size: 0.87rem; border: none; background: none; width: 100%; text-align: left; margin-bottom: 2px;
        }
        .desktop-sidebar a:hover, .desktop-sidebar button:hover { background: #f1f5f9; color: #1e293b; }
        .desktop-sidebar a.active { background: #2563eb; color: white; }
        .desktop-sidebar .sidebar-profile {
            margin-top: auto; border-top: 1px solid #e2e8f0; padding-top: 0.9rem;
            display: flex; align-items: center; gap: 10px; padding-left: 0.5rem; margin-bottom: 0.5rem; text-decoration: none;
        }
        .desktop-sidebar .sidebar-profile .avatar {
            width: 34px; height: 34px; border-radius: 50%; background: #eef2ff;
            display: flex; align-items: center; justify-content: center; color: #2563eb;
            flex-shrink: 0; font-weight: 700; font-size: 0.78rem;
        }
        .desktop-sidebar .sidebar-profile .nama { font-size: 0.82rem; font-weight: 700; color: #1e293b; line-height: 1.2; }
        .desktop-sidebar .sidebar-profile .peran { font-size: 0.72rem; color: #94a3b8; }

        .desktop-main { flex-grow: 1; padding: 1.75rem 2rem; max-width: 1280px; }

        .page-header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .page-header-row h4 { font-weight: 800; color: #1e293b; margin-bottom: 2px; }
        .page-header-row p { color: #94a3b8; font-size: 0.85rem; margin: 0; }

        .periode-bar-desktop {
            display: flex; align-items: center; gap: 10px;
            background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 6px 8px;
        }
        .periode-bar-desktop .nav-btn {
            width: 30px; height: 30px; border-radius: 8px; background: #f1f5f9; color: #475569;
            display: flex; align-items: center; justify-content: center; text-decoration: none;
        }
        .periode-bar-desktop .bulan { font-weight: 700; color: #1e293b; font-size: 0.9rem; padding: 0 6px; min-width: 140px; text-align: center; }

        .stat-card {
            background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.1rem 1.25rem;
            display: flex; justify-content: space-between; align-items: flex-start;
        }
        .stat-card .label { font-size: 0.78rem; color: #94a3b8; font-weight: 600; margin-bottom: 6px; }
        .stat-card .value { font-size: 1.5rem; font-weight: 800; color: #1e293b; }
        .stat-card .icon-box {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; font-size: 1.05rem; flex-shrink: 0;
        }

        .panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; }
        .panel h6 { font-weight: 700; color: #1e293b; margin-bottom: 1rem; }

        .kalender-header-d {
            display: grid; grid-template-columns: repeat(7, 1fr);
            text-align: center; font-size: 0.75rem; font-weight: 700; color: #94a3b8; padding: 0 2px 10px;
        }
        .kalender-grid-d { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; }
        .kalender-sel-d {
            min-height: 74px; border-radius: 12px; background: #f8fafc;
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px;
        }
        .kalender-sel-d.kosong { background: transparent; }
        .kalender-sel-d .tanggal-angka {
            font-size: 0.9rem; font-weight: 700; color: #334155;
            width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; border-radius: 50%;
        }
        .kalender-sel-d.hari-ini .tanggal-angka { border: 2px solid #2563eb; color: #2563eb; }
        .kalender-sel-d i { font-size: 0.85rem; }

        .legenda-item-d { display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: #475569; padding: 6px 0; }
        .legenda-item-d i { font-size: 0.9rem; width: 18px; text-align: center; }
    }
</style>

<div class="desktop-shell">

    <aside class="desktop-sidebar">
        <div class="brand">
            <div><i class="bi bi-qr-code-scan me-1"></i> Presensi App</div>
            <small>Absensi &amp; Aktivitas Karyawan</small>
        </div>

        <div class="nav-section-title">Menu Utama</div>
        <a href="{{ route('dashboard.karyawan') }}"><i class="bi bi-house-fill"></i> Dashboard</a>

        <div class="nav-section-title">Aktivitas Saya</div>
        <a href="#"><i class="bi bi-check2-square"></i> Aktivitas</a>
        <a href="{{ route('absensi.karyawan') }}" class="active"><i class="bi bi-calendar-check"></i> Absensi</a>
        <a href="#"><i class="bi bi-mortarboard"></i> Pelatihan</a>
        <a href="{{ route('profil.karyawan') }}"><i class="bi bi-person-circle"></i> Profil Saya</a>

        <a href="{{ route('profil.karyawan') }}" class="sidebar-profile">
            <div class="avatar">{{ $inisial ?: '?' }}</div>
            <div>
                <div class="nama">{{ $namaTampil }}</div>
                <div class="peran">{{ $jabatanTampil }}</div>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
            @csrf
            <button type="submit"><i class="bi bi-box-arrow-right"></i> Keluar</button>
        </form>
    </aside>

    <main class="desktop-main">

        <div class="page-header-row">
            <div>
                <h4>Rekap Absensi</h4>
                <p>Riwayat kehadiran kamu per bulan</p>
            </div>
            <div class="periode-bar-desktop">
                <a href="{{ route('absensi.karyawan', ['bulan' => $bulanSebelumnya]) }}" class="nav-btn"><i class="bi bi-chevron-left"></i></a>
                <div class="bulan">{{ $namaBulanTampil }}</div>
                <a href="{{ route('absensi.karyawan', ['bulan' => $bulanBerikutnya]) }}" class="nav-btn"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-6">
                <div class="stat-card">
                    <div><div class="label">Hadir</div><div class="value">{{ $ringkasan['hadir'] }}</div></div>
                    <div class="icon-box" style="background:#dcfce7; color:#16a34a;"><i class="bi bi-check-circle"></i></div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card">
                    <div><div class="label">Tidak Hadir</div><div class="value">{{ $ringkasan['tidak_hadir'] }}</div></div>
                    <div class="icon-box" style="background:#fee2e2; color:#ef4444;"><i class="bi bi-record-circle"></i></div>
                </div>
            </div>
        </div>

        <div class="panel">
            <h6>Kalender Kehadiran &mdash; {{ $namaBulanTampil }}</h6>

            <div class="kalender-header-d">
                @foreach ($namaHari as $h)
                    <div>{{ $h }}</div>
                @endforeach
            </div>
            <div class="kalender-grid-d mb-3">
                @foreach ($minggu as $baris)
                    @foreach ($baris as $hari)
                        @if ($hari === null)
                            <div class="kalender-sel-d kosong"></div>
                        @else
                            <div class="kalender-sel-d {{ $hari['hari_ini'] ? 'hari-ini' : '' }}">
                                <div class="tanggal-angka">{{ $hari['tanggal']->day }}</div>
                                <i class="bi {{ $statusInfo[$hari['status']]['icon'] }}" style="color: {{ $statusInfo[$hari['status']]['warna'] }};"></i>
                            </div>
                        @endif
                    @endforeach
                @endforeach
            </div>

            <div class="row border-top pt-3">
                @foreach ($statusInfo as $info)
                    <div class="col-3">
                        <div class="legenda-item-d">
                            <i class="bi {{ $info['icon'] }}" style="color: {{ $info['warna'] }};"></i>
                            {{ $info['label'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
</div>

@endsection