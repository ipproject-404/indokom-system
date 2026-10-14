<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelatihan & Sertifikasi - HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
<div class="flex min-h-screen">

    @include('partials.sidebar-hrd')

    <main class="grow flex flex-col min-w-0">
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-20 shadow-sm">
            <div class="flex items-center">
                <a href="{{ route('dashboard.hrd') }}" class="text-gray-500 hover:bg-gray-100 p-2 rounded-lg mr-3">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="font-bold text-xl text-gray-800">Pelatihan & Sertifikasi</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Kelola jadwal pelatihan dan tunjuk peserta</p>
                </div>
            </div>
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 shadow-sm">
                <i class="bi bi-plus-lg mr-1"></i> Tambah Pelatihan
            </button>
        </nav>

        <div class="p-6 grow overflow-y-auto max-w-6xl mx-auto w-full">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-4 text-sm font-semibold">
                    <i class="bi bi-check-circle-fill mr-1"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg mb-4 text-sm font-semibold">
                    <i class="bi bi-exclamation-triangle-fill mr-1"></i> {{ session('error') }}
                </div>
            @endif

            {{-- Filter status --}}
            <div class="flex gap-2 mb-5 flex-wrap">
                @foreach([''=>'Semua', 'aktif'=>'Aktif', 'selesai'=>'Selesai', 'dibatalkan'=>'Dibatalkan'] as $val => $label)
                    <a href="{{ route('pelatihan.index', ['status' => $val]) }}"
                        class="px-4 py-1.5 rounded-full text-xs font-bold border transition-colors
                        {{ request('status') === $val || (request('status') === null && $val === '')
                            ? 'bg-blue-600 text-white border-blue-600'
                            : 'bg-white text-gray-600 border-gray-300 hover:border-blue-400' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Daftar Pelatihan --}}
            <div class="space-y-4">
                @forelse($pelatihans as $p)
                @php
                    $warnaBadge = match($p->status) {
                        'selesai'    => 'bg-gray-100 text-gray-600',
                        'dibatalkan' => 'bg-rose-100 text-rose-700',
                        default      => $p->sedangBerjalan()
                            ? 'bg-emerald-100 text-emerald-700'
                            : 'bg-blue-100 text-blue-700',
                    };
                    $labelStatus = match(true) {
                        $p->status === 'selesai'    => 'Selesai',
                        $p->status === 'dibatalkan' => 'Dibatalkan',
                        $p->sedangBerjalan()        => 'Sedang Berjalan',
                        $p->akanDatang()            => 'Akan Datang',
                        default                     => 'Aktif',
                    };
                    $warnaKategori = match($p->kategori) {
                        'sertifikasi'          => 'bg-violet-50 text-violet-700',
                        'pelatihan_eksternal'  => 'bg-amber-50 text-amber-700',
                        default                => 'bg-sky-50 text-sky-700',
                    };
                @endphp
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" x-data="{ bukaDetail: false }">
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $warnaKategori }}">
                                        {{ str_replace('_', ' ', ucfirst($p->kategori)) }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $warnaBadge }}">
                                        {{ $labelStatus }}
                                    </span>
                                </div>
                                <h3 class="font-bold text-gray-900 text-base">{{ $p->nama_pelatihan }}</h3>
                                <div class="text-sm text-gray-500 mt-1 flex flex-wrap gap-x-4 gap-y-1">
                                    @if($p->penyelenggara)
                                        <span><i class="bi bi-building mr-1"></i>{{ $p->penyelenggara }}</span>
                                    @endif
                                    <span><i class="bi bi-calendar3 mr-1"></i>
                                        {{ $p->tanggal_mulai->translatedFormat('d M Y') }}
                                        @if($p->tanggal_selesai && !$p->tanggal_mulai->isSameDay($p->tanggal_selesai))
                                            – {{ $p->tanggal_selesai->translatedFormat('d M Y') }}
                                        @endif
                                    </span>
                                    <span><i class="bi bi-people mr-1"></i>{{ $p->peserta->count() }} peserta</span>
                                </div>
                                @if($p->deskripsi)
                                    <p class="text-xs text-gray-400 mt-1">{{ $p->deskripsi }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button @click="bukaDetail = !bukaDetail"
                                    class="text-xs font-bold text-blue-600 hover:text-blue-700 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors">
                                    <span x-text="bukaDetail ? 'Tutup' : 'Kelola Peserta'"></span>
                                </button>
                                <button onclick="bukaModalEdit({{ $p->id }}, {{ json_encode($p) }})"
                                    class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <i class="bi bi-pencil-fill text-sm"></i>
                                </button>
                                <form method="POST" action="{{ route('pelatihan.destroy', $p->id) }}"
                                    onsubmit="return confirm('Hapus pelatihan ini beserta semua pesertanya?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                        <i class="bi bi-trash-fill text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Panel Peserta --}}
                    <div x-show="bukaDetail" x-collapse class="border-t border-gray-100">
                        <div class="p-5 bg-gray-50">
                            {{-- Form tambah peserta --}}
                            <form method="POST" action="{{ route('pelatihan.peserta.tambah', $p->id) }}" class="mb-4">
                                @csrf
                                <div class="text-xs font-bold text-gray-600 mb-2">Tunjuk Peserta</div>
                                <div class="bg-white border border-gray-200 rounded-lg p-3 max-h-40 overflow-y-auto mb-2">
                                    @php
                                        $sudahTerdaftar = $p->peserta->pluck('karyawan_id')->toArray();
                                    @endphp
                                    @foreach(\App\Models\Karyawan::with('departemen')->where('status', 'aktif')->orderBy('nama_lengkap')->get() as $k)
                                        <label class="flex items-center gap-2 py-1 cursor-pointer hover:text-blue-600">
                                            <input type="checkbox" name="karyawan_ids[]" value="{{ $k->id }}"
                                                class="rounded"
                                                {{ in_array($k->id, $sudahTerdaftar) ? 'disabled checked' : '' }}>
                                            <span class="text-xs">{{ $k->nama_lengkap }}
                                                <span class="text-gray-400">— {{ $k->departemen->nama_departemen ?? '-' }}</span>
                                                @if(in_array($k->id, $sudahTerdaftar))
                                                    <span class="text-emerald-600 font-bold">(sudah terdaftar)</span>
                                                @endif
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="flex gap-2">
                                    <input type="text" name="catatan" placeholder="Catatan (opsional)"
                                        class="flex-1 text-xs border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-blue-500 outline-none">
                                    <button type="submit"
                                        class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-700">
                                        Tambah
                                    </button>
                                </div>
                            </form>

                            {{-- Daftar peserta --}}
                            <div class="text-xs font-bold text-gray-600 mb-2">Daftar Peserta ({{ $p->peserta->count() }})</div>
                            @forelse($p->peserta as $ps)
                                <div class="flex items-center justify-between bg-white rounded-lg px-3 py-2 mb-1.5 border border-gray-100">
                                    <div>
                                        <div class="text-xs font-semibold text-gray-800">{{ $ps->karyawan->nama_lengkap ?? '-' }}</div>
                                        <div class="text-[10px] text-gray-400">
                                            {{ $ps->karyawan->jabatan->nama_jabatan ?? '-' }} &bull;
                                            {{ $ps->karyawan->departemen->nama_departemen ?? '-' }}
                                        </div>
                                    </div>
                                    <form method="POST"
                                        action="{{ route('pelatihan.peserta.hapus', [$p->id, $ps->karyawan_id]) }}"
                                        onsubmit="return confirm('Hapus peserta ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-rose-500 transition-colors">
                                            <i class="bi bi-x-circle-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="text-xs text-gray-400 text-center py-3">Belum ada peserta ditunjuk.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @empty
                    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                        <i class="bi bi-mortarboard text-4xl text-gray-300 block mb-3"></i>
                        <p class="text-gray-500 font-semibold">Belum ada pelatihan tercatat</p>
                        <p class="text-xs text-gray-400 mt-1">Klik "Tambah Pelatihan" untuk memulai</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">{{ $pelatihans->links() }}</div>
        </div>
    </main>
