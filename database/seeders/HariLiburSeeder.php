<?php

namespace Database\Seeders;

use App\Models\HariLibur;
use Illuminate\Database\Seeder;

class HariLiburSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['tanggal' => '2026-01-01', 'keterangan' => 'Tahun Baru 2026 Masehi', 'is_cuti_bersama' => false],
            ['tanggal' => '2026-01-16', 'keterangan' => 'Isra Mikraj Nabi Muhammad SAW', 'is_cuti_bersama' => false],
            ['tanggal' => '2026-02-17', 'keterangan' => 'Tahun Baru Imlek 2577 Kongzili', 'is_cuti_bersama' => false],
            ['tanggal' => '2026-03-19', 'keterangan' => 'Hari Suci Nyepi Tahun Baru Saka 1948', 'is_cuti_bersama' => false],
            ['tanggal' => '2026-03-20', 'keterangan' => 'Hari Raya Idul Fitri 1447 Hijriah', 'is_cuti_bersama' => false],
            ['tanggal' => '2026-03-21', 'keterangan' => 'Hari Raya Idul Fitri 1447 Hijriah', 'is_cuti_bersama' => false],
            ['tanggal' => '2026-03-23', 'keterangan' => 'Cuti Bersama Idul Fitri 1447 H', 'is_cuti_bersama' => true],
            ['tanggal' => '2026-03-24', 'keterangan' => 'Cuti Bersama Idul Fitri 1447 H', 'is_cuti_bersama' => true],
            ['tanggal' => '2026-04-03', 'keterangan' => 'Wafat Yesus Kristus', 'is_cuti_bersama' => false],
            ['tanggal' => '2026-05-01', 'keterangan' => 'Hari Buruh Internasional', 'is_cuti_bersama' => false],
            ['tanggal' => '2026-05-14', 'keterangan' => 'Kenaikan Yesus Kristus', 'is_cuti_bersama' => false],
            ['tanggal' => '2026-05-27', 'keterangan' => 'Hari Raya Idul Adha 1447 Hijriah', 'is_cuti_bersama' => false],
            ['tanggal' => '2026-05-31', 'keterangan' => 'Hari Raya Waisak 2570 BE', 'is_cuti_bersama' => false],
            ['tanggal' => '2026-06-01', 'keterangan' => 'Hari Lahir Pancasila', 'is_cuti_bersama' => false],
            ['tanggal' => '2026-06-16', 'keterangan' => 'Tahun Baru Islam 1448 Hijriah', 'is_cuti_bersama' => false],
            ['tanggal' => '2026-08-17', 'keterangan' => 'Hari Kemerdekaan Republik Indonesia', 'is_cuti_bersama' => false],
            ['tanggal' => '2026-08-25', 'keterangan' => 'Maulid Nabi Muhammad SAW', 'is_cuti_bersama' => false],
            ['tanggal' => '2026-12-25', 'keterangan' => 'Hari Raya Natal', 'is_cuti_bersama' => false],
            ['tanggal' => '2026-12-26', 'keterangan' => 'Cuti Bersama Hari Raya Natal', 'is_cuti_bersama' => true],
        ];

        foreach ($data as $item) {
            HariLibur::updateOrCreate(
                ['tanggal' => $item['tanggal']],
                $item
            );
        }
    }
}
