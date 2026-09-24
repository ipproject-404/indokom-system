@extends('layouts.app')

@section('title', 'Scan QR - Login')

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
    /* Mempercantik area Scanner HTML5-QRCode */
    .scanner-container {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        background-color: #000;
        box-shadow: inset 0 0 10px rgba(0,0,0,0.5);
    }
    #reader {
        width: 100%;
        border-radius: 16px;
        border: none !important;
    }
    #reader video {
        border-radius: 16px;
        object-fit: cover;
    }
    #reader__dashboard_section_csr span, 
    #reader__dashboard_section_swaplink {
        color: #fff !important;
        text-decoration: none;
        font-size: 0.85rem;
    }
    #reader__dashboard_section_csr button {
        background-color: var(--primary-blue) !important;
        border: none !important;
        border-radius: 8px !important;
        color: white !important;
        padding: 5px 15px !important;
        margin-top: 10px !important;
    }
    /* Dekorasi overlay pemindai (Frame kotak sudut) */
    .scan-overlay-frame {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        pointer-events: none;
        z-index: 10;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .scan-box {
        width: 220px;
        height: 220px;
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        position: relative;
        box-shadow: 0 0 0 4000px rgba(0, 0, 0, 0.4);
    }
    .scan-box::before, .scan-box::after {
        content: '';
        position: absolute;
        width: 30px;
        height: 30px;
        border-color: #4facfe;
        border-style: solid;
    }
    .scan-box::before {
        top: -2px; left: -2px;
        border-width: 4px 0 0 4px;
        border-radius: 16px 0 0 0;
    }
    .scan-box::after {
        top: -2px; right: -2px;
        border-width: 4px 4px 0 0;
        border-radius: 0 16px 0 0;
    }
    .scan-box-bottom::before, .scan-box-bottom::after {
        content: '';
        position: absolute;
        width: 30px;
        height: 30px;
        border-color: #4facfe;
        border-style: solid;
    }
    .scan-box-bottom::before {
        bottom: -2px; left: -2px;
        border-width: 0 0 4px 4px;
        border-radius: 0 0 0 16px;
    }
    .scan-box-bottom::after {
        bottom: -2px; right: -2px;
        border-width: 0 4px 4px 0;
        border-radius: 0 0 16px 0;
    }
    /* Animasi laser */
    .laser-line {
        position: absolute;
        width: 90%;
        height: 2px;
        background: #4facfe;
        box-shadow: 0 0 10px #4facfe, 0 0 20px #4facfe;
        top: 10%;
        left: 5%;
        animation: scan-laser 2s infinite linear;
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
            
            <!-- Header (Kembali & Judul) -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('login') }}" class="text-secondary text-decoration-none me-3" style="transition: color 0.2s;">
                    <i class="bi bi-arrow-left fs-4 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"></i>
                </a>
                <div>
                    <h5 class="fw-bold mb-0" style="color: var(--dark-text);">Login via QR</h5>
                    <p class="text-muted small mb-0">Arahkan kamera ke ID Card</p>
                </div>
            </div>

            <!-- Pesan Error -->
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

            <!-- Form Hidden (Terisi otomatis saat QR terbaca) -->
            <form id="form-scan-login" method="POST" action="{{ route('login.qr.submit') }}">
                @csrf
                <input type="hidden" name="token" id="token-input">
            </form>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scanner = new Html5Qrcode("reader");
        const overlay = document.getElementById('scanner-overlay');

        function onScanSuccess(decodedText) {
            scanner.stop().then(() => {
                document.getElementById('token-input').value = decodedText;
                document.getElementById('form-scan-login').submit();
            });
        }

        Html5Qrcode.getCameras().then(cameras => {
            if (cameras && cameras.length) {
                // Konfigurasi agar menyesuaikan div #reader secara proporsional
                scanner.start(
                    { facingMode: "environment" },
                    { 
                        fps: 15, 
                        qrbox: { width: 220, height: 220 },
                        aspectRatio: 1.0 
                    },
                    onScanSuccess
                ).then(() => {
                    // Tampilkan overlay laser setelah kamera berhasil menyala
                    overlay.style.display = 'flex';
                });
            }
        }).catch(err => {
            console.error("Camera error:", err);
            // Pesan error diubah mengarahkan kembali ke login biasa
            document.getElementById('reader').innerHTML = `
                <div class="d-flex flex-column align-items-center justify-content-center p-4 bg-light border border-warning rounded-4 text-center">
                    <i class="bi bi-camera-video-off text-warning mb-2" style="font-size: 2.5rem;"></i>
                    <h6 class="text-dark fw-bold mb-2">Kamera Tidak Tersedia</h6>
                    <p class="text-muted small mb-0">Izin kamera ditolak atau perangkat tidak mendukung. Silakan izinkan akses kamera atau gunakan <strong>login username/password</strong> biasa.</p>
                </div>
            `;
            overlay.style.display = 'none';
        });
    });
</script>
@endpush
@endsection