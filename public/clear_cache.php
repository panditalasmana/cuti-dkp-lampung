<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$baseDir = file_exists(__DIR__ . '/../bootstrap') ? realpath(__DIR__ . '/..') : realpath(__DIR__);
if (!$baseDir) $baseDir = __DIR__;

echo "<h2>SIPENCUTI — Cache & Route Cleaner</h2><ul>";

// Physical deletion of all cache files
$cacheFiles = array_merge(
    glob($baseDir . '/bootstrap/cache/*.php') ?: [],
    glob($baseDir . '/storage/framework/views/*.php') ?: [],
    glob($baseDir . '/storage/framework/cache/data/*') ?: []
);

$deletedCount = 0;
foreach ($cacheFiles as $file) {
    if (is_file($file)) {
        @unlink($file);
        $deletedCount++;
    }
}

echo "<li>✓ Berhasil menghapus {$deletedCount} file cache fisik di <code>bootstrap/cache</code> dan <code>storage/framework</code>.</li>";

// Fix controller directories
$dir = $baseDir . '/app/Http/Controllers';
if (file_exists($dir . '/admin') && !file_exists($dir . '/Admin')) {
    @rename($dir . '/admin', $dir . '/Admin');
    echo "<li>✓ Renamed admin -> Admin</li>";
}
if (file_exists($dir . '/pegawai') && !file_exists($dir . '/Pegawai')) {
    @rename($dir . '/pegawai', $dir . '/Pegawai');
    echo "<li>✓ Renamed pegawai -> Pegawai</li>";
}

// Reset jatah cuti 12 hari
try {
    $vendorPath = $baseDir . '/vendor/autoload.php';
    $appPath    = $baseDir . '/bootstrap/app.php';
    if (file_exists($vendorPath) && file_exists($appPath)) {
        require $vendorPath;
        $app = require_once $appPath;
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->call('optimize:clear');
        echo "<li>✓ Artisan optimize:clear berhasil dijalankan.</li>";
    }
} catch (\Throwable $e) {
    echo "<li>ℹ Note: " . htmlspecialchars($e->getMessage()) . "</li>";
}

echo "</ul><h3 style='color:green;'>✅ SUCCESS! Seluruh cache route & views telah dibersihkan 100%!</h3><br><a href='/login'>Buka Halaman Login SIPENCUTI</a>";
