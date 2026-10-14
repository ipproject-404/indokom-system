<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matrix Kehadiran Karyawan - HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .sticky-col-checkbox { position: sticky; left: 0; background-color: #f8fafc; z-index: 12; width: 40px; min-width: 40px; max-width: 40px; }
        .sticky-col-name { position: sticky; left: 40px; background-color: #f8fafc; z-index: 10; box-shadow: 3px 0 6px rgba(0,0,0,0.06); }
        .matrix-scroll::-webkit-scrollbar { height: 12px; width: 12px; }
        .matrix-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 8px; border: 1px solid #e2e8f0; }
        .matrix-scroll::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 8px; border: 2px solid #f1f5f9; }
        .matrix-scroll::-webkit-scrollbar-thumb:hover { background: #64748b; }

        .table-matrix th, .table-matrix td { border: 1px solid #cbd5e1; }

        #tooltipPortal { position: fixed; z-index: 9999; pointer-events: none; transition: opacity .15s ease; }
        #tooltipPortal.hidden { opacity: 0; visibility: hidden; }
        #tooltipPortal.visible { opacity: 1; visibility: visible; }

        .cell-presensi { cursor: pointer; }

        .filter-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right .5rem center;
            background-size: 1rem;
        }

        .search-loading {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            width: 14px; height: 14px; border: 2px solid #3B82F6; border-top-color: transparent;
            border-radius: 50%; animation: spin 0.6s linear infinite;
        }
        @keyframes spin { to { transform: translateY(-50%) rotate(360deg); } }

        .status-cell { cursor: pointer; transition: all .15s ease; }
        .status-cell:hover { transform: scale(1.05); }
        .empty-cell-btn { transition: all .15s ease; }
        .empty-cell-btn:hover { background: rgba(59,130,246,0.1); }
    </style>
</head>
<body class="bg-gray-50">

<div class="flex min-h-screen bg-gray-50">

   @include('partials.sidebar-hrd')

    <main class="grow flex flex-col min-w-0">
        <!-- NAVBAR -->
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-20 shadow-sm">
            <div class="flex items-center">
                <a href="{{ route('dashboard.hrd') }}" class="text-gray-500 hover:bg-gray-100 p-2 rounded-lg mr-3 transition-colors">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="font-bold text-xl text-gray-800">Matrix Kehadiran Karyawan</h1>
                    <p class="text-xs text-gray-500">Rekapitulasi absensi bulanan / periodik</p>
                </div>
            </div>
            <div class="text-gray-500 text-sm flex items-center bg-blue-50 px-4 py-2 rounded-lg border border-blue-100">
                <i class="bi bi-calendar-range mr-2 text-blue-600"></i>
                <span class="font-bold text-blue-800">
                    {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                </span>
            </div>
        </nav>

        <div class="p-6 grow overflow-y-auto w-full max-w-[1400px] mx-auto space-y-4">
            
            <!-- STATISTIK -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Total Kehadiran</p>
                        <h3 class="font-bold text-3xl text-gray-800">{{ $totalKehadiran }} <span class="text-sm font-medium text-gray-400">Data</span></h3>
                    </div>
                    <div class="bg-blue-50 text-blue-600 rounded-full w-14 h-14 flex items-center justify-center text-3xl">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Belum Absen Pulang</p>
                        <h3 class="font-bold text-3xl text-amber-600">{{ $belumPulang }} <span class="text-sm font-medium text-gray-400">Orang</span></h3>
                    </div>
                    <div class="bg-amber-50 text-amber-600 rounded-full w-14 h-14 flex items-center justify-center text-3xl">
                        <i class="bi bi-box-arrow-right"></i>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Rata-rata Harian</p>
                        <h3 class="font-bold text-3xl text-emerald-600">~{{ $rataHarian }} <span class="text-sm font-medium text-gray-400">Hadir/Hari</span></h3>
                    </div>
                    <div class="bg-emerald-50 text-emerald-600 rounded-full w-14 h-14 flex items-center justify-center text-3xl">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                </div>
            </div>

            <!-- FILTER BAR -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <form action="{{ route('kehadiran.log') }}" method="GET" id="filterForm">
                    <div class="flex flex-wrap items-end gap-3 mb-3">
                        <div class="w-36">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Dari</label>
                            <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" 
                                   onchange="submitDateFilter()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-600 outline-none cursor-pointer">
                        </div>
                        <div class="w-36">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Sampai</label>
                            <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" 
                                   onchange="submitDateFilter()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-600 outline-none cursor-pointer">
                        </div>
                        <div class="flex gap-1">
                            <button type="button" id="btn-quick-0" onclick="setQuickDate(0, 'btn-quick-0')" class="quick-btn px-3 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg border border-gray-300 transition-colors hover:bg-gray-200">Hari Ini</button>
                            <button type="button" id="btn-quick-6" onclick="setQuickDate(6, 'btn-quick-6')" class="quick-btn px-3 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg border border-gray-300 transition-colors hover:bg-gray-200">7 Hari</button>
                            <button type="button" id="btn-quick-30" onclick="setQuickDate(30, 'btn-quick-30')" class="quick-btn px-3 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg border border-gray-300 transition-colors hover:bg-gray-200">31 Hari</button>
                        </div>
                        <div class="grow"></div>

                        <button type="button" onclick="openMultiHariModal()" 
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-indigo-700 transition-colors shadow-sm flex items-center gap-1.5">
                            <i class="bi bi-calendar-plus-fill"></i> Izin/Cuti Multi-Hari
                        </button>

                        <button type="button" onclick="exportTerpilihExcel()" 
                                class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-1.5">
                            <i class="bi bi-file-earmark-excel"></i> Export
                        </button>

                        <a href="{{ route('kehadiran.log') }}" 
                           class="bg-gray-100 text-gray-700 border border-gray-300 px-3 py-2 rounded-lg text-xs font-bold hover:bg-gray-200 transition-colors flex items-center gap-1.5">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </form>

                <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100">
                    <div class="relative flex-1 min-w-[240px]">
                        <i class="bi bi-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                        <input type="text" id="searchInput" name="search" form="filterForm"
                               value="{{ $search }}" autocomplete="off"
                               placeholder="Cari nama atau NIK... (live)"
                               class="w-full pl-9 pr-16 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-600 outline-none">
                        <span id="searchBadge" class="hidden absolute right-2 top-1.5 text-[9px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">⚡ Live</span>
                        <span id="searchLoading" class="hidden search-loading"></span>
                    </div>

                    <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>

                    <select id="liveFilterPerusahaan" onchange="applyAllFilters()"
                            class="filter-select px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none cursor-pointer max-w-[160px]">
                        <option value="">🏢 Semua PT</option>
                        @foreach($allPerusahaans as $pt)
                            <option value="{{ $pt->nama_perusahaan }}">{{ $pt->nama_perusahaan }}</option>
                        @endforeach
                    </select>

                    <select id="liveFilterDivisi" onchange="applyAllFilters()"
                            class="filter-select px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none cursor-pointer max-w-[140px]">
                        <option value="">📁 Semua Divisi</option>
                        @foreach($allDivisis as $dv)
                            <option value="{{ $dv->nama_divisi }}">{{ $dv->nama_divisi }}</option>
                        @endforeach
                    </select>

                    <select id="liveFilterDepartemen" onchange="applyAllFilters()"
                            class="filter-select px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none cursor-pointer max-w-[160px]">
                        <option value="">🏛️ Semua Dept</option>
                        @foreach($allDepartemens as $dp)
                            <option value="{{ $dp->nama_departemen }}">{{ $dp->nama_departemen }}</option>
                        @endforeach
                    </select>

                    <select id="liveFilterJabatan" onchange="applyAllFilters()"
                            class="filter-select px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none cursor-pointer max-w-[160px]">
                        <option value="">💼 Semua Jabatan</option>
                        @foreach($allJabatans as $jb)
                            <option value="{{ $jb->nama_jabatan }}">{{ $jb->nama_jabatan }}</option>
                        @endforeach
                    </select>

                    <button type="button" onclick="resetLiveFilter()" 
                            class="text-[10px] font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-2.5 py-2 rounded-lg transition-colors border border-rose-200 flex items-center gap-1">
                        <i class="bi bi-x-circle"></i> Clear
                    </button>
                </div>

                <p id="filterInfoBar" class="text-[11px] text-gray-500 mt-2 hidden">
                    <i class="bi bi-info-circle text-blue-500"></i>
                    Menampilkan <b id="matchCount" class="text-gray-700">0</b> dari <b id="totalCount" class="text-gray-700">0</b> karyawan di halaman ini
                </p>
            </div>

            <!-- KETERANGAN WARNA -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-1">Keterangan:</span>
                <div class="flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-lg">
                    <i class="bi bi-check-square-fill text-emerald-500 text-sm"></i>
                    <span class="text-[11px] font-bold text-emerald-700">Hadir Lengkap</span>
                </div>
                <div class="flex items-center gap-1.5 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-lg">
                    <i class="bi bi-exclamation-square-fill text-amber-500 text-sm"></i>
                    <span class="text-[11px] font-bold text-amber-700">Belum Pulang / Luar Radius</span>
                </div>
                <div class="flex items-center gap-1.5 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-lg">
                    <i class="bi bi-bandaid-fill text-blue-500 text-sm"></i>
                    <span class="text-[11px] font-bold text-blue-700">Sakit</span>
                </div>
                <div class="flex items-center gap-1.5 bg-yellow-50 border border-yellow-200 px-2.5 py-1 rounded-lg">
                    <i class="bi bi-envelope-fill text-yellow-600 text-sm"></i>
                    <span class="text-[11px] font-bold text-yellow-700">Izin</span>
                </div>
                <div class="flex items-center gap-1.5 bg-purple-50 border border-purple-200 px-2.5 py-1 rounded-lg">
                    <i class="bi bi-airplane-fill text-purple-500 text-sm"></i>
                    <span class="text-[11px] font-bold text-purple-700">Cuti</span>
                </div>
                <div class="flex items-center gap-1.5 bg-red-50 border border-red-200 px-2.5 py-1 rounded-lg">
                    <i class="bi bi-x-octagon-fill text-red-500 text-sm"></i>
                    <span class="text-[11px] font-bold text-red-700">Alpa</span>
                </div>
                <div class="flex items-center gap-1.5 bg-orange-50 border border-orange-200 px-2.5 py-1 rounded-lg">
                    <i class="bi bi-briefcase-fill text-orange-500 text-sm"></i>
                    <span class="text-[11px] font-bold text-orange-700">Dinas Luar</span>
                </div>
            </div>

            <!-- TABEL MATRIX -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-300 overflow-hidden relative">
                <div class="overflow-x-auto matrix-scroll">
                    <table class="w-full text-left border-collapse table-matrix" style="min-width: max-content;">
                        <thead class="bg-slate-700 text-white text-[11px] uppercase tracking-wider">
                            <tr>
                                <th class="sticky-col-checkbox bg-slate-800 text-white border-r border-slate-900 px-2 py-3 text-center">
                                    <input type="checkbox" id="selectAllKaryawan" onclick="toggleSelectAllKaryawan(this)" 
                                           class="w-4 h-4 rounded cursor-pointer accent-blue-600">
                                </th>
                                <th class="sticky-col-name bg-slate-800 text-white border-r-2 border-slate-900 px-5 py-3 font-bold w-64 min-w-[250px] shadow-[2px_0_5px_rgba(0,0,0,0.3)]">Karyawan</th>
                                
                                @foreach($dates as $date)
                                @php 
                                    $isWeekend = \Carbon\Carbon::parse($date)->isSunday(); 
                                @endphp
                                <th class="px-2 py-3 font-semibold text-center {{ $isWeekend ? 'bg-rose-600' : 'bg-slate-700' }} border-r border-slate-600 min-w-[50px]">
                                    <div class="opacity-90 mb-1 lowercase font-medium">{{ \Carbon\Carbon::parse($date)->translatedFormat('D') }}</div>
                                    <div class="text-base font-bold">{{ \Carbon\Carbon::parse($date)->format('d') }}</div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700" id="tabelMatrixBody">
                            @forelse($karyawans as $karyawan)
                            @php
                                $namaPT   = $karyawan->perusahaan->nama_perusahaan ?? '';
                                $namaDiv  = $karyawan->departemen->divisi->nama_divisi ?? '';
                                $namaDept = $karyawan->departemen->nama_departemen ?? '';
                                $namaJab  = $karyawan->jabatan->nama_jabatan ?? '';
                            @endphp
                            <tr class="hover:bg-blue-50/50 transition-colors group matrix-row"
                                data-nama="{{ strtolower($karyawan->nama_lengkap) }}"
                                data-nik="{{ strtolower($karyawan->nik_kerja) }}"
                                data-perusahaan="{{ $namaPT }}"
                                data-divisi="{{ $namaDiv }}"
                                data-departemen="{{ $namaDept }}"
                                data-jabatan="{{ $namaJab }}">
                                
                                <td class="sticky-col-checkbox group-hover:bg-slate-100 text-center px-2 py-3 border-r border-gray-300">
                                    <input type="checkbox" class="row-checkbox-karyawan w-4 h-4 rounded cursor-pointer accent-blue-600" 
                                           value="{{ $karyawan->id }}">
                                </td>

                                <td class="sticky-col-name group-hover:bg-slate-100 border-r-2 border-gray-300 px-5 py-3 shadow-[2px_0_5px_rgba(0,0,0,0.05)]">
                                    <div class="font-bold text-gray-900 truncate" title="{{ $karyawan->nama_lengkap }}">{{ $karyawan->nama_lengkap }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5 font-medium truncate">{{ $karyawan->nik_kerja }} &bull; {{ $karyawan->departemen->nama_departemen ?? '-' }}</div>
                                </td>

                                @foreach($dates as $date)
                                @php
                                    $prs = isset($presensiData[$karyawan->id]) && isset($presensiData[$karyawan->id][$date]) 
                                            ? $presensiData[$karyawan->id][$date] 
                                            : null;
                                    
                                    $isSunday = \Carbon\Carbon::parse($date)->isSunday();
                                    $statusManual = $prs->status_kehadiran ?? null;
                                    
                                    $cellBg = 'bg-gray-50/40';
                                    if ($prs && $prs->jam_masuk && !$statusManual) {
                                        if ($prs->jam_pulang) {
                                            $cellBg = 'bg-emerald-50/60 hover:bg-emerald-100';
                                        } else {
                                            $cellBg = 'bg-amber-50/60 hover:bg-amber-100';
                                        }
                                    }
                                @endphp

                                <td class="px-1 py-1 text-center border-r border-gray-300 relative cell-presensi {{ $cellBg }}"
                                    data-tanggal="{{ $date }}"
                                    data-karyawan-id="{{ $karyawan->id }}"
                                    data-nama-karyawan="{{ $karyawan->nama_lengkap }}"
                                    data-is-sunday="{{ $isSunday ? '1' : '0' }}"
                                    @if($prs)
                                        data-prs="1"
                                        data-jam-masuk="{{ $prs->jam_masuk }}"
                                        data-jam-pulang="{{ $prs->jam_pulang }}"
                                        data-radius-masuk="{{ $prs->status_radius_masuk }}"
                                        data-radius-pulang="{{ $prs->status_radius_pulang }}"
                                        data-alamat-masuk="{{ $prs->alamat_masuk ?? $prs->nama_jalan_masuk ?? '' }}"
                                        data-alamat-pulang="{{ $prs->alamat_pulang ?? $prs->nama_jalan_pulang ?? '' }}"
                                        data-jarak-masuk="{{ $prs->jarak_masuk_meter ?? '' }}"
                                        data-jarak-pulang="{{ $prs->jarak_pulang_meter ?? '' }}"
                                        data-metode="{{ $prs->metode_presensi }}"
                                        data-status-manual="{{ $statusManual ?? '' }}"
                                        data-keterangan-manual="{{ $prs->keterangan_manual ?? '' }}"
                                    @endif
                                >
                                    @if($statusManual)
                                        @php
                                            $ikonMap = [
                                                'sakit'          => ['ikon' => 'bi-bandaid-fill',     'warna' => 'text-blue-500',   'label' => 'Sakit'],
                                                'izin'           => ['ikon' => 'bi-envelope-fill',    'warna' => 'text-yellow-600', 'label' => 'Izin'],
                                                'cuti'           => ['ikon' => 'bi-airplane-fill',    'warna' => 'text-purple-500', 'label' => 'Cuti'],
                                                'alpa'           => ['ikon' => 'bi-x-octagon-fill',   'warna' => 'text-red-500',    'label' => 'Alpa'],
                                                'dinas_luar'     => ['ikon' => 'bi-briefcase-fill',   'warna' => 'text-orange-500', 'label' => 'Dinas'],
                                                'libur_nasional' => ['ikon' => 'bi-calendar-x-fill',  'warna' => 'text-gray-500',   'label' => 'Libur'],
                                            ];
                                            $cfg = $ikonMap[$statusManual] ?? ['ikon' => 'bi-question-circle', 'warna' => 'text-gray-400', 'label' => '-'];
                                        @endphp
                                        <div class="status-cell py-0.5" onclick="openStatusModal(this.closest('td'))">
                                            <i class="bi {{ $cfg['ikon'] }} {{ $cfg['warna'] }} text-2xl block"></i>
                                            <span class="text-[8px] font-bold uppercase {{ $cfg['warna'] }}">{{ $cfg['label'] }}</span>
                                        </div>
                                    @elseif($prs && $prs->jam_masuk)
                                        @if($prs->jam_pulang && $prs->status_radius_masuk == 'dalam_radius' && $prs->status_radius_pulang == 'dalam_radius')
                                            <i class="bi bi-check-square-fill text-emerald-500 text-3xl drop-shadow-sm"></i>
                                        @else
                                            <i class="bi bi-exclamation-square-fill text-amber-500 text-3xl drop-shadow-sm"></i>
                                        @endif
                                    @elseif($isSunday)
                                        <i class="bi bi-calendar-x text-gray-300 text-2xl" title="Hari Minggu"></i>
                                    @else
                                        <button type="button" onclick="openStatusModal(this.closest('td'))"
                                                class="empty-cell-btn w-full h-full flex items-center justify-center py-2 rounded"
                                                title="Klik untuk input status (sakit/izin/cuti/dll)">
                                            <i class="bi bi-plus-square-dotted text-gray-300 text-2xl"></i>
                                        </button>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ count($dates) + 2 }}" class="px-6 py-16 text-center">
                                    <h3 class="text-lg font-bold text-gray-800 mb-1">Tidak Ada Data Karyawan</h3>
                                </td>
                            </tr>
                            @endforelse

                            <tr id="emptyFilterRow" class="hidden">
                                <td colspan="{{ count($dates) + 2 }}" class="px-6 py-14 text-center">
                                    <div class="flex flex-col items-center text-gray-400">
                                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                            <i class="bi bi-funnel text-3xl"></i>
                                        </div>
                                        <p class="font-semibold text-gray-600">Tidak ada karyawan yang cocok</p>
                                        <p class="text-xs mt-1">Coba ubah filter / kata kunci pencarian</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                @if($karyawans->hasPages())
                <div class="px-6 py-4 border-t-2 border-gray-300 bg-gray-50 flex items-center justify-between">
                    <div class="text-sm font-semibold text-gray-600">
                        Menampilkan {{ $karyawans->firstItem() }} - {{ $karyawans->lastItem() }} dari {{ $karyawans->total() }} Karyawan
                    </div>
                    <div>
                        {{ $karyawans->withQueryString()->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </main>
</div>

<div id="tooltipPortal" class="hidden"></div>

@php
    // Mapping manual (arrow function tidak didukung di @json Blade)
    $karyawanSimple = $semuaKaryawan->map(function($k) {
        return [
            'id'   => $k->id,
            'nama' => $k->nama_lengkap,
            'nik'  => $k->nik_kerja,
            'dept' => $k->departemen->nama_departemen ?? '-',
        ];
    })->values()->toArray();
@endphp

<!-- ==========================================
     MODAL SINGLE HARI
========================================== -->
<div id="statusModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden max-h-[90vh] flex flex-col">
        
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="bi bi-pencil-square text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-white text-base">Input Status Kehadiran</h3>
                    <p class="text-blue-100 text-xs" id="modalSubtitle">-</p>
                </div>
            </div>
            <button onclick="closeStatusModal()" class="text-blue-100 hover:text-white hover:bg-white/20 rounded-lg p-1.5">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="p-5 space-y-4 overflow-y-auto grow">
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-3">
                <div class="flex items-center gap-2 mb-1">
                    <i class="bi bi-person-circle text-blue-600"></i>
                    <span class="text-sm font-bold text-gray-800" id="modalNamaKaryawan">-</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="bi bi-calendar3 text-blue-600"></i>
                    <span class="text-xs text-gray-600" id="modalTanggal">-</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wider">Pilih Status</label>
                <div class="grid grid-cols-2 gap-2" id="statusOptions">
                    <label class="cursor-pointer">
                        <input type="radio" name="status_kehadiran" value="sakit" class="peer sr-only">
                        <div class="border-2 border-gray-200 rounded-xl p-3 text-center hover:border-blue-400 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                            <i class="bi bi-bandaid-fill text-2xl text-blue-500 block mb-1"></i>
                            <span class="text-xs font-bold text-gray-700">Sakit</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status_kehadiran" value="izin" class="peer sr-only">
                        <div class="border-2 border-gray-200 rounded-xl p-3 text-center hover:border-yellow-400 peer-checked:border-yellow-500 peer-checked:bg-yellow-50">
                            <i class="bi bi-envelope-fill text-2xl text-yellow-500 block mb-1"></i>
                            <span class="text-xs font-bold text-gray-700">Izin</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status_kehadiran" value="cuti" class="peer sr-only">
                        <div class="border-2 border-gray-200 rounded-xl p-3 text-center hover:border-purple-400 peer-checked:border-purple-500 peer-checked:bg-purple-50">
                            <i class="bi bi-airplane-fill text-2xl text-purple-500 block mb-1"></i>
                            <span class="text-xs font-bold text-gray-700">Cuti</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status_kehadiran" value="alpa" class="peer sr-only">
                        <div class="border-2 border-gray-200 rounded-xl p-3 text-center hover:border-red-400 peer-checked:border-red-500 peer-checked:bg-red-50">
                            <i class="bi bi-x-octagon-fill text-2xl text-red-500 block mb-1"></i>
                            <span class="text-xs font-bold text-gray-700">Alpa</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status_kehadiran" value="dinas_luar" class="peer sr-only">
                        <div class="border-2 border-gray-200 rounded-xl p-3 text-center hover:border-orange-400 peer-checked:border-orange-500 peer-checked:bg-orange-50">
                            <i class="bi bi-briefcase-fill text-2xl text-orange-500 block mb-1"></i>
                            <span class="text-xs font-bold text-gray-700">Dinas Luar</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status_kehadiran" value="libur_nasional" class="peer sr-only">
                        <div class="border-2 border-gray-200 rounded-xl p-3 text-center hover:border-gray-400 peer-checked:border-gray-500 peer-checked:bg-gray-100">
                            <i class="bi bi-calendar-x-fill text-2xl text-gray-500 block mb-1"></i>
                            <span class="text-xs font-bold text-gray-700">Libur</span>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wider">Keterangan <span class="text-gray-400 font-normal">(opsional)</span></label>
                <textarea id="modalKeterangan" rows="2" maxlength="500"
                          placeholder="Contoh: Sakit demam, ada surat dokter"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"></textarea>
            </div>
        </div>

        <div class="bg-gray-50 px-5 py-3 flex items-center justify-between border-t border-gray-200 shrink-0">
            <button type="button" id="btnHapusStatus" onclick="deleteStatusManual()" 
                    class="hidden text-xs font-bold text-red-600 hover:bg-red-50 px-3 py-2 rounded-lg flex items-center gap-1">
                <i class="bi bi-trash"></i> Hapus Status
            </button>
            <div class="flex gap-2 ml-auto">
                <button type="button" onclick="closeStatusModal()" 
                        class="text-xs font-bold text-gray-600 hover:bg-gray-200 px-4 py-2 rounded-lg">
                    Batal
                </button>
                <button type="button" onclick="saveStatusManual()" id="btnSaveStatus"
                        class="text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg flex items-center gap-1.5">
                    <i class="bi bi-check-lg"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================
     MODAL MULTI-HARI
========================================== -->
<div id="multiHariModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden max-h-[90vh] flex flex-col">
        
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="bi bi-calendar-plus-fill text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-white text-base">Input Izin/Cuti Multi-Hari</h3>
                    <p class="text-indigo-100 text-xs">Otomatis terisi di semua tanggal</p>
                </div>
            </div>
            <button onclick="closeMultiHariModal()" class="text-indigo-100 hover:text-white hover:bg-white/20 rounded-lg p-1.5">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="p-5 space-y-4 overflow-y-auto grow">

            <div class="relative">
                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wider">Karyawan</label>
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                    <input type="text" 
                           id="multiKaryawanSearch" 
                           autocomplete="off"
                           placeholder="Ketik nama atau NIK karyawan..."
                           oninput="filterMultiKaryawan()"
                           onfocus="showMultiKaryawanDropdown()"
                           class="w-full pl-9 pr-10 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    <i class="bi bi-chevron-down absolute right-3 top-2.5 text-gray-400 text-sm pointer-events-none"></i>
                </div>
                
                <input type="hidden" id="multiKaryawanId" value="">

                <div id="multiKaryawanDropdown" 
                     class="hidden absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wider">Dari Tanggal</label>
                    <input type="date" id="multiTanggalMulai" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wider">Sampai Tanggal</label>
                    <input type="date" id="multiTanggalSelesai" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>
            <p class="text-[11px] text-amber-600 flex items-center gap-1">
                <i class="bi bi-info-circle"></i> Maksimal 30 hari. Tanggal yang sudah absen QR akan dilewati.
            </p>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wider">Status</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="multi_status" value="sakit" class="peer sr-only">
                        <div class="border-2 border-gray-200 rounded-lg p-2 text-center hover:border-blue-400 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                            <i class="bi bi-bandaid-fill text-xl text-blue-500 block mb-0.5"></i>
                            <span class="text-[10px] font-bold text-gray-700">Sakit</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="multi_status" value="izin" class="peer sr-only">
                        <div class="border-2 border-gray-200 rounded-lg p-2 text-center hover:border-yellow-400 peer-checked:border-yellow-500 peer-checked:bg-yellow-50">
                            <i class="bi bi-envelope-fill text-xl text-yellow-500 block mb-0.5"></i>
                            <span class="text-[10px] font-bold text-gray-700">Izin</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="multi_status" value="cuti" class="peer sr-only">
                        <div class="border-2 border-gray-200 rounded-lg p-2 text-center hover:border-purple-400 peer-checked:border-purple-500 peer-checked:bg-purple-50">
                            <i class="bi bi-airplane-fill text-xl text-purple-500 block mb-0.5"></i>
                            <span class="text-[10px] font-bold text-gray-700">Cuti</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="multi_status" value="alpa" class="peer sr-only">
                        <div class="border-2 border-gray-200 rounded-lg p-2 text-center hover:border-red-400 peer-checked:border-red-500 peer-checked:bg-red-50">
                            <i class="bi bi-x-octagon-fill text-xl text-red-500 block mb-0.5"></i>
                            <span class="text-[10px] font-bold text-gray-700">Alpa</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="multi_status" value="dinas_luar" class="peer sr-only">
                        <div class="border-2 border-gray-200 rounded-lg p-2 text-center hover:border-orange-400 peer-checked:border-orange-500 peer-checked:bg-orange-50">
                            <i class="bi bi-briefcase-fill text-xl text-orange-500 block mb-0.5"></i>
                            <span class="text-[10px] font-bold text-gray-700">Dinas</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="multi_status" value="libur_nasional" class="peer sr-only">
                        <div class="border-2 border-gray-200 rounded-lg p-2 text-center hover:border-gray-400 peer-checked:border-gray-500 peer-checked:bg-gray-100">
                            <i class="bi bi-calendar-x-fill text-xl text-gray-500 block mb-0.5"></i>
                            <span class="text-[10px] font-bold text-gray-700">Libur</span>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wider">Keterangan <span class="text-gray-400 font-normal">(opsional)</span></label>
                <textarea id="multiKeterangan" rows="2" maxlength="500"
                          placeholder="Contoh: Izin keperluan keluarga di luar kota"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none"></textarea>
            </div>
        </div>

        <div class="bg-gray-50 px-5 py-3 flex items-center justify-end gap-2 border-t border-gray-200 shrink-0">
            <button type="button" onclick="closeMultiHariModal()" 
                    class="text-xs font-bold text-gray-600 hover:bg-gray-200 px-4 py-2 rounded-lg">
                Batal
            </button>
            <button type="button" onclick="saveMultiHari()" id="btnSaveMulti"
                    class="text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-5 py-2 rounded-lg flex items-center gap-1.5">
                <i class="bi bi-check-lg"></i> Simpan Semua
            </button>
        </div>
    </div>
</div>

<script>
    // ==========================================
    // VARIABEL GLOBAL
    // ==========================================
    const serverKeyword = @json($search ?? '');
    const CSRF = '{{ csrf_token() }}';
    const semuaKaryawan = @json($karyawanSimple);

    const rows = Array.from(document.querySelectorAll('#tabelMatrixBody .matrix-row'));
    const searchInput = document.getElementById('searchInput');
    const searchBadge = document.getElementById('searchBadge');
    const searchLoading = document.getElementById('searchLoading');
    const filterInfoBar = document.getElementById('filterInfoBar');
    const matchCount  = document.getElementById('matchCount');
    const totalCount  = document.getElementById('totalCount');

    // ==========================================
    // ⭐ AUTO-SUBMIT TANGGAL (Baru)
    // ==========================================
    function submitDateFilter() {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;

        if (start && end && new Date(start) > new Date(end)) {
            alert('Tanggal "Dari" tidak boleh lebih besar dari "Sampai"!');
            return;
        }

        document.getElementById('filterForm').submit();
    }

    // ==========================================
    // QUICK DATE
    // ==========================================
    function setQuickDate(daysAgo, buttonId) {
        const end = new Date();
        const start = new Date();
        start.setDate(end.getDate() - daysAgo);

        document.getElementById('end_date').value = end.toISOString().split('T')[0];
        document.getElementById('start_date').value = start.toISOString().split('T')[0];
        
        document.querySelectorAll('.quick-btn').forEach(btn => {
            btn.className = 'quick-btn px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg border border-gray-300 transition-colors';
        });
        
        const activeBtn = document.getElementById(buttonId);
        if (activeBtn) {
            activeBtn.className = 'quick-btn px-3 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-300 text-xs font-semibold rounded-lg transition-colors';
        }

        document.getElementById('filterForm').submit();
    }

    window.addEventListener('load', function() {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;
        
        const startDate = new Date(start);
        const endDate = new Date(end);
        const diffDays = Math.ceil(Math.abs(endDate - startDate) / (1000 * 60 * 60 * 24)); 

        let activeId = null;
        if (diffDays === 0) activeId = 'btn-quick-0';
        else if (diffDays === 6) activeId = 'btn-quick-6';
        else if (diffDays === 30) activeId = 'btn-quick-30';

        if (activeId) {
            const btn = document.getElementById(activeId);
            if (btn) btn.className = 'quick-btn px-3 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-300 text-xs font-semibold rounded-lg transition-colors';
        }

        if (searchInput && searchInput.value.trim() !== '') {
            searchBadge.classList.remove('hidden');
            setTimeout(() => {
                searchInput.focus();
                const len = searchInput.value.length;
                searchInput.setSelectionRange(len, len);
            }, 100);
        }
    });

    // ==========================================
    // SELECT ALL
    // ==========================================
    function toggleSelectAllKaryawan(source) {
        document.querySelectorAll('.row-checkbox-karyawan').forEach(cb => {
            const row = cb.closest('tr');
            if (row && row.style.display !== 'none') cb.checked = source.checked;
        });
    }

    // ==========================================
    // TOOLTIP PORTAL
    // ==========================================
    const tooltipPortal = document.getElementById('tooltipPortal');

    function buildTooltipHTML(cell) {
        const nama      = cell.dataset.namaKaryawan || '-';
        const tanggal   = cell.dataset.tanggal;
        const jamMasuk  = cell.dataset.jamMasuk;
        const jamPulang = cell.dataset.jamPulang;
        const rMasuk    = cell.dataset.radiusMasuk;
        const rPulang   = cell.dataset.radiusPulang;
        const aMasuk    = cell.dataset.alamatMasuk;
        const aPulang   = cell.dataset.alamatPulang;
        const jMasuk    = cell.dataset.jarakMasuk;
        const jPulang   = cell.dataset.jarakPulang;
        const statusManual = cell.dataset.statusManual;
        const ketManual    = cell.dataset.keteranganManual;

        const d = new Date(tanggal + 'T00:00:00');
        const hariID = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const bulanID = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const tglFormatted = `${hariID[d.getDay()]}, ${d.getDate()} ${bulanID[d.getMonth()]} ${d.getFullYear()}`;

        const fmtJam = (str) => {
            if (!str) return '--:--';
            const m = str.match(/(\d{2}):(\d{2})/);
            return m ? `${m[1]}:${m[2]}` : '--:--';
        };

        if (statusManual) {
            const labelMap = {
                'sakit': 'Sakit', 'izin': 'Izin', 'cuti': 'Cuti',
                'alpa': 'Alpa', 'dinas_luar': 'Dinas Luar', 'libur_nasional': 'Libur'
            };
            const warnaMap = {
                'sakit': 'text-blue-300', 'izin': 'text-yellow-300', 'cuti': 'text-purple-300',
                'alpa': 'text-red-300', 'dinas_luar': 'text-orange-300', 'libur_nasional': 'text-gray-300'
            };
            const label = labelMap[statusManual] || '-';
            const warna = warnaMap[statusManual] || 'text-gray-300';

            return `
                <div class="w-72 bg-slate-900 text-white text-left p-3.5 rounded-xl shadow-2xl text-xs border border-slate-700">
                    <div class="font-bold text-blue-300 border-b border-slate-700 pb-1.5 mb-2.5 flex justify-between gap-2">
                        <span>${tglFormatted}</span>
                        <span class="truncate">${nama}</span>
                    </div>
                    <div class="bg-slate-800 rounded-lg p-2.5 text-center">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Status Manual HRD</div>
                        <div class="text-lg font-bold ${warna}">${label}</div>
                    </div>
                    ${ketManual ? `
                        <div class="mt-2 text-[11px] text-gray-300 leading-tight bg-slate-800 p-1.5 rounded">
                            <i class="bi bi-chat-left-text mr-0.5"></i> ${ketManual}
                        </div>
                    ` : ''}
                </div>
            `;
        }

        const badgeMasuk  = rMasuk  === 'dalam_radius' ? 'Dalam Radius' : (rMasuk  ? 'Luar Radius' : 'Tidak Terdeteksi');
        const badgePulang = rPulang === 'dalam_radius' ? 'Dalam Radius' : (rPulang ? 'Luar Radius' : 'Tidak Terdeteksi');
        const clsMasuk    = rMasuk  === 'dalam_radius' ? 'bg-emerald-900 text-emerald-300' : 'bg-rose-900 text-rose-300';
        const clsPulang   = rPulang === 'dalam_radius' ? 'bg-emerald-900 text-emerald-300' : 'bg-rose-900 text-rose-300';

        return `
            <div class="w-72 bg-slate-900 text-white text-left p-3.5 rounded-xl shadow-2xl text-xs border border-slate-700">
                <div class="font-bold text-blue-300 border-b border-slate-700 pb-1.5 mb-2.5 flex justify-between gap-2">
                    <span>${tglFormatted}</span>
                    <span class="truncate">${nama}</span>
                </div>
                <div class="mb-2.5">
                    <div class="flex justify-between items-center mb-1 gap-2">
                        <span class="font-bold text-emerald-400 text-sm"><i class="bi bi-box-arrow-in-right mr-1"></i> In: ${fmtJam(jamMasuk)}</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded ${clsMasuk} whitespace-nowrap">${badgeMasuk}</span>
                    </div>
                    <div class="text-[11px] text-gray-300 leading-tight bg-slate-800 p-1.5 rounded">
                        <i class="bi bi-geo-alt-fill text-emerald-500 mr-0.5"></i>
                        ${aMasuk || 'Lokasi tidak diketahui'}
                        ${jMasuk ? '(' + jMasuk + 'm)' : ''}
                    </div>
                </div>
                ${jamPulang ? `
                <div>
                    <div class="flex justify-between items-center mb-1 gap-2">
                        <span class="font-bold text-rose-400 text-sm"><i class="bi bi-box-arrow-left mr-1"></i> Out: ${fmtJam(jamPulang)}</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded ${clsPulang} whitespace-nowrap">${badgePulang}</span>
                    </div>
                    <div class="text-[11px] text-gray-300 leading-tight bg-slate-800 p-1.5 rounded">
                        <i class="bi bi-geo-alt-fill text-rose-500 mr-0.5"></i>
                        ${aPulang || 'Lokasi tidak diketahui'}
                        ${jPulang ? '(' + jPulang + 'm)' : ''}
                    </div>
                </div>
                ` : `
                <div class="text-[11px] text-amber-300 bg-amber-900/30 p-1.5 rounded border border-amber-900/50">
                    <i class="bi bi-hourglass-split mr-1"></i> Belum absen pulang
                </div>
                `}
            </div>
        `;
    }

    function showTooltip(cell) {
        if (!cell.dataset.prs) return;
        tooltipPortal.innerHTML = buildTooltipHTML(cell);
        tooltipPortal.classList.remove('hidden');
        positionTooltip(cell);
        requestAnimationFrame(() => tooltipPortal.classList.add('visible'));
    }

    function positionTooltip(cell) {
        const rect = cell.getBoundingClientRect();
        const ttRect = tooltipPortal.getBoundingClientRect();

        let top  = rect.top - ttRect.height - 10;
        let left = rect.left + (rect.width / 2) - (ttRect.width / 2);

        if (top < 8) top = rect.bottom + 10;
        if (left < 8) left = 8;
        if (left + ttRect.width > window.innerWidth - 8) {
            left = window.innerWidth - ttRect.width - 8;
        }

        tooltipPortal.style.top  = top + 'px';
        tooltipPortal.style.left = left + 'px';
    }

    function hideTooltip() {
        tooltipPortal.classList.remove('visible');
        tooltipPortal.classList.add('hidden');
    }

    document.querySelectorAll('.cell-presensi').forEach(cell => {
        cell.addEventListener('mouseenter', function () { showTooltip(this); });
        cell.addEventListener('mouseleave', function () { hideTooltip(); });
    });

    window.addEventListener('scroll', hideTooltip, true);
    window.addEventListener('resize', hideTooltip);

    // ==========================================
    // LIVE FILTER
    // ==========================================
    function applyAllFilters() {
        const q  = (searchInput.value || '').trim().toLowerCase();
        const pt = document.getElementById('liveFilterPerusahaan').value;
        const dv = document.getElementById('liveFilterDivisi').value;
        const dp = document.getElementById('liveFilterDepartemen').value;
        const jb = document.getElementById('liveFilterJabatan').value;

        const emptyRow = document.getElementById('emptyFilterRow');
        let shown = 0;

        rows.forEach(row => {
            const nama = row.dataset.nama || '';
            const nik  = row.dataset.nik || '';
            const matchSearch = !q || nama.includes(q) || nik.includes(q);
            const matchFilter =
                (!pt || row.dataset.perusahaan === pt) &&
                (!dv || row.dataset.divisi === dv) &&
                (!dp || row.dataset.departemen === dp) &&
                (!jb || row.dataset.jabatan === jb);

            if (matchSearch && matchFilter) {
                row.style.display = '';
                shown++;
            } else {
                row.style.display = 'none';
            }
        });

        const adaAktivitas = q || pt || dv || dp || jb;
        if (adaAktivitas) {
            filterInfoBar.classList.remove('hidden');
            matchCount.textContent = shown;
            totalCount.textContent = rows.length;
        } else {
            filterInfoBar.classList.add('hidden');
        }

        if (q) searchBadge.classList.remove('hidden');
        else searchBadge.classList.add('hidden');

        if (emptyRow) emptyRow.classList.toggle('hidden', shown > 0 || !adaAktivitas);

        const selectAll = document.getElementById('selectAllKaryawan');
        if (selectAll) selectAll.checked = false;
    }

    function reloadWithoutSearch() {
        const url = new URL(window.location.href);
        url.searchParams.delete('search');
        url.searchParams.delete('page');
        window.location.href = url.toString();
    }

    function resetLiveFilter() {
        document.getElementById('liveFilterPerusahaan').value = '';
        document.getElementById('liveFilterDivisi').value = '';
        document.getElementById('liveFilterDepartemen').value = '';
        document.getElementById('liveFilterJabatan').value = '';
        searchInput.value = '';

        if (serverKeyword) {
            reloadWithoutSearch();
            return;
        }
        applyAllFilters();
    }

    let searchTimeout;
    let isSubmitting = false;

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = (this.value || '').trim();
            clearTimeout(searchTimeout);
            searchLoading.classList.add('hidden');
            searchBadge.classList.add('hidden');

            if (q === '' && serverKeyword) {
                searchLoading.classList.remove('hidden');
                searchTimeout = setTimeout(() => reloadWithoutSearch(), 300);
                return;
            }
            if (q === '') {
                applyAllFilters();
                return;
            }

            searchBadge.classList.remove('hidden');
            searchTimeout = setTimeout(() => {
                if (isSubmitting) return;
                isSubmitting = true;
                searchBadge.classList.add('hidden');
                searchLoading.classList.remove('hidden');
                document.getElementById('filterForm').submit();
            }, 500);
        });

        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                const q = (this.value || '').trim();
                if (q === '') {
                    reloadWithoutSearch();
                } else {
                    searchBadge.classList.add('hidden');
                    searchLoading.classList.remove('hidden');
                    document.getElementById('filterForm').submit();
                }
            }
        });
    }

    // ==========================================
    // MODAL SINGLE HARI
    // ==========================================
    let activeCell = null;
    let activeKaryawanId = null;
    let activeTanggal = null;

    function openStatusModal(cell) {
        activeCell = cell;
        activeKaryawanId = cell.dataset.karyawanId;
        activeTanggal = cell.dataset.tanggal;
        const namaKaryawan = cell.dataset.namaKaryawan;

        const d = new Date(activeTanggal + 'T00:00:00');
        const hariID = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const bulanID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const tglFormatted = `${hariID[d.getDay()]}, ${d.getDate()} ${bulanID[d.getMonth()]} ${d.getFullYear()}`;

        document.getElementById('modalNamaKaryawan').textContent = namaKaryawan;
        document.getElementById('modalTanggal').textContent = tglFormatted;
        document.getElementById('modalSubtitle').textContent = `${namaKaryawan} • ${d.getDate()}/${d.getMonth()+1}/${d.getFullYear()}`;

        document.querySelectorAll('input[name="status_kehadiran"]').forEach(r => r.checked = false);
        document.getElementById('modalKeterangan').value = '';
        document.getElementById('btnHapusStatus').classList.add('hidden');

        const existingStatus = cell.dataset.statusManual;
        if (existingStatus) {
            const radio = document.querySelector(`input[name="status_kehadiran"][value="${existingStatus}"]`);
            if (radio) radio.checked = true;
            document.getElementById('modalKeterangan').value = cell.dataset.keteranganManual || '';
            document.getElementById('btnHapusStatus').classList.remove('hidden');
        }

        document.getElementById('statusModal').classList.remove('hidden');
    }

    function closeStatusModal() {
        document.getElementById('statusModal').classList.add('hidden');
        activeCell = null;
        activeKaryawanId = null;
        activeTanggal = null;
    }

    async function saveStatusManual() {
        const selected = document.querySelector('input[name="status_kehadiran"]:checked');
        if (!selected) {
            alert('Pilih status kehadiran terlebih dahulu!');
            return;
        }

        const btn = document.getElementById('btnSaveStatus');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';

        try {
            const response = await fetch('{{ route("kehadiran.status-manual") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    karyawan_id: activeKaryawanId,
                    tanggal: activeTanggal,
                    status_kehadiran: selected.value,
                    keterangan: document.getElementById('modalKeterangan').value.trim(),
                }),
            });

            const data = await response.json();

            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Gagal menyimpan status.');
            }
        } catch (err) {
            alert('Terjadi kesalahan: ' + err.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    async function deleteStatusManual() {
        if (!confirm('Yakin hapus status kehadiran ini?')) return;

        const btn = document.getElementById('btnHapusStatus');
        btn.disabled = true;

        try {
            const response = await fetch('{{ route("kehadiran.status-manual.hapus") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    karyawan_id: activeKaryawanId,
                    tanggal: activeTanggal,
                }),
            });

            const data = await response.json();

            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Gagal hapus status.');
            }
        } catch (err) {
            alert('Terjadi kesalahan: ' + err.message);
        } finally {
            btn.disabled = false;
        }
    }

    document.getElementById('statusModal').addEventListener('click', function(e) {
        if (e.target === this) closeStatusModal();
    });

    // ==========================================
    // MODAL MULTI-HARI
    // ==========================================
    function openMultiHariModal() {
        document.getElementById('multiKaryawanSearch').value = '';
        document.getElementById('multiKaryawanId').value = '';
        document.getElementById('multiTanggalMulai').value = '';
        document.getElementById('multiTanggalSelesai').value = '';
        document.querySelectorAll('input[name="multi_status"]').forEach(r => r.checked = false);
        document.getElementById('multiKeterangan').value = '';
        document.getElementById('multiKaryawanDropdown').classList.add('hidden');
        document.getElementById('multiHariModal').classList.remove('hidden');
    }

    function closeMultiHariModal() {
        document.getElementById('multiHariModal').classList.add('hidden');
    }

    function showMultiKaryawanDropdown() {
        const dropdown = document.getElementById('multiKaryawanDropdown');
        dropdown.classList.remove('hidden');
        filterMultiKaryawan();
    }

    function filterMultiKaryawan() {
        const input = document.getElementById('multiKaryawanSearch');
        const dropdown = document.getElementById('multiKaryawanDropdown');
        const q = input.value.trim().toLowerCase();

        let hasil = [];
        if (q === '') {
            hasil = semuaKaryawan.slice(0, 10);
        } else {
            hasil = semuaKaryawan.filter(k =>
                k.nama.toLowerCase().includes(q) || k.nik.toLowerCase().includes(q)
            ).slice(0, 30);
        }

        if (hasil.length === 0) {
            dropdown.innerHTML = `
                <div class="px-4 py-6 text-center text-gray-400 text-xs">
                    <i class="bi bi-inbox text-2xl block mb-1"></i>
                    Tidak ada karyawan yang cocok
                </div>
            `;
            return;
        }

        dropdown.innerHTML = hasil.map(k => `
            <div onclick="pilihKaryawanMulti(${k.id}, '${k.nama.replace(/'/g, "\\'")}')"
                 class="px-4 py-2.5 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 last:border-0 transition-colors">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center shrink-0">
                        ${k.nama.substring(0, 2).toUpperCase()}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-sm text-gray-800 truncate">${k.nama}</div>
                        <div class="text-[11px] text-gray-500">${k.nik} • ${k.dept}</div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function pilihKaryawanMulti(id, nama) {
        document.getElementById('multiKaryawanId').value = id;
        document.getElementById('multiKaryawanSearch').value = nama;
        document.getElementById('multiKaryawanDropdown').classList.add('hidden');
    }

    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('multiKaryawanDropdown');
        const input = document.getElementById('multiKaryawanSearch');
        if (dropdown && input && !dropdown.contains(e.target) && e.target !== input) {
            dropdown.classList.add('hidden');
        }
    });

    async function saveMultiHari() {
        const karyawanId = document.getElementById('multiKaryawanId').value;
        const tglMulai   = document.getElementById('multiTanggalMulai').value;
        const tglSelesai = document.getElementById('multiTanggalSelesai').value;
        const status     = document.querySelector('input[name="multi_status"]:checked');
        const keterangan = document.getElementById('multiKeterangan').value.trim();

        if (!karyawanId) { alert('Pilih karyawan terlebih dahulu!'); return; }
        if (!tglMulai)   { alert('Isi tanggal mulai!'); return; }
        if (!tglSelesai) { alert('Isi tanggal selesai!'); return; }
        if (!status)     { alert('Pilih status kehadiran!'); return; }

        const btn = document.getElementById('btnSaveMulti');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';

        try {
            const response = await fetch('{{ route("kehadiran.status-manual.multi") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    karyawan_id: karyawanId,
                    tanggal_mulai: tglMulai,
                    tanggal_selesai: tglSelesai,
                    status_kehadiran: status.value,
                    keterangan: keterangan,
                }),
            });

            const data = await response.json();

            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Gagal menyimpan.');
            }
        } catch (err) {
            alert('Terjadi kesalahan: ' + err.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    document.getElementById('multiHariModal').addEventListener('click', function(e) {
        if (e.target === this) closeMultiHariModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeStatusModal();
            closeMultiHariModal();
        }
    });

    // ==========================================
    // EXPORT EXCEL
    // ==========================================
    function exportTerpilihExcel() {
        const checked = Array.from(document.querySelectorAll('.row-checkbox-karyawan:checked'))
            .filter(cb => {
                const row = cb.closest('tr');
                return row && row.style.display !== 'none';
            });

        if (checked.length === 0) {
            alert('Pilih minimal 1 karyawan terlebih dahulu!');
            return;
        }

        const startDate = document.getElementById('start_date').value;
        const endDate   = document.getElementById('end_date').value;
        const baseUrl   = '{{ route("kehadiran.export") }}';

        if (!confirm('Anda akan mendownload ' + checked.length + ' file Excel. Lanjutkan?')) return;

        const infoBox = document.createElement('div');
        infoBox.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#1E3A8A;color:white;padding:14px 20px;border-radius:10px;font-family:Arial;font-size:14px;z-index:99999;box-shadow:0 4px 12px rgba(0,0,0,0.3);';
        infoBox.innerHTML = '<i class="bi bi-download"></i> Mendownload 0/' + checked.length + ' file...';
        document.body.appendChild(infoBox);

        checked.forEach((cb, index) => {
            const url = baseUrl + '?karyawan_id=' + cb.value + '&start_date=' + startDate + '&end_date=' + endDate;
            setTimeout(() => {
                const a = document.createElement('a');
                a.href = url;
                a.style.display = 'none';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                infoBox.innerHTML = '<i class="bi bi-download"></i> Mendownload ' + (index + 1) + '/' + checked.length + ' file...';
            }, index * 1000);
        });

        setTimeout(() => {
            infoBox.style.background = '#15803D';
            infoBox.innerHTML = '<i class="bi bi-check-circle-fill"></i> Selesai! ' + checked.length + ' file terdownload.';
            setTimeout(() => infoBox.remove(), 3000);
        }, checked.length * 1000 + 500);
    }
</script>
</body>
</html>