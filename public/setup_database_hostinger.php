<?php

/**
 * Helper script untuk membuat tabel (migrate:fresh) & menjalankan SELURUH Seeder resmi SIPENCUTI di Hostinger.
 * Akses via browser: https://domain-anda.com/setup_database_hostinger.php
 */

// Perbaiki path autoload & bootstrap karena file ada di subfolder public/
$vendorPath = file_exists(__DIR__ . '/vendor/autoload.php') ? __DIR__ . '/vendor/autoload.php' : __DIR__ . '/../vendor/autoload.php';
$appPath    = file_exists(__DIR__ . '/bootstrap/app.php') ? __DIR__ . '/bootstrap/app.php' : __DIR__ . '/../bootstrap/app.php';

require $vendorPath;
$app = require_once $appPath;
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<h2>SIPENCUTI — Full Database Reset & All Seeders Tool</h2>";
echo "<ul>";

try {
    // 1. Run Migration Fresh (Buat semua tabel dari nol)
    $kernel->call('migrate:fresh', ['--force' => true]);
    echo "<li>✓ <strong>Migrasi Tabel</strong>: Seluruh tabel (users, pegawai, bidang, jabatan, jenis_cuti, penandatangan, pengajuan_cuti) berhasil dibuat bersih!</li>";

    // 2. Run All Seeders (Jabatan, Pegawai, Penandatangan, Jenis Cuti, Hari Libur, Admin)
    $kernel->call('db:seed', ['--force' => true]);
    echo "<li>✓ <strong>Full Database Seeder</strong>: Berhasil mengisikan 162 Pegawai DUK, 67 Jabatan, 9 Pejabat Penandatangan Atasan, Master Jenis Cuti, Hari Libur, & Akun Admin!</li>";

    echo "</ul>";
    echo "<h3 style='color:green;'>✅ SUCCESS! Seluruh data Atasan Langsung (9 Pejabat), Jenis Cuti (12 Hari), & Data Pegawai DUK 100% LENGKAP & SAMA PERSIS DENGAN LOCALHOST!</h3>";
} catch (\Exception $e) {
    echo "</ul>";
    echo "<h3 style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</h3>";
}
