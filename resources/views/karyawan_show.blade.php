<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Karyawan - HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center sticky top-0 z-10">
        <a href="{{ route('karyawan.index') }}" class="text-gray-500 hover:bg-gray-100 p-2 rounded-lg mr-3">
            <i class="bi bi-arrow-left text-xl"></i>
        </a>
        <h1 class="font-bold text-xl text-gray-800">Detail Karyawan</h1>
    </nav>

    <div class="p-6 max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- KOLOM KIRI: Informasi Akun -->
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
                    {{ strtoupper(substr($karyawan->nama_lengkap, 0, 1)) }}
                </div>
                <h2 class="font-bold text-gray-800 text-lg">{{ $karyawan->nama_lengkap }}</h2>
                <p class="text-sm text-gray-500 mb-4">{{ $karyawan->jabatan->nama_jabatan ?? 'Jabatan -' }}</p>
                
                @if($karyawan->status == 'aktif')
                    <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">AKTIF</span>
                @else
                    <span class="bg-rose-100 text-rose-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">NONAKTIF</span>
                @endif
            </div>

            <!-- KOTAK AKUN LOGIN (SESUAI PERMINTAAN) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 border-b pb-2 mb-3 text-sm flex items-center">
                    <i class="bi bi-person-lock mr-2 text-blue-600"></i> Akun Login Sistem
                </h3>
                
                @if($akun)
                    <div class="space-y-3 mt-4">
                        <div>
                            <p class="text-[11px] text-gray-500 font-medium uppercase">Username</p>
                            <p class="font-bold text-blue-700">{{ $akun->username }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-500 font-medium uppercase">Password Default</p>
                            <p class="font-bold text-gray-800">passwor123</p>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-amber-50 rounded text-[11px] text-amber-700 leading-tight">
                        <i class="bi bi-info-circle-fill mr-1"></i> Jika karyawan mengubah sandinya, sandi baru <b>tidak dapat dilihat</b>. HRD hanya bisa mereset sandi jika diperlukan.
                    </div>
                @else
                    <p class="text-sm text-rose-500 italic">Akun login belum dibuat untuk karyawan ini.</p>
                @endif
            </div>
        </div>

        <!-- KOLOM KANAN: Biodata -->
        <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 border-b pb-3 mb-5 text-lg">Biodata Karyawan</h3>
            
            <div class="grid grid-cols-2 gap-y-4 gap-x-6 text-sm">
                <div>
                    <p class="text-gray-500 mb-1">NIK Pekerja / NIK KTP</p>
                    <p class="font-semibold text-gray-800">{{ $karyawan->nik_kerja }} / {{ $karyawan->nik_ktp }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Departemen</p>
                    <p class="font-semibold text-gray-800">{{ $karyawan->departemen->nama_departemen ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Tempat, Tanggal Lahir</p>
                    <p class="font-semibold text-gray-800">{{ $karyawan->tempat_lahir }}, {{ \Carbon\Carbon::parse($karyawan->tanggal_lahir)->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Jenis Kelamin</p>
                    <p class="font-semibold text-gray-800">{{ $karyawan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">No. Handphone</p>
                    <p class="font-semibold text-gray-800">{{ $karyawan->no_hp }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Pendidikan</p>
                    <p class="font-semibold text-gray-800">{{ $karyawan->pendidikan }}</p>
                </div>
                <div class="col-span-2 mt-2">
                    <p class="text-gray-500 mb-1">Alamat Lengkap</p>
                    <p class="font-semibold text-gray-800">{{ $karyawan->alamat }}</p>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-2 border-t pt-4">
                <a href="{{ route('karyawan.edit', $karyawan->id) }}" class="bg-blue-50 text-blue-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-600 hover:text-white transition-colors">
                    <i class="bi bi-pencil-square mr-1"></i> Edit Data
                </a>
                <a href="{{ route('karyawan.qr', $karyawan->id) }}" target="_blank" class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-600 hover:text-white transition-colors">
                    <i class="bi bi-qr-code mr-1"></i> Cetak QR
                </a>
            </div>
        </div>
        
    </div>
</body>
</html>