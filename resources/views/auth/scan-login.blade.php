@extends('layouts.app')

@section('title', 'Scan QR - Login')

@section('content')
<div class="p-4">

    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('login') }}" class="text-dark me-3"><i class="bi bi-arrow-left fs-4"></i></a>
        <h5 class="fw-bold mb-0">Login dengan QR</h5>
    </div>

    <p class="text-muted small">Arahkan kamera ke QR pada kartu ID karyawan.</p>

    @if ($errors->any())
        <div class="alert alert-danger py-2 small">
            {{ $errors->first() }}
        </div>
    @endif

    <div id="reader" class="mb-3"></div>

    <form id="form-scan-login" method="POST" action="{{ route('login.qr.submit') }}">
        @csrf
        <input type="hidden" name="token" id="token-input">
    </form>

    <div class="text-center text-muted small my-2">atau masukkan kode manual (untuk testing)</div>
    <form method="POST" action="{{ route('login.qr.submit') }}" class="d-flex gap-2">
        @csrf
        <input type="text" name="token" class="form-control" placeholder="Kode QR karyawan">
        <button type="submit" class="btn btn-biru">Masuk</button>
    </form>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const scanner = new Html5Qrcode("reader");

    function onScanSuccess(decodedText) {
        scanner.stop().then(() => {
            document.getElementById('token-input').value = decodedText;
            document.getElementById('form-scan-login').submit();
        });
    }

    Html5Qrcode.getCameras().then(cameras => {
        if (cameras && cameras.length) {
            scanner.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 240 },
                onScanSuccess
            );
        }
    }).catch(() => {
        document.getElementById('reader').innerHTML =
            '<div class="alert alert-warning small">Kamera tidak ditemukan / izin ditolak. Gunakan kode manual di bawah.</div>';
    });
</script>
@endpush
@endsection
