@once
<style>
    .kartu-lembur { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1rem; }
    .badge-lembur { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: .72rem; font-weight: 700; white-space: nowrap; }
    .tab-status { padding: 6px 14px; border-radius: 20px; font-size: .78rem; font-weight: 700; text-decoration: none; background: #f1f5f9; color: #64748b; }
    .tab-status.aktif { background: #2563eb; color: #fff; }
</style>
@endonce

@php
    $statusLembur = [
        'menunggu' => ['warna' => '#f59e0b', 'label' => 'Menunggu'],
        'disetujui' => ['warna' => '#16a34a', 'label' => 'Disetujui'],
        'ditolak' => ['warna' => '#ef4444', 'label' => 'Ditolak'],
    ];
    $tabStatus = ['menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'semua' => 'Semua'];
@endphp

@if (session('success'))
    <div class="alert alert-success py-2 px-3 mb-3" style="font-size:.82rem; border-radius:12px;">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.82rem; border-radius:12px;">{{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.82rem; border-radius:12px;">{{ $errors->first() }}</div>
@endif

<div class="d-flex flex-wrap gap-2 mb-3">
    @foreach ($tabStatus as $kode => $label)
        <a href="{{ route('lembur.payroll', ['status' => $kode]) }}" class="tab-status {{ $status === $kode ? 'aktif' : '' }}">{{ $label }}</a>
    @endforeach
</div>

@forelse ($lemburs as $l)
    @php
        $st = $statusLembur[$l->status] ?? $statusLembur['menunggu'];
        $milikSendiri = $l->karyawan_id === $idKaryawanLogin;
        $dikoreksi = $l->status === 'disetujui' && $l->jam_selesai_scan
            && substr($l->jam_selesai, 0, 5) !== substr($l->jam_selesai_scan, 0, 5);
    @endphp
    <div class="kartu-lembur mb-3">
        <div class="d-flex justify-content-between align-items-start gap-2">
            <div>
                <div class="fw-bold" style="font-size:.9rem;">{{ $l->karyawan->nama_lengkap ?? '-' }}</div>
                <div class="text-muted" style="font-size:.74rem;">{{ $l->karyawan->nik_kerja ?? '-' }} &middot; {{ $l->karyawan->departemen->nama_departemen ?? '-' }}</div>
            </div>
            <span class="badge-lembur" style="background: {{ $st['warna'] }}1a; color: {{ $st['warna'] }};">{{ $st['label'] }}</span>
        </div>

        <div class="mt-2" style="font-size:.82rem; font-weight:600; color:#1e293b;">
            {{ \Carbon\Carbon::parse($l->tanggal)->translatedFormat('l, d M Y') }}
        </div>
        <div class="text-muted" style="font-size:.78rem;">{{ $l->catatan }}</div>

        <div class="mt-2 p-2 rounded-3" style="background:#f8fafc; font-size:.78rem;">
            <div class="text-muted" style="font-size:.7rem; font-weight:600;">DATA SISTEM</div>
            @if ($l->jam_selesai_scan)
                {{ $l->jam_mulai ? substr($l->jam_mulai, 0, 5) : '--:--' }} &ndash; {{ substr($l->jam_selesai_scan, 0, 5) }}
                <span class="text-muted">(jam selesai dari absen pulang)</span>
            @else
                <span class="text-muted">Belum ada absen pulang lembur.</span>
            @endif
        </div>

        @if ($l->status === 'menunggu')
            @if ($milikSendiri)
                <div class="mt-2 text-muted" style="font-size:.76rem;">Ini lembur milikmu, diproses oleh payroll lain.</div>
            @else
                <form method="POST" action="{{ route('lembur.payroll.setujui', $l->id) }}" class="mt-3">
                    @csrf
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label mb-1" style="font-size:.72rem; font-weight:700;">Jam mulai (sesuai surat)</label>
                            <input type="time" name="jam_mulai" class="form-control form-control-sm" value="{{ $l->jam_mulai ? substr($l->jam_mulai, 0, 5) : '' }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label mb-1" style="font-size:.72rem; font-weight:700;">Jam selesai (sesuai surat)</label>
                            <input type="time" name="jam_selesai" class="form-control form-control-sm" value="{{ $l->jam_selesai ? substr($l->jam_selesai, 0, 5) : '' }}" required>
                        </div>
                    </div>
                    <input type="text" name="catatan_payroll" maxlength="255" class="form-control form-control-sm mb-2" placeholder="Catatan payroll (wajib kalau menolak)">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-biru flex-fill" style="border-radius:10px;"
                            onclick="return confirm('Setujui lembur ini sesuai jam di atas?')">Setujui</button>
                        <button type="submit" formaction="{{ route('lembur.payroll.tolak', $l->id) }}" formnovalidate
                            class="btn btn-sm btn-light flex-fill" style="border-radius:10px; color:#ef4444;"
                            onclick="return confirm('Tolak pengajuan lembur ini?')">Tolak</button>
                    </div>
                </form>
            @endif
        @else
            <div class="mt-2" style="font-size:.78rem;">
                @if ($l->status === 'disetujui')
                    <strong>{{ substr($l->jam_mulai, 0, 5) }} &ndash; {{ substr($l->jam_selesai, 0, 5) }}</strong>
                    &middot; {{ $l->durasi_jam }} jam &middot; Rp {{ number_format($l->total_upah ?? 0, 0, ',', '.') }}
                    @if ($dikoreksi) <span class="badge-lembur ms-1" style="background:#fef3c7; color:#b45309;">Dikoreksi</span> @endif
                @endif
                @if ($l->catatan_payroll)
                    <div class="text-muted mt-1"><i class="bi bi-chat-left-text me-1"></i>{{ $l->catatan_payroll }}</div>
                @endif
            </div>
        @endif
    </div>
@empty
    <div class="kartu-lembur text-center text-muted" style="font-size:.85rem;">Tidak ada pengajuan lembur di status ini.</div>
@endforelse

@if ($lemburs->hasPages())
    <div class="mt-2">{{ $lemburs->links() }}</div>
@endif