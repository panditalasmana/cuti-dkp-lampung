<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_jabatan', 30)->unique();
            $table->string('nama_jabatan');
            $table->enum('golongan', ['I', 'II', 'III', 'IV'])->nullable()->comment('Golongan ASN');
            $table->string('eselon')->nullable()->comment('Eselon jabatan struktural');
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatan');
    }
};