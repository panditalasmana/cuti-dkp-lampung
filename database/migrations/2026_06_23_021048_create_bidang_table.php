<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bidang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bidang', 30)->unique();
            $table->string('nama_bidang');
            $table->string('kepala_bidang')->nullable()->comment('Nama Kepala Bidang untuk tanda tangan');
            $table->string('nip_kepala_bidang', 18)->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bidang');
    }
};