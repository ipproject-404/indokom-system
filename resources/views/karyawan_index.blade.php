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

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-10">
        <div class="flex items-center">
            <a href="{{ route('dashboard.hrd') }}" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg mr-3">
                <i class="bi bi-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="font-bold text-xl text-gray-800">Kelola Data Karyawan</h1>
            </div>
        </div>
        <a href="{{ route('karyawan.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors">
            <i class="bi bi-plus-lg mr-1"></i> Tambah Karyawan
        </a>
    </nav>

    <div class="p-6 max-w-6xl mx-auto">
        
        <!-- Alert Success Saja -->
        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 px-4 py-4 rounded-lg mb-6 shadow-sm">
            <div class="flex items-center text-emerald-700">
                <i class="bi bi-check-circle-fill mr-2 text-lg"></i> 
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        </div>
        @endif

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
                                <div class="text-xs text-gray-400">{{ $kry->jenis_kelamin }}</div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="text-gray-800 font-medium">{{ $kry->jabatan->nama_jabatan ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $kry->departemen->nama_departemen ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($kry->status == 'aktif')
                                    <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-md text-[11px] font-bold">AKTIF</span>
                                @else
                                    <span class="bg-rose-100 text-rose-700 px-2 py-1 rounded-md text-[11px] font-bold">NONAKTIF</span>
                                @endif
                            </td>
                            <!-- AKSI: Hanya Detail dan Cetak QR -->
                            <td class="px-5 py-3 text-center whitespace-nowrap">
                                <a href="{{ route('karyawan.show', $kry->id) }}" class="text-emerald-600 hover:text-emerald-800 mx-2 inline-block" title="Detail Karyawan">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <!-- Tombol Edit Dihapus dari sini -->
                                <a href="{{ route('karyawan.qr', $kry->id) }}" class="text-indigo-600 hover:text-indigo-800 mx-2 inline-block" title="Cetak QR" target="_blank">
                                    <i class="bi bi-qr-code"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-5 py-4 text-center text-gray-500">Belum ada data karyawan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>