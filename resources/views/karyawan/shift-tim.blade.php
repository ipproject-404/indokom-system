@extends('layouts.app')

@section('title', 'Shift Tim')

@section('content')

@php
    $namaDepartemenTampil = $departemenDipimpin->pluck('nama_departemen')->implode(', ');
    $hariIni = \Carbon\Carbon::today();
    $namaHari = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    $labelMinggu = $hari->first()->translatedFormat('d M') . ' - ' . $hari->last()->translatedFormat('d M Y');

    // Info satu sel jadwal (karyawan x tanggal), dipakai versi HP & desktop.
    $infoSel = function ($k, $tgl) use ($jadwal, $adaPresensi, $hariIni) {
        $tanggal = $tgl->toDateString();
        $j = $jadwal->get($k->id)?->get($tanggal);

        return [
            'shift' => $j?->shift,
            'ada' => (bool) $j,
            'kunci' => $tgl->lt($hariIni) || $adaPresensi->has($k->id . '|' . $tanggal),
        ];
    };
@endphp

<style>
    html, body { margin: 0; padding: 0; width: 100%; overflow-x: hidden; }
    body { background-color: #f1f5f9; font-family: 'Inter', 'Segoe UI', sans-serif; }
    .app-shell { max-width: none; margin: 0; box-shadow: none; padding-bottom: 0; }

    .mobile-shell { width: 100%; max-width: 100%; margin: 0; background: #ffffff; min-height: 100vh; box-shadow: none; padding-bottom: 90px; }
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

    .modern-card { background: #fff; border-radius: 18px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.04); overflow: hidden; }
    .avatar-kecil {
        width: 40px; height: 40px; border-radius: 12px; background: #eff6ff; color: #2563eb;
        display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: .85rem; flex-shrink: 0;
    }
    .nama-abk { font-weight: 700; color: #1e293b; font-size: .88rem; }
    .jabatan-abk { font-size: .72rem; color: #94a3b8; }

    .minggu-bar { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
    .minggu-bar .nav-btn {
        width: 34px; height: 34px; border-radius: 10px; background: #f1f5f9; color: #475569;
        display: flex; align-items: center; justify-content: center; text-decoration: none; flex-shrink: 0;
    }
    .minggu-bar .label { font-weight: 700; color: #1e293b; font-size: .85rem; text-align: center; }

    .btn-aksi { border: none; border-radius: 10px; padding: 8px 14px; font-size: .78rem; font-weight: 700; }
    .btn-aksi.utama { background: #2563eb; color: #fff; }
    .btn-aksi.sekunder { background: #eff6ff; color: #2563eb; }

    .sel-jadwal {
        border: none; background: transparent; width: 100%; text-align: left; cursor: pointer;
    }
    .sel-jadwal:disabled { cursor: not-allowed; opacity: .55; }
    .badge-shift {
        display: inline-flex; align-items: center; background: #eff6ff; color: #2563eb;
        padding: 3px 10px; border-radius: 20px; font-size: .72rem; font-weight: 700;
    }
    .badge-libur { background: #f1f5f9; color: #64748b; }
    .badge-kosong { background: transparent; color: #cbd5e1; }
    .baris-hari { display: flex; justify-content: space-between; align-items: center; padding: 9px 14px; border-top: 1px solid #f1f5f9; font-size: .8rem; color: #475569; }
</style>

{{-- ================= VERSI HP ================= --}}
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
        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.8rem; border-radius: 12px;">{{ $errors->first() }}</div>
        @endif

        <div class="modern-card p-3 mb-3">
            <div class="minggu-bar mb-3">
                <a href="{{ route('shift-tim.index', ['minggu' => $mingguSebelumnya]) }}" class="nav-btn"><i class="bi bi-chevron-left"></i></a>
                <div class="label">{{ $labelMinggu }}</div>
                <a href="{{ route('shift-tim.index', ['minggu' => $mingguBerikutnya]) }}" class="nav-btn"><i class="bi bi-chevron-right"></i></a>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn-aksi utama flex-fill" data-bs-toggle="modal" data-bs-target="#modalAtur"><i class="bi bi-calendar-plus me-1"></i> Atur Jadwal</button>
                <button type="button" class="btn-aksi sekunder flex-fill" data-bs-toggle="modal" data-bs-target="#modalTukar"><i class="bi bi-arrow-left-right me-1"></i> Tukar Shift</button>
            </div>
        </div>

        @forelse ($anakBuah as $k)
            <div class="modern-card mb-3">
                <div class="d-flex align-items-center gap-3 p-3">
                    <div class="avatar-kecil">{{ strtoupper(mb_substr($k->nama_lengkap, 0, 2)) }}</div>
                    <div>
                        <div class="nama-abk">{{ $k->nama_lengkap }}</div>
                        <div class="jabatan-abk">{{ $k->jabatan->nama_jabatan ?? '-' }}</div>
                    </div>
                </div>
                @foreach ($hari as $i => $tgl)
                    @php $info = $infoSel($k, $tgl); @endphp
                    <button type="button" class="sel-jadwal baris-hari" {{ $info['kunci'] ? 'disabled' : '' }}
                        data-bs-toggle="modal" data-bs-target="#modalAtur"
                        data-id="{{ $k->id }}" data-tanggal="{{ $tgl->toDateString() }}" data-shift="{{ $info['shift']->id ?? '' }}">
                        <span>{{ $namaHari[$i] }}, {{ $tgl->format('d/m') }}</span>
                        @if ($info['shift'])
                            <span class="badge-shift">{{ $info['shift']->nama_shift }} {{ substr($info['shift']->jam_masuk, 0, 5) }}</span>
                        @elseif ($info['ada'])
                            <span class="badge-shift badge-libur">Libur</span>
                        @else
                            <span class="badge-shift badge-kosong">belum diatur</span>
                        @endif
                    </button>
                @endforeach
            </div>
        @empty
            <div class="modern-card p-4 text-center text-muted" style="font-size:.85rem;">
                Belum ada anak buah yang jadwal shift-nya diaktifkan.
                @if ($tanpaJadwal > 0) <br>{{ $tanpaJadwal }} karyawan di departemenmu memakai shift tetap. @endif
                <br>Minta HRD mencentang "Pakai jadwal shift" di data karyawan.
            </div>
        @endforelse
    </div>
        @include('partials.bottom-nav-karyawan')
</div>

{{-- ================= VERSI DESKTOP ================= --}}
<style>
    .desktop-shell { display: none; }
    @media (min-width: 992px) {
        .desktop-shell { display: block; min-height: 100vh; background: #f8fafc; font-family: 'Inter', 'Segoe UI', sans-serif; padding: 1.75rem 2rem; max-width: 1200px; margin: 0 auto; }
        .page-header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; }
        .page-header-row h4 { font-weight: 800; color: #1e293b; margin-bottom: 2px; }
        .page-header-row p { color: #94a3b8; font-size: .85rem; margin: 0; }
        .panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; }
        table.tabel-tim { width: 100%; border-collapse: collapse; }
        table.tabel-tim th { text-align: center; font-size: .72rem; text-transform: uppercase; color: #94a3b8; padding: 12px 8px; border-bottom: 1px solid #e2e8f0; }
        table.tabel-tim th:first-child { text-align: left; padding-left: 16px; }
        table.tabel-tim td { padding: 6px; border-bottom: 1px solid #f1f5f9; font-size: .8rem; vertical-align: middle; text-align: center; }
        table.tabel-tim td:first-child { text-align: left; padding: 10px 16px; }
        table.tabel-tim .sel-jadwal { text-align: center; padding: 8px 4px; border-radius: 10px; }
        table.tabel-tim .sel-jadwal:not(:disabled):hover { background: #eff6ff; }
        table.tabel-tim th.hari-ini { color: #2563eb; }
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
    @if ($errors->any())
        <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.85rem; border-radius: 10px;">{{ $errors->first() }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="minggu-bar gap-3">
            <a href="{{ route('shift-tim.index', ['minggu' => $mingguSebelumnya]) }}" class="nav-btn"><i class="bi bi-chevron-left"></i></a>
            <div class="label">{{ $labelMinggu }}</div>
            <a href="{{ route('shift-tim.index', ['minggu' => $mingguBerikutnya]) }}" class="nav-btn"><i class="bi bi-chevron-right"></i></a>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn-aksi utama" data-bs-toggle="modal" data-bs-target="#modalAtur"><i class="bi bi-calendar-plus me-1"></i> Atur Jadwal</button>
            <button type="button" class="btn-aksi sekunder" data-bs-toggle="modal" data-bs-target="#modalTukar"><i class="bi bi-arrow-left-right me-1"></i> Tukar Shift</button>
        </div>
    </div>

    <div class="panel">
        <table class="tabel-tim">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    @foreach ($hari as $i => $tgl)
                        <th class="{{ $tgl->isSameDay($hariIni) ? 'hari-ini' : '' }}">{{ $namaHari[$i] }}<br>{{ $tgl->format('d/m') }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($anakBuah as $k)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $k->nama_lengkap }}</div>
                            <div class="text-muted" style="font-size:.72rem;">{{ $k->jabatan->nama_jabatan ?? '-' }}</div>
                        </td>
                        @foreach ($hari as $tgl)
                            @php $info = $infoSel($k, $tgl); @endphp
                            <td>
                                <button type="button" class="sel-jadwal" {{ $info['kunci'] ? 'disabled' : '' }}
                                    data-bs-toggle="modal" data-bs-target="#modalAtur"
                                    data-id="{{ $k->id }}" data-tanggal="{{ $tgl->toDateString() }}" data-shift="{{ $info['shift']->id ?? '' }}">
                                    @if ($info['shift'])
                                        <span class="badge-shift">{{ $info['shift']->nama_shift }}</span>
                                        <div class="text-muted" style="font-size:.68rem;">{{ substr($info['shift']->jam_masuk, 0, 5) }}</div>
                                    @elseif ($info['ada'])
                                        <span class="badge-shift badge-libur">Libur</span>
                                    @else
                                        <span class="badge-shift badge-kosong">-</span>
                                    @endif
                                </button>
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">
                        Belum ada anak buah yang jadwal shift-nya diaktifkan.
                        @if ($tanpaJadwal > 0) {{ $tanpaJadwal }} karyawan di departemenmu memakai shift tetap. @endif
                        Minta HRD mencentang "Pakai jadwal shift" di data karyawan.
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ================= MODAL ATUR JADWAL ================= --}}
<div class="modal fade" id="modalAtur" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('shift-tim.store') }}" class="modal-content" style="border-radius: 16px;">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Atur Jadwal Shift</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label small fw-bold">Karyawan</label>
                <div class="border rounded-3 p-2 mb-3" style="max-height: 150px; overflow-y: auto;">
                    @foreach ($anakBuah as $k)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="karyawan_ids[]" value="{{ $k->id }}" id="atur-k-{{ $k->id }}">
                            <label class="form-check-label small" for="atur-k-{{ $k->id }}">{{ $k->nama_lengkap }}</label>
                        </div>
                    @endforeach
                </div>

                <label class="form-label small fw-bold">Shift</label>
                <select name="shift_id" class="form-select mb-3">
                    <option value="">Libur</option>
                    @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}">{{ $shift->nama_shift }} ({{ substr($shift->jam_masuk, 0, 5) }} - {{ substr($shift->jam_pulang_default, 0, 5) }})</option>
                    @endforeach
                </select>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Dari Tanggal</label>
                        <input type="date" name="tanggal_mulai" class="form-control" value="{{ $hariIni->toDateString() }}" min="{{ $hariIni->toDateString() }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Sampai Tanggal</label>
                        <input type="date" name="tanggal_selesai" class="form-control" value="{{ $hariIni->toDateString() }}" min="{{ $hariIni->toDateString() }}" required>
                    </div>
                </div>
                <p class="text-muted" style="font-size:.74rem;">Tanggal yang sudah lewat tidak bisa diubah. Tanggal yang sudah ada absensinya dilewati otomatis.</p>

                <label class="form-label small fw-bold">Keterangan (opsional)</label>
                <input type="text" name="keterangan" class="form-control" maxlength="255">
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px;">Batal</button>
                <button type="submit" class="btn btn-biru" style="border-radius:10px;">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL TUKAR SHIFT ================= --}}
<div class="modal fade" id="modalTukar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('shift-tim.tukar') }}" class="modal-content" style="border-radius: 16px;">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Tukar Shift</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label small fw-bold">Karyawan A</label>
                <select name="karyawan_a_id" class="form-select mb-3" required>
                    <option value="">Pilih karyawan...</option>
                    @foreach ($anakBuah as $k)
                        <option value="{{ $k->id }}" {{ old('karyawan_a_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_lengkap }}</option>
                    @endforeach
                </select>

                <label class="form-label small fw-bold">Karyawan B</label>
                <select name="karyawan_b_id" class="form-select mb-3" required>
                    <option value="">Pilih karyawan...</option>
                    @foreach ($anakBuah as $k)
                        <option value="{{ $k->id }}" {{ old('karyawan_b_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_lengkap }}</option>
                    @endforeach
                </select>

                <label class="form-label small fw-bold">Tanggal</label>
                <input type="date" name="tanggal" class="form-control mb-1" value="{{ old('tanggal', $hariIni->toDateString()) }}" min="{{ $hariIni->toDateString() }}" required>
                <p class="text-muted mb-3" style="font-size:.74rem;">Shift (atau libur) A dan B di tanggal ini saling ditukar. Jadwal hari lain tidak berubah.</p>

                <label class="form-label small fw-bold">Keterangan (opsional)</label>
                <input type="text" name="keterangan" class="form-control" maxlength="255" placeholder="Misal: A ada keperluan keluarga">
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px;">Batal</button>
                <button type="submit" class="btn btn-biru" style="border-radius:10px;">Tukar</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Klik sel jadwal -> modal terisi karyawan, tanggal, dan shift sel itu.
    // Dibuka dari tombol "Atur Jadwal" -> form kosong (default hari ini).
    document.getElementById('modalAtur').addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        this.querySelectorAll('input[name="karyawan_ids[]"]').forEach(function (c) { c.checked = false; });

        if (btn && btn.dataset.id) {
            var cek = this.querySelector('input[name="karyawan_ids[]"][value="' + btn.dataset.id + '"]');
            if (cek) cek.checked = true;
            this.querySelector('[name="tanggal_mulai"]').value = btn.dataset.tanggal;
            this.querySelector('[name="tanggal_selesai"]').value = btn.dataset.tanggal;
            this.querySelector('[name="shift_id"]').value = btn.dataset.shift || '';
        }
    });
</script>
@endpush

@endsection