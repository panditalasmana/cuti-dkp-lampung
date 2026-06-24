<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('bidang_id')->nullable()->constrained('bidang')->onDelete('set null');
            $table->foreignId('jabatan_id')->constrained('jabatan')->onDelete('restrict');
            $table->string('nip', 20)->unique();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('email')->nullable();
            $table->date('tanggal_masuk')->comment('TMT CPNS / PPPK');
            $table->enum('jenis_pegawai', ['PNS', 'PPPK', 'Honorer'])->default('PNS');
            $table->string('pangkat')->nullable()->comment('Pangkat/Golongan Ruang, e.g. Penata Muda / III-a');
            $table->string('foto')->nullable();
            $table->integer('sisa_cuti_tahunan')->default(12)->comment('Sisa hak cuti tahunan berjalan');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};