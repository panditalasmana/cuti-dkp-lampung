<?php

namespace App\Repositories;

use App\Models\Dokumen;
use Illuminate\Database\Eloquent\Collection;

class DokumenRepository
{
    public function __construct(private Dokumen $model) {}

    public function create(array $data): Dokumen
    {
        return $this->model->create($data);
    }

    public function findById(int $id): Dokumen
    {
        return $this->model->findOrFail($id);
    }

    public function getByPengajuan(int $pengajuanId): Collection
    {
        return $this->model->where('pengajuan_cuti_id', $pengajuanId)->latest()->get();
    }

    public function getScanByPengajuan(int $pengajuanId): ?Dokumen
    {
        return $this->model->where('pengajuan_cuti_id', $pengajuanId)
                           ->where('jenis_dokumen', 'scan_surat_ditandatangani')
                           ->latest()
                           ->first();
    }

    public function delete(Dokumen $dokumen): bool
    {
        return $dokumen->delete();
    }
}