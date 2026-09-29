<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengajuan Lembur - HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Custom scrollbar untuk sidebar */
        #sidebar::-webkit-scrollbar { width: 6px; }
        #sidebar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        
        /* Animasi fade-in untuk notifikasi */
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fade-in 0.3s ease-out; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50/20 to-slate-100 min-h-screen text-slate-800">

<div class="flex min-h-screen">

    <!-- =========================
         SIDEBAR (Disalin dari Dashboard)
    ========================== -->
    <aside id="sidebar" class="bg-white border-r border-slate-200 flex flex-col w-[260px] shrink-0 sticky top-0 h-screen">
        <div class="p-4 border-b border-slate-200">
            <div class="flex items-center">
                <div class="bg-blue-600 text-white rounded-lg flex items-center justify-center mr-3 w-[42px] h-[42px] shrink-0">
                    <i class="bi bi-person-badge-fill text-xl"></i>
                </div>
                <div>
                    <div class="font-bold text-blue-600">Portal HRD</div>
                    <div class="text-xs text-slate-500">Presensi & Kinerja</div>
                </div>
            </div>
        </div>

        <div class="p-4 grow overflow-y-auto">
            <div class="uppercase text-slate-400 text-xs font-bold mb-3 mt-2">Menu Utama</div>
            <a href="{{ route('dashboard.hrd') }}" class="flex items-center text-slate-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-grid-1x2-fill mr-3"></i>
                <span class="text-sm font-medium">Dashboard</span>
            </a>

            <div class="uppercase text-slate-400 text-xs font-bold mb-3 mt-6">Kelola Karyawan</div>
            <a href="{{ route('karyawan.index') }}" class="flex items-center text-slate-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-people-fill mr-3"></i>
                <span class="text-sm font-medium">Daftar Karyawan</span>
            </a>
            <a href="{{ route('karyawan.create') }}" class="flex items-center text-slate-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-person-plus-fill mr-3"></i>
                <span class="text-sm font-medium">Tambah Karyawan</span>
            </a>
            <a href="{{ route('jabatan.departemen.index') }}" class="flex items-center text-slate-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-diagram-3-fill mr-3"></i>
                <span class="text-sm font-medium">Jabatan & Departemen</span>
            </a>

            <div class="uppercase text-slate-400 text-xs font-bold mb-3 mt-6">Kehadiran & QR</div>
            <a href="{{ route('karyawan.qr.index') }}" class="flex items-center text-slate-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-qr-code-scan mr-3"></i>
                <span class="text-sm font-medium">Manajemen QR Code</span>
            </a>
            <a href="{{ route('kehadiran.log') }}" class="flex items-center text-slate-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-calendar-check mr-3"></i>
                <span class="text-sm font-medium">Log Kehadiran Harian</span>
            </a>
            <!-- Menu Pengajuan Lembur Aktif -->
            <a href="{{ route('lembur.pengajuan') }}" class="flex items-center justify-between bg-blue-600 text-white rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <div class="flex items-center">
                    <i class="bi bi-file-earmark-plus mr-3"></i>
                    <span class="text-sm font-medium">Pengajuan Lembur</span>
                </div>
                @if(isset($lemburMenunggu) && $lemburMenunggu > 0)
                    <span class="bg-white text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $lemburMenunggu }}</span>
                @endif
            </a>

            <div class="uppercase text-slate-400 text-xs font-bold mb-3 mt-6">Akun</div>
            <a href="{{ route('profile.index') }}" class="flex items-center text-slate-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
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

        <!-- ========== PERUBAHAN DI SINI ========== -->
        <!-- Info User: Nama Asli + Jabatan + Departemen (Tanpa Role) -->
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

        <!-- NAVBAR UTAMA -->
        <nav class="bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-8 py-4 flex items-center justify-between sticky top-0 z-30 shadow-xs">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard.hrd') }}" class="bg-slate-100 text-slate-600 hover:bg-blue-600 hover:text-white p-2.5 rounded-xl transition-all duration-200 shadow-xs flex items-center justify-center">
                    <i class="bi bi-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="font-extrabold text-xl text-slate-900 tracking-tight">Manajemen Pengajuan Lembur</h1>
                    <p class="text-xs text-slate-500 font-medium">Persetujuan, rekapitulasi, dan audit lembur karyawan perusahaan</p>
                </div>
            </div>
            <div class="hidden md:flex items-center gap-3 bg-blue-50/60 border border-blue-100 px-4 py-2 rounded-2xl text-xs font-bold text-blue-700">
                <i class="bi bi-shield-check text-blue-600 text-base"></i> Portal HRD Terintegrasi
            </div>
        </nav>

        <!-- CONTENT BODY -->
        <div class="p-8 grow overflow-y-auto w-full max-w-[1400px] mx-auto space-y-8">

            <!-- Notifikasi Sukses -->
            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-900 px-5 py-4 rounded-2xl text-sm font-semibold flex items-center shadow-sm animate-fade-in">
                    <div class="bg-emerald-500 text-white rounded-full w-7 h-7 flex items-center justify-center mr-3 shrink-0">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Notifikasi Error -->
            @if(session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-900 px-5 py-4 rounded-2xl text-sm font-semibold flex items-center shadow-sm animate-fade-in">
                    <div class="bg-rose-500 text-white rounded-full w-7 h-7 flex items-center justify-center mr-3 shrink-0">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- KARTU STATISTIK RINGKASAN (EKSEKUTIF) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-center justify-between relative overflow-hidden group hover:border-amber-300 transition-all">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-50 rounded-full group-hover:scale-125 transition-transform duration-300 pointer-events-none"></div>
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Status Menunggu</p>
                        <h3 class="text-3xl font-black text-amber-600">
                            {{ \App\Models\Lembur::where('status', 'menunggu')->count() }} <span class="text-xs font-bold text-slate-400">Pengajuan</span>
                        </h3>
                    </div>
                    <div class="relative z-10 w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl shadow-xs">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-center justify-between relative overflow-hidden group hover:border-emerald-300 transition-all">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-50 rounded-full group-hover:scale-125 transition-transform duration-300 pointer-events-none"></div>
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Disetujui</p>
                        <h3 class="text-3xl font-black text-emerald-600">
                            {{ \App\Models\Lembur::where('status', 'disetujui')->count() }} <span class="text-xs font-bold text-slate-400">Kasus</span>
                        </h3>
                    </div>
                    <div class="relative z-10 w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shadow-xs">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-center justify-between relative overflow-hidden group hover:border-blue-300 transition-all">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-125 transition-transform duration-300 pointer-events-none"></div>
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Jam Lembur</p>
                        <h3 class="text-3xl font-black text-blue-600">
                            {{ \App\Models\Lembur::where('status', 'disetujui')->sum('durasi_jam') }} <span class="text-xs font-bold text-slate-400">Jam</span>
                        </h3>
                    </div>
                    <div class="relative z-10 w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl shadow-xs">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>

            <!-- PANEL FILTER & PENCARIAN UTAMA -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 space-y-6">
                
                <!-- Tab Filter Status -->
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-5">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mr-2">Status:</span>
                        
                        <a href="{{ route('lembur.pengajuan', array_merge(request()->all(), ['status' => 'menunggu'])) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-1.5 {{ ($status == 'menunggu') ? 'bg-amber-500 text-white shadow-md shadow-amber-500/20 scale-105' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            <i class="bi bi-hourglass-split"></i> Menunggu
                        </a>
                        
                        <a href="{{ route('lembur.pengajuan', array_merge(request()->all(), ['status' => 'disetujui'])) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-1.5 {{ ($status == 'disetujui') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 scale-105' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            <i class="bi bi-check-circle"></i> Disetujui
                        </a>
                        
                        <a href="{{ route('lembur.pengajuan', array_merge(request()->all(), ['status' => 'ditolak'])) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-1.5 {{ ($status == 'ditolak') ? 'bg-rose-600 text-white shadow-md shadow-rose-600/20 scale-105' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            <i class="bi bi-x-circle"></i> Ditolak
                        </a>
                        
                        <a href="{{ route('lembur.pengajuan', array_merge(request()->all(), ['status' => 'semua'])) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-1.5 {{ ($status == 'semua') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20 scale-105' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            <i class="bi bi-grid"></i> Semua
                        </a>
                    </div>

                    <div class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl">
                        Menampilkan: <span class="text-slate-900 font-black">{{ $lemburs->total() }} Data</span>
                    </div>
                </div>

                <!-- Form Filter Tanggal & Search -->
                <form action="{{ route('lembur.pengajuan') }}" method="GET" class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
                    <input type="hidden" name="status" value="{{ $status }}">
                    
                    <div class="lg:col-span-4 flex gap-3">
                        <div class="w-1/2">
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600 outline-none bg-slate-50/50">
                        </div>
                        <div class="w-1/2">
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600 outline-none bg-slate-50/50">
                        </div>
                    </div>

                    <div class="lg:col-span-4">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Cari Karyawan</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama lengkap atau NIK..." class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600 outline-none bg-slate-50/50">
                            <i class="bi bi-search absolute left-3.5 top-3 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="lg:col-span-4 flex items-center gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm shadow-blue-600/20 flex items-center justify-center gap-2">
                            <i class="bi bi-filter text-sm"></i> Terapkan Filter
                        </button>
                        
                        <button type="submit" formaction="{{ route('lembur.export') }}" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm shadow-emerald-600/20 flex items-center justify-center gap-2">
                            <i class="bi bi-file-earmark-excel text-sm"></i> Cetak Excel
                        </button>
                        
                        <a href="{{ route('lembur.pengajuan') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- TABEL DATA UTAMA -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead class="bg-slate-900 text-white text-[11px] uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4 font-bold text-center w-16">No</th>
                                <th class="px-6 py-4 font-bold">Informasi Karyawan</th>
                                <th class="px-6 py-4 font-bold">Tanggal & Jam Lembur</th>
                                <th class="px-6 py-4 font-bold">Durasi & Upah</th>
                                <th class="px-6 py-4 font-bold">Catatan Pekerjaan</th>
                                <th class="px-6 py-4 font-bold text-center">Status</th>
                                <th class="px-6 py-4 font-bold text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-slate-700 divide-y divide-slate-100">
                            @forelse($lemburs as $index => $lmb)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-6 py-4 text-center font-bold text-slate-400">
                                    {{ $lemburs->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors">{{ $lmb->karyawan->nama_lengkap ?? '-' }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5 font-medium">NIK: <span class="text-slate-600 font-bold">{{ $lmb->karyawan->nik_kerja ?? '-' }}</span> &bull; <span class="text-blue-600">{{ $lmb->karyawan->departemen->nama_departemen ?? '-' }}</span></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 mb-1 flex items-center gap-1.5">
                                        <i class="bi bi-calendar2-event text-blue-500"></i> {{ \Carbon\Carbon::parse($lmb->tanggal)->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg inline-flex items-center gap-1 border border-slate-200/60">
                                        <i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($lmb->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($lmb->jam_selesai)->format('H:i') }} WIB
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-black text-blue-600 text-xs mb-0.5">{{ $lmb->durasi_jam ?? 0 }} Jam Kerja</div>
                                    <div class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md w-fit border border-emerald-100">
                                        Rp {{ number_format($lmb->total_upah ?? 0, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 max-w-xs">
                                    <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed bg-slate-50 p-2.5 rounded-xl border border-slate-100" title="{{ $lmb->catatan }}">
                                        {{ $lmb->catatan ?? 'Tidak ada catatan khusus.' }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($lmb->status == 'menunggu')
                                        <span class="bg-amber-100 text-amber-800 text-xs px-3.5 py-1.5 rounded-full font-extrabold inline-flex items-center gap-1.5 shadow-xs border border-amber-200">
                                            <i class="bi bi-hourglass-split"></i> Menunggu
                                        </span>
                                    @elseif($lmb->status == 'disetujui')
                                        <span class="bg-emerald-100 text-emerald-800 text-xs px-3.5 py-1.5 rounded-full font-extrabold inline-flex items-center gap-1.5 shadow-xs border border-emerald-200">
                                            <i class="bi bi-check-circle-fill"></i> Disetujui
                                        </span>
                                    @else
                                        <span class="bg-rose-100 text-rose-800 text-xs px-3.5 py-1.5 rounded-full font-extrabold inline-flex items-center gap-1.5 shadow-xs border border-rose-200">
                                            <i class="bi bi-x-circle-fill"></i> Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($lmb->status == 'menunggu')
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="{{ route('lembur.approve', $lmb->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui pengajuan lembur ini?')">
                                                @csrf
                                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all shadow-sm shadow-emerald-600/20 flex items-center gap-1">
                                                    <i class="bi bi-check-lg"></i> ACC
                                                </button>
                                            </form>
                                            <form action="{{ route('lembur.reject', $lmb->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak pengajuan lembur ini?')">
                                                @csrf
                                                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all shadow-sm shadow-rose-600/20 flex items-center gap-1">
                                                    <i class="bi bi-x-lg"></i> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 font-bold bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200 inline-block">
                                            <i class="bi bi-lock-fill mr-1"></i> Selesai Diproses
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-2xl">
                                        <i class="bi bi-folder-x"></i>
                                    </div>
                                    <h3 class="font-bold text-slate-700 text-base mb-1">Tidak Ada Data Ditemukan</h3>
                                    <p class="text-slate-400 text-xs">Coba ubah filter tanggal atau kata kunci pencarian Anda.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                @if($lemburs->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50 flex items-center justify-between">
                    <div class="text-xs font-semibold text-slate-500">
                        Menampilkan <span class="font-bold text-slate-800">{{ $lemburs->firstItem() }}</span> - <span class="font-bold text-slate-800">{{ $lemburs->lastItem() }}</span> dari <span class="font-bold text-slate-800">{{ $lemburs->total() }}</span> data
                    </div>
                    <div>
                        {{ $lemburs->withQueryString()->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </main>
</div>

</body>
</html>