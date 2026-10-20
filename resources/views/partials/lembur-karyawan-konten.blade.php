@once
<style>
    .kartu-lembur { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.2rem; transition: all 0.2s; }
    .kartu-lembur:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .badge-lembur { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: .75rem; font-weight: 700; white-space: nowrap; }
    .timeline-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 8px; }
</style>
@endonce

@php
    $statusLembur = [
        'menunggu' => ['warna' => '#f59e0b', 'bg' => '#fffbeb', 'label' => 'Menunggu Validasi', 'icon' => 'bi-hourglass-split'],
        'disetujui' => ['warna' => '#16a34a', 'bg' => '#f0fdf4', 'label' => 'Disetujui', 'icon' => 'bi-check-circle-fill'],
        'ditolak' => ['warna' => '#ef4444', 'bg' => '#fef2f2', 'label' => 'Ditolak', 'icon' => 'bi-x-circle-fill'],
    ];
@endphp

{{-- Banner Informasi --}}
<div class="alert alert-info d-flex align-items-center gap-3 mb-4" style="border-radius: 14px; font-size: 0.85rem; background-color: #eff6ff; border-color: #bfdbfe; color: #1e40af;">
    <i class="bi bi-info-circle-fill fs-5"></i>
    <div>
        <strong>Informasi:</strong> Pengajuan lembur saat ini dikelola langsung oleh <strong>Kepala Bagian</strong>. 
        Halaman ini hanya menampilkan jadwal dan riwayat lembur Anda.
    </div>
</div>

<div class="fw-bold mb-3" style="color:#1e293b; font-size: 1.1rem;">Jadwal & Riwayat Lembur</div>

@forelse ($riwayat as $l)
    @php $st = $statusLembur[$l->status] ?? $statusLembur['menunggu']; @endphp
    
    <div class="kartu-lembur mb-3">
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
            
            {{-- Info Kiri --}}
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="timeline-dot" style="background: {{ $st['warna'] }};"></span>
                    <div class="fw-bold" style="font-size:.95rem; color: #1e293b;">
                        {{ \Carbon\Carbon::parse($l->tanggal)->translatedFormat('l, d F Y') }}
                    </div>
                </div>
                
                <div class="text-muted mb-2" style="font-size:.85rem; margin-left: 18px;">
                    <i class="bi bi-clock me-1"></i>
                    {{ $l->jam_mulai ? substr($l->jam_mulai, 0, 5) : '--:--' }}
                    <span class="mx-1">&ndash;</span>
                    {{ $l->jam_selesai ? substr($l->jam_selesai, 0, 5) : 'Belum Selesai' }}
                    
                    @if($l->durasi_final_jam) 
                        <span class="badge bg-light text-dark border ms-2">{{ $l->durasi_final_jam }} Jam</span>
                    @elseif($l->estimasi_jam)
                        <span class="badge bg-light text-dark border ms-2">Est. {{ $l->estimasi_jam }} Jam</span>
                    @endif
                </div>

                {{-- Alasan / Catatan --}}
                <div class="p-2 rounded mb-2" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size:.82rem; margin-left: 18px;">
                    <i class="bi bi-briefcase text-muted me-1"></i> {{ $l->catatan }}
                </div>

                {{-- Catatan Payroll --}}
                @if ($l->catatan_payroll)
                    <div class="mt-2" style="font-size:.78rem; color:#475569; margin-left: 18px;">
                        <i class="bi bi-chat-left-text text-primary me-1"></i> 
                        <strong>Payroll:</strong> {{ $l->catatan_payroll }}
                    </div>
                @endif
            </div>

            {{-- Status Kanan --}}
            <div class="text-end">
                <span class="badge-lembur" style="background: {{ $st['bg'] }}; color: {{ $st['warna'] }}; border: 1px solid {{ $st['warna'] }}40;">
                    <i class="bi {{ $st['icon'] }} me-1"></i> {{ $st['label'] }}
                </span>
                
                @if ($l->status === 'disetujui' && $l->total_upah)
                    <div class="mt-2" style="font-size:.85rem; color:#16a34a; font-weight:800;">
                        Rp {{ number_format($l->total_upah, 0, ',', '.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@empty
    <div class="kartu-lembur text-center py-5">
        <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 50%;">
            <i class="bi bi-calendar-x fs-2 text-muted"></i>
        </div>
        <h6 class="fw-bold text-dark mb-1">Belum Ada Jadwal Lembur</h6>
        <p class="text-muted mb-0" style="font-size:.85rem;">Anda belum memiliki jadwal atau riwayat lembur saat ini.</p>
    </div>
@endforelse