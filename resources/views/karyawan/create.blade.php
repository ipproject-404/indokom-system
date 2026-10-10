<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Karyawan - Indokom System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function hanyaAngka(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
    </script>
</head>
<body class="bg-gray-50">

<div class="flex min-h-screen bg-gray-50">

    @include('partials.sidebar-hrd')

    <!-- MAIN CONTENT -->
    <main class="grow flex flex-col min-w-0">
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center sticky top-0 z-10">
            <a href="{{ route('karyawan.index') }}" class="text-gray-500 hover:bg-gray-100 p-2 rounded-lg mr-3 transition-colors">
                <i class="bi bi-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="font-bold text-xl text-gray-800">Tambah Karyawan Baru</h1>
                <p class="text-sm text-gray-500 mt-0.5">Lengkapi formulir di bawah ini dengan data yang valid</p>
            </div>
        </nav>

        <div class="p-6 grow overflow-y-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-4xl mx-auto">

                @if ($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-lg mb-6 text-sm">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('karyawan.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- SEKSI 1: DATA PRIBADI -->
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                            <i class="bi bi-person-lines-fill"></i>
                        </div>
                        <h3 class="font-bold text-gray-800">Data Pribadi</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIK KTP <span class="text-xs text-gray-400">(16 digit)</span></label>
                            <input type="text" name="nik_ktp" value="{{ old('nik_ktp') }}" maxlength="16" minlength="16" onkeypress="return hanyaAngka(event)" placeholder="Contoh: 187103..." class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIK Kerja <span class="text-rose-500">*</span></label>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                            <div class="flex gap-2">
                                <select name="tingkat_pendidikan" required class="w-32 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none bg-white text-sm">
                                    <option value="">Jenjang</option>
                                    <option value="SD" {{ old('tingkat_pendidikan') == 'SD' ? 'selected' : '' }}>SD</option>
                                    <option value="SMP" {{ old('tingkat_pendidikan') == 'SMP' ? 'selected' : '' }}>SMP</option>
                                    <option value="SMA/SMK" {{ old('tingkat_pendidikan') == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                                    <option value="D3" {{ old('tingkat_pendidikan') == 'D3' ? 'selected' : '' }}>D3</option>
                                    <option value="S1" {{ old('tingkat_pendidikan') == 'S1' ? 'selected' : '' }}>S1</option>
                                    <option value="S2" {{ old('tingkat_pendidikan') == 'S2' ? 'selected' : '' }}>S2</option>
                                    <option value="S3" {{ old('tingkat_pendidikan') == 'S3' ? 'selected' : '' }}>S3</option>
                                </select>
                                <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah') }}"
                                       placeholder="Nama Sekolah / Institusi"
                                       class="flex-1 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none text-sm">
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1"><i class="bi bi-info-circle"></i> Hasil digabung, contoh: <strong>S1-Unila</strong></p>
                        </div>
                    </div>

                    <!-- SEKSI 2: DATA KEPEGAWAIAN -->
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-100 pt-4">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                        <h3 class="font-bold text-gray-800">Data Kepegawaian</h3>
                    </div>

                    <!-- Perusahaan & Tipe Karyawan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Perusahaan <span class="text-rose-500">*</span></label>
                            <select name="perusahaan_id" id="perusahaan_id" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none bg-white" required>
                                <option value="">Pilih Perusahaan...</option>
                                @foreach($perusahaans as $pt)
                                    <option value="{{ $pt->id }}" {{ old('perusahaan_id') == $pt->id ? 'selected' : '' }}>
                                        {{ $pt->kode }} - {{ $pt->nama_perusahaan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Karyawan <span class="text-rose-500">*</span></label>
                            <select name="tipe_karyawan_id" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none bg-white" required>
                                <option value="">Pilih Tipe Karyawan...</option>
                                @foreach($tipeKaryawans as $tk)
                                    <option value="{{ $tk->id }}" {{ old('tipe_karyawan_id') == $tk->id ? 'selected' : '' }}>
                                        {{ $tk->nama }} ({{ $tk->dasar_absensi }} - {{ $tk->periode_gaji }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Shift -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div id="box-shift-tetap">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shift Tetap</label>
                            <select name="shift_id" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none bg-white">
                                <option value="">Tanpa shift (borongan)</option>
                                @foreach($shifts as $s)
                                    <option value="{{ $s->id }}" {{ old('shift_id', $shiftDefaultId) == $s->id ? 'selected' : '' }}>
                                        {{ $s->nama_shift }} ({{ substr($s->jam_masuk, 0, 5) }} - {{ substr($s->jam_pulang_default, 0, 5) }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-gray-400 mt-1"><i class="bi bi-info-circle"></i> Wajib untuk karyawan bulanan yang tidak memakai jadwal bergilir.</p>
                        </div>
                        <div class="flex items-start gap-3 bg-indigo-50/50 border border-indigo-100 rounded-lg p-3">
                            <input type="hidden" name="pakai_jadwal_shift" value="0">
                            <input type="checkbox" id="pakai_jadwal_shift" name="pakai_jadwal_shift" value="1"
                                   class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded"
                                   {{ old('pakai_jadwal_shift') ? 'checked' : '' }}>
                            <label for="pakai_jadwal_shift" class="cursor-pointer">
                                <span class="block text-sm font-medium text-gray-700">Pakai jadwal shift bergilir</span>
                                <span class="block text-[11px] text-gray-500 mt-0.5">
                                    Jadwal diatur kepala bagian lewat menu Shift Tim. Hanya berlaku untuk tipe karyawan dengan dasar absensi "jadwal" (bulanan).
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Divisi → Departemen → Jabatan -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Divisi <span class="text-rose-500">*</span></label>
                            <select name="divisi_filter" id="divisi_id" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none bg-white disabled:bg-gray-100 disabled:cursor-not-allowed" required disabled>
                                <option value="">Pilih Perusahaan dahulu...</option>
                            </select>
                            <p class="text-[11px] text-gray-400 mt-1"><i class="bi bi-info-circle"></i> Untuk filter departemen</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Departemen <span class="text-rose-500">*</span></label>
                            <select name="departemen_id" id="departemen_id" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none bg-white disabled:bg-gray-100 disabled:cursor-not-allowed" required disabled>
                                <option value="">Pilih Divisi dahulu...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan <span class="text-rose-500">*</span></label>
                            <select name="jabatan_id" id="jabatan_id" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none bg-white disabled:bg-gray-100 disabled:cursor-not-allowed" required disabled>
                                <option value="">Pilih Departemen dahulu...</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tanggal Masuk -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-600 outline-none" required>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end border-t border-gray-100">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
                            <i class="bi bi-save"></i> Simpan Data Karyawan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<!-- SCRIPT AJAX CASCADING -->
<script>
    // ==========================================
    // 1. Perusahaan → Divisi
    // ==========================================
    document.getElementById('perusahaan_id').addEventListener('change', function() {
        let ptId = this.value;
        let divisiSelect = document.getElementById('divisi_id');
        let deptSelect = document.getElementById('departemen_id');
        let jabSelect = document.getElementById('jabatan_id');

        divisiSelect.innerHTML = '<option value="">Loading...</option>';
        divisiSelect.disabled = true;
        deptSelect.innerHTML = '<option value="">Pilih Divisi dahulu...</option>';
        deptSelect.disabled = true;
        jabSelect.innerHTML = '<option value="">Pilih Departemen dahulu...</option>';
        jabSelect.disabled = true;

        if (ptId) {
            fetch('/get-divisi/' + ptId)
                .then(r => r.json())
                .then(data => {
                    divisiSelect.innerHTML = '<option value="">Pilih Divisi...</option>';
                    divisiSelect.disabled = false;
                    data.forEach(d => {
                        let opt = document.createElement('option');
                        opt.value = d.id;
                        opt.textContent = d.nama_divisi;
                        divisiSelect.appendChild(opt);
                    });
                })
                .catch(() => {
                    divisiSelect.innerHTML = '<option value="">Gagal memuat</option>';
                });
        } else {
            divisiSelect.innerHTML = '<option value="">Pilih Perusahaan dahulu...</option>';
        }
    });

    // ==========================================
    // 2. Divisi → Departemen
    // ==========================================
    document.getElementById('divisi_id').addEventListener('change', function() {
        let divisiId = this.value;
        let deptSelect = document.getElementById('departemen_id');
        let jabSelect = document.getElementById('jabatan_id');

        deptSelect.innerHTML = '<option value="">Loading...</option>';
        deptSelect.disabled = true;
        jabSelect.innerHTML = '<option value="">Pilih Departemen dahulu...</option>';
        jabSelect.disabled = true;

        if (divisiId) {
            fetch('/get-departemen/' + divisiId)
                .then(r => r.json())
                .then(data => {
                    deptSelect.innerHTML = '<option value="">Pilih Departemen...</option>';
                    deptSelect.disabled = false;
                    data.forEach(d => {
                        let opt = document.createElement('option');
                        opt.value = d.id;
                        opt.textContent = d.nama_departemen;
                        deptSelect.appendChild(opt);
                    });
                })
                .catch(() => {
                    deptSelect.innerHTML = '<option value="">Gagal memuat</option>';
                });
        } else {
            deptSelect.innerHTML = '<option value="">Pilih Divisi dahulu...</option>';
        }
    });

    // ==========================================
    // 3. Departemen → Jabatan
    // ==========================================
    document.getElementById('departemen_id').addEventListener('change', function() {
        let deptId = this.value;
        let jabSelect = document.getElementById('jabatan_id');

        jabSelect.innerHTML = '<option value="">Loading...</option>';
        jabSelect.disabled = true;

        if (deptId) {
            fetch('/get-jabatan/' + deptId)
                .then(r => r.json())
                .then(data => {
                    jabSelect.innerHTML = '<option value="">Pilih Jabatan...</option>';
                    jabSelect.disabled = false;
                    data.forEach(j => {
                        let opt = document.createElement('option');
                        opt.value = j.id;
                        opt.textContent = j.nama_jabatan;
                        if("{{ old('jabatan_id') }}" == j.id) opt.selected = true;
                        jabSelect.appendChild(opt);
                    });
                })
                .catch(() => {
                    jabSelect.innerHTML = '<option value="">Gagal memuat</option>';
                });
        } else {
            jabSelect.innerHTML = '<option value="">Pilih Departemen dahulu...</option>';
        }
    });

    // ==========================================
    // Trigger otomatis jika ada old value
    // ==========================================
    @if(old('perusahaan_id'))
        document.getElementById('perusahaan_id').dispatchEvent(new Event('change'));
        setTimeout(() => {
            @if(old('divisi_filter') || old('divisi_id'))
                let divisiOld = "{{ old('divisi_filter') ?? old('divisi_id') }}";
                let divisiSel = document.getElementById('divisi_id');
                if (divisiSel && divisiOld) {
                    divisiSel.value = divisiOld;
                    divisiSel.dispatchEvent(new Event('change'));

                    setTimeout(() => {
                        @if(old('departemen_id'))
                            let deptSel = document.getElementById('departemen_id');
                            if (deptSel) {
                                deptSel.value = "{{ old('departemen_id') }}";
                                deptSel.dispatchEvent(new Event('change'));
                            }
                        @endif
                    }, 400);
                }
            @endif
        }, 500);
    @endif

        // Shift tetap disembunyikan kalau pakai jadwal bergilir (tetap ikut terkirim).
    function toggleShiftTetap() {
        var cek = document.getElementById('pakai_jadwal_shift');
        var box = document.getElementById('box-shift-tetap');
        if (cek && box) box.style.display = cek.checked ? 'none' : '';
    }
    document.getElementById('pakai_jadwal_shift').addEventListener('change', toggleShiftTetap);
    toggleShiftTetap();
</script>
</body>
</html>