</div>

{{-- Modal Tambah --}}
<div id="modalTambah" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6">
        <h3 class="font-bold text-gray-800 text-lg mb-4">Tambah Pelatihan Baru</h3>
        <form method="POST" action="{{ route('pelatihan.store') }}">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Nama Pelatihan *</label>
                    <input type="text" name="nama_pelatihan" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Penyelenggara</label>
                        <input type="text" name="penyelenggara" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Kategori *</label>
                        <select name="kategori" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none" required>
                            <option value="pelatihan_internal">Internal</option>
                            <option value="pelatihan_eksternal">Eksternal</option>
                            <option value="sertifikasi">Sertifikasi</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Tanggal Mulai *</label>
                        <input type="date" name="tanggal_mulai" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                </div>
            </div>
            <div class="flex gap-2 justify-end mt-5">
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="px-4 py-2 text-sm font-bold bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit"
                    class="px-4 py-2 text-sm font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEdit" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6">
        <h3 class="font-bold text-gray-800 text-lg mb-4">Edit Pelatihan</h3>
        <form method="POST" id="formEdit">
            @csrf @method('PUT')
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Nama Pelatihan *</label>
                    <input type="text" name="nama_pelatihan" id="editNama" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Penyelenggara</label>
                        <input type="text" name="penyelenggara" id="editPenyelenggara" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Kategori *</label>
                        <select name="kategori" id="editKategori" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none" required>
                            <option value="pelatihan_internal">Internal</option>
                            <option value="pelatihan_eksternal">Eksternal</option>
                            <option value="sertifikasi">Sertifikasi</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Tanggal Mulai *</label>
                        <input type="date" name="tanggal_mulai" id="editTglMulai" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="editTglSelesai" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Status</label>
                    <select name="status" id="editStatus" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="aktif">Aktif</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="editDeskripsi" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                </div>
            </div>
            <div class="flex gap-2 justify-end mt-5">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                    class="px-4 py-2 text-sm font-bold bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit"
                    class="px-4 py-2 text-sm font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function bukaModalEdit(id, data) {
    document.getElementById('formEdit').action = `/hrd/pelatihan/${id}`;
    document.getElementById('editNama').value          = data.nama_pelatihan;
    document.getElementById('editPenyelenggara').value = data.penyelenggara ?? '';
    document.getElementById('editKategori').value      = data.kategori;
    document.getElementById('editTglMulai').value      = data.tanggal_mulai;
    document.getElementById('editTglSelesai').value    = data.tanggal_selesai ?? '';
    document.getElementById('editStatus').value        = data.status;
    document.getElementById('editDeskripsi').value     = data.deskripsi ?? '';
    document.getElementById('modalEdit').classList.remove('hidden');
}
</script>
</body>
</html>