<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Karyawan - Indokom System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fade-in 0.3s ease-out; }
    </style>
</head>
<body class="bg-slate-50 pb-10">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside id="sidebar" class="bg-white border-r border-gray-200 flex flex-col w-[260px] shrink-0 sticky top-0 h-screen">
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
            <a href="{{ route('dashboard.hrd') }}" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-grid-1x2-fill mr-3"></i>
                <span class="text-sm font-medium">Dashboard</span>
            </a>

            <div class="uppercase text-gray-400 text-xs font-bold mb-3 mt-6">Kelola Karyawan</div>
            <a href="{{ route('karyawan.index') }}" class="flex items-center bg-blue-600 text-white rounded-lg px-4 py-2.5 mb-1 transition-colors">
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
                    <div class="font-bold text-sm text-gray-800 truncate">{{ $namaTampil }}</div>
                    <div class="text-xs text-blue-600 font-semibold truncate">{{ $jabatanTampil }} &bull; {{ $departemenTampil }}</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="grow flex flex-col min-w-0">

        <!-- HEADER -->
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center">
                <a href="{{ route('karyawan.index') }}" class="text-gray-500 hover:bg-gray-100 p-2 rounded-lg mr-3 transition-colors">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="font-bold text-xl text-gray-800">Detail Karyawan</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Informasi lengkap & riwayat kehadiran</p>
                </div>
            </div>
            <a href="{{ route('karyawan.edit', $karyawan->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm">
                <i class="bi bi-pencil-square mr-1"></i> Edit Karyawan
            </a>
        </nav>

        <div class="p-6 grow overflow-y-auto">

            <!-- ALERTS -->
            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 px-4 py-4 rounded-lg mb-6 shadow-sm animate-fade-in">
                <div class="flex items-center text-emerald-700">
                    <i class="bi bi-check-circle-fill mr-2 text-lg"></i>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            </div>
            @endif
            @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 px-4 py-4 rounded-lg mb-6 shadow-sm animate-fade-in">
                <div class="flex items-center text-rose-700">
                    <i class="bi bi-exclamation-triangle-fill mr-2 text-lg"></i>
                    <span class="font-bold">{{ session('error') }}</span>
                </div>
            </div>
            @endif

            <!-- HERO BANNER PROFIL -->
            <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 rounded-2xl p-6 mb-6 text-white shadow-xl shadow-blue-600/15 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
                    <i class="bi bi-person-badge text-[180px]"></i>
                </div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center gap-6">
                    <!-- Avatar Besar -->
                    <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-sm border-2 border-white/40 flex items-center justify-center text-3xl font-black shadow-inner shrink-0">
                        {{ strtoupper(substr($karyawan->nama_lengkap, 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <h2 class="text-2xl font-black tracking-tight">{{ $karyawan->nama_lengkap }}</h2>
                            @if($karyawan->status == 'aktif')
                                <span class="bg-emerald-500 text-white text-[10px] font-extrabold px-2.5 py-0.5 rounded-full shadow-sm">Aktif</span>
                            @else
                                <span class="bg-rose-500 text-white text-[10px] font-extrabold px-2.5 py-0.5 rounded-full shadow-sm">Nonaktif</span>
                            @endif
                        </div>
                        <p class="text-blue-100 text-sm font-medium">
                            <i class="bi bi-briefcase-fill mr-1 opacity-70"></i>
                            {{ $karyawan->jabatan->nama_jabatan ?? '-' }}
                            &bull; 
                            {{ $karyawan->departemen->nama_departemen ?? '-' }}
                            @if($karyawan->departemen->divisi)
                                &bull; {{ $karyawan->departemen->divisi->nama_divisi }}
                            @endif
                        </p>
                        <div class="flex flex-wrap gap-3 mt-3 text-[11px] text-blue-100 font-medium">
                            <span class="inline-flex items-center bg-white/15 rounded-md px-2 py-1">
                                <i class="bi bi-upc-scan mr-1"></i> {{ $karyawan->nik_kerja }}
                            </span>
                            <span class="inline-flex items-center bg-white/15 rounded-md px-2 py-1">
                                <i class="bi bi-building mr-1"></i> {{ $karyawan->perusahaan->nama_perusahaan ?? '-' }}
                            </span>
                            @if($karyawan->tipeKaryawan)
                            <span class="inline-flex items-center bg-white/15 rounded-md px-2 py-1">
                                <i class="bi bi-tag-fill mr-1"></i> {{ $karyawan->tipeKaryawan->nama }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRID UTAMA -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- KOLOM KIRI -->
                <div class="lg:col-span-1 space-y-6">

                    <!-- AKUN LOGIN -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6" x-data="{ showPwd: false }">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-3 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                <i class="bi bi-person-lock"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-sm">Akun Login Sistem</h3>
                        </div>

                        @if($akun)
                            <div class="space-y-4">
                                <div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Username</p>
                                    <p class="font-bold text-blue-700 text-sm bg-blue-50 py-2 px-3 rounded-lg flex items-center justify-between">
                                        <span>{{ $akun->username }}</span>
                                        <button onclick="navigator.clipboard.writeText('{{ $akun->username }}'); this.innerHTML='<i class=\'bi bi-check2\'></i>'" 
                                                class="text-blue-600 hover:text-blue-800" title="Copy">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </p>
                                </div>

                                <!-- Password dengan Toggle Mata -->
                                <div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Password (Default)</p>
                                    <div class="flex items-center justify-between bg-slate-50 py-2 px-3 rounded-lg border border-slate-200">
                                        <p class="font-mono font-bold text-sm tracking-widest">
                                            <span x-show="!showPwd" class="text-slate-700">••••••••••</span>
                                            <span x-show="showPwd" class="text-amber-600">passwor123</span>
                                        </p>
                                        <button @click="showPwd = !showPwd" type="button" class="text-slate-400 hover:text-blue-600 transition-colors" title="Lihat/Sembunyikan Password">
                                            <i class="bi" :class="showPwd ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i>
                                        </button>
                                    </div>
                                    <p class="text-[10px] text-amber-600 mt-1.5 flex items-start gap-1">
                                        <i class="bi bi-info-circle-fill mt-0.5"></i>
                                        <span>Hanya default password. Jika karyawan sudah ganti sendiri, password ini tidak berlaku.</span>
                                    </p>
                                </div>

                                <!-- Tombol Reset Password -->
                                <button onclick="document.getElementById('modalResetPwd').classList.remove('hidden')" 
                                        class="w-full bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-4 py-2.5 rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-2">
                                    <i class="bi bi-key-fill"></i> Reset Password
                                </button>
                            </div>
                        @else
                            <div class="text-center py-6 bg-slate-50 rounded-lg border border-dashed border-slate-300">
                                <i class="bi bi-person-x text-3xl text-slate-400 block mb-2"></i>
                                <p class="text-xs text-slate-500 font-medium">Akun login belum dibuat</p>
                            </div>
                        @endif
                    </div>

                    <!-- STATISTIK KEHADIRAN -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-3 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-sm">Ringkasan Kehadiran</h3>
                        </div>

                        @php
                            $totalHadir = \App\Models\Presensi::where('karyawan_id', $karyawan->id)->count();
                            $bulanIni = \App\Models\Presensi::where('karyawan_id', $karyawan->id)
                                        ->whereMonth('tanggal', now()->month)
                                        ->whereYear('tanggal', now()->year)
                                        ->count();
                        @endphp

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-emerald-50 rounded-lg p-3 border border-emerald-100">
                                <p class="text-[10px] text-emerald-700 font-bold uppercase tracking-wider">Total Hadir</p>
                                <p class="font-black text-2xl text-emerald-700 mt-0.5">{{ $totalHadir }}</p>
                                <p class="text-[10px] text-emerald-600 font-medium">semua waktu</p>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                                <p class="text-[10px] text-blue-700 font-bold uppercase tracking-wider">Bulan Ini</p>
                                <p class="font-black text-2xl text-blue-700 mt-0.5">{{ $bulanIni }}</p>
                                <p class="text-[10px] text-blue-600 font-medium">{{ now()->translatedFormat('F Y') }}</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- KOLOM KANAN -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- BIODATA LENGKAP -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                    <i class="bi bi-person-lines-fill"></i>
                                </div>
                                <h3 class="font-bold text-slate-800 text-sm">Biodata Lengkap</h3>
                            </div>
                        </div>

                        <!-- Data Pribadi -->
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-3">📋 Data Pribadi</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">NIK KTP</p>
                                <p class="font-bold text-slate-800 text-sm">{{ $karyawan->nik_ktp ?? '-' }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">NIK Kerja</p>
                                <p class="font-bold text-slate-800 text-sm">{{ $karyawan->nik_kerja ?? '-' }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Tempat, Tanggal Lahir</p>
                                <p class="font-bold text-slate-800 text-sm">
                                    {{ $karyawan->tempat_lahir ?? '-' }}, 
                                    {{ $karyawan->tanggal_lahir ? \Carbon\Carbon::parse($karyawan->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                                </p>
                                @if($karyawan->tanggal_lahir)
                                    <p class="text-[11px] text-blue-600 font-bold mt-1">
                                        <i class="bi bi-cake2"></i> Umur: {{ \Carbon\Carbon::parse($karyawan->tanggal_lahir)->age }} tahun
                                    </p>
                                @endif
                            </div>
                            <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Jenis Kelamin</p>
                                <p class="font-bold text-slate-800 text-sm">
                                    @if($karyawan->jenis_kelamin == 'L')
                                        <i class="bi bi-gender-male text-blue-600"></i> Laki-laki
                                    @else
                                        <i class="bi bi-gender-female text-pink-500"></i> Perempuan
                                    @endif
                                </p>
                            </div>
                            <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">No. Handphone</p>
                                <p class="font-bold text-slate-800 text-sm">
                                    <i class="bi bi-telephone-fill text-emerald-500"></i> {{ $karyawan->no_hp ?? '-' }}
                                </p>
                            </div>
                            <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Pendidikan</p>
                                <p class="font-bold text-slate-800 text-sm">
                                    <i class="bi bi-mortarboard-fill text-indigo-500"></i> {{ $karyawan->pendidikan ?? '-' }}
                                </p>
                            </div>
                            <div class="bg-slate-50 rounded-lg p-3 border border-slate-100 md:col-span-2">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Alamat Lengkap</p>
                                <p class="font-medium text-slate-800 text-sm leading-relaxed">{{ $karyawan->alamat ?? '-' }}</p>
                            </div>
                        </div>

                        <!-- Data Kepegawaian -->
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-3">💼 Data Kepegawaian</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                                <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider mb-1">Perusahaan</p>
                                <p class="font-bold text-slate-800 text-sm">
                                    <i class="bi bi-building-fill text-blue-600"></i> {{ $karyawan->perusahaan->nama_perusahaan ?? '-' }}
                                </p>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-3 border border-purple-100">
                                <p class="text-[10px] text-purple-600 font-bold uppercase tracking-wider mb-1">Divisi</p>
                                <p class="font-bold text-slate-800 text-sm">
                                    <i class="bi bi-diagram-2-fill text-purple-600"></i> {{ $karyawan->departemen->divisi->nama_divisi ?? '-' }}
                                </p>
                            </div>
                            <div class="bg-indigo-50 rounded-lg p-3 border border-indigo-100">
                                <p class="text-[10px] text-indigo-600 font-bold uppercase tracking-wider mb-1">Departemen</p>
                                <p class="font-bold text-slate-800 text-sm">
                                    <i class="bi bi-building-fill text-indigo-600"></i> {{ $karyawan->departemen->nama_departemen ?? '-' }}
                                </p>
                            </div>
                            <div class="bg-amber-50 rounded-lg p-3 border border-amber-100">
                                <p class="text-[10px] text-amber-600 font-bold uppercase tracking-wider mb-1">Jabatan</p>
                                <p class="font-bold text-slate-800 text-sm">
                                    <i class="bi bi-person-badge-fill text-amber-600"></i> {{ $karyawan->jabatan->nama_jabatan ?? '-' }}
                                </p>
                            </div>
                            <div class="bg-rose-50 rounded-lg p-3 border border-rose-100">
                                <p class="text-[10px] text-rose-600 font-bold uppercase tracking-wider mb-1">Tipe Karyawan</p>
                                <p class="font-bold text-slate-800 text-sm">
                                    <i class="bi bi-tag-fill text-rose-600"></i> {{ $karyawan->tipeKaryawan->nama ?? '-' }}
                                </p>
                                @if($karyawan->tipeKaryawan && $karyawan->tipeKaryawan->dasar_absensi)
                                    <p class="text-[10px] text-rose-500 mt-0.5">
                                        Dasar: {{ $karyawan->tipeKaryawan->dasar_absensi }} • 
                                        {{ $karyawan->tipeKaryawan->periode_gaji }}
                                    </p>
                                @endif
                            </div>
                            <div class="bg-teal-50 rounded-lg p-3 border border-teal-100">
                                <p class="text-[10px] text-teal-600 font-bold uppercase tracking-wider mb-1">Shift Kerja</p>
                                @if($karyawan->shift)
                                    <p class="font-bold text-slate-800 text-sm">
                                        <i class="bi bi-clock-fill text-teal-600"></i> {{ $karyawan->shift->nama_shift }}
                                    </p>
                                    <p class="text-[10px] text-teal-600 mt-0.5">
                                        {{ \Carbon\Carbon::parse($karyawan->shift->jam_masuk)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($karyawan->shift->jam_pulang_default)->format('H:i') }}
                                    </p>
                                @else
                                    <p class="text-xs text-slate-400 italic">Belum diatur</p>
                                @endif
                            </div>
                            <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Tanggal Masuk</p>
                                <p class="font-bold text-slate-800 text-sm">
                                    <i class="bi bi-calendar-check-fill text-emerald-500"></i>
                                    {{ $karyawan->tanggal_masuk ? \Carbon\Carbon::parse($karyawan->tanggal_masuk)->translatedFormat('d F Y') : '-' }}
                                </p>
                                @if($karyawan->tanggal_masuk)
                                    <p class="text-[10px] text-emerald-600 mt-0.5 font-medium">
                                        Masa kerja: {{ \Carbon\Carbon::parse($karyawan->tanggal_masuk)->diffForHumans(now(), true) }}
                                    </p>
                                @endif
                            </div>
                            <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Status Karyawan</p>
                                @if($karyawan->status == 'aktif')
                                    <p class="font-bold text-emerald-700 text-sm">
                                        <i class="bi bi-check-circle-fill"></i> AKTIF
                                    </p>
                                @else
                                    <p class="font-bold text-rose-700 text-sm">
                                        <i class="bi bi-x-circle-fill"></i> NONAKTIF
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- RIWAYAT PRESENSI -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <i class="bi bi-calendar2-check"></i>
                                </div>
                                <h3 class="font-bold text-slate-800 text-sm">Riwayat Kehadiran</h3>
                            </div>
                            <span class="text-[10px] font-bold bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-md border border-emerald-100">
                                10 Terakhir
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-wider">
                                    <tr>
                                        <th class="px-3 py-2.5 font-bold rounded-tl-lg">Tanggal</th>
                                        <th class="px-3 py-2.5 font-bold text-center">Masuk</th>
                                        <th class="px-3 py-2.5 font-bold text-center">Pulang</th>
                                        <th class="px-3 py-2.5 font-bold">Metode</th>
                                        <th class="px-3 py-2.5 font-bold text-center rounded-tr-lg">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm text-slate-700 divide-y divide-slate-100">
                                    @if(isset($riwayat_presensi) && count($riwayat_presensi) > 0)
                                        @foreach($riwayat_presensi as $prs)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-3 py-2.5">
                                                <div class="font-semibold text-slate-800 text-xs">{{ \Carbon\Carbon::parse($prs->tanggal)->translatedFormat('d M Y') }}</div>
                                                <div class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($prs->tanggal)->translatedFormat('l') }}</div>
                                            </td>
                                            <td class="px-3 py-2.5 text-center">
                                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded text-[11px] font-bold">
                                                    <i class="bi bi-box-arrow-in-right"></i> {{ \Carbon\Carbon::parse($prs->jam_masuk)->format('H:i') }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2.5 text-center">
                                                @if($prs->jam_pulang)
                                                    <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 px-2 py-0.5 rounded text-[11px] font-bold">
                                                        <i class="bi bi-box-arrow-left"></i> {{ \Carbon\Carbon::parse($prs->jam_pulang)->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-[11px] text-slate-400 italic">--:--</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2.5">
                                                <span class="text-[10px] text-slate-500 font-medium">
                                                    <i class="bi bi-qr-code-scan mr-0.5"></i> {{ $prs->metode_presensi ?? 'QR_KARYAWAN' }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2.5 text-center">
                                                @if($prs->status_verifikasi == 'disetujui')
                                                    <span class="inline-flex items-center gap-1 text-emerald-600 text-[10px] font-bold bg-emerald-50 px-2 py-0.5 rounded">
                                                        <i class="bi bi-check-circle-fill"></i> OK
                                                    </span>
                                                @elseif($prs->status_verifikasi == 'ditolak')
                                                    <span class="inline-flex items-center gap-1 text-rose-600 text-[10px] font-bold bg-rose-50 px-2 py-0.5 rounded">
                                                        <i class="bi bi-x-circle-fill"></i> Tolak
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 text-amber-600 text-[10px] font-bold bg-amber-50 px-2 py-0.5 rounded">
                                                        <i class="bi bi-clock-fill"></i> Pending
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="px-3 py-10 text-center text-slate-400 text-sm">
                                                <i class="bi bi-calendar-x text-3xl mb-2 block opacity-50"></i>
                                                <p class="font-semibold text-slate-500">Belum ada data kehadiran</p>
                                                <p class="text-xs text-slate-400 mt-1">Riwayat akan muncul setelah karyawan absen</p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
</div>

<!-- ================= MODAL RESET PASSWORD ================= -->
<div id="modalResetPwd" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 animate-fade-in">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-800 text-lg">Reset Password?</h3>
                <p class="text-xs text-slate-500">Tindakan ini akan mengubah sandi karyawan</p>
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-5">
            <p class="text-xs text-amber-800 leading-relaxed">
                Password untuk karyawan <b>{{ $karyawan->nama_lengkap }}</b> akan direset menjadi:
                <br>
                <code class="inline-block mt-2 bg-white border border-amber-300 px-3 py-1 rounded font-mono font-bold text-amber-700 text-sm">passwor123</code>
            </p>
        </div>

        <form action="{{ route('karyawan.reset-password', $karyawan->id) }}" method="POST">
            @csrf
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="document.getElementById('modalResetPwd').classList.add('hidden')" 
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-bold transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-colors shadow-sm flex items-center gap-2">
                    <i class="bi bi-key-fill"></i> Ya, Reset Password
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>