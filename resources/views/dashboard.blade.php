@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    // Data menu dipakai bareng oleh versi HP maupun Desktop
    $menuAplikasi = [
        ['icon' => 'bi-calendar-check', 'label' => 'Absensi', 'warna' => '#f59e0b', 'link' => route('absensi.scan')],
        ['icon' => 'bi-people', 'label' => 'Kehadiran', 'warna' => '#16a34a', 'link' => '#'],
        ['icon' => 'bi-award', 'label' => 'Talent', 'warna' => '#ec4899', 'link' => '#'],
        ['icon' => 'bi-file-earmark-text', 'label' => 'Report', 'warna' => '#2563eb', 'link' => '#'],
        ['icon' => 'bi-grid-3x3-gap', 'label' => 'Spaces', 'warna' => '#f97316', 'link' => '#'],
        ['icon' => 'bi-clock-history', 'label' => 'Lembur', 'warna' => '#16a34a', 'link' => '#'],
        ['icon' => 'bi-cash-coin', 'label' => 'Reimburse', 'warna' => '#ec4899', 'link' => '#'],
        ['icon' => 'bi-car-front', 'label' => 'Fasilitas', 'warna' => '#2563eb', 'link' => '#'],
    ];

    $labelStatusAbsen = !$presensiHariIni ? 'Clock In' : (!$presensiHariIni->jam_pulang ? 'Clock Out' : 'Selesai Hari Ini');
    $jamMasuk = $presensiHariIni?->jam_masuk ? \Carbon\Carbon::parse($presensiHariIni->jam_masuk)->format('H:i') : '--:--';
    $jamPulang = $presensiHariIni?->jam_pulang ? \Carbon\Carbon::parse($presensiHariIni->jam_pulang)->format('H:i') : '--:--';
    $namaTampil = $karyawan->nama_lengkap ?? $user->username;
    $jabatanTampil = $karyawan->jabatan->nama_jabatan ?? ucfirst($user->role);
    $tanggalHariIni = \Carbon\Carbon::now()->translatedFormat('l, d F Y');
@endphp

