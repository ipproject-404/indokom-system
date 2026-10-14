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
                
                <form action="{{ route('karyawan.index') }}" method="GET" id="filterForm">
                    
                    <!-- BARIS 1: SEARCH REALTIME + TOMBOL -->
                    <div class="p-5 border-b border-gray-100">
                        <div class="flex flex-col md:flex-row gap-3">
                            <div class="flex-1 relative">
                                <i class="bi bi-search absolute left-3.5 top-3 text-gray-400"></i>
                                <input type="text" 
                                       name="search" 
                                       id="searchRealtime"
                                       value="{{ request('search') }}"
                                       autocomplete="off"
                                       placeholder="Ketik nama, NIK kerja, atau NIK KTP... (langsung filter)"
                                       class="w-full pl-10 pr-12 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <span id="searchBadge" class="hidden absolute right-3 top-2.5 text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-1 rounded-md">
                                    <i class="bi bi-lightning-charge-fill"></i> Live
                                </span>
                            </div>

                            <!-- Tombol Excel -->
                            <button type="submit" name="export" value="excel" 
                                    class="bg-emerald-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center justify-center gap-2">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </button>

                            @if(request()->hasAny(['search','perusahaan','divisi','departemen','jabatan','jenis_kelamin','pendidikan','umur_min','umur_max','status']))
                                <a href="{{ route('karyawan.index') }}" class="bg-rose-50 text-rose-600 border border-rose-200 px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-rose-100 transition-colors flex items-center gap-2">
                                    <i class="bi bi-x-circle"></i> Reset
                                </a>
                            @endif
                        </div>

                        <p id="searchInfo" class="text-xs text-gray-500 mt-2 hidden">
                            <i class="bi bi-info-circle text-blue-500"></i>
                            Menampilkan <b id="matchCount" class="text-gray-700">0</b> dari <b id="totalCount" class="text-gray-700">0</b> karyawan di halaman ini
                            <span class="text-gray-400 mx-1">|</span>
                            <span class="text-blue-600 font-semibold">Tekan <kbd class="px-1.5 py-0.5 bg-gray-100 border border-gray-300 rounded text-[10px] font-mono">Enter</kbd> untuk cari di semua halaman</span>
                        </p>
                    </div>

                    <!-- BARIS 2: QUICK FILTERS (auto-submit) -->
                    <div class="p-5 grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Divisi</label>
                            <select name="divisi" onchange="this.form.submit()"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                                <option value="">Semua Divisi</option>
                                @foreach($divisis as $div)
                                    <option value="{{ $div->id }}" {{ request('divisi') == $div->id ? 'selected' : '' }}>{{ $div->nama_divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Departemen</label>
                            <select name="departemen" onchange="this.form.submit()"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                                <option value="">Semua Departemen</option>
                                @foreach($departemens as $dept)
                                    <option value="{{ $dept->id }}" {{ request('departemen') == $dept->id ? 'selected' : '' }}>{{ $dept->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Jabatan</label>
                            <select name="jabatan" onchange="this.form.submit()"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                                <option value="">Semua Jabatan</option>
                                @foreach($jabatans as $jab)
                                    <option value="{{ $jab->id }}" {{ request('jabatan') == $jab->id ? 'selected' : '' }}>{{ $jab->nama_jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Status</label>
                            <select name="status" onchange="this.form.submit()"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <!-- TOGGLE ADVANCED -->
                    <div class="px-5 pb-3">
                        <button type="button" @click="showAdvanced = !showAdvanced"
                                class="w-full text-left text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1.5 py-1 transition-colors">
                            <i class="bi bi-chevron-down transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            <span x-text="showAdvanced ? 'Sembunyikan Filter Lanjutan' : 'Tampilkan Filter Lanjutan (JK, Pendidikan, Umur, Perusahaan)'"></span>
                        </button>
                    </div>

                    <!-- ADVANCED FILTERS (auto-submit untuk dropdown) -->
                    <div x-show="showAdvanced" x-collapse class="border-t border-gray-100 p-5 grid grid-cols-1 md:grid-cols-4 gap-3 bg-blue-50/30">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Perusahaan</label>
                            <select name="perusahaan" onchange="this.form.submit()"
                                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                                <option value="">Semua Perusahaan</option>
                                @foreach($perusahaans as $pt)
                                    <option value="{{ $pt->id }}" {{ request('perusahaan') == $pt->id ? 'selected' : '' }}>{{ $pt->kode }} - {{ $pt->nama_perusahaan }}</option>
                                @endforeach
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
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Pendidikan</label>
                            <select name="pendidikan" onchange="this.form.submit()"
                                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                                <option value="">Semua Pendidikan</option>
                                @foreach($opsiPendidikan as $opsi)
                                    <option value="{{ $opsi }}" {{ request('pendidikan') == $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Umur (Tahun)</label>
                            <div class="flex gap-2 items-center">
                                <input type="number" name="umur_min" value="{{ request('umur_min') }}" placeholder="Min" min="0" max="100" 
                                       onchange="this.form.submit()"
                                       class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <span class="text-gray-400 font-bold text-xs">s/d</span>
                                <input type="number" name="umur_max" value="{{ request('umur_max') }}" placeholder="Max" min="0" max="100" 
                                       onchange="this.form.submit()"
                                       class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
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
                <table class="w-full table-fixed">
                    <thead class="bg-gray-50 text-gray-600 text-[10px] uppercase tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-3 font-semibold text-left w-[20%]">Karyawan</th>
                            <th class="px-3 py-3 font-semibold text-left w-[15%]">Kontak & JK</th>
                            <th class="px-3 py-3 font-semibold text-left w-[22%]">Posisi & Penempatan</th>
                            <th class="px-3 py-3 font-semibold text-left w-[12%]">Perusahaan</th>
                            <th class="px-3 py-3 font-semibold text-center w-[13%]">Pendidikan / Umur</th>
                            <th class="px-3 py-3 font-semibold text-center w-[9%]">Status</th>
                            <th class="px-3 py-3 font-semibold text-center w-[9%]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100" id="tabelKaryawan">
                        @forelse($karyawans as $kry)
                        @php
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
                            ]));
                        @endphp
                        <tr class="karyawan-row hover:bg-blue-50/30 transition-colors" data-search="{{ $searchText }}">

                            <!-- KOLOM 1: KARYAWAN -->
                            <td class="px-3 py-3 align-top">
                                <div class="font-semibold text-gray-900 text-xs truncate" title="{{ $kry->nama_lengkap }}">
                                    {{ $kry->nama_lengkap }}
                                </div>
                                <div class="text-[10px] text-gray-500 mt-0.5 truncate" title="NIK: {{ $kry->nik_kerja }}">
                                    <i class="bi bi-upc text-gray-400"></i> {{ $kry->nik_kerja }}
                                </div>
                            </td>

                            <!-- KOLOM 2: KONTAK + JK -->
                            <td class="px-3 py-3 align-top">
                                <div class="text-xs text-gray-700 truncate" title="{{ $kry->no_hp }}">
                                    <i class="bi bi-telephone text-emerald-500"></i> {{ $kry->no_hp }}
                                </div>
                                <div class="mt-1">
                                    @if($kry->jenis_kelamin == 'L')
                                        <span class="inline-flex items-center gap-0.5 bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded text-[9px] font-bold">
                                            <i class="bi bi-gender-male"></i> Laki-laki
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-0.5 bg-pink-50 text-pink-700 px-1.5 py-0.5 rounded text-[9px] font-bold">
                                            <i class="bi bi-gender-female"></i> Perempuan
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- KOLOM 3: POSISI & PENEMPATAN -->
                            <td class="px-3 py-3 align-top">
                                <div class="text-xs font-semibold text-gray-800 truncate" title="{{ $kry->jabatan->nama_jabatan ?? '-' }}">
                                    {{ $kry->jabatan->nama_jabatan ?? '-' }}
                                </div>
                                <div class="text-[10px] text-gray-500 mt-0.5 truncate" title="{{ $kry->departemen->nama_departemen ?? '-' }}">
                                    <i class="bi bi-building text-gray-400"></i> {{ $kry->departemen->nama_departemen ?? '-' }}
                                </div>
                                <div class="text-[10px] text-blue-600 font-semibold mt-0.5 truncate" title="{{ $kry->departemen->divisi->nama_divisi ?? '-' }}">
                                    <i class="bi bi-diagram-2 text-blue-400"></i> {{ $kry->departemen->divisi->nama_divisi ?? '-' }}
                                </div>
                            </td>

                            <!-- KOLOM 4: PERUSAHAAN -->
                            <td class="px-3 py-3 align-top">
                                <div class="text-[10px] font-semibold text-gray-700 truncate" title="{{ $kry->perusahaan->nama_perusahaan ?? '-' }}">
                                    {{ $kry->perusahaan->nama_perusahaan ?? '-' }}
                                </div>
                            </td>

                            <!-- KOLOM 5: PENDIDIKAN + UMUR -->
                            <td class="px-3 py-3 align-top text-center">
                                @php
                                    $pend = $kry->pendidikan ?? '-';
                                    $jenjang = explode('-', $pend)[0] ?? '-';
                                    $sekolah = explode('-', $pend)[1] ?? null;
                                @endphp
                                <div class="inline-block bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded text-[10px] font-bold" title="{{ $pend }}">
                                    {{ $jenjang }}
                                </div>
                                @if($sekolah)
                                    <div class="text-[9px] text-gray-400 mt-0.5 truncate" title="{{ $sekolah }}">
                                        {{ $sekolah }}
                                    </div>
                                @endif
                                @if($kry->tanggal_lahir)
                                    <div class="text-[10px] text-gray-600 mt-1 font-bold">
                                        <i class="bi bi-cake2 text-pink-400"></i> {{ \Carbon\Carbon::parse($kry->tanggal_lahir)->age }} thn
                                    </div>
                                @endif
                            </td>

                            <!-- KOLOM 6: STATUS -->
                            <td class="px-3 py-3 align-top text-center">
                                @if($kry->status == 'aktif')
                                    <span class="inline-flex items-center gap-0.5 bg-emerald-100 text-emerald-700 px-2 py-1 rounded text-[10px] font-bold tracking-wide">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> AKTIF
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-0.5 bg-rose-100 text-rose-700 px-2 py-1 rounded text-[10px] font-bold tracking-wide">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> OFF
                                    </span>
                                @endif
                            </td>

                            <!-- KOLOM 7: AKSI -->
                            <td class="px-3 py-3 align-top text-center whitespace-nowrap">
                                <a href="{{ route('karyawan.show', $kry->id) }}" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors" title="Detail Karyawan">
                                    <i class="bi bi-eye-fill text-xs"></i>
                                </a>
                                <a href="{{ route('karyawan.qr', $kry->id) }}" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors ml-1" title="Cetak QR" target="_blank">
                                    <i class="bi bi-qr-code text-xs"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
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

                        <!-- Baris kosong khusus search realtime -->
                        <tr id="emptySearchRow" class="hidden">
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="bi bi-search text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-gray-500 font-semibold mb-1">Tidak ada yang cocok di halaman ini</p>
                                    <p class="text-xs text-gray-400">
                                        Tekan <kbd class="px-1.5 py-0.5 bg-gray-100 border border-gray-300 rounded text-[10px] font-mono">Enter</kbd> untuk cari di semua halaman
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

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