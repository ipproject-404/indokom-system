<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pelatihan & Sertifikasi - HRD</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important;}</style>
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

            {{-- SUMMARY --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                            <i class="bi bi-collection-fill"></i>
                        </div>
                        <div>
                            <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wide">Total</div>
                            <div class="text-xl font-bold text-gray-800">{{ $stats['semua'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-sky-50 text-sky-600 rounded-lg flex items-center justify-center">
                            <i class="bi bi-play-circle-fill"></i>
                        </div>
                        <div>
                            <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wide">Aktif</div>
                            <div class="text-xl font-bold text-gray-800">{{ $stats['aktif'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wide">Selesai</div>
                            <div class="text-xl font-bold text-gray-800">{{ $stats['selesai'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-rose-50 text-rose-600 rounded-lg flex items-center justify-center">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <div>
                            <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wide">Dibatalkan</div>
                            <div class="text-xl font-bold text-gray-800">{{ $stats['dibatalkan'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEARCH BAR --}}
            <div class="mb-4">
                <form method="GET" action="{{ route('pelatihan.index') }}" class="flex gap-2">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="relative flex-1">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Cari nama pelatihan, penyelenggara, atau deskripsi..."
                            class="w-full pl-9 pr-9 py-2.5 text-sm border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                        @if(request('q'))
                            <a href="{{ route('pelatihan.index', ['status' => request('status')]) }}"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-rose-500"
                                title="Hapus pencarian">
                                <i class="bi bi-x-circle-fill"></i>
                            </a>
                        @endif
                    </div>
                    <button type="submit"
                        class="bg-gray-800 text-white px-5 py-2.5 rounded-lg text-sm font-bold hover:bg-gray-900 shrink-0">
                        <i class="bi bi-search mr-1"></i>Cari
                    </button>
                </form>
            </div>

            {{-- INFO FILTER AKTIF --}}
            @if(request('q') || request('status'))
                <div class="mb-4 flex items-center gap-2 flex-wrap text-xs">
                    <span class="text-gray-500">Filter aktif:</span>
                    @if(request('q'))
                        <span class="bg-blue-50 text-blue-700 font-bold px-2 py-1 rounded-lg inline-flex items-center gap-1">
                            <i class="bi bi-search"></i> "{{ request('q') }}"
                        </span>
                    @endif
                    @if(request('status'))
                        <span class="bg-sky-50 text-sky-700 font-bold px-2 py-1 rounded-lg inline-flex items-center gap-1">
                            <i class="bi bi-funnel"></i> {{ ucfirst(request('status')) }}
                        </span>
                    @endif
                    <a href="{{ route('pelatihan.index') }}"
                        class="text-rose-500 hover:underline font-bold inline-flex items-center gap-1">
                        <i class="bi bi-x-circle"></i> Hapus semua filter
                    </a>
                </div>
            @endif

            {{-- FILTER STATUS --}}
            <div class="flex gap-2 mb-5 flex-wrap">
                @foreach([''=>'Semua', 'aktif'=>'Aktif', 'selesai'=>'Selesai', 'dibatalkan'=>'Dibatalkan'] as $val => $label)
                    <a href="{{ route('pelatihan.index', ['status' => $val, 'q' => request('q')]) }}"
                        class="px-4 py-1.5 rounded-full text-xs font-bold border transition-all
                        {{ request('status', '') === $val
                            ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                            : 'bg-white text-gray-600 border-gray-300 hover:border-blue-400 hover:text-blue-600' }}">
                        {{ $label }}
                        <span class="ml-1 opacity-70">({{ $stats[$val ?: 'semua'] }})</span>
                    </a>
                @endforeach
            </div>

            {{-- DAFTAR PELATIHAN --}}
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
                    $ikonStatus = match(true) {
                        $p->status === 'selesai'    => 'bi-check-circle-fill',
                        $p->status === 'dibatalkan' => 'bi-x-circle-fill',
                        $p->sedangBerjalan()        => 'bi-play-circle-fill',
                        $p->akanDatang()            => 'bi-clock-fill',
                        default                     => 'bi-circle-fill',
                    };
                    $labelStatus = match(true) {
                        $p->status === 'selesai'    => 'Selesai',
                        $p->status === 'dibatalkan' => 'Dibatalkan',
                        $p->sedangBerjalan()        => 'Sedang Berjalan',
                        $p->akanDatang()            => 'Akan Datang',
                        default                     => 'Aktif',
                    };
                    $warnaKategori = match($p->kategori) {
                        'sertifikasi'         => 'bg-violet-50 text-violet-700 ring-violet-200',
                        'pelatihan_eksternal' => 'bg-amber-50 text-amber-700 ring-amber-200',
                        default               => 'bg-sky-50 text-sky-700 ring-sky-200',
                    };
                    $ikonKategori = match($p->kategori) {
                        'sertifikasi'         => 'bi-patch-check-fill',
                        'pelatihan_eksternal' => 'bi-globe',
                        default               => 'bi-building',
                    };
                    $labelKategori = match($p->kategori) {
                        'sertifikasi'         => 'Sertifikasi',
                        'pelatihan_eksternal' => 'Eksternal',
                        default               => 'Internal',
                    };

                    $bisaKelolaPeserta   = $p->status === 'aktif';
                    $bisaInputNilai      = $p->status === 'selesai';
                    $bisaDihapus         = $p->status === 'dibatalkan';
                    $bisaDibatalkan      = $p->status === 'aktif';
                    $jumlahLulus         = $p->sertifikasi->where('status', 'lulus')->count();

                    $aksenWarna = match(true) {
                        $p->status === 'selesai'    => 'bg-gray-300',
                        $p->status === 'dibatalkan' => 'bg-rose-400',
                        $p->sedangBerjalan()        => 'bg-emerald-400',
                        default                     => 'bg-blue-400',
                    };

                    $sertifPertama = $p->sertifikasi->first();

                    $pesertaData = $p->peserta->map(function ($ps) use ($p) {
                        $s = $p->sertifikasi->firstWhere('karyawan_id', $ps->karyawan_id);
                        return [
                            'karyawan_id'   => $ps->karyawan_id,
                            'nama'          => $ps->karyawan->nama_lengkap ?? '-',
                            'jabatan'       => $ps->karyawan->jabatan->nama_jabatan ?? '-',
                            'departemen'    => $ps->karyawan->departemen->nama_departemen ?? '-',
                            'nilai_pretest' => $s->nilai_pretest ?? '',
                            'nilai_postest' => $s->nilai_postest ?? '',
                        ];
                    })->values()->toArray();

                    $modalData = [
                        'pelatihan_id'          => $p->id,
                        'nama_pelatihan'        => $p->nama_pelatihan,
                        'nomor_sertifikat'      => $sertifPertama->nomor_sertifikat ?? '',
                        'tanggal_surat'         => $sertifPertama?->tanggal_surat?->toDateString() ?? '',
                        'ditandatangani_oleh'   => $sertifPertama->ditandatangani_oleh ?? '',
                        'jabatan_penandatangan' => $sertifPertama->jabatan_penandatangan ?? '',
                        'peserta'               => $pesertaData,
                    ];
                @endphp

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow"
                     x-data="{ bukaDetail: false }">

                    <div class="flex">
                        <div class="w-1.5 {{ $aksenWarna }} shrink-0"></div>

                        <div class="p-5 grow">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-2">
                                        <span class="text-[10px] font-bold px-2 py-1 rounded-full ring-1 {{ $warnaKategori }} inline-flex items-center gap-1">
                                            <i class="bi {{ $ikonKategori }}"></i> {{ $labelKategori }}
                                        </span>
                                        <span class="text-[10px] font-bold px-2 py-1 rounded-full {{ $warnaBadge }} inline-flex items-center gap-1">
                                            <i class="bi {{ $ikonStatus }}"></i> {{ $labelStatus }}
                                        </span>
                                    </div>

                                    <h3 class="font-bold text-gray-900 text-base leading-snug">{{ $p->nama_pelatihan }}</h3>

                                    <div class="text-sm text-gray-500 mt-2 flex flex-wrap gap-x-4 gap-y-1.5">
                                        @if($p->penyelenggara)
                                            <span class="inline-flex items-center gap-1.5">
                                                <i class="bi bi-building text-gray-400"></i>{{ $p->penyelenggara }}
                                            </span>
                                        @endif
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="bi bi-calendar3 text-gray-400"></i>
                                            {{ $p->tanggal_mulai->translatedFormat('d M Y') }}
                                            @if($p->tanggal_selesai && !$p->tanggal_mulai->isSameDay($p->tanggal_selesai))
                                                <span class="text-gray-300">–</span>
                                                {{ $p->tanggal_selesai->translatedFormat('d M Y') }}
                                            @endif
                                        </span>
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="bi bi-people-fill text-gray-400"></i>
                                            {{ $p->peserta->count() }} peserta
                                        </span>
                                        @if($p->status === 'selesai' && $p->sertifikasi->count() > 0)
                                            <span class="inline-flex items-center gap-1.5 text-emerald-600 font-semibold">
                                                <i class="bi bi-trophy-fill"></i> {{ $jumlahLulus }} lulus
                                            </span>
                                        @endif
                                    </div>

                                    @if($sertifPertama && $sertifPertama->nomor_sertifikat)
                                        <div class="text-[11px] text-gray-500 mt-2 font-mono">
                                            <i class="bi bi-envelope-paper"></i> No: {{ $sertifPertama->nomor_sertifikat }}
                                            @if($sertifPertama->tanggal_surat)
                                                &bull; {{ $sertifPertama->tanggal_surat->translatedFormat('d M Y') }}
                                            @endif
                                        </div>
                                    @endif

                                    @if($p->deskripsi)
                                        <p class="text-xs text-gray-400 mt-2 italic line-clamp-2">{{ $p->deskripsi }}</p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0 flex-wrap">
                                    <button @click="bukaDetail = !bukaDetail"
                                        class="text-xs font-bold px-3 py-1.5 rounded-lg transition-colors inline-flex items-center gap-1.5
                                        {{ $bisaKelolaPeserta
                                            ? 'text-blue-600 bg-blue-50 hover:bg-blue-100'
                                            : 'text-gray-600 bg-gray-100 hover:bg-gray-200' }}">
                                        <i class="bi" :class="bukaDetail ? 'bi-chevron-up' : '{{ $bisaKelolaPeserta ? 'bi-people' : 'bi-eye' }}'"></i>
                                        <span x-text="bukaDetail ? 'Tutup' : '{{ $bisaKelolaPeserta ? 'Kelola Peserta' : 'Lihat Peserta' }}'"></span>
                                    </button>

                                    @if($bisaInputNilai && $p->peserta->count() > 0)
                                        <button type="button"
                                            data-modal='@json($modalData)'
                                            onclick="bukaModalNilai(JSON.parse(this.dataset.modal))"
                                            class="text-xs font-bold px-3 py-1.5 rounded-lg transition-colors inline-flex items-center gap-1.5 bg-violet-600 text-white hover:bg-violet-700 shadow-sm">
                                            <i class="bi bi-clipboard-data-fill"></i> Input Nilai Peserta
                                        </button>
                                    @endif

                                    @if($jumlahLulus > 0)
                                        <a href="{{ route('pelatihan.cetak-semua', $p->id) }}"
                                           target="_blank"
                                           class="text-xs font-bold px-3 py-1.5 rounded-lg transition-colors inline-flex items-center gap-1.5 bg-amber-500 text-white hover:bg-amber-600 shadow-sm">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                            Cetak {{ $jumlahLulus }} Sertifikat
                                        </a>
                                    @endif

                                    <button onclick="bukaModalEdit({{ $p->id }}, {{ json_encode($p) }})"
                                        class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="Edit">
                                        <i class="bi bi-pencil-fill text-sm"></i>
                                    </button>

                                    @if($bisaDibatalkan)
                                        <button
                                            onclick="bukaKonfirmasi({
                                                judul: 'Batalkan Pelatihan?',
                                                pesan: 'Pelatihan <strong>{{ addslashes($p->nama_pelatihan) }}</strong> akan dibatalkan.',
                                                labelOk: 'Ya, Batalkan',
                                                warnaOk: 'bg-amber-500 hover:bg-amber-600',
                                                onOk: function() { submitForm('form-batalkan-{{ $p->id }}') }
                                            })"
                                            class="p-2 text-amber-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                            title="Batalkan pelatihan">
                                            <i class="bi bi-x-circle-fill text-sm"></i>
                                        </button>
                                        <form id="form-batalkan-{{ $p->id }}" method="POST"
                                            action="{{ route('pelatihan.update', $p->id) }}" class="hidden">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="nama_pelatihan"  value="{{ $p->nama_pelatihan }}">
                                            <input type="hidden" name="kategori"        value="{{ $p->kategori }}">
                                            <input type="hidden" name="tanggal_mulai"   value="{{ $p->tanggal_mulai->toDateString() }}">
                                            <input type="hidden" name="tanggal_selesai" value="{{ $p->tanggal_selesai?->toDateString() }}">
                                            <input type="hidden" name="penyelenggara"   value="{{ $p->penyelenggara }}">
                                            <input type="hidden" name="deskripsi"       value="{{ $p->deskripsi }}">
                                            <input type="hidden" name="status"          value="dibatalkan">
                                        </form>
                                    @elseif($bisaDihapus)
                                        <button
                                            onclick="bukaKonfirmasi({
                                                judul: 'Hapus Pelatihan?',
                                                pesan: 'Pelatihan <strong>{{ addslashes($p->nama_pelatihan) }}</strong> beserta seluruh data pesertanya akan dihapus permanen.',
                                                labelOk: 'Ya, Hapus',
                                                warnaOk: 'bg-rose-600 hover:bg-rose-700',
                                                onOk: function() { submitForm('form-hapus-{{ $p->id }}') }
                                            })"
                                            class="p-2 text-rose-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                            title="Hapus permanen">
                                            <i class="bi bi-trash-fill text-sm"></i>
                                        </button>
                                        <form id="form-hapus-{{ $p->id }}" method="POST"
                                            action="{{ route('pelatihan.destroy', $p->id) }}" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PANEL PESERTA --}}
                    <div x-show="bukaDetail" x-collapse class="border-t border-gray-100">
                        <div class="p-5 bg-gray-50">

                            @if(!$bisaKelolaPeserta)
                                <div class="mb-4 flex items-start gap-2 {{ $bisaInputNilai ? 'bg-violet-50 border-violet-200 text-violet-800' : 'bg-amber-50 border-amber-200 text-amber-800' }} border px-3 py-2.5 rounded-lg text-xs">
                                    <i class="bi {{ $bisaInputNilai ? 'bi-clipboard-data-fill' : 'bi-info-circle-fill' }} mt-0.5 shrink-0"></i>
                                    <div>
                                        @if($bisaInputNilai)
                                            <span class="font-bold">Input Nilai di level Pelatihan.</span>
                                            Klik tombol <strong>"Input Nilai Peserta"</strong> di atas untuk isi semua nilai sekaligus.
                                        @else
                                            <span class="font-bold">Mode Lihat Saja.</span>
                                            Pelatihan sudah <span class="font-bold">{{ strtolower($labelStatus) }}</span>.
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- FORM TAMBAH PESERTA --}}
                            @if($bisaKelolaPeserta)
                                <form method="POST" action="{{ route('pelatihan.peserta.tambah', $p->id) }}" class="mb-5">
                                    @csrf
                                    <div class="text-xs font-bold text-gray-600 mb-2 flex items-center gap-1.5">
                                        <i class="bi bi-person-plus-fill text-blue-500"></i> Tunjuk Peserta Baru
                                    </div>
                                    <div class="relative mb-2">
                                        <i class="bi bi-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                                        <input type="text" placeholder="Cari nama, departemen, atau jabatan..."
                                            class="w-full pl-8 pr-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none"
                                            oninput="filterPeserta(this, 'list-karyawan-{{ $p->id }}')">
                                    </div>
                                    <div id="list-karyawan-{{ $p->id }}"
                                        class="bg-white border border-gray-200 rounded-lg p-2 max-h-48 overflow-y-auto mb-2">
                                        @php $sudahTerdaftar = $p->peserta->pluck('karyawan_id')->toArray(); @endphp
                                        @foreach(\App\Models\Karyawan::with(['departemen','jabatan'])->where('status','aktif')->orderBy('nama_lengkap')->get() as $k)
                                        <label class="peserta-item flex items-center gap-2 py-1.5 px-2 rounded cursor-pointer hover:bg-blue-50 transition-colors"
                                            data-cari="{{ strtolower($k->nama_lengkap . ' ' . ($k->departemen->nama_departemen ?? '') . ' ' . ($k->jabatan->nama_jabatan ?? '')) }}">
                                            <input type="checkbox" name="karyawan_ids[]" value="{{ $k->id }}"
                                                class="rounded accent-blue-600 shrink-0"
                                                {{ in_array($k->id, $sudahTerdaftar) ? 'disabled checked' : '' }}>
                                            <span class="text-xs leading-tight">
                                                <span class="{{ in_array($k->id, $sudahTerdaftar) ? 'text-gray-400' : 'text-gray-800 font-medium' }}">
                                                    {{ $k->nama_lengkap }}
                                                </span>
                                                <span class="text-gray-400"> — {{ $k->departemen->nama_departemen ?? '-' }}</span>
                                                @if(in_array($k->id, $sudahTerdaftar))
                                                    <span class="text-emerald-600 font-bold text-[10px]"> ✓ terdaftar</span>
                                                @endif
                                            </span>
                                        </label>
                                        @endforeach
                                        <div class="peserta-kosong hidden text-xs text-gray-400 text-center py-3">
                                            Tidak ada karyawan yang cocok.
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <input type="text" name="catatan" placeholder="Catatan untuk peserta (opsional)"
                                            class="flex-1 text-xs border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-blue-500 outline-none">
                                        <button type="submit"
                                            class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-700 shrink-0">
                                            <i class="bi bi-plus-lg mr-1"></i>Tambah
                                        </button>
                                    </div>
                                </form>
                            @endif

                            {{-- DAFTAR PESERTA + EXPORT --}}
                            <div class="flex items-center justify-between mb-2 flex-wrap gap-2">
                                <div class="text-xs font-bold text-gray-600 flex items-center gap-1.5">
                                    <i class="bi bi-people-fill text-gray-500"></i> Peserta Terdaftar
                                    <span class="bg-gray-200 text-gray-700 px-1.5 py-0.5 rounded-full text-[10px]">{{ $p->peserta->count() }}</span>
                                </div>

                                @if($p->peserta->count() > 0)
                                    <div x-data="{ bukaExport: false }" class="relative">
                                        <button type="button" @click="bukaExport = !bukaExport" @click.away="bukaExport = false"
                                            class="text-[10px] font-bold px-3 py-1.5 rounded-lg transition-colors inline-flex items-center gap-1.5 bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm">
                                            <i class="bi bi-download"></i> Export Peserta
                                            <i class="bi bi-chevron-down text-[8px]"></i>
                                        </button>
                                        <div x-show="bukaExport" x-cloak x-transition
                                            class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden z-30">
                                            <a href="{{ route('pelatihan.export-peserta-excel', $p->id) }}"
                                                class="flex items-center gap-2.5 px-3 py-2.5 text-xs text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                                <i class="bi bi-file-earmark-excel-fill text-emerald-600 text-base"></i>
                                                <div>
                                                    <div class="font-bold">Excel (.xls)</div>
                                                    <div class="text-[9px] text-gray-400">Tabel peserta</div>
                                                </div>
                                            </a>
                                            <a href="{{ route('pelatihan.export-peserta-pdf', $p->id) }}"
                                                class="flex items-center gap-2.5 px-3 py-2.5 text-xs text-gray-700 hover:bg-rose-50 hover:text-rose-700 transition-colors border-t border-gray-100">
                                                <i class="bi bi-file-earmark-pdf-fill text-rose-600 text-base"></i>
                                                <div>
                                                    <div class="font-bold">PDF (.pdf)</div>
                                                    <div class="text-[9px] text-gray-400">Layout landscape</div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @forelse($p->peserta as $ps)
                                @php
                                    $sertif = $p->sertifikasi->firstWhere('karyawan_id', $ps->karyawan_id);
                                    $warnaSertif = match($sertif->status ?? null) {
                                        'lulus'       => 'bg-emerald-100 text-emerald-700',
                                        'tidak_lulus' => 'bg-rose-100 text-rose-700',
                                        default       => null,
                                    };
                                    $labelSertif = match($sertif->status ?? null) {
                                        'lulus'       => 'Lulus',
                                        'tidak_lulus' => 'Tidak Lulus',
                                        default       => null,
                                    };
                                @endphp

                                <div class="flex items-center justify-between bg-white rounded-lg px-3 py-2.5 mb-1.5 border border-gray-100 hover:border-gray-200 transition-colors">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center text-[11px] font-bold shrink-0">
                                            {{ strtoupper(substr($ps->karyawan->nama_lengkap ?? '?', 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-xs font-semibold text-gray-800 truncate flex items-center gap-1.5 flex-wrap">
                                                {{ $ps->karyawan->nama_lengkap ?? '-' }}
                                                @if($sertif && $warnaSertif)
                                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded {{ $warnaSertif }}">
                                                        <i class="bi bi-patch-check-fill"></i> {{ $labelSertif }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-[10px] text-gray-400 truncate">
                                                <i class="bi bi-briefcase"></i> {{ $ps->karyawan->jabatan->nama_jabatan ?? '-' }}
                                                &bull;
                                                <i class="bi bi-diagram-3"></i> {{ $ps->karyawan->departemen->nama_departemen ?? '-' }}
                                            </div>
                                            @if($sertif && $sertif->nilai_postest !== null)
                                                @php
                                                    $pre     = $sertif->nilai_pretest;
                                                    $post    = $sertif->nilai_postest;
                                                    $selisih = ($pre !== null) ? $post - $pre : null;
                                                @endphp
                                                <div class="text-[10px] text-gray-600 mt-1 flex items-center gap-1.5 flex-wrap">
                                                    @if($pre !== null)
                                                        <span class="inline-flex items-center gap-1">
                                                            <i class="bi bi-pencil-square text-gray-400"></i>
                                                            Pre: <strong>{{ number_format($pre, 2) }}</strong>
                                                        </span>
                                                        <i class="bi bi-arrow-right text-gray-300"></i>
                                                    @endif
                                                    <span class="inline-flex items-center gap-1">
                                                        <i class="bi bi-trophy-fill text-amber-500"></i>
                                                        Post: <strong class="text-gray-800">{{ number_format($post, 2) }}</strong>
                                                    </span>
                                                    @if($selisih !== null && $selisih != 0)
                                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded
                                                            {{ $selisih > 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                                            {{ $selisih > 0 ? '+' : '' }}{{ number_format($selisih, 2) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if($bisaKelolaPeserta)
                                        <button
                                            onclick="bukaKonfirmasi({
                                                judul: 'Hapus Peserta?',
                                                pesan: '<strong>{{ addslashes($ps->karyawan->nama_lengkap ?? '') }}</strong> akan dihapus dari daftar peserta pelatihan ini.',
                                                labelOk: 'Ya, Hapus',
                                                warnaOk: 'bg-rose-600 hover:bg-rose-700',
                                                onOk: function() { submitForm('form-hapus-peserta-{{ $ps->id }}') }
                                            })"
                                            class="text-gray-300 hover:text-rose-500 transition-colors p-1 shrink-0"
                                            title="Hapus peserta">
                                            <i class="bi bi-x-circle-fill text-base"></i>
                                        </button>
                                        <form id="form-hapus-peserta-{{ $ps->id }}" method="POST"
                                            action="{{ route('pelatihan.peserta.hapus', [$p->id, $ps->karyawan_id]) }}"
                                            class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <div class="text-xs text-gray-400 text-center py-6 bg-white rounded-lg border border-dashed border-gray-200">
                                    <i class="bi bi-person-dash text-2xl block mb-1 text-gray-300"></i>
                                    Belum ada peserta ditunjuk.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @empty
                    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="bi bi-mortarboard text-3xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-600 font-semibold">
                            @if(request('q') || request('status'))
                                Tidak ada pelatihan yang cocok dengan filter
                            @else
                                Belum ada pelatihan tercatat
                            @endif
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            @if(request('q') || request('status'))
                                Coba ubah kata kunci atau hapus filter
                            @else
                                Klik "Tambah Pelatihan" untuk memulai
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">{{ $pelatihans->links() }}</div>
        </div>
    </main>
</div>

{{-- MODAL KONFIRMASI --}}
<div id="modal-konfirmasi" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
    style="background: rgba(15,23,42,0.5); backdrop-filter: blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
        <div class="flex items-start gap-3 mb-4">
            <div id="konfirmasi-ikon-box" class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-white text-lg">
                <i id="konfirmasi-ikon" class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div>
                <h3 id="konfirmasi-judul" class="font-bold text-gray-900 text-base"></h3>
                <p id="konfirmasi-pesan" class="text-sm text-gray-500 mt-1 leading-relaxed"></p>
            </div>
        </div>
        <div class="flex gap-2 justify-end">
            <button onclick="tutupKonfirmasi()"
                class="px-4 py-2 text-sm font-bold bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">Batal</button>
            <button id="konfirmasi-ok" class="px-4 py-2 text-sm font-bold text-white rounded-xl">Konfirmasi</button>
        </div>
    </div>
</div>

<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

{{-- MODAL TAMBAH PELATIHAN --}}
<div id="modalTambah" class="hidden fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="font-bold text-gray-800 text-lg mb-4">Tambah Pelatihan Baru</h3>
        <form method="POST" action="{{ route('pelatihan.store') }}">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Nama Pelatihan *</label>
                    <input type="text" name="nama_pelatihan"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Penyelenggara</label>
                        <input type="text" name="penyelenggara"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Kategori *</label>
                        <select name="kategori"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none" required>
                            <option value="pelatihan_internal">Internal</option>
                            <option value="pelatihan_eksternal">Eksternal</option>
                            <option value="sertifikasi">Sertifikasi</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Tanggal Mulai *</label>
                        <input type="date" name="tanggal_mulai"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                </div>
            </div>
            <div class="flex gap-2 justify-end mt-5">
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="px-4 py-2 text-sm font-bold bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit"
                    class="px-4 py-2 text-sm font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT PELATIHAN --}}
<div id="modalEdit" class="hidden fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="font-bold text-gray-800 text-lg mb-4">Edit Pelatihan</h3>
        <form method="POST" id="formEdit">
            @csrf @method('PUT')
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Nama Pelatihan *</label>
                    <input type="text" name="nama_pelatihan" id="editNama"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Penyelenggara</label>
                        <input type="text" name="penyelenggara" id="editPenyelenggara"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Kategori *</label>
                        <select name="kategori" id="editKategori"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none" required>
                            <option value="pelatihan_internal">Internal</option>
                            <option value="pelatihan_eksternal">Eksternal</option>
                            <option value="sertifikasi">Sertifikasi</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Tanggal Mulai *</label>
                        <input type="date" name="tanggal_mulai" id="editTglMulai"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="editTglSelesai"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Status</label>
                    <select name="status" id="editStatus"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="aktif">Aktif</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1">
                        <i class="bi bi-info-circle"></i> Ubah ke <strong>Selesai</strong> untuk mengaktifkan fitur input nilai.
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="editDeskripsi" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                </div>
            </div>
            <div class="flex gap-2 justify-end mt-5">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                    class="px-4 py-2 text-sm font-bold bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit"
                    class="px-4 py-2 text-sm font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL INPUT NILAI BULK --}}
<div id="modalNilai" class="hidden fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[92vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center shrink-0">
                <i class="bi bi-clipboard-data-fill text-lg"></i>
            </div>
            <div class="min-w-0">
                <h3 class="font-bold text-gray-800 text-lg">Input Nilai Peserta</h3>
                <p class="text-xs text-gray-500 mt-0.5" id="nilaiSubtitle">-</p>
            </div>
        </div>

        <form method="POST" id="formNilai" class="flex-1 flex flex-col min-h-0">
            @csrf
            <div class="flex-1 overflow-y-auto p-6 space-y-5">

                <div class="bg-violet-50 border border-violet-200 rounded-xl p-4">
                    <div class="text-xs font-bold text-violet-800 mb-3 flex items-center gap-1.5">
                        <i class="bi bi-envelope-paper-fill"></i> Data Surat & Penandatangan
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">Nomor Surat</label>
                            <input type="text" name="nomor_sertifikat" id="inputNomorSurat"
                                placeholder="Contoh: 001/SERT-HRD/INDOKOM/X/2026"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-violet-500 outline-none bg-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">Tanggal Surat</label>
                            <input type="date" name="tanggal_surat" id="inputTanggalSurat"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500 outline-none bg-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">Ditandatangani Oleh</label>
                            <input type="text" name="ditandatangani_oleh" id="inputPenandatangan"
                                placeholder="Contoh: Abdul"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500 outline-none bg-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">Jabatan</label>
                            <input type="text" name="jabatan_penandatangan" id="inputJabatan"
                                placeholder="Contoh: Plant Manager"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500 outline-none bg-white">
                        </div>
                    </div>
                </div>

                <div>
                    <div class="text-xs font-bold text-gray-700 mb-2 flex items-center gap-1.5">
                        <i class="bi bi-people-fill text-violet-600"></i> Nilai Peserta
                        <span class="text-[10px] font-normal text-gray-500">(postest ≥ 8 = LULUS)</span>
                    </div>

                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="text-left px-3 py-2 font-bold text-gray-600 w-8">#</th>
                                    <th class="text-left px-3 py-2 font-bold text-gray-600">Peserta</th>
                                    <th class="text-center px-3 py-2 font-bold text-gray-600 w-24">Pretest</th>
                                    <th class="text-center px-3 py-2 font-bold text-gray-600 w-24">Postest</th>
                                    <th class="text-center px-3 py-2 font-bold text-gray-600 w-28">Status</th>
                                </tr>
                            </thead>
                            <tbody id="tabelPeserta" class="divide-y divide-gray-100"></tbody>
                        </table>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2">
                        <i class="bi bi-info-circle"></i> Kosongkan postest jika peserta belum dinilai.
                    </p>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalNilai').classList.add('hidden')"
                    class="px-4 py-2 text-sm font-bold bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit"
                    class="px-5 py-2 text-sm font-bold bg-violet-600 text-white rounded-lg hover:bg-violet-700 shadow-sm">
                    <i class="bi bi-save mr-1"></i>Simpan Semua Nilai
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function filterPeserta(input, listId) {
        const q      = input.value.trim().toLowerCase();
        const list   = document.getElementById(listId);
        if (!list) return;
        const items  = list.querySelectorAll('.peserta-item');
        const kosong = list.querySelector('.peserta-kosong');
        let ada = 0;

        items.forEach(item => {
            const cocok = !q || item.dataset.cari.includes(q);
            item.style.display = cocok ? '' : 'none';
            if (cocok) ada++;
        });

        if (kosong) kosong.classList.toggle('hidden', ada > 0);
    }

    let _onOkCallback = null;

    function bukaKonfirmasi({ judul, pesan, labelOk, warnaOk, onOk, ikon = 'bi-exclamation-triangle-fill', warnaIkon = 'bg-amber-500' }) {
        document.getElementById('konfirmasi-judul').innerHTML    = judul;
        document.getElementById('konfirmasi-pesan').innerHTML    = pesan;
        document.getElementById('konfirmasi-ikon').className     = 'bi ' + ikon;
        document.getElementById('konfirmasi-ikon-box').className = `w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-white text-lg ${warnaIkon}`;

        const btnOk = document.getElementById('konfirmasi-ok');
        btnOk.textContent = labelOk;
        btnOk.className   = `px-4 py-2 text-sm font-bold text-white rounded-xl transition-colors ${warnaOk}`;

        _onOkCallback = onOk;
        document.getElementById('modal-konfirmasi').classList.remove('hidden');
    }

    document.getElementById('konfirmasi-ok').addEventListener('click', function () {
        const callback = _onOkCallback;
        tutupKonfirmasi();
        if (typeof callback === 'function') callback();
    });

    function tutupKonfirmasi() {
        document.getElementById('modal-konfirmasi').classList.add('hidden');
        _onOkCallback = null;
    }

    document.getElementById('modal-konfirmasi').addEventListener('click', function(e) {
        if (e.target === this) tutupKonfirmasi();
    });

    function submitForm(id) {
        const form = document.getElementById(id);
        if (form) form.submit();
    }

    function tampilkanToast(pesan, tipe = 'success') {
        const container = document.getElementById('toast-container');
        const warna = tipe === 'success' ? 'bg-emerald-600'
                    : tipe === 'error'   ? 'bg-rose-600'
                    : 'bg-amber-500';
        const ikon = tipe === 'success' ? 'bi-check-circle-fill'
                   : tipe === 'error'   ? 'bi-x-circle-fill'
                   : 'bi-exclamation-triangle-fill';

        const el = document.createElement('div');
        el.className = `pointer-events-auto flex items-center gap-3 ${warna} text-white px-4 py-3 rounded-xl shadow-lg text-sm font-medium max-w-sm transform translate-x-full transition-transform duration-300`;
        el.innerHTML = `<i class="bi ${ikon} shrink-0"></i><span>${pesan}</span>`;

        container.appendChild(el);
        requestAnimationFrame(() => el.classList.remove('translate-x-full'));

        setTimeout(() => {
            el.classList.add('translate-x-full');
            setTimeout(() => el.remove(), 300);
        }, 3500);
    }

    function bukaModalEdit(id, data) {
        document.getElementById('formEdit').action         = `/hrd/pelatihan/${id}`;
        document.getElementById('editNama').value          = data.nama_pelatihan;
        document.getElementById('editPenyelenggara').value = data.penyelenggara ?? '';
        document.getElementById('editKategori').value      = data.kategori;
        document.getElementById('editTglMulai').value      = data.tanggal_mulai;
        document.getElementById('editTglSelesai').value    = data.tanggal_selesai ?? '';
        document.getElementById('editStatus').value        = data.status;
        document.getElementById('editDeskripsi').value     = data.deskripsi ?? '';
        document.getElementById('modalEdit').classList.remove('hidden');
    }

    function bukaModalNilai(data) {
        document.getElementById('formNilai').action = `/hrd/pelatihan/${data.pelatihan_id}/nilai`;
        document.getElementById('nilaiSubtitle').innerHTML = `<strong>${data.nama_pelatihan}</strong>`;

        document.getElementById('inputNomorSurat').value    = data.nomor_sertifikat || '';
        document.getElementById('inputTanggalSurat').value  = data.tanggal_surat || '';
        document.getElementById('inputPenandatangan').value = data.ditandatangani_oleh || '';
        document.getElementById('inputJabatan').value       = data.jabatan_penandatangan || '';

        const tbody = document.getElementById('tabelPeserta');
        tbody.innerHTML = '';

        if (!data.peserta || data.peserta.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-6 text-gray-400">
                <i class="bi bi-person-dash text-2xl block mb-1"></i>Belum ada peserta
            </td></tr>`;
        } else {
            data.peserta.forEach((p, i) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50';
                tr.innerHTML = `
                    <td class="px-3 py-2 text-gray-400">${i + 1}</td>
                    <td class="px-3 py-2">
                        <div class="font-semibold text-gray-800">${p.nama}</div>
                        <div class="text-[10px] text-gray-400">${p.jabatan} • ${p.departemen}</div>
                        <input type="hidden" name="karyawan_ids[]" value="${p.karyawan_id}">
                    </td>
                    <td class="px-3 py-2">
                        <input type="number" step="0.01" min="0" max="10"
                            name="nilai_pretest[${p.karyawan_id}]"
                            value="${p.nilai_pretest ?? ''}"
                            placeholder="0-10"
                            class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-center text-xs focus:ring-2 focus:ring-violet-500 outline-none">
                    </td>
                    <td class="px-3 py-2">
                        <input type="number" step="0.01" min="0" max="10"
                            name="nilai_postest[${p.karyawan_id}]"
                            value="${p.nilai_postest ?? ''}"
                            placeholder="0-10"
                            oninput="updateStatusBaris(this)"
                            data-status-target="status-${p.karyawan_id}"
                            class="w-full border-2 border-violet-300 rounded-lg px-2 py-1.5 text-center text-sm font-bold focus:ring-2 focus:ring-violet-500 outline-none">
                    </td>
                    <td class="px-3 py-2 text-center" id="status-${p.karyawan_id}"></td>
                `;
                tbody.appendChild(tr);

                const inputPost = tr.querySelector('input[name^="nilai_postest"]');
                updateStatusBaris(inputPost);
            });
        }

        document.getElementById('modalNilai').classList.remove('hidden');
    }

    function updateStatusBaris(input) {
        const targetId = input.dataset.statusTarget;
        const el       = document.getElementById(targetId);
        if (!el) return;

        const v = parseFloat(input.value);
        if (isNaN(v)) {
            el.innerHTML = '<span class="text-gray-300 text-[10px]">—</span>';
            return;
        }

        if (v >= 8) {
            el.innerHTML = '<span class="bg-emerald-100 text-emerald-700 font-bold text-[10px] px-2 py-1 rounded-full"><i class="bi bi-check-circle-fill"></i> LULUS</span>';
        } else {
            el.innerHTML = '<span class="bg-rose-100 text-rose-700 font-bold text-[10px] px-2 py-1 rounded-full"><i class="bi bi-x-circle-fill"></i> TIDAK</span>';
        }
    }

    @if(session('success'))
        tampilkanToast("{{ session('success') }}", 'success');
    @endif
    @if(session('error'))
        tampilkanToast("{{ session('error') }}", 'error');
    @endif
</script>
</body>
</html>