@php
    // Menu bar bawah (HP) untuk halaman karyawan -- SATU-SATUNYA tempat
    // yang perlu diubah kalau menu ditambah/diganti. 'route' null = belum
    // ada halamannya (link '#'). 'aktif' = daftar nama route yang membuat
    // menu ini tampil terpilih.
    $menuBawah = [
        ['label' => 'Beranda',   'icon' => 'bi-house-fill',      'route' => 'dashboard.karyawan', 'aktif' => ['dashboard.karyawan']],
        ['label' => 'Aktivitas', 'icon' => 'bi-check2-square',   'route' => null,                 'aktif' => []],
        ['label' => 'Absensi',   'icon' => 'bi-calendar-check',  'route' => 'absensi.karyawan',   'aktif' => ['absensi.karyawan']],
        ['label' => 'Pelatihan', 'icon' => 'bi-mortarboard',     'route' => null,                 'aktif' => []],
        ['label' => 'Profil',    'icon' => 'bi-person-circle',   'route' => 'profil.karyawan',    'aktif' => ['profil.karyawan']],
    ];
@endphp

<style>
    .bottom-nav {
        position: fixed;
        bottom: 0; left: 50%;
        transform: translateX(-50%);
        width: 100%; max-width: 480px;
        background: white;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
        border-top: 1px solid #e2e8f0;
        padding: 0.5rem 0;
        z-index: 20;
    }
    .bottom-nav a {
        display: flex; flex-direction: column; align-items: center;
        width: 100%;
        font-size: 0.7rem; color: #94a3b8; text-decoration: none;
    }
    .bottom-nav a.active, .bottom-nav a:hover { color: #0d6efd; }
    .bottom-nav i { font-size: 1.3rem; margin-bottom: 2px; }
</style>

<nav class="bottom-nav">
    <div class="row text-center gx-0">
        @foreach ($menuBawah as $item)
            @php $menuAktif = ! empty($item['aktif']) && request()->routeIs(...$item['aktif']); @endphp
            <div class="col">
                <a href="{{ $item['route'] ? route($item['route']) : '#' }}" class="{{ $menuAktif ? 'active' : '' }}">
                    <i class="bi {{ $item['icon'] }}"></i> {{ $item['label'] }}
                </a>
            </div>
        @endforeach
    </div>
</nav>