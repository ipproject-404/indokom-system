<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Kehadiran Lengkap - HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

    <!-- NAVBAR -->
    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-20 shadow-sm">
        <div class="flex items-center">
            <a href="{{ route('dashboard.hrd') }}" class="text-gray-500 hover:bg-gray-100 p-2 rounded-lg mr-3 transition-colors">
                <i class="bi bi-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="font-bold text-xl text-gray-800">Log Kehadiran Karyawan</h1>
                <p class="text-xs text-gray-500">Rekapitulasi absensi masuk dan pulang</p>
            </div>
        </div>
        <div class="text-gray-500 text-sm flex items-center bg-blue-50 px-4 py-2 rounded-lg border border-blue-100">
            <i class="bi bi-calendar-range mr-2 text-blue-600"></i>
            <span class="font-bold text-blue-800">
                {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
            </span>
        </div>
    </nav>

    <div class="p-6 max-w-7xl mx-auto space-y-6">
        
        <!-- KARTU STATISTIK RINGKAS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Total Kehadiran</p>
                    <h3 class="font-bold text-3xl text-gray-800">{{ $totalKehadiran }} <span class="text-sm font-medium text-gray-400">Data</span></h3>
                </div>
                <div class="bg-blue-50 text-blue-600 rounded-full w-12 h-12 flex items-center justify-center text-2xl">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Belum Absen Pulang</p>
                    <h3 class="font-bold text-3xl text-amber-600">{{ $belumPulang }} <span class="text-sm font-medium text-gray-400">Orang</span></h3>
                </div>
                <div class="bg-amber-50 text-amber-600 rounded-full w-12 h-12 flex items-center justify-center text-2xl">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Rata-rata Harian</p>
                    <h3 class="font-bold text-3xl text-emerald-600">~{{ $rataHarian }} <span class="text-sm font-medium text-gray-400">Hadir/Hari</span></h3>
                </div>
                <div class="bg-emerald-50 text-emerald-600 rounded-full w-12 h-12 flex items-center justify-center text-2xl">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
        </div>

        <!-- AREA FILTER & PENCARIAN -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <!-- Filter Cepat -->
            <div class="mb-4 flex flex-wrap gap-2 border-b border-gray-100 pb-4">
                <span class="text-xs font-semibold text-gray-500 py-1.5 mr-2">Filter Cepat:</span>
                <button type="button" onclick="setQuickDate(0)" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-full transition-colors">Hari Ini</button>
                <button type="button" onclick="setQuickDate(6)" class="px-3 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-semibold rounded-full border border-blue-200 transition-colors">7 Hari Terakhir</button>
                <button type="button" onclick="setQuickDate(29)" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-full transition-colors">30 Hari Terakhir</button>
            </div>

            <!-- Form Filter Manual -->
            <form action="{{ route('kehadiran.log') }}" method="GET" class="flex flex-col lg:flex-row gap-4 items-end" id="filterForm">
                <div class="flex-1 w-full flex gap-3">
                    <div class="w-1/2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Dari Tanggal</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Sampai Tanggal</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Cari Karyawan (Nama/NIK)</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama atau NIK..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                        <i class="bi bi-search absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                </div>
                <div class="w-full lg:w-auto flex gap-2">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors shadow-sm flex items-center justify-center w-full lg:w-auto">
                        <i class="bi bi-filter mr-1.5 text-lg"></i> Filter Data
                    </button>
                    <a href="{{ route('kehadiran.log') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors flex items-center justify-center">
                        <i class="bi bi-arrow-clockwise mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- TABEL DATA PRESENSI -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-max">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 font-semibold text-center w-12">No</th>
                            <th class="px-6 py-4 font-semibold">Tanggal & Waktu</th>
                            <th class="px-6 py-4 font-semibold">Informasi Karyawan</th>
                            <th class="px-6 py-4 font-semibold">Status Presensi</th>
                            <th class="px-6 py-4 font-semibold">Titik Lokasi Scanner</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        @forelse($presensis as $index => $prs)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-6 py-4 text-center text-gray-400 font-medium">
                                {{ $presensis->firstItem() + $index }}
                            </td>
                            
                            <!-- Kolom Tanggal & Waktu -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 mb-1">
                                    <i class="bi bi-calendar2-event text-blue-500 mr-1"></i> 
                                    {{ \Carbon\Carbon::parse($prs->tanggal)->translatedFormat('d M Y') }}
                                </div>
                                <div class="flex items-center gap-3 text-xs">
                                    <div class="bg-emerald-50 text-emerald-700 px-2 py-1 rounded font-bold border border-emerald-100">
                                        Masuk: {{ $prs->jam_masuk ? \Carbon\Carbon::parse($prs->jam_masuk)->format('H:i') : '--:--' }}
                                    </div>
                                    <div class="{{ $prs->jam_pulang ? 'bg-rose-50 text-rose-700 border-rose-100' : 'bg-gray-100 text-gray-500 border-gray-200' }} px-2 py-1 rounded font-bold border">
                                        Pulang: {{ $prs->jam_pulang ? \Carbon\Carbon::parse($prs->jam_pulang)->format('H:i') : '--:--' }}
                                    </div>
                                </div>
                            </td>

                            <!-- Kolom Karyawan -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold mr-3">
                                        {{ substr($prs->karyawan->nama_lengkap ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $prs->karyawan->nama_lengkap ?? '-' }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">{{ $prs->karyawan->nik_kerja ?? '-' }} | <span class="text-blue-600 font-medium">{{ $prs->karyawan->departemen->nama_departemen ?? '-' }}</span></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Kolom Status Radius -->
                            <td class="px-6 py-4">
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between w-32">
                                        <span class="text-xs text-gray-500">In:</span>
                                        @if($prs->status_radius_masuk == 'dalam_radius')
                                            <span class="bg-emerald-100 text-emerald-700 text-[10px] px-2 py-0.5 rounded-full font-bold">Dalam Radius</span>
                                        @elseif($prs->status_radius_masuk == 'luar_radius')
                                            <span class="bg-rose-100 text-rose-700 text-[10px] px-2 py-0.5 rounded-full font-bold">Luar Radius</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-500 text-[10px] px-2 py-0.5 rounded-full font-bold">N/A</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between w-32">
                                        <span class="text-xs text-gray-500">Out:</span>
                                        @if($prs->jam_pulang)
                                            @if($prs->status_radius_pulang == 'dalam_radius')
                                                <span class="bg-emerald-100 text-emerald-700 text-[10px] px-2 py-0.5 rounded-full font-bold">Dalam Radius</span>
                                            @elseif($prs->status_radius_pulang == 'luar_radius')
                                                <span class="bg-rose-100 text-rose-700 text-[10px] px-2 py-0.5 rounded-full font-bold">Luar Radius</span>
                                            @else
                                                <span class="bg-gray-100 text-gray-500 text-[10px] px-2 py-0.5 rounded-full font-bold">N/A</span>
                                            @endif
                                        @else
                                            <span class="text-[10px] font-medium text-gray-400 italic">-</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Kolom Peta -->
                            <td class="px-6 py-4 text-xs">
                                <div class="mb-1.5">
                                    <span class="font-semibold text-emerald-600 w-8 inline-block">In:</span>
                                    @if($prs->latitude_masuk)
                                        <a href="https://maps.google.com/?q={{ $prs->latitude_masuk }},{{ $prs->longitude_masuk }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline">
                                            <i class="bi bi-geo-alt-fill"></i> Peta <span class="text-gray-400 ml-1">({{ $prs->jarak_masuk_meter ?? 0 }}m)</span>
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic">N/A</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="font-semibold text-rose-500 w-8 inline-block">Out:</span>
                                    @if($prs->latitude_pulang)
                                        <a href="https://maps.google.com/?q={{ $prs->latitude_pulang }},{{ $prs->longitude_pulang }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline">
                                            <i class="bi bi-geo-alt-fill"></i> Peta <span class="text-gray-400 ml-1">({{ $prs->jarak_pulang_meter ?? 0 }}m)</span>
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic">N/A</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                    <i class="bi bi-folder-x text-2xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800 mb-1">Tidak Ada Data Presensi</h3>
                                <p class="text-gray-500 text-sm">Cobalah untuk mengubah rentang tanggal atau kata kunci pencarian.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- PAGINATION -->
            @if($presensis->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Menampilkan <span class="font-semibold text-gray-800">{{ $presensis->firstItem() }}</span> sampai <span class="font-semibold text-gray-800">{{ $presensis->lastItem() }}</span> dari <span class="font-semibold text-gray-800">{{ $presensis->total() }}</span> data
                </div>
                <div>
                    {{ $presensis->withQueryString()->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Script untuk Tombol Filter Cepat -->
    <script>
        function setQuickDate(daysAgo) {
            const end = new Date();
            const start = new Date();
            start.setDate(end.getDate() - daysAgo);

            // Format to YYYY-MM-DD
            document.getElementById('end_date').value = end.toISOString().split('T')[0];
            document.getElementById('start_date').value = start.toISOString().split('T')[0];
            
            // Auto submit
            document.getElementById('filterForm').submit();
        }
    </script>
</body>
</html>