<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pelatihan & Sertifikasi - HRD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important;}</style>
</head>
<body class="bg-gray-50 text-gray-800">
<div class="flex min-h-screen">

    @include('partials.sidebar-hrd')

    <main class="grow flex flex-col min-w-0">
        {{-- HEADER --}}
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-20 shadow-sm">
            <div class="flex items-center">
                <a href="{{ route('dashboard.hrd') }}" class="text-gray-500 hover:bg-gray-100 p-2 rounded-lg mr-3 transition-colors">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="font-bold text-xl text-gray-800">Pelatihan & Sertifikasi</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Kelola jadwal pelatihan dan tunjuk peserta</p>
                </div>
            </div>
            <button onclick="document.getElementById('modalTambahPelatihan').classList.remove('hidden')"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 shadow-sm transition-colors">
                <i class="bi bi-plus-lg mr-1"></i> Tambah Pelatihan
            </button>
        </nav>

        <div class="p-6 grow overflow-y-auto max-w-6xl mx-auto w-full">

            {{-- SUMMARY CARDS --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="bi bi-collection-fill"></i>
                    </div>
                    <div>
                        <div class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Total</div>
                        <div class="text-2xl font-black text-gray-800 leading-none">{{ $stats['semua'] }}</div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="bi bi-play-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Aktif</div>
                        <div class="text-2xl font-black text-gray-800 leading-none">{{ $stats['aktif'] }}</div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Selesai</div>
                        <div class="text-2xl font-black text-gray-800 leading-none">{{ $stats['selesai'] }}</div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Dibatalkan</div>
                        <div class="text-2xl font-black text-gray-800 leading-none">{{ $stats['dibatalkan'] }}</div>
                    </div>
                </div>
            </div>

            {{-- SEARCH & FILTER --}}
            <div class="flex flex-col md:flex-row gap-3 mb-6 items-start md:items-center justify-between">
                <div class="flex gap-2 flex-wrap">
                    @foreach([''=>'Semua', 'aktif'=>'Aktif', 'selesai'=>'Selesai', 'dibatalkan'=>'Dibatalkan'] as $val => $label)
                        <a href="{{ route('pelatihan.index', ['status' => $val, 'q' => request('q')]) }}"
                            class="px-4 py-2 rounded-xl text-xs font-bold border transition-all
                            {{ request('status', '') === $val
                                ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                                : 'bg-white text-gray-600 border-gray-300 hover:border-blue-400 hover:text-blue-600' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('pelatihan.index') }}" class="flex gap-2 w-full md:w-72">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="relative w-full">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari pelatihan..."
                            class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </form>
            </div>

            {{-- DAFTAR PELATIHAN --}}
            <div class="space-y-4">
                @forelse($pelatihans as $p)
                @php
                    // ⬇️ LOGIKA BARU: Cek apakah tanggal sudah lewat tapi status masih aktif ⬇️
                    $isExpired = ($p->status === 'aktif') && \Carbon\Carbon::parse($p->tanggal_selesai ?? $p->tanggal_mulai)->endOfDay()->isPast();

                    // Logika badge & warna
                    $warnaBadge = match(true) {
                        $p->status === 'selesai'    => 'bg-gray-100 text-gray-600 border-gray-200',
                        $p->status === 'dibatalkan' => 'bg-rose-50 text-rose-700 border-rose-200',
                        $isExpired                  => 'bg-amber-50 text-amber-700 border-amber-200',
                        $p->sedangBerjalan()        => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        default                     => 'bg-blue-50 text-blue-700 border-blue-200',
                    };
                    $ikonStatus = match(true) {
                        $p->status === 'selesai'    => 'bi-check-circle-fill',
                        $p->status === 'dibatalkan' => 'bi-x-circle-fill',
                        $isExpired                  => 'bi-hourglass-bottom',
                        $p->sedangBerjalan()        => 'bi-play-circle-fill',
                        default                     => 'bi-clock-fill',
                    };
                    $labelStatus = match(true) {
                        $p->status === 'selesai'    => 'Selesai',
                        $p->status === 'dibatalkan' => 'Dibatalkan',
                        $isExpired                  => 'Waktu Habis',
                        $p->sedangBerjalan()        => 'Sedang Berjalan',
                        default                     => 'Akan Datang',
                    };
                    $labelKategori = match($p->kategori) {
                        'sertifikasi'         => 'Sertifikasi',
                        'pelatihan_eksternal' => 'Eksternal',
                        default               => 'Internal',
                    };

                    $bisaKelolaPeserta   = in_array($p->status, ['aktif', 'selesai']);
                    $bisaInputNilai      = $p->status === 'selesai';
                    $bisaDihapus         = $p->status === 'dibatalkan';
                    $bisaDibatalkan      = $p->status === 'aktif';
                    $jumlahLulus         = $p->sertifikasi->where('status', 'lulus')->count();

                    // Data untuk modal input nilai
                    $sertifPertama = $p->sertifikasi->first();
                    $pesertaData = $p->peserta->map(function ($ps) use ($p) {
                        $s = $p->sertifikasi->firstWhere('karyawan_id', $ps->karyawan_id);
                        return [
                            'karyawan_id'   => $ps->karyawan_id,
                            'nama'          => $ps->karyawan->nama_lengkap ?? '-',
                            'jabatan'       => $ps->karyawan->jabatan->nama_jabatan ?? '-',
                            'departemen'    => $ps->karyawan->departemen->nama_departemen ?? '-',
                            'status_kehadiran' => $ps->status_kehadiran, // ⬅️ Tambahan untuk validasi input
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

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow" 
                     x-data="{ bukaDetail: false, bukaMenu: false }">
                    
                    {{-- CARD HEADER --}}
                    <div class="p-5 flex items-start gap-4">
                        {{-- Ikon Kiri --}}
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 border {{ $warnaBadge }}">
                            <i class="bi {{ $ikonStatus }} text-xl"></i>
                        </div>

                        {{-- Info Tengah --}}
                        <div class="flex-1 min-w-0 pt-0.5">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">{{ $labelKategori }}</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $warnaBadge }}">{{ $labelStatus }}</span>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 leading-tight">{{ $p->nama_pelatihan }}</h3>
                            <div class="flex items-center flex-wrap gap-x-4 gap-y-1 mt-2 text-sm text-gray-500">
                                <span><i class="bi bi-calendar3 mr-1"></i> {{ $p->tanggal_mulai->translatedFormat('d M Y') }} @if($p->tanggal_selesai && !$p->tanggal_mulai->isSameDay($p->tanggal_selesai)) - {{ $p->tanggal_selesai->translatedFormat('d M Y') }} @endif</span>
                                @if($p->penyelenggara)
                                    <span><i class="bi bi-building mr-1"></i> {{ $p->penyelenggara }}</span>
                                @endif
                                <span><i class="bi bi-people-fill mr-1"></i> {{ $p->peserta->count() }} Peserta</span>
                            </div>
                        </div>

                        {{-- Tombol Aksi Kanan --}}
                        <div class="flex items-center gap-2 shrink-0">
                            {{-- Tombol Utama --}}
                            <button @click="bukaDetail = !bukaDetail"
                                class="text-xs font-bold px-4 py-2 rounded-xl transition-colors flex items-center gap-2
                                {{ $bisaKelolaPeserta ? 'bg-blue-50 text-blue-700 hover:bg-blue-100' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                <i class="bi" :class="bukaDetail ? 'bi-chevron-up' : 'bi-people'"></i>
                                <span x-text="bukaDetail ? 'Tutup' : 'Peserta'"></span>
                            </button>

                            {{-- TOMBOL INPUT NILAI --}}
                            @if($bisaInputNilai)
                                <button type="button" 
                                    data-modal='@json($modalData)'
                                    onclick="bukaModalNilai(JSON.parse(this.dataset.modal))"
                                    class="text-xs font-bold px-4 py-2 rounded-xl bg-violet-600 text-white hover:bg-violet-700 shadow-sm flex items-center gap-2">
                                    <i class="bi bi-clipboard-data-fill"></i> Input Nilai
                                </button>
                            @endif

                            @if($jumlahLulus > 0)
                                <a href="{{ route('pelatihan.cetak-semua', $p->id) }}" target="_blank"
                                   class="text-xs font-bold px-4 py-2 rounded-xl bg-amber-500 text-white hover:bg-amber-600 shadow-sm flex items-center gap-2">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> Cetak ({{ $jumlahLulus }})
                                </a>
                            @endif

                            {{-- Dropdown Menu (Titik Tiga) --}}
                            <div class="relative" @click.away="bukaMenu = false">
                                <button @click="bukaMenu = !bukaMenu" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                    <i class="bi bi-three-dots-vertical text-lg"></i>
                                </button>
                                <div x-show="bukaMenu" x-cloak x-transition
                                    class="absolute right-0 mt-1 w-44 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-30">
                                    
                                    <button onclick="bukaModalEdit({{ $p->id }}, {{ json_encode($p) }})"
                                        class="w-full text-left px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                        <i class="bi bi-pencil"></i> Edit Pelatihan
                                    </button>
                                    
                                    {{-- TOMBOL TANDAI SELESAI --}}
                                    @if($p->status === 'aktif')
                                        <button onclick="bukaKonfirmasiSelesai({{ $p->id }}, '{{ addslashes($p->nama_pelatihan) }}')"
                                            class="w-full text-left px-4 py-2.5 text-xs font-semibold text-emerald-600 hover:bg-emerald-50 flex items-center gap-2">
                                            <i class="bi bi-check-circle-fill"></i> Tandai Selesai
                                        </button>
                                        <form id="form-selesai-{{ $p->id }}" method="POST" action="{{ route('pelatihan.selesai', $p->id) }}" class="hidden">
                                            @csrf @method('PATCH')
                                        </form>
                                    @endif

                                    @if($bisaDibatalkan)
                                        <button onclick="bukaKonfirmasiBatalkan({{ $p->id }}, '{{ addslashes($p->nama_pelatihan) }}')"
                                            class="w-full text-left px-4 py-2.5 text-xs font-semibold text-amber-600 hover:bg-amber-50 flex items-center gap-2">
                                            <i class="bi bi-x-circle"></i> Batalkan
                                        </button>
                                    @endif
                                    
                                    @if($bisaDihapus)
                                        <button onclick="bukaKonfirmasiHapus({{ $p->id }}, '{{ addslashes($p->nama_pelatihan) }}')"
                                            class="w-full text-left px-4 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 flex items-center gap-2 border-t border-gray-100">
                                            <i class="bi bi-trash"></i> Hapus Permanen
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PANEL PESERTA (Collapsible) --}}
                    <div x-show="bukaDetail" x-collapse x-cloak class="border-t border-gray-100 bg-gray-50/50">
                        <div class="p-5">
                            
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-bold text-gray-800">Daftar Peserta</h4>
                                @if($bisaKelolaPeserta)
                                    <button onclick="bukaModalTambahPeserta({{ $p->id }})" 
                                        class="text-xs font-bold px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm">
                                        <i class="bi bi-person-plus-fill mr-1"></i> Tambah Peserta
                                    </button>
                                @endif
                            </div>

                            {{-- List Peserta (Grid) --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @forelse($p->peserta as $ps)
                                    @php
                                        $sertif = $p->sertifikasi->firstWhere('karyawan_id', $ps->karyawan_id);
                                    @endphp
                                    <div class="bg-white rounded-xl border border-gray-200 p-3 flex items-center justify-between gap-3 shadow-sm hover:border-blue-300 transition-colors">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center text-sm font-bold shrink-0">
                                                {{ strtoupper(substr($ps->karyawan->nama_lengkap ?? '?', 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-sm font-bold text-gray-800 truncate">{{ $ps->karyawan->nama_lengkap ?? '-' }}</div>
                                                <div class="text-[11px] text-gray-500 truncate">{{ $ps->karyawan->jabatan->nama_jabatan ?? '-' }}</div>
                                                
                                                {{-- Info Nilai (Jika ada) --}}
                                                @if($sertif && $sertif->nilai_postest !== null)
                                                    <div class="text-[10px] mt-1 flex gap-2">
                                                        <span class="text-gray-500">Postest: <strong class="text-gray-800">{{ $sertif->nilai_postest }}</strong></span>
                                                        @if($sertif->status === 'lulus')
                                                            <span class="text-emerald-600 font-bold"><i class="bi bi-check-circle-fill"></i> Lulus</span>
                                                        @else
                                                            <span class="text-rose-600 font-bold"><i class="bi bi-x-circle-fill"></i> Gagal</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="shrink-0 flex items-center gap-2">
                                            {{-- Tombol Hadir --}}
                                            @if($ps->status_kehadiran === 'hadir')
                                                <span class="text-[10px] font-bold px-2.5 py-1.5 rounded-lg bg-emerald-100 text-emerald-700 border border-emerald-200 flex items-center gap-1" title="Hadir pada {{ $ps->waktu_hadir ? \Carbon\Carbon::parse($ps->waktu_hadir)->format('H:i') : '' }}">
                                                    <i class="bi bi-check2-circle"></i> Hadir
                                                </span>
                                            @elseif($bisaKelolaPeserta)
                                                <form method="POST" action="{{ route('pelatihan.peserta.hadir', $ps->id) }}">
                                                    @csrf
                                                    <button type="submit" class="text-[10px] font-bold px-2.5 py-1.5 rounded-lg bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition-colors" title="Tandai Hadir">
                                                        Klik Hadir
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Tombol Hapus Peserta --}}
                                            @if($bisaKelolaPeserta)
                                                <button onclick="bukaKonfirmasiHapusPeserta({{ $ps->id }}, '{{ addslashes($ps->karyawan->nama_lengkap ?? '') }}')"
                                                    class="p-1.5 text-gray-300 hover:text-rose-500 transition-colors" title="Hapus peserta">
                                                    <i class="bi bi-x-circle-fill text-lg"></i>
                                                </button>
                                                <form id="form-hapus-peserta-{{ $ps->id }}" method="POST" action="{{ route('pelatihan.peserta.hapus', [$p->id, $ps->karyawan_id]) }}" class="hidden">
                                                    @csrf @method('DELETE')
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full text-center py-6 text-sm text-gray-400 bg-white rounded-xl border border-dashed border-gray-300">
                                        Belum ada peserta yang ditunjuk.
                                    </div>
                                @endforelse
                            </div>

                        </div>
                    </div>
                </div>
                @empty
                    <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-sm">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="bi bi-inbox text-4xl text-gray-300"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Tidak Ada Data</h3>
                        <p class="text-sm text-gray-500">Belum ada pelatihan yang cocok dengan pencarian Anda.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">{{ $pelatihans->links() }}</div>
        </div>
    </main>
</div>

{{-- ========================================================= --}}
{{-- MODAL TAMBAH PESERTA                                      --}}
{{-- ========================================================= --}}
<div id="modalTambahPeserta" class="hidden fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-gray-800 text-lg">Tambah Peserta</h3>
            <button onclick="document.getElementById('modalTambahPeserta').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <form id="formTambahPeserta" method="POST" action="">
            @csrf
            <div class="relative mb-3">
                <i class="bi bi-search absolute left-3 top-2.5 text-gray-400"></i>
                <input type="text" placeholder="Cari nama karyawan..." oninput="filterModalPeserta(this)"
                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            
            <div id="modalListKaryawan" class="border border-gray-200 rounded-lg max-h-60 overflow-y-auto mb-4 bg-gray-50 p-2 space-y-1">
                {{-- Diisi via JS --}}
            </div>

            <div class="flex gap-2 justify-end">
                <button type="button" onclick="document.getElementById('modalTambahPeserta').classList.add('hidden')"
                    class="px-4 py-2 text-sm font-bold bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm">
                    Simpan Peserta
                </button>
            </div>
        </form>
    </div>
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
            <button id="konfirmasi-ok" class="px-4 py-2 text-sm font-bold text-white rounded-xl">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

{{-- MODAL TAMBAH PELATIHAN --}}
<div id="modalTambahPelatihan" class="hidden fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
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
                <button type="button" onclick="document.getElementById('modalTambahPelatihan').classList.add('hidden')" class="px-4 py-2 text-sm font-bold bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT PELATIHAN --}}
<div id="modalEditPelatihan" class="hidden fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="font-bold text-gray-800 text-lg mb-4">Edit Pelatihan</h3>
        <form method="POST" id="formEditPelatihan">
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
                <button type="button" onclick="document.getElementById('modalEditPelatihan').classList.add('hidden')" class="px-4 py-2 text-sm font-bold bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL INPUT NILAI BULK --}}
<div id="modalNilai" class="hidden fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[92vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center gap-3 bg-violet-50 rounded-t-2xl">
            <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center shrink-0">
                <i class="bi bi-clipboard-data-fill text-lg"></i>
            </div>
            <div class="min-w-0">
                <h3 class="font-bold text-gray-800 text-lg">Input Nilai Peserta</h3>
                <p class="text-xs text-violet-700 font-semibold mt-0.5" id="nilaiSubtitle">-</p>
            </div>
        </div>

        <form method="POST" id="formNilai" class="flex-1 flex flex-col min-h-0">
            @csrf
            <div class="flex-1 overflow-y-auto p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Nomor Surat</label>
                        <input type="text" name="nomor_sertifikat" id="inputNomorSurat" placeholder="001/SERT/..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Tanggal Surat</label>
                        <input type="date" name="tanggal_surat" id="inputTanggalSurat" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Ditandatangani Oleh</label>
                        <input type="text" name="ditandatangani_oleh" id="inputPenandatangan" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Jabatan</label>
                        <input type="text" name="jabatan_penandatangan" id="inputJabatan" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none">
                    </div>
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
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2 bg-gray-50 rounded-b-2xl">
                <button type="button" onclick="document.getElementById('modalNilai').classList.add('hidden')" class="px-4 py-2 text-sm font-bold bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">Batal</button>
                <button type="submit" class="px-5 py-2 text-sm font-bold bg-violet-600 text-white rounded-lg hover:bg-violet-700 shadow-sm">
                    <i class="bi bi-save mr-1"></i>Simpan Nilai
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Data Karyawan untuk Modal Tambah
    const semuaKaryawan = @json(\App\Models\Karyawan::with(['departemen','jabatan'])->where('status','aktif')->orderBy('nama_lengkap')->get());

    // --- Modal Tambah Peserta ---
    function bukaModalTambahPeserta(pelatihanId) {
        const form = document.getElementById('formTambahPeserta');
        form.action = `/hrd/pelatihan/${pelatihanId}/peserta`;
        
        const list = document.getElementById('modalListKaryawan');
        list.innerHTML = '';

        semuaKaryawan.forEach(k => {
            const label = document.createElement('label');
            label.className = 'flex items-center gap-2 p-2 hover:bg-blue-50 rounded cursor-pointer transition-colors modal-peserta-item';
            label.innerHTML = `
                <input type="checkbox" name="karyawan_ids[]" value="${k.id}" class="rounded accent-blue-600 w-4 h-4">
                <div class="text-sm">
                    <div class="font-bold text-gray-800">${k.nama_lengkap}</div>
                    <div class="text-[10px] text-gray-500">${k.jabatan?.nama_jabatan ?? '-'} • ${k.departemen?.nama_departemen ?? '-'}</div>
                </div>
            `;
            list.appendChild(label);
        });

        document.getElementById('modalTambahPeserta').classList.remove('hidden');
    }

    function filterModalPeserta(input) {
        const q = input.value.toLowerCase();
        const items = document.querySelectorAll('.modal-peserta-item');
        items.forEach(item => {
            item.style.display = item.innerText.toLowerCase().includes(q) ? '' : 'none';
        });
    }

    // --- Modal Input Nilai (DENGAN VALIDASI ABSEN) ---
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
                const isHadir = p.status_kehadiran === 'hadir';
                const tr = document.createElement('tr');
                tr.className = isHadir ? 'hover:bg-gray-50' : 'bg-gray-50 opacity-70';
                
                const disabledAttr = isHadir ? '' : 'disabled';
                const placeholderText = isHadir ? '0-10' : 'Absen';

                tr.innerHTML = `
                    <td class="px-3 py-2 text-gray-400">${i + 1}</td>
                    <td class="px-3 py-2">
                        <div class="font-semibold ${isHadir ? 'text-gray-800' : 'text-gray-500'}">${p.nama}</div>
                        <div class="text-[10px] text-gray-400">${p.jabatan} • ${p.departemen}</div>
                        <input type="hidden" name="karyawan_ids[]" value="${p.karyawan_id}">
                    </td>
                    <td class="px-3 py-2">
                        <input type="number" step="0.01" min="0" max="10"
                            name="nilai_pretest[${p.karyawan_id}]"
                            value="${p.nilai_pretest ?? ''}"
                            placeholder="${placeholderText}"
                            ${disabledAttr}
                            class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-center text-xs focus:ring-2 focus:ring-violet-500 outline-none disabled:bg-gray-200 disabled:cursor-not-allowed">
                    </td>
                    <td class="px-3 py-2">
                        <input type="number" step="0.01" min="0" max="10"
                            name="nilai_postest[${p.karyawan_id}]"
                            value="${p.nilai_postest ?? ''}"
                            placeholder="${placeholderText}"
                            oninput="updateStatusBaris(this)"
                            data-status-target="status-${p.karyawan_id}"
                            ${disabledAttr}
                            class="w-full border-2 border-violet-300 rounded-lg px-2 py-1.5 text-center text-sm font-bold focus:ring-2 focus:ring-violet-500 outline-none disabled:bg-gray-200 disabled:cursor-not-allowed disabled:border-gray-300">
                    </td>
                    <td class="px-3 py-2 text-center" id="status-${p.karyawan_id}"></td>
                `;
                tbody.appendChild(tr);

                if (isHadir) {
                    const inputPost = tr.querySelector('input[name^="nilai_postest"]');
                    updateStatusBaris(inputPost);
                } else {
                    const statusEl = tr.querySelector(`#status-${p.karyawan_id}`);
                    statusEl.innerHTML = '<span class="bg-gray-200 text-gray-500 font-bold text-[10px] px-2 py-1 rounded-full"><i class="bi bi-x-circle-fill"></i> ABSEN</span>';
                }
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

    // --- Modal Edit Pelatihan ---
    function bukaModalEdit(id, data) {
        document.getElementById('formEditPelatihan').action = `/hrd/pelatihan/${id}`;
        document.getElementById('editNama').value = data.nama_pelatihan;
        document.getElementById('editPenyelenggara').value = data.penyelenggara ?? '';
        document.getElementById('editKategori').value = data.kategori;
        
        // ⬇️ PERBAIKAN: Potong string tanggal agar hanya mengambil YYYY-MM-DD ⬇️
        document.getElementById('editTglMulai').value = data.tanggal_mulai ? data.tanggal_mulai.substring(0, 10) : '';
        document.getElementById('editTglSelesai').value = data.tanggal_selesai ? data.tanggal_selesai.substring(0, 10) : '';
        
        document.getElementById('editStatus').value = data.status;
        document.getElementById('editDeskripsi').value = data.deskripsi ?? '';
        document.getElementById('modalEditPelatihan').classList.remove('hidden');
    }

    // --- Konfirmasi Universal ---
    let _onOkCallback = null;
    function bukaKonfirmasi({ judul, pesan, labelOk, warnaOk, onOk, ikon = 'bi-exclamation-triangle-fill', warnaIkon = 'bg-amber-500' }) {
        document.getElementById('konfirmasi-judul').innerHTML = judul;
        document.getElementById('konfirmasi-pesan').innerHTML = pesan;
        document.getElementById('konfirmasi-ikon').className = 'bi ' + ikon;
        document.getElementById('konfirmasi-ikon-box').className = `w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-white text-lg ${warnaIkon}`;
        const btnOk = document.getElementById('konfirmasi-ok');
        btnOk.textContent = labelOk;
        btnOk.className = `px-4 py-2 text-sm font-bold text-white rounded-xl transition-colors ${warnaOk}`;
        _onOkCallback = onOk;
        document.getElementById('modal-konfirmasi').classList.remove('hidden');
    }

    document.getElementById('konfirmasi-ok').addEventListener('click', function () {
        const cb = _onOkCallback;
        tutupKonfirmasi();
        if (typeof cb === 'function') cb();
    });

    function tutupKonfirmasi() {
        document.getElementById('modal-konfirmasi').classList.add('hidden');
        _onOkCallback = null;
    }

    // ⬇️ FUNGSI KONFIRMASI TANDAI SELESAI ⬇️
    function bukaKonfirmasiSelesai(id, nama) {
        bukaKonfirmasi({
            judul: 'Tandai Pelatihan Selesai?',
            pesan: `Pelatihan <strong>${nama}</strong> akan ditandai sebagai selesai. Pastikan Anda sudah menginput semua nilai peserta.`,
            labelOk: 'Ya, Tandai Selesai',
            warnaOk: 'bg-emerald-600 hover:bg-emerald-700',
            onOk: () => document.getElementById(`form-selesai-${id}`).submit(),
            ikon: 'bi-check-circle-fill',
            warnaIkon: 'bg-emerald-500'
        });
    }

    function bukaKonfirmasiBatalkan(id, nama) {
        bukaKonfirmasi({
            judul: 'Batalkan Pelatihan?',
            pesan: `Pelatihan <strong>${nama}</strong> akan dibatalkan.`,
            labelOk: 'Ya, Batalkan',
            warnaOk: 'bg-amber-500 hover:bg-amber-600',
            onOk: () => document.getElementById(`form-batalkan-${id}`).submit()
        });
    }

    function bukaKonfirmasiHapus(id, nama) {
        bukaKonfirmasi({
            judul: 'Hapus Pelatihan?',
            pesan: `Pelatihan <strong>${nama}</strong> akan dihapus permanen.`,
            labelOk: 'Ya, Hapus',
            warnaOk: 'bg-rose-600 hover:bg-rose-700',
            onOk: () => document.getElementById(`form-hapus-${id}`).submit()
        });
    }

    function bukaKonfirmasiHapusPeserta(id, nama) {
        bukaKonfirmasi({
            judul: 'Hapus Peserta?',
            pesan: `<strong>${nama}</strong> akan dihapus dari daftar.`,
            labelOk: 'Ya, Hapus',
            warnaOk: 'bg-rose-600 hover:bg-rose-700',
            onOk: () => document.getElementById(`form-hapus-peserta-${id}`).submit()
        });
    }
</script>
</body>
</html>