<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Karyawan - HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Fungsi pembatas input agar hanya bisa mengetik angka
        function hanyaAngka(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
    </script>
</head>
<body class="bg-gray-50">

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center sticky top-0 z-10">
        <a href="{{ route('karyawan.index') }}" class="text-gray-500 hover:bg-gray-100 p-2 rounded-lg mr-3 transition-colors">
            <i class="bi bi-arrow-left text-xl"></i>
        </a>
        <h1 class="font-bold text-xl text-gray-800">Tambah Karyawan Baru</h1>
    </nav>

    <div class="p-6 max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            
            <!-- Tampilkan Error Validasi -->
            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-lg mb-6 text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('karyawan.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK KTP <span class="text-xs text-gray-400">(Wajib 16 Digit Angka)</span></label>
                        <input type="text" name="nik_ktp" value="{{ old('nik_ktp') }}" maxlength="16" minlength="16" onkeypress="return hanyaAngka(event)" placeholder="Contoh: 187103..." class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK Kerja (Perusahaan)</label>
                        <input type="text" name="nik_kerja" value="{{ old('nik_kerja') }}" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none bg-white" required>
                            <option value="">Pilih Jenis Kelamin...</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                    <textarea name="alamat" rows="2" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none" required>{{ old('alamat') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Handphone <span class="text-xs text-gray-400">(Hanya Angka)</span></label>
                        <input type="text" name="no_hp" value="{{ old('no_hp') }}" onkeypress="return hanyaAngka(event)" placeholder="Contoh: 08123456789" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir & Institusi</label>
                        <div class="grid grid-cols-2 gap-2">
                            <select name="tingkat_pendidikan" class="border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none bg-white text-sm" required>
                                <option value="">Pilih Jenjang</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA/K">SMA/SMK</option>
                                <option value="D3">D3</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3">S3</option>
                            </select>
                            <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah') }}" placeholder="Misal: Unila / SMAN 1" class="border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-gray-100 pt-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                        <!-- Dropdown Departemen dengan ID untuk trigger AJAX -->
                        <select name="departemen_id" id="departemen_id" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none bg-white" required>
                            <option value="">Pilih Departemen...</option>
                            @foreach($departemens as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                        <!-- Dropdown Jabatan kosong, diisi otomatis lewat Javascript -->
                        <select name="jabatan_id" id="jabatan_id" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none bg-white" required>
                            <option value="">Pilih Jabatan...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none" required>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-blue-700 transition-colors shadow-sm">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script AJAX untuk Dependent Dropdown Laravel -->
    <script>
        document.getElementById('departemen_id').addEventListener('change', function() {
            let departemenId = this.value;
            let jabatanSelect = document.getElementById('jabatan_id');
            
            // Kosongkan opsi sebelumnya setiap kali departemen diubah
            jabatanSelect.innerHTML = '<option value="">Pilih Jabatan...</option>';

            if (departemenId) {
                // Panggil route Laravel
                fetch('/get-jabatan/' + departemenId)
                    .then(response => response.json())
                    .then(data => {
                        // Tambahkan data JSON ke dalam elemen select
                        data.forEach(jabatan => {
                            let option = document.createElement('option');
                            option.value = jabatan.id;
                            option.textContent = jabatan.nama_jabatan;
                            jabatanSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }
        });
    </script>
</body>
</html>