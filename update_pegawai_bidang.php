<?php
require 'c:/Users/pandi/Downloads/Cuti-DKP-Lampung/vendor/autoload.php';
$app = require_once 'c:/Users/pandi/Downloads/Cuti-DKP-Lampung/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pegawai;
use App\Models\Bidang;

$jsonPath = 'C:\\Users\\pandi\\.gemini\\antigravity\\brain\\c910c2f8-9db1-46a2-a86c-de4f1227fd10\\scratch\\pdf_mappings.json';
if (!file_exists($jsonPath)) {
    die("JSON file not found!\n");
}

$mappings = json_decode(file_get_contents($jsonPath), true);
$pegawais = Pegawai::all();

function normalizeName($name) {
    // Bersihkan gelar depan
    $name = preg_replace('/\b(ir|dr|dra|drs|h|hj)\b/i', '', $name);
    // Bersihkan gelar belakang
    $name = preg_replace('/[,.]\s*(s\.?pi|m\.?si|s\.?e|m\.?m|s\.?pkp|a\.?md\.?pi|s\.?kom|s\.?sn|s\.?a\.?n|sh|m\.?ling|m\.?i\.?l|s\.?si|m\.?mg|a\.?md\.?t|s\.?t|m\.?p|s\.?pd|a\.?md|m\.?sc|m\.?ap)\b/i', '', $name);
    $name = preg_replace('/\b(s\.?pi|m\.?si|s\.?e|m\.?m|s\.?pkp|a\.?md\.?pi|s\.?kom|s\.?sn|s\.?a\.?n|sh|m\.?ling|m\.?i\.?l|s\.?si|m\.?mg|a\.?md\.?t|s\.?t|m\.?p|s\.?pd|a\.?md|m\.?sc|m\.?ap)\b/i', '', $name);
    $name = preg_replace('/[^a-zA-Z\s]/', ' ', $name);
    $name = preg_replace('/\s+/', ' ', $name);
    return strtolower(trim($name));
}

function cleanForFuzzy($name) {
    $name = normalizeName($name);
    // Hapus semua spasi
    $name = str_replace(' ', '', $name);
    // Deduplikasi huruf berturut-turut (misal ll -> l, mm -> m)
    $name = preg_replace('/(.)\1+/', '$1', $name);
    return $name;
}

$bidangMap = Bidang::pluck('id', 'nama_bidang')->toArray();

$updated = 0;
$notFound = 0;

// Pre-calculate fuzzy keys for mappings
$fuzzyMappings = [];
foreach ($mappings as $key => $val) {
    $fuzzyKey = cleanForFuzzy($key);
    $fuzzyMappings[$fuzzyKey] = $val;
}

foreach ($pegawais as $p) {
    $norm = normalizeName($p->nama_lengkap);
    $fuzzyDb = cleanForFuzzy($p->nama_lengkap);
    
    // 1. Direct match
    $match = null;
    if (isset($mappings[$norm])) {
        $match = $mappings[$norm];
    } 
    // 2. Fuzzy direct match
    elseif (isset($fuzzyMappings[$fuzzyDb])) {
        $match = $fuzzyMappings[$fuzzyDb];
    }
    // 3. Substring fuzzy match
    else {
        foreach ($fuzzyMappings as $fKey => $fVal) {
            if ($fKey && (strpos($fuzzyDb, $fKey) !== false || strpos($fKey, $fuzzyDb) !== false)) {
                $match = $fVal;
                break;
            }
        }
    }

    if ($match) {
        $bidangName = $match['bidang'];
        $subBagianName = $match['sub_bagian'];
        
        // Cari bidang ID
        $bidangId = $bidangMap[$bidangName] ?? null;
        
        if ($bidangId) {
            $p->bidang_id = $bidangId;
            $p->sub_bagian = $subBagianName;
            $p->save();
            echo "Updated: {$p->nama_lengkap} -> Bidang: {$bidangName}, Subbag: " . ($subBagianName ?? '-') . "\n";
            $updated++;
        } else {
            echo "Bidang not found in DB: {$bidangName} for {$p->nama_lengkap}\n";
        }
    } else {
        echo "No mapping found in PDF for: {$p->nama_lengkap} ($norm / fuzzy: $fuzzyDb)\n";
        $notFound++;
    }
}

echo "\nSummary:\n";
echo "Total updated: {$updated}\n";
echo "Not matched: {$notFound}\n";
