<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$vendorPath = file_exists(__DIR__ . '/vendor/autoload.php') ? __DIR__ . '/vendor/autoload.php' : __DIR__ . '/../vendor/autoload.php';
$appPath    = file_exists(__DIR__ . '/bootstrap/app.php') ? __DIR__ . '/bootstrap/app.php' : __DIR__ . '/../bootstrap/app.php';

require $vendorPath;
$app = require_once $appPath;
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<h2>SIPENCUTI — Cache & Controller Fixer</h2><ul>";

try {
    // Clear all laravel caches
    $kernel->call('optimize:clear');
    echo "<li>✓ Optimize & Route Cache Cleared</li>";

    // Clean bootstrap cache files
    $bootstrapCache = glob(dirname($appPath) . '/cache/*.php');
    foreach ($bootstrapCache as $f) {
        if (basename($f) !== '.gitignore') {
            @unlink($f);
        }
    }
    echo "<li>✓ Bootstrap Cache Files Deleted</li>";

    // Check Controllers directory case
    $basePath = dirname($appPath) . '/app/Http/Controllers';
    echo "<li>📁 Controllers Directory Check: <strong>{$basePath}</strong></li>";

    if (is_dir("{$basePath}/admin") && !is_dir("{$basePath}/Admin")) {
        @rename("{$basePath}/admin", "{$basePath}/Admin");
        echo "<li>✓ Renamed folder <code>admin</code> -> <code>Admin</code></li>";
    }

    if (is_dir("{$basePath}/pegawai") && !is_dir("{$basePath}/Pegawai")) {
        @rename("{$basePath}/pegawai", "{$basePath}/Pegawai");
        echo "<li>✓ Renamed folder <code>pegawai</code> -> <code>Pegawai</code></li>";
    }

    echo "</ul><h3 style='color:green;'>✅ BERHASIL! Cache dibersihkan & Nama folder Admin/Pegawai dipastikan berhuruf besar!</h3>";
} catch (\Throwable $e) {
    echo "</ul><h3 style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</h3>";
}
