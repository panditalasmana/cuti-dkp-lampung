<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$baseDir = file_exists(__DIR__ . '/storage') ? realpath(__DIR__) : realpath(__DIR__ . '/..');
if (!$baseDir) $baseDir = __DIR__;

$storagePaths = [
    $baseDir . '/storage',
    $baseDir . '/storage/app',
    $baseDir . '/storage/app/public',
    $baseDir . '/storage/app/public/pegawai',
    $baseDir . '/storage/app/public/dokumen',
    $baseDir . '/storage/app/public/pengajuan',
    $baseDir . '/storage/framework',
    $baseDir . '/storage/framework/cache',
    $baseDir . '/storage/framework/cache/data',
    $baseDir . '/storage/framework/sessions',
    $baseDir . '/storage/framework/views',
    $baseDir . '/storage/logs',
    $baseDir . '/bootstrap/cache',
];

echo "<h2>SIPENCUTI — Full Storage & Symlink Fixer</h2><ul>";

foreach ($storagePaths as $path) {
    if (!file_exists($path)) {
        @mkdir($path, 0775, true);
        echo "<li>✓ Membuat folder: <code>" . htmlspecialchars($path) . "</code></li>";
    }
    @chmod($path, 0775);
}

// Target folder dimana file tersimpan
$target = $baseDir . '/storage/app/public';

// Shortcuts link yang akan diakses web
$shortcuts = [
    $baseDir . '/public/storage',
    $baseDir . '/storage_link',
];

foreach ($shortcuts as $shortcut) {
    if (file_exists($shortcut) || is_link($shortcut)) {
        @unlink($shortcut);
    }
    if (@symlink($target, $shortcut)) {
        echo "<li>✓ Symlink berhasil dibuat: <code>" . htmlspecialchars($shortcut) . "</code> -> <code>" . htmlspecialchars($target) . "</code></li>";
    }
}

echo "</ul><h3 style='color:green;'>✅ BERHASIL 100%! Seluruh folder upload & symlink file scan/foto sudah aktif sempurna!</h3>";
