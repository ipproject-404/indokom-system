<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarifLembur extends Model
{
    protected $table = 'tarif_lembur';

    protected $fillable = [
        'departemen_id',
        'kelompok',
        'urutan_jam',
        'upah_per_jam',
    ];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    /**
     * Ambil daftar tarif bertingkat untuk departemen & kelompok tertentu.
     * Fallback ke tarif default (departemen_id NULL) kalau tidak ada tarif khusus.
     * Return collection urut dari jam ke-1.
     */
    public static function untukDepartemen(int $departemenId, string $kelompok): \Illuminate\Support\Collection
    {
        $tarif = static::where('departemen_id', $departemenId)
            ->where('kelompok', $kelompok)
            ->orderBy('urutan_jam')
            ->get();

        if ($tarif->isEmpty()) {
            $tarif = static::whereNull('departemen_id')
                ->where('kelompok', $kelompok)
                ->orderBy('urutan_jam')
                ->get();
        }

        return $tarif;
    }

    /**
     * Hitung total upah dari durasi (dalam jam desimal, sudah dibulatkan).
     * Jam yang melebihi tier terakhir dihitung dengan tarif tier terakhir.
     */
    public static function hitungUpah(int $departemenId, string $kelompok, float $durasiJam): float
    {
        $tarif = static::untukDepartemen($departemenId, $kelompok);

        if ($tarif->isEmpty()) {
            return 0;
        }

        $total = 0;
        $sisaJam = $durasiJam;
        $tarifTerakhir = $tarif->last()->upah_per_jam;

        foreach ($tarif as $tier) {
            if ($sisaJam <= 0) break;

            $jamTier = min(1, $sisaJam); // tiap tier = 1 jam
            $total += $jamTier * $tier->upah_per_jam;
            $sisaJam -= $jamTier;
        }

        // Jam sisa setelah melewati semua tier → pakai tarif terakhir
        if ($sisaJam > 0) {
            $total += $sisaJam * $tarifTerakhir;
        }

        return round($total);
    }
}