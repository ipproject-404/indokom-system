<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struktur Bagan Jabatan & Departemen - HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function bukaModalEditDepartemen(id, nama) {
            document.getElementById('edit_dept_id').value = id;
            document.getElementById('edit_nama_departemen').value = nama;
            document.getElementById('formEditDepartemen').action = "/hrd/departemen/" + id;
            document.getElementById('modalEditDepartemen').classList.remove('hidden');
        }
        function tutupModalEditDepartemen() {
            document.getElementById('modalEditDepartemen').classList.add('hidden');
        }

        function bukaModalEditJabatan(id, nama, deptId) {
            document.getElementById('edit_jab_id').value = id;
            document.getElementById('edit_nama_jabatan').value = nama;
            document.getElementById('edit_dept_induk').value = deptId;
            document.getElementById('formEditJabatan').action = "/hrd/jabatan/" + id;
            document.getElementById('modalEditJabatan').classList.remove('hidden');
        }
        function tutupModalEditJabatan() {
            document.getElementById('modalEditJabatan').classList.add('hidden');
        }
    </script>
</head>
<body class="bg-gray-50">

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-10 shadow-sm">
        <div class="flex items-center">
            <a href="{{ route('dashboard.hrd') }}" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg mr-3 transition-colors">
                <i class="bi bi-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="font-bold text-xl text-gray-800">Struktur Bagan Jabatan & Departemen</h1>
            </div>
        </div>
    </nav>

    <div class="p-6 max-w-5xl mx-auto space-y-6">
        
        <!-- Alert Success / Error -->
        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 px-4 py-3 rounded-lg shadow-sm flex items-center text-emerald-700">
            <i class="bi bi-check-circle-fill mr-2 text-lg"></i> 
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 px-4 py-3 rounded-lg shadow-sm flex items-center text-rose-700">
            <i class="bi bi-exclamation-triangle-fill mr-2 text-lg"></i> 
            <span class="font-bold text-sm">{{ session('error') }}</span>
        </div>
        @endif

        @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 px-4 py-3 rounded-lg shadow-sm text-rose-700 text-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- PANEL FORM TAMBAH DATA (ATAS) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Form Tambah Departemen -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 text-sm mb-3 flex items-center">
                    <i class="bi bi-plus-circle-fill text-blue-600 mr-2"></i> Tambah Departemen Baru
                </h3>
                <form action="{{ route('departemen.store') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="nama_departemen" placeholder="Nama Departemen..." class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-600 outline-none" required>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors shadow-sm shrink-0">
                        Simpan
                    </button>
                </form>
            </div>

            <!-- Form Tambah Jabatan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 text-sm mb-3 flex items-center">
                    <i class="bi bi-plus-circle-fill text-indigo-600 mr-2"></i> Tambah Jabatan Baru
                </h3>
                <form action="{{ route('jabatan.store') }}" method="POST" class="space-y-2">
                    @csrf
                    <select name="departemen_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-600 outline-none bg-white" required>
                        <option value="">-- Pilih Departemen Induk --</option>
                        @foreach($departemens as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </select>
                    <div class="flex gap-2">
                        <input type="text" name="nama_jabatan" placeholder="Nama Jabatan..." class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-600 outline-none" required>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors shadow-sm shrink-0">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- BAGAN STRUKTUR ORGANISASI (HIERARKI) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="border-b border-gray-100 pb-4 mb-6 flex justify-between items-center">
                <h2 class="font-bold text-gray-800 text-lg flex items-center">
                    <i class="bi bi-diagram-3-fill mr-2 text-blue-600"></i> Bagan Hierarki Struktur Perusahaan
                </h2>
                <span class="text-xs text-gray-500 font-medium">Total Departemen: {{ count($departemens) }}</span>
            </div>

            <div class="space-y-6">
                @forelse($departemens as $dept)
                <!-- KOTAK DEPARTEMEN UTAMA (LEVEL ATAS) -->
                <div class="border-2 border-blue-200 rounded-xl bg-blue-50/30 overflow-hidden shadow-sm">
                    <!-- Header Departemen -->
                    <div class="bg-blue-600 text-white px-5 py-3 flex justify-between items-center">
                        <div class="flex items-center">
                            <i class="bi bi-building-fill text-lg mr-2"></i>
                            <span class="font-bold tracking-wide uppercase text-sm">{{ $dept->nama_departemen }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="bg-blue-700 text-blue-100 text-xs px-2.5 py-0.5 rounded-full font-semibold mr-2">
                                {{ $jabatans->where('departemen_id', $dept->id)->count() }} Jabatan
                            </span>
                            
                            <!-- Tombol Edit Departemen -->
                            <button onclick="bukaModalEditDepartemen('{{ $dept->id }}', '{{ $dept->nama_departemen }}')" class="text-blue-200 hover:text-white transition-colors p-1" title="Edit Departemen">
                                <i class="bi bi-pencil-square text-base"></i>
                            </button>

                            <!-- Tombol Hapus Departemen -->
                            <form action="{{ route('departemen.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('Hapus departemen {{ $dept->nama_departemen }}?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-blue-200 hover:text-white transition-colors p-1" title="Hapus Departemen">
                                    <i class="bi bi-trash-fill text-base"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- CABANG KEBAWAH (DAFTAR JABATAN DI BAWAH DEPARTEMEN) -->
                    <div class="p-4 bg-white">
                        @php
                            $listJabatan = $jabatans->where('departemen_id', $dept->id);
                        @endphp

                        @if($listJabatan->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach($listJabatan as $jab)
                                <div class="border border-gray-200 rounded-lg p-3 bg-gray-50 flex justify-between items-center hover:shadow-md transition-shadow relative">
                                    <div class="absolute -top-3 left-4 w-0.5 h-3 bg-blue-300"></div>
                                    
                                    <div class="flex items-center overflow-hidden pr-2">
                                        <div class="bg-indigo-100 text-indigo-700 rounded-md p-2 mr-2.5 shrink-0">
                                            <i class="bi bi-person-badge-fill text-sm"></i>
                                        </div>
                                        <div class="truncate">
                                            <div class="font-bold text-gray-800 text-xs truncate">{{ $jab->nama_jabatan }}</div>
                                            <div class="text-[10px] text-gray-400">Sub-Posisi</div>
                                        </div>
                                    </div>

                                    <!-- Tombol Aksi Jabatan (Edit & Hapus) -->
                                    <div class="flex items-center gap-1 shrink-0">
                                        <button onclick="bukaModalEditJabatan('{{ $jab->id }}', '{{ $jab->nama_jabatan }}', '{{ $jab->departemen_id }}')" class="text-gray-400 hover:text-blue-600 transition-colors p-1" title="Edit Jabatan">
                                            <i class="bi bi-pencil-fill text-xs"></i>
                                        </button>
                                        <form action="{{ route('jabatan.destroy', $jab->id) }}" method="POST" onsubmit="return confirm('Hapus jabatan {{ $jab->nama_jabatan }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-rose-600 transition-colors p-1" title="Hapus Jabatan">
                                                <i class="bi bi-x-circle-fill text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-gray-400 text-xs italic">
                                <i class="bi bi-info-circle mr-1"></i> Belum ada posisi jabatan terdaftar di departemen ini.
                            </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-gray-400 text-sm">
                    Belum ada data departemen dan jabatan yang tercatat.
                </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- ================= MODAL EDIT DEPARTEMEN ================= -->
    <div id="modalEditDepartemen" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg max-w-md w-full p-6">
            <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center">
                <i class="bi bi-pencil-square text-blue-600 mr-2"></i> Edit Departemen
            </h3>
            <form id="formEditDepartemen" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_dept_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Departemen</label>
                    <input type="text" name="nama_departemen" id="edit_nama_departemen" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-600 outline-none" required>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="tutupModalEditDepartemen()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL EDIT JABATAN ================= -->
    <div id="modalEditJabatan" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg max-w-md w-full p-6">
            <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center">
                <i class="bi bi-pencil-square text-indigo-600 mr-2"></i> Edit Jabatan
            </h3>
            <form id="formEditJabatan" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_jab_id">
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Departemen Induk</label>
                    <select name="departemen_id" id="edit_dept_induk" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-600 outline-none bg-white" required>
                        @foreach($departemens as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Jabatan</label>
                    <input type="text" name="nama_jabatan" id="edit_nama_jabatan" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-600 outline-none" required>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="tutupModalEditJabatan()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>