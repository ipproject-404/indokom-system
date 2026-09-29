<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Karyawan - HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">

<div class="flex min-h-screen bg-gray-50">

    <!-- =========================
         SIDEBAR (Disalin dari Dashboard)
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
            <a href="{{ route('dashboard.hrd') }}" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-grid-1x2-fill mr-3"></i>
                <span class="text-sm font-medium">Dashboard</span>
            </a>

            <div class="uppercase text-gray-400 text-xs font-bold mb-3 mt-6">Kelola Karyawan</div>
            <!-- Menu Daftar Karyawan Aktif -->
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
        <!-- HEADER -->
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-10">
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

        <!-- CONTENT BODY -->
        <div class="p-6 grow overflow-y-auto">
            
            <!-- Alert Success -->
            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 px-4 py-4 rounded-lg mb-6 shadow-sm">
                <div class="flex items-center text-emerald-700">
                    <i class="bi bi-check-circle-fill mr-2 text-lg"></i> 
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            </div>
            @endif

            <!-- KOTAK FILTER PENCARIAN -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
                <form action="{{ route('karyawan.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <!-- Cari Nama/NIK -->
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Cari Karyawan</label>
                        <div class="relative">
                            <i class="bi bi-search absolute left-3 top-2.5 text-gray-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama atau NIK..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition-shadow">
                        </div>
                    </div>
                    
                    <!-- Filter Departemen -->
                    <div class="w-full md:w-48">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Departemen</label>
                        <select name="departemen" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none appearance-none">
                            <option value="">Semua Departemen</option>
                            @foreach($departemens as $dept)
                                <option value="{{ $dept->id }}" {{ request('departemen') == $dept->id ? 'selected' : '' }}>{{ $dept->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Jabatan -->
                    <div class="w-full md:w-48">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Jabatan</label>
                        <select name="jabatan" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none appearance-none">
                            <option value="">Semua Jabatan</option>
                            @foreach($jabatans as $jab)
                                <option value="{{ $jab->id }}" {{ request('jabatan') == $jab->id ? 'selected' : '' }}>{{ $jab->nama_jabatan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div class="w-full md:w-36">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none appearance-none">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <!-- Tombol Submit, Excel, & Reset -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 transition-colors shadow-sm">
                            Filter
                        </button>
                        
                        <button type="submit" name="export" value="excel" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm flex items-center">
                            <i class="bi bi-file-earmark-excel mr-1"></i> Excel
                        </button>

                        @if(request()->hasAny(['search', 'departemen', 'jabatan', 'status']))
                        <a href="{{ route('karyawan.index') }}" class="bg-gray-100 text-gray-600 px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors" title="Reset Filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        @endif
                    </div>
                </form>
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
                                    <div>{{ $kry->no_hp }}</div>
                                    <div class="text-xs text-gray-400">{{ $kry->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="text-gray-800 font-medium">{{ $kry->jabatan->nama_jabatan ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $kry->departemen->nama_departemen ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if($kry->status == 'aktif')
                                        <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-md text-[11px] font-bold tracking-wide">AKTIF</span>
                                    @else
                                        <span class="bg-rose-100 text-rose-700 px-2 py-1 rounded-md text-[11px] font-bold tracking-wide">NONAKTIF</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <a href="{{ route('karyawan.show', $kry->id) }}" class="text-emerald-600 hover:text-emerald-800 mx-2 inline-block" title="Detail Karyawan">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('karyawan.qr', $kry->id) }}" class="text-indigo-600 hover:text-indigo-800 mx-2 inline-block" title="Cetak QR" target="_blank">
                                        <i class="bi bi-qr-code"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="bi bi-search text-3xl mb-2 text-gray-300"></i>
                                        <p>Data karyawan tidak ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if(method_exists($karyawans, 'links'))
                <div class="px-5 py-3 border-t border-gray-200">
                    {{ $karyawans->links() }}
                </div>
                @endif
            </div>
        </div>
    </main>
</div>

</body>
</html>