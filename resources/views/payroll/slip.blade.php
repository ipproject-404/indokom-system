<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Lembur - {{ $karyawan->nama_lengkap }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; margin: 0; padding: 20px; }
        .slip { max-width: 400px; margin: 0 auto; border: 1px solid #ccc; padding: 20px; border-radius: 8px; }
        .header { text-align: center; border-bottom: 2px solid #111; padding-bottom: 10px; margin-bottom: 14px; }
        .header h2 { margin: 0; font-size: 16px; }
        .header p { margin: 2px 0; font-size: 11px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        td { padding: 4px 2px; }
        td:last-child { text-align: right; font-weight: bold; }
        .total-row td { border-top: 1px solid #ccc; padding-top: 8px; font-weight: bold; font-size: 13px; }
        .footer { text-align: center; margin-top: 20px; font-size: 11px; color: #777; }
        .ttd { display: flex; justify-content: space-between; margin-top: 30px; }
        .ttd div { text-align: center; width: 120px; }
        .ttd .garis { border-top: 1px solid #111; margin-top: 40px; }
        @media print { body { padding: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
<div class="slip">
    <div class="header">
        <h2>SLIP UPAH LEMBUR</h2>
        <p>{{ config('kantor.nama') }}</p>
        <p>Periode: {{ \Carbon\Carbon::parse($awal)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($akhir)->translatedFormat('d M Y') }}</p>
    </div>

    <table>
        <tr><td>Nama</td><td>{{ $karyawan->nama_lengkap }}</td></tr>
        <tr><td>Jabatan</td><td>{{ $karyawan->jabatan->nama_jabatan ?? '-' }}</td></tr>
        <tr><td>Departemen</td><td>{{ $karyawan->departemen->nama_departemen ?? '-' }}</td></tr>
        <tr><td>NIK</td><td>{{ $karyawan->nik_kerja }}</td></tr>
    </table>

    <table>
        <thead>
            <tr style="border-bottom:1px solid #ccc;">
                <td><strong>Tanggal</strong></td>
                <td><strong>Jam</strong></td>
                <td><strong>Durasi</strong></td>
                <td><strong>Upah</strong></td>
            </tr>
        </thead>
        <tbody>
            @foreach($lemburs as $l)
            <tr>
                <td>{{ \Carbon\Carbon::parse($l->tanggal)->translatedFormat('d M') }}</td>
                <td>{{ substr($l->jam_mulai, 0, 5) }}–{{ substr($l->jam_selesai, 0, 5) }}</td>
                <td>{{ $l->durasi_final_jam }}j</td>
                <td>Rp {{ number_format($l->total_upah, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2">TOTAL</td>
                <td>{{ $totalJam }}j</td>
                <td>Rp {{ number_format($totalUpah, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="ttd">
        <div>
            <div>Penerima</div>
            <div class="garis">{{ $karyawan->nama_lengkap }}</div>
        </div>
        <div>
            <div>Payroll</div>
            <div class="garis">{{ Auth::user()->karyawan->nama_lengkap ?? '' }}</div>
        </div>
    </div>

    <div class="footer">Dicetak {{ now()->translatedFormat('d F Y, H:i') }}</div>
</div>

<div class="no-print" style="text-align:center; margin-top:16px;">
    <button onclick="window.print()" style="padding:8px 20px; background:#2563eb; color:#fff; border:none; border-radius:8px; font-weight:bold; cursor:pointer;">
        <i>🖨</i> Cetak
    </button>
</div>
</body>
</html>