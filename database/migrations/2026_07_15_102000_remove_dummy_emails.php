<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Bersihkan email dummy yang diakhiri dengan @dkplampung.id pada tabel users
        DB::table('users')
            ->where('email', 'LIKE', '%@dkplampung.id')
            ->update(['email' => null]);
    }

    public function down(): void
    {
        // Tidak perlu memulihkan email dummy
    }
};
