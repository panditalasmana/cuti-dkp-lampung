<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Bidang;
$deleted = Bidang::whereIn('nama_bidang', [
    'Sub Bagian Umum dan Kepegawaian',
    'Sub Bagian Keuangan dan Aset'
])->delete();

echo "Deleted $deleted unused bidangs from database.\n";
