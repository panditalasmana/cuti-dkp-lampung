<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update Cuti Besar Umroh menjadi maks 30 hari
        DB::table('jenis_cuti')
            ->where('kode_cuti', 'CB_UMROH')
            ->update([
                'maks_hari' => 30,
                'keterangan' => 'Cuti besar keagamaan untuk ibadah Umroh (maksimal 2 kali pengajuan, total 30 hari)'
            ]);

        // Update Cuti Besar Haji menjadi maks 3 bulan
        DB::table('jenis_cuti')
            ->where('kode_cuti', 'CB_HAJI')
            ->update([
                'maks_hari' => 3,
                'keterangan' => 'Cuti besar keagamaan untuk ibadah Haji (maksimal 1 kali pengajuan, total 3 bulan)'
            ]);
    }

    public function down(): void
    {
        DB::table('jenis_cuti')
            ->where('kode_cuti', 'CB_UMROH')
            ->update(['maks_hari' => 90]);

        DB::table('jenis_cuti')
            ->where('kode_cuti', 'CB_HAJI')
            ->update(['maks_hari' => 90]);
    }
};
