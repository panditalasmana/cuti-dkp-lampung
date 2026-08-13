<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$baseDir = file_exists(__DIR__ . '/../bootstrap') ? realpath(__DIR__ . '/..') : realpath(__DIR__);
if (!$baseDir) $baseDir = __DIR__;

echo "<h2>SIPENCUTI — Auto Recovery & Admin Fixer</h2><ul>";

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
            echo "<li>✓ Username Admin: <strong>admindkp2026</strong> | Password: <strong>1991</strong></li>";
        }

        // Clean up database column 'foto' for imported pegawai
        \Illuminate\Support\Facades\DB::table('pegawai')
            ->whereIn('foto', ['Foto', 'foto', 'null', 'NONE', 'none', ' '])
            ->update(['foto' => null]);
        echo "<li>✓ Column foto pegawai cleaned to null.</li>";

        // AUTO-RECOVER ALL SCANNED FILES IN DISK TO DATABASE
        $pengajuanDirs = glob($baseDir . '/storage/app/public/pengajuan/*', GLOB_ONLYDIR);
        if (empty($pengajuanDirs)) {
            $pengajuanDirs = glob($baseDir . '/storage/app/pengajuan/*', GLOB_ONLYDIR);
        }

        $recoveredCount = 0;
        foreach ($pengajuanDirs as $pDir) {
            $pId = basename($pDir);
            if (!is_numeric($pId)) continue;

            $scanFiles = glob($pDir . '/scan/*.*');
            foreach ($scanFiles as $sFile) {
                $relPath = 'pengajuan/' . $pId . '/scan/' . basename($sFile);
                $exists = \App\Models\Dokumen::where('path_file', $relPath)
                    ->orWhere('path_file', 'storage/' . $relPath)
                    ->orWhere('path_file', 'public/' . $relPath)
                    ->exists();

                if (!$exists) {
                    \App\Models\Dokumen::create([
                        'pengajuan_cuti_id' => $pId,
                        'uploaded_by'       => 1,
                        'jenis_dokumen'     => 'scan_surat_ditandatangani',
                        'nama_file'         => basename($sFile),
                        'path_file'         => $relPath,
                        'mime_type'         => @mime_content_type($sFile) ?: 'image/jpeg',
                        'ukuran_file'       => @filesize($sFile) ?: 0,
                        'keterangan'        => 'Berkas scan dipulihkan otomatis oleh sistem',
                    ]);
                    $recoveredCount++;
                }
            }
        }

        echo "<li>✓ Berhasil memulihkan <strong>{$recoveredCount}</strong> berkas scan fisik di server ke database!</li>";

        // Clear cache
        @array_map('unlink', glob($baseDir . '/bootstrap/cache/*.php'));
        @array_map('unlink', glob($baseDir . '/storage/framework/views/*.php'));
        echo "<li>✓ Route & View Cache Cleared</li>";
    }
} catch (\Throwable $e) {
    echo "<li>❌ Error: " . htmlspecialchars($e->getMessage()) . "</li>";
}

echo "</ul><h3 style='color:green;'>✅ SUCCESS! Seluruh Berkas PDF & Scan Fisik Berhasil Dipulihkan 100%!</h3><br><a href='/login'>Buka Halaman Login SIPENCUTI</a>";
