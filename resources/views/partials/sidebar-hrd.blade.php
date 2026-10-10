@php
    // Daftar menu sidebar HRD -- SATU-SATUNYA tempat yang perlu diubah
    // kalau menu ditambah/diganti. 'aktif' = daftar nama route yang
    // membuat menu ini tampil terpilih (kosong = tidak pernah terpilih).
    $menuSidebar = [
        'Menu Utama' => [
            ['label' => 'Dashboard', 'icon' => 'bi-grid-1x2-fill', 'route' => 'dashboard.hrd', 'aktif' => ['dashboard.hrd']],
            ['label' => 'Absensi Saya', 'icon' => 'bi-qr-code-scan', 'route' => 'dashboard.karyawan', 'aktif' => []],
        ],
        'Kelola Karyawan' => [
            ['label' => 'Daftar Karyawan', 'icon' => 'bi-people-fill', 'route' => 'karyawan.index', 'aktif' => ['karyawan.index', 'karyawan.show', 'karyawan.edit']],
            ['label' => 'Tambah Karyawan', 'icon' => 'bi-person-plus-fill', 'route' => 'karyawan.create', 'aktif' => ['karyawan.create']],
            ['label' => 'Jabatan & Departemen', 'icon' => 'bi-diagram-3-fill', 'route' => 'jabatan.departemen.index', 'aktif' => ['jabatan.departemen.index']],
        ],
        'Kehadiran & QR' => [
            ['label' => 'Manajemen QR Code', 'icon' => 'bi-qr-code-scan', 'route' => 'karyawan.qr.index', 'aktif' => ['karyawan.qr.index']],
            ['label' => 'Log Kehadiran Harian', 'icon' => 'bi-calendar-check', 'route' => 'kehadiran.log', 'aktif' => ['kehadiran.log']],
            ['label' => 'Shift Karyawan', 'icon' => 'bi-calendar-range', 'route' => 'shift.hrd.index', 'aktif' => ['shift.hrd.index']],
            ['label' => 'Pengajuan Lembur', 'icon' => 'bi-file-earmark-plus', 'route' => 'lembur.pengajuan', 'aktif' => ['lembur.pengajuan'], 'badge' => $lemburMenunggu ?? 0],
        ],
        'Akun' => [
            ['label' => 'Profil Saya', 'icon' => 'bi-person-circle', 'route' => 'profile.index', 'aktif' => ['profile.index']],
        ],
    ];

    $akunLogin = Auth::user();
    $karyawanLogin = $akunLogin->karyawan ?? null;
    $namaSidebar = $karyawanLogin->nama_lengkap ?? ($akunLogin->username ?? '-');
    $jabatanSidebar = optional($karyawanLogin->jabatan ?? null)->nama_jabatan ?? '-';
    $departemenSidebar = optional($karyawanLogin->departemen ?? null)->nama_departemen ?? '-';
@endphp

<style>
    #sidebar::-webkit-scrollbar { width: 6px; }
    #sidebar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>

<aside id="sidebar" class="bg-white border-r border-gray-200 flex flex-col w-[260px] shrink-0 sticky top-0 h-screen">
    <div class="p-4 border-b border-gray-200">
        <div class="flex items-center">
            <div class="bg-blue-600 text-white rounded-lg flex items-center justify-center mr-3 w-[42px] h-[42px] shrink-0">
                <i class="bi bi-building-fill text-xl"></i>
            </div>
            <div>
                <div class="font-bold text-blue-600">Indokom System</div>
                <div class="text-xs text-gray-500">Presensi & Kinerja</div>
            </div>
        </div>
    </div>

    <div class="p-4 grow overflow-y-auto">
        @foreach ($menuSidebar as $judul => $items)
            <div class="uppercase text-gray-400 text-xs font-bold mb-3 {{ $loop->first ? 'mt-2' : 'mt-6' }}">{{ $judul }}</div>

            @foreach ($items as $item)
                @php $menuAktif = ! empty($item['aktif']) && request()->routeIs(...$item['aktif']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center justify-between rounded-lg px-4 py-2.5 mb-1 transition-colors {{ $menuAktif ? 'bg-blue-600 text-white' : 'text-gray-700 hover:text-blue-700 hover:bg-blue-50' }}">
                    <div class="flex items-center">
                        <i class="bi {{ $item['icon'] }} mr-3"></i>
                        <span class="text-sm font-medium">{{ $item['label'] }}</span>
                    </div>
                    @if (($item['badge'] ?? 0) > 0)
                        <span class="{{ $menuAktif ? 'bg-white text-blue-700' : 'bg-rose-500 text-white' }} text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach

            @if ($loop->last)
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full flex items-center text-red-600 hover:bg-red-50 rounded-lg px-4 py-2.5 transition-colors">
                        <i class="bi bi-box-arrow-left mr-3"></i>
                        <span class="text-sm font-medium">Logout</span>
                    </button>
                </form>
            @endif
        @endforeach
    </div>

    <div class="border-t border-gray-200 p-4 bg-gray-50">
        <div class="flex items-center">
            <div class="bg-blue-600 text-white rounded-full flex items-center justify-center mr-3 w-10 h-10 shrink-0 font-bold shadow-sm">
                {{ strtoupper(substr($namaSidebar, 0, 2)) }}
            </div>
            <div class="overflow-hidden">
                <div class="font-bold text-sm text-gray-800 truncate" title="{{ $namaSidebar }}">{{ $namaSidebar }}</div>
                <div class="text-xs text-blue-600 font-semibold truncate" title="{{ $jabatanSidebar }} • {{ $departemenSidebar }}">
                    {{ $jabatanSidebar }} &bull; {{ $departemenSidebar }}
                </div>
            </div>
        </div>
    </div>
</aside>