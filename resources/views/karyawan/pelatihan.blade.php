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
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
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
        padding-bottom: 110px;
    }

    /* Card Pelatihan Premium */
    .training-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 22px;
        margin-bottom: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        position: relative;
        overflow: hidden;
        border: 1px solid #f1f5f9;
        transition: transform 0.2s ease;
    }

    /* Efek Garis Aksen Kiri */
    .training-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 6px; height: 100%;
        z-index: 2;
    }
    .card-aktif::before { background: linear-gradient(to bottom, #3b82f6, #8b5cf6); }
    .card-berlangsung::before { background: linear-gradient(to bottom, #10b981, #059669); }
    .card-selesai::before { background: linear-gradient(to bottom, #94a3b8, #64748b); }
    .card-batal::before { background: linear-gradient(to bottom, #ef4444, #b91c1c); }

    /* Ikon Watermark Background */
    .bg-icon {
        position: absolute;
        right: -10px;
        bottom: -15px;
        font-size: 100px;
        color: #f1f5f9;
        z-index: 0;
        opacity: 0.6;
        transform: rotate(-15deg);
        pointer-events: none;
    }

    /* Z-index untuk konten */
    .card-content {
        position: relative;
        z-index: 1;
    }

    /* Badges */
    .badge-status {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .badge-lulus {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #166534;
        border: 1px solid #86efac;
    }

    /* Tanggal & Lokasi Chip */
    .meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    /* Tombol Hadir Mobile */
    .btn-hadir {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #ffffff;
        border: none;
        border-radius: 14px;
        padding: 16px;
        font-weight: 800;
        font-size: 15px;
        width: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }
    .btn-hadir:active { transform: scale(0.97); }

    /* Box Nilai (Sertifikat Style) */
    .score-box {
        background: linear-gradient(135deg, #ffffff, #f8fafc);
        border-radius: 16px;
        padding: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }
    .score-item { text-align: center; flex: 1; }
    .score-label { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px; }
    .score-value { font-size: 20px; font-weight: 900; color: #1e293b; line-height: 1; }
</style>

<!-- ==================== TAMPILAN MOBILE (HP) ==================== -->
<div class="d-block d-lg-none">
    <!-- Header Mobile -->
    <div class="mobile-header">
        <a href="{{ route('dashboard.karyawan') }}" class="text-dark text-decoration-none d-flex align-items-center justify-content-center bg-white shadow-sm rounded-circle" style="width: 38px; height: 38px;">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div>
            <h6 class="fw-bold mb-0 text-dark" style="font-size: 16px;">Pelatihan Saya</h6>
            <div class="text-muted" style="font-size: 12px; font-weight: 500;">Riwayat & Sertifikasi</div>
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
                
                if ($pel->status === 'selesai') {
                    $cardClass = 'card-selesai';
                    $statusBadge = 'bg-secondary text-white';
                    $statusText = 'Selesai';
                    $statusIcon = 'bi-check-circle-fill';
                    $bgIcon = 'bi-journal-check';
                } elseif ($pel->status === 'dibatalkan') {
                    $cardClass = 'card-batal';
                    $statusBadge = 'bg-danger text-white';
                    $statusText = 'Dibatalkan';
                    $statusIcon = 'bi-x-circle-fill';
                    $bgIcon = 'bi-journal-x';
                } elseif ($pel->sedangBerjalan()) {
                    $cardClass = 'card-berlangsung';
                    $statusBadge = 'bg-success text-white';
                    $statusText = 'Berlangsung';
                    $statusIcon = 'bi-play-circle-fill';
                    $bgIcon = 'bi-journal-play';
                } else {
                    $cardClass = 'card-aktif';
                    $statusBadge = 'bg-primary text-white';
                    $statusText = 'Akan Datang';
                    $statusIcon = 'bi-clock-fill';
                    $bgIcon = 'bi-calendar-event';
                }
            @endphp
            
            <div class="training-card {{ $cardClass }}">
                <!-- Ikon Watermark -->
                <i class="bi {{ $bgIcon }} bg-icon"></i>

                <div class="card-content">
                    <!-- Baris Badge -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge badge-status {{ $statusBadge }}">
                            <i class="bi {{ $statusIcon }}"></i> {{ $statusText }}
                        </span>
                        @if($sertif && $sertif->status === 'lulus')
                            <span class="badge badge-status badge-lulus">
                                <i class="bi bi-award-fill"></i> Lulus
                            </span>
                        @endif
                    </div>
                    
                    <!-- Judul -->
                    <h5 class="fw-bold mb-3 text-dark" style="font-size: 18px; line-height: 1.4;">{{ $pel->nama_pelatihan }}</h5>
                    
                    <!-- Meta Info -->
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <div class="meta-chip">
                            <i class="bi bi-calendar3 text-primary"></i> 
                            {{ $pel->tanggal_mulai->translatedFormat('d M Y') }}
                            @if($pel->tanggal_selesai && !$pel->tanggal_mulai->isSameDay($pel->tanggal_selesai))
                                - {{ $pel->tanggal_selesai->translatedFormat('d M Y') }}
                            @endif
                        </div>
                        <div class="meta-chip">
                            <i class="bi bi-building text-warning"></i> 
                            {{ $pel->penyenyelenggara ?? 'Internal' }}
                        </div>
                    </div>

                    <!-- Tombol Absen Mobile -->
                    @if($pel->status === 'aktif' && $pel->sedangBerjalan())
                        <div class="mb-3">
                            @if($p->status_kehadiran === 'hadir')
                                <div class="alert alert-success py-3 mb-0 fw-bold border-0 text-center d-flex justify-content-center align-items-center gap-2" style="border-radius: 14px; font-size: 14px;">
                                    <i class="bi bi-patch-check-fill fs-4"></i> Hadir ({{ \Carbon\Carbon::parse($p->waktu_hadir)->format('H:i') }})
                                </div>
                            @else
                                <form method="POST" action="{{ route('pelatihan.karyawan.hadir', $p->id) }}">
                                    @csrf
                                    <button type="submit" class="btn-hadir">
                                        <i class="bi bi-fingerprint fs-4"></i> Absen Hadir Sekarang
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Nilai & Sertifikat Mobile -->
                    @if($sertif && $sertif->nilai_postest !== null)
                        <div class="score-box mb-3 d-flex justify-content-between align-items-center">
                            <div class="score-item">
                                <div class="score-label">Pretest</div>
                                <div class="score-value">{{ number_format($sertif->nilai_pretest ?? 0, 1) }}</div>
                            </div>
                            <div class="border-end mx-3" style="height: 45px; border-color: #cbd5e1;"></div>
                            <div class="score-item">
                                <div class="score-label">Postest</div>
                                <div class="score-value">{{ number_format($sertif->nilai_postest ?? 0, 1) }}</div>
                            </div>
                            <div class="border-end mx-3" style="height: 45px; border-color: #cbd5e1;"></div>
                            <div class="score-item">
                                <div class="score-label">Status</div>
                                <div class="score-value {{ $sertif->status === 'lulus' ? 'text-success' : 'text-danger' }}" style="font-size: 16px; margin-top: 2px;">
                                    {{ strtoupper(str_replace('_', ' ', $sertif->status)) }}
                                </div>
                            </div>
                        </div>
                        
                        @if($sertif->status === 'lulus')
                            <a href="{{ route('sertifikasi.cetak', $sertif->id) }}" target="_blank" class="btn btn-outline-primary w-100 fw-bold" style="border-radius: 12px; padding: 12px; border-width: 2px;">
                                <i class="bi bi-download me-1"></i> Cetak Sertifikat
                            </a>
                        @endif
                    @elseif($pel->status === 'selesai')
                        <div class="alert alert-warning py-2 px-3 mb-0 d-flex align-items-center gap-2" style="border-radius: 12px; font-size: 13px; background-color: #fffbeb; border-color: #fef3c7; color: #92400e;">
                            <i class="bi bi-info-circle-fill fs-5"></i> Nilai belum diinput oleh HRD.
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <!-- Tampilan Kosong Mobile -->
            <div class="text-center py-5">
                <div class="d-inline-flex justify-content-center align-items-center bg-white shadow-sm border rounded-circle mb-3" style="width: 90px; height: 90px;">
                    <i class="bi bi-journal-x fs-1 text-slate-300"></i>
                </div>
                <h6 class="fw-bold text-dark mt-3">Belum Ada Pelatihan</h6>
                <p class="text-muted" style="font-size: 13px;">Anda belum terdaftar dalam pelatihan apa pun.</p>
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
        <!-- Header Desktop -->
        <div class="mb-4 pb-3 border-bottom">
            <h3 class="fw-bold mb-1 text-dark">Pelatihan Saya</h3>
            <p class="text-muted mb-0" style="font-size: 14px;">Riwayat pelatihan dan sertifikasi kompetensi Anda</p>
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
                    
                    if ($pel->status === 'selesai') {
                        $cardClass = 'card-selesai';
                        $statusBadge = 'bg-secondary text-white';
                        $statusText = 'Selesai';
                        $statusIcon = 'bi-check-circle-fill';
                        $bgIcon = 'bi-journal-check';
                    } elseif ($pel->status === 'dibatalkan') {
                        $cardClass = 'card-batal';
                        $statusBadge = 'bg-danger text-white';
                        $statusText = 'Dibatalkan';
                        $statusIcon = 'bi-x-circle-fill';
                        $bgIcon = 'bi-journal-x';
                    } elseif ($pel->sedangBerjalan()) {
                        $cardClass = 'card-berlangsung';
                        $statusBadge = 'bg-success text-white';
                        $statusText = 'Sedang Berlangsung';
                        $statusIcon = 'bi-play-circle-fill';
                        $bgIcon = 'bi-journal-play';
                    } else {
                        $cardClass = 'card-aktif';
                        $statusBadge = 'bg-primary text-white';
                        $statusText = 'Akan Datang';
                        $statusIcon = 'bi-clock-fill';
                        $bgIcon = 'bi-calendar-event';
                    }
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="training-card h-100 m-0 {{ $cardClass }}" style="display: flex; flex-direction: column;">
                        <i class="bi {{ $bgIcon }} bg-icon"></i>
                        <div class="card-content d-flex flex-column h-100">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge badge-status {{ $statusBadge }}">
                                    <i class="bi {{ $statusIcon }}"></i> {{ $statusText }}
                                </span>
                                @if($sertif && $sertif->status === 'lulus')
                                    <span class="badge badge-status badge-lulus">
                                        <i class="bi bi-award-fill"></i> Lulus
                                    </span>
                                @endif
                            </div>
                            
                            <h5 class="fw-bold mb-3 text-dark" style="font-size: 18px;">{{ $pel->nama_pelatihan }}</h5>
                            
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <div class="meta-chip">
                                    <i class="bi bi-calendar3 text-primary"></i> 
                                    {{ $pel->tanggal_mulai->translatedFormat('d M Y') }}
                                </div>
                                <div class="meta-chip">
                                    <i class="bi bi-building text-warning"></i> 
                                    {{ $pel->penyenyelenggara ?? 'Internal' }}
                                </div>
                            </div>

                            <!-- Konten Bawah (Tombol/Nilai) -->
                            <div class="mt-auto">
                                @if($pel->status === 'aktif' && $pel->sedangBerjalan())
                                    <div class="mb-3">
                                        @if($p->status_kehadiran === 'hadir')
                                            <div class="alert alert-success py-2 mb-0 fw-bold border-0 text-center" style="border-radius: 12px; font-size: 13px;">
                                                <i class="bi bi-check-circle-fill me-1"></i> Hadir ({{ \Carbon\Carbon::parse($p->waktu_hadir)->format('H:i') }})
                                            </div>
                                        @else
                                            <form method="POST" action="{{ route('pelatihan.karyawan.hadir', $p->id) }}">
                                                @csrf
                                                <button type="submit" class="btn-hadir">
                                                    <i class="bi bi-fingerprint"></i> Absen Hadir Sekarang
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif

                                @if($sertif && $sertif->nilai_postest !== null)
                                    <div class="score-box mb-3 d-flex justify-content-between align-items-center">
                                        <div class="score-item">
                                            <div class="score-label">Pretest</div>
                                            <div class="score-value">{{ number_format($sertif->nilai_pretest ?? 0, 1) }}</div>
                                        </div>
                                        <div class="border-end mx-2" style="height: 40px; border-color: #cbd5e1;"></div>
                                        <div class="score-item">
                                            <div class="score-label">Postest</div>
                                            <div class="score-value">{{ number_format($sertif->nilai_postest ?? 0, 1) }}</div>
                                        </div>
                                        <div class="border-end mx-2" style="height: 40px; border-color: #cbd5e1;"></div>
                                        <div class="score-item">
                                            <div class="score-label">Status</div>
                                            <div class="score-value {{ $sertif->status === 'lulus' ? 'text-success' : 'text-danger' }}" style="font-size: 14px;">
                                                {{ strtoupper(str_replace('_', ' ', $sertif->status)) }}
                                            </div>
                                        </div>
                                    </div>
                                    @if($sertif->status === 'lulus')
                                        <a href="{{ route('sertifikasi.cetak', $sertif->id) }}" target="_blank" class="btn btn-outline-primary w-100 fw-bold" style="border-radius: 12px; padding: 10px; border-width: 2px;">
                                            <i class="bi bi-download me-1"></i> Download Sertifikat
                                        </a>
                                    @endif
                                @elseif($pel->status === 'selesai')
                                    <div class="alert alert-warning py-2 mb-0 d-flex align-items-center gap-2" style="border-radius: 12px; font-size: 13px; background-color: #fffbeb; border-color: #fef3c7; color: #92400e;">
                                        <i class="bi bi-info-circle-fill fs-5"></i> Nilai belum diinput HRD.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="d-inline-flex justify-content-center align-items-center bg-white shadow-sm border rounded-circle mb-3" style="width: 90px; height: 90px;">
                        <i class="bi bi-journal-x fs-1 text-slate-300"></i>
                    </div>
                    <h6 class="mt-3 fw-bold text-dark">Belum Ada Pelatihan</h6>
                    <p class="text-muted">Anda belum terdaftar dalam pelatihan apa pun.</p>
                </div>
            @endforelse
        </div>
    </main>
</div>
@endsection