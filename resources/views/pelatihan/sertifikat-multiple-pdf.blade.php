<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat {{ $pelatihan->nama_pelatihan }}</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/x-icon">
    <style>
        @page { size: A4 landscape; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; color: #1a1a1a; }

        .sertifikat {
            width: 297mm; height: 210mm;
            position: relative; background: #fdfcf7; overflow: hidden;
        }
        .page-break { page-break-after: always; }

        .border-outer { position: absolute; top: 8mm; left: 8mm; right: 8mm; bottom: 8mm; border: 4px solid #b8860b; }
        .border-inner { position: absolute; top: 12mm; left: 12mm; right: 12mm; bottom: 12mm; border: 1px solid #b8860b; }

        .corner { position: absolute; width: 15mm; height: 15mm; border: 2px solid #b8860b; }
        .corner-tl { top: 6mm; left: 6mm; border-right: none; border-bottom: none; }
        .corner-tr { top: 6mm; right: 6mm; border-left: none; border-bottom: none; }
        .corner-bl { bottom: 6mm; left: 6mm; border-right: none; border-top: none; }
        .corner-br { bottom: 6mm; right: 6mm; border-left: none; border-top: none; }

        .content { position: absolute; top: 20mm; left: 25mm; right: 25mm; bottom: 20mm; text-align: center; }

        .company-name { font-family: 'DejaVu Serif', serif; font-size: 18pt; font-weight: bold; color: #1e3a8a; letter-spacing: 4px; margin-bottom: 2mm; }
        .company-address { font-size: 9pt; color: #666; letter-spacing: 2px; margin-bottom: 5mm; }
        .divider-gold { width: 60mm; height: 1px; background: #b8860b; margin: 0 auto 7mm auto; }

        .title { font-family: 'DejaVu Serif', serif; font-size: 30pt; font-weight: bold; color: #b8860b; letter-spacing: 8px; margin-bottom: 2mm; }
        .subtitle { font-size: 10pt; color: #555; letter-spacing: 4px; margin-bottom: 7mm; }

        .diberikan-kepada { font-size: 10pt; color: #666; margin-bottom: 3mm; }
        .nama-peserta {
            font-family: 'DejaVu Serif', serif; font-size: 26pt; font-weight: bold;
            color: #1e3a8a; letter-spacing: 2px; padding: 2mm 10mm;
            border-bottom: 2px solid #b8860b; display: inline-block; margin-bottom: 5mm;
        }
        .text-utama { font-size: 10.5pt; line-height: 1.6; color: #333; margin: 0 auto; max-width: 200mm; }
        .nama-pelatihan { font-weight: bold; color: #1e3a8a; font-style: italic; }
        .nilai { font-weight: bold; color: #059669; font-size: 12pt; }

        .footer { margin-top: 8mm; text-align: right; padding-right: 15mm; }
        .footer-tanggal { font-size: 10pt; color: #333; margin-bottom: 3mm; }
        .footer-ditandatangani { font-size: 9pt; color: #666; font-style: italic; margin-bottom: 18mm; }
        .footer-ttd-line { width: 55mm; height: 0.5px; background: #333; margin: 0 0 2mm auto; }
        .footer-nama { font-size: 11pt; font-weight: bold; color: #1e3a8a; }
        .footer-jabatan { font-size: 9pt; color: #666; font-style: italic; }

        .nomor-sertifikat {
            position: absolute; bottom: 4mm; left: 25mm;
            font-size: 9pt; color: #333;
            font-family: 'DejaVu Sans Mono', monospace; letter-spacing: 1px;
        }
        .nomor-sertifikat strong { color: #b8860b; }
        .tanggal-sertifikat {
            position: absolute; bottom: 4mm; right: 25mm;
            font-size: 8pt; color: #999; letter-spacing: 1px;
        }
    </style>
</head>
<body>
    @php \Carbon\Carbon::setLocale('id'); @endphp

    @foreach($sertifikasis as $index => $sertifikasi)
        @php
            $k = $sertifikasi->karyawan;
            $p = $pelatihan;
        @endphp

        <div class="sertifikat {{ $index < $sertifikasis->count() - 1 ? 'page-break' : '' }}">

            <div class="border-outer"></div>
            <div class="border-inner"></div>
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <div class="corner corner-br"></div>

            <div class="content">
                <div class="company-name">{{ config('perusahaan.nama', 'PT INDOKOM GROUP') }}</div>
                <div class="company-address">{{ config('perusahaan.alamat', 'Jakarta, Indonesia') }}</div>
                <div class="divider-gold"></div>

                <div class="title">SERTIFIKAT</div>
                <div class="subtitle">KELULUSAN PELATIHAN</div>

                <div class="diberikan-kepada">Diberikan kepada:</div>
                <div class="nama-peserta">{{ strtoupper($k->nama_lengkap ?? '-') }}</div>

                <div class="text-utama">
                    atas keberhasilan menyelesaikan pelatihan
                    <span class="nama-pelatihan">"{{ $p->nama_pelatihan }}"</span>
                    @if($p->penyelenggara)
                        yang diselenggarakan oleh <strong>{{ $p->penyelenggara }}</strong>
                    @endif
                    pada tanggal
                    <strong>{{ $p->tanggal_mulai->translatedFormat('d F Y') }}</strong>@if($p->tanggal_selesai && !$p->tanggal_mulai->isSameDay($p->tanggal_selesai)) s.d. <strong>{{ $p->tanggal_selesai->translatedFormat('d F Y') }}</strong>@endif,
                    dengan <span class="nilai">nilai postest {{ number_format($sertifikasi->nilai_postest, 2) }}</span>.
                </div>

                <div class="footer">
                    <div class="footer-tanggal">
                        @if($sertifikasi->tanggal_surat)
                            {{ config('perusahaan.alamat', 'Jakarta') }}, {{ $sertifikasi->tanggal_surat->translatedFormat('d F Y') }}
                        @else
                            {{ config('perusahaan.alamat', 'Jakarta') }}, {{ now()->translatedFormat('d F Y') }}
                        @endif
                    </div>
                    <div class="footer-ditandatangani">Ditandatangani oleh,</div>
                    <div class="footer-ttd-line"></div>
                    <div class="footer-nama">{{ $sertifikasi->ditandatangani_oleh ?: config('perusahaan.penandatangan.nama', '-') }}</div>
                    <div class="footer-jabatan">{{ $sertifikasi->jabatan_penandatangan ?: config('perusahaan.penandatangan.jabatan', '-') }}</div>
                </div>
            </div>

            @if($sertifikasi->nomor_sertifikat)
                <div class="nomor-sertifikat">
                    <strong>No:</strong> {{ $sertifikasi->nomor_sertifikat }}
                </div>
            @endif
            <div class="tanggal-sertifikat">Diterbitkan: {{ now()->translatedFormat('d M Y') }}</div>
        </div>
    @endforeach
</body>
</html>