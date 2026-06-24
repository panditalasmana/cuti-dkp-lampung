<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_cuti', function (Blueprint $table) {
            $table->id();
            $table->string('kode_cuti', 30)->unique();
            $table->string('nama_cuti');
            $table->integer('maks_hari')->nullable()->comment('Maksimum hari yang diizinkan, null = tidak terbatas');
            $table->boolean('potong_kuota')->default(true)->comment('Apakah memotong kuota cuti tahunan');
            $table->boolean('perlu_lampiran')->default(false)->comment('Wajib upload lampiran dokumen');
            $table->text('keterangan')->nullable();
            $table->text('dasar_hukum')->nullable()->comment('Dasar hukum pemberian cuti');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_cuti');
    }
};