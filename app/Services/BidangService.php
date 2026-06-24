<?php

namespace App\Services;

use App\Models\Bidang;
use App\Repositories\BidangRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class BidangService
{
    public function __construct(
        private BidangRepository $repo,
        private ActivityLogService $logService,
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->all();
    }

    public function paginate(int $perPage = 10, string $search = ''): LengthAwarePaginator
    {
        return $this->repo->paginate($perPage, $search);
    }

    public function findById(int $id): Bidang
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Bidang
    {
        if ($this->repo->existsByKode($data['kode_bidang'])) {
            throw ValidationException::withMessages(['kode_bidang' => 'Kode bidang sudah digunakan.']);
        }

        $bidang = $this->repo->create($data);
        $this->logService->logCreate('bidang', "Menambah bidang: {$bidang->nama_bidang}", $bidang);

        return $bidang;
    }

    public function update(Bidang $bidang, array $data): Bidang
    {
        if ($this->repo->existsByKode($data['kode_bidang'], $bidang->id)) {
            throw ValidationException::withMessages(['kode_bidang' => 'Kode bidang sudah digunakan.']);
        }

        $old    = $bidang->toArray();
        $bidang = $this->repo->update($bidang, $data);
        $this->logService->logUpdate('bidang', "Mengubah bidang: {$bidang->nama_bidang}", $bidang, $old, $bidang->toArray());

        return $bidang;
    }

    public function delete(Bidang $bidang): void
    {
        if ($bidang->pegawai()->count() > 0) {
            throw ValidationException::withMessages(['bidang' => 'Bidang tidak dapat dihapus karena masih memiliki pegawai.']);
        }

        $nama = $bidang->nama_bidang;
        $this->repo->delete($bidang);
        $this->logService->logDelete('bidang', "Menghapus bidang: {$nama}");
    }
}