@extends('layouts.app')

@section('title', 'Login')

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
    .login-wrapper {
        min-height: 100vh;
        padding: 1rem;
    }
    .login-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
        width: 100%;
        max-width: 380px; /* Dibuat lebih compact di desktop (sebelumnya 420px) */
    }
    .icon-box {
        width: 56px; /* Diperkecil agar tidak terlalu memakan ruang vertikal */
        height: 56px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--primary-blue), #4facfe);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 16px rgba(13, 110, 253, 0.2);
    }
    .form-control {
        border-radius: 10px;
        padding: 0.6rem 1rem;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        font-size: 0.9rem;
    }
    .form-control:focus {
        background-color: #ffffff;
        border-color: #93c5fd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
    .input-group-text {
        border-radius: 10px 0 0 10px;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        color: #94a3b8;
        padding: 0.6rem 1rem;
    }
    .btn-primary-custom {
        background-color: var(--primary-blue);
        border: none;
        border-radius: 10px;
        padding: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    .btn-primary-custom:hover {
        background-color: #0b5ed7;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(13, 110, 253, 0.3);
    }
    .btn-outline-custom {
        border-radius: 10px;
        padding: 0.65rem;
        border: 1.5px solid var(--primary-blue);
        color: var(--primary-blue);
        font-weight: 600;
        transition: all 0.3s ease;
        background: transparent;
        font-size: 0.95rem;
    }
    .btn-outline-custom:hover {
        background-color: rgba(13, 110, 253, 0.05);
        color: var(--primary-blue);
    }
    .divider-text {
        position: relative;
        text-align: center;
        margin: 1.2rem 0; /* Jarak atas bawah dirapatkan */
        color: #94a3b8;
        font-size: 0.8rem;
    }
    .divider-text::before, .divider-text::after {
        content: "";
        position: absolute;
        top: 50%;
        width: 38%;
        height: 1px;
        background-color: #e2e8f0;
    }
    .divider-text::before { left: 0; }
    .divider-text::after { right: 0; }
</style>

<div class="d-flex flex-column justify-content-center align-items-center login-wrapper">

    <!-- Card Login -->
    <div class="card login-card">
        <!-- p-md-5 dihapus agar padding konstan (p-4), membuat form lebih compact di desktop -->
        <div class="card-body p-4">
            
            <!-- Header/Logo -->
            <div class="text-center mb-4">
                <div class="icon-box mx-auto mb-3">
                    <i class="bi bi-buildings text-white" style="font-size:24px;"></i>
                </div>
                <h5 class="fw-bold mb-1" style="color: var(--dark-text);">Sistem Presensi</h5>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Silakan login untuk mengelola kehadiran</p>
            </div>

            <!-- Pesan Error -->
            @if ($errors->any())
                <div class="alert alert-danger py-2 px-3 small rounded-3 border-0 bg-danger bg-opacity-10 text-danger mb-3 d-flex align-items-center">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i> 
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Username</label>
                    <div class="input-group">
                        <span class="input-group-text border-end-0"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control border-start-0 ps-0" value="{{ old('username') }}" required autofocus placeholder="Masukkan username">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Password</label>
                    <div class="input-group">
                        <span class="input-group-text border-end-0"><i class="bi bi-shield-lock"></i></span>
                        <input type="password" name="password" class="form-control border-start-0 ps-0" required placeholder="Masukkan password">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-custom w-100 text-white mb-2">
                    Login
                </button>
            </form>

            <div class="divider-text fw-medium">ATAU</div>

            <!-- Tombol Login QR -->
            <a href="{{ route('login.qr') }}" class="btn btn-outline-custom w-100 d-flex align-items-center justify-content-center">
                <i class="bi bi-qr-code-scan me-2" style="font-size: 1.1rem;"></i> Login dengan Scan QR
            </a>

        </div>
        
        <!-- Bagian Bawah Card (CTA Absen) -->
        <div class="card-footer border-0 text-center py-3" style="background-color: #f8fafc !important;">
            <a href="{{ route('absensi.scan') }}" class="text-decoration-none fw-semibold text-primary d-flex align-items-center justify-content-center" style="font-size: 0.9rem;">
                <div class="bg-white rounded-circle shadow-sm me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-camera-fill text-primary"></i>
                </div>
                Langsung Absen (Scan QR)
            </a>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="text-center mt-3 text-muted" style="font-size: 0.75rem; opacity: 0.7;">
        &copy; {{ date('Y') }} HRIS Pabrik Udang. All rights reserved.
    </div>

</div>
@endsection