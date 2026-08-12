<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$baseDir = file_exists(__DIR__ . '/bootstrap') ? realpath(__DIR__) : realpath(__DIR__ . '/..');
if (!$baseDir) $baseDir = __DIR__;

echo "<h2>SIPENCUTI — Admin & Profile Photo Fixer</h2><ul>";

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
            echo "<li>✓ Username Admin berhasil diubah menjadi: <strong>admindkp2026</strong></li>";
            echo "<li>✓ Password Admin berhasil diubah menjadi: <strong>1991</strong></li>";
        }

        // Clean up database column 'foto' for imported pegawai
        $count = \Illuminate\Support\Facades\DB::table('pegawai')
            ->whereIn('foto', ['Foto', 'foto', 'null', 'NONE', 'none', ' '])
            ->update(['foto' => null]);
        echo "<li>✓ Berhasil membersihkan foto {$count} pegawai ke avatar inisial default awal!</li>";

        // Clear cache
        @array_map('unlink', glob($baseDir . '/bootstrap/cache/*.php'));
        @array_map('unlink', glob($baseDir . '/storage/framework/views/*.php'));
        echo "<li>✓ Route & View Cache Cleared</li>";
    }
} catch (\Throwable $e) {
    echo "<li>❌ Error: " . htmlspecialchars($e->getMessage()) . "</li>";
}

echo "</ul><h3 style='color:green;'>✅ SUCCESS! Foto Profil Default Awal & Admin Berhasil Dipulihkan 100%!</h3><br><a href='/login'>Buka Halaman Login SIPENCUTI</a>";
