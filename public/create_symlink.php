<?php

/**
 * Helper script untuk membuat Storage Link di Hostinger Shared Hosting.
 * Akses file ini via browser: https://domain-anda.com/create_symlink.php
 */

$target = __DIR__ . '/storage/app/public';
$shortcut = __DIR__ . '/storage';

echo "<h2>SIPENCUTI — Storage Link Generator</h2>";

if (file_exists($shortcut)) {
    if (is_link($shortcut)) {
        echo "<p style='color:green;'>✓ Link storage sudah ada dan aktif!</p>";
    } else {
        echo "<p style='color:orange;'>! Folder storage biasa ditemukan. Menghapus folder biasa untuk membuat symlink...</p>";
        // Jika folder biasa, hapus agar bisa dibuat symlink
        rename($shortcut, $shortcut . '_backup_' . time());
        if (symlink($target, $shortcut)) {
            echo "<p style='color:green;'>✓ BERHASIL! Storage symlink berhasil dibuat di Hostinger!</p>";
        } else {
            echo "<p style='color:red;'>X Gagal membuat symlink via symlink().</p>";
        }
    }
} else {
    if (symlink($target, $shortcut)) {
        echo "<p style='color:green;'>✓ BERHASIL! Storage symlink berhasil dibuat di Hostinger!</p>";
    } else {
        echo "<p style='color:red;'>X Gagal membuat symlink.</p>";
    }
}

echo "<hr><p>Sekarang upload foto pegawai & download surat PDF akan berjalan 100% normal!</p>";
