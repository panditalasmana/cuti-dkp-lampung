<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengajuan_cuti', function (Blueprint $table) {
            $table->string('atasan_nama', 150)->nullable()->after('pdf_surat');
            $table->string('atasan_nip', 30)->nullable()->after('atasan_nama');
            $table->string('atasan_jabatan', 150)->nullable()->after('atasan_nip');
            $table->string('pejabat_nama', 150)->nullable()->after('atasan_jabatan');
            $table->string('pejabat_nip', 30)->nullable()->after('pejabat_nama');
            $table->string('pejabat_jabatan', 150)->nullable()->after('pejabat_nip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_cuti', function (Blueprint $table) {
            $table->dropColumn([
                'atasan_nama',
                'atasan_nip',
                'atasan_jabatan',
                'pejabat_nama',
                'pejabat_nip',
                'pejabat_jabatan'
            ]);
        });
    }
};
