<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Nonaktifkan Cuti Besar (CB) lama
        DB::table('jenis_cuti')->where('kode_cuti', 'CB')->update(['is_active' => false]);

        // Tambah jenis cuti Cuti Besar Umroh
        DB::table('jenis_cuti')->updateOrInsert(
            ['kode_cuti' => 'CB_UMROH'],
            [
                'nama_cuti' => 'Cuti Besar (Umroh)',
                'maks_hari' => 90,
                'potong_kuota' => true,
                'perlu_lampiran' => true,
                'keterangan' => 'Cuti besar keagamaan untuk ibadah Umroh (maksimal 2 kali pengajuan)',
                'dasar_hukum' => 'PP No. 11 Tahun 2017',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Tambah jenis cuti Cuti Besar Haji
        DB::table('jenis_cuti')->updateOrInsert(
            ['kode_cuti' => 'CB_HAJI'],
            [
                'nama_cuti' => 'Cuti Besar (Haji)',
                'maks_hari' => 90,
                'potong_kuota' => true,
                'perlu_lampiran' => true,
                'keterangan' => 'Cuti besar keagamaan untuk ibadah Haji (maksimal 1 kali pengajuan)',
                'dasar_hukum' => 'PP No. 11 Tahun 2017',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('jenis_cuti')->whereIn('kode_cuti', ['CB_UMROH', 'CB_HAJI'])->delete();
        DB::table('jenis_cuti')->where('kode_cuti', 'CB')->update(['is_active' => true]);
    }
};
