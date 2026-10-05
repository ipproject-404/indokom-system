@extends('layouts.app')

@section('title', 'Shift Tim')

@section('content')

@php
    $namaDepartemenTampil = $departemenDipimpin->pluck('nama_departemen')->implode(', ');
@endphp

<style>
    html, body { margin: 0; padding: 0; width: 100%; overflow-x: hidden; }
    body { background-color: #f1f5f9; font-family: 'Inter', 'Segoe UI', sans-serif; }
    .app-shell { max-width: none; margin: 0; box-shadow: none; padding-bottom: 0; }

    .mobile-shell { width: 100%; max-width: 100%; margin: 0; background: #ffffff; min-height: 100vh; box-shadow: none; padding-bottom: 40px; }
    .mobile-shell .app-header {
        background: #fff; padding: 1.1rem 1rem; border-bottom: 1px solid #e2e8f0;
        position: sticky; top: 0; z-index: 10; display: flex; align-items: center; gap: 12px;
    }
    .mobile-shell .app-header .back-btn {
        width: 38px; height: 38px; border-radius: 12px; background: #f1f5f9;
        display: flex; align-items: center; justify-content: center; color: #475569; text-decoration: none; flex-shrink: 0;
    }
    .mobile-shell .app-header h6 { margin: 0; font-weight: 800; color: #1e293b; }
    .mobile-shell .app-header small { display: block; color: #94a3b8; font-size: .72rem; }

    .modern-card { background: #fff; border-radius: 18px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.02); }

    .anak-buah-row { display: flex; align-items: center; gap: 12px; padding: 14px; border-bottom: 1px solid #f1f5f9; }
    .anak-buah-row:last-child { border-bottom: none; }
    .avatar-kecil {
        width: 40px; height: 40px; border-radius: 12px; background: #eff6ff; color: #2563eb;
        display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: .85rem; flex-shrink: 0;
    }
    .nama-abk { font-weight: 700; color: #1e293b; font-size: .88rem; }
    .jabatan-abk { font-size: .72rem; color: #94a3b8; }
    .badge-shift {
        display: inline-flex; align-items: center; gap: 4px; background: #eff6ff; color: #2563eb;
        padding: 3px 10px; border-radius: 20px; font-size: .7rem; font-weight: 700; margin-top: 4px;
    }
    .badge-akan-datang {
        display: inline-flex; align-items: center; gap: 4px; background: #fffbeb; color: #b45309;
        padding: 2px 8px; border-radius: 20px; font-size: .65rem; font-weight: 700; margin-top: 4px; margin-left: 4px;
    }
    .btn-atur {
        background: #2563eb; color: #fff; border: none; border-radius: 10px;
        padding: 7px 12px; font-size: .75rem; font-weight: 700; flex-shrink: 0;
    }
</style>

<div class="mobile-shell d-lg-none">
    <div class="app-header">
        <a href="{{ route('dashboard.karyawan') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div>
            <h6>Shift Tim</h6>
            <small>{{ $namaDepartemenTampil }}</small>
        </div>
    </div>

    <div class="p-3">
        @if (session('success'))
            <div class="alert alert-success py-2 px-3 mb-3" style="font-size:.8rem; border-radius: 12px;">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.8rem; border-radius: 12px;">{{ session('error') }}</div>
        @endif

        <div class="modern-card">
            @forelse ($anakBuah as $k)
                <div class="anak-buah-row">
                    <div class="avatar-kecil">{{ strtoupper(mb_substr($k->nama_lengkap, 0, 2)) }}</div>
                    <div class="flex-grow-1">
                        <div class="nama-abk">{{ $k->nama_lengkap }}</div>
                        <div class="jabatan-abk">{{ $k->jabatan->nama_jabatan ?? '-' }}</div>
                        <div>
                            <span class="badge-shift"><i class="bi bi-clock"></i> {{ $k->shift_sekarang->nama_shift ?? 'Belum ada shift' }}</span>
                            @if ($k->jadwal_akan_datang->isNotEmpty())
                                <span class="badge-akan-datang"><i class="bi bi-calendar-plus"></i> {{ $k->jadwal_akan_datang->count() }} terjadwal</span>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="btn-atur" data-bs-toggle="modal" data-bs-target="#modalAtur"
                        data-id="{{ $k->id }}" data-nama="{{ $k->nama_lengkap }}" data-shift="{{ $k->shift_id }}">
                        Atur
                    </button>
                </div>
            @empty
                <div class="text-center text-muted py-4" style="font-size:.85rem;">Belum ada karyawan di departemen yang kamu pimpin.</div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .desktop-shell { display: none; }
    @media (min-width: 992px) {
        .desktop-shell { display: block; min-height: 100vh; background: #f8fafc; font-family: 'Inter', 'Segoe UI', sans-serif; padding: 1.75rem 2rem; max-width: 1000px; margin: 0 auto; }
        .page-header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .page-header-row h4 { font-weight: 800; color: #1e293b; margin-bottom: 2px; }
        .page-header-row p { color: #94a3b8; font-size: .85rem; margin: 0; }
        .panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; }
        table.tabel-tim { width: 100%; border-collapse: collapse; }
        table.tabel-tim th { text-align: left; font-size: .72rem; text-transform: uppercase; color: #94a3b8; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; }
        table.tabel-tim td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: .85rem; vertical-align: middle; }
    }
</style>

<div class="desktop-shell">
    <div class="page-header-row">
        <div>
            <h4>Shift Tim</h4>
            <p>Atur jadwal shift karyawan di {{ $namaDepartemenTampil }}</p>
        </div>
        <a href="{{ route('dashboard.karyawan') }}" class="text-decoration-none text-muted" style="font-size:.85rem;"><i class="bi bi-arrow-left me-1"></i>Kembali ke Dashboard</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success py-2 px-3 mb-3" style="font-size:.85rem; border-radius: 10px;">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.85rem; border-radius: 10px;">{{ session('error') }}</div>
    @endif

    <div class="panel">
        <table class="tabel-tim">
            <thead>
                <tr><th>Nama</th><th>Jabatan</th><th>Shift Sekarang</th><th>Terjadwal</th><th></th></tr>
            </thead>
            <tbody>
                @forelse ($anakBuah as $k)
                    <tr>
                        <td class="fw-bold">{{ $k->nama_lengkap }}</td>
                        <td class="text-muted">{{ $k->jabatan->nama_jabatan ?? '-' }}</td>
                        <td><span class="badge-shift"><i class="bi bi-clock"></i> {{ $k->shift_sekarang->nama_shift ?? 'Belum ada shift' }}</span></td>
                        <td>
                            @forelse ($k->jadwal_akan_datang as $j)
                                <div class="badge-akan-datang d-inline-flex mb-1">{{ $j->shift->nama_shift }} &middot; {{ $j->berlaku_mulai->translatedFormat('d M') }}</div>
                            @empty
                                <span class="text-muted" style="font-size:.78rem;">-</span>
                            @endforelse
                        </td>
                        <td>
                            <button type="button" class="btn-atur" data-bs-toggle="modal" data-bs-target="#modalAtur"
                                data-id="{{ $k->id }}" data-nama="{{ $k->nama_lengkap }}" data-shift="{{ $k->shift_id }}">
                                Atur
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada karyawan di departemen yang kamu pimpin.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal atur/jadwalkan shift -->
<div class="modal fade" id="modalAtur" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('shift-tim.store') }}" class="modal-content" style="border-radius: 16px;">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Atur Shift &mdash; <span id="modalNamaKaryawan"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="karyawan_id" id="modalKaryawanId">

                <label class="form-label small fw-bold">Shift</label>
                <select name="shift_id" id="modalShiftId" class="form-select mb-3" required>
                    @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}">{{ $shift->nama_shift }} ({{ substr($shift->jam_masuk,0,5) }} - {{ substr($shift->jam_pulang_default,0,5) }})</option>
                    @endforeach
                </select>

                <label class="form-label small fw-bold">Berlaku Mulai Tanggal</label>
                <input type="date" name="berlaku_mulai" class="form-control mb-3" value="{{ now()->toDateString() }}" required>
                <p class="text-muted" style="font-size:.75rem;">Boleh tanggal ke depan, untuk disiapkan dari sekarang. Jadwal yang sudah disiapkan sebelumnya untuk tanggal yang sama atau sesudahnya akan diganti dengan yang ini.</p>

                <label class="form-label small fw-bold">Keterangan (opsional)</label>
                <input type="text" name="keterangan" class="form-control" placeholder="Misal: tukar jadwal dengan Budi">
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px;">Batal</button>
                <button type="submit" class="btn btn-biru" style="border-radius:10px;">Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('modalAtur').addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        document.getElementById('modalKaryawanId').value = btn.dataset.id;
        document.getElementById('modalNamaKaryawan').textContent = btn.dataset.nama;
        var selectShift = document.getElementById('modalShiftId');
        if (btn.dataset.shift) selectShift.value = btn.dataset.shift;
    });
</script>
@endpush

@endsection