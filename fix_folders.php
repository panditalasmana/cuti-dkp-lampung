<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$dir = __DIR__ . '/app/Http/Controllers';
if (!is_dir($dir)) {
    $dir = __DIR__ . '/../app/Http/Controllers';
}

echo "Target Directory: " . realpath($dir) . "<br>";

$renamed = false;
if (file_exists($dir . '/admin')) {
    rename($dir . '/admin', $dir . '/Admin');
    echo "✓ Renamed folder: admin -> Admin<br>";
    $renamed = true;
}
if (file_exists($dir . '/pegawai')) {
    rename($dir . '/pegawai', $dir . '/Pegawai');
    echo "✓ Renamed folder: pegawai -> Pegawai<br>";
    $renamed = true;
}

if (!$renamed) {
    echo "✓ Folder Admin & Pegawai SUDAH berhuruf besar (OK).<br>";
}

// Clear view/route cache
$cacheDir = __DIR__ . '/bootstrap/cache';
if (is_dir($cacheDir)) {
    foreach (glob($cacheDir . '/*.php') as $f) {
        if (basename($f) !== '.gitignore') @unlink($f);
    }
    echo "✓ Cache bootstrap dibersihkan.<br>";
}

echo "<h3 style='color:green;'>✅ FIX SELESAI PERMANEN! Silakan coba login lagi.</h3>";
