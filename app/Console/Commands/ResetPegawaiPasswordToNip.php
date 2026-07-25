<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\PegawaiService;

class ResetPegawaiPasswordToNip extends Command
{
    protected $signature = 'pegawai:reset-password {--nip= : Reset hanya untuk satu NIP tertentu}';
    protected $description = 'Reset password pegawai ke 4 digit awal NIP (menggantikan migration update_pegawai_passwords_to_nip_prefix)';

    public function handle(PegawaiService $service): int
    {
        $query = User::where('role', 'pegawai')->whereNotNull('nip');

        if ($nipFilter = $this->option('nip')) {
            $query->where('nip', $nipFilter);
        }

        $pegawais = $query->get();

        if ($pegawais->isEmpty()) {
            $this->warn('Tidak ada pegawai yang cocok.');
            return self::SUCCESS;
        }

        if (!$this->confirm("Akan reset password untuk {$pegawais->count()} pegawai. Lanjutkan?")) {
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($pegawais as $user) {
            $nip = preg_replace('/\s+/', '', trim($user->nip));
            if (strlen($nip) >= 4) {
                $service->gantiPassword($user, substr($nip, 0, 4));
                $count++;
            }
        }

        $this->info("Selesai. {$count} password direset.");
        return self::SUCCESS;
    }
}