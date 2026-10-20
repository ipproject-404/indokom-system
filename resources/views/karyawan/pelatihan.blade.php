@extends('layouts.app')

@section('title', 'Pelatihan Saya')

@section('content')
@php
    $namaTampil = $karyawan->nama_lengkap ?? Auth::user()->username;
    $jabatanTampil = $karyawan->jabatan->nama_jabatan ?? ucfirst(Auth::user()->role);
@endphp

<style>
    /* ⬇️ Mematikan batasan lebar dari layouts.app ⬇️ */
    .app-shell {
        max-width: none !important;
        margin: 0 !important;
        box-shadow: none !important;
        padding-bottom: 0 !important;
    }

    /* Mobile style */
    .mobile-shell { max-width: 480px; margin: 0 auto; background: #f8fafc; min-height: 100vh; padding-bottom: 80px; position: relative; box-shadow: 0 0 30px rgba(0,0,0,0.08); }
    .mobile-shell .app-header { background: #fff; padding: 1.2rem 1rem; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 10; }
    .modern-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; overflow: hidden; }
    
    /* Desktop style */
    .desktop-shell { display: none; }
    @media (min-width: 992px) {
        .desktop-shell { display: flex; min-height: 100vh; background: #f8fafc; font-family: 'Inter', 'Segoe UI', sans-serif; }
        .desktop-main { flex-grow: 1; padding: 1.75rem 2rem; max-width: 1200px; margin: 0 auto; width: 100%; }
        .page-header-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; }
        .page-header-row h4 { font-weight: 800; color: #1e293b; margin-bottom: 2px; }
        .page-header-row p { color: #94a3b8; font-size: 0.85rem; margin: 0; }
        .date-pill { background: #eef2ff; color: #2563eb; font-weight: 600; font-size: 0.82rem; padding: 8px 16px; border-radius: 10px; }
    }
</style>

<!-- ==================== MOBILE VIEW ==================== -->
<div class="mobile-shell d-lg-none">
    <div class="app-header d-flex align-items-center gap-3">
        <a href="{{ route('dashboard.karyawan') }}" class="text-dark text-decoration-none">
            <i class="bi bi-arrow-left fs-4"></i>
        </a>
        <div>
            <div class="fw-bold text-dark lh-sm">Pelatihan Saya</div>
            <div class="small text-muted">Riwayat & Sertifikasi</div>
        </div>
    </div>

    <div class="p-3">
        @if(session('success'))
            <div class="alert alert-success py-2 px-3 mb-3" style="font-size:.82rem; border-radius:12px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.82rem; border-radius:12px;">{{ session('error') }}</div>
        @endif

        @forelse($peserta as $p)
            @php
                $pel = $p->pelatihan;
                $sertif = $p->sertifikat; // Ambil data sertifikat
                $statusBadge = match($pel->status) {
                    'selesai' => 'bg-secondary',
                    'dibatalkan' => 'bg-danger',
                    default => $pel->sedangBerjalan() ? 'bg-success' : 'bg-primary'
                };
                $statusText = match($pel->status) {
                    'selesai' => 'Selesai',
                    'dibatalkan' => 'Dibatalkan',
                    default => $pel->sedangBerjalan() ? 'Sedang Berjalan' : 'Akan Datang'
                };
            @endphp
            <div class="modern-card mb-3 p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                    @if($sertif && $sertif->status === 'lulus')
                        <span class="badge bg-success"><i class="bi bi-award-fill"></i> Lulus</span>
                    @endif
                </div>
                <h6 class="fw-bold mb-1">{{ $pel->nama_pelatihan }}</h6>
                <div class="text-muted small mb-2">
                    <i class="bi bi-calendar-event"></i> {{ $pel->tanggal_mulai->translatedFormat('d M Y') }}
                    @if($pel->tanggal_selesai && !$pel->tanggal_mulai->isSameDay($pel->tanggal_selesai))
                        - {{ $pel->tanggal_selesai->translatedFormat('d M Y') }}
                    @endif
                </div>

                {{-- ⬇️ TOMBOL ABSEN HADIR MOBILE ⬇️ --}}
                @if($pel->status === 'aktif' && $pel->sedangBerjalan())
                    <div class="mb-3">
                        @if($p->status_kehadiran === 'hadir')
                            <div class="alert alert-success py-2 mb-0 small fw-bold border-0">
                                <i class="bi bi-check-circle-fill me-1"></i> Hadir ({{ \Carbon\Carbon::parse($p->waktu_hadir)->format('H:i') }})
                            </div>
                        @else
                            <form method="POST" action="{{ route('pelatihan.karyawan.hadir', $p->id) }}">
                                @csrf
                                <button type="submit" class="btn w-100 fw-bold text-dark border-0 shadow-sm" style="background-color: #f59e0b;">
                                    <i class="bi bi-fingerprint me-1"></i> Klik Hadir Sekarang
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
                
                @if($sertif && $sertif->nilai_postest !== null)
                    <div class="bg-light rounded p-2 mb-2 d-flex justify-content-between small">
                        <div>Pre: <strong>{{ number_format($sertif->nilai_pretest ?? 0, 1) }}</strong></div>
                        <div>Post: <strong>{{ number_format($sertif->nilai_postest ?? 0, 1) }}</strong></div>
                        <div>Status: <strong class="{{ $sertif->status === 'lulus' ? 'text-success' : 'text-danger' }}">{{ ucfirst(str_replace('_', ' ', $sertif->status)) }}</strong></div>
                    </div>
                    
                    @if($sertif->status === 'lulus')
                        <a href="{{ route('sertifikasi.cetak', $sertif->id) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 fw-bold">
                            <i class="bi bi-download"></i> Cetak Sertifikat
                        </a>
                    @endif
                @elseif($pel->status === 'selesai')
                    <div class="alert alert-warning py-1 px-2 mb-0 small">
                        <i class="bi bi-info-circle me-1"></i> Nilai belum diinput HRD.
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-2">Belum ada riwayat pelatihan.</p>
            </div>
        @endforelse
    </div>
    @include('partials.bottom-nav-karyawan')
</div>

<!-- ==================== DESKTOP VIEW ==================== -->
<div class="desktop-shell">
    @include('partials.sidebar-karyawan')

    <main class="desktop-main">
        <div class="page-header-row">
            <div>
                <h4>Pelatihan Saya</h4>
                <p>Riwayat pelatihan dan sertifikasi kompetensi Anda</p>
            </div>
            <div class="date-pill"><i class="bi bi-award me-1"></i> {{ $peserta->count() }} Pelatihan</div>
        </div>

        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-3 rounded-3 small">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 mb-3 rounded-3 small">
                <i class="bi bi-x-circle-fill fs-5 me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <div class="row g-3">
            @forelse($peserta as $p)
                @php
                    $pel = $p->pelatihan;
                    $sertif = $p->sertifikat; // Ambil data sertifikat
                    $statusBadge = match($pel->status) {
                        'selesai' => 'bg-secondary',
                        'dibatalkan' => 'bg-danger',
                        default => $pel->sedangBerjalan() ? 'bg-success' : 'bg-primary'
                    };
                    $statusText = match($pel->status) {
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                        default => $pel->sedangBerjalan() ? 'Sedang Berjalan' : 'Akan Datang'
                    };
                @endphp
                <div class="col-md-6">
                    <div class="panel h-100 p-3" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                            @if($sertif && $sertif->status === 'lulus')
                                <span class="badge bg-success"><i class="bi bi-award-fill"></i> Lulus</span>
                            @endif
                        </div>
                        <h6 class="fw-bold mb-1" style="color: #1e293b;">{{ $pel->nama_pelatihan }}</h6>
                        <div class="text-muted small mb-3">
                            <i class="bi bi-calendar-event"></i> {{ $pel->tanggal_mulai->translatedFormat('d F Y') }}
                            @if($pel->tanggal_selesai && !$pel->tanggal_mulai->isSameDay($pel->tanggal_selesai))
                                - {{ $pel->tanggal_selesai->translatedFormat('d F Y') }}
                            @endif
                            &bull; <i class="bi bi-building"></i> {{ $pel->penyelenggara ?? 'Internal' }}
                        </div>

                        {{-- ⬇️ TOMBOL ABSEN HADIR DESKTOP ⬇️ --}}
                        @if($pel->status === 'aktif' && $pel->sedangBerjalan())
                            <div class="mb-3">
                                @if($p->status_kehadiran === 'hadir')
                                    <div class="alert alert-success py-2 mb-0 small fw-bold border-0">
                                        <i class="bi bi-check-circle-fill me-1"></i> Anda sudah tercatat Hadir ({{ \Carbon\Carbon::parse($p->waktu_hadir)->format('H:i') }})
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('pelatihan.karyawan.hadir', $p->id) }}">
                                        @csrf
                                        <button type="submit" class="btn w-100 fw-bold text-dark border-0 shadow-sm" style="background-color: #f59e0b;">
                                            <i class="bi bi-fingerprint me-1"></i> Klik Hadir Sekarang
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif

                        @if($sertif && $sertif->nilai_postest !== null)
                            <div class="d-flex justify-content-between bg-light rounded p-2 mb-3 text-center">
                                <div class="flex-fill">
                                    <div class="small text-muted">Pretest</div>
                                    <div class="fw-bold">{{ number_format($sertif->nilai_pretest ?? 0, 1) }}</div>
                                </div>
                                <div class="border-end mx-2"></div>
                                <div class="flex-fill">
                                    <div class="small text-muted">Postest</div>
                                    <div class="fw-bold">{{ number_format($sertif->nilai_postest ?? 0, 1) }}</div>
                                </div>
                                <div class="border-end mx-2"></div>
                                <div class="flex-fill">
                                    <div class="small text-muted">Status</div>
                                    <div class="fw-bold {{ $sertif->status === 'lulus' ? 'text-success' : 'text-danger' }}">
                                        {{ strtoupper(str_replace('_', ' ', $sertif->status)) }}
                                    </div>
                                </div>
                            </div>
                            @if($sertif->status === 'lulus')
                                <a href="{{ route('sertifikasi.cetak', $sertif->id) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100 fw-bold">
                                    <i class="bi bi-download me-1"></i> Download Sertifikat
                                </a>
                            @endif
                        @elseif($pel->status === 'selesai')
                            <div class="alert alert-warning py-2 mb-0 small">
                                <i class="bi bi-info-circle me-1"></i> Nilai belum diinput oleh HRD.
                            </div>
                        @else
                            <div class="alert alert-info py-2 mb-0 small">
                                <i class="bi bi-info-circle me-1"></i> Nilai dan sertifikat akan muncul setelah pelatihan selesai.
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 text-secondary opacity-50"></i>
                    <h6 class="mt-3 fw-bold">Belum Ada Pelatihan</h6>
                    <p class="small">Anda belum terdaftar dalam pelatihan apa pun.</p>
                </div>
            @endforelse
        </div>
    </main>
</div>
@endsection