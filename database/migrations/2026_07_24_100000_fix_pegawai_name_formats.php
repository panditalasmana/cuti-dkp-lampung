<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Pegawai;
use App\Models\User;
use App\Models\Bidang;
use App\Models\Jabatan;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update seluruh nama & data pegawai di database dari file CSV resmi terbaru.
     */
    public function up(): void
    {
        $csvFile = database_path('data/pegawai.csv');
        if (!file_exists($csvFile)) {
            $csvFile = database_path('data/pegawai_format_resmi_duk.csv');
        }

        if (!file_exists($csvFile)) {
            return;
        }

        $bidangMap  = Bidang::pluck('id', 'nama_bidang')->toArray();
        $jabatanMap = Jabatan::pluck('id', 'nama_jabatan')->toArray();

        $handle = fopen($csvFile, 'r');
        fgetcsv($handle, 0, ','); // Skip header

        DB::transaction(function () use ($handle, &$bidangMap, &$jabatanMap) {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                if (empty($row) || empty($row[0])) continue;

                $nip          = preg_replace('/\s+/', '', trim($row[0]));
                $namaLengkap  = trim($row[1]  ?? '');
                $jenisKelamin = strtoupper(trim($row[2] ?? 'L'));
                $tempatLahir  = trim($row[3]  ?? '-');
                $tanggalLahir = trim($row[4]  ?? '1990-01-01');
                $tanggalMasuk = trim($row[5]  ?? date('Y-m-d'));
                $jenisPegawai = trim($row[6]  ?? 'PNS');
                $pangkat      = trim($row[7]  ?? '-');
                $namaBidang   = trim($row[8]  ?? '');
                $namaJabatan  = trim($row[9]  ?? '');

                $subBagian = null;
                if ($namaBidang === 'Sub Bagian Umum dan Kepegawaian') {
                    $namaBidang = 'Sekretariat';
                    $subBagian = 'Sub Bagian Umum dan Kepegawaian';
                } elseif ($namaBidang === 'Sub Bagian Keuangan dan Aset') {
                    $namaBidang = 'Sekretariat';
                    $subBagian = 'Sub Bagian Keuangan dan Aset';
                }
                
                if (str_contains($namaBidang, 'UPTD') && str_contains($namaJabatan, 'Tata Usaha')) {
                    $subBagian = 'Sub Bagian Tata Usaha';
                }

                $bidangId = $bidangMap[$namaBidang] ?? null;
                if (!$bidangId && $namaBidang !== '') {
                    $bidang = Bidang::firstOrCreate(
                        ['nama_bidang' => $namaBidang],
                        [
                            'kode_bidang'       => strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $namaBidang), 0, 10)),
                            'kepala_bidang'     => '-',
                            'nip_kepala_bidang' => '-',
                            'keterangan'        => $namaBidang,
                            'is_active'         => true,
                        ]
                    );
                    $bidangId = $bidang->id;
                    $bidangMap[$namaBidang] = $bidangId;
                }

                $jabatanId = $jabatanMap[$namaJabatan] ?? null;
                if (!$jabatanId && $namaJabatan !== '') {
                    $jabatan = Jabatan::firstOrCreate(
                        ['nama_jabatan' => $namaJabatan],
                        [
                            'kode_jabatan' => strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $namaJabatan), 0, 10)),
                            'golongan'     => null,
                            'eselon'       => null,
                            'is_active'    => true,
                        ]
                    );
                    $jabatanId = $jabatan->id;
                    $jabatanMap[$namaJabatan] = $jabatanId;
                }

                // Update User
                $user = User::where('nip', $nip)->first();
                if ($user) {
                    $user->name = $namaLengkap;
                    $user->save();
                }

                // Update Pegawai
                $pegawai = Pegawai::where('nip', $nip)->first();
                if ($pegawai) {
                    $pegawai->nama_lengkap  = $namaLengkap;
                    $pegawai->bidang_id     = $bidangId;
                    $pegawai->sub_bagian    = $subBagian;
                    $pegawai->jabatan_id    = $jabatanId;
                    $pegawai->jenis_kelamin = in_array($jenisKelamin, ['L', 'P']) ? $jenisKelamin : 'L';
                    $pegawai->tempat_lahir  = $tempatLahir ?: '-';
                    $pegawai->jenis_pegawai = in_array($jenisPegawai, ['PNS', 'PPPK', 'Honorer']) ? $jenisPegawai : 'PNS';
                    $pegawai->pangkat        = $pangkat ?: '-';
                    $pegawai->save();
                }
            }
        });

        fclose($handle);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse action
    }
};
