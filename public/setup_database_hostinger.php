<?php

/**
 * Helper script untuk memperbarui data database & seeder di Hostinger secara otomatis.
 * Akses via browser: https://domain-anda.com/setup_database_hostinger.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<h2>SIPENCUTI — Database Update & Cleanup Tool</h2>";
echo "<ul>";

try {
    // 1. Seed Jabatan
    $kernel->call('db:seed', ['--class' => 'JabatanSeeder', '--force' => true]);
    echo "<li>✓ JabatanSeeder berhasil dijalankan.</li>";

    // 2. Cleanup Jabatan Duplikat
    $kernel->call('jabatan:cleanup');
    echo "<li>✓ Cleanup Jabatan duplikat berhasil diselesaikan.</li>";

    // 3. Seed Pegawai Resmi DUK
    $kernel->call('db:seed', ['--class' => 'PegawaiSeeder', '--force' => true]);
    echo "<li>✓ PegawaiSeeder (Data Resmi DUK) berhasil diproses.</li>";

    echo "</ul>";
    echo "<h3 style='color:green;'>✅ BERHASIL! Seluruh nama pegawai, jabatan, dan bidang di Hostinger sudah 100% diperbarui sesuai data resmi terkini!</h3>";
} catch (\Exception $e) {
    echo "</ul>";
    echo "<h3 style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</h3>";
}
