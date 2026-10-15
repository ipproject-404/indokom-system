<?php

namespace App\Observers;

use App\Models\Karyawan;

class KaryawanObserver
{
    /**
     * Saat karyawan dibuat atau pindah departemen,
     * boleh_lembur ikut departemen barunya.
     */
    public function creating(Karyawan $karyawan): void
    {
        $this->sinkronLembur($karyawan);
    }

    public function updating(Karyawan $karyawan): void
    {
        // Hanya sinkron kalau departemen_id berubah
        if ($karyawan->isDirty('departemen_id')) {
            $this->sinkronLembur($karyawan);
        }
    }

    private function sinkronLembur(Karyawan $karyawan): void
    {
        if (! $karyawan->departemen_id) return;

        $boleh = \App\Models\Departemen::find($karyawan->departemen_id)?->boleh_lembur ?? false;
        $karyawan->boleh_lembur = $boleh;
    }
}