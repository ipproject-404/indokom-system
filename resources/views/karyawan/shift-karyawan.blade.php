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

    <!-- SIDEBAR (sama seperti halaman HRD lain -- lihat catatan di bawah untuk menambahkan link ke sini di sidebar yang sudah ada) -->
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
            <a href="{{ route('jabatan.departemen.index') }}" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-diagram-3-fill mr-3"></i>
                <span class="text-sm font-medium">Jabatan & Departemen</span>
            </a>

            <div class="uppercase text-gray-400 text-xs font-bold mb-3 mt-6">Kehadiran & QR</div>
            <a href="{{ route('karyawan.qr.index') }}" class="flex items-center text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-qr-code-scan mr-3"></i>
                <span class="text-sm font-medium">Manajemen QR Code</span>
            </a>
            <!-- Menu Shift Karyawan Aktif -->
            <a href="{{ route('shift.hrd.index') }}" class="flex items-center bg-blue-600 text-white rounded-lg px-4 py-2.5 mb-1 transition-colors">
                <i class="bi bi-calendar-range mr-3"></i>
                <span class="text-sm font-medium">Shift Karyawan</span>
            </a>

            <div class="uppercase text-gray-400 text-xs font-bold mb-3 mt-6">Akun</div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="w-full flex items-center text-red-600 hover:bg-red-50 rounded-lg px-4 py-2.5 transition-colors">
                    <i class="bi bi-box-arrow-left mr-3"></i>
                    <span class="text-sm font-medium">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="grow flex flex-col min-w-0">
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-10 shadow-sm">
            <div class="flex items-center">
                <a href="{{ route('dashboard.hrd') }}" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg mr-3 transition-colors">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="font-bold text-xl text-gray-800">Shift Karyawan</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Lihat siapa pakai shift apa. Shift diatur oleh kepala bagian masing-masing, bukan dari sini.</p>
                </div>
            </div>
        </nav>

        <div class="p-6 grow overflow-y-auto">
            <div class="max-w-5xl mx-auto space-y-4">

                <!-- Filter Departemen -->
                <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
                    <label class="text-sm font-medium text-gray-700 shrink-0">Filter Departemen</label>
                    <select name="departemen" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 outline-none">
                        <option value="">Semua Departemen</option>
                        @foreach ($departemens as $dept)
                            <option value="{{ $dept->id }}" {{ request('departemen') == $dept->id ? 'selected' : '' }}>{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </select>
                    @if (request('departemen'))
                        <a href="{{ route('shift.hrd.index') }}" class="text-sm text-gray-400 hover:text-gray-600">Reset</a>
                    @endif
                </form>

                <!-- Tabel -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                                <th class="text-left px-5 py-3 font-bold">Nama</th>
                                <th class="text-left px-5 py-3 font-bold">Departemen</th>
                                <th class="text-left px-5 py-3 font-bold">Kepala Bagian</th>
                                <th class="text-left px-5 py-3 font-bold">Shift Sekarang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($karyawans as $k)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 font-semibold text-gray-800">{{ $k->nama_lengkap }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ $k->departemen->nama_departemen ?? '-' }}</td>
                                    <td class="px-5 py-3 text-gray-500">{{ $k->departemen->kepalaKaryawan->nama_lengkap ?? '-' }}</td>
                                    <td class="px-5 py-3">
                                        @if ($k->shift_sekarang)
                                            <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full text-xs font-semibold">
                                                <i class="bi bi-clock mr-1"></i>{{ $k->shift_sekarang->nama_shift }}
                                                ({{ substr($k->shift_sekarang->jam_masuk, 0, 5) }}-{{ substr($k->shift_sekarang->jam_pulang_default, 0, 5) }})
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs italic">Belum ada shift</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-8 text-gray-400">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </main>
</div>

</body>
</html>