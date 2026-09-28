<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Lembur - HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

    <!-- NAVBAR -->
    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-20 shadow-sm">
        <div class="flex items-center">
            <a href="{{ route('dashboard.hrd') }}" class="text-gray-500 hover:bg-gray-100 p-2 rounded-lg mr-3 transition-colors">
                <i class="bi bi-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="font-bold text-xl text-gray-800">Manajemen Pengajuan Lembur</h1>
                <p class="text-xs text-gray-500">Persetujuan atau penolakan lembur karyawan</p>
            </div>
        </div>
    </nav>

    <div class="p-6 max-w-7xl mx-auto space-y-6">

        <!-- Notifikasi Sukses -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium flex items-center shadow-sm">
                <i class="bi bi-check-circle-fill text-lg mr-2"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Filter Status Tab -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 uppercase mr-2">Filter Status:</span>
                <a href="{{ route('lembur.pengajuan', ['status' => 'pending']) }}" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-colors {{ ($status == 'pending') ? 'bg-amber-500 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Menunggu
                </a>
                <a href="{{ route('lembur.pengajuan', ['status' => 'disetujui']) }}" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-colors {{ ($status == 'disetujui') ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Disetujui
                </a>
                <a href="{{ route('lembur.pengajuan', ['status' => 'ditolak']) }}" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-colors {{ ($status == 'ditolak') ? 'bg-rose-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Ditolak
                </a>
                <a href="{{ route('lembur.pengajuan', ['status' => 'semua']) }}" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-colors {{ ($status == 'semua') ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Semua
                </a>
            </div>
            <div class="text-xs font-semibold text-gray-500">
                Total Data: <span class="text-gray-800 font-bold">{{ $lemburs->total() }}</span>
            </div>
        </div>

        <!-- TABEL PENGAJUAN LEMBUR -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-max">
                    <thead class="bg-slate-700 text-white text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 font-semibold">No</th>
                            <th class="px-6 py-3.5 font-semibold">Informasi Karyawan</th>
                            <th class="px-6 py-3.5 font-semibold">Tanggal & Waktu Lembur</th>
                            <th class="px-6 py-3.5 font-semibold">Durasi & Catatan Pekerjaan</th>
                            <th class="px-6 py-3.5 font-semibold text-center">Status</th>
                            <th class="px-6 py-3.5 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-200">
                        @forelse($lemburs as $index => $lmb)
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-500">
                                {{ $lemburs->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $lmb->karyawan->nama_lengkap ?? '-' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">NIK: {{ $lmb->karyawan->nik_kerja ?? '-' }}</div>
                                <div class="text-xs text-blue-600 font-medium mt-1">{{ $lmb->karyawan->departemen->nama_departemen ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800 mb-1">
                                    <i class="bi bi-calendar-event text-blue-500 mr-1"></i> {{ \Carbon\Carbon::parse($lmb->tanggal)->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-xs font-semibold text-gray-600 bg-gray-100 px-2 py-1 rounded inline-block">
                                    <i class="bi bi-clock mr-1"></i> {{ \Carbon\Carbon::parse($lmb->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($lmb->jam_selesai)->format('H:i') }} WIB
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-xs">
                                <div class="font-bold text-blue-600 text-xs mb-1">{{ $lmb->durasi_jam }} Jam Kerja</div>
                                <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed" title="{{ $lmb->catatan }}">{{ $lmb->catatan }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($lmb->status == 'pending' || $lmb->status == 'menunggu')
                                    <span class="bg-amber-100 text-amber-700 text-xs px-3 py-1 rounded-full font-bold inline-flex items-center">
                                        <i class="bi bi-hourglass-split mr-1"></i> Menunggu
                                    </span>
                                @elseif($lmb->status == 'disetujui' || $lmb->status == 'approved')
                                    <span class="bg-emerald-100 text-emerald-700 text-xs px-3 py-1 rounded-full font-bold inline-flex items-center">
                                        <i class="bi bi-check-circle-fill mr-1"></i> Disetujui
                                    </span>
                                @else
                                    <span class="bg-rose-100 text-rose-700 text-xs px-3 py-1 rounded-full font-bold inline-flex items-center">
                                        <i class="bi bi-x-circle-fill mr-1"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($lmb->status == 'pending' || $lmb->status == 'menunggu')
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Tombol Setujui -->
                                        <form action="{{ route('lembur.approve', $lmb->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menyetujui lembur ini?')">
                                            @csrf
                                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm flex items-center">
                                                <i class="bi bi-check-lg mr-1"></i> ACC
                                            </button>
                                        </form>
                                        <!-- Tombol Tolak -->
                                        <form action="{{ route('lembur.reject', $lmb->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak lembur ini?')">
                                            @csrf
                                            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm flex items-center">
                                                <i class="bi bi-x-lg mr-1"></i> Tolak
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Telah Diproses</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                                <i class="bi bi-folder-x text-4xl mb-2 block text-gray-300"></i>
                                Tidak ada data pengajuan lembur dengan status ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            @if($lemburs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Menampilkan {{ $lemburs->firstItem() }} - {{ $lemburs->lastItem() }} dari {{ $lemburs->total() }} data
                </div>
                <div>
                    {{ $lemburs->withQueryString()->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

</body>
</html>