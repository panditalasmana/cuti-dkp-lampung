<?php

namespace Database\Seeders;

use App\Models\JenisCuti;
use Illuminate\Database\Seeder;

class JenisCutiSeeder extends Seeder
{
    public function run(): void
    {
        $jenis = [
            [
                'kode_cuti'     => 'CT',
                'nama_cuti'     => 'Cuti Tahunan',
                'maks_hari'     => 12,
                'potong_kuota'  => true,
                'perlu_lampiran'=> true,
                'keterangan'    => 'Cuti tahunan bagi PNS yang telah bekerja minimal 1 tahun',
                'dasar_hukum'   => 'Pasal 7 PP No. 11 Tahun 2017 dan PP No. 17 Tahun 2020',
            ],

            [
                'kode_cuti'     => 'CB_UMROH',
                'nama_cuti'     => 'Cuti Besar (Umroh)',
                'maks_hari'     => 30,
                'potong_kuota'  => false,
                'perlu_lampiran'=> true,
                'keterangan'    => 'Cuti besar keagamaan untuk ibadah Umroh (maksimal 2 kali pengajuan, total 30 hari)',
                'dasar_hukum'   => 'Pasal 8 PP No. 11 Tahun 2017',
                'is_active'     => true,
            ],
            [
                'kode_cuti'     => 'CB_HAJI',
                'nama_cuti'     => 'Cuti Besar (Haji)',
                'maks_hari'     => 3,
                'potong_kuota'  => false,
                'perlu_lampiran'=> true,
                'keterangan'    => 'Cuti besar keagamaan untuk ibadah Haji (maksimal 1 kali pengajuan, total 3 bulan)',
                'dasar_hukum'   => 'Pasal 8 PP No. 11 Tahun 2017',
                'is_active'     => true,
            ],
            [
                'kode_cuti'     => 'CS',
                'nama_cuti'     => 'Cuti Sakit',
                'maks_hari'     => 14,
                'potong_kuota'  => true,
                'perlu_lampiran'=> true,
                'keterangan'    => 'Cuti karena sakit, diperlukan surat keterangan dokter',
                'dasar_hukum'   => 'Pasal 9 PP No. 11 Tahun 2017',
            ],
            [
                'kode_cuti'     => 'CM',
                'nama_cuti'     => 'Cuti Melahirkan',
                'maks_hari'     => 3,
                'potong_kuota'  => true,
                'perlu_lampiran'=> true,
                'keterangan'    => 'Cuti melahirkan untuk pegawai wanita, maks 3 kali kelahiran',
                'dasar_hukum'   => 'Pasal 10 PP No. 11 Tahun 2017',
            ],
            [
                'kode_cuti'     => 'CAK',
                'nama_cuti'     => 'Cuti Alasan Penting',
                'maks_hari'     => 30,
                'potong_kuota'  => true,
                'perlu_lampiran'=> true,
                'keterangan'    => 'Cuti karena anggota keluarga meninggal, pernikahan, dll.',
                'dasar_hukum'   => 'Pasal 11 PP No. 11 Tahun 2017',
            ],
            [
                'kode_cuti'     => 'CLN',
                'nama_cuti'     => 'Cuti di Luar Tanggungan Negara',
                'maks_hari'     => 2,
                'potong_kuota'  => true,
                'perlu_lampiran'=> true,
                'keterangan'    => 'Cuti di luar tanggungan negara untuk alasan pribadi penting',
                'dasar_hukum'   => 'Pasal 12 PP No. 11 Tahun 2017',
            ],
        ];

        foreach ($jenis as $item) {
            JenisCuti::updateOrCreate(
                ['kode_cuti' => $item['kode_cuti']],
                $item
            );
        }
    }
}