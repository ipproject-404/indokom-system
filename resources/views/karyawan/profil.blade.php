@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')

@php
    $namaTampil = $karyawan->nama_lengkap ?? $user->username;
    $jabatanTampil = $karyawan->jabatan->nama_jabatan ?? ucfirst($user->role);
    $departemenTampil = $karyawan->departemen->nama_departemen ?? '-';

    $inisial = collect(explode(' ', trim($namaTampil)))
        ->filter()
        ->map(fn ($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))
        ->take(2)
        ->implode('');

    $tanggalLahirTampil = $karyawan?->tanggal_lahir
        ? \Carbon\Carbon::parse($karyawan->tanggal_lahir)->translatedFormat('d F Y')
        : '-';

    $ttlTampil = trim(($karyawan->tempat_lahir ?? '') . ($karyawan?->tanggal_lahir ? ', ' . $tanggalLahirTampil : ''));
    $ttlTampil = $ttlTampil !== '' ? $ttlTampil : '-';

    $tanggalMasukTampil = $karyawan?->tanggal_masuk
        ? \Carbon\Carbon::parse($karyawan->tanggal_masuk)->translatedFormat('d F Y')
        : '-';

    $lastLoginTampil = $user->last_login
        ? $user->last_login->translatedFormat('d F Y, H:i') . ' WIB'
        : 'Belum pernah login';

    $statusKaryawan = $karyawan->status ?? 'tidak diketahui';
    $statusWarna = $statusKaryawan === 'aktif' ? '#16a34a' : '#ef4444';

    $jenisKelaminTampil = match ($karyawan->jenis_kelamin ?? null) {
        'L' => 'Laki-laki',
        'P' => 'Perempuan',
        default => $karyawan->jenis_kelamin ?? '-',
    };

    $punyaQr = $karyawan && $karyawan->barcode_uid;
    $namaPerusahaan = config('kantor.nama', 'PT. Indokom Sistem');
@endphp

