<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================
        // ADMIN UTAMA
        // ==========================
        User::updateOrCreate(
            [
                'nip' => '198501012010011001',
            ],
            [
                'name'      => 'Administrator DKP',
                'email'     => 'admin@dkp.lampungprov.go.id',
                'password'  => Hash::make('Admin@DKP2026'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        // ==========================
        // USER PEGAWAI DEMO
        // ==========================
        User::updateOrCreate(
            [
                'nip' => ' ',
            ],
            [
                'name'      => 'Ir. Bani Ispriyanto M.M.',
                'email'     => '196904101995031002@dkplampung.id',
                'password'  => Hash::make('password123'),
                'role'      => 'pegawai',
                'is_active' => true,
            ]
        );

        $this->command->info('✅ UserSeeder berhasil dijalankan.');
    }
}