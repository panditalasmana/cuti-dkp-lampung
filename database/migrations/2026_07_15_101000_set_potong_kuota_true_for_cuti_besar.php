<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Set potong_kuota menjadi true untuk Cuti Besar Umroh & Haji
        DB::table('jenis_cuti')
            ->whereIn('kode_cuti', ['CB_UMROH', 'CB_HAJI'])
            ->update(['potong_kuota' => true]);
    }

    public function down(): void
    {
        DB::table('jenis_cuti')
            ->whereIn('kode_cuti', ['CB_UMROH', 'CB_HAJI'])
            ->update(['potong_kuota' => false]);
    }
};
