<?php

namespace App\Repositories;

use App\Models\PengajuanCuti;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PengajuanCutiRepository
{
    public function __construct(private PengajuanCuti $model) {}

    public function paginateForAdmin(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->model->with(['pegawai.bidang', 'pegawai.jabatan', 'jenisCuti'])
                           ->when(!empty($filters['search']), function ($q) use ($filters) {
                               $q->whereHas('pegawai', fn($p) => $p->where('nama_lengkap', 'like', "%{$filters['search']}%")
                                                                    ->orWhere('nip', 'like', "%{$filters['search']}%"))
                                 ->orWhere('nomor_surat', 'like', "%{$filters['search']}%");
                           })
                           ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
                           ->when(!empty($filters['jenis_cuti_id']), fn($q) => $q->where('jenis_cuti_id', $filters['jenis_cuti_id']))
                           ->when(!empty($filters['bidang_id']), fn($q) => $q->whereHas('pegawai', fn($p) => $p->where('bidang_id', $filters['bidang_id'])))
                           ->when(!empty($filters['bulan']), fn($q) => $q->whereMonth('tanggal_pengajuan', $filters['bulan']))
                           ->when(!empty($filters['tahun']), fn($q) => $q->whereYear('tanggal_pengajuan', $filters['tahun']))
                           ->latest('tanggal_pengajuan')
                           ->paginate($perPage)
                           ->withQueryString();
    }

    public function paginateForPegawai(int $pegawaiId, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->model->with(['jenisCuti', 'dokumen', 'scanSurat'])
                           ->where('pegawai_id', $pegawaiId)
                           ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
                           ->when(!empty($filters['tahun']), fn($q) => $q->whereYear('tanggal_pengajuan', $filters['tahun']))
                           ->latest('tanggal_pengajuan')
                           ->paginate($perPage)
                           ->withQueryString();
    }

    public function findById(int $id): PengajuanCuti
    {
        return $this->model->with(['pegawai.bidang', 'pegawai.jabatan', 'jenisCuti', 'verifikator', 'dokumen.uploader', 'scanSurat'])
                           ->findOrFail($id);
    }

    public function findByIdForPegawai(int $id, int $pegawaiId): PengajuanCuti
    {
        return $this->model->with(['jenisCuti', 'dokumen', 'scanSurat'])
                           ->where('pegawai_id', $pegawaiId)
                           ->findOrFail($id);
    }

    public function create(array $data): PengajuanCuti
    {
        return $this->model->create($data);
    }

    public function update(PengajuanCuti $pengajuan, array $data): PengajuanCuti
    {
        $pengajuan->update($data);
        return $pengajuan->fresh();
    }

    public function updateStatus(PengajuanCuti $pengajuan, string $status, ?string $catatan, int $adminId): PengajuanCuti
    {
        $pengajuan->update([
            'status'              => $status,
            'catatan_admin'       => $catatan,
            'tanggal_verifikasi'  => now(),
            'diverifikasi_oleh'   => $adminId,
        ]);
        return $pengajuan->fresh();
    }

    public function generateNomorSurat(): string
    {
        $tahun  = now()->year;
        $bulan  = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        
        $count = $this->model->withTrashed()->whereYear('tanggal_pengajuan', $tahun)->count() + 1;
        do {
            $urutan = str_pad($count, 4, '0', STR_PAD_LEFT);
            $nomorSurat = "DKP.800/{$urutan}/CUTI/{$bulan}/{$tahun}";
            $exists = $this->model->withTrashed()->where('nomor_surat', $nomorSurat)->exists();
            if (!$exists) {
                break;
            }
            $count++;
        } while (true);

        return $nomorSurat;
    }

    public function countByStatus(string $status): int
    {
        return $this->model->where('status', $status)->count();
    }

    public function countTahunIni(): int
    {
        return $this->model->tahunIni()->count();
    }

    public function statistikBulanan(int $tahun): Collection
    {
        return $this->model->selectRaw('MONTH(tanggal_pengajuan) as bulan, COUNT(*) as total, status')
                           ->whereYear('tanggal_pengajuan', $tahun)
                           ->groupBy('bulan', 'status')
                           ->orderBy('bulan')
                           ->get();
    }

    public function statistikPerBidang(int $tahun): Collection
    {
        return $this->model->selectRaw('bidang.nama_bidang, COUNT(pengajuan_cuti.id) as total')
                           ->join('pegawai', 'pengajuan_cuti.pegawai_id', '=', 'pegawai.id')
                           ->join('bidang', 'pegawai.bidang_id', '=', 'bidang.id')
                           ->whereYear('pengajuan_cuti.tanggal_pengajuan', $tahun)
                           ->groupBy('bidang.nama_bidang')
                           ->orderByDesc('total')
                           ->get();
    }

    public function getForExport(array $filters = []): Collection
    {
        return $this->model->with(['pegawai.bidang', 'pegawai.jabatan', 'jenisCuti', 'verifikator'])
                           ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
                           ->when(!empty($filters['tahun']), fn($q) => $q->whereYear('tanggal_pengajuan', $filters['tahun']))
                           ->when(!empty($filters['bulan']), fn($q) => $q->whereMonth('tanggal_pengajuan', $filters['bulan']))
                           ->latest('tanggal_pengajuan')
                           ->get();
    }
}