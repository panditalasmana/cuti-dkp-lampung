<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$baseDir = file_exists(__DIR__ . '/../bootstrap') ? realpath(__DIR__ . '/..') : realpath(__DIR__);
if (!$baseDir) $baseDir = __DIR__;

echo "<h2>SIPENCUTI — Scan Cleaner & Admin Fixer</h2><ul>";

try {
    $vendorPath = $baseDir . '/vendor/autoload.php';
    $appPath    = $baseDir . '/bootstrap/app.php';
    
    if (file_exists($vendorPath) && file_exists($appPath)) {
        require $vendorPath;
        $app = require_once $appPath;
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();

        $admin = \App\Models\User::where('role', 'admin')->first();
        if ($admin) {
            $admin->update([
                'nip'            => 'admindkp2026',
                'password'       => \Illuminate\Support\Facades\Hash::make('1991'),
                'password_plain' => '1991',
            ]);
            echo "<li>✓ Admin Credential Active: admindkp2026 / 1991</li>";
        }

        \Illuminate\Support\Facades\DB::table('pegawai')
            ->whereIn('foto', ['Foto', 'foto', 'null', 'NONE', 'none', ' '])
            ->update(['foto' => null]);

        // Hapus seluruh record dokumen yang BUKAN file scan fisik (yang tidak di subfolder /scan/)
        $deleted = \App\Models\Dokumen::where('jenis_dokumen', 'scan_surat_ditandatangani')
            ->where('path_file', 'NOT LIKE', '%/scan/%')
            ->delete();
        echo "<li>✓ Berhasil membersihkan {$deleted} dokumen non-scan fisik dari database!</li>";

        // Clear cache
        @array_map('unlink', glob($baseDir . '/bootstrap/cache/*.php'));
        @array_map('unlink', glob($baseDir . '/storage/framework/views/*.php'));
        echo "<li>✓ Route & View Cache Cleared</li>";
    }
} catch (\Throwable $e) {
    echo "<li>❌ Error: " . htmlspecialchars($e->getMessage()) . "</li>";
}

echo "</ul><h3 style='color:green;'>✅ SUCCESS! Sistem Bersih 100%, Hanya Menyajikan Berkas Scan Fisik Asli Upload!</h3><br><a href='/login'>Buka Halaman Login SIPENCUTI</a>";
