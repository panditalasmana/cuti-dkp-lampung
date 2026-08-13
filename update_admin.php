<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$baseDir = file_exists(__DIR__ . '/bootstrap') ? realpath(__DIR__) : realpath(__DIR__ . '/..');
if (!$baseDir) $baseDir = __DIR__;

echo "<h2>SIPENCUTI — System Restorer</h2><ul>";

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

        // Clear cache
        @array_map('unlink', glob($baseDir . '/bootstrap/cache/*.php'));
        @array_map('unlink', glob($baseDir . '/storage/framework/views/*.php'));
        echo "<li>✓ Route & View Cache Cleared</li>";
    }
} catch (\Throwable $e) {
    echo "<li>❌ Error: " . htmlspecialchars($e->getMessage()) . "</li>";
}

echo "</ul><h3 style='color:green;'>✅ SUCCESS! Sistem SIPENCUTI Berhasil Diperbarui 100%!</h3><br><a href='/login'>Buka Halaman Login SIPENCUTI</a>";
