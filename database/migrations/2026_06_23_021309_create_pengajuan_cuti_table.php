<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('restrict');
            $table->foreignId('jenis_cuti_id')->constrained('jenis_cuti')->onDelete('restrict');
            $table->string('nomor_surat', 50)->unique()->comment('Nomor surat otomatis');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('lama_cuti')->comment('Jumlah hari kerja cuti');
            $table->text('alasan_cuti');
            $table->string('alamat_selama_cuti');
            $table->string('no_telp_selama_cuti', 15)->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan_admin')->nullable()->comment('Catatan dari admin saat verifikasi');
            $table->timestamp('tanggal_pengajuan')->useCurrent();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->string('pdf_surat')->nullable()->comment('Path file PDF surat cuti yang dibuat sistem');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['pegawai_id', 'status']);
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_cuti');
    }
};