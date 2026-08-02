<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$vendorPath = file_exists(__DIR__ . '/vendor/autoload.php') ? __DIR__ . '/vendor/autoload.php' : __DIR__ . '/../vendor/autoload.php';
$appPath    = file_exists(__DIR__ . '/bootstrap/app.php') ? __DIR__ . '/bootstrap/app.php' : __DIR__ . '/../bootstrap/app.php';

require $vendorPath;
$app = require_once $appPath;
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<h2>SIPENCUTI — Controller Directory & Cache Fixer</h2><ul>";

try {
    // Clear all laravel caches
    $kernel->call('optimize:clear');
    echo "<li>✓ Optimize & Route Cache Cleared</li>";

    $basePath = base_path('app/Http/Controllers');
    echo "<li>📁 Checking Controllers Directory: <strong>{$basePath}</strong></li>";

    $dirAdminLower   = $basePath . '/admin';
    $dirAdminUpper   = $basePath . '/Admin';
    $dirPegawaiLower = $basePath . '/pegawai';
    $dirPegawaiUpper = $basePath . '/Pegawai';

    if (file_exists($dirAdminLower) && !file_exists($dirAdminUpper)) {
        rename($dirAdminLower, $dirAdminUpper);
        echo "<li>✓ Successfully renamed <code>admin</code> -> <code>Admin</code></li>";
    } else {
        echo "<li>✓ Folder Admin status: OK</li>";
    }

    if (file_exists($dirPegawaiLower) && !file_exists($dirPegawaiUpper)) {
        rename($dirPegawaiLower, $dirPegawaiUpper);
        echo "<li>✓ Successfully renamed <code>pegawai</code> -> <code>Pegawai</code></li>";
    } else {
        echo "<li>✓ Folder Pegawai status: OK</li>";
    }

    // Run dump-autoload via composer if possible or re-optimize
    $kernel->call('optimize');

    echo "</ul><h3 style='color:green;'>✅ PERBAIKAN SELESAI! Folder Admin & Pegawai dipastikan berhuruf besar (Case-Sensitive Linux Hostinger).</h3>";
} catch (\Throwable $e) {
    echo "</ul><h3 style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</h3>";
}
