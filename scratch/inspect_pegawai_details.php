<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pegawais = \App\Models\Pegawai::with('bidang')->get();
$summary = [];
foreach ($pegawais as $p) {
    $bidang = $p->bidang->nama_bidang ?? 'NULL';
    $sub = $p->sub_bagian ?? 'NULL';
    $key = "$bidang -> $sub";
    if (!isset($summary[$key])) {
        $summary[$key] = 0;
    }
    $summary[$key]++;
}

echo "DISTINCT BIDANG -> SUB_BAGIAN ASSIGNMENTS:\n";
foreach ($summary as $k => $v) {
    echo "  $k: $v pegawai\n";
}
