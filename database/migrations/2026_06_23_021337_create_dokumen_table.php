<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_cuti_id')->constrained('pengajuan_cuti')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('restrict');
            $table->enum('jenis_dokumen', ['scan_surat_ditandatangani', 'lampiran_pendukung'])
                  ->default('scan_surat_ditandatangani');
            $table->string('nama_file')->comment('Nama file asli');
            $table->string('path_file')->comment('Path file di storage Laravel');
            $table->string('mime_type', 50);
            $table->integer('ukuran_file')->comment('Ukuran dalam bytes');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('pengajuan_cuti_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};