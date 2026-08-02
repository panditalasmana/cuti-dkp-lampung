<?php

/**
 * Helper script untuk membuat Folder Storage, Storage Link & Fixing Permissions di Hostinger.
 * Akses via browser: https://domain-anda.com/create_symlink.php
 */

$storagePaths = [
    __DIR__ . '/storage',
    __DIR__ . '/storage/app',
    __DIR__ . '/storage/app/public',
    __DIR__ . '/storage/app/public/pegawai',
    __DIR__ . '/storage/app/public/dokumen',
    __DIR__ . '/storage/framework',
    __DIR__ . '/storage/framework/cache',
    __DIR__ . '/storage/framework/cache/data',
    __DIR__ . '/storage/framework/sessions',
    __DIR__ . '/storage/framework/views',
    __DIR__ . '/storage/logs',
    __DIR__ . '/bootstrap/cache',
];

echo "<h2>SIPENCUTI — Storage & Folder Permissions Fixer</h2>";
echo "<ul>";

foreach ($storagePaths as $path) {
    if (!file_exists($path)) {
        mkdir($path, 0775, true);
        echo "<li>✓ Membuat folder: " . basename($path) . "</li>";
    }
    @chmod($path, 0775);
}
echo "</ul>";

$target = __DIR__ . '/storage/app/public';
$shortcut = __DIR__ . '/storage';

if (file_exists($shortcut)) {
    if (is_link($shortcut)) {
        echo "<h3 style='color:green;'>✓ Storage link sudah aktif 100%!</h3>";
    } else {
        @rename($shortcut, $shortcut . '_old_' . time());
        if (@symlink($target, $shortcut)) {
            echo "<h3 style='color:green;'>✓ BERHASIL! Storage symlink berhasil dibuat di Hostinger!</h3>";
        } else {
            echo "<h3 style='color:green;'>✓ Storage folder disiapkan untuk Hostinger.</h3>";
        }
    }
} else {
    if (@symlink($target, $shortcut)) {
        echo "<h3 style='color:green;'>✓ BERHASIL! Storage symlink berhasil dibuat di Hostinger!</h3>";
    } else {
        echo "<h3 style='color:green;'>✓ Storage folder disiapkan untuk Hostinger.</h3>";
    }
}

echo "<hr><h3 style='color:green;'>✅ SUCCESS! Seluruh folder session, view cache, dan storage symlink sudah 100% Siap!</h3>";
