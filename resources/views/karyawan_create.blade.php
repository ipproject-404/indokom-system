<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Karyawan - HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center sticky top-0 z-10">
        <a href="{{ route('karyawan.index') }}" class="text-gray-500 hover:bg-gray-100 p-2 rounded-lg mr-3">
            <i class="bi bi-arrow-left text-xl"></i>
        </a>
        <h1 class="font-bold text-xl text-gray-800">Tambah Karyawan Baru</h1>
    </nav>

    <div class="p-6 max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            
            <!-- Tampilkan Error Validasi -->
            @if ($errors->any())
                <div class="bg-rose-50 text-rose-700 p-4 rounded-lg mb-6 text-sm">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('karyawan.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK KTP</label>
                        <input type="number" name="nik_ktp" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK Kerja (Perusahaan)</label>
                        <input type="text" name="nik_kerja" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none" required>
                            <option value="">Pilih...</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                    <textarea name="alamat" rows="2" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none" required></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Handphone</label>
                        <input type="number" name="no_hp" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                        <input type="text" name="pendidikan" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 border-t border-gray-100 pt-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                        <select name="departemen_id" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none" required>
                            <option value="">Pilih Departemen...</option>
                            @foreach($departemens as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                        <select name="jabatan_id" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none" required>
                            <option value="">Pilih Jabatan...</option>
                            @foreach($jabatans as $jab)
                                <option value="{{ $jab->id }}">{{ $jab->nama_jabatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none" required>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-blue-700 transition-colors">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>