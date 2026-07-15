<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pegawais = App\Models\Pegawai::whereHas('jabatan', function($q) {
        $q->where('nama_jabatan', 'like', '%Kepala Seksi%');
    })
    ->with(['jabatan', 'bidang'])
    ->get();

foreach ($pegawais as $p) {
    echo "NAMA: {$p->nama_lengkap} | NIP: {$p->nip} | JABATAN: " . ($p->jabatan->nama_jabatan ?? '-') . " | BIDANG: " . ($p->bidang->nama_bidang ?? '-') . "\n";
}
