@php
    // Sidebar desktop sisi karyawan. Dipakai: dashboard, profil, rekap absensi.
    // Halaman dashboard mengirim $menuAplikasi (grid menu berwarna); halaman
    // lain tidak mengirimnya, jadi bagian "Menu Aplikasi" otomatis tidak tampil.
    $akunSb = Auth::user();
    $karyawanSb = $akunSb->karyawan;

    $namaSb = $karyawanSb->nama_lengkap ?? $akunSb->username;
    $jabatanSb = $karyawanSb->jabatan->nama_jabatan ?? ucfirst($akunSb->role);
    $inisialSb = collect(explode(' ', trim($namaSb)))
        ->filter()
        ->map(fn ($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))
        ->take(2)
        ->implode('');

    $isHrdSb = $akunSb->role === 'hrd';
    $isKepalaSb = $karyawanSb && $karyawanSb->departemenYangDipimpin()->isNotEmpty();

    // route null = menu belum aktif (link '#'). 'aktif' = nama route yang
    // membuat menu tampil terpilih.
    $bagianSb = [
        'Menu Utama' => [
            ['label' => 'Dashboard', 'icon' => 'bi-house-fill', 'route' => 'dashboard.karyawan', 'aktif' => ['dashboard.karyawan']],
        ],
        'Aktivitas Saya' => [
            ['label' => 'Aktivitas', 'icon' => 'bi-check2-square', 'route' => null, 'aktif' => []],
            ['label' => 'Absensi', 'icon' => 'bi-calendar-check', 'route' => 'absensi.karyawan', 'aktif' => ['absensi.karyawan']],
            ['label' => 'Pelatihan', 'icon' => 'bi-mortarboard', 'route' => null, 'aktif' => []],
            ['label' => 'Profil Saya', 'icon' => 'bi-person-circle', 'route' => 'profil.karyawan', 'aktif' => ['profil.karyawan']],
        ],
    ];

    if ($isHrdSb) {
        $bagianSb['Khusus HRD'] = [
            ['label' => 'Dashboard HRD', 'icon' => 'bi-grid-1x2-fill', 'route' => 'dashboard.hrd', 'aktif' => []],
        ];
    }

    // Di dashboard, Shift Tim sudah muncul lewat Menu Aplikasi (menggantikan
    // Talent), jadi tidak ditampilkan dobel di sini.
    if ($isKepalaSb && empty($menuAplikasi)) {
        $bagianSb['Kepala Bagian'] = [
            ['label' => 'Shift Tim', 'icon' => 'bi-calendar-range', 'route' => 'shift-tim.index', 'aktif' => ['shift-tim.index']],
        ];
    }
@endphp

@push('styles')
<style>
    @media (min-width: 992px) {
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
        .desktop-sidebar .menu-icon-small {
            width: 26px; height: 26px; border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 0.8rem; flex-shrink: 0;
        }
        .desktop-sidebar .sidebar-profile {
            margin-top: auto; border-top: 1px solid #e2e8f0; padding-top: 0.9rem;
            display: flex; align-items: center; gap: 10px; padding-left: 0.5rem;
            margin-bottom: 0.5rem; text-decoration: none;
        }
        .desktop-sidebar .sidebar-profile .avatar {
            width: 34px; height: 34px; border-radius: 50%; background: #eef2ff;
            display: flex; align-items: center; justify-content: center; color: #2563eb;
            flex-shrink: 0; font-weight: 700; font-size: 0.78rem;
        }
        .desktop-sidebar .sidebar-profile .nama { font-size: 0.82rem; font-weight: 700; color: #1e293b; line-height: 1.2; }
        .desktop-sidebar .sidebar-profile .peran { font-size: 0.72rem; color: #94a3b8; }
    }
</style>
@endpush

<aside class="desktop-sidebar">
    <div class="brand">
        <div><i class="bi bi-qr-code-scan me-1"></i> Indokom Group</div>
        <small>Absensi &amp; Aktivitas Karyawan</small>
    </div>

    @foreach ($bagianSb as $judul => $items)
        <div class="nav-section-title">{{ $judul }}</div>
        @foreach ($items as $item)
            @php $aktifSb = ! empty($item['aktif']) && request()->routeIs(...$item['aktif']); @endphp
            <a href="{{ $item['route'] ? route($item['route']) : '#' }}" class="{{ $aktifSb ? 'active' : '' }}">
                <i class="bi {{ $item['icon'] }}"></i> {{ $item['label'] }}
            </a>
        @endforeach
    @endforeach

    @if (! empty($menuAplikasi))
        <div class="nav-section-title">Menu Aplikasi</div>
        @foreach ($menuAplikasi as $item)
            <a href="{{ $item['link'] }}">
                <span class="menu-icon-small" style="background: {{ $item['warna'] }};">
                    <i class="bi {{ $item['icon'] }}"></i>
                </span>
                {{ $item['label'] }}
            </a>
        @endforeach
    @endif

    <a href="{{ route('profil.karyawan') }}" class="sidebar-profile">
        <div class="avatar">{{ $inisialSb ?: '?' }}</div>
        <div>
            <div class="nama">{{ $namaSb }}</div>
            <div class="peran">{{ $jabatanSb }}</div>
        </div>
    </a>
    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
        @csrf
        <button type="submit"><i class="bi bi-box-arrow-right"></i> Keluar</button>
    </form>
</aside>