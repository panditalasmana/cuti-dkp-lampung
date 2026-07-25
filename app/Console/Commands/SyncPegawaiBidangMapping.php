<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PegawaiService;

class SyncPegawaiBidangMapping extends Command
{
    protected $signature = 'pegawai:sync-bidang {file : Path ke file JSON mapping nama->bidang}';
    protected $description = 'Cocokkan bidang/sub_bagian pegawai dari mapping JSON (menggantikan script root update_pegawai_bidang.php)';

    public function handle(PegawaiService $service): int
    {
        $result = $service->syncBidangFromJsonMapping($this->argument('file'));

        foreach ($result['log'] as $line) {
            $this->line($line);
        }

        $this->info("Selesai. Diperbarui: {$result['updated']}, tidak cocok: {$result['not_found']}");
        return self::SUCCESS;
    }
}