@extends('layouts.app')

@section('title', 'Scan QR - Absensi')

@section('content')
<div class="p-4">

    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('login') }}" class="text-dark me-3"><i class="bi bi-arrow-left fs-4"></i></a>
        <h5 class="fw-bold mb-0">Absensi</h5>
    </div>

    <p class="text-muted small">Arahkan kamera ke QR pada kartu ID kamu. Lokasi akan otomatis dicatat.</p>

    <div id="status-lokasi" class="alert alert-secondary py-2 small">
        <i class="bi bi-geo-alt"></i> Mengambil lokasi kamu...
    </div>

    @if ($errors->any())
        <div class="alert alert-danger py-2 small">
            {{ $errors->first() }}
        </div>
    @endif

    <div id="reader" class="mb-3"></div>

    <form id="form-scan-absensi" method="POST" action="{{ route('absensi.scan.submit') }}">
        @csrf
        <input type="hidden" name="token" id="token-input">
        <input type="hidden" name="latitude" id="latitude-input">
        <input type="hidden" name="longitude" id="longitude-input">
    </form>

    <div class="text-center text-muted small my-2">atau masukkan kode manual (untuk testing)</div>
    <form method="POST" action="{{ route('absensi.scan.submit') }}" class="d-flex gap-2" id="form-manual">
        @csrf
        <input type="text" name="token" class="form-control" placeholder="Kode QR karyawan">
        <input type="hidden" name="latitude" id="latitude-input-manual">
        <input type="hidden" name="longitude" id="longitude-input-manual">
        <button type="submit" class="btn btn-biru">Absen</button>
    </form>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let lokasiSiap = false;

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            document.getElementById('latitude-input').value = lat;
            document.getElementById('longitude-input').value = lng;
            document.getElementById('latitude-input-manual').value = lat;
            document.getElementById('longitude-input-manual').value = lng;
            document.getElementById('status-lokasi').innerHTML =
                '<i class="bi bi-geo-alt-fill text-success"></i> Lokasi berhasil didapat.';
            lokasiSiap = true;
        },
        () => {
            document.getElementById('status-lokasi').className = 'alert alert-warning py-2 small';
            document.getElementById('status-lokasi').innerHTML =
                '<i class="bi bi-exclamation-triangle"></i> Gagal ambil lokasi. Aktifkan izin lokasi di browser, lalu muat ulang halaman.';
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );

    const scanner = new Html5Qrcode("reader");

    function onScanSuccess(decodedText) {
        if (!lokasiSiap) {
            alert('Lokasi belum siap, tunggu sebentar lalu coba scan lagi.');
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
