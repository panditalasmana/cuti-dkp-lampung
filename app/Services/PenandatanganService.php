<?php

namespace App\Services;

use App\Models\Penandatangan;
use App\Repositories\PenandatanganRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PenandatanganService
{
    public function __construct(
        private PenandatanganRepository $repo,
        private ActivityLogService $logService,
    ) {}

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repo->paginate($perPage, $filters);
    }

    public function findById(int $id): Penandatangan
    {
        return $this->repo->findById($id);
    }

    public function store(array $data): Penandatangan
    {
        return DB::transaction(function () use ($data) {
            $isDefault = !empty($data['is_default']);
            if ($isDefault) {
                $this->repo->unsetOtherDefaults($data['kategori']);
            }

            $penandatangan = $this->repo->create([
                'kategori'         => $data['kategori'],
                'nama'             => trim($data['nama']),
                'nip'              => preg_replace('/\s+/', '', trim($data['nip'])),
                'jabatan'          => trim($data['jabatan']),
                'pangkat_golongan' => trim($data['pangkat_golongan'] ?? ''),
                'is_default'       => $isDefault,
                'is_active'        => isset($data['is_active']) ? (bool)$data['is_active'] : true,
            ]);

            $this->logService->logCreate('penandatangan', "Menambahkan pejabat penandatangan: {$penandatangan->nama}", $penandatangan);

            return $penandatangan;
        });
    }

    public function update(Penandatangan $penandatangan, array $data): Penandatangan
    {
        return DB::transaction(function () use ($penandatangan, $data) {
            $isDefault = !empty($data['is_default']);
            $kategori = $data['kategori'] ?? $penandatangan->kategori;

            if ($isDefault) {
                $this->repo->unsetOtherDefaults($kategori, $penandatangan->id);
            }

            $updated = $this->repo->update($penandatangan, [
                'kategori'         => $kategori,
                'nama'             => trim($data['nama']),
                'nip'              => preg_replace('/\s+/', '', trim($data['nip'])),
                'jabatan'          => trim($data['jabatan']),
                'pangkat_golongan' => trim($data['pangkat_golongan'] ?? ''),
                'is_default'       => $isDefault,
                'is_active'        => isset($data['is_active']) ? (bool)$data['is_active'] : $penandatangan->is_active,
            ]);

            $this->logService->logUpdate('penandatangan', "Mengubah pejabat penandatangan: {$updated->nama}", $updated, [], []);

            return $updated;
        });
    }

    public function delete(Penandatangan $penandatangan): bool
    {
        return DB::transaction(function () use ($penandatangan) {
            $nama = $penandatangan->nama;
            $res = $this->repo->delete($penandatangan);
            $this->logService->logDelete('penandatangan', "Menghapus pejabat penandatangan: {$nama}", $penandatangan);
            return $res;
        });
    }

    public function setDefault(Penandatangan $penandatangan): Penandatangan
    {
        return DB::transaction(function () use ($penandatangan) {
            $this->repo->unsetOtherDefaults($penandatangan->kategori, $penandatangan->id);
            return $this->repo->update($penandatangan, ['is_default' => true]);
        });
    }

    public function getActiveSignersGrouped(): array
    {
        return [
            'pejabat_wenang'  => $this->repo->getActiveByKategori('pejabat_wenang'),
            'atasan_langsung' => $this->repo->getActiveByKategori('atasan_langsung'),
            'pejabat_pengawas' => $this->repo->getActiveByKategori('pejabat_pengawas'),
        ];
    }
}
