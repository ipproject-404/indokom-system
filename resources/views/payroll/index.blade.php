@extends('layouts.app')

@section('title', 'Dashboard Payroll')

@section('content')
@php
    $labelMinggu = \Carbon\Carbon::parse($awal)->translatedFormat('d M') . ' – ' . \Carbon\Carbon::parse($akhir)->translatedFormat('d M Y');
    $statusLembur = [
        'menunggu'  => ['warna' => '#f59e0b', 'label' => 'Menunggu'],
        'disetujui' => ['warna' => '#16a34a', 'label' => 'Disetujui'],
        'ditolak'   => ['warna' => '#ef4444', 'label' => 'Ditolak'],
    ];
    $idKaryawanLogin = Auth::user()->karyawan?->id;
@endphp

<style>
    html, body { margin:0; padding:0; width:100%; overflow-x:hidden; }
    body { background:#f1f5f9; font-family:'Inter','Segoe UI',sans-serif; }
    .app-shell { max-width:none; margin:0; box-shadow:none; padding-bottom:0; }

    /* SHARED */
    .kartu-lembur { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:1rem; margin-bottom:.75rem; }
    .kartu-stat   { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:1rem 1.25rem; }
    .badge-status { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:700; white-space:nowrap; }
    .nav-minggu   { display:flex; align-items:center; gap:8px; }
    .nav-minggu a { width:32px; height:32px; border-radius:10px; background:#f1f5f9; color:#475569; display:flex; align-items:center; justify-content:center; text-decoration:none; }
    .nav-minggu .label { font-weight:700; color:#1e293b; font-size:.85rem; }
    .menu-payroll-grid { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; margin-bottom:1.25rem; }
    .menu-payroll-grid a { background:#fff; border:1.5px solid #e2e8f0; border-radius:14px; padding:1rem .75rem; text-decoration:none; display:flex; flex-direction:column; align-items:center; gap:6px; color:#475569; font-size:.75rem; font-weight:700; text-align:center; transition:border-color .15s; }
    .menu-payroll-grid a:hover { border-color:#2563eb; color:#2563eb; }
    .menu-payroll-grid a i { font-size:1.4rem; }
    .menu-payroll-grid a.aktif { border-color:#2563eb; color:#2563eb; background:#eff6ff; }

    /* MOBILE */
    .mobile-shell { width:100%; background:#fff; min-height:100vh; padding-bottom:90px; }
    .mobile-shell .app-header {
        background:#fff; padding:1.1rem 1rem; border-bottom:1px solid #e2e8f0;
        position:sticky; top:0; z-index:10; display:flex; align-items:center; gap:12px;
    }
    .mobile-shell .app-header .back-btn {
        width:38px; height:38px; border-radius:12px; background:#f1f5f9;
        display:flex; align-items:center; justify-content:center; color:#475569; text-decoration:none; flex-shrink:0;
    }
    .mobile-shell .app-header h6 { margin:0; font-weight:800; color:#1e293b; }

    /* DESKTOP */
    .desktop-shell { display:none; }
    @media (min-width:992px) {
        .desktop-shell { display:flex; min-height:100vh; background:#f8fafc; font-family:'Inter','Segoe UI',sans-serif; }
        .desktop-main  { flex-grow:1; padding:1.75rem 2rem; max-width:1100px; }
        .page-header-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; }
        .page-header-row h4 { font-weight:800; color:#1e293b; margin-bottom:2px; }
        .page-header-row p  { color:#94a3b8; font-size:.85rem; margin:0; }
        .panel { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:1.25rem; }
    }
</style>

{{-- ===== MOBILE ===== --}}
<div class="mobile-shell d-lg-none">
    <div class="app-header">
        <a href="{{ route('dashboard.karyawan') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <h6>Dashboard Payroll</h6>
    </div>

    <div class="p-3">

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 mb-3" style="font-size:.82rem; border-radius:12px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.82rem; border-radius:12px;">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.82rem; border-radius:12px;">{{ $errors->first() }}</div>
        @endif

        {{-- Menu --}}
        <div class="menu-payroll-grid">
            <a href="{{ route('payroll.index') }}" class="aktif">
                <i class="bi bi-house-fill"></i> Dashboard
            </a>
            <a href="{{ route('payroll.rekap', ['minggu' => $minggu->toDateString()]) }}">
                <i class="bi bi-table"></i> Rekap Minggu
            </a>
            <a href="{{ route('payroll.tarif') }}">
                <i class="bi bi-gear-fill"></i> Kelola Tarif
            </a>
            <a href="{{ route('lembur.payroll') }}">
                <i class="bi bi-clock-history"></i> Pengajuan Lama
            </a>
        </div>

        {{-- Navigasi Minggu --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="nav-minggu">
                <a href="{{ route('payroll.index', ['minggu' => $mingguSebelumnya]) }}"><i class="bi bi-chevron-left"></i></a>
                <div class="label">{{ $labelMinggu }}</div>
                <a href="{{ route('payroll.index', ['minggu' => $mingguBerikutnya]) }}"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>

        {{-- Stat --}}
        <div class="row g-2 mb-3">
            <div class="col-4">
                <div class="kartu-stat text-center">
                    <div class="fw-bold fs-5" style="color:#f59e0b;">{{ $lemburs->total() }}</div>
                    <div style="font-size:.68rem; color:#94a3b8;">Total</div>
                </div>
            </div>
            <div class="col-4">
                <div class="kartu-stat text-center">
                    <div class="fw-bold fs-5" style="color:#16a34a;">{{ $rekap->get('disetujui') ?? 0 }}</div>
                    <div style="font-size:.68rem; color:#94a3b8;">ACC</div>
                </div>
            </div>
            <div class="col-4">
                <div class="kartu-stat text-center">
                    <div class="fw-bold" style="font-size:.85rem; color:#2563eb;">
                        Rp {{ number_format($rekap->get('total') ?? 0, 0, ',', '.') }}
                    </div>
                    <div style="font-size:.68rem; color:#94a3b8;">Upah</div>
                </div>
            </div>
        </div>

        {{-- Daftar Lembur --}}
        @forelse($lemburs as $l)
        @php
            $st = $statusLembur[$l->status] ?? $statusLembur['menunggu'];
            $milikSendiri = $l->karyawan_id === $idKaryawanLogin;
        @endphp
        <div class="kartu-lembur">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div>
                    <div class="fw-bold" style="font-size:.88rem;">{{ $l->karyawan->nama_lengkap ?? '-' }}</div>
                    <div class="text-muted" style="font-size:.74rem;">
                        {{ $l->karyawan->jabatan->nama_jabatan ?? '-' }} &middot; {{ $l->karyawan->departemen->nama_departemen ?? '-' }}
                    </div>
                </div>
                <span class="badge-status" style="background:{{ $st['warna'] }}1a; color:{{ $st['warna'] }};">{{ $st['label'] }}</span>
            </div>
            <div style="font-size:.82rem; font-weight:600; color:#1e293b;">
                {{ \Carbon\Carbon::parse($l->tanggal)->translatedFormat('l, d M Y') }}
            </div>
            <div class="text-muted mb-1" style="font-size:.78rem;">{{ $l->catatan }}</div>
            @if($l->estimasi_jam)
                <div style="font-size:.74rem; color:#94a3b8;" class="mb-2">
                    <i class="bi bi-clock me-1"></i>Estimasi karyawan: <strong>{{ $l->estimasi_jam }} jam</strong>
                </div>
            @endif

            @if($l->status === 'disetujui')
                <div class="d-flex gap-2 flex-wrap mb-1">
                    <span class="badge-status" style="background:#eff6ff; color:#2563eb;">
                        {{ $l->durasi_final_jam }} jam &middot; {{ ucfirst($l->kelompok ?? '-') }}
                    </span>
                    <span class="badge-status" style="background:#f0fdf4; color:#16a34a;">
                        Rp {{ number_format($l->total_upah ?? 0, 0, ',', '.') }}
                    </span>
                    @if(!($l->memenuhi_syarat ?? true))
                        <span class="badge-status" style="background:#fef2f2; color:#ef4444;">Insentif &lt; 4 jam</span>
                    @endif
                </div>
            @endif

            @if($l->status === 'menunggu' && !$milikSendiri)
                <form method="POST" action="{{ route('payroll.setujui', $l->id) }}" class="mt-2">
                    @csrf
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label mb-1" style="font-size:.72rem; font-weight:700;">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control form-control-sm"
                                value="{{ $l->jam_mulai ? substr($l->jam_mulai, 0, 5) : '' }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label mb-1" style="font-size:.72rem; font-weight:700;">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control form-control-sm"
                                value="{{ $l->jam_selesai ? substr($l->jam_selesai, 0, 5) : '' }}" required>
                        </div>
                    </div>
                    <input type="text" name="catatan_payroll" maxlength="255"
                        class="form-control form-control-sm mb-2"
                        placeholder="Catatan (wajib kalau menolak)">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-biru flex-fill" style="border-radius:10px;"
                            onclick="return confirm('ACC lembur ini?')">
                            <i class="bi bi-check-lg me-1"></i>ACC
                        </button>
                        <button type="submit"
                            formaction="{{ route('payroll.tolak', $l->id) }}"
                            formnovalidate
                            class="btn btn-sm btn-light flex-fill"
                            style="border-radius:10px; color:#ef4444;"
                            onclick="return confirm('Tolak lembur ini?')">
                            <i class="bi bi-x-lg me-1"></i>Tolak
                        </button>
                    </div>
                </form>
            @elseif($milikSendiri && $l->status === 'menunggu')
                <div class="text-muted mt-1" style="font-size:.76rem;">Ini lembur milikmu, diproses payroll lain.</div>
            @endif
        </div>
        @empty
            <div class="kartu-lembur text-center text-muted" style="font-size:.85rem;">Tidak ada pengajuan lembur minggu ini.</div>
        @endforelse

        @if($lemburs->hasPages())
            <div class="mt-2">{{ $lemburs->links() }}</div>
        @endif
    </div>

    @include('partials.bottom-nav-karyawan')
</div>

{{-- ===== DESKTOP ===== --}}
<div class="desktop-shell">
    @include('partials.sidebar-karyawan')

    <main class="desktop-main">
        <div class="page-header-row">
            <div>
                <h4>Dashboard Payroll</h4>
                <p>Kelola lembur, tarif, dan rekap pembayaran mingguan</p>
            </div>
            <div class="nav-minggu">
                <a href="{{ route('payroll.index', ['minggu' => $mingguSebelumnya]) }}"><i class="bi bi-chevron-left"></i></a>
                <div class="label">{{ $labelMinggu }}</div>
                <a href="{{ route('payroll.index', ['minggu' => $mingguBerikutnya]) }}"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 mb-3" style="font-size:.85rem; border-radius:10px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.85rem; border-radius:10px;">{{ session('error') }}</div>
        @endif

        {{-- Menu --}}
        <div class="menu-payroll-grid mb-4" style="grid-template-columns:repeat(4,1fr);">
            <a href="{{ route('payroll.index') }}" class="aktif">
                <i class="bi bi-house-fill"></i> Dashboard
            </a>
            <a href="{{ route('payroll.rekap', ['minggu' => $minggu->toDateString()]) }}">
                <i class="bi bi-table"></i> Rekap Minggu
            </a>
            <a href="{{ route('payroll.tarif') }}">
                <i class="bi bi-gear-fill"></i> Kelola Tarif
            </a>
            <a href="{{ route('lembur.payroll') }}">
                <i class="bi bi-clock-history"></i> Pengajuan Lama
            </a>
        </div>

        {{-- Stat --}}
        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="kartu-stat d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size:.78rem; color:#94a3b8; font-weight:600;">Total Pengajuan</div>
                        <div class="fw-bold" style="font-size:1.5rem; color:#f59e0b;">{{ $lemburs->total() }}</div>
                    </div>
                    <div style="width:40px;height:40px;border-radius:10px;background:#fef3c7;color:#f59e0b;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="kartu-stat d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size:.78rem; color:#94a3b8; font-weight:600;">Disetujui</div>
                        <div class="fw-bold" style="font-size:1.5rem; color:#16a34a;">{{ $rekap->get('disetujui') ?? 0 }}</div>
                    </div>
                    <div style="width:40px;height:40px;border-radius:10px;background:#dcfce7;color:#16a34a;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="kartu-stat d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size:.78rem; color:#94a3b8; font-weight:600;">Total Upah Minggu Ini</div>
                        <div class="fw-bold" style="font-size:1.2rem; color:#2563eb;">
                            Rp {{ number_format($rekap->get('total') ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                    <div style="width:40px;height:40px;border-radius:10px;background:#dbeafe;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Lembur (desktop table) --}}
        <div class="panel">
            <h6 class="fw-bold mb-3" style="color:#1e293b;">Pengajuan Lembur — {{ $labelMinggu }}</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle" style="font-size:.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Karyawan</th>
                            <th>Tanggal</th>
                            <th>Estimasi</th>
                            <th>Alasan</th>
                            <th class="text-center">Status</th>
                            <th>Hasil</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lemburs as $l)
                        @php
                            $st = $statusLembur[$l->status] ?? $statusLembur['menunggu'];
                            $milikSendiri = $l->karyawan_id === $idKaryawanLogin;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $l->karyawan->nama_lengkap ?? '-' }}</div>
                                <div class="text-muted" style="font-size:.75rem;">{{ $l->karyawan->jabatan->nama_jabatan ?? '-' }}</div>
                                <div class="text-muted" style="font-size:.75rem;">{{ $l->karyawan->departemen->nama_departemen ?? '-' }}</div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($l->tanggal)->translatedFormat('d M Y') }}</td>
                            <td>
                                @if($l->estimasi_jam)
                                    <span class="badge-status" style="background:#f8fafc; color:#475569;">
                                        {{ $l->estimasi_jam }} jam
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td style="max-width:200px;">{{ $l->catatan }}</td>
                            <td class="text-center">
                                <span class="badge-status" style="background:{{ $st['warna'] }}1a; color:{{ $st['warna'] }};">
                                    {{ $st['label'] }}
                                </span>
                            </td>
                            <td>
                                @if($l->status === 'disetujui')
                                    <div style="font-size:.78rem;">{{ $l->durasi_final_jam }} jam</div>
                                    <div class="fw-bold" style="color:#16a34a; font-size:.78rem;">
                                        Rp {{ number_format($l->total_upah ?? 0, 0, ',', '.') }}
                                    </div>
                                    @if(!($l->memenuhi_syarat ?? true))
                                        <div style="color:#ef4444; font-size:.72rem;">Insentif &lt;4 jam</div>
                                    @endif
                                @elseif($l->catatan_payroll)
                                    <div class="text-muted" style="font-size:.75rem;">{{ $l->catatan_payroll }}</div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($l->status === 'menunggu' && !$milikSendiri)
                                    <button class="btn btn-sm btn-biru" style="border-radius:8px; font-size:.75rem;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalAcc"
                                        data-id="{{ $l->id }}"
                                        data-nama="{{ $l->karyawan->nama_lengkap }}"
                                        data-mulai="{{ $l->jam_mulai ? substr($l->jam_mulai, 0, 5) : '' }}"
                                        data-selesai="{{ $l->jam_selesai ? substr($l->jam_selesai, 0, 5) : '' }}">
                                        Proses
                                    </button>
                                @elseif($milikSendiri && $l->status === 'menunggu')
                                    <span class="text-muted" style="font-size:.75rem;">Milikmu</span>
                                @else
                                    <span class="text-muted" style="font-size:.75rem;">Selesai</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Tidak ada pengajuan lembur minggu ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($lemburs->hasPages())
                <div class="mt-2">{{ $lemburs->links() }}</div>
            @endif
        </div>
    </main>
</div>

{{-- Modal ACC Desktop --}}
<div class="modal fade" id="modalAcc" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Proses Lembur</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAcc" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3" style="font-size:.85rem;" id="modalNama"></p>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Jam Mulai</label>
                            <input type="time" name="jam_mulai" id="modalJamMulai" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Jam Selesai</label>
                            <input type="time" name="jam_selesai" id="modalJamSelesai" class="form-control" required>
                        </div>
                    </div>
                    <label class="form-label small fw-bold">Catatan Payroll</label>
                    <input type="text" name="catatan_payroll" class="form-control" maxlength="255"
                        placeholder="Wajib diisi kalau menolak">
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px;">Batal</button>
                    <button type="submit" class="btn btn-biru" id="btnAcc" style="border-radius:10px;"
                        onclick="return confirm('ACC lembur ini?')">ACC</button>
                    <button type="submit" id="btnTolak" class="btn btn-light" style="border-radius:10px; color:#ef4444;"
                        onclick="this.form.action = this.dataset.url; return confirm('Tolak lembur ini?')">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('modalAcc').addEventListener('show.bs.modal', function(e) {
        const btn = e.relatedTarget;
        const id  = btn.dataset.id;
        document.getElementById('modalNama').textContent    = btn.dataset.nama;
        document.getElementById('modalJamMulai').value      = btn.dataset.mulai;
        document.getElementById('modalJamSelesai').value    = btn.dataset.selesai;
        document.getElementById('formAcc').action           = `/payroll/${id}/setujui`;
        document.getElementById('btnTolak').dataset.url     = `/payroll/${id}/tolak`;
    });
</script>
@endpush

@endsection