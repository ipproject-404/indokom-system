@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="d-flex flex-column justify-content-center p-4" style="min-height: 100vh;">

    <div class="text-center mb-4">
        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
             style="width:64px;height:64px;border-radius:16px;background:var(--biru-utama);">
            <i class="bi bi-building text-white" style="font-size:28px;"></i>
        </div>
        <h4 class="fw-bold mb-0">Sistem Presensi Karyawan</h4>
        <p class="text-muted small">Silakan login untuk melanjutkan</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger py-2 small">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label small fw-semibold">Username</label>
            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label small fw-semibold">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-biru w-100 py-2 fw-semibold">Login</button>
    </form>

    <div class="text-center my-3 text-muted small">atau</div>

    <a href="{{ route('login.qr') }}" class="btn btn-outline-primary w-100 py-2 fw-semibold">
        <i class="bi bi-qr-code-scan me-1"></i> Login dengan Scan QR
    </a>

    <div class="text-center mt-4">
        <a href="{{ route('absensi.scan') }}" class="text-decoration-none small text-muted">
            <i class="bi bi-camera me-1"></i> Langsung Absen (Scan QR)
        </a>
    </div>

</div>
@endsection
