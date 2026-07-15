<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Mengubah kolom status enum agar mendukung opsi 'dibatalkan'
        DB::statement("ALTER TABLE pengajuan_cuti MODIFY COLUMN status ENUM('menunggu', 'disetujui', 'ditolak', 'dibatalkan') NOT NULL DEFAULT 'menunggu'");
    }

    public function down(): void
    {
        // Mengembalikan ke enum awal (mengubah data 'dibatalkan' menjadi 'menunggu' terlebih dahulu agar tidak error)
        DB::table('pengajuan_cuti')->where('status', 'dibatalkan')->update(['status' => 'menunggu']);
        DB::statement("ALTER TABLE pengajuan_cuti MODIFY COLUMN status ENUM('menunggu', 'disetujui', 'ditolak') NOT NULL DEFAULT 'menunggu'");
    }
};
