<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BidangSeeder::class,    // 1. Bidang dulu
            JabatanSeeder::class,   // 2. Jabatan dulu
            JenisCutiSeeder::class, // 3. Jenis Cuti
            HariLiburSeeder::class, // 4. Hari Libur Nasional
            UserSeeder::class,      // 5. User admin
            PegawaiSeeder::class,   // 6. Pegawai terakhir (butuh semua di atas)
        ]);

        @file_put_contents(storage_path('app/last_reset_year.txt'), date('Y'));
    }
}