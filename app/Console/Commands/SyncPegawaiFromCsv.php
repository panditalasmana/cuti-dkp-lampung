<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PegawaiService;

class SyncPegawaiFromCsv extends Command
{
    protected $signature = 'pegawai:sync {file? : Path ke file CSV DUK resmi}';
    protected $description = 'Sinkronisasi nama & data pegawai dari CSV DUK resmi (menggantikan migration fix_pegawai_name_formats)';

    public function handle(PegawaiService $service): int
    {
        $file = $this->argument('file') ?? database_path('data/pegawai_format_resmi_duk.csv');

        if (!file_exists($file)) {
            $this->error("File tidak ditemukan: {$file}");
            return self::FAILURE;
        }

        $this->info("Menyinkronkan data dari: {$file}");
        $stats = $service->syncFromDukCsv($file);

        $this->info("Selesai. Diperbarui: {$stats['updated']}, dilewati (NIP tidak cocok): {$stats['dilewati']}");

        if (!empty($stats['bidang_baru'])) {
            $this->comment('Bidang baru otomatis dibuat: ' . implode(', ', $stats['bidang_baru']));
        }
        if (!empty($stats['jabatan_baru'])) {
            $this->comment('Jabatan baru otomatis dibuat: ' . implode(', ', $stats['jabatan_baru']));
        }

        return self::SUCCESS;
    }
}