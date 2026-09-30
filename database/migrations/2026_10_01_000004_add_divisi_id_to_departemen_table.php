<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ditambah nullable dulu, karena tabel departemen sudah ada isinya
        // (Produksi, HRD, Gudang, dll) yang belum punya divisi_id.
        Schema::table('departemen', function (Blueprint $table) {
            $table->foreignId('divisi_id')->nullable()->after('id')->constrained('divisi');
        });

        // Semua departemen yang sudah ada dianggap masuk Divisi Holding (id=1)
        // -- sesuaikan manual lewat HRD kalau ternyata ada yang harusnya di
        // Divisi Tambak.
        DB::table('departemen')->whereNull('divisi_id')->update(['divisi_id' => 1]);

        // Setelah semua baris lama terisi, kunci jadi wajib supaya departemen
        // baru ke depannya tidak bisa dibuat tanpa divisi.
        Schema::table('departemen', function (Blueprint $table) {
            $table->foreignId('divisi_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('departemen', function (Blueprint $table) {
            $table->dropConstrainedForeignId('divisi_id');
        });
    }
};
