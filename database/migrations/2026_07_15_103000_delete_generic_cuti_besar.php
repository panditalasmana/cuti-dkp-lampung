<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus data Jenis Cuti dengan kode 'CB' dari database
        DB::table('jenis_cuti')->where('kode_cuti', 'CB')->delete();
    }

    public function down(): void
    {
        // Mengembalikan data CB jika di-rollback
        DB::table('jenis_cuti')->insert([
            'kode_cuti'      => 'CB',
            'nama_cuti'      => 'Cuti Besar',
            'maks_hari'      => 90,
            'potong_kuota'   => false,
            'perlu_lampiran' => true,
            'keterangan'     => 'Cuti besar untuk PNS/PPPK yang telah bekerja minimal 6 tahun terus menerus',
            'dasar_hukum'    => 'Pasal 8 PP No. 11 Tahun 2017',
            'is_active'      => false,
        ]);
    }
};
