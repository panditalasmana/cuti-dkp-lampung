<?php

namespace Database\Seeders;

use App\Models\Bidang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BidangSeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('data/bidang.csv');

        if (!file_exists($file)) {
            $this->command->error('File bidang.csv tidak ditemukan!');
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Bidang::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $handle = fopen($file, 'r');
        fgetcsv($handle); // skip header

        $count = 0;
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (empty($row[0])) continue;

            Bidang::create([
                'kode_bidang' => trim($row[0]),
                'nama_bidang' => trim($row[1]),
                'kepala_bidang'     => '-',
                'nip_kepala_bidang' => '-',
                'keterangan'        => trim($row[1]),
                'is_active'         => true,
            ]);
            $count++;
        }

        fclose($handle);
        $this->command->info("BidangSeeder: {$count} data berhasil diimport.");
    }
}