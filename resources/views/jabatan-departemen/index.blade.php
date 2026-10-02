<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struktur Bagan Jabatan & Departemen - HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        const karyawanPerDepartemen = @json($karyawanPerDepartemen);

        function bukaModalEditDepartemen(id, nama, divisiId, kepalaId) {
            document.getElementById('edit_dept_id').value = id;
            document.getElementById('edit_nama_departemen').value = nama;
            document.getElementById('edit_divisi_id').value = divisiId ?? '';
            document.getElementById('formEditDepartemen').action = "/hrd/departemen/" + id;

            const selectKepala = document.getElementById('edit_kepala_karyawan_id');
            selectKepala.innerHTML = '<option value="">-- Belum ada kepala ditunjuk --</option>';
            const daftarKaryawan = karyawanPerDepartemen[id] || [];
            daftarKaryawan.forEach(function (k) {
                const opt = document.createElement('option');
                opt.value = k.id;
                opt.textContent = k.nama_lengkap;
                if (kepalaId && String(k.id) === String(kepalaId)) opt.selected = true;
                selectKepala.appendChild(opt);
            });
            if (daftarKaryawan.length === 0) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = 'Belum ada karyawan di departemen ini';
                opt.disabled = true;
                selectKepala.appendChild(opt);
            }

            document.getElementById('modalEditDepartemen').classList.remove('hidden');
        }
        function tutupModalEditDepartemen() {
            document.getElementById('modalEditDepartemen').classList.add('hidden');
        }

        function bukaModalEditJabatan(id, nama, deptId) {
            document.getElementById('edit_jab_id').value = id;
            document.getElementById('edit_nama_jabatan').value = nama;
            document.getElementById('edit_dept_induk').value = deptId;
            document.getElementById('formEditJabatan').action = "/hrd/jabatan/" + id;
            document.getElementById('modalEditJabatan').classList.remove('hidden');
        }
        function tutupModalEditJabatan() {
            document.getElementById('modalEditJabatan').classList.add('hidden');
        }
    </script>
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
            <a href="{{ route('karyawan.index') }}" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-people-fill mr-3"></i>
                <span class="text-sm font-medium">Daftar Karyawan</span>
            </a>
            <a href="{{ route('karyawan.create') }}" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-person-plus-fill mr-3"></i>
                <span class="text-sm font-medium">Tambah Karyawan</span>
            </a>
            <a href="{{ route('jabatan.departemen.index') }}" class="flex items-center bg-blue-600 text-white rounded-lg px-4 py-2.5 mb-1 transition-colors">
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
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div class="flex items-center">
                <a href="{{ route('dashboard.hrd') }}" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg mr-3 transition-colors">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="font-bold text-xl text-gray-800">Struktur Bagan Jabatan & Departemen</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Kelola data departemen dan posisi jabatan</p>
                </div>
            </div>
        </nav>

        <div class="p-6 grow overflow-y-auto">
            <div class="max-w-7xl mx-auto space-y-5">

                <!-- ALERTS -->
                @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 px-4 py-3 rounded-lg shadow-sm flex items-center text-emerald-700">
                    <i class="bi bi-check-circle-fill mr-2 text-lg"></i>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
                @endif
                @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 px-4 py-3 rounded-lg shadow-sm flex items-center text-rose-700">
                    <i class="bi bi-exclamation-triangle-fill mr-2 text-lg"></i>
                    <span class="font-bold text-sm">{{ session('error') }}</span>
                </div>
                @endif
                @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 px-4 py-3 rounded-lg shadow-sm text-rose-700 text-sm">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- STATS CARDS -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Divisi</p>
                            <p class="text-2xl font-bold text-purple-600">{{ count($divisis) }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                            <i class="bi bi-diagram-2-fill text-lg"></i>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Departemen</p>
                            <p class="text-2xl font-bold text-blue-600" id="statDepartemen">{{ count($departemens) }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="bi bi-building-fill text-lg"></i>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Jabatan</p>
                            <p class="text-2xl font-bold text-indigo-600" id="statJabatan">{{ count($jabatans) }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <i class="bi bi-person-badge-fill text-lg"></i>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Karyawan</p>
                            <p class="text-2xl font-bold text-emerald-600">
                                {{ collect($karyawanPerDepartemen)->flatten(1)->count() }}
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="bi bi-people-fill text-lg"></i>
                        </div>
                    </div>
                </div>

                <!-- SEARCH & FILTER -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <div class="flex flex-col md:flex-row gap-3 items-stretch md:items-center">
                        <div class="flex-1 relative">
                            <i class="bi bi-search absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" id="searchInput" onkeyup="filterStruktur()" placeholder="Cari departemen atau nama jabatan..."
                                   class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div class="w-full md:w-56">
                            <select id="filterDivisi" onchange="filterStruktur()" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Semua Divisi</option>
                                @foreach($divisis as $div)
                                    <option value="{{ $div->id }}">{{ $div->nama_divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button onclick="toggleExpandAll()" id="btnToggleExpand" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2 whitespace-nowrap">
                            <i class="bi bi-arrows-collapse" id="toggleIcon"></i>
                            <span id="toggleText">Collapse All</span>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-3" id="searchInfo">
                        Menampilkan <span class="font-bold text-gray-700" id="resultCount">{{ count($departemens) }}</span> departemen
                    </p>
                </div>

                <!-- FORM TAMBAH (COLLAPSIBLE) -->
                <div x-data="{ showForms: false }" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <button @click="showForms = !showForms" type="button" class="w-full flex justify-between items-center px-5 py-3.5 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm">
                                <i class="bi bi-plus-circle-fill"></i>
                            </div>
                            <span class="font-bold text-sm text-gray-800">Tambah Departemen / Jabatan Baru</span>
                        </div>
                        <i class="bi bi-chevron-down text-gray-400 transition-transform" :class="showForms ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="showForms" x-collapse class="border-t border-gray-100">
                        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Form Tambah Departemen -->
                            <div class="bg-blue-50/50 rounded-lg p-4 border border-blue-100">
                                <h3 class="font-bold text-gray-800 text-sm mb-3 flex items-center">
                                    <i class="bi bi-building-fill text-blue-600 mr-2"></i> Tambah Departemen Baru
                                </h3>
                                <form action="{{ route('departemen.store') }}" method="POST" class="space-y-2">
                                    @csrf
                                    <select name="divisi_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-600 outline-none bg-white" required>
                                        <option value="">-- Pilih Divisi Induk --</option>
                                        @foreach($divisis as $div)
                                            <option value="{{ $div->id }}">{{ $div->nama_divisi }}</option>
                                        @endforeach
                                    </select>
                                    <div class="flex gap-2">
                                        <input type="text" name="nama_departemen" placeholder="Nama Departemen..." class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-600 outline-none" required>
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors shadow-sm shrink-0">
                                            Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Form Tambah Jabatan -->
                            <div class="bg-indigo-50/50 rounded-lg p-4 border border-indigo-100">
                                <h3 class="font-bold text-gray-800 text-sm mb-3 flex items-center">
                                    <i class="bi bi-person-badge-fill text-indigo-600 mr-2"></i> Tambah Jabatan Baru
                                </h3>
                                <form action="{{ route('jabatan.store') }}" method="POST" class="space-y-2">
                                    @csrf
                                    <select name="departemen_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-600 outline-none bg-white" required>
                                        <option value="">-- Pilih Departemen Induk --</option>
                                        @foreach($departemens as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                                        @endforeach
                                    </select>
                                    <div class="flex gap-2">
                                        <input type="text" name="nama_jabatan" placeholder="Nama Jabatan..." class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-600 outline-none" required>
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors shadow-sm shrink-0">
                                            Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BAGAN STRUKTUR - GRID 2 COLUMN -->
                <div id="departemenGrid" class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                    @forelse($departemens as $dept)
                    @php
                        $listJabatan = $jabatans->where('departemen_id', $dept->id);
                        $jabatanNames = $listJabatan->pluck('nama_jabatan')->implode(', ');
                    @endphp
                    <div class="departemen-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
                         data-nama="{{ strtolower($dept->nama_departemen) }}"
                         data-divisi="{{ $dept->divisi_id }}"
                         data-jabatan="{{ strtolower($jabatanNames) }}">

                        <!-- HEADER DEPARTEMEN -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-start gap-2.5 min-w-0 flex-1">
                                    <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center shrink-0">
                                        <i class="bi bi-building-fill text-base"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-sm uppercase tracking-wide truncate" title="{{ $dept->nama_departemen }}">
                                            {{ $dept->nama_departemen }}
                                        </div>
                                        <div class="flex items-center gap-2 text-[10px] text-blue-100 mt-1 flex-wrap">
                                            <span class="inline-flex items-center bg-white/15 rounded px-1.5 py-0.5">
                                                <i class="bi bi-diagram-2 mr-1"></i>{{ $dept->divisi->nama_divisi ?? '-' }}
                                            </span>
                                            <span class="inline-flex items-center bg-white/15 rounded px-1.5 py-0.5">
                                                <i class="bi bi-person-badge mr-1"></i>{{ $dept->kepalaKaryawan->nama_lengkap ?? 'Belum ditunjuk' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <span class="bg-white text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        {{ $listJabatan->count() }} Jabatan
                                    </span>
                                    <button onclick="bukaModalEditDepartemen('{{ $dept->id }}', '{{ $dept->nama_departemen }}', '{{ $dept->divisi_id }}', '{{ $dept->kepala_karyawan_id }}')" class="text-blue-100 hover:text-white hover:bg-white/20 rounded p-1 transition-colors" title="Edit Departemen">
                                        <i class="bi bi-pencil-square text-sm"></i>
                                    </button>
                                    <form action="{{ route('departemen.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('Hapus departemen {{ $dept->nama_departemen }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-blue-100 hover:text-white hover:bg-white/20 rounded p-1 transition-colors" title="Hapus Departemen">
                                            <i class="bi bi-trash-fill text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- TOGGLE COLLAPSE -->
                            <button onclick="toggleDepartemen(this)" type="button" class="w-full mt-2.5 text-[11px] text-blue-100 hover:text-white font-semibold flex items-center justify-center gap-1 transition-colors">
                                <span class="toggle-label">Sembunyikan Jabatan</span>
                                <i class="bi bi-chevron-up toggle-icon transition-transform"></i>
                            </button>
                        </div>

                        <!-- BODY: DAFTAR JABATAN -->
                        <div class="departemen-body p-3">
                            @if($listJabatan->count() > 0)
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($listJabatan as $jab)
                                    <div class="jabatan-item border border-gray-200 rounded-lg p-2 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-200 transition-colors group"
                                         data-jabatan-name="{{ strtolower($jab->nama_jabatan) }}">
                                        <div class="flex items-start gap-1.5">
                                            <div class="w-7 h-7 rounded-md bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                                                <i class="bi bi-person-badge-fill text-xs"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="font-bold text-gray-800 text-[11px] leading-tight break-words" title="{{ $jab->nama_jabatan }}">
                                                    {{ $jab->nama_jabatan }}
                                                </div>
                                                <div class="text-[9px] text-gray-400">Sub-Posisi</div>
                                            </div>
                                        </div>
                                        <div class="flex justify-end gap-1 mt-1.5 opacity-60 group-hover:opacity-100 transition-opacity">
                                            <button onclick="bukaModalEditJabatan('{{ $jab->id }}', '{{ $jab->nama_jabatan }}', '{{ $jab->departemen_id }}')" class="text-gray-400 hover:text-blue-600 p-0.5 transition-colors" title="Edit">
                                                <i class="bi bi-pencil-fill text-[10px]"></i>
                                            </button>
                                            <form action="{{ route('jabatan.destroy', $jab->id) }}" method="POST" onsubmit="return confirm('Hapus jabatan {{ $jab->nama_jabatan }}?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-rose-600 p-0.5 transition-colors" title="Hapus">
                                                    <i class="bi bi-x-circle-fill text-[11px]"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 text-gray-400 text-xs italic">
                                    <i class="bi bi-info-circle mr-1"></i> Belum ada jabatan terdaftar.
                                </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-12 text-gray-400 text-sm">
                        <i class="bi bi-inbox text-4xl block mb-2 opacity-40"></i>
                        Belum ada data departemen dan jabatan yang tercatat.
                    </div>
                    @endforelse
                </div>

                <!-- EMPTY SEARCH RESULT -->
                <div id="emptySearch" class="hidden text-center py-12 bg-white rounded-xl border border-gray-100">
                    <i class="bi bi-search text-4xl text-gray-300 block mb-3"></i>
                    <p class="text-gray-500 font-semibold">Tidak ada departemen atau jabatan yang cocok</p>
                    <p class="text-xs text-gray-400 mt-1">Coba ubah kata kunci atau filter divisi</p>
                </div>

            </div>
        </div>
    </main>
</div>

<!-- ================= MODAL EDIT DEPARTEMEN ================= -->
<div id="modalEditDepartemen" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg max-w-md w-full p-6">
        <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center">
            <i class="bi bi-pencil-square text-blue-600 mr-2"></i> Edit Departemen
        </h3>
        <form id="formEditDepartemen" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_dept_id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Departemen</label>
                <input type="text" name="nama_departemen" id="edit_nama_departemen" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-600 outline-none" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Divisi Induk</label>
                <select name="divisi_id" id="edit_divisi_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-600 outline-none bg-white" required>
                    @foreach($divisis as $div)
                        <option value="{{ $div->id }}">{{ $div->nama_divisi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kepala Bagian</label>
                <select name="kepala_karyawan_id" id="edit_kepala_karyawan_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-600 outline-none bg-white">
                    <option value="">-- Belum ada kepala ditunjuk --</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">Hanya karyawan yang sudah ada di departemen ini yang muncul di daftar.</p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="tutupModalEditDepartemen()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold transition-colors">
                    Batal
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT JABATAN ================= -->
<div id="modalEditJabatan" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg max-w-md w-full p-6">
        <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center">
            <i class="bi bi-pencil-square text-indigo-600 mr-2"></i> Edit Jabatan
        </h3>
        <form id="formEditJabatan" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_jab_id">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Departemen Induk</label>
                <select name="departemen_id" id="edit_dept_induk" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-600 outline-none bg-white" required>
                    @foreach($departemens as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Jabatan</label>
                <input type="text" name="nama_jabatan" id="edit_nama_jabatan" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-600 outline-none" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="tutupModalEditJabatan()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold transition-colors">
                    Batal
                </button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Alpine.js untuk Collapsible Form -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    // ==========================================
    // SEARCH & FILTER
    // ==========================================
    function filterStruktur() {
        const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
        const divisiFilter = document.getElementById('filterDivisi').value;

        const cards = document.querySelectorAll('.departemen-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const namaDept = card.dataset.nama || '';
            const divisiId = card.dataset.divisi || '';
            const jabatanList = card.dataset.jabatan || '';

            // Cek kecocokan
            const matchSearch = keyword === '' || namaDept.includes(keyword) || jabatanList.includes(keyword);
            const matchDivisi = divisiFilter === '' || divisiId === divisiFilter;

            // Filter jabatan di dalam card (kalau ada search)
            const jabatanItems = card.querySelectorAll('.jabatan-item');
            let jabatanVisible = 0;

            if (keyword !== '') {
                jabatanItems.forEach(item => {
                    const jabName = item.dataset.jabatanName || '';
                    if (jabName.includes(keyword)) {
                        item.style.display = '';
                        jabatanVisible++;
                    } else if (namaDept.includes(keyword)) {
                        // kalau nama dept yang match, tampilkan semua jabatan
                        item.style.display = '';
                        jabatanVisible++;
                    } else {
                        item.style.display = 'none';
                    }
                });
            } else {
                jabatanItems.forEach(item => item.style.display = '');
            }

            if (matchSearch && matchDivisi) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Update info
        document.getElementById('resultCount').textContent = visibleCount;
        const emptyEl = document.getElementById('emptySearch');
        emptyEl.classList.toggle('hidden', visibleCount !== 0);
    }

    // ==========================================
    // TOGGLE PER DEPARTEMEN (Collapse/Expand)
    // ==========================================
    function toggleDepartemen(btn) {
        const card = btn.closest('.departemen-card');
        const body = card.querySelector('.departemen-body');
        const icon = btn.querySelector('.toggle-icon');
        const label = btn.querySelector('.toggle-label');

        if (body.style.display === 'none') {
            body.style.display = '';
            icon.style.transform = 'rotate(0deg)';
            label.textContent = 'Sembunyikan Jabatan';
        } else {
            body.style.display = 'none';
            icon.style.transform = 'rotate(180deg)';
            label.textContent = 'Tampilkan Jabatan';
        }
    }

    // ==========================================
    // EXPAND/COLLAPSE ALL
    // ==========================================
    let allCollapsed = false;

    function toggleExpandAll() {
        allCollapsed = !allCollapsed;
        const cards = document.querySelectorAll('.departemen-card');
        const toggleIcon = document.getElementById('toggleIcon');
        const toggleText = document.getElementById('toggleText');

        cards.forEach(card => {
            const body = card.querySelector('.departemen-body');
            const btn = card.querySelector('button[onclick*="toggleDepartemen"]');
            const icon = btn.querySelector('.toggle-icon');
            const label = btn.querySelector('.toggle-label');

            if (allCollapsed) {
                body.style.display = 'none';
                icon.style.transform = 'rotate(180deg)';
                label.textContent = 'Tampilkan Jabatan';
            } else {
                body.style.display = '';
                icon.style.transform = 'rotate(0deg)';
                label.textContent = 'Sembunyikan Jabatan';
            }
        });

        if (allCollapsed) {
            toggleIcon.className = 'bi bi-arrows-expand';
            toggleText.textContent = 'Expand All';
        } else {
            toggleIcon.className = 'bi bi-arrows-collapse';
            toggleText.textContent = 'Collapse All';
        }
    }

    // Search juga saat filter divisi berubah
    document.getElementById('filterDivisi').addEventListener('change', filterStruktur);
</script>
</body>
</html>