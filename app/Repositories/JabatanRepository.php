<?php

namespace App\Repositories;

use App\Models\Jabatan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class JabatanRepository
{
    public function __construct(private Jabatan $model) {}

    public function all(): Collection
    {
        return $this->model->active()->orderBy('nama_jabatan')->get();
    }

    public function paginate(int $perPage = 10, string $search = ''): LengthAwarePaginator
    {
        return $this->model->when($search, fn($q) => $q->where('nama_jabatan', 'like', "%$search%")
                                                       ->orWhere('kode_jabatan', 'like', "%$search%"))
                           ->withCount('pegawai')
                           ->orderBy('nama_jabatan')
                           ->paginate($perPage)
                           ->withQueryString();
    }

    public function findById(int $id): Jabatan
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Jabatan
    {
        return $this->model->create($data);
    }

    public function update(Jabatan $jabatan, array $data): Jabatan
    {
        $jabatan->update($data);
        return $jabatan->fresh();
    }

    public function delete(Jabatan $jabatan): bool
    {
        return $jabatan->delete();
    }
}