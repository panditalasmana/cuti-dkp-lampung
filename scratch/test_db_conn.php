<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "DATABASE CONNECTED SUCCESSFULLY!\n";
} catch (\Exception $e) {
    echo "DATABASE ERROR: " . $e->getMessage() . "\n";
}
