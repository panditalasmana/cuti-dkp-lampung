<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pegawai;

class ResetAnnualLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-annual-leave';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset sisa cuti tahunan pegawai menjadi 12 hari setiap awal tahun';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai reset sisa cuti tahunan pegawai dengan akumulasi (BKN)...');
        
        $pegawais = Pegawai::where('is_active', true)->get();
        $count = 0;
        
        foreach ($pegawais as $pegawai) {
            $sisaLama = $pegawai->sisa_cuti_tahunan;
            // Maksimal akumulasi sisa cuti tahun lalu adalah 6 hari kerja
            $carryOver = min(max($sisaLama, 0), 6);
            $sisaBaru = 12 + $carryOver;
            
            $pegawai->update(['sisa_cuti_tahunan' => $sisaBaru]);
            $count++;
        }
        
        $this->info("Berhasil mereset jatah cuti dengan akumulasi untuk {$count} pegawai.");
        
        return Command::SUCCESS;
    }
}
