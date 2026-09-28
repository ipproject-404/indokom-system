<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Portal HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 flex" x-data="{ showModalPassword: false, showModalQr: false }">

    <!-- SIDEBAR HRD -->
    <aside class="w-68 bg-white border-r border-slate-200/80 hidden lg:flex flex-col shrink-0 sticky top-0 h-screen shadow-xs">
        <div class="p-6 border-b border-slate-100 flex items-center gap-3.5">
            <div class="bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rounded-2xl flex items-center justify-center w-11 h-11 shadow-lg shadow-blue-600/30">
                <i class="bi bi-person-badge-fill text-xl"></i>
            </div>
            <div>
                <div class="font-black text-blue-600 text-sm tracking-tight">Portal HRD</div>
                <div class="text-[11px] text-slate-400 font-semibold">Presensi & Kinerja</div>
            </div>
        </div>

        <div class="p-4 flex-1 overflow-y-auto space-y-6">
            <div>
                <div class="text-[10px] uppercase text-slate-400 font-extrabold tracking-widest mb-2.5 px-3">Menu Utama</div>
                <a href="{{ route('dashboard.hrd') }}" class="flex items-center text-slate-600 hover:bg-blue-50 hover:text-blue-600 rounded-2xl px-4 py-3 text-xs font-bold transition-all group">
                    <i class="bi bi-grid-1x2-fill mr-3 text-base group-hover:scale-110 transition-transform"></i> Dashboard
                </a>
            </div>

            <div>
                <div class="text-[10px] uppercase text-slate-400 font-extrabold tracking-widest mb-2.5 px-3">Akun</div>
                <a href="{{ route('profile.index') }}" class="flex items-center bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl px-4 py-3 text-xs font-bold shadow-md shadow-blue-600/25 transition-all">
                    <i class="bi bi-person-circle mr-3 text-base"></i> Profil Saya
                </a>
            </div>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50/60">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-700 font-black flex items-center justify-center text-xs shadow-inner">
                    {{ strtoupper(substr(optional($karyawan)->nama_lengkap ?? ($user->name ?? 'HR'), 0, 2)) }}
                </div>
                <div class="overflow-hidden flex-1">
                    <div class="font-bold text-xs text-slate-800 truncate">{{ optional($karyawan)->nama_lengkap ?? ($user->name ?? 'Admin HRD') }}</div>
                    <div class="text-[10px] text-blue-600 font-semibold truncate">{{ optional($karyawan)->jabatan->nama_jabatan ?? 'HRD Manager' }}</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col min-w-0">
        
        <header class="bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-8 py-4.5 flex items-center justify-between sticky top-0 z-20 shadow-xs">
            <div>
                <h1 class="font-black text-xl text-slate-900 tracking-tight">Profil Pengguna</h1>
                <p class="text-xs text-slate-400 font-medium">Kelola informasi pribadi, kepegawaian, dan keamanan akun</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold bg-emerald-50 text-emerald-700 px-3.5 py-1.5 rounded-full border border-emerald-100 flex items-center gap-2 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Online
                </span>
            </div>
        </header>

        <div class="p-8 max-w-7xl mx-auto w-full space-y-8">

            <!-- Notifikasi Sukses / Error -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-xs font-bold flex items-center shadow-xs">
                    <i class="bi bi-check-circle-fill text-lg mr-2.5 text-emerald-600"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-3.5 rounded-2xl text-xs font-bold shadow-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center"><i class="bi bi-exclamation-circle-fill text-base mr-2.5 text-rose-600"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- 1. HERO BANNER PROFILE HEADER -->
            <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-800 rounded-3xl p-8 text-white shadow-xl shadow-blue-600/15 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6 border border-blue-400/20">
                <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl pointer-events-none">
                    <i class="bi bi-person-badge"></i>
                </div>

                <div class="flex items-center gap-6 relative z-10 w-full md:w-auto">
                    <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-md border-2 border-white/40 flex items-center justify-center text-3xl font-black shadow-inner shrink-0">
                        {{ strtoupper(substr(optional($karyawan)->nama_lengkap ?? ($user->name ?? 'HR'), 0, 2)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h2 class="text-2xl font-black tracking-tight">{{ optional($karyawan)->nama_lengkap ?? ($user->name ?? '-') }}</h2>
                            <span class="bg-emerald-500 text-white text-[10px] font-extrabold px-2.5 py-0.5 rounded-full shadow-xs">Aktif</span>
                        </div>
                        <p class="text-xs text-blue-100 font-semibold">{{ optional($karyawan)->jabatan->nama_jabatan ?? 'Human Resource Development' }} &bull; <span class="text-white">{{ optional($karyawan)->departemen->nama_departemen ?? 'Manajemen' }}</span></p>
                        <p class="text-[11px] text-blue-200 mt-1.5 font-medium">NIK Kerja: <strong class="text-white">{{ optional($karyawan)->nik_kerja ?? 'ADM-001' }}</strong></p>
                    </div>
                </div>

                <!-- Tombol Aksi Cepat di Header -->
                <div class="flex flex-wrap items-center gap-3 relative z-10 w-full md:w-auto justify-end">
                    <button @click="showModalQr = true" class="bg-white hover:bg-blue-50 text-blue-700 px-5 py-3 rounded-2xl text-xs font-extrabold transition-all shadow-md flex items-center gap-2">
                        <i class="bi bi-qr-code text-base"></i> Tampilkan QR Card
                    </button>
                    <button @click="showModalPassword = true" class="bg-blue-900/60 hover:bg-blue-900 text-white border border-white/30 px-5 py-3 rounded-2xl text-xs font-extrabold transition-all shadow-sm flex items-center gap-2 backdrop-blur-md">
                        <i class="bi bi-key-fill text-base"></i> Ubah Sandi
                    </button>
                </div>
            </div>

            <!-- 2. GRID KONTEN UTAMA (2 Kolom Seimbang) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- KOLOM KIRI (Data Pribadi & Kepegawaian) -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- DATA PRIBADI -->
                    <div class="bg-white rounded-3xl p-7 border border-slate-200/80 shadow-xs space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <h3 class="font-extrabold text-sm text-slate-900 flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm"><i class="bi bi-person-lines-fill"></i></span> Data Pribadi Karyawan
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100/80">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">NIK KTP</span>
                                <span class="font-extrabold text-slate-800 text-sm">{{ optional($karyawan)->nik_ktp ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100/80">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">No. HP / WhatsApp</span>
                                <span class="font-extrabold text-slate-800 text-sm">{{ optional($karyawan)->no_hp ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100/80">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Tempat, Tanggal Lahir</span>
                                <span class="font-extrabold text-slate-800 text-sm">{{ optional($karyawan)->tempat_lahir ?? '-' }}, {{ optional($karyawan)->tanggal_lahir ? \Carbon\Carbon::parse(optional($karyawan)->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100/80">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Jenis Kelamin</span>
                                <span class="font-extrabold text-slate-800 text-sm">{{ optional($karyawan)->jenis_kelamin ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100/80 md:col-span-2">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Alamat Domisili</span>
                                <span class="font-extrabold text-slate-800 text-sm leading-relaxed">{{ optional($karyawan)->alamat ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- DATA KEPEGAWAIAN -->
                    <div class="bg-white rounded-3xl p-7 border border-slate-200/80 shadow-xs space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <h3 class="font-extrabold text-sm text-slate-900 flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm"><i class="bi bi-briefcase-fill"></i></span> Data Kepegawaian & Posisi
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100/80">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">NIK Kerja</span>
                                <span class="font-extrabold text-slate-800 text-sm">{{ optional($karyawan)->nik_kerja ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100/80">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Jabatan</span>
                                <span class="font-extrabold text-slate-800 text-sm">{{ optional($karyawan)->jabatan->nama_jabatan ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100/80">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Departemen</span>
                                <span class="font-extrabold text-slate-800 text-sm">{{ optional($karyawan)->departemen->nama_departemen ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100/80">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Tanggal Bergabung</span>
                                <span class="font-extrabold text-slate-800 text-sm">{{ optional($karyawan)->created_at ? optional($karyawan)->created_at->translatedFormat('d F Y') : '-' }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- KOLOM KANAN (Statistik, Akun, & Keluar) -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- KARTU STATISTIK BULAN INI -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Kehadiran Bulan Ini</span>
                            <h3 class="text-3xl font-black text-slate-900">{{ $jumlahHadirBulanIni ?? 0 }} <span class="text-xs font-bold text-slate-400">Hari Hadir</span></h3>
                        </div>
                        <div class="w-13 h-13 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold shadow-xs">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                    </div>

                    <!-- INFORMASI AKUN -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h3 class="font-extrabold text-sm text-slate-900 flex items-center gap-2">
                                <i class="bi bi-shield-lock-fill text-emerald-600"></i> Informasi Akun Login
                            </h3>
                        </div>
                        <div class="space-y-3 text-xs">
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100/80">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-0.5">Username / Email</span>
                                <span class="font-extrabold text-slate-800 text-xs">{{ $user->username ?? $user->email ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100/80">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-0.5">Hak Akses (Role)</span>
                                <span class="font-extrabold text-blue-600 uppercase tracking-wide text-xs">{{ $user->role ?? 'HRD' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- TOMBOL KELUAR AKUN -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold py-3.5 px-4 rounded-2xl text-xs transition-all flex items-center justify-center gap-2 border border-rose-100 shadow-xs">
                                <i class="bi bi-box-arrow-right text-base"></i> Keluar Sistem (Logout)
                            </button>
                        </form>
                    </div>

                </div>

            </div>

        </div>
    </main>

    <!-- MODAL POPUP QR CODE -->
    <div x-show="showModalQr" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4" style="display: none;" x-transition.opacity>
        <div @click.away="showModalQr = false" class="bg-white rounded-3xl p-7 max-w-xs w-full text-center space-y-4 shadow-2xl border border-slate-100" x-transition.scale>
            <div class="text-[11px] font-black uppercase tracking-widest text-blue-600">ID Card Karyawan</div>
            <div class="text-[10px] text-slate-400 font-semibold -mt-2">PT Indokom Sistem</div>
            
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-center justify-center shadow-inner">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ optional($karyawan)->barcode_uid }}" alt="QR Code" class="rounded-xl shadow-xs">
            </div>

            <div>
                <div class="font-black text-slate-900 text-sm tracking-tight">{{ optional($karyawan)->nama_lengkap ?? $user->name }}</div>
                <div class="text-xs font-bold text-slate-500 mt-0.5">{{ optional($karyawan)->nik_kerja ?? 'ADM-001' }}</div>
            </div>

            <button @click="showModalQr = false" class="w-full bg-slate-900 hover:bg-blue-600 text-white font-bold py-3 rounded-2xl text-xs transition-colors shadow-sm">Tutup</button>
        </div>
    </div>

    <!-- MODAL POPUP UBAH SANDI DENGAN IKON MATA -->
    <div x-show="showModalPassword" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4" style="display: none;" x-data="{ show1: false, show2: false, show3: false }" x-transition.opacity>
        <div @click.away="showModalPassword = false" class="bg-white rounded-3xl p-7 max-w-md w-full space-y-5 shadow-2xl border border-slate-100" x-transition.scale>
            <div class="flex justify-between items-center border-b border-slate-100 pb-3.5">
                <h3 class="font-black text-base text-slate-900 flex items-center gap-2"><i class="bi bi-key-fill text-blue-600 text-lg"></i> Ubah Kata Sandi</h3>
                <button @click="showModalPassword = false" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center transition-colors"><i class="bi bi-x-lg text-sm"></i></button>
            </div>
            
            <form action="{{ route('profile.update-password') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                
                <div>
                    <label class="block font-bold text-slate-600 mb-1.5">Sandi Lama</label>
                    <div class="relative">
                        <input :type="show1 ? 'text' : 'password'" name="current_password" required class="w-full pl-4 pr-11 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-600 bg-slate-50/80 font-medium text-slate-800">
                        <button type="button" @click="show1 = !show1" class="absolute right-3.5 top-3.5 text-slate-400 hover:text-slate-600">
                            <i class="bi text-base" :class="show1 ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-600 mb-1.5">Sandi Baru</label>
                    <div class="relative">
                        <input :type="show2 ? 'text' : 'password'" name="password" required class="w-full pl-4 pr-11 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-600 bg-slate-50/80 font-medium text-slate-800">
                        <button type="button" @click="show2 = !show2" class="absolute right-3.5 top-3.5 text-slate-400 hover:text-slate-600">
                            <i class="bi text-base" :class="show2 ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-600 mb-1.5">Konfirmasi Sandi Baru</label>
                    <div class="relative">
                        <input :type="show3 ? 'text' : 'password'" name="password_confirmation" required class="w-full pl-4 pr-11 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-600 bg-slate-50/80 font-medium text-slate-800">
                        <button type="button" @click="show3 = !show3" class="absolute right-3.5 top-3.5 text-slate-400 hover:text-slate-600">
                            <i class="bi text-base" :class="show3 ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" @click="showModalPassword = false" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3 rounded-2xl transition-colors">Batal</button>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-2xl transition-all shadow-md shadow-blue-600/20">Simpan Sandi</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>