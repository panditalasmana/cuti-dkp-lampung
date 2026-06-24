<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action', 100)->comment('Jenis aksi: login, logout, create, update, delete, upload, etc');
            $table->string('module', 100)->comment('Modul yang diakses: pengajuan, pegawai, bidang, etc');
            $table->text('description')->comment('Deskripsi lengkap aktivitas');
            $table->nullableMorphs('subject');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->json('old_values')->nullable()->comment('Data sebelum perubahan');
            $table->json('new_values')->nullable()->comment('Data setelah perubahan');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['module', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};