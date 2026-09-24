<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard HRD - Sistem Presensi & Kinerja</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Menggunakan Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>

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
            <!-- LINK DIPERBARUI -->
            <a href="{{ route('dashboard.hrd') }}" class="flex items-center bg-blue-600 text-white rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-grid-1x2-fill mr-3"></i>
                <span class="text-sm font-medium">Dashboard</span>
            </a>

            <!-- Kelola Karyawan -->
            <div class="uppercase text-gray-400 text-xs font-bold mb-3 mt-6">Kelola Karyawan</div>
            <!-- LINK DIPERBARUI -->
            <a href="{{ route('karyawan.index') }}" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-people-fill mr-3"></i>
                <span class="text-sm font-medium">Daftar Karyawan</span>
            </a>
            <!-- LINK DIPERBARUI -->
            <a href="{{ route('karyawan.create') }}" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-person-plus-fill mr-3"></i>
                <span class="text-sm font-medium">Tambah Karyawan</span>
            </a>
            <a href="#" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-diagram-3-fill mr-3"></i>
                <span class="text-sm font-medium">Jabatan & Departemen</span>
            </a>

            <!-- Manajemen QR & Presensi -->
            <div class="uppercase text-gray-400 text-xs font-bold mb-3 mt-6">Kehadiran & QR</div>
            <a href="#" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-qr-code-scan mr-3"></i>
                <span class="text-sm font-medium">Manajemen QR Code</span>
            </a>
            <a href="#" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-calendar-check mr-3"></i>
                <span class="text-sm font-medium">Log Kehadiran Harian</span>
            </a>

            <!-- Manajemen Lembur -->
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

            <!-- Akun -->
            <div class="uppercase text-gray-400 text-xs font-bold mb-3 mt-6">Akun</div>
            <a href="#" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-person-circle mr-3"></i>
                <span class="text-sm font-medium">Profil Saya</span>
            </a>
            <a href="#" class="flex items-center text-red-600 hover:bg-red-50 rounded-lg px-4 py-2.5 mb-1 transition-colors mt-2">
                <i class="bi bi-box-arrow-left mr-3"></i>
                <span class="text-sm font-medium">Logout</span>
            </a>

            <div class="mt-6 p-3 bg-blue-50 rounded-xl border border-blue-100">
                <div class="flex items-center text-blue-800 text-xs font-bold mb-1">
                    <i class="bi bi-arrow-repeat mr-1.5"></i> Info Integrasi Data
                </div>
                <p class="text-[10px] text-blue-600 leading-tight">Data kehadiran & lembur yang disetujui akan diolah otomatis oleh sistem Penggajian.</p>
            </div>
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

            <!-- STATISTIC CARDS DINAMIS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
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
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
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
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
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
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">QR (barcode_uid)</p>
                            <h3 class="font-bold text-3xl text-gray-800 mt-1">{{ $qrTercetak ?? 0 }}<span class="text-sm text-gray-400 font-normal ml-1">/ {{ $totalKaryawanAktif ?? 0 }}</span></h3>
                        </div>
                        <div class="bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center w-12 h-12">
                            <i class="bi bi-qr-code text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AREA DATA TABLES -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- KOLOM KIRI: TABEL DATA (Diperlebar menjadi 2 kolom grid) -->
                <div class="lg:col-span-2 space-y-6">
                    
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
                                        <th class="px-5 py-3 font-semibold text-center">Status Verifikasi</th>
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
                                            <td class="px-5 py-3 text-center">
                                                @if($prs->status_verifikasi == 'disetujui')
                                                    <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-md text-[11px] font-bold border border-emerald-200 uppercase tracking-wide">DISETUJUI</span>
                                                @elseif($prs->status_verifikasi == 'ditolak')
                                                    <span class="bg-rose-100 text-rose-700 px-2.5 py-1 rounded-md text-[11px] font-bold border border-rose-200 uppercase tracking-wide">DITOLAK</span>
                                                @else
                                                    <span class="bg-amber-100 text-amber-700 px-2.5 py-1 rounded-md text-[11px] font-bold border border-amber-200 uppercase tracking-wide">MENUNGGU</span>
                                                @endif
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

                <!-- KOLOM KANAN: MENU CEPAT -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
                        <div class="px-5 py-4 border-b border-gray-100 bg-white">
                            <h3 class="font-bold text-gray-800">Aksi Cepat HRD</h3>
                        </div>
                        <div class="p-5">
                            
                            <!-- Input Karyawan (LINK DIPERBARUI) -->
                            <a href="{{ route('karyawan.create') }}" class="flex items-center p-3 mb-3 border border-gray-200 rounded-xl hover:border-blue-600 hover:bg-blue-50/50 transition-all shadow-sm group">
                                <div class="bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center w-11 h-11 mr-4 shrink-0 transition-colors group-hover:bg-blue-600 group-hover:text-white"><i class="bi bi-person-plus-fill text-lg"></i></div>
                                <div>
                                    <div class="font-semibold text-gray-800 text-sm group-hover:text-blue-700">Input Karyawan Baru</div>
                                    <div class="text-xs text-gray-500 mt-0.5">Daftarkan data diri ke database</div>
                                </div>
                            </a>
                            
                            <!-- Generate QR -->
                            <a href="#" class="flex items-center p-3 mb-3 border border-gray-200 rounded-xl hover:border-indigo-600 hover:bg-indigo-50/50 transition-all shadow-sm group">
                                <div class="bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center w-11 h-11 mr-4 shrink-0 transition-colors group-hover:bg-indigo-600 group-hover:text-white"><i class="bi bi-qr-code-scan text-lg"></i></div>
                                <div>
                                    <div class="font-semibold text-gray-800 text-sm group-hover:text-indigo-700">Generate QR Massal</div>
                                    <div class="text-xs text-gray-500 mt-0.5">Buat barcode_uid untuk sistem</div>
                                </div>
                            </a>
                            
                            <!-- Download/Cetak QR -->
                            <a href="#" class="flex items-center p-3 mb-3 border border-emerald-200 rounded-xl hover:border-emerald-500 hover:bg-emerald-50/50 transition-all shadow-sm group">
                                <div class="bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center w-11 h-11 mr-4 shrink-0 transition-colors group-hover:bg-emerald-500 group-hover:text-white"><i class="bi bi-printer-fill text-lg"></i></div>
                                <div>
                                    <div class="font-semibold text-gray-800 text-sm group-hover:text-emerald-700">Download / Cetak QR</div>
                                    <div class="text-xs text-gray-500 mt-0.5">Cetak ID Card & Barcode QR</div>
                                </div>
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>