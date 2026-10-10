<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Indokom System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        #sidebar::-webkit-scrollbar { width: 6px; }
        #sidebar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800" x-data="{ showModalPassword: false, showModalQr: false }">

<div class="flex min-h-screen">

    @php
    $userKaryawan = Auth::user()->karyawan ?? null;
    $namaTampil = $userKaryawan->nama_lengkap ?? (Auth::user()->name ?? '-');
    $jabatanTampil = optional($userKaryawan->jabatan ?? null)->nama_jabatan ?? '-';
    $departemenTampil = optional($userKaryawan->departemen ?? null)->nama_departemen ?? '-';
@endphp
@include('partials.sidebar-hrd')

    <!-- =========================
         MAIN CONTENT
    ========================== -->
    <main class="flex-1 flex flex-col min-w-0">
        
        <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 px-8 py-4 flex items-center justify-between sticky top-0 z-20">
            <div>
                <h1 class="font-bold text-xl text-slate-900 tracking-tight">Profil Pengguna</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola informasi pribadi, kepegawaian, dan keamanan akun</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold bg-emerald-50 text-emerald-700 px-3.5 py-1.5 rounded-full border border-emerald-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Online
                </span>
            </div>
        </header>

        <div class="p-8 max-w-7xl mx-auto w-full space-y-8">

            <!-- Notifikasi -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-xs font-bold flex items-center shadow-xs animate-fade-in">
                    <i class="bi bi-check-circle-fill text-lg mr-2.5 text-emerald-600"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-3.5 rounded-2xl text-xs font-bold flex items-center shadow-xs">
                    <i class="bi bi-exclamation-circle-fill text-lg mr-2.5 text-rose-600"></i> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-3.5 rounded-2xl text-xs font-bold shadow-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center"><i class="bi bi-exclamation-circle-fill text-base mr-2.5 text-rose-600"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- 1. HERO BANNER PROFILE -->
            <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-800 rounded-3xl p-8 text-white shadow-xl shadow-blue-600/15 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl pointer-events-none">
                    <i class="bi bi-person-badge"></i>
                </div>
                <!-- Ornamen dekoratif -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-20 -mt-20 pointer-events-none"></div>
                <div class="absolute bottom-0 right-40 w-32 h-32 bg-white/5 rounded-full -mb-10 pointer-events-none"></div>

                <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
                    <div class="flex items-center gap-6 w-full md:w-auto">
                        <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-md border-2 border-white/40 flex items-center justify-center text-3xl font-black shadow-inner shrink-0">
                            {{ strtoupper(substr($namaTampil, 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <h2 class="text-2xl font-black tracking-tight">{{ $namaTampil }}</h2>
                                @if($userKaryawan && $userKaryawan->status == 'aktif')
                                    <span class="bg-emerald-500 text-white text-[10px] font-extrabold px-2.5 py-0.5 rounded-full shadow-sm">Aktif</span>
                                @elseif($userKaryawan)
                                    <span class="bg-rose-500 text-white text-[10px] font-extrabold px-2.5 py-0.5 rounded-full shadow-sm">Nonaktif</span>
                                @endif
                            </div>
                            <p class="text-xs text-blue-100 font-semibold">
                                {{ $jabatanTampil }} &bull; <span class="text-white">{{ $departemenTampil }}</span>
                            </p>
                            <p class="text-[11px] text-blue-200 mt-1.5 font-medium">
                                NIK Kerja: <strong class="text-white">{{ $userKaryawan->nik_kerja ?? '-' }}</strong>
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 w-full md:w-auto justify-end">
                        <button @click="showModalQr = true" class="bg-white hover:bg-blue-50 text-blue-700 px-5 py-3 rounded-2xl text-xs font-extrabold transition-all shadow-md flex items-center gap-2">
                            <i class="bi bi-qr-code text-base"></i> Kartu QR
                        </button>
                        <button @click="showModalPassword = true" class="bg-blue-900/60 hover:bg-blue-900 text-white border border-white/30 px-5 py-3 rounded-2xl text-xs font-extrabold transition-all shadow-sm flex items-center gap-2 backdrop-blur-md">
                            <i class="bi bi-key-fill text-base"></i> Ubah Sandi
                        </button>
                    </div>
                </div>
            </div>

            <!-- 2. GRID KONTEN UTAMA -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- KOLOM KIRI -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- DATA PRIBADI -->
                    <div class="bg-white rounded-3xl p-7 border border-slate-200 shadow-sm space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <h3 class="font-extrabold text-sm text-slate-900 flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm"><i class="bi bi-person-lines-fill"></i></span> Data Pribadi Karyawan
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">NIK KTP</span>
                                <span class="font-extrabold text-slate-800 text-sm">{{ $userKaryawan->nik_ktp ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">No. HP / WhatsApp</span>
                                <span class="font-extrabold text-slate-800 text-sm">{{ $userKaryawan->no_hp ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Tempat, Tanggal Lahir</span>
                                <span class="font-extrabold text-slate-800 text-sm">
                                    {{ $userKaryawan->tempat_lahir ?? '-' }}, 
                                    {{ $userKaryawan && $userKaryawan->tanggal_lahir ? \Carbon\Carbon::parse($userKaryawan->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                                </span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Jenis Kelamin</span>
                                <span class="font-extrabold text-slate-800 text-sm">
                                    {{ $userKaryawan && $userKaryawan->jenis_kelamin == 'L' ? 'Laki-laki' : ($userKaryawan && $userKaryawan->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                                </span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Umur</span>
                                <span class="font-extrabold text-slate-800 text-sm">
                                    @if($userKaryawan && $userKaryawan->tanggal_lahir)
                                        {{ \Carbon\Carbon::parse($userKaryawan->tanggal_lahir)->age }} Tahun
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Pendidikan Terakhir</span>
                                <span class="font-extrabold text-slate-800 text-sm">
                                    {{ $userKaryawan->tingkat_pendidikan ?? '-' }} 
                                    {{ $userKaryawan->nama_sekolah ? '- ' . $userKaryawan->nama_sekolah : '' }}
                                </span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 md:col-span-2">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Alamat Domisili</span>
                                <span class="font-extrabold text-slate-800 text-sm leading-relaxed">{{ $userKaryawan->alamat ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- DATA KEPEGAWAIAN -->
                    <div class="bg-white rounded-3xl p-7 border border-slate-200 shadow-sm space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <h3 class="font-extrabold text-sm text-slate-900 flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm"><i class="bi bi-briefcase-fill"></i></span> Data Kepegawaian & Posisi
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">NIK Kerja</span>
                                <span class="font-extrabold text-slate-800 text-sm">{{ $userKaryawan->nik_kerja ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Jabatan</span>
                                <span class="font-extrabold text-slate-800 text-sm">{{ $jabatanTampil }}</span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Departemen</span>
                                <span class="font-extrabold text-slate-800 text-sm">{{ $departemenTampil }}</span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Tanggal Bergabung</span>
                                <span class="font-extrabold text-slate-800 text-sm">
                                    {{ $userKaryawan && $userKaryawan->created_at ? $userKaryawan->created_at->translatedFormat('d F Y') : '-' }}
                                </span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 md:col-span-2">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Masa Kerja</span>
                                <span class="font-extrabold text-blue-600 text-sm">
                                    @if($userKaryawan && $userKaryawan->created_at)
                                        @php
                                            $diff = $userKaryawan->created_at->diff(now());
                                            $masaKerja = [];
                                            if($diff->y > 0) $masaKerja[] = $diff->y . ' Tahun';
                                            if($diff->m > 0) $masaKerja[] = $diff->m . ' Bulan';
                                            if($diff->d > 0 && $diff->y == 0) $masaKerja[] = $diff->d . ' Hari';
                                            echo implode(' ', $masaKerja) ?: 'Baru bergabung';
                                        @endphp
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- KOLOM KANAN -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- STATISTIK KEHADIRAN -->
                    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-3xl p-6 shadow-lg shadow-emerald-500/20 text-white relative overflow-hidden">
                        <div class="absolute -right-4 -bottom-4 opacity-20 text-8xl pointer-events-none">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="relative z-10">
                            <span class="text-[11px] font-extrabold text-emerald-100 uppercase tracking-wider block mb-2">Kehadiran Bulan Ini</span>
                            <h3 class="text-4xl font-black">{{ $jumlahHadirBulanIni ?? 0 }} <span class="text-sm font-bold text-emerald-100">Hari</span></h3>
                            <p class="text-[11px] text-emerald-100 mt-2 font-medium">
                                <i class="bi bi-info-circle-fill mr-1"></i> Total kehadiran tercatat bulan ini
                            </p>
                        </div>
                    </div>

                    <!-- INFORMASI AKUN -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h3 class="font-extrabold text-sm text-slate-900 flex items-center gap-2">
                                <i class="bi bi-shield-lock-fill text-emerald-600"></i> Informasi Akun Login
                            </h3>
                        </div>
                        <div class="space-y-3 text-xs">
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-0.5">Username</span>
                                <span class="font-extrabold text-slate-800 text-xs">{{ $user->username ?? $user->email ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-0.5">Hak Akses (Role)</span>
                                <span class="inline-flex items-center bg-blue-100 text-blue-700 font-extrabold uppercase tracking-wide text-[10px] px-2.5 py-1 rounded-full">
                                    {{ $user->role ?? 'HRD' }}
                                </span>
                            </div>
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-0.5">Login Terakhir</span>
                                <span class="font-extrabold text-slate-800 text-xs">
                                    {{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->translatedFormat('d M Y, H:i') : 'Belum pernah login' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- TOMBOL KELUAR AKUN -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold py-3.5 px-4 rounded-2xl text-xs transition-all flex items-center justify-center gap-2 border border-rose-100">
                                <i class="bi bi-box-arrow-right text-base"></i> Keluar Sistem (Logout)
                            </button>
                        </form>
                    </div>

                </div>

            </div>

        </div>
    </main>
</div>

<!-- MODAL QR CODE -->
<div x-show="showModalQr" x-effect="showModalQr && $nextTick(() => buatQrHrd())" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4" style="display: none;" x-transition.opacity>
    <div @click.away="showModalQr = false" class="bg-white rounded-3xl p-7 max-w-xs w-full text-center space-y-4 shadow-2xl border border-slate-100" x-transition.scale>
        <div class="text-[11px] font-black uppercase tracking-widest text-blue-600">ID Card Karyawan</div>
        <div class="text-[10px] text-slate-400 font-semibold -mt-2">Indokom System</div>
        
        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-center justify-center shadow-inner">
         <div id="qr-hrd" data-token="{{ $userKaryawan->barcode_uid ?? '' }}"></div>
        </div>

        <div>
            <div class="font-black text-slate-900 text-sm tracking-tight">{{ $namaTampil }}</div>
            <div class="text-xs font-bold text-slate-500 mt-0.5">{{ $userKaryawan->nik_kerja ?? '-' }}</div>
        </div>

        <button @click="showModalQr = false" class="w-full bg-slate-900 hover:bg-blue-600 text-white font-bold py-3 rounded-2xl text-xs transition-colors">Tutup</button>
    </div>
</div>

<!-- MODAL UBAH SANDI -->
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

<script src="/js/qrcode.min.js"></script>
<script>
    // QR digambar di browser dari barcode_uid -- tidak ada data yang dikirim ke layanan luar.
    function buatQrHrd() {
        var el = document.getElementById('qr-hrd');
        if (!el) return;
        el.innerHTML = '';
        if (!el.dataset.token || typeof QRCode === 'undefined') {
            el.innerHTML = '<div class="text-xs text-rose-600">QR belum tersedia.</div>';
            return;
        }
        new QRCode(el, {
            text: el.dataset.token,
            width: 160, height: 160,
            colorDark: '#000000', colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });
    }
</script>

</body>
</html>