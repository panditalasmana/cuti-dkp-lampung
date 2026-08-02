<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
} else {
    die("Error: File vendor/autoload.php tidak ditemukan.");
}

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<h2>SIPENCUTI — Full Database Setup Tool</h2><ul>";

try {
    $kernel->call('migrate:fresh', ['--force' => true]);
    echo "<li>✓ Migrasi Tabel Sukses</li>";

    $kernel->call('db:seed', ['--force' => true]);
    echo "<li>✓ Full Database Seeder Sukses</li>";

    echo "</ul><h3 style='color:green;'>✅ SUCCESS! Database 100% Terisi Lengkap!</h3>";
} catch (\Throwable $e) {
    echo "</ul><h3 style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</h3>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
