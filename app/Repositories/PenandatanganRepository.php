<?php

namespace App\Repositories;

use App\Models\Penandatangan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PenandatanganRepository
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Penandatangan::query();

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                  ->orWhere('nip', 'like', "%{$s}%")
                  ->orWhere('jabatan', 'like', "%{$s}%");
            });
        }

        if (!empty($filters['kategori'])) {
            $query->where('kategori', $filters['kategori']);
        }

        return $query->orderBy('kategori')->orderBy('is_default', 'desc')->orderBy('id', 'asc')->paginate($perPage);
    }

    public function findById(int $id): Penandatangan
    {
        return Penandatangan::findOrFail($id);
    }

    public function getActiveByKategori(string $kategori): Collection
    {
        $kategoris = ($kategori === 'eselon_4' || $kategori === 'pejabat_pengawas')
            ? ['eselon_4', 'pejabat_pengawas']
            : [$kategori];

        return Penandatangan::active()->whereIn('kategori', $kategoris)->orderBy('is_default', 'desc')->orderBy('id', 'asc')->get();
    }

    public function getDefaultByKategori(string $kategori): ?Penandatangan
    {
        $kategoris = ($kategori === 'eselon_4' || $kategori === 'pejabat_pengawas')
            ? ['eselon_4', 'pejabat_pengawas']
            : [$kategori];

        return Penandatangan::active()->whereIn('kategori', $kategoris)->default()->first()
            ?? Penandatangan::active()->whereIn('kategori', $kategoris)->first();
    }

    public function create(array $data): Penandatangan
    {
        return Penandatangan::create($data);
    }

    public function update(Penandatangan $penandatangan, array $data): Penandatangan
    {
        $penandatangan->update($data);
        return $penandatangan->fresh();
    }

    public function delete(Penandatangan $penandatangan): bool
    {
        return $penandatangan->delete();
    }

    public function unsetOtherDefaults(string $kategori, ?int $exceptId = null): void
    {
        $query = Penandatangan::where('kategori', $kategori);
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        $query->update(['is_default' => false]);
    }
}
