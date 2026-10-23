<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Pelatihan;
use App\Models\PesertaPelatihan;
use App\Models\SertifikasiKaryawan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelatihanController extends Controller
{
    // ================================================================
    // INDEX — daftar pelatihan
    // ================================================================
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $query = Pelatihan::with([
                'peserta.karyawan.departemen',
                'peserta.karyawan.jabatan',
                'sertifikasi',
                'pembuat',
            ])
            ->orderByDesc('tanggal_mulai');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qb) use ($q) {
                $qb->where('nama_pelatihan', 'like', "%{$q}%")
                   ->orWhere('penyelenggara', 'like', "%{$q}%")
                   ->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pelatihans = $query->paginate(15)->withQueryString();

        $stats = [
            'semua'      => Pelatihan::count(),
            'aktif'      => Pelatihan::where('status', 'aktif')->count(),
            'selesai'    => Pelatihan::where('status', 'selesai')->count(),
            'dibatalkan' => Pelatihan::where('status', 'dibatalkan')->count(),
        ];

        return view('pelatihan.index', compact('pelatihans', 'stats'));
    }

    // ================================================================
    // STORE — tambah pelatihan baru
    // ================================================================
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelatihan'  => 'required|string|max:150',
            'penyelenggara'   => 'nullable|string|max:150',
            'kategori'        => 'required|in:sertifikasi,pelatihan_internal,pelatihan_eksternal',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'deskripsi'       => 'nullable|string',
        ]);

        Pelatihan::create([
            ...$request->only([
                'nama_pelatihan', 'penyelenggara', 'kategori',
                'tanggal_mulai', 'tanggal_selesai', 'deskripsi',
            ]),
            'dibuat_oleh' => Auth::id(),
        ]);

        return back()->with('success', 'Pelatihan berhasil ditambahkan.');
    }

    // ================================================================
    // UPDATE — edit pelatihan
    // ================================================================
    public function update(Request $request, Pelatihan $pelatihan)
    {
        $request->validate([
            'nama_pelatihan'  => 'required|string|max:150',
            'penyelenggara'   => 'nullable|string|max:150',
            'kategori'        => 'required|in:sertifikasi,pelatihan_internal,pelatihan_eksternal',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'deskripsi'       => 'nullable|string',
            'status'          => 'required|in:aktif,selesai,dibatalkan',
        ]);

        $pelatihan->update($request->only([
            'nama_pelatihan', 'penyelenggara', 'kategori',
            'tanggal_mulai', 'tanggal_selesai', 'deskripsi', 'status',
        ]));

        return back()->with('success', 'Pelatihan berhasil diperbarui.');
    }

    // ================================================================
    // DESTROY — hapus pelatihan
    // ================================================================
    public function destroy(Pelatihan $pelatihan)
    {
        $pelatihan->delete();
        return back()->with('success', 'Pelatihan dihapus.');
    }

    // ================================================================
    // TANDAI SELESAI — shortcut ubah status aktif ke selesai
    // ================================================================
    public function tandaiSelesai(Pelatihan $pelatihan)
    {
        if ($pelatihan->status !== 'aktif') {
            return back()->with('error', 'Hanya pelatihan berstatus aktif yang dapat ditandai selesai.');
        }

        $pelatihan->update([
            'status' => 'selesai'
        ]);

        return back()->with('success', 'Pelatihan berhasil ditandai sebagai Selesai.');
    }

    // ================================================================
    // TAMBAH PESERTA
    // ================================================================
    public function tambahPeserta(Request $request, Pelatihan $pelatihan)
    {
        if ($pelatihan->status !== 'aktif' && $pelatihan->status !== 'selesai') {
            return back()->with('error', 'Pelatihan sudah dibatalkan. Daftar peserta tidak dapat diubah.');
        }

        $request->validate([
            'karyawan_ids'   => 'required|array|min:1',
            'karyawan_ids.*' => 'exists:karyawan,id',
            'catatan'        => 'nullable|string|max:255',
        ]);

        $ditambah = 0;
        $sudahAda = 0;

        foreach ($request->karyawan_ids as $id) {
            $exists = PesertaPelatihan::where('pelatihan_id', $pelatihan->id)
                ->where('karyawan_id', $id)
                ->exists();

            if ($exists) {
                $sudahAda++;
                continue;
            }

            PesertaPelatihan::create([
                'pelatihan_id'  => $pelatihan->id,
                'karyawan_id'   => $id,
                'ditunjuk_oleh' => Auth::id(),
                'catatan'       => $request->catatan,
            ]);
            $ditambah++;
        }

        $pesan = "{$ditambah} peserta berhasil ditambahkan.";
        if ($sudahAda > 0) {
            $pesan .= " {$sudahAda} sudah terdaftar sebelumnya.";
        }

        return back()->with('success', $pesan);
    }

    // ================================================================
    // TANDAI HADIR PESERTA (Oleh HRD)
    // ================================================================
    public function tandaiHadir(PesertaPelatihan $peserta)
    {
        if (!in_array($peserta->pelatihan->status, ['aktif', 'selesai'])) {
            return back()->with('error', 'Kehadiran tidak bisa dicatat pada pelatihan yang dibatalkan.');
        }

        $peserta->update([
            'status_kehadiran' => 'hadir',
            'waktu_hadir'      => now(),
        ]);

        return back()->with('success', "{$peserta->karyawan->nama_lengkap} ditandai Hadir.");
    }

    // ================================================================
    // HAPUS PESERTA
    // ================================================================
    public function hapusPeserta(Pelatihan $pelatihan, Karyawan $karyawan)
    {
        if ($pelatihan->status !== 'aktif' && $pelatihan->status !== 'selesai') {
            return back()->with('error', 'Pelatihan sudah dibatalkan. Daftar peserta tidak dapat diubah.');
        }

        PesertaPelatihan::where('pelatihan_id', $pelatihan->id)
            ->where('karyawan_id', $karyawan->id)
            ->delete();

        return back()->with('success', "{$karyawan->nama_lengkap} dihapus dari daftar peserta.");
    }

    // ================================================================
    // SIMPAN NILAI BULK (DENGAN VALIDASI KEHADIRAN)
    // ================================================================
    public function simpanNilaiBulk(Request $request, Pelatihan $pelatihan)
    {
        if ($pelatihan->status !== 'selesai') {
            return back()->with('error', 'Nilai hanya bisa diinput setelah pelatihan selesai.');
        }

        $request->validate([
            'nomor_sertifikat'      => 'nullable|string|max:100',
            'tanggal_surat'         => 'nullable|date',
            'ditandatangani_oleh'   => 'nullable|string|max:150',
            'jabatan_penandatangan' => 'nullable|string|max:150',
            'karyawan_ids'          => 'required|array|min:1',
            'karyawan_ids.*'        => 'exists:karyawan,id',
            'nilai_pretest'         => 'nullable|array',
            'nilai_postest'         => 'nullable|array',
        ]);

        $tersimpan = 0;
        $lulus     = 0;
        $tidak     = 0;
        $absen     = 0; // Tambahan counter untuk yang tidak hadir

        foreach ($request->karyawan_ids as $kid) {
            // ⬇️ VALIDASI: Cek apakah peserta benar-benar HADIR ⬇️
            $peserta = PesertaPelatihan::where('pelatihan_id', $pelatihan->id)
                ->where('karyawan_id', $kid)
                ->first();
            
            if (!$peserta || $peserta->status_kehadiran !== 'hadir') {
                $absen++;
                continue; // Skip jika belum/tidak hadir
            }
            // ⬆️ END VALIDASI ⬆️

            $pre  = $request->input("nilai_pretest.{$kid}");
            $post = $request->input("nilai_postest.{$kid}");

            if ($post === null || $post === '') continue;

            $pre  = ($pre === '' || $pre === null) ? null : (float) $pre;
            $post = (float) $post;
            if ($post < 0 || $post > 10) continue;
            if ($pre !== null && ($pre < 0 || $pre > 10)) $pre = null;

            $status = $post >= 8 ? 'lulus' : 'tidak_lulus';
            if ($status === 'lulus') $lulus++; else $tidak++;

            SertifikasiKaryawan::updateOrCreate(
                [
                    'karyawan_id'   => $kid,
                    'pelatihan_id'  => $pelatihan->id,
                    'tanggal_mulai' => $pelatihan->tanggal_mulai->toDateString(),
                ],
                [
                    'tanggal_selesai'       => $pelatihan->tanggal_selesai?->toDateString(),
                    'nilai_pretest'         => $pre,
                    'nilai_postest'         => $post,
                    'nomor_sertifikat'      => $request->nomor_sertifikat ?: null,
                    'tanggal_surat'         => $request->tanggal_surat ?: null,
                    'ditandatangani_oleh'   => $request->ditandatangani_oleh ?: null,
                    'jabatan_penandatangan' => $request->jabatan_penandatangan ?: null,
                    'status'                => $status,
                    'diinput_oleh'          => Auth::id(),
                ]
            );

            $tersimpan++;
        }

        $pesan = "{$tersimpan} nilai tersimpan. Lulus: {$lulus}, Tidak Lulus: {$tidak}.";
        if ($absen > 0) {
            $pesan .= " ({$absen} peserta diabaikan karena tidak hadir).";
        }

        return back()->with('success', $pesan);
    }

    // ================================================================
    // HAPUS SERTIFIKASI
    // ================================================================
    public function hapusSertifikasi(Pelatihan $pelatihan, SertifikasiKaryawan $sertifikasi)
    {
        $sertifikasi->delete();
        return back()->with('success', 'Data nilai pretest & postest dihapus.');
    }

    // ================================================================
    // CETAK SEMUA SERTIFIKAT (Multi-page per Pelatihan)
    // ================================================================
    public function cetakSemuaSertifikat(Pelatihan $pelatihan)
    {
        $sertifikasis = $pelatihan->sertifikasi()
            ->where('status', 'lulus')
            ->with(['karyawan.jabatan', 'karyawan.departemen'])
            ->get();

        if ($sertifikasis->isEmpty()) {
            return back()->with('error', 'Belum ada peserta yang lulus. Input nilai dulu dengan postest ≥ 8.');
        }

        Carbon::setLocale('id');

        $pdf = Pdf::loadView('pelatihan.sertifikat-multiple-pdf', [
            'pelatihan'    => $pelatihan,
            'sertifikasis' => $sertifikasis,
        ])->setPaper('a4', 'landscape');

        $namaFile = 'Sertifikat-' . str_replace(' ', '-', $pelatihan->nama_pelatihan) . '.pdf';

        return $pdf->stream($namaFile);
    }

    // ================================================================
    // CETAK 1 SERTIFIKAT (Untuk halaman Karyawan)
    // ================================================================
    public function cetakSertifikat(SertifikasiKaryawan $sertifikasi)
    {
        if ($sertifikasi->status !== 'lulus') {
            abort(403, 'Sertifikat hanya tersedia untuk peserta yang lulus.');
        }

        $sertifikasi->load(['karyawan.jabatan', 'karyawan.departemen', 'pelatihan']);
        $pelatihan = $sertifikasi->pelatihan;

        Carbon::setLocale('id');
        $sertifikasis = collect([$sertifikasi]);

        $pdf = Pdf::loadView('pelatihan.sertifikat-multiple-pdf', [
            'pelatihan'    => $pelatihan,
            'sertifikasis' => $sertifikasis,
        ])->setPaper('a4', 'landscape');

        $namaFile = 'Sertifikat-' . str_replace(' ', '-', $sertifikasi->karyawan->nama_lengkap) . '.pdf';

        return $pdf->stream($namaFile);
    }

    // ================================================================
    // EXPORT EXCEL PESERTA
    // ================================================================
    public function exportPesertaExcel(Pelatihan $pelatihan)
    {
        Carbon::setLocale('id');
        $pelatihan->load(['peserta.karyawan.jabatan', 'peserta.karyawan.departemen', 'sertifikasi']);

        $sertifPertama = $pelatihan->sertifikasi->first();
        $namaFile      = 'Peserta-' . str_replace(' ', '-', $pelatihan->nama_pelatihan) . '-' . now()->format('Ymd-His') . '.xls';

        $totalPeserta = $pelatihan->peserta->count();
        $totalLulus   = $pelatihan->sertifikasi->where('status', 'lulus')->count();
        $totalTidak   = $pelatihan->sertifikasi->where('status', 'tidak_lulus')->count();
        $totalBelum   = $totalPeserta - $totalLulus - $totalTidak;

        ob_start();
        ?>
<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
<meta charset="UTF-8">
<!--[if gte mso 9]>
<xml>
    <x:ExcelWorkbook>
        <x:ExcelWorksheets>
            <x:ExcelWorksheet>
                <x:Name>Peserta Pelatihan</x:Name>
                <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
            </x:ExcelWorksheet>
        </x:ExcelWorksheets>
    </x:ExcelWorkbook>
</xml>
<![endif]-->
<style>
    table { border-collapse: collapse; font-family: Calibri, sans-serif; font-size: 11pt; }
    th { background: #4F46E5; color: #FFFFFF; font-weight: bold; padding: 8px 10px; border: 1px solid #3730A3; text-align: left; }
    th.center { text-align: center; }
    td { padding: 6px 10px; border: 1px solid #CBD5E1; vertical-align: top; }
    td.center { text-align: center; }
    .title-row { font-size: 16pt; font-weight: bold; color: #1E3A8A; text-align: center; }
    .subtitle-row { font-size: 10pt; color: #666; text-align: center; }
    .section-header { background: #E0E7FF; font-weight: bold; color: #3730A3; padding: 8px 10px; border: 1px solid #A5B4FC; }
    .info-label { font-weight: bold; color: #4B5563; background: #F3F4F6; }
    .lulus { color: #059669; font-weight: bold; }
    .tidak { color: #E11D48; font-weight: bold; }
    .belum { color: #94A3B8; font-style: italic; }
    .badge-aktif { color: #0EA5E9; font-weight: bold; }
    .badge-selesai { color: #64748B; font-weight: bold; }
    .badge-dibatalkan { color: #E11D48; font-weight: bold; }
</style>
</head>
<body>
<table>
    <tr>
        <td colspan="8" class="title-row" style="background:#F1F5F9;border:none;padding:14px;">
            DAFTAR PESERTA PELATIHAN
        </td>
    </tr>
    <tr>
        <td colspan="8" class="subtitle-row" style="background:#F1F5F9;border:none;padding:0 10px 14px;">
            <?= e(config('perusahaan.nama', 'PT INDOKOM GROUP')) ?>
        </td>
    </tr>
    <tr><td colspan="8" style="border:none;height:10px;"></td></tr>

    <tr><td colspan="8" class="section-header">INFORMASI PELATIHAN</td></tr>
    <tr>
        <td class="info-label" width="180">Nama Pelatihan</td>
        <td colspan="7"><strong><?= e($pelatihan->nama_pelatihan) ?></strong></td>
    </tr>
    <tr>
        <td class="info-label">Kategori</td>
        <td colspan="7"><?= e(ucwords(str_replace('_', ' ', $pelatihan->kategori))) ?></td>
    </tr>
    <tr>
        <td class="info-label">Penyelenggara</td>
        <td colspan="7"><?= e($pelatihan->penyelenggara ?? '-') ?></td>
    </tr>
    <tr>
        <td class="info-label">Tanggal Pelaksanaan</td>
        <td colspan="7">
            <?= $pelatihan->tanggal_mulai->translatedFormat('d F Y') ?>
            <?php if ($pelatihan->tanggal_selesai && !$pelatihan->tanggal_mulai->isSameDay($pelatihan->tanggal_selesai)): ?>
                s/d <?= $pelatihan->tanggal_selesai->translatedFormat('d F Y') ?>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td class="info-label">Status Pelatihan</td>
        <td colspan="7">
            <?php
                $kelasStatus = match ($pelatihan->status) {
                    'aktif'      => 'badge-aktif',
                    'selesai'    => 'badge-selesai',
                    'dibatalkan' => 'badge-dibatalkan',
                    default      => 'badge-aktif',
                };
            ?>
            <span class="<?= $kelasStatus ?>"><?= e(ucfirst($pelatihan->status)) ?></span>
        </td>
    </tr>
    <?php if ($sertifPertama && $sertifPertama->nomor_sertifikat): ?>
        <tr>
            <td class="info-label">Nomor Surat</td>
            <td colspan="7" style="font-family:Consolas,monospace;"><?= e($sertifPertama->nomor_sertifikat) ?></td>
        </tr>
    <?php endif; ?>
    <?php if ($sertifPertama && $sertifPertama->tanggal_surat): ?>
        <tr>
            <td class="info-label">Tanggal Surat</td>
            <td colspan="7"><?= $sertifPertama->tanggal_surat->translatedFormat('d F Y') ?></td>
        </tr>
    <?php endif; ?>
    <?php if ($sertifPertama && $sertifPertama->ditandatangani_oleh): ?>
        <tr>
            <td class="info-label">Ditandatangani Oleh</td>
            <td colspan="7"><?= e($sertifPertama->ditandatangani_oleh) ?> (<?= e($sertifPertama->jabatan_penandatangan ?? '-') ?>)</td>
        </tr>
    <?php endif; ?>

    <tr><td colspan="8" style="border:none;height:10px;"></td></tr>

    <tr><td colspan="8" class="section-header">STATISTIK</td></tr>
    <tr>
        <td class="info-label">Total Peserta</td>
        <td colspan="7"><strong><?= $totalPeserta ?></strong> orang</td>
    </tr>
    <tr>
        <td class="info-label">Lulus</td>
        <td colspan="7"><strong class="lulus"><?= $totalLulus ?></strong> orang</td>
    </tr>
    <tr>
        <td class="info-label">Tidak Lulus</td>
        <td colspan="7"><strong class="tidak"><?= $totalTidak ?></strong> orang</td>
    </tr>
    <tr>
        <td class="info-label">Belum Dinilai</td>
        <td colspan="7"><strong class="belum"><?= $totalBelum ?></strong> orang</td>
    </tr>

    <tr><td colspan="8" style="border:none;height:10px;"></td></tr>

    <tr><td colspan="8" class="section-header">DAFTAR PESERTA</td></tr>
    <tr>
        <th class="center" width="40">No</th>
        <th width="200">Nama Lengkap</th>
        <th width="120">NIK</th>
        <th width="150">Jabatan</th>
        <th width="150">Departemen</th>
        <th class="center" width="80">Pretest</th>
        <th class="center" width="80">Postest</th>
        <th class="center" width="120">Status</th>
    </tr>

    <?php if ($pelatihan->peserta->isEmpty()): ?>
        <tr>
            <td colspan="8" style="text-align:center;padding:20px;color:#999;">
                Belum ada peserta
            </td>
        </tr>
    <?php else: ?>
        <?php foreach ($pelatihan->peserta as $i => $ps): ?>
            <?php
                $s = $pelatihan->sertifikasi->firstWhere('karyawan_id', $ps->karyawan_id);
                $statusLabel = 'Belum Dinilai';
                $statusKelas = 'belum';
                if ($s && $s->nilai_postest !== null) {
                    if ($s->status === 'lulus') {
                        $statusLabel = 'LULUS';
                        $statusKelas = 'lulus';
                    } else {
                        $statusLabel = 'TIDAK LULUS';
                        $statusKelas = 'tidak';
                    }
                }
            ?>
            <tr>
                <td class="center"><?= $i + 1 ?></td>
                <td><strong><?= e($ps->karyawan->nama_lengkap ?? '-') ?></strong></td>
                <td style="font-family:Consolas,monospace;font-size:10pt;"><?= e($ps->karyawan->nik_kerja ?? '-') ?></td>
                <td><?= e($ps->karyawan->jabatan->nama_jabatan ?? '-') ?></td>
                <td><?= e($ps->karyawan->departemen->nama_departemen ?? '-') ?></td>
                <td class="center"><?= $s && $s->nilai_pretest !== null ? number_format($s->nilai_pretest, 2) : '-' ?></td>
                <td class="center"><?= $s && $s->nilai_postest !== null ? number_format($s->nilai_postest, 2) : '-' ?></td>
                <td class="center"><span class="<?= $statusKelas ?>"><?= $statusLabel ?></span></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
</body>
</html>
        <?php
        $html = ob_get_clean();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$namaFile}\"")
            ->header('Cache-Control', 'max-age=0');
    }

    // ================================================================
    // EXPORT PDF PESERTA
    // ================================================================
    public function exportPesertaPdf(Pelatihan $pelatihan)
    {
        Carbon::setLocale('id');
        $pelatihan->load(['peserta.karyawan.jabatan', 'peserta.karyawan.departemen', 'sertifikasi']);

        $sertifPertama = $pelatihan->sertifikasi->first();

        $totalPeserta = $pelatihan->peserta->count();
        $totalLulus   = $pelatihan->sertifikasi->where('status', 'lulus')->count();
        $totalTidak   = $pelatihan->sertifikasi->where('status', 'tidak_lulus')->count();
        $totalBelum   = $totalPeserta - $totalLulus - $totalTidak;

        ob_start();
        ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    @page { size: A4 landscape; margin: 10mm; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #333; }

    .header { text-align: center; margin-bottom: 5mm; padding-bottom: 3mm; border-bottom: 2px solid #1e3a8a; }
    .header h1 { font-size: 15pt; color: #1e3a8a; margin-bottom: 1mm; letter-spacing: 1px; }
    .header p { font-size: 9pt; color: #666; }

    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 3mm; }
    .info-table td { padding: 3px 6px; font-size: 8.5pt; border: 1px solid #e2e8f0; }
    .info-table td.label { background: #f1f5f9; font-weight: bold; width: 150px; color: #475569; }

    .stat-box { width: 100%; border-collapse: collapse; margin-bottom: 3mm; }
    .stat-box td { text-align: center; padding: 4mm 2mm; border: 1px solid #e2e8f0; font-size: 8.5pt; }
    .stat-box .num { font-size: 16pt; font-weight: bold; display: block; margin-bottom: 1mm; }
    .stat-box .lbl { font-size: 7.5pt; color: #666; text-transform: uppercase; letter-spacing: 0.5px; }
    .num-total { color: #1e3a8a; }
    .num-lulus { color: #059669; }
    .num-tidak { color: #e11d48; }
    .num-belum { color: #94a3b8; }

    table.data { width: 100%; border-collapse: collapse; }
    thead { background: #1e3a8a; color: #fff; }
    th { padding: 6px 4px; font-size: 8.5pt; text-align: left; border: 1px solid #1e3a8a; }
    th.center { text-align: center; }
    td { padding: 5px 4px; border: 1px solid #ddd; font-size: 8pt; }
    td.center { text-align: center; }
    tbody tr:nth-child(even) { background: #f8fafc; }

    .lulus { color: #059669; font-weight: bold; }
    .tidak { color: #e11d48; font-weight: bold; }
    .belum { color: #94a3b8; font-style: italic; }

    .footer { margin-top: 5mm; padding-top: 3mm; border-top: 1px solid #ddd; text-align: right; font-size: 7.5pt; color: #999; }
</style>
</head>
<body>
    <div class="header">
        <h1>DAFTAR PESERTA PELATIHAN</h1>
        <p><?= e(config('perusahaan.nama', 'PT INDOKOM GROUP')) ?></p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Pelatihan</td>
            <td><strong><?= e($pelatihan->nama_pelatihan) ?></strong></td>
            <td class="label">Status Pelatihan</td>
            <td><strong><?= e(ucfirst($pelatihan->status)) ?></strong></td>
        </tr>
        <tr>
            <td class="label">Kategori</td>
            <td><?= e(ucwords(str_replace('_', ' ', $pelatihan->kategori))) ?></td>
            <td class="label">Penyelenggara</td>
            <td><?= e($pelatihan->penyelenggara ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">Tanggal Pelaksanaan</td>
            <td colspan="3">
                <?= $pelatihan->tanggal_mulai->translatedFormat('d F Y') ?>
                <?php if ($pelatihan->tanggal_selesai && !$pelatihan->tanggal_mulai->isSameDay($pelatihan->tanggal_selesai)): ?>
                    s/d <?= $pelatihan->tanggal_selesai->translatedFormat('d F Y') ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php if ($sertifPertama && $sertifPertama->nomor_sertifikat): ?>
            <tr>
                <td class="label">Nomor Surat</td>
                <td><?= e($sertifPertama->nomor_sertifikat) ?></td>
                <td class="label">Tanggal Surat</td>
                <td><?= $sertifPertama->tanggal_surat?->translatedFormat('d F Y') ?? '-' ?></td>
            </tr>
        <?php endif; ?>
        <?php if ($sertifPertama && $sertifPertama->ditandatangani_oleh): ?>
            <tr>
                <td class="label">Ditandatangani Oleh</td>
                <td colspan="3"><?= e($sertifPertama->ditandatangani_oleh) ?> (<?= e($sertifPertama->jabatan_penandatangan ?? '-') ?>)</td>
            </tr>
        <?php endif; ?>
    </table>

    <table class="stat-box">
        <tr>
            <td>
                <span class="num num-total"><?= $totalPeserta ?></span>
                <span class="lbl">Total Peserta</span>
            </td>
            <td>
                <span class="num num-lulus"><?= $totalLulus ?></span>
                <span class="lbl">Lulus</span>
            </td>
            <td>
                <span class="num num-tidak"><?= $totalTidak ?></span>
                <span class="lbl">Tidak Lulus</span>
            </td>
            <td>
                <span class="num num-belum"><?= $totalBelum ?></span>
                <span class="lbl">Belum Dinilai</span>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th class="center" style="width:25px;">#</th>
                <th style="width:180px;">Nama Lengkap</th>
                <th style="width:100px;">NIK</th>
                <th style="width:120px;">Jabatan</th>
                <th style="width:120px;">Departemen</th>
                <th class="center" style="width:60px;">Pretest</th>
                <th class="center" style="width:60px;">Postest</th>
                <th class="center" style="width:90px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($pelatihan->peserta->isEmpty()): ?>
                <tr>
                    <td colspan="8" style="text-align:center;padding:20px;color:#999;">
                        Belum ada peserta
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pelatihan->peserta as $i => $ps): ?>
                    <?php
                        $s = $pelatihan->sertifikasi->firstWhere('karyawan_id', $ps->karyawan_id);
                        $statusLabel = 'Belum Dinilai';
                        $statusKelas = 'belum';
                        if ($s && $s->nilai_postest !== null) {
                            if ($s->status === 'lulus') {
                                $statusLabel = 'LULUS';
                                $statusKelas = 'lulus';
                            } else {
                                $statusLabel = 'TIDAK LULUS';
                                $statusKelas = 'tidak';
                            }
                        }
                    ?>
                    <tr>
                        <td class="center"><?= $i + 1 ?></td>
                        <td><strong><?= e($ps->karyawan->nama_lengkap ?? '-') ?></strong></td>
                        <td style="font-family:'DejaVu Sans Mono',monospace;font-size:7.5pt;"><?= e($ps->karyawan->nik_kerja ?? '-') ?></td>
                        <td><?= e($ps->karyawan->jabatan->nama_jabatan ?? '-') ?></td>
                        <td><?= e($ps->karyawan->departemen->nama_departemen ?? '-') ?></td>
                        <td class="center"><?= $s && $s->nilai_pretest !== null ? number_format($s->nilai_pretest, 2) : '-' ?></td>
                        <td class="center"><?= $s && $s->nilai_postest !== null ? number_format($s->nilai_postest, 2) : '-' ?></td>
                        <td class="center"><span class="<?= $statusKelas ?>"><?= $statusLabel ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh sistem Indokom System &bull; <?= e(now()->translatedFormat('d F Y H:i')) ?>
    </div>
</body>
</html>
        <?php
        $html = ob_get_clean();

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');

        return $pdf->download('Peserta-' . str_replace(' ', '-', $pelatihan->nama_pelatihan) . '-' . now()->format('Ymd-His') . '.pdf');
    }
}