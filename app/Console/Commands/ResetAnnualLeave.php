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
        $this->info('Memulai reset sisa cuti tahunan pegawai...');
        
        // Reset all active pegawai to 12
        $count = Pegawai::where('is_active', true)->update(['sisa_cuti_tahunan' => 12]);
        
        $this->info("Berhasil mereset sisa cuti tahunan untuk {$count} pegawai.");
        
        return Command::SUCCESS;
    }
}
