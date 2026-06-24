<?php

namespace Database\Seeders;

use App\Models\Bidang;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('data/pegawai.csv');

        if (!file_exists($file)) {
            $this->command->error('File pegawai.csv tidak ditemukan di: ' . $file);
            return;
        }

        // Hapus data lama dengan aman
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Pegawai::truncate();
        User::where('role', 'pegawai')->forceDelete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Index bidang & jabatan (nama → id)
        $bidangMap  = Bidang::pluck('id', 'nama_bidang')->toArray();
        $jabatanMap = Jabatan::pluck('id', 'nama_jabatan')->toArray();

        $passwordDefault = Hash::make('password123');

        $handle = fopen($file, 'r');
        fgetcsv($handle, 0, ','); // skip header

        $inserted = 0;
        $skipped  = 0;
        $errors   = [];

        DB::transaction(function () use (
            $handle, &$bidangMap, &$jabatanMap,
            $passwordDefault, &$inserted, &$skipped, &$errors
        ) {
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
                $sisaCuti     = isset($row[10]) && $row[10] !== '' ? (int) trim($row[10]) : 12;

                if (strlen($nip) < 10) {
                    $errors[] = "NIP tidak valid: '{$nip}' ({$namaLengkap})";
                    $skipped++;
                    continue;
                }

                if (User::where('nip', $nip)->exists()) {
                    $skipped++;
                    continue;
                }

                // ── Cari atau buat Bidang ────────────────────────────────
                $bidangId = $bidangMap[$namaBidang] ?? null;
                if (!$bidangId && $namaBidang !== '') {
                    $bidang = Bidang::firstOrCreate(
                        ['nama_bidang' => $namaBidang],
                        [
                            'kode_bidang'       => $this->uniqueKode($namaBidang, Bidang::pluck('kode_bidang')->toArray()),
                            'kepala_bidang'     => '-',
                            'nip_kepala_bidang' => '-',
                            'keterangan'        => $namaBidang,
                            'is_active'         => true,
                        ]
                    );
                    $bidangId = $bidang->id;
                    $bidangMap[$namaBidang] = $bidangId;
                }

                // ── Cari atau buat Jabatan ───────────────────────────────
                $jabatanId = $jabatanMap[$namaJabatan] ?? null;
                if (!$jabatanId && $namaJabatan !== '') {
                    $jabatan = Jabatan::firstOrCreate(
                        ['nama_jabatan' => $namaJabatan],
                        [
                            'kode_jabatan' => $this->uniqueKode($namaJabatan, Jabatan::pluck('kode_jabatan')->toArray()),
                            'golongan'     => null,
                            'eselon'       => null,
                            'is_active'    => true,
                        ]
                    );
                    $jabatanId = $jabatan->id;
                    $jabatanMap[$namaJabatan] = $jabatanId;
                }

                // ── Buat User ────────────────────────────────────────────
                $user = User::create([
                    'nip'       => $nip,
                    'name'      => $namaLengkap,
                    'email'     => $nip . '@dkplampung.id',
                    'password'  => $passwordDefault,
                    'role'      => 'pegawai',
                    'is_active' => true,
                ]);

                // ── Buat Pegawai ─────────────────────────────────────────
                Pegawai::create([
                    'nip'               => $nip,
                    'user_id'           => $user->id,
                    'bidang_id'         => $bidangId,
                    'jabatan_id'        => $jabatanId,
                    'nama_lengkap'      => $namaLengkap,
                    'jenis_kelamin'     => in_array($jenisKelamin, ['L', 'P']) ? $jenisKelamin : 'L',
                    'tempat_lahir'      => $tempatLahir ?: '-',
                    'tanggal_lahir'     => $this->parseDate($tanggalLahir),
                    'tanggal_masuk'     => $this->parseDate($tanggalMasuk),
                    'jenis_pegawai'     => in_array($jenisPegawai, ['PNS', 'PPPK', 'Honorer']) ? $jenisPegawai : 'PNS',
                    'pangkat'           => $pangkat ?: '-',
                    'sisa_cuti_tahunan' => $sisaCuti,
                    'is_active'         => true,
                ]);

                $inserted++;
            }
        });

        fclose($handle);

        $this->command->info('');
        $this->command->info('✅ PegawaiSeeder selesai!');
        $this->command->info("   Inserted : {$inserted}");
        $this->command->info("   Skipped  : {$skipped}");

        if (!empty($errors)) {
            $this->command->warn('⚠️  Peringatan:');
            foreach ($errors as $e) {
                $this->command->warn("   - {$e}");
            }
        }
    }

    /**
     * Buat kode unik — jika kode sudah ada, tambahkan angka di belakang
     * Contoh: KEPALAUPTD sudah ada → KEPALAUPTD2, KEPALAUPTD3, dst.
     */
    private function uniqueKode(string $nama, array $existingKodes): string
    {
        $base = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $nama), 0, 10));
        if (empty($base)) $base = 'LAINNYA';

        $kode = $base;
        $i = 2;
        while (in_array($kode, $existingKodes)) {
            $suffix = (string) $i;
            $kode   = substr($base, 0, 10 - strlen($suffix)) . $suffix;
            $i++;
        }
        return $kode;
    }

    /**
     * Parse berbagai format tanggal ke Y-m-d
     */
    private function parseDate(string $value): string
    {
        $value = trim($value);
        if (empty($value)) return date('Y-m-d');
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) return $value;
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $value, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }
        $ts = strtotime($value);
        return $ts ? date('Y-m-d', $ts) : date('Y-m-d');
    }
}