{{-- ================================================================
     VERSI HP (hanya muncul di layar < 992px)
     Mengikuti gaya visual dashboard.karyawan (mobile-shell, modern-card).
================================================================ --}}
<style>
    .app-shell {
        max-width: none;
        margin: 0;
        box-shadow: none;
        padding-bottom: 0;
    }

    body { background-color: #f1f5f9; font-family: 'Inter', 'Segoe UI', sans-serif; }

    html, body {
        margin: 0;
        padding: 0;
        width: 100%;
        overflow-x: hidden;
    }

    .mobile-shell {
        width: 100%;
        max-width: 100%;
        margin: 0;
        background: #ffffff;
        min-height: 100vh;
        box-shadow: none;
        padding: 10px 10px 90px 10px;
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

    /* ===== ID CARD (dipakai versi HP & desktop) ===== */
    /* Versi HP: menempel penuh ke tepi layar & header, sudut membulat di bawah */
    .id-card-full { border-radius: 26px !important; margin-top: -1rem; padding-top: 22px; }
    .id-card {
        position: relative;
        border-radius: 22px;
        padding: 18px 18px 16px;
        color: #fff;
        overflow: hidden;
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 55%, #38bdf8 135%);
        box-shadow: 0 14px 30px -12px rgba(37,99,235,.6);
    }
    .id-card::before {
        content: ''; position: absolute; width: 230px; height: 230px; border-radius: 50%;
        right: -70px; top: -95px;
        background: radial-gradient(circle, rgba(255,255,255,.24), transparent 70%);
    }
    .id-card::after {
        content: ''; position: absolute; width: 190px; height: 190px; border-radius: 50%;
        left: -65px; bottom: -95px;
        background: radial-gradient(circle, rgba(255,255,255,.15), transparent 70%);
    }
    .id-card > * { position: relative; z-index: 1; }
    .id-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
    .id-brand {
        font-size: .7rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase;
        opacity: .92; display: flex; align-items: center; gap: 6px;
    }
    .id-status {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.22);
        backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
        padding: 4px 10px; border-radius: 20px; font-size: .7rem; font-weight: 700;
    }
    .id-status i { font-size: .45rem; }
    .id-main { display: flex; align-items: center; gap: 14px; }
    .id-avatar {
        width: 66px; height: 66px; border-radius: 20px; flex-shrink: 0;
        background: rgba(255,255,255,.18); border: 1.5px solid rgba(255,255,255,.45);
        backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.55rem; font-weight: 800;
    }
    .id-nama { font-size: 1.1rem; font-weight: 800; line-height: 1.2; word-break: break-word; }
    .id-jabatan { font-size: .82rem; opacity: .92; margin-top: 2px; }
    .id-chip {
        display: inline-flex; align-items: center; gap: 6px; margin-top: 8px;
        background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.2);
        padding: 3px 10px; border-radius: 20px; font-size: .7rem; font-weight: 600;
    }
    .id-bottom {
        display: flex; justify-content: space-between; align-items: flex-end; gap: 12px;
        margin-top: 18px; padding-top: 14px; border-top: 1px dashed rgba(255,255,255,.32);
    }
    .id-label { font-size: .62rem; text-transform: uppercase; letter-spacing: .08em; opacity: .72; font-weight: 600; }
    .id-nik { font-size: 1rem; font-weight: 800; letter-spacing: .04em; }
    .id-nik-sm { font-size: .8rem; font-weight: 600; }
    .id-qr {
        position: relative; width: 88px; height: 88px; padding: 7px; border: none; flex-shrink: 0;
        background: #fff; border-radius: 14px; cursor: pointer; overflow: hidden;
        box-shadow: 0 6px 16px rgba(0,0,0,.22); transition: transform .15s;
    }
    .id-qr:active { transform: scale(.96); }
    .id-qr-dummy { width: 100%; height: 100%; filter: blur(3px); opacity: .85; }
    .id-qr-dummy img, .id-qr-dummy canvas { width: 100% !important; height: 100% !important; display: block; }
    .id-qr-lock {
        position: absolute; inset: 0; display: flex; flex-direction: column;
        align-items: center; justify-content: center; gap: 1px;
        background: rgba(255,255,255,.6); color: #1e3a8a; font-weight: 700;
    }
    .id-qr-lock i { font-size: 1.15rem; }
    .id-qr-lock small { font-size: .6rem; }

    .info-list .info-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .info-list .info-row:last-child { border-bottom: none; }
    .info-list .icon-box {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        background: #eff6ff;
        color: #2563eb;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .info-list .label { font-size: 0.72rem; color: #94a3b8; font-weight: 600; }
    .info-list .value { font-size: 0.87rem; color: #1e293b; font-weight: 600; word-break: break-word; }

    .section-title { font-weight: 800; color: #1e293b; font-size: 0.95rem; }

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

    .btn-keluar-mobile {
        width: 100%;
        background: #fef2f2;
        color: #ef4444;
        border: none;
        padding: 13px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 0.9rem;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
</style>

<div class="mobile-shell d-lg-none">

    <div class="app-header">
        <a href="{{ route('dashboard.karyawan') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <h6>Profil Saya</h6>
    </div>

    <div class="px-0 pt-3 pb-3">

        <div class="id-card id-card-full mb-4">
            <div class="id-top">
                <div class="id-brand"><i class="bi bi-qr-code-scan"></i> {{ $namaPerusahaan }}</div>
                <span class="id-status"><i class="bi bi-circle-fill" style="color: {{ $statusWarna }};"></i> {{ ucfirst($statusKaryawan) }}</span>
            </div>
            <div class="id-main">
                <div class="id-avatar">{{ $inisial ?: '?' }}</div>
                <div>
                    <div class="id-nama">{{ $namaTampil }}</div>
                    <div class="id-jabatan">{{ $jabatanTampil }}</div>
                    <span class="id-chip"><i class="bi bi-building"></i> {{ $departemenTampil }}</span>
                </div>
            </div>
            <div class="id-bottom">
                <div>
                    <div class="id-label">NIK Kerja</div>
                    <div class="id-nik">{{ $karyawan->nik_kerja ?? '-' }}</div>
                    <div class="id-label mt-2">Bergabung</div>
                    <div class="id-nik-sm">{{ $tanggalMasukTampil }}</div>
                </div>
                @if ($punyaQr)
                    <button type="button" class="id-qr" data-bs-toggle="modal" data-bs-target="#qrModal" aria-label="Tampilkan QR">
                        <div class="id-qr-dummy" id="qr-dummy-m"></div>
                        <span class="id-qr-lock"><i class="bi bi-eye-fill"></i><small>Tampilkan</small></span>
                    </button>
                @endif
            </div>
        </div>

        @if ($karyawan && $karyawan->departemenYangDipimpin()->isNotEmpty())
            <a href="{{ route('shift-tim.index') }}" class="modern-card d-flex align-items-center gap-3 p-3 mb-4 text-decoration-none">
                <div class="icon-box" style="width:40px;height:40px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;border-radius:12px;flex-shrink:0;">
                    <i class="bi bi-calendar-range"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold" style="color:#1e293b;font-size:.88rem;">Shift Tim</div>
                    <div class="text-muted" style="font-size:.74rem;">Atur jadwal shift anak buahmu</div>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
            </a>
        @endif

        @if ($karyawan)
            <div class="row g-2 mb-4">
                <div class="col-6">
                    <div class="modern-card p-3 text-center h-100">
                        <div class="fw-bold fs-4" style="color:#2563eb;">{{ $jumlahHadirBulanIni }}</div>
                        <div class="text-muted" style="font-size:0.72rem;">Hadir bulan ini</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="modern-card p-3 text-center h-100">
                        <span class="badge rounded-pill px-3 py-2" style="background: {{ $statusWarna }}1a; color: {{ $statusWarna }};">
                            <i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i>{{ ucfirst($statusKaryawan) }}
                        </span>
                        <div class="text-muted mt-2" style="font-size:0.72rem;">Status Karyawan</div>
                    </div>
                </div>
            </div>
        @endif

        <div class="section-title mb-2 ms-1">Data Pribadi</div>
        <div class="modern-card p-3 mb-4">
            <div class="info-list">
                <div class="info-row">
                    <div class="icon-box"><i class="bi bi-card-text"></i></div>
                    <div><div class="label">NIK KTP</div><div class="value">{{ $karyawan->nik_ktp ?? '-' }}</div></div>
                </div>
                <div class="info-row">
                    <div class="icon-box"><i class="bi bi-calendar-heart"></i></div>
                    <div><div class="label">Tempat, Tanggal Lahir</div><div class="value">{{ $ttlTampil }}</div></div>
                </div>
                <div class="info-row">
                    <div class="icon-box"><i class="bi bi-gender-ambiguous"></i></div>
                    <div><div class="label">Jenis Kelamin</div><div class="value">{{ $jenisKelaminTampil }}</div></div>
                </div>
                <div class="info-row">
                    <div class="icon-box"><i class="bi bi-telephone"></i></div>
                    <div><div class="label">No. HP</div><div class="value">{{ $karyawan->no_hp ?? '-' }}</div></div>
                </div>
                <div class="info-row">
                    <div class="icon-box"><i class="bi bi-geo-alt"></i></div>
                    <div><div class="label">Alamat</div><div class="value">{{ $karyawan->alamat ?? '-' }}</div></div>
                </div>
                <div class="info-row">
                    <div class="icon-box"><i class="bi bi-mortarboard"></i></div>
                    <div><div class="label">Pendidikan Terakhir</div><div class="value">{{ $karyawan->pendidikan ?? '-' }}</div></div>
                </div>
            </div>
        </div>

        <div class="section-title mb-2 ms-1">Data Kepegawaian</div>
        <div class="modern-card p-3 mb-4">
            <div class="info-list">
                <div class="info-row">
                    <div class="icon-box"><i class="bi bi-upc-scan"></i></div>
                    <div><div class="label">NIK Kerja</div><div class="value">{{ $karyawan->nik_kerja ?? '-' }}</div></div>
                </div>
                <div class="info-row">
                    <div class="icon-box"><i class="bi bi-briefcase"></i></div>
                    <div><div class="label">Jabatan</div><div class="value">{{ $jabatanTampil }}</div></div>
                </div>
                <div class="info-row">
                    <div class="icon-box"><i class="bi bi-building"></i></div>
                    <div><div class="label">Departemen</div><div class="value">{{ $departemenTampil }}</div></div>
                </div>
                <div class="info-row">
                    <div class="icon-box"><i class="bi bi-calendar-check"></i></div>
                    <div><div class="label">Tanggal Bergabung</div><div class="value">{{ $tanggalMasukTampil }}</div></div>
                </div>
            </div>
        </div>

        <div class="section-title mb-2 ms-1">Akun</div>
        <div class="modern-card p-3 mb-4">
            <div class="info-list">
                <div class="info-row">
                    <div class="icon-box"><i class="bi bi-person-badge"></i></div>
                    <div><div class="label">Username</div><div class="value">{{ $user->username }}</div></div>
                </div>
                <div class="info-row">
                    <div class="icon-box"><i class="bi bi-shield-check"></i></div>
                    <div><div class="label">Role</div><div class="value">{{ ucfirst($user->role) }}</div></div>
                </div>
                <div class="info-row">
                    <div class="icon-box"><i class="bi bi-clock-history"></i></div>
                    <div><div class="label">Login Terakhir</div><div class="value">{{ $lastLoginTampil }}</div></div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-keluar-mobile">
                <i class="bi bi-box-arrow-right"></i> Keluar Akun
            </button>
        </form>
    </div>

    <nav class="bottom-nav">
        <div class="row text-center gx-0">
            <div class="col"><a href="{{ route('dashboard.karyawan') }}"><i class="bi bi-house-fill"></i> Beranda</a></div>
            <div class="col"><a href="#"><i class="bi bi-check2-square"></i> Aktivitas</a></div>
            <div class="col"><a href="{{ route('absensi.karyawan') }}"><i class="bi bi-calendar-check"></i> Absensi</a></div>
            <div class="col"><a href="#"><i class="bi bi-mortarboard"></i> Pelatihan</a></div>
            <div class="col"><a href="{{ route('profil.karyawan') }}" class="active"><i class="bi bi-person-circle"></i> Profil</a></div>
        </div>
    </nav>
</div>

{{-- ================================================================
     VERSI DESKTOP (hanya muncul di layar >= 992px)
     Konsisten dengan sidebar & panel di dashboard.karyawan.
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
        .desktop-sidebar .brand {
            font-weight: 800;
            font-size: 1.05rem;
            color: #1e293b;
            padding: 0 0.5rem;
            margin-bottom: 0.1rem;
        }
        .desktop-sidebar .brand small {
            display: block;
            font-weight: 500;
            font-size: 0.72rem;
            color: #94a3b8;
        }
        .desktop-sidebar .brand i { color: #2563eb; }
        .desktop-sidebar .nav-section-title {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
            font-weight: 700;
            padding: 0 0.5rem;
            margin: 1.25rem 0 0.5rem;
        }
        .desktop-sidebar a, .desktop-sidebar button {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 12px;
            border-radius: 10px;
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.87rem;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            margin-bottom: 2px;
        }
        .desktop-sidebar a:hover, .desktop-sidebar button:hover { background: #f1f5f9; color: #1e293b; }
        .desktop-sidebar a.active { background: #2563eb; color: white; }
        .desktop-sidebar .sidebar-profile {
            margin-top: auto;
            border-top: 1px solid #e2e8f0;
            padding-top: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-left: 0.5rem;
            margin-bottom: 0.5rem;
            text-decoration: none;
        }
        .desktop-sidebar .sidebar-profile .avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: #eef2ff;
            display: flex; align-items: center; justify-content: center;
            color: #2563eb;
            flex-shrink: 0;
            font-weight: 700;
            font-size: 0.78rem;
        }
        .desktop-sidebar .sidebar-profile .nama { font-size: 0.82rem; font-weight: 700; color: #1e293b; line-height: 1.2; }
        .desktop-sidebar .sidebar-profile .peran { font-size: 0.72rem; color: #94a3b8; }

        .desktop-main { flex-grow: 1; padding: 1.75rem 2rem; max-width: 1280px; }

        .page-header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }
        .page-header-row h4 { font-weight: 800; color: #1e293b; margin-bottom: 2px; }
        .page-header-row p { color: #94a3b8; font-size: 0.85rem; margin: 0; }

        .panel {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1.25rem;
        }
        .panel h6 { font-weight: 700; color: #1e293b; margin-bottom: 1rem; }

        .ringkasan-mini {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
            border-top: 1px solid #e2e8f0;
            padding-top: 1rem;
        }
        .ringkasan-mini .item { text-align: center; }
        .ringkasan-mini .item .angka { font-size: 1.15rem; font-weight: 800; color: #1e293b; }
        .ringkasan-mini .item .label { font-size: 0.7rem; color: #94a3b8; font-weight: 600; }

        .info-list .info-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .info-list .info-row:last-child { border-bottom: none; }
        .info-list .icon-box {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            background: #eff6ff;
            color: #2563eb;
            font-size: 0.95rem;
            flex-shrink: 0;
        }
        .info-list .label { font-size: 0.72rem; color: #94a3b8; font-weight: 600; }
        .info-list .value { font-size: 0.87rem; color: #1e293b; font-weight: 600; word-break: break-word; }

        .btn-keluar-desktop {
            width: 100%;
            background: #fef2f2;
            color: #ef4444;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-top: 1rem;
        }
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
        <a href="{{ route('absensi.karyawan') }}"><i class="bi bi-calendar-check"></i> Absensi</a>
        <a href="#"><i class="bi bi-mortarboard"></i> Pelatihan</a>
        <a href="{{ route('profil.karyawan') }}" class="active"><i class="bi bi-person-circle"></i> Profil Saya</a>

        @if ($karyawan && $karyawan->departemenYangDipimpin()->isNotEmpty())
            <div class="nav-section-title">Kepala Bagian</div>
            <a href="{{ route('shift-tim.index') }}"><i class="bi bi-calendar-range"></i> Shift Tim</a>
        @endif

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
                <h4>Profil Saya</h4>
                <p>Data pribadi &amp; kepegawaian kamu di perusahaan</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-4">
                <div class="id-card">
            <div class="id-top">
                <div class="id-brand"><i class="bi bi-qr-code-scan"></i> {{ $namaPerusahaan }}</div>
                <span class="id-status"><i class="bi bi-circle-fill" style="color: {{ $statusWarna }};"></i> {{ ucfirst($statusKaryawan) }}</span>
            </div>
            <div class="id-main">
                <div class="id-avatar">{{ $inisial ?: '?' }}</div>
                <div>
                    <div class="id-nama">{{ $namaTampil }}</div>
                    <div class="id-jabatan">{{ $jabatanTampil }}</div>
                    <span class="id-chip"><i class="bi bi-building"></i> {{ $departemenTampil }}</span>
                </div>
            </div>
            <div class="id-bottom">
                <div>
                    <div class="id-label">NIK Kerja</div>
                    <div class="id-nik">{{ $karyawan->nik_kerja ?? '-' }}</div>
                    <div class="id-label mt-2">Bergabung</div>
                    <div class="id-nik-sm">{{ $tanggalMasukTampil }}</div>
                </div>
                @if ($punyaQr)
                    <button type="button" class="id-qr" data-bs-toggle="modal" data-bs-target="#qrModal" aria-label="Tampilkan QR">
                        <div class="id-qr-dummy" id="qr-dummy-d"></div>
                        <span class="id-qr-lock"><i class="bi bi-eye-fill"></i><small>Tampilkan</small></span>
                    </button>
                @endif
            </div>
        </div>

                @if ($karyawan)
                    <div class="panel mt-3">
                        <div class="ringkasan-mini" style="margin-top:0; border-top:none; padding-top:0;">
                            <div class="item">
                                <div class="angka" style="color:#2563eb;">{{ $jumlahHadirBulanIni }}</div>
                                <div class="label">Hadir bulan ini</div>
                            </div>
                            <div class="item">
                                <div class="angka" style="color: {{ $statusWarna }};">{{ ucfirst($statusKaryawan) }}</div>
                                <div class="label">Status</div>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn-keluar-desktop">
                        <i class="bi bi-box-arrow-right"></i> Keluar Akun
                    </button>
                </form>
            </div>

            <div class="col-8">
                <div class="panel mb-3">
                    <h6>Data Pribadi</h6>
                    <div class="info-list">
                        <div class="row">
                            <div class="col-6">
                                <div class="info-row">
                                    <div class="icon-box"><i class="bi bi-card-text"></i></div>
                                    <div><div class="label">NIK KTP</div><div class="value">{{ $karyawan->nik_ktp ?? '-' }}</div></div>
                                </div>
                                <div class="info-row">
                                    <div class="icon-box"><i class="bi bi-calendar-heart"></i></div>
                                    <div><div class="label">Tempat, Tanggal Lahir</div><div class="value">{{ $ttlTampil }}</div></div>
                                </div>
                                <div class="info-row">
                                    <div class="icon-box"><i class="bi bi-gender-ambiguous"></i></div>
                                    <div><div class="label">Jenis Kelamin</div><div class="value">{{ $jenisKelaminTampil }}</div></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-row">
                                    <div class="icon-box"><i class="bi bi-telephone"></i></div>
                                    <div><div class="label">No. HP</div><div class="value">{{ $karyawan->no_hp ?? '-' }}</div></div>
                                </div>
                                <div class="info-row">
                                    <div class="icon-box"><i class="bi bi-geo-alt"></i></div>
                                    <div><div class="label">Alamat</div><div class="value">{{ $karyawan->alamat ?? '-' }}</div></div>
                                </div>
                                <div class="info-row">
                                    <div class="icon-box"><i class="bi bi-mortarboard"></i></div>
                                    <div><div class="label">Pendidikan Terakhir</div><div class="value">{{ $karyawan->pendidikan ?? '-' }}</div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <div class="panel h-100">
                            <h6>Data Kepegawaian</h6>
                            <div class="info-list">
                                <div class="info-row">
                                    <div class="icon-box"><i class="bi bi-upc-scan"></i></div>
                                    <div><div class="label">NIK Kerja</div><div class="value">{{ $karyawan->nik_kerja ?? '-' }}</div></div>
                                </div>
                                <div class="info-row">
                                    <div class="icon-box"><i class="bi bi-briefcase"></i></div>
                                    <div><div class="label">Jabatan</div><div class="value">{{ $jabatanTampil }}</div></div>
                                </div>
                                <div class="info-row">
                                    <div class="icon-box"><i class="bi bi-building"></i></div>
                                    <div><div class="label">Departemen</div><div class="value">{{ $departemenTampil }}</div></div>
                                </div>
                                <div class="info-row">
                                    <div class="icon-box"><i class="bi bi-calendar-check"></i></div>
                                    <div><div class="label">Tanggal Bergabung</div><div class="value">{{ $tanggalMasukTampil }}</div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="panel h-100">
                            <h6>Akun</h6>
                            <div class="info-list">
                                <div class="info-row">
                                    <div class="icon-box"><i class="bi bi-person-badge"></i></div>
                                    <div><div class="label">Username</div><div class="value">{{ $user->username }}</div></div>
                                </div>
                                <div class="info-row">
                                    <div class="icon-box"><i class="bi bi-shield-check"></i></div>
                                    <div><div class="label">Role</div><div class="value">{{ ucfirst($user->role) }}</div></div>
                                </div>
                                <div class="info-row">
                                    <div class="icon-box"><i class="bi bi-clock-history"></i></div>
                                    <div><div class="label">Login Terakhir</div><div class="value">{{ $lastLoginTampil }}</div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

@if ($punyaQr)
<div class="modal fade" id="qrModal" tabindex="-1" data-token="{{ $karyawan->barcode_uid }}">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 340px;">
        <div class="modal-content" style="border-radius: 22px;">
            <div class="modal-body text-center p-4">
                <div class="fw-bold" style="color:#1e293b;">{{ $namaTampil }}</div>
                <div class="text-muted mb-3" style="font-size:.8rem;">{{ $karyawan->nik_kerja }}</div>
                <div id="qr-real" class="d-inline-block p-3 bg-white border rounded-4"></div>
                <div class="alert alert-warning py-2 px-3 mt-3 mb-3 text-start" style="font-size:.75rem;">
                    <i class="bi bi-shield-lock-fill me-1"></i>
                    QR ini bisa dipakai untuk login dan absen atas nama kamu. Jangan dibagikan atau di-screenshot ke orang lain.
                </div>
                <button type="button" class="btn btn-biru w-100" data-bs-dismiss="modal" style="border-radius:12px;">Tutup</button>
                <div class="text-muted mt-2" style="font-size:.68rem;">Otomatis tertutup dalam 60 detik</div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="/js/qrcode.min.js"></script>
<script>
    (function () {
        // Thumbnail di kartu sengaja memakai QR "palsu" yang di-blur, supaya
        // screenshot halaman profil tidak membocorkan QR asli.
        ['m', 'd'].forEach(function (sfx) {
            var el = document.getElementById('qr-dummy-' + sfx);
            if (el) new QRCode(el, { text: 'INDOKOM-ID-CARD', width: 74, height: 74, correctLevel: QRCode.CorrectLevel.L });
        });

        var modalEl = document.getElementById('qrModal');
        if (!modalEl) return;
        var timer;

        if (typeof QRCode === 'undefined') {
            console.error('qrcode.min.js gagal dimuat. Cek file public/js/qrcode.min.js dan tab Network (F12).');
            modalEl.addEventListener('shown.bs.modal', function () {
                document.getElementById('qr-real').innerHTML =
                    '<div class="text-danger" style="font-size:.8rem; max-width:220px;">QR gagal dimuat. Hubungi admin (file qrcode.min.js tidak ditemukan).</div>';
            });
            return;
        }

        // QR asli (isi = barcode_uid, sama dengan yang dicetak HRD) baru
        // digambar saat modal dibuka, dan dihapus lagi saat ditutup.
        modalEl.addEventListener('shown.bs.modal', function () {
            var target = document.getElementById('qr-real');
            target.innerHTML = '';
            new QRCode(target, {
                text: modalEl.dataset.token,
                width: 220, height: 220,
                colorDark: '#0f172a', colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
            timer = setTimeout(function () {
                var inst = bootstrap.Modal.getInstance(modalEl);
                if (inst) inst.hide();
            }, 60000);
        });
        modalEl.addEventListener('hidden.bs.modal', function () {
            clearTimeout(timer);
            document.getElementById('qr-real').innerHTML = '';
        });
    })();
</script>
@endpush
@endif

@endsection