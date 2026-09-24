@extends('layouts.app')

@section('title', 'Scan QR - Absensi')

@section('content')
<style>
    :root {
        --primary-blue: #0d6efd;
        --light-blue: #f4f7f6;
        --dark-text: #2b3452;
    }
    body {
        background-color: var(--light-blue);
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }
    .scan-wrapper {
        min-height: 100vh;
        padding: 1rem;
    }
    .scan-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: none;
        width: 100%;
        max-width: 400px;
    }
    
    /* Indikator Lokasi & Skeleton Loading */
    .location-status {
        border-radius: 12px;
        padding: 1rem;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }
    .loc-loading {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
    }
    .loc-success {
        background-color: rgba(25, 135, 84, 0.05);
        border: 1px solid rgba(25, 135, 84, 0.2);
    }
    .loc-error {
        background-color: rgba(220, 53, 69, 0.05);
        border: 1px solid rgba(220, 53, 69, 0.2);
    }
    
    /* Animasi Shimmer untuk Skeleton */
    .skeleton-box {
        height: 14px;
        background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 6px;
        margin-bottom: 8px;
    }
    .skeleton-box:last-child {
        margin-bottom: 0;
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    /* Scanner UI */
    .scanner-container {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        background-color: #000;
        box-shadow: inset 0 0 10px rgba(0,0,0,0.5);
    }
    #reader { width: 100%; border-radius: 16px; border: none !important; }
    #reader video { border-radius: 16px; object-fit: cover; }
    #reader__dashboard_section_csr span, 
    #reader__dashboard_section_swaplink { color: #fff !important; text-decoration: none; font-size: 0.85rem; }
    #reader__dashboard_section_csr button {
        background-color: var(--primary-blue) !important;
        border: none !important;
        border-radius: 8px !important;
        color: white !important;
        padding: 5px 15px !important;
        margin-top: 10px !important;
    }
    
    /* Overlay Scanner */
    .scan-overlay-frame {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        pointer-events: none; z-index: 10;
        display: flex; justify-content: center; align-items: center;
    }
    .scan-box {
        width: 220px; height: 220px;
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px; position: relative;
        box-shadow: 0 0 0 4000px rgba(0, 0, 0, 0.4);
    }
    .scan-box::before, .scan-box::after,
    .scan-box-bottom::before, .scan-box-bottom::after {
        content: ''; position: absolute; width: 30px; height: 30px;
        border-color: #198754; border-style: solid;
    }
    .scan-box::before { top: -2px; left: -2px; border-width: 4px 0 0 4px; border-radius: 16px 0 0 0; }
    .scan-box::after { top: -2px; right: -2px; border-width: 4px 4px 0 0; border-radius: 0 16px 0 0; }
    .scan-box-bottom::before { bottom: -2px; left: -2px; border-width: 0 0 4px 4px; border-radius: 0 0 0 16px; }
    .scan-box-bottom::after { bottom: -2px; right: -2px; border-width: 0 4px 4px 0; border-radius: 0 0 16px 0; }
    .laser-line {
        position: absolute; width: 90%; height: 2px;
        background: #198754; box-shadow: 0 0 10px #198754, 0 0 20px #198754;
        top: 10%; left: 5%; animation: scan-laser 2s infinite linear;
    }
    @keyframes scan-laser {
        0% { top: 5%; opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { top: 95%; opacity: 0; }
    }
</style>

<div class="d-flex flex-column justify-content-center align-items-center scan-wrapper">

    <div class="card scan-card">
        <div class="card-body p-4">
            
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('login') }}" class="text-secondary text-decoration-none me-3">
                    <i class="bi bi-arrow-left fs-4 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"></i>
                </a>
                <div>
                    <h5 class="fw-bold mb-0" style="color: var(--dark-text);">Absensi Kehadiran</h5>
                    <p class="text-muted small mb-0">Scan QR Code pada ID Card</p>
                </div>
            </div>

            <!-- Indikator GPS dengan Skeleton Loading -->
            <div id="status-lokasi" class="location-status loc-loading mb-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-geo-alt me-2 text-secondary" id="loc-icon"></i>
                    <span class="text-secondary fw-semibold" id="loc-title">Mencari Koordinat GPS...</span>
                </div>
                <!-- Area ini akan diganti oleh JS saat data didapat/gagal -->
                <div id="loc-content">
                    <div class="skeleton-box w-75"></div>
                    <div class="skeleton-box w-50"></div>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2 px-3 small rounded-3 border-0 bg-danger bg-opacity-10 text-danger mb-3 d-flex align-items-center">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i> 
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Area Kamera / Scanner -->
            <div class="scanner-container">
                <div id="reader"></div>
                <!-- Overlay UI Scanner -->
                <div class="scan-overlay-frame" id="scanner-overlay" style="display: none;">
                    <div class="scan-box">
                        <div class="scan-box-bottom"></div>
                        <div class="laser-line"></div>
                    </div>
                </div>
            </div>

            <!-- Form Hidden Presensi -->
            <form id="form-scan-absensi" method="POST" action="{{ route('absensi.scan.submit') }}">
                @csrf
                <input type="hidden" name="token" id="token-input">
                <input type="hidden" name="latitude" id="latitude-input">
                <input type="hidden" name="longitude" id="longitude-input">
            </form>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let lokasiSiap = false;
        
        const statusBox = document.getElementById('status-lokasi');
        const locIcon = document.getElementById('loc-icon');
        const locTitle = document.getElementById('loc-title');
        const locContent = document.getElementById('loc-content');

        // Opsi pencarian GPS: Timeout diperpanjang jadi 20 detik (cocok untuk laptop/sinyal lemah)
        const geoOptions = {
            enableHighAccuracy: true,
            timeout: 20000, 
            maximumAge: 0
        };

        // Mengambil Lokasi GPS
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                
                document.getElementById('latitude-input').value = lat;
                document.getElementById('longitude-input').value = lng;
                
                // Ubah UI ke Success
                statusBox.className = 'location-status loc-success mb-4';
                locIcon.className = 'bi bi-geo-alt-fill me-2 text-success fs-5';
                locIcon.parentElement.className = 'd-flex align-items-center mb-1';
                locTitle.className = 'text-success fw-bold';
                locTitle.innerText = 'Lokasi Ditemukan';
                
                // Tampilkan koordinat aktual
                locContent.innerHTML = `
                    <div class="text-success" style="font-size: 0.8rem;">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Latitude:</span> <strong>${lat.toFixed(6)}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Longitude:</span> <strong>${lng.toFixed(6)}</strong>
                        </div>
                    </div>
                `;
                lokasiSiap = true;
            },
            (error) => {
                console.warn("Geolocation error:", error);
                
                // Deteksi pesan error spesifik
                let pesanError = "Gagal mendapatkan lokasi.";
                if(error.code === 1) pesanError = "Izin akses lokasi ditolak oleh Browser atau Windows.";
                if(error.code === 2) pesanError = "Sinyal GPS/Lokasi tidak tersedia di perangkat ini.";
                if(error.code === 3) pesanError = "Waktu tunggu habis (Timeout) saat mencari sinyal lokasi.";

                // Ubah UI ke Error
                statusBox.className = 'location-status loc-error mb-4';
                locIcon.className = 'bi bi-exclamation-triangle-fill me-2 text-danger fs-5';
                locIcon.parentElement.className = 'd-flex align-items-center mb-2';
                locTitle.className = 'text-danger fw-bold';
                locTitle.innerText = 'Akses Lokasi Gagal';
                
                locContent.innerHTML = `
                    <div class="text-danger lh-sm" style="font-size: 0.8rem;">
                        ${pesanError}<br><br>
                        <span class="fw-semibold">Tips PC/Laptop:</span> Pastikan "Location Services" di Settings OS Windows Anda dalam keadaan ON.
                    </div>
                `;
            },
            geoOptions
        );

        // Inisialisasi Scanner (Kamera tetap nyala sambil mencari GPS)
        const scanner = new Html5Qrcode("reader");
        const overlay = document.getElementById('scanner-overlay');

        function onScanSuccess(decodedText) {
            if (!lokasiSiap) {
                alert('Tunggu sebentar, kami masih mencari koordinat lokasi Anda.');
                return;
            }
            scanner.stop().then(() => {
                document.getElementById('token-input').value = decodedText;
                document.getElementById('form-scan-absensi').submit();
            });
        }

        Html5Qrcode.getCameras().then(cameras => {
            if (cameras && cameras.length) {
                scanner.start(
                    { facingMode: "environment" },
                    { fps: 15, qrbox: { width: 220, height: 220 }, aspectRatio: 1.0 },
                    onScanSuccess
                ).then(() => {
                    overlay.style.display = 'flex';
                });
            }
        }).catch(err => {
            document.getElementById('reader').innerHTML = `
                <div class="d-flex flex-column align-items-center justify-content-center p-4 bg-light border border-warning rounded-4 text-center">
                    <i class="bi bi-camera-video-off text-warning mb-2" style="font-size: 2.5rem;"></i>
                    <h6 class="text-dark fw-bold mb-2">Kamera Tidak Tersedia</h6>
                    <p class="text-muted small mb-0">Harap izinkan akses kamera untuk melakukan absensi.</p>
                </div>
            `;
            overlay.style.display = 'none';
        });
    });
</script>
@endpush
@endsection