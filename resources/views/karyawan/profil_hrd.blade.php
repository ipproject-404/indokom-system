<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Portal HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex">

    <!-- SIDEBAR HRD -->
    <aside class="w-64 bg-white border-r border-slate-200 hidden lg:flex flex-col shrink-0 sticky top-0 h-screen">
        <div class="p-5 border-b border-slate-100 flex items-center gap-3">
            <div class="bg-blue-600 text-white rounded-xl flex items-center justify-center w-10 h-10 shadow-md shadow-blue-600/20">
                <i class="bi bi-person-badge-fill text-lg"></i>
            </div>
            <div>
                <div class="font-black text-blue-600 text-sm tracking-tight">Portal HRD</div>
                <div class="text-[11px] text-slate-400 font-medium">Presensi & Kinerja</div>
            </div>
        </div>

        <div class="p-4 flex-1 overflow-y-auto space-y-6">
            <div>
                <div class="text-[10px] uppercase text-slate-400 font-extrabold tracking-wider mb-2 px-3">Menu Utama</div>
                <a href="{{ route('dashboard.hrd') }}" class="flex items-center text-slate-600 hover:bg-blue-50 hover:text-blue-600 rounded-xl px-3.5 py-2.5 text-xs font-bold transition-all">
                    <i class="bi bi-grid-1x2-fill mr-3 text-base"></i> Dashboard
                </a>
            </div>

            <div>
                <div class="text-[10px] uppercase text-slate-400 font-extrabold tracking-wider mb-2 px-3">Akun</div>
                <a href="{{ route('profile.index') }}" class="flex items-center bg-blue-600 text-white rounded-xl px-3.5 py-2.5 text-xs font-bold shadow-md shadow-blue-600/20 transition-all">
                    <i class="bi bi-person-circle mr-3 text-base"></i> Profil Saya
                </a>
            </div>
        </div>

        <!-- User Mini Footer di Sidebar -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-black flex items-center justify-center text-xs">
                    {{ strtoupper(substr($user->name ?? 'HR', 0, 2)) }}
                </div>
                <div class="overflow-hidden flex-1">
                    <div class="font-bold text-xs text-slate-800 truncate">{{ $user->name ?? 'Admin HRD' }}</div>
                    <div class="text-[10px] text-slate-500 truncate">{{ optional($karyawan)->jabatan->nama_jabatan ?? 'HRD Manager' }}</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col min-w-0">
        
        <!-- TOP NAVBAR -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between sticky top-0 z-20 shadow-xs">
            <div>
                <h1 class="font-black text-xl text-slate-900 tracking-tight">Profil Saya</h1>
                <p class="text-xs text-slate-400 font-medium">Data pribadi & kepegawaian kamu di perusahaan</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-full border border-emerald-100 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Online
                </span>
            </div>
        </header>

        <!-- CONTAINER BODY -->
        <div class="p-8 max-w-7xl mx-auto w-full space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- KOLOM KIRI: KARTU ID DIGITAL & STATISTIK -->
                <div class="lg:col-span-5 space-y-6">
                    
                    <!-- ID CARD GRADIENT -->
                    <div class="bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-800 rounded-3xl p-6 text-white shadow-xl shadow-blue-600/10 relative overflow-hidden border border-blue-400/20">
                        <!-- Watermark Icon -->
                        <div class="absolute -right-6 -bottom-6 opacity-10 text-9xl pointer-events-none">
                            <i class="bi bi-person-badge"></i>
                        </div>

                        <div class="flex justify-between items-start mb-6 relative z-10">
                            <span class="text-[11px] font-black uppercase tracking-widest bg-white/20 backdrop-blur-md px-3 py-1 rounded-full border border-white/20">
                                <i class="bi bi-building mr-1"></i> {{ config('app.name', 'PT ISP/IAS') }}
                            </span>
                            <span class="bg-emerald-500 text-white text-[10px] font-extrabold px-3 py-1 rounded-full shadow-sm flex items-center gap-1">
                                <i class="bi bi-check-circle-fill"></i> Aktif
                            </span>
                        </div>

                        <div class="flex items-center gap-4 mb-6 relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-2xl font-black shadow-inner shrink-0">
                                {{ strtoupper(substr($user->name ?? 'HR', 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="font-extrabold text-lg text-white leading-snug">{{ $user->name ?? '-' }}</h3>
                                <p class="text-xs text-blue-100 font-medium">{{ optional($karyawan)->jabatan->nama_jabatan ?? 'Human Resource Development' }}</p>
                                <div class="mt-1 inline-block bg-blue-900/50 backdrop-blur-sm text-blue-200 text-[10px] font-bold px-2.5 py-0.5 rounded-lg border border-blue-400/20">
                                    {{ optional($karyawan)->departemen->nama_departemen ?? 'Manajemen' }}
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-white/10 flex justify-between items-end relative z-10">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-blue-200 tracking-wider block mb-0.5">NIK Kerja</span>
                                <span class="font-black text-sm tracking-wide">{{ optional($karyawan)->nik_kerja ?? 'ADM-001' }}</span>
                                <span class="text-[10px] text-blue-200 block mt-2">Bergabung: <strong class="text-white">{{ optional($karyawan)->created_at ? optional($karyawan)->created_at->format('d F Y') : '01 January 2024' }}</strong></span>
                            </div>
                            <!-- Mini QR Box Placeholder -->
                            <div class="bg-white p-2 rounded-xl text-slate-800 shadow-md text-center">
                                <i class="bi bi-qr-code text-3xl block leading-none text-slate-900"></i>
                                <span class="text-[8px] font-bold uppercase tracking-tighter text-slate-400 block mt-1">ID Card</span>
                            </div>
                        </div>
                    </div>

                    <!-- KARTU STATISTIK KEHADIRAN & TOMBOL KELUAR -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Kehadiran Bulan Ini</span>
                                <h3 class="text-2xl font-black text-slate-800 mt-0.5">{{ $jumlahHadirBulanIni ?? 0 }} <span class="text-xs font-semibold text-slate-400">Hari Hadir</span></h3>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                                <i class="bi bi-calendar-check-fill"></i>
                            </div>
                        </div>

                        <hr class="border-slate-100">

                        <!-- Tombol Logout Akun -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 font-bold py-3 px-4 rounded-2xl text-xs transition-colors flex items-center justify-center gap-2 border border-rose-100">
                                <i class="bi bi-box-arrow-right text-base"></i> Keluar Akun
                            </button>
                        </form>
                    </div>

                </div>

                <!-- KOLOM KANAN: DETAIL DATA PRIBADI, KEPEGAWAIAN & AKUN -->
                <div class="lg:col-span-7 space-y-6">
                    
                    <!-- KARTU 1: DATA PRIBADI -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 class="font-extrabold text-sm text-slate-900 flex items-center gap-2">
                                <i class="bi bi-person-lines-fill text-blue-600"></i> Data Pribadi
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">NIK KTP</span>
                                <span class="font-bold text-slate-800 text-sm">{{ optional($karyawan)->nik_ktp ?? '3201011990010001' }}</span>
                            </div>
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">No. HP / WhatsApp</span>
                                <span class="font-bold text-slate-800 text-sm">{{ optional($karyawan)->no_hp ?? '081234560001' }}</span>
                            </div>
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Tempat, Tanggal Lahir</span>
                                <span class="font-bold text-slate-800 text-sm">{{ optional($karyawan)->tempat_lahir ?? 'Bandar Lampung' }}, {{ optional($karyawan)->tanggal_lahir ? \Carbon\Carbon::parse(optional($karyawan)->tanggal_lahir)->translatedFormat('d F Y') : '31 August 2004' }}</span>
                            </div>
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Jenis Kelamin</span>
                                <span class="font-bold text-slate-800 text-sm">{{ optional($karyawan)->jenis_kelamin ?? 'Laki-laki' }}</span>
                            </div>
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100 md:col-span-2">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Alamat Domisili</span>
                                <span class="font-bold text-slate-800 text-sm">{{ optional($karyawan)->alamat ?? 'Jl. Raden Intan No. 12, Bandar Lampung' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- KARTU 2: DATA KEPEGAWAIAN -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 class="font-extrabold text-sm text-slate-900 flex items-center gap-2">
                                <i class="bi bi-briefcase-fill text-indigo-600"></i> Data Kepegawaian
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">NIK Kerja</span>
                                <span class="font-bold text-slate-800 text-sm">{{ optional($karyawan)->nik_kerja ?? 'HRD-001' }}</span>
                            </div>
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Jabatan</span>
                                <span class="font-bold text-slate-800 text-sm">{{ optional($karyawan)->jabatan->nama_jabatan ?? 'Human Resource Manager' }}</span>
                            </div>
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Departemen</span>
                                <span class="font-bold text-slate-800 text-sm">{{ optional($karyawan)->departemen->nama_departemen ?? 'Manajemen HRD' }}</span>
                            </div>
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Tanggal Bergabung</span>
                                <span class="font-bold text-slate-800 text-sm">{{ optional($karyawan)->created_at ? optional($karyawan)->created_at->translatedFormat('d F Y') : '12 August 2025' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- KARTU 3: INFORMASI AKUN -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 class="font-extrabold text-sm text-slate-900 flex items-center gap-2">
                                <i class="bi bi-shield-lock-fill text-emerald-600"></i> Akun Login
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Email / Username</span>
                                <span class="font-bold text-slate-800 text-sm">{{ $user->email ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Hak Akses (Role)</span>
                                <span class="font-bold text-blue-600 uppercase tracking-wide text-sm">{{ $user->role ?? 'HRD' }}</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </main>

</body>
</html>