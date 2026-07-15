<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_cuti', function (Blueprint $table) {
            // Menambahkan kolom eselon_3 tipe string (maks 255 karakter), opsional (nullable)
            $table->string('eselon_3')->nullable()->after('pejabat_jabatan');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_cuti', function (Blueprint $table) {
            $table->dropColumn('eselon_3');
        });
    }
};