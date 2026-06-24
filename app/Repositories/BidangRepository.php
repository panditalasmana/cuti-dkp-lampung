<?php

namespace App\Repositories;

use App\Models\Bidang;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BidangRepository
{
    public function __construct(private Bidang $model) {}

    public function all(): Collection
    {
        return $this->model->active()->orderBy('nama_bidang')->get();
    }

    public function paginate(int $perPage = 10, string $search = ''): LengthAwarePaginator
    {
        return $this->model->when($search, fn($q) => $q->where('nama_bidang', 'like', "%$search%")
                                                       ->orWhere('kode_bidang', 'like', "%$search%"))
                           ->withCount('pegawai')
                           ->orderBy('nama_bidang')
                           ->paginate($perPage)
                           ->withQueryString();
    }

    public function findById(int $id): Bidang
    {
        return $this->model->withCount('pegawai')->findOrFail($id);
    }

    public function create(array $data): Bidang
    {
        return $this->model->create($data);
    }

    public function update(Bidang $bidang, array $data): Bidang
    {
        $bidang->update($data);
        return $bidang->fresh();
    }

    public function delete(Bidang $bidang): bool
    {
        return $bidang->delete();
    }

    public function existsByKode(string $kode, ?int $exceptId = null): bool
    {
        return $this->model->where('kode_bidang', $kode)
                           ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                           ->exists();
    }
}