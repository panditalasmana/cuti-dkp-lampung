<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\PegawaiService;

try {
    $service = app(PegawaiService::class);
    $data = [
        'nip' => '199912345678901234',
        'nama_lengkap' => 'Test Pegawai Baru',
        'email' => 'testnew@dkp.lampungprov.go.id',
        'password' => 'Pegawai@123',
        'bidang_id' => 1,
        'sub_bagian' => 'Sub Bagian Umum dan Kepegawaian',
        'jabatan_id' => 1,
        'jenis_kelamin' => 'L',
        'tempat_lahir' => 'Bandar Lampung',
        'tanggal_lahir' => '1999-12-31',
        'tanggal_masuk' => '2025-03-01',
        'jenis_pegawai' => 'PNS',
        'pangkat' => 'Penata Muda / III-a',
        'sisa_cuti_tahunan' => 12,
        'is_active' => true,
    ];
    $pegawai = $service->create($data);
    echo "SUCCESS: Pegawai created with ID: " . $pegawai->id . "\n";
    
    // Cleanup
    $pegawai->delete();
    if ($pegawai->user) {
        $pegawai->user->delete();
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
