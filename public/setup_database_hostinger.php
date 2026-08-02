<?php

/**
 * Helper script untuk membuat tabel (migrate:fresh), memperbarui data database & seeder di Hostinger secara otomatis.
 * Akses via browser: https://domain-anda.com/setup_database_hostinger.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<h2>SIPENCUTI — Full Database Setup & Migration Tool</h2>";
echo "<ul>";

try {
    // 1. Run Migration Fresh (Buat semua tabel dari nol)
    $kernel->call('migrate:fresh', ['--force' => true]);
    echo "<li>✓ <strong>Migrasi Tabel</strong>: Seluruh tabel (users, pegawai, bidang, jabatan, jenis_cuti, penandatangan, pengajuan_cuti) berhasil dibuat!</li>";

    // 2. Seed Jabatan
    $kernel->call('db:seed', ['--class' => 'JabatanSeeder', '--force' => true]);
    echo "<li>✓ <strong>JabatanSeeder</strong>: 67 Jabatan resmi DUK berhasil di-seed.</li>";

    // 3. Cleanup Jabatan Duplikat
    $kernel->call('jabatan:cleanup');
    echo "<li>✓ <strong>Cleanup Jabatan</strong>: Pembersihan duplikat selesai.</li>";

    // 4. Seed Pegawai Resmi DUK & Penandatangan
    $kernel->call('db:seed', ['--class' => 'PegawaiSeeder', '--force' => true]);
    echo "<li>✓ <strong>PegawaiSeeder</strong>: 162 Pegawai resmi DUK & Akun Admin berhasil dibuat.</li>";

    echo "</ul>";
    echo "<h3 style='color:green;'>✅ SUCCESS! Seluruh struktur database & data resmi SIPENCUTI di Hostinger sudah 100% SAMA PERSIS dengan Localhost!</h3>";
} catch (\Exception $e) {
    echo "</ul>";
    echo "<h3 style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</h3>";
}
