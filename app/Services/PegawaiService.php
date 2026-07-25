<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\User;
use App\Models\Bidang;
use App\Models\Jabatan;
use App\Repositories\PegawaiRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PegawaiService
{
    public function __construct(
        private PegawaiRepository $repo,
        private ActivityLogService $logService,
    ) {}

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->repo->paginate($perPage, $filters);
    }

    public function findById(int $id): Pegawai
    {
        return $this->repo->findById($id);
    }

    public function findByUserId(int $userId): ?Pegawai
    {
        return $this->repo->findByUserId($userId);
    }

    public function create(array $data): Pegawai
    {
        return DB::transaction(function () use ($data) {
            // Cek NIP unik
            if ($this->repo->findByNip($data['nip'])) {
                throw ValidationException::withMessages(['nip' => 'NIP sudah terdaftar dalam sistem.']);
            }

            // Buat User terlebih dahulu
            $user = User::create([
                'nip'      => $data['nip'],
                'name'     => $data['nama_lengkap'],
                'email'    => $data['email'] ?? null,
                'password' => Hash::make($data['password'] ?? substr($data['nip'], 0, 4)),
                'role'     => 'pegawai',
                'is_active'=> $data['is_active'] ?? true,
            ]);

            // Handle foto upload
            if (isset($data['foto']) && $data['foto']->isValid()) {
                $path          = $data['foto']->store('pegawai/foto', 'public');
                $data['foto']  = $path;
            } else {
                unset($data['foto']);
            }

            // Hapus field password dari data pegawai
            unset($data['password']);

            $pegawai = $this->repo->create(array_merge($data, ['user_id' => $user->id]));
            $this->logService->logCreate('pegawai', "Menambah pegawai: {$pegawai->nama_lengkap} (NIP: {$pegawai->nip})", $pegawai);

            return $pegawai;
        });
    }

    public function update(Pegawai $pegawai, array $data): Pegawai
    {
        return DB::transaction(function () use ($pegawai, $data) {
            $old = $pegawai->toArray();

            // Handle foto
            if (isset($data['foto']) && $data['foto']->isValid()) {
                // Hapus foto lama
                if ($pegawai->foto) {
                    Storage::disk('public')->delete($pegawai->foto);
                }
                $data['foto'] = $data['foto']->store('pegawai/foto', 'public');
            } else {
                unset($data['foto']);
            }

            // Update user terkait
            $pegawai->user->update([
                'name'  => $data['nama_lengkap'],
                'email' => $data['email'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Update password jika diisi
            if (!empty($data['password'])) {
                $pegawai->user->update(['password' => Hash::make($data['password'])]);
            }
            unset($data['password']);

            $pegawai = $this->repo->update($pegawai, $data);
            $this->logService->logUpdate('pegawai', "Mengubah data pegawai: {$pegawai->nama_lengkap}", $pegawai, $old, $pegawai->toArray());

            return $pegawai;
        });
    }

    public function delete(Pegawai $pegawai): void
    {
        if ($pegawai->pengajuanCuti()->whereIn('status', ['menunggu'])->exists()) {
            throw ValidationException::withMessages(['pegawai' => 'Pegawai memiliki pengajuan cuti yang sedang diproses.']);
        }

        DB::transaction(function () use ($pegawai) {
            $nama = $pegawai->nama_lengkap;

            if ($pegawai->foto) {
                Storage::disk('public')->delete($pegawai->foto);
            }

            $this->repo->delete($pegawai);
            $pegawai->user->delete();

            $this->logService->logDelete('pegawai', "Menghapus pegawai: {$nama}");
        });
    }

    public function updateProfil(Pegawai $pegawai, array $data): Pegawai
    {
        return DB::transaction(function () use ($pegawai, $data) {
            // Hanya boleh update field tertentu untuk pegawai sendiri
            $allowed = ['alamat', 'no_telepon', 'email', 'foto'];
            $filtered = array_intersect_key($data, array_flip($allowed));

            if (isset($filtered['foto']) && $filtered['foto']->isValid()) {
                if ($pegawai->foto) {
                    Storage::disk('public')->delete($pegawai->foto);
                }
                $filtered['foto'] = $filtered['foto']->store('pegawai/foto', 'public');
            } else {
                unset($filtered['foto']);
                if (isset($data['hapus_foto']) && $data['hapus_foto'] == '1') {
                    if ($pegawai->foto) {
                        Storage::disk('public')->delete($pegawai->foto);
                    }
                    $filtered['foto'] = null;
                }
            }

            if (!empty($data['email'])) {
                $pegawai->user->update(['email' => $data['email']]);
            }

            return $this->repo->update($pegawai, $filtered);
        });
    }

    public function gantiPassword(User $user, string $newPassword): void
    {
        $user->update(['password' => Hash::make($newPassword)]);
        $this->logService->logUpdate('auth', 'Ganti password', $user, [], []);
    }

    public function countAll(): int
    {
        return $this->repo->countAll();
    }

    /**
     * Sinkronisasi nama & data pegawai dari CSV DUK resmi.
     * Alur ini dipindahkan dari migration 2026_07_24_100000_fix_pegawai_name_formats
     * agar bisa dijalankan ulang kapan saja lewat Artisan Command, tanpa terikat
     * siklus migrate sekali-jalan.
     */
    public function syncFromDukCsv(string $csvFile): array
    {
        $stats = ['updated' => 0, 'dilewati' => 0, 'bidang_baru' => [], 'jabatan_baru' => []];

        $bidangMap  = Bidang::pluck('id', 'nama_bidang')->toArray();
        $jabatanMap = Jabatan::pluck('id', 'nama_jabatan')->toArray();

        $handle = fopen($csvFile, 'r');
        fgetcsv($handle, 0, ','); // Skip header

        DB::transaction(function () use ($handle, &$bidangMap, &$jabatanMap, &$stats) {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                if (empty($row) || empty($row[0])) continue;

                $nip          = preg_replace('/\s+/', '', trim($row[0]));
                $namaLengkap  = trim($row[1]  ?? '');
                $jenisKelamin = strtoupper(trim($row[2] ?? 'L'));
                $tempatLahir  = trim($row[3]  ?? '-');
                $jenisPegawai = trim($row[6]  ?? 'PNS');
                $pangkat      = trim($row[7]  ?? '-');
                $namaBidang   = trim($row[8]  ?? '');
                $namaJabatan  = trim($row[9]  ?? '');

                // --- alur sub_bagian, dipertahankan sama persis dari migration asal ---
                $subBagian = null;
                if ($namaBidang === 'Sub Bagian Umum dan Kepegawaian') {
                    $namaBidang = 'Sekretariat';
                    $subBagian = 'Sub Bagian Umum dan Kepegawaian';
                } elseif ($namaBidang === 'Sub Bagian Keuangan dan Aset') {
                    $namaBidang = 'Sekretariat';
                    $subBagian = 'Sub Bagian Keuangan dan Aset';
                }

                if (str_contains($namaBidang, 'UPTD') && str_contains($namaJabatan, 'Tata Usaha')) {
                    $subBagian = 'Sub Bagian Tata Usaha';
                }

                // --- bidang & jabatan: sekarang PAKAI kode unik anti-tabrakan ---
                $bidangId = $bidangMap[$namaBidang] ?? null;
                if (!$bidangId && $namaBidang !== '') {
                    $bidang = Bidang::firstOrCreate(
                        ['nama_bidang' => $namaBidang],
                        [
                            'kode_bidang'       => $this->buatKodeUnik($namaBidang, 'bidang'),
                            'kepala_bidang'     => '-',
                            'nip_kepala_bidang' => '-',
                            'keterangan'        => $namaBidang,
                            'is_active'         => true,
                        ]
                    );
                    $bidangId = $bidang->id;
                    $bidangMap[$namaBidang] = $bidangId;
                    $stats['bidang_baru'][] = $namaBidang;
                }

                $jabatanId = $jabatanMap[$namaJabatan] ?? null;
                if (!$jabatanId && $namaJabatan !== '') {
                    $jabatan = Jabatan::firstOrCreate(
                        ['nama_jabatan' => $namaJabatan],
                        [
                            'kode_jabatan' => $this->buatKodeUnik($namaJabatan, 'jabatan'),
                            'golongan'     => null,
                            'eselon'       => null,
                            'is_active'    => true,
                        ]
                    );
                    $jabatanId = $jabatan->id;
                    $jabatanMap[$namaJabatan] = $jabatanId;
                    $stats['jabatan_baru'][] = $namaJabatan;
                }

                $user = User::where('nip', $nip)->first();
                $pegawai = Pegawai::where('nip', $nip)->first();

                if (!$user || !$pegawai) {
                    $stats['dilewati']++;
                    continue;
                }

                $user->update(['name' => $namaLengkap]);

                $pegawai->update([
                    'nama_lengkap'  => $namaLengkap,
                    'bidang_id'     => $bidangId,
                    'sub_bagian'    => $subBagian,
                    'jabatan_id'    => $jabatanId,
                    'jenis_kelamin' => in_array($jenisKelamin, ['L', 'P']) ? $jenisKelamin : 'L',
                    'tempat_lahir'  => $tempatLahir ?: '-',
                    'jenis_pegawai' => in_array($jenisPegawai, ['PNS', 'PPPK']) ? $jenisPegawai : 'PNS',
                    'pangkat'       => $pangkat ?: '-',
                ]);

                $stats['updated']++;
            }
        });

        fclose($handle);
        return $stats;
    }

    /**
     * Bikin kode dari nama, tapi kalau sudah dipakai, tambah angka di belakang
     * supaya tidak pernah tabrakan seperti kasus "UPTD Pelabuhan Perikanan ...".
     */
    private function buatKodeUnik(string $nama, string $tabel): string
    {
        $panjangMaks = 30; // sesuai kolom kode_bidang/kode_jabatan (varchar 30) di migration create_bidang_table & create_jabatan_table
        $base = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $nama), 0, $panjangMaks));

        $kodeYangDipakai = DB::table($tabel)->pluck('kode_' . $tabel)->toArray();

        $kode = $base;
        $i = 1;
        while (in_array($kode, $kodeYangDipakai)) {
            $suffix = (string) $i;
            $kode = substr($base, 0, $panjangMaks - strlen($suffix)) . $suffix;
            $i++;
        }

        return $kode;
    }

    /**
     * Cocokkan pegawai ke bidang/sub_bagian berdasarkan mapping nama dari JSON
     * (hasil ekstraksi PDF DUK), pakai fuzzy matching nama.
     * Alur ini dipindahkan dari script root update_pegawai_bidang.php
     * agar tidak lagi bergantung pada path absolut di satu laptop.
     */
    public function syncBidangFromJsonMapping(string $jsonPath): array
    {
        if (!file_exists($jsonPath)) {
            throw new \RuntimeException("File JSON tidak ditemukan: {$jsonPath}");
        }

        $mappings = json_decode(file_get_contents($jsonPath), true);
        $pegawais = Pegawai::all();
        $bidangMap = Bidang::pluck('id', 'nama_bidang')->toArray();

        $log = [];
        $updated = 0;
        $notFound = 0;

        $fuzzyMappings = [];
        foreach ($mappings as $key => $val) {
            $fuzzyMappings[$this->cleanForFuzzy($key)] = $val;
        }

        foreach ($pegawais as $p) {
            $norm = $this->normalizeName($p->nama_lengkap);
            $fuzzyDb = $this->cleanForFuzzy($p->nama_lengkap);

            $match = null;
            if (isset($mappings[$norm])) {
                $match = $mappings[$norm];
            } elseif (isset($fuzzyMappings[$fuzzyDb])) {
                $match = $fuzzyMappings[$fuzzyDb];
            } else {
                foreach ($fuzzyMappings as $fKey => $fVal) {
                    if ($fKey && (str_contains($fuzzyDb, $fKey) || str_contains($fKey, $fuzzyDb))) {
                        $match = $fVal;
                        break;
                    }
                }
            }

            if (!$match) {
                $log[] = "Tidak ada mapping untuk: {$p->nama_lengkap}";
                $notFound++;
                continue;
            }

            $bidangId = $bidangMap[$match['bidang']] ?? null;
            if (!$bidangId) {
                $log[] = "Bidang tidak ditemukan di DB: {$match['bidang']} untuk {$p->nama_lengkap}";
                continue;
            }

            $p->update([
                'bidang_id'  => $bidangId,
                'sub_bagian' => $match['sub_bagian'] ?? null,
            ]);
            $updated++;
        }

        return ['updated' => $updated, 'not_found' => $notFound, 'log' => $log];
    }

    private function normalizeName(string $name): string
    {
        $name = preg_replace('/\b(ir|dr|dra|drs|h|hj)\b/i', '', $name);
        $name = preg_replace('/[,.]\s*(s\.?pi|m\.?si|s\.?e|m\.?m|s\.?pkp|a\.?md\.?pi|s\.?kom|s\.?sn|s\.?a\.?n|sh|m\.?ling|m\.?i\.?l|s\.?si|m\.?mg|a\.?md\.?t|s\.?t|m\.?p|s\.?pd|a\.?md|m\.?sc|m\.?ap)\b/i', '', $name);
        $name = preg_replace('/\b(s\.?pi|m\.?si|s\.?e|m\.?m|s\.?pkp|a\.?md\.?pi|s\.?kom|s\.?sn|s\.?a\.?n|sh|m\.?ling|m\.?i\.?l|s\.?si|m\.?mg|a\.?md\.?t|s\.?t|m\.?p|s\.?pd|a\.?md|m\.?sc|m\.?ap)\b/i', '', $name);
        $name = preg_replace('/[^a-zA-Z\s]/', ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return strtolower(trim($name));
    }

    private function cleanForFuzzy(string $name): string
    {
        $name = $this->normalizeName($name);
        $name = str_replace(' ', '', $name);
        return preg_replace('/(.)\1+/', '$1', $name);
    }
}
