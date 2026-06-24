<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('data/jabatan.csv');

        if (!file_exists($file)) {
            $this->command->error('File jabatan.csv tidak ditemukan!');
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Jabatan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $handle = fopen($file, 'r');
        fgetcsv($handle); // skip header

        $count = 0;
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (empty($row[0])) continue;

            Jabatan::create([
                'kode_jabatan' => trim($row[0]),
                'nama_jabatan' => trim($row[1]),
                'golongan'     => null,
                'eselon'       => null,
                'is_active'    => true,
            ]);
            $count++;
        }

        fclose($handle);
        $this->command->info("JabatanSeeder: {$count} data berhasil diimport.");
    }
}