{{-- ================================================================
     VERSI HP (hanya muncul di layar < 992px)
     Tidak berbagi CSS/markup dengan versi desktop di bawah.
================================================================ --}}
<style>
    /* Halaman dashboard mengatur lebarnya sendiri lewat .mobile-shell dan
       .desktop-shell di bawah, jadi batasan lebar 480px bawaan layout
       (.app-shell, dipakai halaman Login/Scan QR) dimatikan KHUSUS di
       halaman ini saja -- halaman lain tidak terpengaruh. */
    .app-shell {
        max-width: none;
        margin: 0;
        box-shadow: none;
        padding-bottom: 0;
    }

    body { background-color: #f1f5f9; font-family: 'Inter', 'Segoe UI', sans-serif; }

    .mobile-shell {
        max-width: 480px;
        margin: 0 auto;
        background: #ffffff;
        min-height: 100vh;
        box-shadow: 0 0 30px rgba(0,0,0,0.08);
        padding-bottom: 80px;
        position: relative;
    }
    .mobile-shell .app-header {
        background: #fff;
        padding: 1.2rem 1rem;
        border-bottom: 1px solid #e2e8f0;
        position: sticky;
        top: 0;
    }
    .mobile-shell .modern-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .mobile-shell .absen-box {
        background: linear-gradient(135deg, #fff, #f8fafc);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1rem;
    }
    .mobile-shell .time-display {
        font-size: 2.2rem;
        font-weight: 800;
        letter-spacing: -1px;
        color: #1e293b;
        line-height: 1;
    }
    .mobile-shell .menu-grid-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .mobile-shell .icon-circle {
        width: 50px; height: 50px;
        border-radius: 16px;
        display: flex; justify-content: center; align-items: center;
        color: white; font-size: 1.4rem;
        margin-bottom: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
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
    }
    .mobile-shell .bottom-nav a.active, .mobile-shell .bottom-nav a:hover { color: #0d6efd; }
    .mobile-shell .bottom-nav i { font-size: 1.3rem; margin-bottom: 2px; }
</style>

<div class="mobile-shell d-lg-none">

    <div class="app-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width:45px;height:45px;">
                <i class="bi bi-person-fill text-secondary fs-4"></i>
            </div>
            <div>
                <div class="fw-bold text-dark lh-sm">{{ $namaTampil }}</div>
                <div class="small text-muted">{{ $jabatanTampil }}</div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-light rounded-circle p-2" style="width:40px;height:40px;"><i class="bi bi-search"></i></button>
            <button class="btn btn-light rounded-circle p-2 position-relative" style="width:40px;height:40px;">
                <i class="bi bi-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
            </button>
        </div>
    </div>

    <div class="p-3">
        @if ($pesanAbsensi)
            <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-3 rounded-3 small">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <div>{{ $pesanAbsensi }}</div>
            </div>
        @endif

        <div class="modern-card p-3 mb-4">
            <div class="text-center mb-4 mt-2">
                <div class="text-muted small mb-1"><i class="bi bi-calendar-event me-1"></i> {{ $tanggalHariIni }}</div>
                <div class="time-display mt-2" id="realtime-clock-mobile">--:--:--</div>
                <div class="badge bg-primary-subtle text-primary fw-semibold mt-1 px-3">WIB</div>
            </div>

            <div class="absen-box mb-3">
                <div class="row text-center">
                    <div class="col-6 border-end border-2">
                        <div class="text-muted small fw-semibold mb-1">MASUK</div>
                        <div class="fw-bold fs-4 text-dark">{{ $jamMasuk }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small fw-semibold mb-1">KELUAR</div>
                        <div class="fw-bold fs-4 text-dark">{{ $jamPulang }}</div>
                    </div>
                </div>
            </div>

            <a href="{{ route('absensi.scan') }}" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm d-flex justify-content-center align-items-center">
                <i class="bi bi-qr-code-scan me-2 fs-5"></i> {{ $labelStatusAbsen }}
            </a>
        </div>

        <h6 class="fw-bold mb-3 ms-1">Menu Aplikasi</h6>
        <div class="modern-card p-3 mb-4">
            <div class="row row-cols-4 g-3">
                @foreach ($menuAplikasi as $item)
                    <div class="col">
                        <a href="{{ $item['link'] }}" class="menu-grid-item">
                            <div class="icon-circle" style="background: {{ $item['warna'] }};">
                                <i class="bi {{ $item['icon'] }}"></i>
                            </div>
                            <span style="font-size: 0.7rem;">{{ $item['label'] }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <h6 class="fw-bold mb-3 ms-1">Pengumuman</h6>
        <div class="modern-card overflow-hidden">
            <div class="p-4 text-white position-relative" style="background: linear-gradient(135deg, #0d6efd, #4facfe);">
                <span class="badge bg-white text-primary mb-2">Info HRD</span>
                <h6 class="fw-bold mb-1">Belum ada pengumuman</h6>
                <p class="small mb-0 opacity-75" style="font-size: 0.8rem;">Pantau terus kolom ini untuk info terbaru.</p>
                <i class="bi bi-megaphone-fill position-absolute text-white opacity-25" style="font-size: 5rem; right: -10px; bottom: -10px;"></i>
            </div>
        </div>
    </div>

    <nav class="bottom-nav">
        <div class="row text-center gx-0">
            <div class="col"><a href="{{ route('dashboard') }}" class="active"><i class="bi bi-house-fill"></i> Beranda</a></div>
            <div class="col"><a href="#"><i class="bi bi-check2-square"></i> Aktivitas</a></div>
            <div class="col"><a href="#"><i class="bi bi-journal-text"></i> Tugas</a></div>
            <div class="col"><a href="#"><i class="bi bi-mortarboard"></i> Pelatihan</a></div>
            <div class="col">
                <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                    @csrf
                    <button type="submit" class="w-100"><i class="bi bi-person-circle text-secondary"></i> Profil</button>
                </form>
            </div>
        </div>
    </nav>
</div>


{{-- ================================================================
     VERSI DESKTOP (hanya muncul di layar >= 992px)
     Bergaya admin dashboard korporat, konsisten dengan Dashboard HRD.
     Berdiri sendiri, tidak pakai class/style dari versi HP di atas.
================================================================ --}}
@php
    $statusHariIni = !$presensiHariIni ? 'Belum Absen' : (!$presensiHariIni->jam_pulang ? 'Sudah Masuk' : 'Selesai');
    $statusWarna = !$presensiHariIni ? '#f59e0b' : (!$presensiHariIni->jam_pulang ? '#16a34a' : '#64748b');
@endphp
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
        .desktop-sidebar .menu-icon-small {
            width: 26px; height: 26px;
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 0.8rem;
            flex-shrink: 0;
        }
        .desktop-sidebar .sidebar-profile {
            margin-top: auto;
            border-top: 1px solid #e2e8f0;
            padding-top: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-left: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .desktop-sidebar .sidebar-profile .avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: #eef2ff;
            display: flex; align-items: center; justify-content: center;
            color: #2563eb;
            flex-shrink: 0;
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
        .date-pill {
            background: #eef2ff;
            color: #2563eb;
            font-weight: 600;
            font-size: 0.82rem;
            padding: 8px 16px;
            border-radius: 10px;
            white-space: nowrap;
        }

        .stat-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1.1rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .stat-card .label { font-size: 0.78rem; color: #94a3b8; font-weight: 600; margin-bottom: 6px; }
        .stat-card .value { font-size: 1.5rem; font-weight: 800; color: #1e293b; }
        .stat-card .value small { font-size: 0.8rem; color: #94a3b8; font-weight: 600; }
        .stat-card .icon-box {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.05rem;
            flex-shrink: 0;
        }

        .panel {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1.25rem;
        }
        .panel h6 { font-weight: 700; color: #1e293b; margin-bottom: 1rem; }

        .aksi-cepat-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            border-radius: 10px;
            text-decoration: none;
            color: inherit;
            margin-bottom: 4px;
        }
        .aksi-cepat-item:hover { background: #f8fafc; }
        .aksi-cepat-item .icon-box {
            width: 38px; height: 38px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: white;
            flex-shrink: 0;
            font-size: 0.95rem;
        }
        .aksi-cepat-item .judul { font-size: 0.85rem; font-weight: 700; color: #1e293b; }
        .aksi-cepat-item .sub { font-size: 0.75rem; color: #94a3b8; }

        .absen-highlight {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 14px;
            padding: 1.5rem;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .absen-highlight .time-display { font-size: 2.2rem; font-weight: 800; letter-spacing: -1px; }
        .absen-highlight .btn-clock {
            background: white;
            color: #2563eb;
            border: none;
            padding: 12px 28px;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .absen-highlight .btn-clock:hover { background: #f1f5f9; color: #1d4ed8; }
    }
</style>

<div class="desktop-shell">

    <aside class="desktop-sidebar">
        <div class="brand">
            <div><i class="bi bi-qr-code-scan me-1"></i> Presensi App</div>
            <small>Absensi &amp; Aktivitas Karyawan</small>
        </div>

        <div class="nav-section-title">Menu Utama</div>
        <a href="{{ route('dashboard') }}" class="active"><i class="bi bi-house-fill"></i> Dashboard</a>

        <div class="nav-section-title">Aktivitas Saya</div>
        <a href="#"><i class="bi bi-check2-square"></i> Aktivitas</a>
        <a href="#"><i class="bi bi-journal-text"></i> Tugas</a>
        <a href="#"><i class="bi bi-mortarboard"></i> Pelatihan</a>

        <div class="nav-section-title">Menu Aplikasi</div>
        @foreach ($menuAplikasi as $item)
            <a href="{{ $item['link'] }}">
                <span class="menu-icon-small" style="background: {{ $item['warna'] }};">
                    <i class="bi {{ $item['icon'] }}"></i>
                </span>
                {{ $item['label'] }}
            </a>
        @endforeach

        <div class="sidebar-profile">
            <div class="avatar"><i class="bi bi-person-fill"></i></div>
            <div>
                <div class="nama">{{ $namaTampil }}</div>
                <div class="peran">{{ $jabatanTampil }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
            @csrf
            <button type="submit"><i class="bi bi-box-arrow-right"></i> Keluar</button>
        </form>
    </aside>

    <main class="desktop-main">

        <div class="page-header-row">
            <div>
                <h4>Dashboard Saya</h4>
                <p>Absensi &amp; aktivitas kerja harian kamu</p>
            </div>
            <div class="date-pill"><i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
        </div>

        @if ($pesanAbsensi)
            <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-3 rounded-3 small">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <div>{{ $pesanAbsensi }}</div>
            </div>
        @endif

        {{-- Baris kartu statistik --}}
        <div class="row g-3 mb-3">
            <div class="col-4">
                <div class="stat-card">
                    <div>
                        <div class="label">Status Hari Ini</div>
                        <div class="value" style="font-size:1.15rem;">{{ $statusHariIni }}</div>
                    </div>
                    <div class="icon-box" style="background: {{ $statusWarna }}1a; color: {{ $statusWarna }};">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-card">
                    <div>
                        <div class="label">Jam Masuk</div>
                        <div class="value">{{ $jamMasuk }}</div>
                    </div>
                    <div class="icon-box" style="background:#fef3c7; color:#f59e0b;">
                        <i class="bi bi-box-arrow-in-right"></i>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-card">
                    <div>
                        <div class="label">Jam Pulang</div>
                        <div class="value">{{ $jamPulang }}</div>
                    </div>
                    <div class="icon-box" style="background:#dbeafe; color:#2563eb;">
                        <i class="bi bi-box-arrow-left"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Konten 2 kolom: Absensi (kiri, lebih besar) + Aksi Cepat (kanan) --}}
        <div class="row g-3 mb-3">
            <div class="col-8">
                <div class="panel h-100">
                    <h6>Absensi Hari Ini</h6>
                    <div class="absen-highlight">
                        <div>
                            <div class="small opacity-75 mb-1">{{ $tanggalHariIni }}</div>
                            <div class="time-display" id="realtime-clock-desktop">--:--:--</div>
                            <div class="small opacity-75 mt-1">WIB</div>
                        </div>
                        <a href="{{ route('absensi.scan') }}" class="btn-clock">
                            <i class="bi bi-qr-code-scan"></i> {{ $labelStatusAbsen }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="panel h-100">
                    <h6>Aksi Cepat</h6>
                    <a href="{{ route('absensi.scan') }}" class="aksi-cepat-item">
                        <span class="icon-box" style="background:#2563eb;"><i class="bi bi-qr-code-scan"></i></span>
                        <span>
                            <span class="judul d-block">Scan Absensi</span>
                            <span class="sub">Catat kehadiran via QR</span>
                        </span>
                    </a>
                    <a href="#" class="aksi-cepat-item">
                        <span class="icon-box" style="background:#16a34a;"><i class="bi bi-clock-history"></i></span>
                        <span>
                            <span class="judul d-block">Riwayat Kehadiran</span>
                            <span class="sub">Lihat presensi sebelumnya</span>
                        </span>
                    </a>
                    <a href="#" class="aksi-cepat-item">
                        <span class="icon-box" style="background:#f59e0b;"><i class="bi bi-file-earmark-plus"></i></span>
                        <span>
                            <span class="judul d-block">Ajukan Lembur</span>
                            <span class="sub">Kirim pengajuan ke HRD</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Pengumuman --}}
        <div class="panel p-0 overflow-hidden">
            <div class="p-4 text-white position-relative" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 14px;">
                <span class="badge bg-white text-primary mb-2">Info HRD</span>
                <h6 class="fw-bold mb-1 text-white">Belum ada pengumuman</h6>
                <p class="small mb-0 opacity-75">Pantau terus kolom ini untuk info terbaru.</p>
                <i class="bi bi-megaphone-fill position-absolute text-white opacity-25" style="font-size: 5rem; right: 10px; bottom: -10px;"></i>
            </div>
        </div>
    </main>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateClock() {
            const now = new Date();
            const text = String(now.getHours()).padStart(2,'0') + ':' +
                         String(now.getMinutes()).padStart(2,'0') + ':' +
                         String(now.getSeconds()).padStart(2,'0');
            const mobileEl = document.getElementById('realtime-clock-mobile');
            const desktopEl = document.getElementById('realtime-clock-desktop');
            if (mobileEl) mobileEl.textContent = text;
            if (desktopEl) desktopEl.textContent = text;
        }
        updateClock();
        setInterval(updateClock, 1000);
    });
</script>
@endpush
@endsection