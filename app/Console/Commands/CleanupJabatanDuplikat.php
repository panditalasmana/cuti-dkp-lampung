<?php

namespace App\Console\Commands;

use App\Models\Jabatan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupJabatanDuplikat extends Command
{
    protected $signature   = 'jabatan:cleanup';
    protected $description = 'Hapus jabatan duplikat (0 pegawai) dan pastikan jabatan bersih sesuai CSV.';

    // Mapping: kode_jabatan lama (duplikat 0 pegawai) → kode_jabatan baru yang sudah punya pegawai
    private array $duplikatMap = [
        // Kode lama => kode baru yang benar
        'KASUBAG_UMUM'     => 'KASUBAG_UMUM',      // akan di-rename nama-nya
        'KASUBAG_KEUANGAN' => 'KASUBAG_KEUANGAN',   // akan di-rename nama-nya
        'KASUBAG_TU'       => null,                  // hapus saja, sudah ada versi spesifik
        'KASIE'            => null,                  // hapus saja, terlalu generik
    ];

    public function handle(): int
    {
        $this->info('=== Membersihkan duplikat jabatan ===');

        DB::beginTransaction();
        try {
            // 1. Hapus jabatan yang 0 pegawai DAN nama-nya SUDAH ADA versi yang lebih spesifik
            $kodeSimpan = [
                'KASUBAG_UMUM', 'KASUBAG_KEUANGAN',
                'KASUBAG_TU_UPTDBA','KASUBAG_TU_UPTDP4','KASUBAG_TU_UPTDP2',
                'KASUBAG_TU_UPTDP3','KASUBAG_TU_UPTDPE',
            ];

            // Hapus KASUBAG_TU, KASIE, ANALIS (generik) karena sudah ada versi spesifik
            $generikHapus = ['KASUBAG_TU', 'KASIE', 'ANALIS'];
            foreach ($generikHapus as $kode) {
                $jabatan = Jabatan::where('kode_jabatan', $kode)->withCount('pegawai')->first();
                if ($jabatan && $jabatan->pegawai_count === 0) {
                    $jabatan->forceDelete();
                    $this->info("Hapus: {$kode} (0 pegawai, generik)");
                } elseif ($jabatan) {
                    $this->warn("Skip: {$kode} masih punya {$jabatan->pegawai_count} pegawai");
                }
            }

            // 2. Update nama jabatan KASUBAG_UMUM dan KASUBAG_KEUANGAN ke nama lengkap
            $updateNama = [
                'KASUBAG_UMUM'     => 'Kepala Sub Bagian Umum dan Kepegawaian Sekretariat Dinas',
                'KASUBAG_KEUANGAN' => 'Kepala Sub Bagian Keuangan dan Aset Sekretariat Dinas',
            ];
            foreach ($updateNama as $kode => $namaLengkap) {
                $jabatan = Jabatan::where('kode_jabatan', $kode)->first();
                if ($jabatan) {
                    $jabatan->update(['nama_jabatan' => $namaLengkap]);
                    $this->info("Update nama: {$kode} → {$namaLengkap}");
                }
            }

            // 3. Hapus semua jabatan dengan kode JABATAN_* (auto-generate duplikat) yang 0 pegawai
            $autoJabatan = Jabatan::where('kode_jabatan', 'LIKE', 'JABATAN_%')
                ->withCount('pegawai')
                ->get();

            foreach ($autoJabatan as $jb) {
                // Cek apakah ada jabatan lain dengan nama sama (duplikat)
                $duplikat = Jabatan::where('nama_jabatan', $jb->nama_jabatan)
                    ->where('id', '!=', $jb->id)
                    ->first();

                if ($duplikat || $jb->pegawai_count === 0) {
                    // Jika ada duplikat dengan nama sama, pindahkan pegawai ke yang bukan auto
                    if ($duplikat && $jb->pegawai_count > 0) {
                        DB::table('pegawai')
                            ->where('jabatan_id', $jb->id)
                            ->update(['jabatan_id' => $duplikat->id]);
                        $this->info("Pindah pegawai: {$jb->kode_jabatan} → {$duplikat->kode_jabatan}");
                    }
                    $jb->forceDelete();
                    $this->info("Hapus auto-jabatan: {$jb->kode_jabatan} ({$jb->nama_jabatan})");
                }
            }

            // 4. Hapus jabatan lain dengan nama persis sama (duplikat nama)
            $semuaJabatan = Jabatan::orderBy('id')->get();
            $namaTracker = [];
            foreach ($semuaJabatan as $jb) {
                $nama = trim($jb->nama_jabatan);
                if (isset($namaTracker[$nama])) {
                    // Duplikat — cek pegawai, pindahkan dan hapus
                    $pegawaiCount = DB::table('pegawai')->where('jabatan_id', $jb->id)->count();
                    if ($pegawaiCount > 0) {
                        DB::table('pegawai')
                            ->where('jabatan_id', $jb->id)
                            ->update(['jabatan_id' => $namaTracker[$nama]]);
                        $this->info("Pindah {$pegawaiCount} pegawai dari ID {$jb->id} ke ID {$namaTracker[$nama]}");
                    }
                    $jb->forceDelete();
                    $this->info("Hapus duplikat nama: ID {$jb->id} — {$nama}");
                } else {
                    $namaTracker[$nama] = $jb->id;
                }
            }

            DB::commit();
            $this->info('');
            $this->info('✅ Selesai! Total jabatan bersih: ' . Jabatan::count());

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Gagal: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
