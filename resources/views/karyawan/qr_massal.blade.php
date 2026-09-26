<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Code Massal - HRD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background: white !important;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-gray-100 p-8">

    <!-- Tombol Aksi (Hilang saat diprint) -->
    <div class="max-w-4xl mx-auto mb-6 flex justify-between items-center no-print">
        <h1 class="font-bold text-xl text-gray-800">Preview Cetak QR Code Massal</h1>
        <button onclick="window.print()" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-blue-700 shadow-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak Sekarang
        </button>
    </div>

    <!-- Container Grid Kartu ID & QR -->
    <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($karyawans as $karyawan)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col items-center text-center page-break bg-white">
            <div class="text-xs font-bold tracking-wider text-blue-600 uppercase mb-1">ID CARD KARYAWAN</div>
            <div class="text-[10px] text-gray-400 mb-4">PT. Indokom Sistem</div>

            <!-- Bagian QR Code (Menggunakan API pihak ketiga atau library qrcode Anda, sesuaikan dengan file qr.blade.php Anda) -->
            <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 mb-4">
                <!-- Ubah sumber gambar QR ini sesuai dengan struktur project Anda yang sudah ada -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $karyawan->nik_kerja }}" alt="QR Code" class="w-36 h-36 object-contain">
            </div>

            <h2 class="font-bold text-gray-900 text-lg mb-0.5">{{ $karyawan->nama_lengkap }}</h2>
            <p class="text-xs text-gray-500 mb-3">{{ $karyawan->nik_kerja }}</p>

            <div class="w-full bg-blue-50/50 rounded-xl p-2.5 border border-blue-50">
                <div class="text-xs font-semibold text-blue-900">{{ $karyawan->jabatan->nama_jabatan ?? '-' }}</div>
                <div class="text-[10px] text-blue-600 mt-0.5">{{ $karyawan->departemen->nama_departemen ?? '-' }}</div>
            </div>
        </div>
        @endforeach
    </div>

</body>
</html>