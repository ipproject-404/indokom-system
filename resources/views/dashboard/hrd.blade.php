<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard HRD - Indokom System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fade-in-up 0.4s ease-out; }
        #sidebar::-webkit-scrollbar { width: 6px; }
        #sidebar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
</head>

<body class="bg-slate-50">

<div class="flex min-h-screen bg-slate-50">

    <!-- =========================
         SIDEBAR (POSISI TETAP)
    ========================== -->
    <aside id="sidebar" class="bg-white border-r border-gray-200 flex flex-col w-[260px] shrink-0 sticky top-0 h-screen">

        <!-- BRANDING / LOGO -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center">
                <div class="bg-blue-600 text-white rounded-lg flex items-center justify-center mr-3 w-[42px] h-[42px] shrink-0">
                    <i class="bi bi-building-fill text-xl"></i>
                </div>
                <div>
                    <div class="font-bold text-blue-600">Indokom System</div>
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
            <a href="{{ route('karyawan.qr.index') }}" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-qr-code-scan mr-3"></i>
                <span class="text-sm font-medium">Manajemen QR Code</span>
            </a>
            <a href="{{ route('kehadiran.log') }}" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-calendar-check mr-3"></i>
                <span class="text-sm font-medium">Log Kehadiran Harian</span>
            </a>
            <a href="{{ route('lembur.pengajuan') }}" class="flex items-center justify-between text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <div class="flex items-center">
                    <i class="bi bi-file-earmark-plus mr-3"></i>
                    <span class="text-sm font-medium">Pengajuan Lembur</span>
                </div>
                @if(isset($lemburMenunggu) && $lemburMenunggu > 0)
                    <span class="bg-rose-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $lemburMenunggu }}</span>
                @endif
            </a>

            <div class="uppercase text-gray-400 text-xs font-bold mb-3 mt-6">Akun</div>
            <a href="{{ route('profile.index') }}" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
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

        <!-- INFO USER -->
        @php
            $userKaryawan = Auth::user()->karyawan ?? null;
            $namaTampil = $userKaryawan->nama_lengkap ?? (Auth::user()->name ?? '-');
            $jabatanTampil = optional($userKaryawan->jabatan ?? null)->nama_jabatan ?? '-';
            $departemenTampil = optional($userKaryawan->departemen ?? null)->nama_departemen ?? '-';
        @endphp
        <div class="border-t border-gray-200 p-4 bg-gray-50">
            <div class="flex items-center">
                <div class="bg-blue-600 text-white rounded-full flex items-center justify-center mr-3 w-10 h-10 shrink-0 font-bold shadow-sm">
                    {{ strtoupper(substr($namaTampil, 0, 2)) }}
                </div>
                <div class="overflow-hidden">
                    <div class="font-bold text-sm text-gray-800 truncate" title="{{ $namaTampil }}">
                        {{ $namaTampil }}
                    </div>
                    <div class="text-xs text-blue-600 font-semibold truncate" title="{{ $jabatanTampil }} • {{ $departemenTampil }}">
                        {{ $jabatanTampil }} &bull; {{ $departemenTampil }}
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- =========================
         MAIN CONTENT
    ========================== -->
    <main class="grow flex flex-col min-w-0">

        <!-- HEADER BAR -->
        <nav class="bg-white/80 backdrop-blur-md border-b border-slate-200 px-6 py-4 flex justify-between items-center sticky top-0 z-20 shadow-sm">
            <div>
                <h1 class="font-bold text-xl text-slate-900 tracking-tight">Dashboard HRD</h1>
                <p class="text-sm text-slate-500 mt-0.5">Kelola Kehadiran & Pengajuan Lembur</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden md:flex items-center gap-2 bg-slate-100 px-3.5 py-2 rounded-lg border border-slate-200 text-sm">
                    <i class="bi bi-calendar3 text-blue-600"></i>
                    <span class="font-semibold text-slate-700">{{ now()->translatedFormat('d F Y') }}</span>
                </div>
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold shadow-md">
                    {{ strtoupper(substr($namaTampil, 0, 2)) }}
                </div>
            </div>
        </nav>

        <div class="p-6 grow overflow-y-auto">

            <!-- HERO WELCOME BANNER (Tanpa Tombol) -->
            <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 rounded-2xl p-6 mb-6 text-white shadow-xl shadow-blue-600/15 relative overflow-hidden animate-fade-in-up">
                <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
                    <i class="bi bi-people-fill text-[180px]"></i>
                </div>
                <div class="absolute top-0 right-20 w-40 h-40 bg-white/5 rounded-full -mt-20 pointer-events-none"></div>

                <div class="relative z-10">
                    <p class="text-blue-100 text-sm font-medium mb-1">
                        <i class="bi bi-sun-fill mr-1"></i> Selamat datang kembali,
                    </p>
                    <h2 class="text-2xl font-black tracking-tight">{{ $namaTampil }}</h2>
                    <p class="text-blue-100 text-sm mt-1.5">
                        <i class="bi bi-briefcase-fill mr-1 opacity-70"></i>
                        {{ $jabatanTampil }} &bull; {{ $departemenTampil }}
                    </p>
                </div>
            </div>

            <!-- STATISTIC CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

                <!-- Karyawan Aktif -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md hover:border-blue-300 transition-all group animate-fade-in-up">
                    <div class="flex justify-between items-start mb-3">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <span class="text-[10px] font-extrabold bg-blue-100 text-blue-700 px-2 py-1 rounded-md uppercase tracking-wider">Total</span>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Karyawan Aktif</p>
                    <h3 class="font-black text-3xl text-slate-900">{{ $totalKaryawanAktif ?? 0 }}</h3>
                    <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1">
                        <i class="bi bi-check-circle-fill text-emerald-500"></i> Terdaftar di sistem
                    </p>
                </div>

                <!-- Hadir Hari Ini -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all group animate-fade-in-up">
                    <div class="flex justify-between items-start mb-3">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <span class="text-[10px] font-extrabold bg-emerald-100 text-emerald-700 px-2 py-1 rounded-md uppercase tracking-wider">Hari Ini</span>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Hadir Hari Ini</p>
                    <h3 class="font-black text-3xl text-slate-900">{{ $hadirHariIni ?? 0 }}</h3>
                    <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1">
                        <i class="bi bi-clock-history text-emerald-500"></i> Sudah check-in
                    </p>
                </div>

                <!-- Approval Lembur -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md hover:border-amber-300 transition-all group animate-fade-in-up">
                    <div class="flex justify-between items-start mb-3">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                            <i class="bi bi-stopwatch-fill"></i>
                        </div>
                        @if(isset($lemburMenunggu) && $lemburMenunggu > 0)
                            <span class="text-[10px] font-extrabold bg-rose-500 text-white px-2 py-1 rounded-md uppercase tracking-wider animate-pulse">Pending</span>
                        @else
                            <span class="text-[10px] font-extrabold bg-emerald-100 text-emerald-700 px-2 py-1 rounded-md uppercase tracking-wider">Clear</span>
                        @endif
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Approval Lembur</p>
                    <h3 class="font-black text-3xl text-slate-900">{{ $lemburMenunggu ?? 0 }}</h3>
                    <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1">
                        <i class="bi bi-hourglass-split text-amber-500"></i> Menunggu persetujuan
                    </p>
                </div>
            </div>

            <!-- AREA DATA TABLES -->
            <div class="grid grid-cols-1 gap-6">

                <!-- TABEL 1: PANTAUAN KEHADIRAN HARI INI -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden animate-fade-in-up">
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-slate-50 to-white">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                <i class="bi bi-eye-fill"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800">Pantauan Kehadiran Hari Ini</h3>
                                <p class="text-xs text-slate-500">Data real-time karyawan yang sudah check-in</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg border border-blue-100">
                            {{ isset($presensis) ? count($presensis) : 0 }} data
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-max">
                            <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-3 font-bold">Karyawan</th>
                                    <th class="px-6 py-3 font-bold">Waktu Masuk & Pulang</th>
                                    <th class="px-6 py-3 font-bold">Titik Lokasi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-slate-700 divide-y divide-slate-100">
                                @if(isset($presensis) && count($presensis) > 0)
                                    @foreach($presensis as $prs)
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-500 text-white text-xs font-bold flex items-center justify-center shadow-sm">
                                                    {{ strtoupper(substr($prs->karyawan->nama_lengkap ?? 'X', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-slate-900">{{ $prs->karyawan->nama_lengkap ?? '-' }}</div>
                                                    <div class="text-xs text-slate-500">{{ $prs->karyawan->departemen->nama_departemen ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 px-2 py-1 rounded-md text-xs font-bold">
                                                    <i class="bi bi-box-arrow-in-right"></i> {{ \Carbon\Carbon::parse($prs->jam_masuk)->format('H:i') }}
                                                </span>
                                                <span class="text-xs text-slate-400">s/d</span>
                                                <span class="inline-flex items-center gap-1 {{ $prs->jam_pulang ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-400' }} px-2 py-1 rounded-md text-xs font-bold">
                                                    <i class="bi bi-box-arrow-left"></i> {{ $prs->jam_pulang ? \Carbon\Carbon::parse($prs->jam_pulang)->format('H:i') : '--:--' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs space-y-1.5">
                                                <!-- Masuk -->
                                                <div class="flex items-start gap-1.5">
                                                    <span class="font-bold text-emerald-600 shrink-0">In:</span>
                                                    @if($prs->latitude_masuk && $prs->longitude_masuk)
                                                        <a href="https://maps.google.com/?q={{ $prs->latitude_masuk }},{{ $prs->longitude_masuk }}" target="_blank" class="text-blue-600 hover:underline font-medium truncate max-w-[220px]">
                                                            @if($prs->alamat_masuk && $prs->nama_jalan_masuk)
                                                                {{ $prs->alamat_masuk }} {{ $prs->nama_jalan_masuk }}
                                                            @else
                                                                {{ $prs->alamat_masuk ?? ($prs->nama_jalan_masuk ?? $prs->latitude_masuk.', '.$prs->longitude_masuk) }}
                                                            @endif
                                                        </a>
                                                        @if($prs->jarak_masuk_meter)
                                                            <span class="text-slate-400 shrink-0">({{ $prs->jarak_masuk_meter }}m)</span>
                                                        @endif

                                                        @if($prs->status_radius_masuk == 'dalam_radius')
                                                            <span class="bg-emerald-100 text-emerald-700 text-[10px] px-1.5 py-0.5 rounded font-bold shrink-0">Dalam</span>
                                                        @elseif($prs->status_radius_masuk)
                                                            <span class="bg-rose-100 text-rose-700 text-[10px] px-1.5 py-0.5 rounded font-bold shrink-0">Luar</span>
                                                        @else
                                                            <span class="bg-slate-100 text-slate-500 text-[10px] px-1.5 py-0.5 rounded font-bold shrink-0">N/A</span>
                                                        @endif
                                                    @else
                                                        <span class="text-slate-400 italic">Tidak ada lokasi</span>
                                                    @endif
                                                </div>

                                                <!-- Pulang -->
                                                <div class="flex items-start gap-1.5">
                                                    <span class="font-bold text-rose-500 shrink-0">Out:</span>
                                                    @if(isset($prs->latitude_pulang) && $prs->latitude_pulang)
                                                        <a href="https://maps.google.com/?q={{ $prs->latitude_pulang }},{{ $prs->longitude_pulang }}" target="_blank" class="text-blue-600 hover:underline font-medium truncate max-w-[220px]">
                                                            @if(isset($prs->alamat_pulang) && $prs->alamat_pulang && isset($prs->nama_jalan_pulang) && $prs->nama_jalan_pulang)
                                                                {{ $prs->alamat_pulang }} {{ $prs->nama_jalan_pulang }}
                                                            @else
                                                                {{ $prs->alamat_pulang ?? ($prs->nama_jalan_pulang ?? $prs->latitude_pulang.', '.$prs->longitude_pulang) }}
                                                            @endif
                                                        </a>
                                                        @if(isset($prs->jarak_pulang_meter) && $prs->jarak_pulang_meter)
                                                            <span class="text-slate-400 shrink-0">({{ $prs->jarak_pulang_meter }}m)</span>
                                                        @endif

                                                        @if(isset($prs->status_radius_pulang) && $prs->status_radius_pulang == 'dalam_radius')
                                                            <span class="bg-emerald-100 text-emerald-700 text-[10px] px-1.5 py-0.5 rounded font-bold shrink-0">Dalam</span>
                                                        @elseif(isset($prs->status_radius_pulang) && $prs->status_radius_pulang)
                                                            <span class="bg-rose-100 text-rose-700 text-[10px] px-1.5 py-0.5 rounded font-bold shrink-0">Luar</span>
                                                        @else
                                                            <span class="bg-slate-100 text-slate-500 text-[10px] px-1.5 py-0.5 rounded font-bold shrink-0">N/A</span>
                                                        @endif
                                                    @else
                                                        <span class="text-slate-400 italic">Belum absen pulang</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center text-slate-400">
                                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                                                    <i class="bi bi-inbox text-3xl"></i>
                                                </div>
                                                <p class="font-semibold text-slate-600">Belum ada kehadiran hari ini</p>
                                                <p class="text-xs text-slate-400 mt-1">Data akan muncul saat karyawan melakukan scan</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TABEL 2: PENGAJUAN LEMBUR -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden animate-fade-in-up">
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-slate-50 to-white">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800">Pengajuan Lembur</h3>
                                <p class="text-xs text-slate-500">Menunggu persetujuan Anda</p>
                            </div>
                        </div>
                        <a href="{{ route('lembur.pengajuan') }}" class="text-xs font-bold bg-amber-50 text-amber-700 hover:bg-amber-100 px-3 py-1.5 rounded-lg border border-amber-100 transition-colors flex items-center gap-1">
                            Lihat Semua <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-max">
                            <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-3 font-bold">Karyawan</th>
                                    <th class="px-6 py-3 font-bold">Waktu Lembur</th>
                                    <th class="px-6 py-3 font-bold">Catatan Pekerjaan</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-slate-700 divide-y divide-slate-100">
                                @if(isset($lemburs) && count($lemburs) > 0)
                                    @foreach($lemburs as $lmb)
                                    <tr class="hover:bg-amber-50/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-amber-500 to-orange-500 text-white text-xs font-bold flex items-center justify-center shadow-sm">
                                                    {{ strtoupper(substr($lmb->karyawan->nama_lengkap ?? 'X', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-slate-900">{{ $lmb->karyawan->nama_lengkap ?? '-' }}</div>
                                                    <div class="text-xs text-slate-500">{{ $lmb->karyawan->departemen->nama_departemen ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-800 text-xs mb-1 flex items-center gap-1">
                                                <i class="bi bi-calendar2-event text-blue-500"></i>
                                                {{ \Carbon\Carbon::parse($lmb->tanggal)->translatedFormat('d M Y') }}
                                            </div>
                                            <div class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 px-2 py-1 rounded-md text-[11px] font-bold border border-slate-200">
                                                <i class="bi bi-clock"></i>
                                                {{ \Carbon\Carbon::parse($lmb->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($lmb->jam_selesai)->format('H:i') }}
                                                <span class="text-blue-600">({{ $lmb->durasi_jam }} Jam)</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-xs text-slate-600 line-clamp-2 max-w-xs leading-relaxed">{{ $lmb->catatan }}</p>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center text-slate-400">
                                                <div class="w-16 h-16 rounded-full bg-emerald-50 flex items-center justify-center mb-3">
                                                    <i class="bi bi-check-circle-fill text-3xl text-emerald-500"></i>
                                                </div>
                                                <p class="font-semibold text-slate-600">Semua beres!</p>
                                                <p class="text-xs text-slate-400 mt-1">Tidak ada pengajuan lembur yang menunggu</p>
                                            </div>
                                        </td>
                                    </tr>
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