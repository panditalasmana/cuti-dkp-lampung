<?php

namespace App\Repositories;

use App\Models\Pegawai;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PegawaiRepository
{
    public function __construct(private Pegawai $model) {}

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->model->with(['bidang', 'jabatan', 'user'])
                           ->when(!empty($filters['search']), function ($q) use ($filters) {
                               $q->where('nama_lengkap', 'like', "%{$filters['search']}%")
                                 ->orWhere('nip', 'like', "%{$filters['search']}%");
                           })
                           ->when(!empty($filters['bidang_id']), fn($q) => $q->where('bidang_id', $filters['bidang_id']))
                           ->when(!empty($filters['jabatan_id']), fn($q) => $q->where('jabatan_id', $filters['jabatan_id']))
                           ->when(!empty($filters['jenis_pegawai']), fn($q) => $q->where('jenis_pegawai', $filters['jenis_pegawai']))
                           ->when(isset($filters['status']) && $filters['status'] !== '', function ($q) use ($filters) {
                               if ($filters['status'] === 'aktif') {
                                   $q->where('is_active', true);
                               } elseif ($filters['status'] === 'nonaktif') {
                                   $q->where('is_active', false);
                               }
                           })
                           ->orderBy('nama_lengkap')
                           ->paginate($perPage)
                           ->withQueryString();
    }

    public function findById(int $id): Pegawai
    {
        return $this->model->with(['bidang', 'jabatan', 'user'])->findOrFail($id);
    }

    public function findByUserId(int $userId): ?Pegawai
    {
        return $this->model->with(['bidang', 'jabatan'])->where('user_id', $userId)->first();
    }

    public function findByNip(string $nip): ?Pegawai
    {
        return $this->model->where('nip', $nip)->first();
    }

    public function create(array $data): Pegawai
    {
        return $this->model->create($data);
    }

    public function update(Pegawai $pegawai, array $data): Pegawai
    {
        $pegawai->update($data);
        return $pegawai->fresh(['bidang', 'jabatan']);
    }

    public function delete(Pegawai $pegawai): bool
    {
        return $pegawai->delete();
    }

    public function kurangiSisaCuti(Pegawai $pegawai, int $jumlahHari): void
    {
        $pegawai->decrement('sisa_cuti_tahunan', $jumlahHari);
    }

    public function tambahSisaCuti(Pegawai $pegawai, int $jumlahHari): void
    {
        $pegawai->increment('sisa_cuti_tahunan', $jumlahHari);
    }

    public function countAll(): int
    {
        return $this->model->active()->count();
    }
}