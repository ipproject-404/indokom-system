@once
<style>
    .kartu-lembur { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1rem; }
    .badge-lembur { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: .72rem; font-weight: 700; white-space: nowrap; }
</style>
@endonce

@php
    $statusLembur = [
        'menunggu' => ['warna' => '#f59e0b', 'label' => 'Menunggu'],
        'disetujui' => ['warna' => '#16a34a', 'label' => 'Disetujui'],
        'ditolak' => ['warna' => '#ef4444', 'label' => 'Ditolak'],
    ];
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

<form method="POST" action="{{ route('lembur.karyawan.store') }}" class="kartu-lembur mb-4">
    @csrf
    <div class="fw-bold mb-2" style="color:#1e293b;">Ajukan Lembur</div>

    <label class="form-label small fw-bold">Tanggal</label>
    <input type="date" name="tanggal" class="form-control mb-2" value="{{ old('tanggal', now()->toDateString()) }}" min="{{ now()->toDateString() }}" required>

    <label class="form-label small fw-bold">Alasan / pekerjaan</label>
    <textarea name="catatan" rows="2" maxlength="255" class="form-control mb-2" placeholder="Misal: mesin 2 rusak, perbaikan" required>{{ old('catatan') }}</textarea>

    <p class="text-muted mb-3" style="font-size:.74rem;">
        Jam mulai dihitung setelah jam pulang shift. Jam selesai otomatis dari absen pulangmu.
        Besoknya, serahkan surat lembur ke payroll untuk dicocokkan.
    </p>
    <button type="submit" class="btn btn-biru w-100" style="border-radius:12px;">Ajukan Lembur</button>
</form>

<div class="fw-bold mb-2" style="color:#1e293b;">Riwayat</div>

@forelse ($riwayat as $l)
    @php $st = $statusLembur[$l->status] ?? $statusLembur['menunggu']; @endphp
    <div class="kartu-lembur mb-2">
        <div class="d-flex justify-content-between align-items-start gap-2">
            <div>
                <div class="fw-bold" style="font-size:.88rem;">{{ \Carbon\Carbon::parse($l->tanggal)->translatedFormat('l, d M Y') }}</div>
                <div class="text-muted" style="font-size:.78rem;">
                    {{ $l->jam_mulai ? substr($l->jam_mulai, 0, 5) : '--:--' }}
                    &ndash;
                    {{ $l->jam_selesai ? substr($l->jam_selesai, 0, 5) : 'belum selesai' }}
                    @if ($l->durasi_jam) &middot; {{ $l->durasi_jam }} jam @endif
                </div>
            </div>
            <span class="badge-lembur" style="background: {{ $st['warna'] }}1a; color: {{ $st['warna'] }};">{{ $st['label'] }}</span>
        </div>

        @if ($l->status === 'disetujui' && $l->total_upah)
            <div class="mt-1" style="font-size:.78rem; color:#16a34a; font-weight:600;">Rp {{ number_format($l->total_upah, 0, ',', '.') }}</div>
        @endif

        <div class="mt-2 text-muted" style="font-size:.78rem;">{{ $l->catatan }}</div>

        @if ($l->catatan_payroll)
            <div class="mt-1" style="font-size:.76rem; color:#475569;"><i class="bi bi-chat-left-text me-1"></i>Payroll: {{ $l->catatan_payroll }}</div>
        @endif

        @if ($l->status === 'menunggu' && ! $l->presensi_id)
            <form method="POST" action="{{ route('lembur.karyawan.batal', $l->id) }}" class="mt-2" onsubmit="return confirm('Batalkan pengajuan lembur ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-light" style="border-radius:10px; color:#ef4444;">Batalkan</button>
            </form>
        @endif
    </div>
@empty
    <div class="kartu-lembur text-center text-muted" style="font-size:.85rem;">Belum ada pengajuan lembur.</div>
@endforelse