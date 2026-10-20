<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Karyawan - HRD</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">

<div class="flex min-h-screen bg-gray-50">

    @include('partials.sidebar-hrd')

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

        <div class="p-6 grow overflow-y-auto max-w-7xl mx-auto w-full">

            <!-- ALERT SUCCESS -->
            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 px-4 py-4 rounded-lg mb-6 shadow-sm">
                <div class="flex items-center text-emerald-700">
                    <i class="bi bi-check-circle-fill mr-2 text-lg"></i>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            </div>
            @endif

            <!-- FILTER PANEL (RAPI & SIMPLE) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6" x-data="{ showFilter: false }">
                <form action="{{ route('karyawan.index') }}" method="GET" id="filterForm">
                    
                    <!-- BARIS ATAS: SEARCH, FILTER TOGGLE, EXCEL -->
                    <div class="p-4 flex flex-col md:flex-row gap-3 items-center justify-between">
                        
                        <!-- Search Box -->
                        <div class="w-full md:w-1/2 relative">
                            <i class="bi bi-search absolute left-3.5 top-3 text-gray-400"></i>
                            <input type="text" 
                                   name="search" 
                                   id="searchRealtime"
                                   value="{{ request('search') }}"
                                   autocomplete="off"
                                   placeholder="Cari nama, NIK, atau no HP..."
                                   class="w-full pl-10 pr-12 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition-shadow">
                            <span id="searchBadge" class="hidden absolute right-3 top-2.5 text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-1 rounded-md">
                                <i class="bi bi-lightning-charge-fill"></i> Live
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="w-full md:w-auto flex items-center gap-2">
                            <!-- Tombol Toggle Filter -->
                            <button type="button" @click="showFilter = !showFilter"
                                    class="flex-1 md:flex-none justify-center bg-white border border-gray-300 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-gray-50 transition-colors flex items-center gap-2 shadow-sm">
                                <i class="bi bi-funnel" :class="showFilter ? 'text-blue-600' : 'text-gray-400'"></i>
                                <span x-text="showFilter ? 'Tutup Filter' : 'Filter Lanjutan'"></span>
                            </button>

                            <!-- Tombol Excel -->
                            <button type="submit" name="export" value="excel" 
                                    class="flex-1 md:flex-none justify-center bg-emerald-600 text-white px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-2">
                                <i class="bi bi-file-earmark-excel"></i> Export
                            </button>

                            <!-- Reset (Hanya muncul jika ada filter aktif) -->
                            @if(request()->hasAny(['search','perusahaan','divisi','departemen','jabatan','jenis_kelamin','pendidikan','umur_min','umur_max','status','status_pernikahan']))
                                <a href="{{ route('karyawan.index') }}" class="bg-rose-50 text-rose-600 border border-rose-200 px-3 py-2.5 rounded-lg text-sm font-bold hover:bg-rose-100 transition-colors flex items-center" title="Reset Filter">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- INFO LIVE SEARCH -->
                    <p id="searchInfo" class="text-xs text-gray-500 px-4 pb-3 hidden">
                        <i class="bi bi-info-circle text-blue-500"></i>
                        Menampilkan <b id="matchCount" class="text-gray-700">0</b> dari <b id="totalCount" class="text-gray-700">0</b> karyawan di halaman ini.
                        <span class="text-gray-400 mx-1">|</span>
                        <span class="text-blue-600 font-semibold">Tekan <kbd class="px-1.5 py-0.5 bg-gray-100 border border-gray-300 rounded text-[10px] font-mono">Enter</kbd> untuk cari ke semua halaman</span>
                    </p>

                    <!-- COLLAPSIBLE ADVANCED FILTERS -->
                    <div x-show="showFilter" x-collapse x-cloak class="border-t border-gray-100 p-4 bg-gray-50/50 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3 rounded-b-xl">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Perusahaan</label>
                            <select name="perusahaan" onchange="this.form.submit()"
                                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                                <option value="">Semua PT</option>
                                @foreach($perusahaans as $pt)
                                    <option value="{{ $pt->id }}" {{ request('perusahaan') == $pt->id ? 'selected' : '' }}>{{ $pt->kode }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Divisi</label>
                            <select name="divisi" onchange="this.form.submit()"
                                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                                <option value="">Semua Divisi</option>
                                @foreach($divisis as $div)
                                    <option value="{{ $div->id }}" {{ request('divisi') == $div->id ? 'selected' : '' }}>{{ $div->nama_divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Departemen</label>
                            <select name="departemen" onchange="this.form.submit()"
                                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                                <option value="">Semua Dept</option>
                                @foreach($departemens as $dept)
                                    <option value="{{ $dept->id }}" {{ request('departemen') == $dept->id ? 'selected' : '' }}>{{ $dept->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Jabatan</label>
                            <select name="jabatan" onchange="this.form.submit()"
                                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                                <option value="">Semua Jabatan</option>
                                @foreach($jabatans as $jab)
                                    <option value="{{ $jab->id }}" {{ request('jabatan') == $jab->id ? 'selected' : '' }}>{{ $jab->nama_jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Status Kerja</label>
                            <select name="status" onchange="this.form.submit()"
                                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Jenis Kelamin</label>
                            <select name="jenis_kelamin" onchange="this.form.submit()"
                                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                                <option value="">Semua</option>
                                <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Status Nikah</label>
                            <select name="status_pernikahan" onchange="this.form.submit()"
                                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                                <option value="">Semua</option>
                                <option value="belum_menikah" {{ request('status_pernikahan') == 'belum_menikah' ? 'selected' : '' }}>Belum Menikah</option>
                                <option value="menikah" {{ request('status_pernikahan') == 'menikah' ? 'selected' : '' }}>Menikah</option>
                                <option value="cerai_hidup" {{ request('status_pernikahan') == 'cerai_hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                                <option value="cerai_mati" {{ request('status_pernikahan') == 'cerai_mati' ? 'selected' : '' }}>Cerai Mati</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Pendidikan</label>
                            <select name="pendidikan" onchange="this.form.submit()"
                                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                                <option value="">Semua</option>
                                @foreach($opsiPendidikan as $opsi)
                                    <option value="{{ $opsi }}" {{ request('pendidikan') == $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Umur (Tahun)</label>
                            <div class="flex gap-1 items-center">
                                <input type="number" name="umur_min" value="{{ request('umur_min') }}" placeholder="Min" 
                                       onchange="this.form.submit()" class="w-full px-2 py-2 bg-white border border-gray-200 rounded-lg text-sm text-center">
                                <span class="text-gray-400 text-xs">-</span>
                                <input type="number" name="umur_max" value="{{ request('umur_max') }}" placeholder="Max" 
                                       onchange="this.form.submit()" class="w-full px-2 py-2 bg-white border border-gray-200 rounded-lg text-sm text-center">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- INFO HASIL -->
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <p class="text-sm text-gray-500">
                    Menampilkan
                    <span class="font-bold text-gray-700">{{ $karyawans->firstItem() ?? 0 }}–{{ $karyawans->lastItem() ?? 0 }}</span>
                    dari <span class="font-bold text-gray-700">{{ $karyawans->total() }}</span> karyawan
                </p>
                @if($karyawans->hasPages())
                <div class="text-xs text-gray-500 bg-gray-100 px-3 py-1.5 rounded-lg border border-gray-200">
                    Hal. <span class="font-bold text-gray-700">{{ $karyawans->currentPage() }}</span> / <span class="font-bold text-gray-700">{{ $karyawans->lastPage() }}</span>
                </div>
                @endif
            </div>

            <!-- TABEL DATA KARYAWAN (4 KOLOM UTAMA) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed min-w-[800px]">
                        <thead class="bg-gray-50 text-gray-500 text-[10px] uppercase tracking-wider border-b border-gray-200">
                            <tr>
                                <th class="px-5 py-4 font-bold text-left w-[30%]">Karyawan</th>
                                <th class="px-5 py-4 font-bold text-left w-[35%]">Posisi & Penempatan</th>
                                <th class="px-5 py-4 font-bold text-center w-[15%]">Status</th>
                                <th class="px-5 py-4 font-bold text-center w-[20%]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700 divide-y divide-gray-100" id="tabelKaryawan">
                            @forelse($karyawans as $kry)
                            @php
                                // Tetap sertakan semua data di sini agar bisa dicari via Live Search meskipun tidak tampil di tabel
                                $searchText = strtolower(implode(' ', [
                                    $kry->nama_lengkap ?? '',
                                    $kry->nik_kerja ?? '',
                                    $kry->nik_ktp ?? '',
                                    $kry->no_hp ?? '',
                                    $kry->jabatan->nama_jabatan ?? '',
                                    $kry->departemen->nama_departemen ?? '',
                                    $kry->departemen->divisi->nama_divisi ?? '',
                                    $kry->perusahaan->nama_perusahaan ?? '',
                                    $kry->pendidikan ?? '',
                                    $kry->status_pernikahan ?? '',
                                ]));
                            @endphp
                            <tr class="karyawan-row hover:bg-blue-50/30 transition-colors" data-search="{{ $searchText }}">

                                <!-- 1. KARYAWAN (Avatar + Nama + NIK) -->
                                <td class="px-5 py-4 align-middle">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs shrink-0 shadow-inner">
                                            {{ strtoupper(substr($kry->nama_lengkap, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-gray-900 text-sm truncate" title="{{ $kry->nama_lengkap }}">
                                                {{ $kry->nama_lengkap }}
                                            </div>
                                            <div class="text-[11px] text-gray-500 mt-0.5 truncate">
                                                <i class="bi bi-person-vcard text-gray-400 mr-1"></i>{{ $kry->nik_kerja }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. POSISI & PENEMPATAN -->
                                <td class="px-5 py-4 align-middle">
                                    <div class="font-bold text-gray-800 text-sm truncate" title="{{ $kry->jabatan->nama_jabatan ?? '-' }}">
                                        {{ $kry->jabatan->nama_jabatan ?? '-' }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-1 truncate">
                                        {{ $kry->departemen->nama_departemen ?? '-' }} 
                                        @if($kry->departemen->divisi)
                                            <span class="text-gray-300 mx-1">•</span> {{ $kry->departemen->divisi->nama_divisi }}
                                        @endif
                                    </div>
                                    <div class="text-[10px] font-medium text-blue-600 mt-1 truncate">
                                        <i class="bi bi-building mr-1"></i>{{ $kry->perusahaan->nama_perusahaan ?? '-' }}
                                    </div>
                                </td>

                                <!-- 3. STATUS -->
                                <td class="px-5 py-4 align-middle text-center">
                                    @if($kry->status == 'aktif')
                                        <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg text-xs font-bold border border-emerald-100 shadow-sm">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> AKTIF
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-600 px-3 py-1.5 rounded-lg text-xs font-bold border border-gray-200">
                                            <span class="w-2 h-2 rounded-full bg-gray-400"></span> OFF
                                        </span>
                                    @endif
                                </td>

                                <!-- 4. AKSI (Tombol Teks) -->
                                <td class="px-5 py-4 align-middle text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('karyawan.show', $kry->id) }}" 
                                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs font-bold transition-colors shadow-sm">
                                            Detail
                                        </a>
                                        <a href="{{ route('karyawan.qr', $kry->id) }}" target="_blank" 
                                           class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-3 py-1.5 rounded-md text-xs font-bold transition-colors shadow-sm">
                                            QR
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="bi bi-search text-4xl mb-3 text-gray-300"></i>
                                        <p class="text-gray-500 font-semibold mb-1">Data tidak ditemukan</p>
                                        <p class="text-xs text-gray-400">Coba sesuaikan kata kunci atau filter Anda</p>
                                        @if(request()->hasAny(['search','perusahaan','divisi','departemen','jabatan','jenis_kelamin','pendidikan','umur_min','umur_max','status','status_pernikahan']))
                                            <a href="{{ route('karyawan.index') }}" class="mt-3 text-blue-600 hover:underline text-xs font-bold">
                                                <i class="bi bi-arrow-counterclockwise mr-1"></i> Reset semua filter
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse

                            <!-- Baris kosong khusus search realtime -->
                            <tr id="emptySearchRow" class="hidden">
                                <td colspan="4" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="bi bi-search text-4xl mb-3 text-gray-300"></i>
                                        <p class="text-gray-500 font-semibold mb-1">Tidak ada yang cocok di halaman ini</p>
                                        <p class="text-xs text-gray-400">
                                            Tekan <kbd class="px-1.5 py-0.5 bg-gray-100 border border-gray-300 rounded text-[10px] font-mono">Enter</kbd> untuk cari ke semua halaman
                                        </p>
                                    </div>
                                </td>
                            </tr>
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

<script>
    // ==========================================
    // SEARCH REALTIME (Client-side instant filter)
    // ==========================================
    (function () {
        const input     = document.getElementById('searchRealtime');
        const badge     = document.getElementById('searchBadge');
        const info      = document.getElementById('searchInfo');
        const matchEl   = document.getElementById('matchCount');
        const totalEl   = document.getElementById('totalCount');
        const emptyRow  = document.getElementById('emptySearchRow');
        const rows      = document.querySelectorAll('#tabelKaryawan .karyawan-row');

        if (!input || !rows.length) return;

        totalEl.textContent = rows.length;

        function filterRows() {
            const q = input.value.trim().toLowerCase();
            let shown = 0;

            rows.forEach(row => {
                const text = row.dataset.search || '';
                const match = !q || text.includes(q);

                if (match) {
                    row.style.display = '';
                    shown++;
                } else {
                    row.style.display = 'none';
                }
            });

            matchEl.textContent = shown;

            if (q) {
                badge.classList.remove('hidden');
                info.classList.remove('hidden');

                if (emptyRow) {
                    emptyRow.classList.toggle('hidden', shown > 0);
                }
            } else {
                badge.classList.add('hidden');
                info.classList.add('hidden');
                if (emptyRow) emptyRow.classList.add('hidden');
            }
        }

        input.addEventListener('input', filterRows);

        if (input.value.trim() !== '') {
            filterRows();
        }

        // Enter → cari di semua halaman (server-side)
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filterForm').submit();
            }
        });
    })();
</script>

</body>
</html>