<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\PengajuanCuti;
use App\Repositories\DokumenRepository;
use App\Repositories\PegawaiRepository;
use App\Repositories\PengajuanCutiRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PengajuanCutiService
{
    public function __construct(
        private PengajuanCutiRepository $repo,
        private DokumenRepository $dokumenRepo,
        private PegawaiRepository $pegawaiRepo,
        private ActivityLogService $logService,
        private PdfService $pdfService,
    ) {}

    public function paginateForAdmin(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repo->paginateForAdmin($perPage, $filters);
    }

    public function paginateForPegawai(int $pegawaiId, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->repo->paginateForPegawai($pegawaiId, $perPage, $filters);
    }

    public function findById(int $id): PengajuanCuti
    {
        return $this->repo->findById($id);
    }

    public function findByIdForPegawai(int $id, int $pegawaiId): PengajuanCuti
    {
        return $this->repo->findByIdForPegawai($id, $pegawaiId);
    }

    /**
     * Proses pengajuan cuti baru sesuai SOP ASN
     */
    public function ajukan(Pegawai $pegawai, array $data): PengajuanCuti
    {
        return DB::transaction(function () use ($pegawai, $data) {
            $jenisCuti = \App\Models\JenisCuti::findOrFail($data['jenis_cuti_id']);

            // Hitung lama cuti sesuai jenis cuti (hari kerja, bulan, atau tahun)
            $lamaCuti = $this->hitungLamaCuti($jenisCuti, $data['tanggal_mulai'], $data['tanggal_selesai']);

            // Validasi bisnis
            $this->validasiPengajuan($pegawai, $jenisCuti, $data, $lamaCuti);

            // Generate nomor surat otomatis
            $nomorSurat = $this->repo->generateNomorSurat();

            // Default fallbacks
            $atasanNama = 'A. FAISAL, A.Pi.';
            $atasanNip = '197402031999031006';
            $atasanJabatan = 'Sekretaris Dinas';

            if (isset($data['atasan_langsung_select']) && str_contains($data['atasan_langsung_select'], '|')) {
                $atasanParts = explode('|', $data['atasan_langsung_select']);
                $atasanNama = $atasanParts[0] ?? $atasanNama;
                $atasanNip = $atasanParts[1] ?? $atasanNip;
                $atasanJabatan = $atasanParts[2] ?? $atasanJabatan;
            }

            $pejabatNama = 'Ir. BANI ISPRIYANTO, M.M.';
            $pejabatNip = '196904101995031002';
            $pejabatJabatan = 'Kepala Dinas';

            if (isset($data['pejabat_wenang_select']) && str_contains($data['pejabat_wenang_select'], '|')) {
                $pejabatParts = explode('|', $data['pejabat_wenang_select']);
                $pejabatNama = $pejabatParts[0] ?? $pejabatNama;
                $pejabatNip = $pejabatParts[1] ?? $pejabatNip;
                $pejabatJabatan = $pejabatParts[2] ?? $pejabatJabatan;
            }

            // Simpan pengajuan
            $pengajuan = $this->repo->create([
                'pegawai_id'          => $pegawai->id,
                'jenis_cuti_id'       => $data['jenis_cuti_id'],
                'nomor_surat'         => $nomorSurat,
                'tanggal_mulai'       => $data['tanggal_mulai'],
                'tanggal_selesai'     => $data['tanggal_selesai'],
                'lama_cuti'           => $lamaCuti,
                'alasan_cuti'         => $data['alasan_cuti'],
                'alamat_selama_cuti'  => $data['alamat_selama_cuti'],
                'no_telp_selama_cuti' => $data['no_telp_selama_cuti'] ?? null,
                'status'              => PengajuanCuti::STATUS_MENUNGGU,
                'tanggal_pengajuan'   => now(),
                'atasan_nama'         => $atasanNama,
                'atasan_nip'          => $atasanNip,
                'atasan_jabatan'      => $atasanJabatan,
                'pejabat_nama'        => $pejabatNama,
                'pejabat_nip'         => $pejabatNip,
                'pejabat_jabatan'     => $pejabatJabatan,
            ]);

            // Generate PDF surat cuti otomatis
            $pdfPath = $this->pdfService->generateSuratCuti($pengajuan->load(['pegawai.bidang', 'pegawai.jabatan', 'jenisCuti']));
            $pengajuan->update(['pdf_surat' => $pdfPath]);

            $this->logService->logCreate('pengajuan', "Mengajukan cuti: {$nomorSurat} - {$jenisCuti->nama_cuti} ({$lamaCuti} hari)", $pengajuan);

            return $pengajuan->fresh();
        });
    }

    /**
     * Admin: verifikasi dan ubah status pengajuan
     */
    public function verifikasi(PengajuanCuti $pengajuan, string $status, ?string $catatan): PengajuanCuti
    {
        return DB::transaction(function () use ($pengajuan, $status, $catatan) {
            if (!in_array($status, [PengajuanCuti::STATUS_DISETUJUI, PengajuanCuti::STATUS_DITOLAK])) {
                throw ValidationException::withMessages(['status' => 'Status tidak valid.']);
            }

            if ($pengajuan->status !== PengajuanCuti::STATUS_MENUNGGU) {
                throw ValidationException::withMessages(['status' => 'Pengajuan ini sudah diverifikasi sebelumnya.']);
            }

            // Jika disetujui dan jenis cuti memotong kuota, kurangi sisa cuti
            if ($status === PengajuanCuti::STATUS_DISETUJUI) {
                $jenisCuti = $pengajuan->jenisCuti;
                if ($jenisCuti->potong_kuota) {
                    $pegawai = $pengajuan->pegawai;
                    if ($pegawai->sisa_cuti_tahunan < $pengajuan->lama_cuti) {
                        throw ValidationException::withMessages(['cuti' => 'Sisa cuti tahunan pegawai tidak mencukupi.']);
                    }
                    $this->pegawaiRepo->kurangiSisaCuti($pegawai, $pengajuan->lama_cuti);
                }
            }

            $pengajuan = $this->repo->updateStatus($pengajuan, $status, $catatan, Auth::id());

            $label = $status === PengajuanCuti::STATUS_DISETUJUI ? 'Disetujui' : 'Ditolak';
            $this->logService->logStatus('pengajuan', "Status pengajuan {$pengajuan->nomor_surat} diubah menjadi: {$label}", $pengajuan);

            return $pengajuan;
        });
    }

    /**
     * Hitung lama cuti berdasarkan jenis cuti (hari kerja, bulan, atau tahun)
     */
    public function hitungLamaCuti($jenisCuti, string $mulai, string $selesai): int
    {
        $kode = $jenisCuti->kode_cuti ?? '';
        if ($kode === 'CM') {
            $start = \Carbon\Carbon::parse($mulai);
            $end   = \Carbon\Carbon::parse($selesai);
            $days  = $start->diffInDays($end) + 1;
            return (int) max(1, round($days / 30));
        }
        if ($kode === 'CLN') {
            $start = \Carbon\Carbon::parse($mulai);
            $end   = \Carbon\Carbon::parse($selesai);
            $days  = $start->diffInDays($end) + 1;
            return (int) max(1, round($days / 365));
        }
        return $this->hitungHariKerja($mulai, $selesai);
    }

    /**
     * Hitung hari kerja (Senin - Jumat)
     */
    public function hitungHariKerja(string $mulai, string $selesai): int
    {
        $start  = \Carbon\Carbon::parse($mulai);
        $end    = \Carbon\Carbon::parse($selesai);
        $count  = 0;

        $current = $start->copy();
        while ($current->lte($end)) {
            if ($current->isWeekday()) {
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }

    /**
     * Validasi logika bisnis pengajuan cuti
     */
    private function validasiPengajuan(Pegawai $pegawai, $jenisCuti, array $data, int $lamaCuti): void
    {
        $mulai   = \Carbon\Carbon::parse($data['tanggal_mulai']);
        $selesai = \Carbon\Carbon::parse($data['tanggal_selesai']);

        // Tanggal mulai harus sebelum atau sama dengan tanggal selesai
        if ($mulai->gt($selesai)) {
            throw ValidationException::withMessages(['tanggal_selesai' => 'Tanggal selesai harus setelah tanggal mulai.']);
        }

        // Tanggal mulai tidak boleh di masa lalu
        if ($mulai->lt(now()->startOfDay())) {
            throw ValidationException::withMessages(['tanggal_mulai' => 'Tanggal mulai tidak boleh di masa lalu.']);
        }

        // Cuti melahirkan hanya dapat diajukan oleh pegawai perempuan
        if ($jenisCuti->kode_cuti === 'CM' && $pegawai->jenis_kelamin !== 'P') {
            throw ValidationException::withMessages(['jenis_cuti_id' => 'Cuti melahirkan hanya dapat diajukan oleh pegawai perempuan.']);
        }

        if ($jenisCuti->kode_cuti !== 'CT') {
            $tahunIni = now()->year;
            $usedDays = $pegawai->pengajuanCuti()
                ->where('status', '!=', PengajuanCuti::STATUS_DITOLAK)
                ->where('jenis_cuti_id', $jenisCuti->id)
                ->whereYear('tanggal_mulai', $tahunIni)
                ->sum('lama_cuti');

            $maksHari = $jenisCuti->maks_hari ?? 0;
            $sisaKuota = max($maksHari - $usedDays, 0);

            if ($sisaKuota < $lamaCuti) {
                throw ValidationException::withMessages(['lama_cuti' => "Sisa kuota untuk {$jenisCuti->nama_cuti} Anda ({$sisaKuota} {$jenisCuti->satuan}) tidak mencukupi untuk pengajuan ini ({$lamaCuti} {$jenisCuti->satuan})."]);
            }
        } else {
            // Validasi sisa cuti tahunan jika memotong kuota
            if ($jenisCuti->potong_kuota && $pegawai->sisa_cuti_tahunan < $lamaCuti) {
                throw ValidationException::withMessages(['lama_cuti' => "Sisa cuti tahunan Anda ({$pegawai->sisa_cuti_tahunan} hari) tidak mencukupi untuk pengajuan ini ({$lamaCuti} hari)."]);
            }
        }

        // Cek tidak ada pengajuan aktif yang bertabrakan
        $bertabrakan = $pegawai->pengajuanCuti()
            ->where('status', '!=', PengajuanCuti::STATUS_DITOLAK)
            ->where(function ($q) use ($data) {
                $q->whereBetween('tanggal_mulai', [$data['tanggal_mulai'], $data['tanggal_selesai']])
                  ->orWhereBetween('tanggal_selesai', [$data['tanggal_mulai'], $data['tanggal_selesai']])
                  ->orWhere(function ($q2) use ($data) {
                      $q2->where('tanggal_mulai', '<=', $data['tanggal_mulai'])
                         ->where('tanggal_selesai', '>=', $data['tanggal_selesai']);
                  });
            })
            ->exists();

        if ($bertabrakan) {
            throw ValidationException::withMessages(['tanggal_mulai' => 'Terdapat pengajuan cuti lain pada periode yang sama.']);
        }
    }

    public function getStatistik(): array
    {
        return [
            'total_menunggu'  => $this->repo->countByStatus(PengajuanCuti::STATUS_MENUNGGU),
            'total_disetujui' => $this->repo->countByStatus(PengajuanCuti::STATUS_DISETUJUI),
            'total_ditolak'   => $this->repo->countByStatus(PengajuanCuti::STATUS_DITOLAK),
            'total_tahun_ini' => $this->repo->countTahunIni(),
        ];
    }

    public function statistikBulanan(int $tahun): array
    {
        $raw    = $this->repo->statistikBulanan($tahun);
        $result = array_fill(1, 12, ['menunggu' => 0, 'disetujui' => 0, 'ditolak' => 0]);

        foreach ($raw as $item) {
            $result[$item->bulan][$item->status] = $item->total;
        }

        return $result;
    }

    public function statistikPerBidang(int $tahun): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repo->statistikPerBidang($tahun);
    }

    public function getForExport(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repo->getForExport($filters);
    }
}