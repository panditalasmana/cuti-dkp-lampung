<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pegawai;
use App\Models\Bidang;

// Ambil ID Sekretariat
$sekretariat = Bidang::where('nama_bidang', 'Sekretariat')->first();
if (!$sekretariat) {
    echo "ERROR: Bidang 'Sekretariat' tidak ditemukan!\n";
    exit(1);
}

$pegawais = Pegawai::with(['bidang', 'jabatan'])->get();
$updated = 0;

foreach ($pegawais as $p) {
    $changed = false;
    $namaBidang = $p->bidang->nama_bidang ?? '';
    $namaJabatan = $p->jabatan->nama_jabatan ?? '';
    
    if ($namaBidang === 'Sub Bagian Umum dan Kepegawaian') {
        $p->bidang_id = $sekretariat->id;
        $p->sub_bagian = 'Sub Bagian Umum dan Kepegawaian';
        $changed = true;
    } elseif ($namaBidang === 'Sub Bagian Keuangan dan Aset') {
        $p->bidang_id = $sekretariat->id;
        $p->sub_bagian = 'Sub Bagian Keuangan dan Aset';
        $changed = true;
    }
    
    if (str_contains($namaBidang, 'UPTD') && str_contains($namaJabatan, 'Tata Usaha')) {
        $p->sub_bagian = 'Sub Bagian Tata Usaha';
        $changed = true;
    }
    
    if ($changed) {
        $p->save();
        $updated++;
        echo "UPDATED: {$p->nama_lengkap} -> Bidang: {$p->bidang->nama_bidang}, Sub Bagian: {$p->sub_bagian}\n";
    }
}

echo "SELESAI: {$updated} pegawai berhasil disesuaikan.\n";
