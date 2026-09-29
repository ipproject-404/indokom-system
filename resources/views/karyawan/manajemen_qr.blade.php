<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen QR Code - HRD</title>
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
            <a href="{{ route('karyawan.index') }}" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
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
            <!-- Menu Manajemen QR Code Aktif -->
            <a href="{{ route('karyawan.qr.index') }}" class="flex items-center bg-blue-600 text-white rounded-lg px-4 py-2.5 mb-1 transition-colors">
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
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-10 shadow-sm">
            <div class="flex items-center">
                <a href="{{ route('dashboard.hrd') }}" class="text-gray-500 hover:bg-gray-100 p-2 rounded-lg mr-3 transition-colors">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="font-bold text-xl text-gray-800">Manajemen QR Code</h1>
                    <p class="text-xs text-gray-500 mt-0.5">Kelola dan Cetak QR Code Karyawan</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <!-- Tombol Cetak Terpilih -->
                <button onclick="cetakTerpilih()" class="bg-indigo-50 text-indigo-600 border border-indigo-200 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-100 transition-colors shadow-sm flex items-center">
                    <i class="bi bi-check2-square mr-1.5"></i> Cetak Terpilih
                </button>
                <!-- Tombol Cetak Semua QR -->
                <button onclick="cetakSemua()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors shadow-sm flex items-center">
                    <i class="bi bi-printer-fill mr-1.5"></i> Cetak Semua QR
                </button>
            </div>
        </nav>

        <!-- CONTENT BODY -->
        <div class="p-6 grow overflow-y-auto">
            <div class="max-w-6xl mx-auto">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-gray-800">Daftar Karyawan</h3>
                        
                        <!-- Kotak Pencarian / Filter Realtime -->
                        <div class="relative">
                            <input type="text" id="searchInput" onkeyup="filterTabel()" placeholder="Cari nama atau NIK..." class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-600 outline-none w-64 bg-white">
                            <i class="bi bi-search absolute left-3 top-2.5 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table id="tabelKaryawan" class="w-full text-left border-collapse min-w-max">
                            <thead class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 font-semibold text-center w-10">
                                        <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                                    </th>
                                    <th class="px-6 py-4 font-semibold">No</th>
                                    <th class="px-6 py-4 font-semibold">Informasi Karyawan</th>
                                    <th class="px-6 py-4 font-semibold">Departemen / Jabatan</th>
                                    <th class="px-6 py-4 font-semibold text-center">Status QR</th>
                                    <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                                @forelse($karyawans as $index => $karyawan)
                                <tr class="hover:bg-blue-50/30 transition-colors karyawan-row">
                                    <td class="px-6 py-4 text-center">
                                        <!-- Checkbox per baris dengan menyimpan ID Karyawan -->
                                        <input type="checkbox" name="karyawan_checkbox" value="{{ $karyawan->id }}" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer row-checkbox">
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-500">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 searchable-text">
                                        <div class="font-bold text-gray-900">{{ $karyawan->nama_lengkap }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">NIK: {{ $karyawan->nik_kerja }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded font-medium border border-gray-200">
                                            {{ $karyawan->departemen->nama_departemen ?? '-' }}
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1.5 ml-1">
                                            {{ $karyawan->jabatan->nama_jabatan ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="bg-emerald-100 text-emerald-700 text-[11px] px-2 py-1 rounded-full font-bold">
                                            <i class="bi bi-check-circle-fill mr-1"></i> Tersedia
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('karyawan.qr', $karyawan->id) }}" target="_blank" class="inline-flex items-center justify-center bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-700 transition-colors shadow-sm">
                                            <i class="bi bi-qr-code mr-1.5"></i> Lihat / Cetak
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <i class="bi bi-inbox text-3xl mb-2 block text-gray-300"></i>
                                        Belum ada data karyawan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-between items-center text-sm text-gray-500">
                        <div>Menampilkan total <span id="totalData">{{ count($karyawans) }}</span> karyawan</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- JavaScript untuk Filter & Fitur Cetak Massal -->
<script>
    // 1. Fitur Filter / Pencarian Real-time
    function filterTabel() {
        let input = document.getElementById('searchInput');
        let filter = input.value.toLowerCase();
        let rows = document.querySelectorAll('.karyawan-row');

        rows.forEach(row => {
            let text = row.querySelector('.searchable-text').textContent.toLowerCase();
            if (text.includes(filter)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    // 2. Fitur Pilih Semua (Select All Checkbox)
    function toggleSelectAll(source) {
        let checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => {
            if(cb.closest('tr').style.display !== 'none') {
                cb.checked = source.checked;
            }
        });
    }

    // 3. Fitur Cetak Terpilih (Mengirim ID sebagai parameter ke halaman massal)
    function cetakTerpilih() {
        let selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
        
        if (selectedCheckboxes.length === 0) {
            alert('Silakan pilih minimal satu karyawan terlebih dahulu!');
            return;
        }

        let ids = Array.from(selectedCheckboxes).map(cb => cb.value);
        
        // Membuka halaman cetak massal dalam 1 tab baru dengan membawa parameter id
        window.open(`/hrd/karyawan/cetak-qr-massal?ids=${ids.join(',')}`, '_blank');
    }

    // 4. Fitur Cetak Semua QR (Mengambil semua ID karyawan di tabel)
    function cetakSemua() {
        let allCheckboxes = document.querySelectorAll('.row-checkbox');
        
        if (allCheckboxes.length === 0) {
            alert('Tidak ada data karyawan untuk dicetak!');
            return;
        }

        let ids = Array.from(allCheckboxes).map(cb => cb.value);
        
        window.open(`/hrd/karyawan/cetak-qr-massal?ids=${ids.join(',')}`, '_blank');
    }
</script>
</body>
</html>