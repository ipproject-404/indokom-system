<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard HRD - Indokom System</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/x-icon">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['"Bricolage Grotesque"', 'sans-serif'],
                        sans: ['"Instrument Sans"', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        ink: '#0F172A',
                        paper: '#F1F5F9',
                        line: '#E2E8F0',
                        muted: '#64748B',
                        brand: { DEFAULT: '#2563EB', dark: '#1D4ED8', soft: '#DBEAFE' },
                        danger: { DEFAULT: '#DC2626', soft: '#FEE2E2' },
                        success: { DEFAULT: '#16A34A', soft: '#DCFCE7' },
                    },
                },
            },
        };
    </script>
    <style>
        body { font-family: 'Instrument Sans', system-ui, sans-serif; }
        #sidebar::-webkit-scrollbar { width: 6px; }
        #sidebar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        @keyframes fill-bar { from { width: 0; } }
        .bar-fill { animation: fill-bar 1s cubic-bezier(.2, .7, .2, 1); }
        @media (prefers-reduced-motion: reduce) { .bar-fill { animation: none; } }

        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fade-in-up .5s ease-out backwards; }
        .fade-up-1 { animation-delay: .05s; }
        .fade-up-2 { animation-delay: .10s; }
        .fade-up-3 { animation-delay: .15s; }
        .fade-up-4 { animation-delay: .20s; }

        .tabular { font-variant-numeric: tabular-nums; }
        :focus-visible { outline: 2px solid #2563EB; outline-offset: 2px; }

        .stat-card { transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
        .stat-card:hover { transform: translateY(-2px); }
    </style>
</head>

<body class="bg-paper text-ink antialiased">

<div class="flex min-h-screen">

    @php
        $userKaryawan = Auth::user()->karyawan ?? null;
        $namaTampil = $userKaryawan->nama_lengkap ?? (Auth::user()->name ?? '-');
        $jabatanTampil = optional($userKaryawan->jabatan ?? null)->nama_jabatan ?? '-';
        $departemenTampil = optional($userKaryawan->departemen ?? null)->nama_departemen ?? '-';

        $aktif = (int) ($totalKaryawanAktif ?? 0);
        $hadir = (int) ($hadirHariIni ?? 0);
        $belumHadir = max($aktif - $hadir, 0);
        $persen = $aktif > 0 ? min(100, (int) round($hadir / $aktif * 100)) : 0;

        // Hitung "belum pulang" dari data presensi yang ada
        $belumPulang = 0;
        if (isset($presensis)) {
            foreach ($presensis as $p) {
                if (empty($p->jam_pulang) && $p->metode_presensi !== 'MANUAL_HRD') $belumPulang++;
            }
        }

        $jam = now()->hour;
        if ($jam < 11) { $sapaan = 'Selamat pagi'; $ikonSapaan = 'bi-sunrise-fill'; }
        elseif ($jam < 15) { $sapaan = 'Selamat siang'; $ikonSapaan = 'bi-sun-fill'; }
        elseif ($jam < 18) { $sapaan = 'Selamat sore'; $ikonSapaan = 'bi-sunset-fill'; }
        else { $sapaan = 'Selamat malam'; $ikonSapaan = 'bi-moon-stars-fill'; }
    @endphp
    @include('partials.sidebar-hrd')

    <main class="grow flex flex-col min-w-0">

        <!-- HEADER -->
        <header class="bg-white/90 backdrop-blur border-b border-line px-4 sm:px-8 py-4 flex justify-between items-center sticky top-0 z-20">
            <div>
                <h1 class="font-display font-bold text-xl text-ink">Dashboard HRD</h1>
                <p class="text-sm text-muted">Pantau kehadiran karyawan hari ini</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden md:flex items-center gap-2 text-sm text-ink bg-paper border border-line px-3 py-1.5 rounded-lg">
                    <i class="bi bi-calendar3 text-brand"></i>
                    <span class="font-semibold">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
                <div class="w-10 h-10 rounded-full bg-brand text-white flex items-center justify-center font-bold text-sm shadow-sm" title="{{ $namaTampil }}">
                    {{ strtoupper(substr($namaTampil, 0, 2)) }}
                </div>
            </div>
        </header>

        <div class="px-4 sm:px-8 py-6 sm:py-8 grow max-w-6xl w-full mx-auto">

            <!-- SAPAAN HERO (biru) -->
            <section class="mb-6 fade-up fade-up-1">
                <div class="bg-gradient-to-br from-brand-dark via-brand to-blue-500 rounded-2xl p-6 sm:p-8 text-white relative overflow-hidden shadow-lg shadow-brand/20">
                    <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-white/10 pointer-events-none"></div>
                    <div class="absolute -right-4 -bottom-16 w-40 h-40 rounded-full bg-white/5 pointer-events-none"></div>
                    <div class="absolute top-10 right-32 w-24 h-24 rounded-full bg-white/5 pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                        <div class="min-w-0">
                            <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm text-white px-3 py-1 rounded-full text-xs font-semibold mb-3 border border-white/20">
                                <i class="bi {{ $ikonSapaan }}"></i>
                                <span>{{ $sapaan }}</span>
                            </div>
                            <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-white leading-tight">
                                {{ $namaTampil }}
                            </h2>
                            <p class="text-sm text-blue-100 mt-2">
                                <i class="bi bi-briefcase mr-1 opacity-80"></i>{{ $jabatanTampil }}
                                <span class="mx-1.5 opacity-60">•</span>
                                {{ $departemenTampil }}
                            </p>
                        </div>

                        <div class="shrink-0 flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-xs uppercase tracking-wider text-blue-100 font-semibold mb-1">Kehadiran</p>
                                <p class="font-display font-extrabold text-4xl text-white tabular leading-none">
                                    {{ $persen }}<span class="text-2xl">%</span>
                                </p>
                                <p class="text-xs text-blue-100 mt-1 tabular">{{ $hadir }} dari {{ $aktif }}</p>
                            </div>
                            <div class="w-20 h-20 rounded-full flex items-center justify-center" 
                                 style="background: conic-gradient(#FFFFFF {{ $persen * 3.6 }}deg, rgba(255,255,255,0.2) 0);">
                                <div class="w-14 h-14 rounded-full bg-brand-dark flex items-center justify-center shadow-inner">
                                    <i class="bi bi-graph-up-arrow text-white text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- RINGKASAN KEHADIRAN (4 KARTU) -->
            <section class="mb-6 fade-up fade-up-2">
                <h3 class="text-xs uppercase tracking-wider font-bold text-muted mb-3 flex items-center gap-2">
                    <i class="bi bi-bar-chart-fill text-brand"></i> Ringkasan hari ini
                </h3>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

                    <div class="stat-card bg-white border border-line rounded-xl p-5 hover:border-brand hover:shadow-md">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 rounded-lg bg-paper text-ink flex items-center justify-center">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <span class="text-[10px] font-extrabold bg-paper text-muted px-2 py-0.5 rounded uppercase tracking-wider border border-line">Total</span>
                        </div>
                        <p class="text-xs text-muted font-medium">Karyawan aktif</p>
                        <p class="font-display font-extrabold text-3xl text-ink mt-1 tabular">{{ $aktif }}</p>
                        <p class="text-xs text-muted mt-1.5 flex items-center gap-1">
                            <i class="bi bi-check-circle-fill text-brand"></i> Terdaftar
                        </p>
                    </div>

                    <div class="stat-card bg-white border border-line rounded-xl p-5 hover:border-brand hover:shadow-md">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 rounded-lg bg-brand-soft text-brand-dark flex items-center justify-center">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                            <span class="text-[10px] font-extrabold bg-brand-soft text-brand-dark px-2 py-0.5 rounded uppercase tracking-wider">Hari ini</span>
                        </div>
                        <p class="text-xs text-muted font-medium">Sudah hadir</p>
                        <p class="font-display font-extrabold text-3xl text-brand mt-1 tabular">{{ $hadir }}</p>
                        <p class="text-xs text-muted mt-1.5 flex items-center gap-1">
                            <i class="bi bi-clock-history text-brand"></i> Check-in
                        </p>
                    </div>

                    <div class="stat-card bg-white border border-line rounded-xl p-5 hover:border-danger hover:shadow-md">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 rounded-lg bg-danger-soft text-danger flex items-center justify-center">
                                <i class="bi bi-person-dash-fill"></i>
                            </div>
                            <span class="text-[10px] font-extrabold bg-danger-soft text-danger px-2 py-0.5 rounded uppercase tracking-wider">Belum</span>
                        </div>
                        <p class="text-xs text-muted font-medium">Belum hadir</p>
                        <p class="font-display font-extrabold text-3xl text-ink mt-1 tabular">{{ $belumHadir }}</p>
                        <p class="text-xs text-muted mt-1.5 flex items-center gap-1">
                            <i class="bi bi-hourglass-split text-danger"></i> Belum check-in
                        </p>
                    </div>

                    <div class="stat-card bg-white border border-line rounded-xl p-5 hover:border-brand hover:shadow-md">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 rounded-lg bg-paper text-ink flex items-center justify-center">
                                <i class="bi bi-box-arrow-right"></i>
                            </div>
                            <span class="text-[10px] font-extrabold bg-paper text-muted px-2 py-0.5 rounded uppercase tracking-wider border border-line">Aktif</span>
                        </div>
                        <p class="text-xs text-muted font-medium">Belum absen pulang</p>
                        <p class="font-display font-extrabold text-3xl text-ink mt-1 tabular">{{ $belumPulang }}</p>
                        <p class="text-xs text-muted mt-1.5 flex items-center gap-1">
                            <i class="bi bi-hourglass text-muted"></i> Masih bekerja
                        </p>
                    </div>
                </div>
            </section>

            <!-- PROGRESS BAR -->
            <section class="mb-6 fade-up fade-up-3">
                <div class="bg-white border border-line rounded-xl p-5 sm:p-6">
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <p class="font-display font-bold text-ink">Tingkat kehadiran hari ini</p>
                            <p class="text-xs text-muted mt-0.5">Perbandingan hadir dengan total karyawan aktif</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-display font-extrabold text-3xl text-brand tabular leading-none">{{ $persen }}<span class="text-lg">%</span></p>
                        </div>
                    </div>
                    <div class="h-3 rounded-full bg-brand-soft overflow-hidden" role="progressbar" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="bar-fill h-full rounded-full bg-gradient-to-r from-brand-dark to-brand" style="width: {{ $persen }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-muted mt-2 tabular">
                        <span>{{ $hadir }} hadir</span>
                        <span>{{ $belumHadir }} belum</span>
                    </div>
                </div>
            </section>

            <!-- TABEL KEHADIRAN -->
            <section class="bg-white border border-line rounded-xl overflow-hidden fade-up fade-up-4">
                <div class="px-5 sm:px-6 py-5 border-b border-line flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-brand-soft text-brand-dark flex items-center justify-center">
                            <i class="bi bi-eye-fill"></i>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-lg text-ink">Kehadiran hari ini</h3>
                            <p class="text-sm text-muted">
                                <span id="jumlah-data">{{ isset($presensis) ? count($presensis) : 0 }}</span> karyawan tercatat
                            </p>
                        </div>
                    </div>
                    <label class="relative block sm:w-64">
                        <span class="sr-only">Cari karyawan</span>
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-muted text-sm"></i>
                        <input id="cari" type="search" placeholder="Cari nama karyawan"
                               class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-line bg-paper placeholder:text-muted focus:bg-white focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
                    </label>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[760px]">
                        <thead class="bg-paper text-muted text-xs border-b border-line">
                            <tr>
                                <th class="px-5 sm:px-6 py-3 font-semibold">Karyawan</th>
                                <th class="px-5 sm:px-6 py-3 font-semibold">Jam / Status</th>
                                <th class="px-5 sm:px-6 py-3 font-semibold">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-presensi" class="text-sm divide-y divide-line">
                            @if(isset($presensis) && count($presensis) > 0)
                                @foreach($presensis as $prs)
                                @php
                                    // Cek apakah ini status manual
                                    $isManual = ($prs->metode_presensi === 'MANUAL_HRD');
                                    $statusManual = $prs->status_kehadiran ?? null;
                                    
                                    // Config per status
                                    $statusConfig = [
                                        'sakit'          => ['label' => 'Sakit',      'color' => 'text-blue-700',   'bg' => 'bg-blue-100',   'border' => 'border-blue-200',   'icon' => 'bi-bandaid-fill'],
                                        'izin'           => ['label' => 'Izin',       'color' => 'text-yellow-700', 'bg' => 'bg-yellow-100', 'border' => 'border-yellow-200', 'icon' => 'bi-envelope-fill'],
                                        'cuti'           => ['label' => 'Cuti',       'color' => 'text-purple-700', 'bg' => 'bg-purple-100', 'border' => 'border-purple-200', 'icon' => 'bi-airplane-fill'],
                                        'alpa'           => ['label' => 'Alpa',       'color' => 'text-red-700',    'bg' => 'bg-red-100',    'border' => 'border-red-200',    'icon' => 'bi-x-octagon-fill'],
                                        'dinas_luar'     => ['label' => 'Dinas Luar', 'color' => 'text-orange-700', 'bg' => 'bg-orange-100', 'border' => 'border-orange-200', 'icon' => 'bi-briefcase-fill'],
                                        'libur_nasional' => ['label' => 'Libur',      'color' => 'text-gray-700',   'bg' => 'bg-gray-100',   'border' => 'border-gray-200',   'icon' => 'bi-calendar-x-fill'],
                                    ];
                                    $cfg = $statusManual ? ($statusConfig[$statusManual] ?? null) : null;
                                    
                                    $nama = $prs->karyawan->nama_lengkap ?? '-';
                                @endphp
                                <tr class="hover:bg-paper/70 align-top" data-nama="{{ strtolower($nama) }}">
                                    <!-- KOLOM 1: KARYAWAN -->
                                    <td class="px-5 sm:px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 shrink-0 rounded-full {{ $isManual && $cfg ? $cfg['bg'] : 'bg-brand-soft' }} {{ $isManual && $cfg ? $cfg['color'] : 'text-brand-dark' }} text-xs font-bold flex items-center justify-center ring-2 ring-white shadow-sm">
                                                {{ strtoupper(substr($prs->karyawan->nama_lengkap ?? 'X', 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-semibold text-ink">{{ $nama }}</div>
                                                <div class="text-xs text-muted">{{ $prs->karyawan->departemen->nama_departemen ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- KOLOM 2: JAM / STATUS MANUAL -->
                                    <td class="px-5 sm:px-6 py-4">
                                        @if($isManual && $cfg)
                                            {{-- Badge status manual --}}
                                            <span class="inline-flex items-center gap-1.5 {{ $cfg['bg'] }} {{ $cfg['color'] }} px-3 py-1.5 rounded-lg text-xs font-bold border {{ $cfg['border'] }}">
                                                <i class="bi {{ $cfg['icon'] }}"></i>
                                                {{ $cfg['label'] }}
                                            </span>
                                        @elseif($prs->jam_masuk)
                                            {{-- Jam masuk pulang biasa --}}
                                            <div class="flex items-center gap-2 tabular">
                                                <span class="inline-flex items-center gap-1.5 bg-brand-soft text-brand-dark px-2.5 py-1 rounded-md text-xs font-semibold">
                                                    <i class="bi bi-box-arrow-in-right"></i>
                                                    {{ \Carbon\Carbon::parse($prs->jam_masuk)->format('H:i') }}
                                                </span>
                                                <span class="text-muted">&ndash;</span>
                                                @if($prs->jam_pulang)
                                                    <span class="inline-flex items-center gap-1.5 bg-danger-soft text-danger px-2.5 py-1 rounded-md text-xs font-semibold">
                                                        <i class="bi bi-box-arrow-left"></i>
                                                        {{ \Carbon\Carbon::parse($prs->jam_pulang)->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 bg-paper text-muted px-2.5 py-1 rounded-md text-xs font-medium border border-line">
                                                        Belum pulang
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted italic text-xs">-</span>
                                        @endif
                                    </td>

                                    <!-- KOLOM 3: LOKASI / KETERANGAN -->
                                    <td class="px-5 sm:px-6 py-4">
                                        @if($isManual)
                                            {{-- Keterangan manual dari HRD --}}
                                            @if($prs->keterangan_manual)
                                                <div class="flex items-start gap-2 text-xs text-muted">
                                                    <i class="bi bi-chat-left-text text-brand mt-0.5 shrink-0"></i>
                                                    <span>{{ $prs->keterangan_manual }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted italic text-xs">
                                                    <i class="bi bi-info-circle mr-1"></i> Diinput oleh HRD
                                                </span>
                                            @endif
                                        @else
                                            {{-- Lokasi absen QR biasa --}}
                                            @php
                                                $titik = [
                                                    [
                                                        'label' => 'Masuk', 'ikon' => 'bi-box-arrow-in-right', 'warna' => 'text-brand',
                                                        'lat' => $prs->latitude_masuk ?? null, 'lng' => $prs->longitude_masuk ?? null,
                                                        'alamat' => $prs->alamat_masuk ?? null, 'jalan' => $prs->nama_jalan_masuk ?? null,
                                                        'jarak' => $prs->jarak_masuk_meter ?? null, 'status' => $prs->status_radius_masuk ?? null,
                                                        'kosong' => 'Tidak ada lokasi',
                                                    ],
                                                    [
                                                        'label' => 'Pulang', 'ikon' => 'bi-box-arrow-left', 'warna' => 'text-danger',
                                                        'lat' => $prs->latitude_pulang ?? null, 'lng' => $prs->longitude_pulang ?? null,
                                                        'alamat' => $prs->alamat_pulang ?? null, 'jalan' => $prs->nama_jalan_pulang ?? null,
                                                        'jarak' => $prs->jarak_pulang_meter ?? null, 'status' => $prs->status_radius_pulang ?? null,
                                                        'kosong' => 'Belum absen pulang',
                                                    ],
                                                ];
                                            @endphp
                                            <div class="space-y-2.5">
                                                @foreach($titik as $t)
                                                    <div class="flex items-center gap-2 text-xs">
                                                        <span class="w-14 shrink-0 font-semibold {{ $t['warna'] }}">{{ $t['label'] }}</span>

                                                        @if($t['lat'] && $t['lng'])
                                                            @php
                                                                $teks = trim(($t['alamat'] ?? '') . ' ' . ($t['jalan'] ?? ''));
                                                                $teks = $teks !== '' ? $teks : $t['lat'] . ', ' . $t['lng'];
                                                            @endphp
                                                            <a href="https://maps.google.com/?q={{ $t['lat'] }},{{ $t['lng'] }}" target="_blank" rel="noopener"
                                                               title="{{ $teks }}"
                                                               class="text-brand-dark font-medium hover:underline truncate max-w-[200px]">
                                                                {{ $teks }}
                                                            </a>

                                                            @if($t['jarak'])
                                                                <span class="text-muted shrink-0 tabular">{{ $t['jarak'] }} m</span>
                                                            @endif

                                                            @if($t['status'] == 'dalam_radius')
                                                                <span class="inline-flex items-center gap-1 shrink-0 text-brand-dark font-semibold">
                                                                    <span class="w-1.5 h-1.5 rounded-full bg-brand"></span> Dalam radius
                                                                </span>
                                                            @elseif($t['status'])
                                                                <span class="inline-flex items-center gap-1 shrink-0 text-danger font-semibold">
                                                                    <span class="w-1.5 h-1.5 rounded-full bg-danger"></span> Luar radius
                                                                </span>
                                                            @else
                                                                <span class="shrink-0 text-muted">Radius tidak diketahui</span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted italic">{{ $t['kosong'] }}</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach

                                <tr id="baris-kosong-cari" class="hidden">
                                    <td colspan="3" class="px-6 py-10 text-center text-muted">
                                        <i class="bi bi-search text-2xl block mb-2 opacity-50"></i>
                                        Tidak ada karyawan dengan nama tersebut.
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="3" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center text-muted">
                                            <div class="w-16 h-16 rounded-full bg-brand-soft flex items-center justify-center mb-4">
                                                <i class="bi bi-person-check text-3xl text-brand"></i>
                                            </div>
                                            <p class="font-display font-bold text-lg text-ink">Belum ada kehadiran hari ini</p>
                                            <p class="text-sm mt-1 max-w-xs">Data akan muncul saat karyawan check-in atau HRD input status manual.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>

        </div>
    </main>
</div>

<script>
    (function () {
        const input = document.getElementById('cari');
        const rows = document.querySelectorAll('#tabel-presensi tr[data-nama]');
        const kosong = document.getElementById('baris-kosong-cari');
        const jumlah = document.getElementById('jumlah-data');
        if (!input || !rows.length) return;

        input.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            let tampil = 0;
            rows.forEach(function (row) {
                const cocok = row.dataset.nama.includes(q);
                row.classList.toggle('hidden', !cocok);
                if (cocok) tampil++;
            });
            if (jumlah) jumlah.textContent = tampil;
            if (kosong) kosong.classList.toggle('hidden', tampil > 0);
        });
    })();
</script>

</body>
</html>