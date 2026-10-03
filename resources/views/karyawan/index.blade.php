<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Karyawan - HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">

<div class="flex min-h-screen bg-gray-50">

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
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-20">
            <div class="flex items-center">
                <a href="{{ route('dashboard.hrd') }}" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg mr-3 transition-colors">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="font-bold text-xl text-gray-800">Kelola Data Karyawan</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Manajemen data karyawan dan pengaturan akun</p>
                </div>
            </div>
            <a href="{{ route('karyawan.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm">
                <i class="bi bi-plus-lg mr-1"></i> Tambah Karyawan
            </a>
        </nav>

        <div class="p-6 grow overflow-y-auto">

            <!-- ALERT SUCCESS -->
            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 px-4 py-4 rounded-lg mb-6 shadow-sm">
                <div class="flex items-center text-emerald-700">
                    <i class="bi bi-check-circle-fill mr-2 text-lg"></i>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            </div>
            @endif

            <!-- FILTER PANEL -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6" x-data="{ showAdvanced: false }">
                
                <form action="{{ route('karyawan.index') }}" method="GET">
                    
                    <!-- BARIS 1: SEARCH UTAMA + TOMBOL -->
                    <div class="p-5 border-b border-gray-100">
                        <div class="flex flex-col md:flex-row gap-3">
                            <div class="flex-1 relative">
                                <i class="bi bi-search absolute left-3.5 top-3 text-gray-400"></i>
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="Cari nama, NIK kerja, atau NIK KTP..."
                                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <button type="submit" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors shadow-sm flex items-center justify-center gap-2">
                                <i class="bi bi-funnel-fill"></i> Cari
                            </button>
                            <button type="submit" name="export" value="excel" class="bg-emerald-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center justify-center gap-2">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </button>
                            @if(request()->hasAny(['search','perusahaan','divisi','departemen','jabatan','jenis_kelamin','pendidikan','umur_min','umur_max','status']))
                                <a href="{{ route('karyawan.index') }}" class="bg-rose-50 text-rose-600 border border-rose-200 px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-rose-100 transition-colors flex items-center gap-2">
                                    <i class="bi bi-x-circle"></i> Reset
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- BARIS 2: QUICK FILTERS (Selalu Tampil) -->
                    <div class="p-5 grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Divisi</label>
                            <select name="divisi" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Semua Divisi</option>
                                @foreach($divisis as $div)
                                    <option value="{{ $div->id }}" {{ request('divisi') == $div->id ? 'selected' : '' }}>
                                        {{ $div->nama_divisi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Departemen</label>
                            <select name="departemen" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Semua Departemen</option>
                                @foreach($departemens as $dept)
                                    <option value="{{ $dept->id }}" {{ request('departemen') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->nama_departemen }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Jabatan</label>
                            <select name="jabatan" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Semua Jabatan</option>
                                @foreach($jabatans as $jab)
                                    <option value="{{ $jab->id }}" {{ request('jabatan') == $jab->id ? 'selected' : '' }}>
                                        {{ $jab->nama_jabatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Status</label>
                            <select name="status" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <!-- TOGGLE ADVANCED FILTER -->
                    <div class="px-5 pb-3">
                        <button type="button" @click="showAdvanced = !showAdvanced"
                                class="w-full text-left text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1.5 py-1 transition-colors">
                            <i class="bi bi-chevron-down transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            <span x-text="showAdvanced ? 'Sembunyikan Filter Lanjutan' : 'Tampilkan Filter Lanjutan (JK, Pendidikan, Umur, Perusahaan)'"></span>
                        </button>
                    </div>

                    <!-- BARIS 3: ADVANCED FILTERS (Toggle) -->
                    <div x-show="showAdvanced" x-collapse class="border-t border-gray-100 p-5 grid grid-cols-1 md:grid-cols-4 gap-3 bg-blue-50/30">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Perusahaan</label>
                            <select name="perusahaan" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Semua Perusahaan</option>
                                @foreach($perusahaans as $pt)
                                    <option value="{{ $pt->id }}" {{ request('perusahaan') == $pt->id ? 'selected' : '' }}>
                                        {{ $pt->kode }} - {{ $pt->nama_perusahaan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Semua</option>
                                <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Pendidikan</label>
                            <select name="pendidikan" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Semua Pendidikan</option>
                                @foreach($opsiPendidikan as $opsi)
                                    <option value="{{ $opsi }}" {{ request('pendidikan') == $opsi ? 'selected' : '' }}>
                                        {{ $opsi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Umur (Tahun)</label>
                            <div class="flex gap-2 items-center">
                                <input type="number" name="umur_min" value="{{ request('umur_min') }}" 
                                       placeholder="Min" min="0" max="100"
                                       class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <span class="text-gray-400 font-bold text-xs">s/d</span>
                                <input type="number" name="umur_max" value="{{ request('umur_max') }}" 
                                       placeholder="Max" min="0" max="100"
                                       class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- BARIS 4: TOMBOL TERAPKAN (kalau advanced terbuka) -->
                    <div x-show="showAdvanced" class="px-5 pb-4 bg-blue-50/30 border-t-0 flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
                            <i class="bi bi-funnel-fill"></i> Terapkan Filter
                        </button>
                    </div>

                </form>
            </div>

            <!-- INFO HASIL -->
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <p class="text-sm text-gray-500">
                    Menampilkan
                    <span class="font-bold text-gray-700">{{ $karyawans->firstItem() ?? 0 }}–{{ $karyawans->lastItem() ?? 0 }}</span>
                    dari <span class="font-bold text-gray-700">{{ $karyawans->total() }}</span> karyawan
                    @if(request('search'))
                        — cari: <span class="text-blue-600 font-semibold">"{{ request('search') }}"</span>
                    @endif
                </p>
                @if($karyawans->hasPages())
                <div class="text-xs text-gray-500 bg-gray-100 px-3 py-1.5 rounded-lg">
                    Halaman <span class="font-bold">{{ $karyawans->currentPage() }}</span> dari <span class="font-bold">{{ $karyawans->lastPage() }}</span>
                </div>
                @endif
            </div>

            <!-- TABEL DATA KARYAWAN -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider border-b border-gray-200">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Nama & NIK</th>
                                <th class="px-5 py-3 font-semibold">Kontak</th>
                                <th class="px-5 py-3 font-semibold">Jabatan & Dept</th>
                                <th class="px-5 py-3 font-semibold">Perusahaan</th>
                                <th class="px-5 py-3 font-semibold text-center">JK</th>
                                <th class="px-5 py-3 font-semibold text-center">Pendidikan</th>
                                <th class="px-5 py-3 font-semibold text-center">Umur</th>
                                <th class="px-5 py-3 font-semibold text-center">Status</th>
                                <th class="px-5 py-3 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                            @forelse($karyawans as $kry)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="font-semibold text-gray-900">{{ $kry->nama_lengkap }}</div>
                                    <div class="text-xs text-gray-500 font-medium">NIK: {{ $kry->nik_kerja }}</div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="text-xs">{{ $kry->no_hp }}</div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="text-gray-800 font-medium text-xs">{{ $kry->jabatan->nama_jabatan ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $kry->departemen->nama_departemen ?? '-' }}</div>
                                    <div class="text-[10px] text-blue-600 font-semibold">{{ $kry->departemen->divisi->nama_divisi ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="text-xs text-gray-700">{{ $kry->perusahaan->nama_perusahaan ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if($kry->jenis_kelamin == 'L')
                                        <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-2 py-1 rounded-md text-[10px] font-bold">
                                            <i class="bi bi-gender-male"></i> L
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-pink-50 text-pink-700 px-2 py-1 rounded-md text-[10px] font-bold">
                                            <i class="bi bi-gender-female"></i> P
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @php
                                        $pend = $kry->pendidikan ?? '-';
                                        $jenjang = explode('-', $pend)[0] ?? '-';
                                    @endphp
                                    <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded-md text-[10px] font-bold" title="{{ $pend }}">
                                        {{ $jenjang }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if($kry->tanggal_lahir)
                                        <span class="text-xs font-bold text-gray-700">
                                            {{ \Carbon\Carbon::parse($kry->tanggal_lahir)->age }}
                                        </span>
                                        <span class="text-[10px] text-gray-400">thn</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if($kry->status == 'aktif')
                                        <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-md text-[10px] font-bold tracking-wide">AKTIF</span>
                                    @else
                                        <span class="bg-rose-100 text-rose-700 px-2 py-1 rounded-md text-[10px] font-bold tracking-wide">NONAKTIF</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <a href="{{ route('karyawan.show', $kry->id) }}" class="text-emerald-600 hover:text-emerald-800 mx-1.5 inline-block" title="Detail Karyawan">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('karyawan.qr', $kry->id) }}" class="text-indigo-600 hover:text-indigo-800 mx-1.5 inline-block" title="Cetak QR" target="_blank">
                                        <i class="bi bi-qr-code"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="bi bi-search text-4xl mb-3 text-gray-300"></i>
                                        <p class="text-gray-500 font-semibold mb-1">Data karyawan tidak ditemukan</p>
                                        <p class="text-xs text-gray-400">Coba ubah filter atau kata kunci pencarian</p>
                                        @if(request()->hasAny(['search','perusahaan','divisi','departemen','jabatan','jenis_kelamin','pendidikan','umur_min','umur_max','status']))
                                            <a href="{{ route('karyawan.index') }}" class="mt-3 text-blue-600 hover:underline text-xs font-bold">
                                                <i class="bi bi-arrow-counterclockwise mr-1"></i> Reset semua filter
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                @if($karyawans->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $karyawans->links() }}
                </div>
                @endif
            </div>
        </div>
    </main>
</div>

</body>
</html>