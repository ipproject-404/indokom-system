<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak ID Card & QR - {{ $karyawan->nama_lengkap }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Gambar QR dari qrcodejs berupa <img>/<canvas> di dalam #qr-box */
        #qr-box img, #qr-box canvas { display: block; width: 180px; height: 180px; }
    </style>
</head>
<body class="bg-gray-200 flex items-center justify-center min-h-screen">

    <div class="bg-white w-[300px] rounded-2xl shadow-xl overflow-hidden border border-gray-100 p-6 text-center">
        <h2 class="font-bold text-blue-600 text-lg mb-1">ID CARD KARYAWAN</h2>
        <p class="text-xs text-gray-500 mb-6 border-b pb-4">PT. Indokom Sistem</p>

        <!-- QR dibuat langsung di browser dari barcode_uid (tidak ada data yang dikirim ke layanan luar) -->
        <div class="flex justify-center mb-6">
            <div id="qr-box" data-token="{{ $karyawan->barcode_uid }}" class="border p-2 rounded-lg bg-white"></div>
        </div>

        <h3 class="font-bold text-gray-800 text-xl">{{ $karyawan->nama_lengkap }}</h3>
        <p class="text-sm font-medium text-gray-600 mt-1">{{ $karyawan->nik_kerja }}</p>

        <div class="mt-4 bg-blue-50 py-2 rounded-lg">
            <p class="text-xs text-blue-800 font-semibold">{{ $karyawan->jabatan->nama_jabatan ?? 'Jabatan' }}</p>
            <p class="text-[10px] text-blue-600">{{ $karyawan->departemen->nama_departemen ?? 'Departemen' }}</p>
        </div>

        <button onclick="window.print()" class="mt-6 w-full bg-blue-600 text-white py-2 rounded-lg font-medium hover:bg-blue-700 print:hidden">
            Cetak ID Card
        </button>
    </div>

    <script src="/js/qrcode.min.js"></script>
    <script>
        (function () {
            var box = document.getElementById('qr-box');
            if (!box || !box.dataset.token) return;
            new QRCode(box, {
                text: box.dataset.token,
                width: 180,
                height: 180,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
        })();
    </script>
</body>
</html>