<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Karyawan - Indokom System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">

<div class="flex min-h-screen">

    @include('partials.sidebar-hrd')

    <!-- MAIN CONTENT -->
    <main class="grow flex flex-col min-w-0">

        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center">
                <a href="{{ route('karyawan.show', $karyawan->id) }}" class="text-gray-500 hover:bg-gray-100 p-2 rounded-lg mr-3 transition-colors">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="font-bold text-xl text-gray-800">Edit Data Karyawan</h1>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $karyawan->nama_lengkap }} &bull; {{ $karyawan->nik_kerja }}</p>
                </div>
            </div>
            <a href="{{ route('karyawan.show', $karyawan->id) }}" class="bg-slate-100 text-slate-700 px-4 py-2 rounded-lg font-medium hover:bg-slate-200 transition-colors">
                <i class="bi bi-x-lg mr-1"></i> Batal
            </a>
        </nav>

        <div class="p-6 grow overflow-y-auto">
            <div class="max-w-4xl mx-auto">

                @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-xl mb-6 text-sm">
                    <div class="flex items-start gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-lg mt-0.5"></i>
                        <div>
                            <p class="font-bold mb-1">Ada kesalahan pada input Anda:</p>
                            <ul class="list-disc pl-5 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- ==================== SEKSI 1: DATA PRIBADI ==================== -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-3 mb-5">
                            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                <i class="bi bi-person-lines-fill"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-sm">Data Pribadi</h3>
                                <p class="text-xs text-slate-400">Informasi identitas karyawan</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}" 
                                       class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    NIK KTP <span class="text-xs text-gray-400">(16 digit)</span> <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="nik_ktp" value="{{ old('nik_ktp', $karyawan->nik_ktp) }}" 
                                       maxlength="16" minlength="16" onkeypress="return hanyaAngka(event)"
                                       class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    NIK Kerja <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="nik_kerja" value="{{ old('nik_kerja', $karyawan->nik_kerja) }}" 
                                       class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Kelamin <span class="text-rose-500">*</span></label>
                                <select name="jenis_kelamin" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                                    <option value="L" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tempat Lahir <span class="text-rose-500">*</span></label>
                                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $karyawan->tempat_lahir) }}" 
                                       class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Lahir <span class="text-rose-500">*</span></label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir) }}" 
                                       class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                                @if($karyawan->tanggal_lahir)
                                    <p class="text-[11px] text-blue-600 mt-1">
                                        <i class="bi bi-cake2"></i> Umur saat ini: <b>{{ \Carbon\Carbon::parse($karyawan->tanggal_lahir)->age }} tahun</b>
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Lengkap <span class="text-rose-500">*</span></label>
                            <textarea name="alamat" rows="2" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>{{ old('alamat', $karyawan->alamat) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Handphone <span class="text-rose-500">*</span></label>
                                <input type="text" name="no_hp" value="{{ old('no_hp', $karyawan->no_hp) }}" 
                                       onkeypress="return hanyaAngka(event)"
                                       class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Pendidikan Terakhir</label>
                                @php
                                    $pendParts = explode('-', $karyawan->pendidikan ?? '', 2);
                                    $oldTingkat = $pendParts[0] ?? '';
                                    $oldSekolah = $pendParts[1] ?? '';
                                @endphp
                                <div class="flex gap-2">
                                    <select name="tingkat_pendidikan" class="w-32 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                                        <option value="">Jenjang</option>
                                        @foreach(['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3'] as $opsi)
                                            <option value="{{ $opsi }}" {{ old('tingkat_pendidikan', $oldTingkat) == $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah', $oldSekolah) }}" 
                                           placeholder="Nama Sekolah / Institusi"
                                           class="flex-1 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== SEKSI 2: DATA KEPEGAWAIAN ==================== -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-3 mb-5">
                            <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <i class="bi bi-briefcase-fill"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-sm">Data Kepegawaian</h3>
                                <p class="text-xs text-slate-400">Informasi penempatan & posisi karyawan</p>
                            </div>
                        </div>

                        <!-- PERUSAHAAN & TIPE KARYAWAN -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Perusahaan <span class="text-rose-500">*</span></label>
                                <select name="perusahaan_id" id="perusahaan_id" 
                                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white" required>
                                    <option value="">Pilih Perusahaan...</option>
                                    @foreach($perusahaans as $pt)
                                        <option value="{{ $pt->id }}" {{ old('perusahaan_id', $karyawan->perusahaan_id) == $pt->id ? 'selected' : '' }}>
                                            {{ $pt->kode }} - {{ $pt->nama_perusahaan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe Karyawan <span class="text-rose-500">*</span></label>
                                <select name="tipe_karyawan_id" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white" required>
                                    <option value="">Pilih Tipe...</option>
                                    @foreach($tipeKaryawans as $tk)
                                        <option value="{{ $tk->id }}" {{ old('tipe_karyawan_id', $karyawan->tipe_karyawan_id) == $tk->id ? 'selected' : '' }}>
                                            {{ $tk->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- ⭐ CASCADING: DIVISI → DEPARTEMEN → JABATAN -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Divisi <span class="text-xs text-gray-400 font-normal">(untuk filter)</span>
                                </label>
                                <select name="divisi_filter" id="divisi_id" 
                                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white disabled:bg-gray-100 disabled:cursor-not-allowed">
                                    <option value="">Pilih Perusahaan dulu...</option>
                                </select>
                                <p class="text-[10px] text-gray-400 mt-1">
                                    <i class="bi bi-info-circle"></i> Untuk filter departemen
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Departemen <span class="text-rose-500">*</span></label>
                                <select name="departemen_id" id="departemen_id" 
                                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white disabled:bg-gray-100 disabled:cursor-not-allowed" required disabled>
                                    <option value="">Pilih Divisi dulu...</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jabatan <span class="text-rose-500">*</span></label>
                                <select name="jabatan_id" id="jabatan_id" 
                                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white disabled:bg-gray-100 disabled:cursor-not-allowed" required disabled>
                                    <option value="">Pilih Departemen dulu...</option>
                                </select>
                            </div>
                        </div>

                        <!-- STATUS -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Status Karyawan <span class="text-rose-500">*</span></label>
                                <select name="status" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white" required>
                                    <option value="aktif" {{ old('status', $karyawan->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif" {{ old('status', $karyawan->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif (Resign / PHK)</option>
                                </select>
                            </div>
                        </div>

                                                <div id="box-shift-tetap" class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Shift Tetap</label>
                            <select name="shift_id" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
                                <option value="">Tanpa shift (borongan)</option>
                                @foreach($shifts as $s)
                                    <option value="{{ $s->id }}" {{ old('shift_id', $karyawan->shift_id) == $s->id ? 'selected' : '' }}>
                                        {{ $s->nama_shift }} ({{ substr($s->jam_masuk, 0, 5) }} - {{ substr($s->jam_pulang_default, 0, 5) }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-gray-400 mt-1"><i class="bi bi-info-circle"></i> Wajib untuk karyawan bulanan yang tidak memakai jadwal bergilir.</p>
                        </div>
                                                <div class="mt-4 flex items-start gap-3 bg-indigo-50/50 border border-indigo-100 rounded-lg p-3">
                            <input type="hidden" name="pakai_jadwal_shift" value="0">
                            <input type="checkbox" id="pakai_jadwal_shift" name="pakai_jadwal_shift" value="1"
                                   class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded"
                                   {{ old('pakai_jadwal_shift', $karyawan->pakai_jadwal_shift) ? 'checked' : '' }}>
                            <label for="pakai_jadwal_shift" class="cursor-pointer">
                                <span class="block text-sm font-medium text-gray-700">Pakai jadwal shift bergilir</span>
                                <span class="block text-[11px] text-gray-500 mt-0.5">
                                    Jadwal diatur kepala bagian lewat menu Shift Tim. Hanya berlaku untuk tipe karyawan dengan dasar absensi "jadwal" (bulanan). Kalau tidak dicentang, karyawan memakai shift tetap.
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- ==================== SEKSI 3: PERIODE KERJA ==================== -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-3 mb-5">
                            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i class="bi bi-calendar-range"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-sm">Periode Kerja</h3>
                                <p class="text-xs text-slate-400">Tanggal mulai bergabung & jika sudah berhenti</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Tanggal Masuk <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', $karyawan->tanggal_masuk) }}" 
                                       class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                                @if($karyawan->tanggal_masuk)
                                    <p class="text-[11px] text-emerald-600 mt-1 font-medium">
                                        <i class="bi bi-clock-history"></i> Masa kerja saat ini: <b>{{ \Carbon\Carbon::parse($karyawan->tanggal_masuk)->diffForHumans(now(), true) }}</b>
                                    </p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Tanggal Keluar
                                    <span class="text-xs text-gray-400 font-normal">(opsional — diisi jika resign/PHK)</span>
                                </label>
                                <input type="date" name="tanggal_keluar" value="{{ old('tanggal_keluar', $karyawan->tanggal_keluar) }}" 
                                       class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <p class="text-[11px] text-amber-600 mt-1">
                                    <i class="bi bi-info-circle"></i> Isi jika karyawan sudah tidak aktif. Kosongkan jika masih bekerja.
                                </p>
                            </div>
                        </div>

                        @if($karyawan->tanggal_masuk && $karyawan->tanggal_keluar)
                        <div class="mt-4 bg-slate-50 border border-slate-200 rounded-lg p-3">
                            <p class="text-xs text-slate-700 font-medium">
                                <i class="bi bi-info-circle-fill text-blue-500"></i>
                                Total masa kerja tercatat: 
                                <b>{{ \Carbon\Carbon::parse($karyawan->tanggal_masuk)->diff(\Carbon\Carbon::parse($karyawan->tanggal_keluar))->format('%y tahun %m bulan %d hari') }}</b>
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- ==================== TOMBOL AKSI ==================== -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 flex justify-end gap-3">
                        <a href="{{ route('karyawan.show', $karyawan->id) }}" 
                           class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-bold transition-colors flex items-center gap-2">
                            <i class="bi bi-x-lg"></i> Batal
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold transition-colors shadow-sm flex items-center gap-2">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>
</div>

<!-- ============================================================
     SCRIPT: Cascading Dropdown 3 Tingkat (Perusahaan → Divisi → Departemen → Jabatan)
============================================================ -->
<script>
    // Helper: hanya angka
    function hanyaAngka(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) return false;
        return true;
    }

    // ==========================================
    // DATA AWAL (Karyawan Existing)
    // ==========================================
    const initialData = {
        perusahaanId: "{{ $karyawan->perusahaan_id ?? '' }}",
        divisiId: "{{ $karyawan->departemen->divisi_id ?? '' }}",
        departemenId: "{{ $karyawan->departemen_id ?? '' }}",
        jabatanId: "{{ $karyawan->jabatan_id ?? '' }}"
    };

    // ==========================================
    // FUNGSI: Load Divisi
    // ==========================================
    function loadDivisi(perusahaanId, selectedDivisiId = null) {
        let divisiSelect = document.getElementById('divisi_id');
        let deptSelect = document.getElementById('departemen_id');
        let jabSelect = document.getElementById('jabatan_id');

        // Reset departemen & jabatan
        deptSelect.innerHTML = '<option value="">Pilih Divisi dulu...</option>';
        deptSelect.disabled = true;
        jabSelect.innerHTML = '<option value="">Pilih Departemen dulu...</option>';
        jabSelect.disabled = true;

        if (!perusahaanId) {
            divisiSelect.innerHTML = '<option value="">Pilih Perusahaan dulu...</option>';
            divisiSelect.disabled = true;
            return;
        }

        divisiSelect.innerHTML = '<option value="">Loading...</option>';
        divisiSelect.disabled = true;

        fetch('/get-divisi/' + perusahaanId)
            .then(r => r.json())
            .then(data => {
                divisiSelect.innerHTML = '<option value="">Pilih Divisi...</option>';
                divisiSelect.disabled = false;

                data.forEach(d => {
                    let opt = document.createElement('option');
                    opt.value = d.id;
                    opt.textContent = d.nama_divisi;
                    if (selectedDivisiId && selectedDivisiId == d.id) opt.selected = true;
                    divisiSelect.appendChild(opt);
                });

                // Kalau ada selectedDivisiId (kasus edit), trigger load departemen
                if (selectedDivisiId) {
                    loadDepartemen(selectedDivisiId, initialData.departemenId);
                }
            })
            .catch(() => {
                divisiSelect.innerHTML = '<option value="">Gagal memuat</option>';
            });
    }

    // ==========================================
    // FUNGSI: Load Departemen
    // ==========================================
    function loadDepartemen(divisiId, selectedDepartemenId = null) {
        let deptSelect = document.getElementById('departemen_id');
        let jabSelect = document.getElementById('jabatan_id');

        // Reset jabatan
        jabSelect.innerHTML = '<option value="">Pilih Departemen dulu...</option>';
        jabSelect.disabled = true;

        if (!divisiId) {
            deptSelect.innerHTML = '<option value="">Pilih Divisi dulu...</option>';
            deptSelect.disabled = true;
            return;
        }

        deptSelect.innerHTML = '<option value="">Loading...</option>';
        deptSelect.disabled = true;

        fetch('/get-departemen/' + divisiId)
            .then(r => r.json())
            .then(data => {
                deptSelect.innerHTML = '<option value="">Pilih Departemen...</option>';
                deptSelect.disabled = false;

                data.forEach(d => {
                    let opt = document.createElement('option');
                    opt.value = d.id;
                    opt.textContent = d.nama_departemen;
                    if (selectedDepartemenId && selectedDepartemenId == d.id) opt.selected = true;
                    deptSelect.appendChild(opt);
                });

                // Kalau ada selectedDepartemenId (kasus edit), trigger load jabatan
                if (selectedDepartemenId) {
                    loadJabatan(selectedDepartemenId, initialData.jabatanId);
                }
            })
            .catch(() => {
                deptSelect.innerHTML = '<option value="">Gagal memuat</option>';
            });
    }

    // ==========================================
    // FUNGSI: Load Jabatan
    // ==========================================
    function loadJabatan(departemenId, selectedJabatanId = null) {
        let jabSelect = document.getElementById('jabatan_id');

        if (!departemenId) {
            jabSelect.innerHTML = '<option value="">Pilih Departemen dulu...</option>';
            jabSelect.disabled = true;
            return;
        }

        jabSelect.innerHTML = '<option value="">Loading...</option>';
        jabSelect.disabled = true;

        fetch('/get-jabatan/' + departemenId)
            .then(r => r.json())
            .then(data => {
                jabSelect.innerHTML = '<option value="">Pilih Jabatan...</option>';
                jabSelect.disabled = false;

                data.forEach(j => {
                    let opt = document.createElement('option');
                    opt.value = j.id;
                    opt.textContent = j.nama_jabatan;
                    if (selectedJabatanId && selectedJabatanId == j.id) opt.selected = true;
                    jabSelect.appendChild(opt);
                });
            })
            .catch(() => {
                jabSelect.innerHTML = '<option value="">Gagal memuat</option>';
            });
    }

    // ==========================================
    // EVENT LISTENERS
    // ==========================================

    // 1. Perusahaan berubah → load divisi (dari awal, tanpa pre-select)
    document.getElementById('perusahaan_id').addEventListener('change', function () {
        loadDivisi(this.value);
    });

    // 2. Divisi berubah → load departemen (dari awal, tanpa pre-select)
    document.getElementById('divisi_id').addEventListener('change', function () {
        loadDepartemen(this.value);
    });

    // 3. Departemen berubah → load jabatan (dari awal, tanpa pre-select)
    document.getElementById('departemen_id').addEventListener('change', function () {
        loadJabatan(this.value);
    });

    // ==========================================
    // AUTO-LOAD saat halaman pertama dibuka (pre-select data existing)
    // ==========================================
    document.addEventListener('DOMContentLoaded', function () {
        if (initialData.perusahaanId) {
            loadDivisi(initialData.perusahaanId, initialData.divisiId);
        }
    });

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