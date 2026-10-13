@extends('layouts.app')

@section('title', 'Login - Indokom System')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Instrument+Sans:wght@400;500;600;700&display=swap');

    :root {
        --brand: #2563EB;
        --brand-dark: #1D4ED8;
        --brand-soft: #DBEAFE;
        --ink: #0F172A;
        --muted: #64748B;
        --line: #E2E8F0;
    }

    body {
        font-family: 'Instrument Sans', system-ui, sans-serif;
        background: #F1F5F9;
        color: var(--ink);
    }

    h1, h2, h3, h4, h5, .font-display {
        font-family: 'Bricolage Grotesque', sans-serif;
    }

    /* ==========================================
       LAYOUT
    ========================================== */
    .login-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        position: relative;
        overflow: hidden;
    }

    /* Ornamen background */
    .login-wrapper::before {
        content: "";
        position: absolute;
        width: 500px;
        height: 500px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.08) 0%, transparent 70%);
        top: -200px;
        right: -150px;
        pointer-events: none;
    }

    .login-wrapper::after {
        content: "";
        position: absolute;
        width: 400px;
        height: 400px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.06) 0%, transparent 70%);
        bottom: -150px;
        left: -100px;
        pointer-events: none;
    }

    /* ==========================================
       CARD
    ========================================== */
    .login-card {
        background: #FFFFFF;
        border-radius: 24px;
        box-shadow: 
            0 1px 2px rgba(15, 23, 42, 0.04),
            0 20px 40px -12px rgba(15, 23, 42, 0.12);
        border: 1px solid #F1F5F9;
        overflow: hidden;
        width: 100%;
        max-width: 420px;
        position: relative;
        z-index: 1;
        animation: fadeInUp 0.5s ease-out;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ==========================================
       LOGO
    ========================================== */
    .logo-box {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        background: linear-gradient(135deg, #2563EB 0%, #4F46E5 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.4);
        margin: 0 auto 1rem;
    }

    .logo-box i {
        font-size: 28px;
        color: #FFFFFF;
    }

    /* ==========================================
       FORM INPUT
    ========================================== */
    .form-label-custom {
        font-size: 0.8rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.4rem;
        display: block;
    }

    .input-group-custom {
        position: relative;
        display: flex;
        align-items: center;
        border: 1.5px solid var(--line);
        border-radius: 12px;
        background: #F8FAFC;
        transition: all 0.2s ease;
        overflow: hidden;
    }

    .input-group-custom:focus-within {
        background: #FFFFFF;
        border-color: var(--brand);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .input-group-custom .input-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 0.85rem;
        color: #94A3B8;
        font-size: 1rem;
        transition: color 0.2s ease;
    }

    .input-group-custom:focus-within .input-icon {
        color: var(--brand);
    }

    .input-group-custom input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        padding: 0.75rem 0.85rem 0.75rem 0;
        font-size: 0.9rem;
        color: var(--ink);
        font-family: 'Instrument Sans', sans-serif;
    }

    .input-group-custom input::placeholder {
        color: #94A3B8;
    }

    .toggle-password {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 0.85rem;
        color: #94A3B8;
        cursor: pointer;
        background: transparent;
        border: none;
        transition: color 0.2s ease;
    }

    .toggle-password:hover {
        color: var(--brand);
    }

    /* ==========================================
       BUTTON
    ========================================== */
    .btn-primary-custom {
        background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
        border: none;
        border-radius: 12px;
        padding: 0.85rem 1rem;
        font-weight: 700;
        font-size: 0.9rem;
        letter-spacing: 0.2px;
        color: #FFFFFF;
        width: 100%;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px -2px rgba(37, 99, 235, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-primary-custom:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.45);
        color: #FFFFFF;
    }

    .btn-primary-custom:active {
        transform: translateY(0);
    }

    .btn-outline-custom {
        border-radius: 12px;
        padding: 0.75rem 1rem;
        border: 1.5px solid var(--line);
        color: #334155;
        font-weight: 600;
        font-size: 0.88rem;
        transition: all 0.2s ease;
        background: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-outline-custom:hover {
        border-color: var(--brand);
        color: var(--brand);
        background: #EFF6FF;
    }

    .btn-outline-custom i {
        font-size: 1.1rem;
        color: var(--brand);
    }

    /* ==========================================
       DIVIDER
    ========================================== */
    .divider-text {
        position: relative;
        text-align: center;
        margin: 1.25rem 0;
        font-size: 0.75rem;
        color: #94A3B8;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .divider-text::before,
    .divider-text::after {
        content: "";
        position: absolute;
        top: 50%;
        width: 42%;
        height: 1px;
        background: linear-gradient(to right, transparent, var(--line), transparent);
    }

    .divider-text::before { left: 0; }
    .divider-text::after  { right: 0; }

    /* ==========================================
       ALERT
    ========================================== */
    .alert-custom {
        background: #FEF2F2;
        border: 1px solid #FECACA;
        color: #B91C1C;
        padding: 0.7rem 0.9rem;
        border-radius: 12px;
        font-size: 0.82rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .alert-custom i {
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    /* ==========================================
       FOOTER CARD
    ========================================== */
    .card-footer-custom {
        background: linear-gradient(135deg, #EFF6FF 0%, #F0F9FF 100%);
        border-top: 1px solid #DBEAFE;
        padding: 1rem 1.5rem;
        text-align: center;
    }

    .scan-cta {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
        color: #1D4ED8;
        transition: all 0.2s ease;
    }

    .scan-cta:hover {
        color: #1E40AF;
    }

    .scan-cta-icon {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #FFFFFF;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease;
    }

    .scan-cta:hover .scan-cta-icon {
        transform: scale(1.1);
    }

    .scan-cta-icon i {
        font-size: 0.95rem;
        color: var(--brand);
    }

    /* Footer info */
    .footer-info {
        text-align: center;
        margin-top: 1.25rem;
        font-size: 0.72rem;
        color: #94A3B8;
        position: relative;
        z-index: 1;
    }

    /* Badge version */
    .version-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        background: #FFFFFF;
        border: 1px solid var(--line);
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 600;
        color: #64748B;
        margin-top: 0.5rem;
    }

    .version-badge::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10B981;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }
</style>

<div class="login-wrapper">

    <div class="w-100" style="max-width: 420px; position: relative; z-index: 1;">

        <!-- Card Login -->
        <div class="login-card">

            <div class="p-4 p-sm-5 pb-4">

                <!-- Header -->
                <div class="text-center mb-4">
                    <div class="logo-box">
                        <i class="bi bi-buildings-fill"></i>
                    </div>
                    <h1 class="font-display fw-bold mb-1" style="font-size: 1.5rem; color: var(--ink); letter-spacing: -0.5px;">
                        Indokom System
                    </h1>
                    <p class="mb-0" style="color: var(--muted); font-size: 0.85rem;">
                        Sistem Presensi & Kinerja Karyawan
                    </p>
                </div>

                <!-- Pesan Error -->
                @if ($errors->any())
                    <div class="alert-custom">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" autocomplete="off">
                    @csrf

                    <!-- Username -->
                    <div class="mb-3">
                        <label class="form-label-custom">Username</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="bi bi-person-fill"></i></span>
                            <input type="text" 
                                   name="username" 
                                   value="{{ old('username') }}" 
                                   placeholder="Masukkan username"
                                   required autofocus>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="form-label-custom">Password</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="bi bi-shield-lock-fill"></i></span>
                            <input type="password" 
                                   name="password" 
                                   id="passwordInput"
                                   placeholder="Masukkan password"
                                   required>
                            <button type="button" class="toggle-password" onclick="togglePassword()" title="Lihat/Sembunyikan Password">
                                <i class="bi bi-eye-fill" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-primary-custom">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Masuk ke Sistem
                    </button>
                </form>

                <!-- Divider -->
                <div class="divider-text">ATAU</div>

                <!-- Login QR -->
                <a href="{{ route('login.qr') }}" class="btn-outline-custom">
                    <i class="bi bi-qr-code-scan"></i>
                    Login dengan Scan QR
                </a>

            </div>

            <!-- Footer: Absen Cepat -->
            <div class="card-footer-custom">
                <a href="{{ route('absensi.scan') }}" class="scan-cta">
                    <div class="scan-cta-icon">
                        <i class="bi bi-camera-fill"></i>
                    </div>
                    Absen Langsung (Scan QR)
                </a>
            </div>

        </div>

        <!-- Footer Info -->
        <div class="footer-info">
            &copy; {{ date('Y') }} Indokom System. All rights reserved.
            <div class="version-badge">
                System Online
            </div>
        </div>

    </div>

</div>

<script>
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('toggleIcon');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye-fill');
            icon.classList.add('bi-eye-slash-fill');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash-fill');
            icon.classList.add('bi-eye-fill');
        }
    }
</script>

@endsection