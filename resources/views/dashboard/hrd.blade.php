<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard HRD - Sistem Presensi & Kinerja</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Script untuk Jam Realtime -->
    <script>
        function updateClock() {
            var now = new Date();
            var hours = String(now.getHours()).padStart(2, '0');
            var minutes = String(now.getMinutes()).padStart(2, '0');
            var seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('realtime-clock').textContent = hours + ':' + minutes + ':' + seconds;
        }
        setInterval(updateClock, 1000);
    </script>
</head>

<body onload="updateClock()">

<div class="flex min-h-screen bg-gray-50">

    <!-- =========================
         SIDEBAR
    ========================== -->
    <aside id="sidebar" class="bg-white border-r border-gray-200 flex flex-col w-[260px] shrink-0">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center">
                <div class="bg-blue-600 text-white rounded-lg flex items-center justify-center mr-3 w-[42px] h-[42px] shrink-0">
                    <i class="bi bi-person-badge-fill text-xl"></i>
                </div>
                <div>
                    <div class="font-bold text-blue-600">Portal HRD</div>
                    <div class="text-xs text-gray-500">Presensi & Kinerja</div>
                </div>
            </div>
        </div>

        <div class="p-4 grow overflow-y-auto">
            <div class="uppercase text-gray-400 text-xs font-bold mb-3 mt-2">Menu Utama</div>
            <a href="{{ route('dashboard.hrd') }}" class="flex items-center bg-blue-600 text-white rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-grid-1x2-fill mr-3"></i>
                <span class="text-sm font-medium">Dashboard</span>
            </a>

            <div class="uppercase text-gray-400 text-xs font-bold mb-3 mt-6">Kelola Karyawan</div>
            <a href="{{ route('karyawan.index') }}" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-people-fill mr-3"></i>
                <span class="text-sm font-medium">Daftar Karyawan</span>
            </a>
            <a href="{{ route('karyawan.create') }}" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-person-plus-fill mr-3"></i>
                <span class="text-sm font-medium">Tambah Karyawan</span>
            </a>
            <a href="{{ route('jabatan.departemen.index') }}" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-diagram-3-fill mr-3"></i>
                <span class="text-sm font-medium">Jabatan & Departemen</span>
            </a>

            <div class="uppercase text-gray-400 text-xs font-bold mb-3 mt-6">Kehadiran & QR</div>
            <!-- LINK MANAJEMEN QR SUDAH DIARAHKAN KE HALAMAN UMUM -->
            <a href="{{ route('karyawan.qr.index') }}" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-qr-code-scan mr-3"></i>
                <span class="text-sm font-medium">Manajemen QR Code</span>
            </a>
            <a href="#" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-calendar-check mr-3"></i>
                <span class="text-sm font-medium">Log Kehadiran Harian</span>
            </a>

            <div class="uppercase text-gray-400 text-xs font-bold mb-3 mt-6">Manajemen Lembur</div>
            <a href="#" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-clock-history mr-3"></i>
                <span class="text-sm font-medium">Data Lembur</span>
            </a>
            <a href="#" class="flex items-center justify-between text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <div class="flex items-center">
                    <i class="bi bi-file-earmark-plus mr-3"></i>
                    <span class="text-sm font-medium">Pengajuan Lembur</span>
                </div>
                @if(isset($lemburMenunggu) && $lemburMenunggu > 0)
                    <span class="bg-rose-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $lemburMenunggu }}</span>
                @endif
            </a>

            <div class="uppercase text-gray-400 text-xs font-bold mb-3 mt-6">Akun</div>
            <a href="#" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-person-circle mr-3"></i>
                <span class="text-sm font-medium">Profil Saya</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="w-full flex items-center text-red-600 hover:bg-red-50 rounded-lg px-4 py-2.5 transition-colors">
                    <i class="bi bi-box-arrow-left mr-3"></i>
                    <span class="text-sm font-medium">Logout</span>
                </button>
            </form>
        </div>

        <div class="border-t border-gray-200 p-4 bg-gray-50">
            <div class="flex items-center">
                <div class="bg-blue-200 text-blue-800 rounded-full flex items-center justify-center mr-3 w-10 h-10 shrink-0 font-bold">HR</div>
                <div>
                    <div class="font-semibold text-sm text-gray-800">Admin HRD</div>
                    <div class="text-xs text-blue-600 font-medium">Role: hrd</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- =========================
         MAIN CONTENT
    ========================== -->
    <main class="grow flex flex-col min-w-0">
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-10">
            <div>
                <h1 class="font-bold text-xl text-gray-800">Dashboard HRD</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola Kehadiran & Pengajuan Lembur</p>
            </div>
            <div class="text-gray-500 text-sm flex items-center bg-gray-100 px-3 py-1.5 rounded-lg border border-gray-200">
                <i class="bi bi-calendar3 mr-2 text-blue-600"></i>
                <span class="font-medium">{{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </nav>

        <div class="p-6 grow overflow-y-auto">

            <!-- STATISTIC & ABSENSI HRD -->
            <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
                <!-- KOTAK ABSENSI HRD -->
                <div class="xl:col-span-1 md:col-span-3 bg-blue-600 rounded-xl shadow-sm text-white p-5 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-20">
                        <i class="bi bi-qr-code-scan text-6xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-blue-100 font-medium mb-1">Absensi Saya Hari Ini</p>
                        <div class="flex items-end gap-2">
                            <h3 id="realtime-clock" class="font-bold text-3xl tracking-wider">00:00:00</h3>
                            <span class="text-sm mb-1 text-blue-200">WIB</span>
                        </div>
                        
                        <div class="mt-3 flex gap-4 text-xs text-blue-100 font-medium">
                            <div>Masuk: <span class="font-bold text-white">{{ isset($presensiHrdHariIni) && $presensiHrdHariIni ? \Carbon\Carbon::parse($presensiHrdHariIni->jam_masuk)->format('H:i') : '--:--' }}</span></div>
                            <div>Pulang: <span class="font-bold text-white">{{ (isset($presensiHrdHariIni) && $presensiHrdHariIni && $presensiHrdHariIni->jam_pulang) ? \Carbon\Carbon::parse($presensiHrdHariIni->jam_pulang)->format('H:i') : '--:--' }}</span></div>
                        </div>
                    </div>
                    
                    <a href="{{ route('absensi.scan') }}" class="mt-4 bg-white text-blue-700 hover:bg-blue-50 py-2 px-4 rounded-lg text-sm font-bold flex items-center justify-center transition-colors shadow-sm">
                        <i class="bi bi-qr-code-scan mr-2"></i> Scan Absensi
                    </a>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Karyawan Aktif</p>
                            <h3 class="font-bold text-3xl text-gray-800 mt-1">{{ $totalKaryawanAktif ?? 0 }}</h3>
                        </div>
                        <div class="bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center w-12 h-12">
                            <i class="bi bi-people-fill text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Hadir Hari Ini</p>
                            <h3 class="font-bold text-3xl text-gray-800 mt-1">{{ $hadirHariIni ?? 0 }}</h3>
                        </div>
                        <div class="bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center w-12 h-12">
                            <i class="bi bi-person-check-fill text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Approval Lembur</p>
                            <h3 class="font-bold text-3xl text-gray-800 mt-1">{{ $lemburMenunggu ?? 0 }} <span class="text-sm text-gray-400 font-normal ml-1">Menunggu</span></h3>
                        </div>
                        <div class="bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center w-12 h-12">
                            <i class="bi bi-stopwatch-fill text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AREA DATA TABLES -->
            <div class="space-y-6">
                <!-- TABEL 1: PRESENSI HARI INI -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                        <div>
                            <h3 class="font-bold text-gray-800">Pantauan Kehadiran Hari Ini</h3>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-max">
                            <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider border-b border-gray-200">
                                <tr>
                                    <th class="px-5 py-3 font-semibold">Karyawan</th>
                                    <th class="px-5 py-3 font-semibold">Waktu Masuk & Pulang</th>
                                    <th class="px-5 py-3 font-semibold">Titik Lokasi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                                @if(isset($presensis) && count($presensis) > 0)
                                    @foreach($presensis as $prs)
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="px-5 py-3">
                                            <div class="font-semibold text-gray-900">{{ $prs->karyawan->nama_lengkap ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">{{ $prs->karyawan->departemen->nama_departemen ?? '-' }}</div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="text-xs font-medium text-gray-800"><span class="text-emerald-600 font-bold mr-1">Masuk:</span> {{ \Carbon\Carbon::parse($prs->jam_masuk)->format('H:i') }} WIB</div>
                                            <div class="text-xs font-medium text-gray-400 mt-0.5"><span class="text-rose-400 font-bold mr-1">Pulang:</span> {{ $prs->jam_pulang ? \Carbon\Carbon::parse($prs->jam_pulang)->format('H:i') . ' WIB' : '-' }}</div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="text-xs space-y-1.5">
                                                <!-- Masuk (In) -->
                                                <div>
                                                    <span class="font-semibold text-emerald-600">In:</span> 
                                                    @if($prs->latitude_masuk && $prs->longitude_masuk)
                                                        <a href="https://maps.google.com/?q={{ $prs->latitude_masuk }},{{ $prs->longitude_masuk }}" target="_blank" class="text-blue-600 hover:underline font-medium">
                                                            @if($prs->alamat_masuk && $prs->nama_jalan_masuk)
                                                                {{ $prs->alamat_masuk }} {{ $prs->nama_jalan_masuk }}
                                                            @else
                                                                {{ $prs->alamat_masuk ?? ($prs->nama_jalan_masuk ?? $prs->latitude_masuk.', '.$prs->longitude_masuk) }}
                                                            @endif
                                                        </a>
                                                    @else
                                                        <span class="text-gray-400 italic">Tidak ada lokasi</span>
                                                    @endif
                                                </div>
                                                <!-- Pulang (Out) -->
                                                <div>
                                                    <span class="font-semibold text-rose-500">Out:</span> 
                                                    @if(isset($prs->latitude_pulang) && $prs->latitude_pulang)
                                                        <a href="https://maps.google.com/?q={{ $prs->latitude_pulang }},{{ $prs->longitude_pulang }}" target="_blank" class="text-blue-600 hover:underline font-medium">
                                                            @if(isset($prs->alamat_pulang) && $prs->alamat_pulang && isset($prs->nama_jalan_pulang) && $prs->nama_jalan_pulang)
                                                                {{ $prs->alamat_pulang }} {{ $prs->nama_jalan_pulang }}
                                                            @else
                                                                {{ $prs->alamat_pulang ?? ($prs->nama_jalan_pulang ?? $prs->latitude_pulang.', '.$prs->longitude_pulang) }}
                                                            @endif
                                                        </a>
                                                    @else
                                                        <span class="text-gray-400 italic">Belum absen pulang</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="3" class="px-5 py-4 text-center text-gray-500 text-sm">Belum ada data kehadiran tercatat hari ini.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TABEL 2: PENGAJUAN LEMBUR -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                        <div>
                            <h3 class="font-bold text-gray-800">Pengajuan Lembur (Menunggu Approval)</h3>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-max">
                            <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider border-b border-gray-200">
                                <tr>
                                    <th class="px-5 py-3 font-semibold">Karyawan</th>
                                    <th class="px-5 py-3 font-semibold">Waktu Lembur</th>
                                    <th class="px-5 py-3 font-semibold">Catatan Pekerjaan</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                                @if(isset($lemburs) && count($lemburs) > 0)
                                    @foreach($lemburs as $lmb)
                                    <tr class="hover:bg-amber-50/30 transition-colors">
                                        <td class="px-5 py-3">
                                            <div class="font-semibold text-gray-900">{{ $lmb->karyawan->nama_lengkap ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">{{ $lmb->karyawan->departemen->nama_departemen ?? '-' }}</div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($lmb->tanggal)->format('d M Y') }}</div>
                                            <div class="text-xs font-semibold text-blue-600 mt-0.5"><i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($lmb->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($lmb->jam_selesai)->format('H:i') }} ({{ $lmb->durasi_jam }} Jam)</div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <p class="text-xs text-gray-600 line-clamp-2">{{ $lmb->catatan }}</p>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="3" class="px-5 py-4 text-center text-gray-500 text-sm">Tidak ada pengajuan lembur yang menunggu.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>