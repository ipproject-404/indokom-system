@extends('layouts.app')

@section('title', 'Pelatihan Saya')

@section('content')
@php
    $namaTampil = $karyawan->nama_lengkap ?? Auth::user()->username;
    $jabatanTampil = $karyawan->jabatan->nama_jabatan ?? ucfirst(Auth::user()->role);
@endphp

<style>
    /* Import Font Modern */
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    /* Reset Layout Dasar */
    .app-shell {
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        background-color: #f8fafc !important;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* ================= MOBILE STYLES ================= */
    .mobile-header {
        background: #ffffff;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        position: sticky;
        top: 0;
        z-index: 100;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .mobile-content {
        padding: 20px;
        padding-bottom: 110px; /* Jarak aman dari Bottom Nav */
    }

    /* Card Pelatihan */
    .training-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 18px;
        margin-bottom: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease;
    }
    .training-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 4px; height: 100%;
        background: #3b82f6; /* Aksen biru di kiri */
    }

    /* Badges */
    .badge-status {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .badge-lulus {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    /* Tombol Hadir Mobile */
    .btn-hadir {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-weight: 700;
        font-size: 14px;
        width: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 8px 15px rgba(16, 185, 129, 0.25);
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }
    .btn-hadir:active { transform: scale(0.97); }

    /* Box Nilai */
    .score-box {
        background: #f8fafc;
        border-radius: 12px;
        padding: 12px;
        border: 1px dashed #cbd5e1;
    }
</style>

<!-- ==================== TAMPILAN MOBILE (HP) ==================== -->
<div class="d-block d-lg-none">
    <!-- Header Mobile -->
    <div class="mobile-header">
        <a href="{{ route('dashboard.karyawan') }}" class="text-slate-800 text-decoration-none">
            <i class="bi bi-arrow-left fs-4"></i>
        </a>
        <div>
            <h6 class="fw-bold mb-0 text-slate-800">Pelatihan Saya</h6>
            <div class="text-slate-500" style="font-size: 12px;">Riwayat & Sertifikasi</div>
        </div>
    </div>

    <!-- Konten Mobile -->
    <div class="mobile-content">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px; font-size: 13px;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px; font-size: 13px;">
                <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
            </div>
        @endif

        @forelse($peserta as $p)
            @php
                $pel = $p->pelatihan;
                $sertif = $p->sertifikat;
                $statusBadge = match($pel->status) {
                    'selesai' => 'bg-secondary text-white',
                    'dibatalkan' => 'bg-danger text-white',
                    default => $pel->sedangBerjalan() ? 'bg-success text-white' : 'bg-primary text-white'
                };
                $statusText = match($pel->status) {
                    'selesai' => 'Selesai',
                    'dibatalkan' => 'Dibatalkan',
                    default => $pel->sedangBerjalan() ? 'Berlangsung' : 'Akan Datang'
                };
            @endphp
            <div class="training-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge badge-status {{ $statusBadge }}">{{ $statusText }}</span>
                    @if($sertif && $sertif->status === 'lulus')
                        <span class="badge badge-status badge-lulus"><i class="bi bi-award-fill me-1"></i> Lulus</span>
                    @endif
                </div>
                
                <h5 class="fw-bold mb-2 text-slate-800" style="font-size: 16px;">{{ $pel->nama_pelatihan }}</h5>
                <div class="text-slate-500 mb-3" style="font-size: 13px;">
                    <i class="bi bi-calendar-event me-1"></i> {{ $pel->tanggal_mulai->translatedFormat('d M Y') }}
                    @if($pel->tanggal_selesai && !$pel->tanggal_mulai->isSameDay($pel->tanggal_selesai))
                        - {{ $pel->tanggal_selesai->translatedFormat('d M Y') }}
                    @endif
                </div>

                <!-- Tombol Hadir -->
                @if($pel->status === 'aktif' && $pel->sedangBerjalan())
                    <div class="mb-3">
                        @if($p->status_kehadiran === 'hadir')
                            <div class="alert alert-success py-3 mb-0 fw-bold border-0 text-center d-flex justify-content-center align-items-center gap-2" style="border-radius: 12px; font-size: 13px;">
                                <i class="bi bi-patch-check-fill fs-5"></i> Hadir ({{ \Carbon\Carbon::parse($p->waktu_hadir)->format('H:i') }})
                            </div>
                        @else
                            <form method="POST" action="{{ route('pelatihan.karyawan.hadir', $p->id) }}">
                                @csrf
                                <button type="submit" class="btn-hadir">
                                    <i class="bi bi-fingerprint fs-5"></i> Klik Hadir Sekarang
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
                
                <!-- Nilai & Sertifikat -->
                @if($sertif && $sertif->nilai_postest !== null)
                    <div class="score-box mb-3 d-flex justify-content-between text-center">
                        <div class="flex-fill">
                            <div class="text-slate-400" style="font-size: 11px; font-weight: 600;">PRETEST</div>
                            <div class="fw-bold text-slate-700 fs-6">{{ number_format($sertif->nilai_pretest ?? 0, 1) }}</div>
                        </div>
                        <div class="border-end mx-2"></div>
                        <div class="flex-fill">
                            <div class="text-slate-400" style="font-size: 11px; font-weight: 600;">POSTEST</div>
                            <div class="fw-bold text-slate-700 fs-6">{{ number_format($sertif->nilai_postest ?? 0, 1) }}</div>
                        </div>
                        <div class="border-end mx-2"></div>
                        <div class="flex-fill">
                            <div class="text-slate-400" style="font-size: 11px; font-weight: 600;">STATUS</div>
                            <div class="fw-bold {{ $sertif->status === 'lulus' ? 'text-success' : 'text-danger' }}" style="font-size: 14px;">
                                {{ strtoupper(str_replace('_', ' ', $sertif->status)) }}
                            </div>
                        </div>
                    </div>
                    
                    @if($sertif->status === 'lulus')
                        <a href="{{ route('sertifikasi.cetak', $sertif->id) }}" target="_blank" class="btn btn-outline-primary w-100 fw-bold" style="border-radius: 10px; padding: 10px;">
                            <i class="bi bi-download me-1"></i> Cetak Sertifikat
                        </a>
                    @endif
                @elseif($pel->status === 'selesai')
                    <div class="alert alert-warning py-2 px-3 mb-0" style="border-radius: 10px; font-size: 12px;">
                        <i class="bi bi-info-circle me-1"></i> Nilai belum diinput HRD.
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-5">
                <div class="d-inline-flex justify-content-center align-items-center bg-slate-100 rounded-circle mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-inbox fs-1 text-slate-400"></i>
                </div>
                <h6 class="fw-bold text-slate-700">Belum Ada Pelatihan</h6>
                <p class="text-slate-500" style="font-size: 13px;">Anda belum terdaftar dalam pelatihan apa pun.</p>
            </div>
        @endforelse
    </div>

    <!-- Navigasi Bawah -->
    @include('partials.bottom-nav-karyawan')
</div>


<!-- ==================== TAMPILAN DESKTOP (PC) ==================== -->
<div class="d-none d-lg-flex" style="min-height: 100vh; width: 100%; background: #f8fafc;">
    
    <!-- Sidebar Desktop -->
    @include('partials.sidebar-karyawan')

    <!-- Konten Desktop -->
    <main class="flex-grow-1 p-4 p-md-5" style="max-width: 1200px; margin: 0 auto; width: 100%;">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h3 class="fw-bold mb-1 text-slate-800">Pelatihan Saya</h3>
                <p class="text-slate-500 mb-0" style="font-size: 14px;">Riwayat pelatihan dan sertifikasi kompetensi Anda</p>
            </div>
            <div class="bg-blue-50 text-blue-600 px-4 py-2 rounded-pill fw-bold" style="font-size: 14px;">
                <i class="bi bi-award me-1"></i> Total: {{ $peserta->count() }} Pelatihan
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
            </div>
        @endif

        <div class="row g-4">
            @forelse($peserta as $p)
                @php
                    $pel = $p->pelatihan;
                    $sertif = $p->sertifikat;
                    $statusBadge = match($pel->status) {
                        'selesai' => 'bg-secondary text-white',
                        'dibatalkan' => 'bg-danger text-white',
                        default => $pel->sedangBerjalan() ? 'bg-success text-white' : 'bg-primary text-white'
                    };
                    $statusText = match($pel->status) {
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                        default => $pel->sedangBerjalan() ? 'Sedang Berlangsung' : 'Akan Datang'
                    };
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 16px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge badge-status {{ $statusBadge }}">{{ $statusText }}</span>
                                @if($sertif && $sertif->status === 'lulus')
                                    <span class="badge badge-status badge-lulus"><i class="bi bi-award-fill"></i> Lulus</span>
                                @endif
                            </div>
                            
                            <h5 class="fw-bold mb-2 text-slate-800">{{ $pel->nama_pelatihan }}</h5>
                            <div class="text-slate-500 mb-4" style="font-size: 13px;">
                                <i class="bi bi-calendar-event me-1"></i> {{ $pel->tanggal_mulai->translatedFormat('d F Y') }}
                                @if($pel->tanggal_selesai && !$pel->tanggal_mulai->isSameDay($pel->tanggal_selesai))
                                    - {{ $pel->tanggal_selesai->translatedFormat('d F Y') }}
                                @endif
                                <br>
                                <i class="bi bi-building me-1"></i> {{ $pel->penyelenggara ?? 'Internal' }}
                            </div>

                            @if($pel->status === 'aktif' && $pel->sedangBerjalan())
                                <div class="mb-3">
                                    @if($p->status_kehadiran === 'hadir')
                                        <div class="alert alert-success py-2 mb-0 fw-bold border-0 text-center" style="border-radius: 10px; font-size: 13px;">
                                            <i class="bi bi-check-circle-fill me-1"></i> Hadir ({{ \Carbon\Carbon::parse($p->waktu_hadir)->format('H:i') }})
                                        </div>
                                    @else
                                        <form method="POST" action="{{ route('pelatihan.karyawan.hadir', $p->id) }}">
                                            @csrf
                                            <button type="submit" class="btn-hadir">
                                                <i class="bi bi-fingerprint"></i> Klik Hadir Sekarang
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif

                            @if($sertif && $sertif->nilai_postest !== null)
                                <div class="score-box mb-3 d-flex justify-content-between text-center">
                                    <div class="flex-fill">
                                        <div class="text-slate-400" style="font-size: 11px; font-weight: 600;">PRETEST</div>
                                        <div class="fw-bold text-slate-700">{{ number_format($sertif->nilai_pretest ?? 0, 1) }}</div>
                                    </div>
                                    <div class="border-end mx-2"></div>
                                    <div class="flex-fill">
                                        <div class="text-slate-400" style="font-size: 11px; font-weight: 600;">POSTEST</div>
                                        <div class="fw-bold text-slate-700">{{ number_format($sertif->nilai_postest ?? 0, 1) }}</div>
                                    </div>
                                    <div class="border-end mx-2"></div>
                                    <div class="flex-fill">
                                        <div class="text-slate-400" style="font-size: 11px; font-weight: 600;">STATUS</div>
                                        <div class="fw-bold {{ $sertif->status === 'lulus' ? 'text-success' : 'text-danger' }}">
                                            {{ strtoupper(str_replace('_', ' ', $sertif->status)) }}
                                        </div>
                                    </div>
                                </div>
                                @if($sertif->status === 'lulus')
                                    <a href="{{ route('sertifikasi.cetak', $sertif->id) }}" target="_blank" class="btn btn-outline-primary w-100 fw-bold" style="border-radius: 10px;">
                                        <i class="bi bi-download me-1"></i> Download Sertifikat
                                    </a>
                                @endif
                            @elseif($pel->status === 'selesai')
                                <div class="alert alert-warning py-2 mb-0" style="border-radius: 10px; font-size: 13px;">
                                    <i class="bi bi-info-circle me-1"></i> Nilai belum diinput HRD.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-inbox fs-1 text-slate-300"></i>
                    <h6 class="mt-3 fw-bold text-slate-700">Belum Ada Pelatihan</h6>
                    <p class="text-slate-500">Anda belum terdaftar dalam pelatihan apa pun.</p>
                </div>
            @endforelse
        </div>
    </main>
</div>
@endsection