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
                <a href="{{ route('dashboard') }}" class="text-secondary text-decoration-none me-3">
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
                <input type="hidden" name="alamat" id="alamat-input">
                <input type="hidden" name="nama_jalan" id="nama-jalan-input">
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
        const readerBox = document.getElementById('reader');
        const overlay = document.getElementById('scanner-overlay');

        // ==========================================================
        // KONFIGURASI KANTOR -- sekarang diambil dari config/kantor.php
        // (dan file .env) di server, BUKAN diketik manual di sini lagi.
        // Ini supaya nilai yang dipakai untuk tampilan (JS ini) selalu
        // sama persis dengan nilai yang dipakai untuk validasi jarak
        // di server (PHP) -- ganti nilainya di .env, bukan di sini.
        //
        // Catatan: pengecekan radius di JS ini hanya untuk TAMPILAN
        // (supaya jelas dibaca karyawan). Perhitungan yang menentukan
        // apa yang benar-benar tersimpan ke database tetap dihitung
        // ULANG di server (controller Laravel), karena nilai dari JS
        // bisa saja dimanipulasi lewat browser.
        // ==========================================================
        const KANTOR = {
            nama: @json(config('kantor.nama')),
            lat: {{ config('kantor.latitude') }},
            lng: {{ config('kantor.longitude') }},
            radius: {{ config('kantor.radius_meter') }}
        };

        // Menyimpan teks lokasi yang sedang ditampilkan (nama kantor ATAU
        // hasil reverse-geocoding), supaya bisa ikut dikirim ke server
        // saat submit -- bukan cuma angka latitude/longitude saja.
        let alamatTerkini = '';

        // Nama jalan hasil reverse-geocoding SELALU disimpan di sini,
        // baik posisi di dalam maupun di luar radius kantor -- ini yang
        // dikirim ke server sebagai field terpisah "nama_jalan", supaya
        // masuk ke kolom nama_jalan_masuk / nama_jalan_pulang di DB.
        let namaJalanTerkini = '';

        // Hitung jarak dua koordinat pakai rumus Haversine, hasil dalam meter
        function hitungJarakMeter(lat1, lng1, lat2, lng2) {
            const R = 6371000;
            const toRad = (deg) => deg * Math.PI / 180;
            const dLat = toRad(lat2 - lat1);
            const dLng = toRad(lng2 - lng1);
            const a = Math.sin(dLat / 2) ** 2 +
                      Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                      Math.sin(dLng / 2) ** 2;
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function tampilkanSuksesLokasi(lat, lng) {
            statusBox.className = 'location-status loc-success mb-4';
            locIcon.className = 'bi bi-geo-alt-fill me-2 text-success fs-5';
            locIcon.parentElement.className = 'd-flex align-items-center mb-1';
            locTitle.className = 'text-success fw-bold';
            locTitle.innerText = 'Lokasi Ditemukan';

            const jarak = hitungJarakMeter(lat, lng, KANTOR.lat, KANTOR.lng);
            const diKantor = jarak <= KANTOR.radius;

            let infoAtas;
            if (diKantor) {
                // Dalam radius kantor -- nama kantor langsung dipakai sebagai
                // "alamat" yang dikirim ke server (tidak berubah, sesuai
                // kesepakatan sebelumnya). Nama jalan tetap dicari di bawah,
                // tapi cuma buat tambahan info, bukan menggantikan ini.
                alamatTerkini = KANTOR.nama;
                infoAtas = `
                    <div class="fw-semibold mb-2">
                        <i class="bi bi-building-check me-1"></i> ${KANTOR.nama}
                    </div>
                    <div class="mb-2" style="font-size: 0.75rem;">
                        <i class="bi bi-check-circle-fill me-1"></i> ${Math.round(jarak)} m dari titik kantor (dalam radius ${KANTOR.radius} m)
                    </div>
                `;
            } else {
                infoAtas = `
                    <div class="fw-semibold mb-2">
                        <i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i> Di luar radius kantor
                    </div>
                    <div class="mb-2 text-warning" style="font-size: 0.75rem;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> ${Math.round(jarak)} m dari kantor (di luar radius ${KANTOR.radius} m)
                    </div>
                `;
            }

            locContent.innerHTML = `
                <div class="text-success" style="font-size: 0.8rem;">
                    ${infoAtas}
                    <div class="d-flex justify-content-between mb-1">
                        <span>Latitude:</span> <strong>${lat.toFixed(6)}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Longitude:</span> <strong>${lng.toFixed(6)}</strong>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2 mt-1">
                        <span>Nama Jalan:</span>
                        <strong id="nama-jalan-value" class="text-end ms-2">Mencari...</strong>
                    </div>
                </div>
            `;

            // Nama jalan SELALU dicari lewat reverse-geocoding, baik di
            // dalam maupun di luar radius kantor -- beda dengan alamatTerkini
            // (yang dikirim ke server) yang cuma diisi ulang kalau di luar
            // radius, sesuai penjelasan di cariNamaLokasi() di bawah.
            cariNamaLokasi(lat, lng, !diKantor);
        }

        // ==========================================================
        // Reverse geocoding: ubah koordinat angka jadi nama jalan/
        // area yang gampang dibaca, pakai OpenStreetMap Nominatim
        // (gratis, tanpa API key). Kalau gagal/timeout, koordinat
        // di atas tetap tampil jadi tidak mengganggu proses absen.
        //
        // Parameter jadikanAlamatUtama: kalau true (posisi di luar
        // radius kantor), hasil pencarian ini JUGA dipakai sebagai
        // alamatTerkini yang dikirim & disimpan ke server. Kalau false
        // (posisi di dalam radius), hasil ini HANYA ditampilkan sebagai
        // info nama jalan tambahan -- alamatTerkini tetap nama kantor.
        // ==========================================================
        function cariNamaLokasi(lat, lng, jadikanAlamatUtama) {
            const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;

            fetch(url, { headers: { 'Accept-Language': 'id' } })
                .then(res => res.json())
                .then(data => {
                    const el = document.getElementById('nama-jalan-value');
                    if (!el) return;

                    let namaJalan;
                    if (data && data.address) {
                        const a = data.address;
                        // Prioritaskan nama tempat/gedung/kantor kalau ada datanya
                        // di OpenStreetMap, baru fallback ke nama jalan + area.
                        const bagian = [
                            a.office || a.amenity || a.building || a.shop || null,
                            a.road || null,
                            a.village || a.suburb || a.city_district || null,
                            a.city || a.town || a.county || null
                        ].filter(Boolean);

                        namaJalan = bagian.join(', ') || 'Nama lokasi tidak ditemukan';
                    } else {
                        namaJalan = 'Nama lokasi tidak ditemukan';
                    }

                    el.innerText = namaJalan;
                    namaJalanTerkini = namaJalan; // selalu diisi, apapun status radius
                    if (jadikanAlamatUtama) alamatTerkini = namaJalan;
                })
                .catch(() => {
                    const el = document.getElementById('nama-jalan-value');
                    if (el) el.innerText = 'Gagal memuat nama jalan';
                    namaJalanTerkini = 'Gagal memuat nama lokasi'; // selalu diisi
                    if (jadikanAlamatUtama) alamatTerkini = 'Gagal memuat nama lokasi';
                });
        }

        function tampilkanErrorLokasi(judul, pesan) {
            statusBox.className = 'location-status loc-error mb-4';
            locIcon.className = 'bi bi-exclamation-triangle-fill me-2 text-danger fs-5';
            locIcon.parentElement.className = 'd-flex align-items-center mb-2';
            locTitle.className = 'text-danger fw-bold';
            locTitle.innerText = judul;
            locContent.innerHTML = `<div class="text-danger lh-sm" style="font-size: 0.8rem;">${pesan}</div>`;
        }

        function tampilkanErrorKamera(judul, pesan) {
            readerBox.innerHTML = `
                <div class="d-flex flex-column align-items-center justify-content-center p-4 bg-light border border-warning rounded-4 text-center">
                    <i class="bi bi-camera-video-off text-warning mb-2" style="font-size: 2.5rem;"></i>
                    <h6 class="text-dark fw-bold mb-2">${judul}</h6>
                    <p class="text-muted small mb-0">${pesan}</p>
                </div>
            `;
            overlay.style.display = 'none';
        }

        // ==========================================================
        // 0. CEK SECURE CONTEXT
        // Browser modern MEMBLOKIR TOTAL akses GPS & Kamera kalau
        // halaman dibuka lewat "http://" biasa (selain localhost/
        // 127.0.0.1) -- walau izin di HP/Windows sudah Allow. Ini
        // penyebab paling umum dari "sudah aktif tapi tetap ditolak".
        // ==========================================================
        if (!window.isSecureContext) {
            tampilkanErrorLokasi(
                'Koneksi Tidak Aman (Bukan HTTPS)',
                'Browser memblokir GPS karena halaman ini dibuka lewat "http://" biasa, bukan "https://" atau "localhost". Ini tetap terjadi walau izin lokasi di HP/Windows sudah dinyalakan. Akses halaman lewat HTTPS (domain asli, ngrok, atau Laravel Herd/Valet) untuk mengatasinya.'
            );
            tampilkanErrorKamera(
                'Kamera Diblokir Browser',
                'Kamera tidak bisa dipakai karena koneksi bukan HTTPS/localhost. Gunakan HTTPS agar kamera bisa diakses.'
            );
            return;
        }

        // Opsi pencarian GPS: Timeout diperpanjang jadi 20 detik (cocok untuk laptop/sinyal lemah)
        const geoOptions = {
            enableHighAccuracy: true,
            timeout: 20000,
            maximumAge: 0
        };

        // ==========================================================
        // 1. CEK STATUS IZIN LEBIH AWAL (kalau browser mendukung)
        // Kalau status sudah 'denied', browser TIDAK akan menampilkan
        // pop-up izin lagi -- harus direset manual lewat ikon gembok
        // di address bar. Kita kasih tahu ini lebih awal daripada
        // menunggu timeout 20 detik dulu.
        // ==========================================================
        if (navigator.permissions && navigator.permissions.query) {
            navigator.permissions.query({ name: 'geolocation' }).then(function(status) {
                if (status.state === 'denied') {
                    tampilkanErrorLokasi(
                        'Izin Lokasi Diblokir di Browser',
                        'Izin lokasi untuk situs ini sudah pernah ditolak, jadi browser tidak akan menampilkan pop-up izin lagi -- menyalakan GPS di Settings HP/Windows saja tidak cukup. Klik ikon gembok 🔒 di address bar → ubah "Lokasi" ke Allow → refresh halaman.'
                    );
                }
            }).catch(function() {});

            navigator.permissions.query({ name: 'camera' }).then(function(status) {
                if (status.state === 'denied') {
                    tampilkanErrorKamera(
                        'Izin Kamera Diblokir di Browser',
                        'Izin kamera untuk situs ini sudah pernah ditolak. Klik ikon gembok 🔒 di address bar → ubah "Kamera" ke Allow → refresh halaman.'
                    );
                }
            }).catch(function() {});
        }

        // Mengambil Lokasi GPS
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                document.getElementById('latitude-input').value = lat;
                document.getElementById('longitude-input').value = lng;
                tampilkanSuksesLokasi(lat, lng);
                lokasiSiap = true;
            },
            (error) => {
                console.warn("Geolocation error:", error);

                let judul = 'Akses Lokasi Gagal';
                let pesanError = "Gagal mendapatkan lokasi.";
                let tips = '<br><br><span class="fw-semibold">Tips PC/Laptop:</span> Pastikan "Location Services" di Settings OS Windows Anda dalam keadaan ON.';

                if (error.code === 1) {
                    judul = 'Izin Lokasi Ditolak';
                    pesanError = 'Browser menolak permintaan lokasi. Kalau menurut Anda izin sudah Allow, cek ikon gembok 🔒 di address bar dan pastikan "Lokasi" berstatus Allow (bukan cuma toggle di Settings HP/Windows), lalu refresh halaman.';
                    tips = '';
                }
                if (error.code === 2) pesanError = "Sinyal GPS/Lokasi tidak tersedia di perangkat ini.";
                if (error.code === 3) pesanError = "Waktu tunggu habis (Timeout) saat mencari sinyal lokasi. Coba pindah ke tempat dengan sinyal lebih baik atau refresh halaman.";

                tampilkanErrorLokasi(judul, pesanError + tips);
            },
            geoOptions
        );

        // Inisialisasi Scanner (Kamera tetap nyala sambil mencari GPS)
        const scanner = new Html5Qrcode("reader");

        function onScanSuccess(decodedText) {
            if (!lokasiSiap) {
                alert('Tunggu sebentar, kami masih mencari koordinat lokasi Anda.');
                return;
            }
            scanner.stop().then(() => {
                document.getElementById('token-input').value = decodedText;
                document.getElementById('alamat-input').value = alamatTerkini;
                document.getElementById('nama-jalan-input').value = namaJalanTerkini;
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
                }).catch(err => {
                    console.warn("Gagal start kamera:", err);
                    tampilkanErrorKamera(
                        'Gagal Menyalakan Kamera',
                        'Kamera terdeteksi tapi gagal dinyalakan. Tutup aplikasi lain yang memakai kamera (Zoom/Meet/tab lain), lalu refresh halaman.'
                    );
                });
            } else {
                tampilkanErrorKamera(
                    'Kamera Tidak Ditemukan',
                    'Perangkat ini tidak memiliki kamera yang terdeteksi oleh browser.'
                );
            }
        }).catch(err => {
            console.warn("Gagal ambil daftar kamera:", err);
            const errName = (err && err.name) ? err.name : '';
            let judul = 'Kamera Tidak Tersedia';
            let pesan = 'Harap izinkan akses kamera untuk melakukan absensi.';

            if (errName === 'NotAllowedError') {
                judul = 'Izin Kamera Ditolak';
                pesan = 'Browser menolak akses kamera. Klik ikon gembok 🔒 di address bar, ubah izin "Kamera" ke Allow, lalu refresh -- menyalakan kamera di HP/Windows saja tidak cukup kalau izin di level browser masih Block.';
            } else if (errName === 'NotFoundError') {
                judul = 'Kamera Tidak Ditemukan';
                pesan = 'Tidak ada kamera yang terdeteksi di perangkat ini.';
            } else if (errName === 'NotReadableError') {
                judul = 'Kamera Sedang Dipakai Aplikasi Lain';
                pesan = 'Tutup aplikasi/tab lain yang mungkin sedang memakai kamera (Zoom, Google Meet, dll), lalu refresh halaman ini.';
            }

            tampilkanErrorKamera(judul, pesan);
        });
    });
</script>
@endpush
@endsection