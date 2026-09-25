<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Karyawan - HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Script untuk Toggle Password -->
    <script>
        function togglePassword() {
            const pwdText = document.getElementById("pwd-text");
            const pwdIcon = document.getElementById("pwd-icon");
            
            if (pwdText.textContent === "••••••••••") {
                pwdText.textContent = "passwor123";
                pwdIcon.classList.remove("bi-eye-fill");
                pwdIcon.classList.add("bi-eye-slash-fill");
            } else {
                pwdText.textContent = "••••••••••";
                pwdIcon.classList.remove("bi-eye-slash-fill");
                pwdIcon.classList.add("bi-eye-fill");
            }
        }
    </script>
</head>
<body class="bg-gray-50 pb-10">

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center sticky top-0 z-10 shadow-sm">
        <a href="{{ route('karyawan.index') }}" class="text-gray-500 hover:bg-gray-100 p-2 rounded-lg mr-3 transition-colors">
            <i class="bi bi-arrow-left text-xl"></i>
        </a>
        <h1 class="font-bold text-xl text-gray-800">Detail Karyawan</h1>
    </nav>

    <div class="p-6 max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
        
        <!-- =======================
             KOLOM KIRI: INFO AKUN
        ======================== -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Card Profil -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                <div class="w-24 h-24 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl font-bold shadow-inner">
                    {{ strtoupper(substr($karyawan->nama_lengkap, 0, 1)) }}
                </div>
                <h2 class="font-bold text-gray-800 text-xl">{{ $karyawan->nama_lengkap }}</h2>
                <p class="text-sm text-gray-500 mb-5 mt-1">{{ $karyawan->jabatan->nama_jabatan ?? 'Jabatan -' }}</p>
                
                @if($karyawan->status == 'aktif')
                    <span class="bg-emerald-100 text-emerald-700 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider">AKTIF</span>
                @else
                    <span class="bg-rose-100 text-rose-700 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider">NONAKTIF</span>
                @endif
            </div>

            <!-- Card Akun Login -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4 text-sm flex items-center uppercase tracking-wide">
                    <i class="bi bi-person-lock mr-2 text-blue-600 text-lg"></i> Akun Login Sistem
                </h3>
                
                @if($akun)
                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">Username</p>
                            <p class="font-bold text-blue-700 text-base bg-blue-50 py-1.5 px-3 rounded-md">{{ $akun->username }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">Password Default</p>
                            <div class="flex items-center justify-between bg-gray-50 py-1.5 px-3 rounded-md border border-gray-200">
                                <p id="pwd-text" class="font-mono font-bold text-gray-800 text-base tracking-widest">••••••••••</p>
                                <button onclick="togglePassword()" class="text-gray-400 hover:text-blue-600 focus:outline-none transition-colors">
                                    <i id="pwd-icon" class="bi bi-eye-fill text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 p-3 bg-amber-50 rounded-lg text-xs text-amber-800 leading-relaxed border border-amber-100">
                        <i class="bi bi-info-circle-fill mr-1 text-amber-500"></i> Jika karyawan mengubah sandinya, sandi baru <b>tidak dapat dilihat</b>. HRD hanya bisa mereset sandi jika diperlukan.
                    </div>
                @else
                    <div class="text-center py-4 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <i class="bi bi-person-x text-2xl text-gray-400 block mb-1"></i>
                        <p class="text-xs text-gray-500 font-medium">Akun login belum dibuat</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- =======================
             KOLOM KANAN: BIODATA & PRESENSI
        ======================== -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Card Biodata -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-7">
                <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3 mb-6 text-lg">Biodata Karyawan</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8 text-sm">
                    <div>
                        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-1">NIK Pekerja / NIK KTP</p>
                        <p class="font-bold text-gray-800 text-base">{{ $karyawan->nik_kerja }} <span class="text-gray-400 mx-1">/</span> <span class="font-medium">{{ $karyawan->nik_ktp }}</span></p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-1">Departemen</p>
                        <p class="font-bold text-gray-800 text-base">{{ $karyawan->departemen->nama_departemen ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-1">Tempat, Tanggal Lahir</p>
                        <p class="font-medium text-gray-800 text-base">{{ $karyawan->tempat_lahir }}, {{ \Carbon\Carbon::parse($karyawan->tanggal_lahir)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-1">Jenis Kelamin</p>
                        <p class="font-medium text-gray-800 text-base">{{ $karyawan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-1">No. Handphone</p>
                        <p class="font-medium text-gray-800 text-base">{{ $karyawan->no_hp }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-1">Pendidikan</p>
                        <p class="font-medium text-gray-800 text-base">{{ $karyawan->pendidikan }}</p>
                    </div>
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-1">Alamat Lengkap</p>
                        <p class="font-medium text-gray-800 text-sm leading-relaxed">{{ $karyawan->alamat }}</p>
                    </div>
                </div>

                <!-- Tombol Aksi Biodata -->
                <div class="mt-8 flex justify-end space-x-3 pt-2">
                    <a href="{{ route('karyawan.edit', $karyawan->id) }}" class="bg-blue-50 text-blue-600 px-5 py-2.5 rounded-lg text-sm font-bold hover:bg-blue-600 hover:text-white transition-colors flex items-center">
                        <i class="bi bi-pencil-square mr-2"></i> Edit Data
                    </a>
                    <a href="{{ route('karyawan.qr', $karyawan->id) }}" target="_blank" class="bg-indigo-50 text-indigo-600 px-5 py-2.5 rounded-lg text-sm font-bold hover:bg-indigo-600 hover:text-white transition-colors flex items-center">
                        <i class="bi bi-qr-code mr-2"></i> Cetak QR
                    </a>
                </div>
            </div>

            <!-- Card Riwayat Presensi -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-7">
                <div class="flex justify-between items-end border-b border-gray-100 pb-3 mb-5">
                    <h3 class="font-bold text-gray-800 text-lg flex items-center">
                        <i class="bi bi-calendar2-check mr-2 text-emerald-500"></i> Riwayat Kehadiran (10 Terakhir)
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-500 text-[10px] uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 font-bold rounded-tl-lg">Tanggal</th>
                                <th class="px-4 py-3 font-bold text-center">Jam Masuk</th>
                                <th class="px-4 py-3 font-bold text-center">Jam Pulang</th>
                                <th class="px-4 py-3 font-bold">Metode</th>
                                <th class="px-4 py-3 font-bold text-center rounded-tr-lg">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                            @if(isset($riwayat_presensi) && count($riwayat_presensi) > 0)
                                @foreach($riwayat_presensi as $prs)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ \Carbon\Carbon::parse($prs->tanggal)->translatedFormat('d M Y') }}</td>
                                    <td class="px-4 py-3 text-center text-emerald-600 font-bold">{{ \Carbon\Carbon::parse($prs->jam_masuk)->format('H:i') }}</td>
                                    <td class="px-4 py-3 text-center text-rose-500 font-bold">{{ $prs->jam_pulang ? \Carbon\Carbon::parse($prs->jam_pulang)->format('H:i') : '--:--' }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500">
                                        <i class="bi bi-qr-code-scan mr-1"></i> {{ $prs->metode_presensi ?? 'QR_KARYAWAN' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($prs->status_verifikasi == 'disetujui')
                                            <i class="bi bi-check-circle-fill text-emerald-500 text-lg" title="Disetujui"></i>
                                        @elseif($prs->status_verifikasi == 'ditolak')
                                            <i class="bi bi-x-circle-fill text-rose-500 text-lg" title="Ditolak"></i>
                                        @else
                                            <i class="bi bi-clock-fill text-amber-400 text-lg" title="Menunggu"></i>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">
                                        <i class="bi bi-calendar-x text-2xl mb-1 block opacity-50"></i>
                                        Belum ada data kehadiran tercatat.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</body>
</html>