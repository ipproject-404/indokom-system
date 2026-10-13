<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Karyawan - HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

<div class="flex min-h-screen bg-gray-50">

    @include('partials.sidebar-hrd')

    <!-- MAIN CONTENT -->
    <main class="grow flex flex-col min-w-0">
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-20 shadow-sm">
            <div class="flex items-center">
                <a href="{{ route('dashboard.hrd') }}" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg mr-3 transition-colors">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="font-bold text-xl text-gray-800">Shift Karyawan</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Lihat siapa pakai shift apa. Shift diatur oleh kepala bagian masing-masing.</p>
                </div>
            </div>
        </nav>

        <div class="p-6 grow overflow-y-auto">
            <div class="max-w-6xl mx-auto space-y-5">

                @php
                    // Statistik ringkas
                    $totalKaryawan = $karyawans->count();
                    $punyaShift = $karyawans->filter(fn($k) => $k->shift_sekarang)->count();
                    $tanpaShift = $totalKaryawan - $punyaShift;
                    
                    // Hitung jumlah per shift
                    $shiftCounter = [];
                    foreach ($karyawans as $k) {
                        if ($k->shift_sekarang) {
                            $nama = $k->shift_sekarang->nama_shift;
                            $shiftCounter[$nama] = ($shiftCounter[$nama] ?? 0) + 1;
                        }
                    }
                    arsort($shiftCounter);
                @endphp

                <!-- STATISTIK CARDS -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Total</p>
                            <p class="text-2xl font-bold text-gray-800 mt-0.5">{{ $totalKaryawan }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Karyawan</p>
                        </div>
                        <div class="w-11 h-11 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="bi bi-people-fill text-lg"></i>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Sudah Ada</p>
                            <p class="text-2xl font-bold text-emerald-600 mt-0.5">{{ $punyaShift }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Punya shift</p>
                        </div>
                        <div class="w-11 h-11 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="bi bi-check-circle-fill text-lg"></i>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Kosong</p>
                            <p class="text-2xl font-bold text-rose-600 mt-0.5">{{ $tanpaShift }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Belum ada shift</p>
                        </div>
                        <div class="w-11 h-11 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                            <i class="bi bi-exclamation-circle-fill text-lg"></i>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Jenis Shift</p>
                            <p class="text-2xl font-bold text-indigo-600 mt-0.5">{{ count($shiftCounter) }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Tipe aktif</p>
                        </div>
                        <div class="w-11 h-11 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <i class="bi bi-clock-history text-lg"></i>
                        </div>
                    </div>
                </div>

                <!-- INFO PEMBAGIAN SHIFT -->
                @if(!empty($shiftCounter))
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="bi bi-pie-chart-fill text-blue-500"></i> Distribusi Shift
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($shiftCounter as $nama => $jumlah)
                            <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-100 text-blue-800 px-3 py-1.5 rounded-full text-xs font-semibold">
                                <i class="bi bi-clock-fill"></i>
                                <span>{{ $nama }}</span>
                                <span class="bg-white text-blue-700 px-2 py-0.5 rounded-full font-bold">{{ $jumlah }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- FILTER & SEARCH PANEL -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <form method="GET" action="{{ route('shift.hrd.index') }}" id="filterForm" class="flex flex-col md:flex-row gap-3">
                        <!-- Search -->
                        <div class="flex-1 relative">
                            <i class="bi bi-search absolute left-3.5 top-3 text-gray-400"></i>
                            <input type="text" 
                                   name="search" 
                                   id="searchRealtime"
                                   value="{{ request('search') }}"
                                   autocomplete="off"
                                   placeholder="Cari nama karyawan, NIK, atau kepala bagian..."
                                   class="w-full pl-10 pr-12 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <span id="searchBadge" class="hidden absolute right-3 top-2.5 text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-1 rounded-md">
                                <i class="bi bi-lightning-charge-fill"></i> Live
                            </span>
                        </div>

                        <!-- Filter Departemen (auto-submit) -->
                        <div class="w-full md:w-56">
                            <select name="departemen" onchange="this.form.submit()"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:ring-2 focus:ring-blue-600 outline-none cursor-pointer">
                                <option value="">Semua Departemen</option>
                                @foreach ($departemens as $dept)
                                    <option value="{{ $dept->id }}" {{ request('departemen') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->nama_departemen }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Shift (auto-submit) -->
                        <div class="w-full md:w-48">
                            <select name="shift" onchange="this.form.submit()"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:ring-2 focus:ring-blue-600 outline-none cursor-pointer">
                                <option value="">Semua Shift</option>
                                @foreach($shiftCounter as $nama => $jml)
                                    <option value="{{ $nama }}" {{ request('shift') == $nama ? 'selected' : '' }}>
                                        {{ $nama }} ({{ $jml }})
                                    </option>
                                @endforeach
                                <option value="__kosong__" {{ request('shift') == '__kosong__' ? 'selected' : '' }}>
                                    Belum ada shift
                                </option>
                            </select>
                        </div>

                        <!-- Reset -->
                        @if(request()->hasAny(['search', 'departemen', 'shift']))
                            <a href="{{ route('shift.hrd.index') }}" 
                               class="bg-rose-50 text-rose-600 border border-rose-200 px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-rose-100 transition-colors flex items-center justify-center gap-2 whitespace-nowrap">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>
                        @endif
                    </form>

                    <!-- Info hasil realtime -->
                    <p id="searchInfo" class="text-xs text-gray-500 mt-3 hidden">
                        <i class="bi bi-info-circle text-blue-500"></i>
                        Menampilkan <b id="matchCount" class="text-gray-700">0</b> dari <b id="totalCount" class="text-gray-700">0</b> karyawan di halaman ini
                        <span class="text-gray-400 mx-1">|</span>
                        <span class="text-blue-600 font-semibold">Tekan <kbd class="px-1.5 py-0.5 bg-gray-100 border border-gray-300 rounded text-[10px] font-mono">Enter</kbd> untuk cari semua</span>
                    </p>
                </div>

                <!-- TABEL -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                <i class="bi bi-list-ul"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-gray-800">Daftar Karyawan & Shift</h3>
                                <p class="text-[11px] text-gray-500">
                                    <span id="jumlah-data">{{ $karyawans->count() }}</span> karyawan terdaftar
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-[10px] uppercase tracking-wider border-b border-gray-200">
                                    <th class="text-left px-5 py-3 font-bold">Karyawan</th>
                                    <th class="text-left px-5 py-3 font-bold">Departemen</th>
                                    <th class="text-left px-5 py-3 font-bold">Kepala Bagian</th>
                                    <th class="text-left px-5 py-3 font-bold">Shift Sekarang</th>
                                    <th class="text-center px-5 py-3 font-bold w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tabelKaryawan" class="divide-y divide-gray-100">
                                @forelse ($karyawans as $k)
                                @php
                                    $searchText = strtolower(implode(' ', [
                                        $k->nama_lengkap ?? '',
                                        $k->nik_kerja ?? '',
                                        $k->departemen->nama_departemen ?? '',
                                        $k->departemen->kepalaKaryawan->nama_lengkap ?? '',
                                        $k->shift_sekarang->nama_shift ?? '',
                                    ]));
                                @endphp
                                <tr class="karyawan-row hover:bg-blue-50/30 transition-colors" data-search="{{ $searchText }}">
                                    <!-- KARYAWAN -->
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-500 text-white text-xs font-bold flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($k->nama_lengkap ?? 'X', 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-semibold text-gray-900 truncate">{{ $k->nama_lengkap }}</div>
                                                <div class="text-[10px] text-gray-500">{{ $k->nik_kerja ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- DEPARTEMEN -->
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 px-2.5 py-1 rounded-md text-xs font-medium">
                                            <i class="bi bi-building text-gray-400"></i>
                                            {{ $k->departemen->nama_departemen ?? '-' }}
                                        </span>
                                    </td>

                                    <!-- KEPALA BAGIAN -->
                                    <td class="px-5 py-3">
                                        @if($k->departemen->kepalaKaryawan)
                                            <span class="inline-flex items-center gap-1 text-xs text-gray-700">
                                                <i class="bi bi-person-badge text-amber-500"></i>
                                                {{ $k->departemen->kepalaKaryawan->nama_lengkap }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum ditunjuk</span>
                                        @endif
                                    </td>

                                    <!-- SHIFT -->
                                    <td class="px-5 py-3">
                                        @if ($k->shift_sekarang)
                                            <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-full text-xs font-bold">
                                                <i class="bi bi-clock-fill"></i>
                                                {{ $k->shift_sekarang->nama_shift }}
                                                <span class="text-blue-500 font-normal">
                                                    ({{ substr($k->shift_sekarang->jam_masuk, 0, 5) }}-{{ substr($k->shift_sekarang->jam_pulang_default, 0, 5) }})
                                                </span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-600 border border-rose-200 px-2.5 py-1 rounded-full text-[10px] font-bold">
                                                <i class="bi bi-exclamation-circle"></i> Belum ada shift
                                            </span>
                                        @endif
                                    </td>

                                    <!-- AKSI -->
                                    <td class="px-5 py-3 text-center">
                                        <a href="{{ route('karyawan.show', $k->id) }}" 
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors"
                                           title="Detail Karyawan">
                                            <i class="bi bi-eye-fill text-sm"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-16 text-center">
                                        <div class="flex flex-col items-center text-gray-400">
                                            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                                <i class="bi bi-inbox text-3xl"></i>
                                            </div>
                                            <p class="font-semibold text-gray-600">Tidak ada data karyawan</p>
                                            <p class="text-xs mt-1">Coba ubah filter departemen</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse

                                <!-- Empty search result -->
                                <tr id="emptySearchRow" class="hidden">
                                    <td colspan="5" class="px-5 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="bi bi-search text-4xl mb-3 text-gray-300"></i>
                                            <p class="text-gray-500 font-semibold mb-1">Tidak ada yang cocok di halaman ini</p>
                                            <p class="text-xs text-gray-400">
                                                Tekan <kbd class="px-1.5 py-0.5 bg-gray-100 border border-gray-300 rounded text-[10px] font-mono">Enter</kbd> untuk cari di semua
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- INFO FOOTER -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                    <i class="bi bi-info-circle-fill text-blue-600 text-lg mt-0.5"></i>
                    <div class="text-xs text-blue-800">
                        <p class="font-bold mb-1">Catatan</p>
                        <p>Shift diatur oleh <b>kepala bagian</b> masing-masing departemen, bukan dari halaman HRD ini. Halaman ini hanya untuk <b>melakukan pemantauan</b> siapa yang memakai shift apa.</p>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>

<script>
    // ==========================================
    // SEARCH REALTIME
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
                if (emptyRow) emptyRow.classList.toggle('hidden', shown > 0);
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

        // Enter → submit server-side (cari semua)
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