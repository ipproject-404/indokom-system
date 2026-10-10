<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matrix Kehadiran Karyawan - HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Sticky kolom: checkbox (left 0, width 40px) */
        .sticky-col-checkbox {
            position: sticky;
            left: 0;
            background-color: #f8fafc;
            z-index: 12;
            width: 40px;
            min-width: 40px;
            max-width: 40px;
        }
        /* Sticky kolom: nama karyawan (left 40px) */
        .sticky-col-name {
            position: sticky;
            left: 40px;
            background-color: #f8fafc;
            z-index: 10;
            box-shadow: 3px 0 6px rgba(0,0,0,0.06);
        }
        .matrix-scroll::-webkit-scrollbar { height: 12px; width: 12px; }
        .matrix-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 8px; border: 1px solid #e2e8f0; }
        .matrix-scroll::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 8px; border: 2px solid #f1f5f9; }
        .matrix-scroll::-webkit-scrollbar-thumb:hover { background: #64748b; }
        
        .table-matrix th, .table-matrix td {
            border: 1px solid #cbd5e1;
        }
    </style>
</head>
<body class="bg-gray-50">

<div class="flex min-h-screen bg-gray-50">

   @include('partials.sidebar-hrd')

    <!-- =========================
         MAIN CONTENT
    ========================== -->
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

        <!-- CONTENT BODY -->
        <div class="p-6 grow overflow-y-auto w-full max-w-[1400px] mx-auto space-y-6">
            
            <!-- KARTU STATISTIK RINGKAS -->
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

            <!-- AREA FILTER, PENCARIAN & EXPORT EXCEL -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="mb-4 flex flex-wrap gap-2 border-b border-gray-200 pb-4" id="quickFilterContainer">
                    <span class="text-xs font-semibold text-gray-500 py-1.5 mr-2">Cepat:</span>
                    <button type="button" id="btn-quick-0" onclick="setQuickDate(0, 'btn-quick-0')" class="quick-btn px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full border border-gray-300 transition-colors">Hari Ini</button>
                    <button type="button" id="btn-quick-6" onclick="setQuickDate(6, 'btn-quick-6')" class="quick-btn px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full border border-gray-300 transition-colors">7 Hari</button>
                    <button type="button" id="btn-quick-30" onclick="setQuickDate(30, 'btn-quick-30')" class="quick-btn px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full border border-gray-300 transition-colors">31 Hari</button>
                </div>

                <form action="{{ route('kehadiran.log') }}" method="GET" class="flex flex-col xl:flex-row gap-4 items-end" id="filterForm">
                    <div class="flex-1 w-full flex gap-3">
                        <div class="w-1/2">
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Dari Tanggal (Max 31 Hari)</label>
                            <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-600 outline-none shadow-sm">
                        </div>
                        <div class="w-1/2">
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Sampai Tanggal</label>
                            <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-600 outline-none shadow-sm">
                        </div>
                    </div>
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Cari Karyawan</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama atau NIK..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-600 outline-none shadow-sm">
                            <i class="bi bi-search absolute left-3 top-2.5 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="w-full xl:w-auto flex flex-wrap gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors shadow-sm flex items-center justify-center flex-1">
                            <i class="bi bi-filter mr-1.5 text-lg"></i> Filter
                        </button>
                        <!-- ⭐ TOMBOL EXPORT TERPILIH (via JS) -->
                        <button type="button" onclick="exportTerpilihExcel()" class="bg-emerald-600 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center justify-center flex-1">
                            <i class="bi bi-file-earmark-excel mr-1.5 text-lg"></i> Export Excel Terpilih
                        </button>
                        <a href="{{ route('kehadiran.log') }}" class="bg-gray-100 text-gray-700 border border-gray-300 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-200 transition-colors flex items-center justify-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- KETERANGAN WARNA -->
            <div class="flex flex-wrap items-center gap-3 mb-1">
                <div class="flex items-center gap-2.5 bg-emerald-50 border-2 border-emerald-300 px-4 py-2 rounded-xl shadow-sm">
                    <i class="bi bi-check-square-fill text-emerald-500 text-xl"></i>
                    <span class="text-sm font-bold text-emerald-800">Hadir Lengkap</span>
                </div>
                <div class="flex items-center gap-2.5 bg-amber-50 border-2 border-amber-300 px-4 py-2 rounded-xl shadow-sm">
                    <i class="bi bi-exclamation-square-fill text-amber-500 text-xl"></i>
                    <span class="text-sm font-bold text-amber-800">Belum Pulang / Luar Radius</span>
                </div>
                <div class="flex items-center gap-2.5 bg-gray-50 border-2 border-gray-200 px-4 py-2 rounded-xl shadow-sm">
                    <i class="bi bi-dash-square text-gray-400 text-xl"></i>
                    <span class="text-sm font-bold text-gray-600">Tidak Hadir / Libur</span>
                </div>
            </div>

            <!-- TABEL MATRIX DATA PRESENSI -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-300 overflow-hidden relative">
                <div class="overflow-x-auto matrix-scroll">
                    <table class="w-full text-left border-collapse table-matrix" style="min-width: max-content;">
                        <thead class="bg-slate-700 text-white text-[11px] uppercase tracking-wider">
                            <tr>
                                <!-- ⭐ CHECKBOX COLUMN -->
                                <th class="sticky-col-checkbox bg-slate-800 text-white border-r border-slate-900 px-2 py-3 text-center">
                                    <input type="checkbox" id="selectAllKaryawan" onclick="toggleSelectAllKaryawan(this)" 
                                           class="w-4 h-4 rounded cursor-pointer accent-blue-600">
                                </th>
                                <!-- NAMA KARYAWAN COLUMN -->
                                <th class="sticky-col-name bg-slate-800 text-white border-r-2 border-slate-900 px-5 py-3 font-bold w-64 min-w-[250px] shadow-[2px_0_5px_rgba(0,0,0,0.3)]">Karyawan</th>
                                
                                @foreach($dates as $date)
                                @php 
                                    \Carbon\Carbon::setLocale('id');
                                    $isWeekend = \Carbon\Carbon::parse($date)->isSunday(); 
                                @endphp
                                <th class="px-2 py-3 font-semibold text-center {{ $isWeekend ? 'bg-rose-600' : 'bg-slate-700' }} border-r border-slate-600 min-w-[50px]">
                                    <div class="opacity-90 mb-1 lowercase font-medium">{{ \Carbon\Carbon::parse($date)->translatedFormat('D') }}</div>
                                    <div class="text-base font-bold">{{ \Carbon\Carbon::parse($date)->format('d') }}</div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700">
                            @forelse($karyawans as $karyawan)
                            <tr class="hover:bg-blue-50/50 transition-colors group">
                                
                                <!-- ⭐ CHECKBOX CELL -->
                                <td class="sticky-col-checkbox group-hover:bg-slate-100 text-center px-2 py-3 border-r border-gray-300">
                                    <input type="checkbox" class="row-checkbox-karyawan w-4 h-4 rounded cursor-pointer accent-blue-600" 
                                           value="{{ $karyawan->id }}">
                                </td>

                                <!-- NAMA KARYAWAN CELL -->
                                <td class="sticky-col-name group-hover:bg-slate-100 border-r-2 border-gray-300 px-5 py-3 shadow-[2px_0_5px_rgba(0,0,0,0.05)]">
                                    <div class="font-bold text-gray-900 truncate" title="{{ $karyawan->nama_lengkap }}">{{ $karyawan->nama_lengkap }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5 font-medium truncate">{{ $karyawan->nik_kerja }} &bull; {{ $karyawan->departemen->nama_departemen ?? '-' }}</div>
                                </td>

                                @foreach($dates as $date)
                                @php
                                    $prs = isset($presensiData[$karyawan->id]) && isset($presensiData[$karyawan->id][$date]) 
                                            ? $presensiData[$karyawan->id][$date] 
                                            : null;
                                    
                                    $cellBg = 'bg-gray-50/40';
                                    if($prs) {
                                        if($prs->jam_pulang) {
                                            $cellBg = 'bg-emerald-50/60 hover:bg-emerald-100';
                                        } else {
                                            $cellBg = 'bg-amber-50/60 hover:bg-amber-100';
                                        }
                                    }
                                @endphp

                                <td class="px-2 py-2 text-center border-r border-gray-300 relative {{ $cellBg }}">
                                    @if($prs)
                                        <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative inline-block cursor-pointer w-full h-full flex items-center justify-center">
                                            
                                            @if($prs->jam_pulang && $prs->status_radius_masuk == 'dalam_radius' && $prs->status_radius_pulang == 'dalam_radius')
                                                <i class="bi bi-check-square-fill text-emerald-500 text-3xl drop-shadow-sm"></i>
                                            @else
                                                <i class="bi bi-exclamation-square-fill text-amber-500 text-3xl drop-shadow-sm"></i>
                                            @endif

                                            <div x-show="open" 
                                                 x-transition.opacity 
                                                 class="absolute z-50 bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-72 bg-slate-900 text-white text-left p-3.5 rounded-xl shadow-2xl text-xs border border-slate-700"
                                                 style="display: none;">
                                                <div class="font-bold text-blue-300 border-b border-slate-700 pb-1.5 mb-2.5 flex justify-between">
                                                    <span>{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d M Y') }}</span>
                                                    <span>{{ $karyawan->nama_lengkap }}</span>
                                                </div>
                                                
                                                <div class="mb-2.5">
                                                    <div class="flex justify-between items-center mb-1">
                                                        <span class="font-bold text-emerald-400 text-sm"><i class="bi bi-box-arrow-in-right mr-1"></i> In: {{ \Carbon\Carbon::parse($prs->jam_masuk)->format('H:i') }}</span>
                                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $prs->status_radius_masuk == 'dalam_radius' ? 'bg-emerald-900 text-emerald-300' : 'bg-rose-900 text-rose-300' }}">{{ $prs->status_radius_masuk == 'dalam_radius' ? 'Dalam Radius' : 'Luar Radius' }}</span>
                                                    </div>
                                                    <div class="text-[11px] text-gray-300 leading-tight bg-slate-800 p-1.5 rounded">
                                                        <i class="bi bi-geo-alt-fill text-emerald-500 mr-0.5"></i> 
                                                        {{ $prs->alamat_masuk ?? ($prs->nama_jalan_masuk ?? 'Lokasi tidak diketahui') }} 
                                                        {{ $prs->jarak_masuk_meter ? '('.$prs->jarak_masuk_meter.'m)' : '' }}
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="flex justify-between items-center mb-1">
                                                        <span class="font-bold text-rose-400 text-sm"><i class="bi bi-box-arrow-left mr-1"></i> Out: {{ $prs->jam_pulang ? \Carbon\Carbon::parse($prs->jam_pulang)->format('H:i') : '--:--' }}</span>
                                                        @if($prs->jam_pulang)
                                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $prs->status_radius_pulang == 'dalam_radius' ? 'bg-emerald-900 text-emerald-300' : 'bg-rose-900 text-rose-300' }}">{{ $prs->status_radius_pulang == 'dalam_radius' ? 'Dalam Radius' : 'Luar Radius' }}</span>
                                                        @endif
                                                    </div>
                                                    @if($prs->jam_pulang)
                                                        <div class="text-[11px] text-gray-300 leading-tight bg-slate-800 p-1.5 rounded">
                                                        <i class="bi bi-geo-alt-fill text-rose-500 mr-0.5"></i> 
                                                            {{ $prs->alamat_pulang ?? ($prs->nama_jalan_pulang ?? 'Lokasi tidak diketahui') }}
                                                            {{ $prs->jarak_pulang_meter ? '('.$prs->jarak_pulang_meter.'m)' : '' }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="absolute w-3 h-3 bg-slate-900 transform rotate-45 left-1/2 -bottom-1.5 -translate-x-1/2 border-r border-b border-slate-700"></div>
                                            </div>
                                        </div>
                                    @else
                                        <i class="bi bi-dash-square text-gray-300 text-2xl font-light"></i>
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

<script>
    function setQuickDate(daysAgo, buttonId) {
        const end = new Date();
        const start = new Date();
        start.setDate(end.getDate() - daysAgo);

        document.getElementById('end_date').value = end.toISOString().split('T')[0];
        document.getElementById('start_date').value = start.toISOString().split('T')[0];
        
        const allButtons = document.querySelectorAll('.quick-btn');
        allButtons.forEach(btn => {
            btn.className = 'quick-btn px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-full border border-gray-300 transition-colors';
        });
        
        const activeBtn = document.getElementById(buttonId);
        if(activeBtn) {
            activeBtn.className = 'quick-btn px-3 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-300 text-xs font-semibold rounded-full transition-colors';
        }

        document.getElementById('filterForm').submit();
    }

    window.onload = function() {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;
        
        const startDate = new Date(start);
        const endDate = new Date(end);
        const diffTime = Math.abs(endDate - startDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 

        let activeId = null;
        if (diffDays === 0) activeId = 'btn-quick-0';
        else if (diffDays === 6) activeId = 'btn-quick-6';
        else if (diffDays === 30) activeId = 'btn-quick-30';

        if (activeId) {
            const btn = document.getElementById(activeId);
            if (btn) {
                btn.className = 'quick-btn px-3 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-300 text-xs font-semibold rounded-full transition-colors';
            }
        }
    };

    // ==========================================
    // SELECT ALL CHECKBOX
    // ==========================================
    function toggleSelectAllKaryawan(source) {
        document.querySelectorAll('.row-checkbox-karyawan').forEach(cb => {
            cb.checked = source.checked;
        });
    }

    // ==========================================
    // EXPORT EXCEL — MULTI DOWNLOAD (1 file per karyawan)
    // ==========================================
    function exportTerpilihExcel() {
        const checked = document.querySelectorAll('.row-checkbox-karyawan:checked');

        if (checked.length === 0) {
            alert('Pilih minimal 1 karyawan terlebih dahulu!');
            return;
        }

        const startDate = document.getElementById('start_date').value;
        const endDate   = document.getElementById('end_date').value;
        const baseUrl   = '{{ route("kehadiran.export") }}';

        if (!confirm('Anda akan mendownload ' + checked.length + ' file Excel. Lanjutkan?')) {
            return;
        }

        // Info progress
        const infoBox = document.createElement('div');
        infoBox.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#1E3A8A;color:white;padding:14px 20px;border-radius:10px;font-family:Arial;font-size:14px;z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,0.3);';
        infoBox.innerHTML = '<i class="bi bi-download"></i> Mendownload 0/' + checked.length + ' file...';
        document.body.appendChild(infoBox);

        // Download satu-satu dengan delay 1 detik
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

        // Selesai
        setTimeout(() => {
            infoBox.style.background = '#15803D';
            infoBox.innerHTML = '<i class="bi bi-check-circle-fill"></i> Selesai! ' + checked.length + ' file terdownload.';
            setTimeout(() => infoBox.remove(), 3000);
        }, checked.length * 1000 + 500);
    }
</script>
</body>
</html>