<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penandatangan', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori', ['pejabat_wenang', 'atasan_langsung', 'eselon_4', 'pejabat_pengawas'])->comment('Kelompok penandatangan/paraf');
            $table->string('nama');
            $table->string('nip', 20);
            $table->string('jabatan');
            $table->string('pangkat_golongan')->nullable()->comment('Pangkat/Golongan ruang');
            $table->boolean('is_default')->default(false)->comment('Penandatangan utama/default kategori');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['kategori', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penandatangan');
    }
};
