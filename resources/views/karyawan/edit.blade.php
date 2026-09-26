<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Karyawan - HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center sticky top-0 z-10">
        <a href="{{ route('karyawan.index') }}" class="text-gray-500 hover:bg-gray-100 p-2 rounded-lg mr-3">
            <i class="bi bi-arrow-left text-xl"></i>
        </a>
        <h1 class="font-bold text-xl text-gray-800">Edit Data: {{ $karyawan->nama_lengkap }}</h1>
    </nav>

    <div class="p-6 max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            
            <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT') <!-- Wajib ditambahkan untuk proses UPDATE -->
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="{{ $karyawan->nama_lengkap }}" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK KTP</label>
                        <input type="number" name="nik_ktp" value="{{ $karyawan->nik_ktp }}" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK Kerja</label>
                        <input type="text" name="nik_kerja" value="{{ $karyawan->nik_kerja }}" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none" required>
                            <option value="L" {{ $karyawan->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ $karyawan->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Bagian form lainnya dipersingkat agar tidak terlalu panjang, lengkapi sesuai kebutuhan (alamat, no_hp, dll seperti di form create) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                    <textarea name="alamat" rows="2" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none" required>{{ $karyawan->alamat }}</textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Karyawan</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none" required>
                            <option value="aktif" {{ $karyawan->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ $karyawan->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif (Resign/PHK)</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex justify-end border-t mt-4">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-blue-700">
                        Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>