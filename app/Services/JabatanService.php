<?php

namespace App\Services;

use App\Models\Jabatan;
use App\Repositories\JabatanRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class JabatanService
{
    public function __construct(
        private JabatanRepository $repo,
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

    public function findById(int $id): Jabatan
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Jabatan
    {
        $jabatan = $this->repo->create($data);
        $this->logService->logCreate('jabatan', "Menambah jabatan: {$jabatan->nama_jabatan}", $jabatan);
        return $jabatan;
    }

    public function update(Jabatan $jabatan, array $data): Jabatan
    {
        $old     = $jabatan->toArray();
        $jabatan = $this->repo->update($jabatan, $data);
        $this->logService->logUpdate('jabatan', "Mengubah jabatan: {$jabatan->nama_jabatan}", $jabatan, $old, $jabatan->toArray());
        return $jabatan;
    }

    public function delete(Jabatan $jabatan): void
    {
        if ($jabatan->pegawai()->count() > 0) {
            throw ValidationException::withMessages(['jabatan' => 'Jabatan tidak dapat dihapus karena masih digunakan pegawai.']);
        }

        $nama = $jabatan->nama_jabatan;
        $this->repo->delete($jabatan);
        $this->logService->logDelete('jabatan', "Menghapus jabatan: {$nama}");
    }
}